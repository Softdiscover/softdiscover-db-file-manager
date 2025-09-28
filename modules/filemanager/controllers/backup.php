<?php

/**
 * Frontend
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://softdiscover.com
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('flmbkp_Filemanager_Controller_Backup')) {
    return;
}

/**
 * Controller Frontend class
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      http://wordpress-cost_estimator.zigaform.com
 */
class flmbkp_Filemanager_Controller_Backup extends Flmbkp_Base_Module
{
    private $tables = array();
    private $suffix = 'd-M-Y_H-i-s';
    const VERSION = '1.2';

    private $wpdb = "";
    private $pagination = "";
    private $model_backup = "";

    public $progress;
    public $zip_obj;
    public $last_abort_check;

    public $total_targets;
    public $startTime;
    public $max_execution_time;
    public $is_initial_run;
    public $iteration_number;
    public $oFile;
    public $excludes;

    protected $modules;
    private $per_page = 10;

    /**
     * Constructor
     *
     * @mvc Controller
     */
    public function __construct()
    {

        global $wpdb;
        $this->wpdb = $wpdb;
        $this->model_backup = self::$_models['filemanager']['backup'];

        //create records
        add_action('wp_ajax_flmbkp_backup_createrec', array($this, 'ajax_create_records'));

        //submit header options
        add_action('wp_ajax_flmbkp_backup_sendoptions', array($this, 'ajax_submit_options_switch'));

        //progress polling
        add_action('wp_ajax_flmbkp_backup_watchprogress', array($this, 'ajax_watchprogress'));

        //download file
        add_action('wp_ajax_flmbkp_backup_downloadfile', array($this, 'ajax_downloadfile'));

        //delete record
        add_action('wp_ajax_flmbkp_backup_delete_records', array($this, 'ajax_delete_record'));

        //restore record
        add_action('wp_ajax_flmbkp_backup_restore_records', array($this, 'ajax_restore_record'));

        // cancel & cleanup endpoints
        add_action('wp_ajax_flmbkp_backup_cancel', array($this, 'ajax_cancel_backup'));
        add_action('wp_ajax_flmbkp_backup_cleanup', array($this, 'ajax_cleanup_backup'));

        define('NL', "\r\n");
    }

    /**
     * Create deny-all hardening files in a directory (Apache/IIS) + placeholder index.html
     */
    private function harden_dir($dir)
    {
        // Apache
        $htaccess = $dir . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccess)) {
            $ht = ""
                . "Options -Indexes\n"
                . "<IfModule mod_authz_core.c>\n"
                . "  Require all denied\n"
                . "</IfModule>\n"
                . "<IfModule !mod_authz_core.c>\n"
                . "  Order allow,deny\n"
                . "  Deny from all\n"
                . "</IfModule>\n";
            @file_put_contents($htaccess, $ht);
        }

        // IIS
        $webconfig = $dir . DIRECTORY_SEPARATOR . 'web.config';
        if (!file_exists($webconfig)) {
            $wc = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<configuration>' . "\n"
                . '  <system.webServer>' . "\n"
                . '    <security>' . "\n"
                . '      <authorization>' . "\n"
                . '        <deny users="*" />' . "\n"
                . '      </authorization>' . "\n"
                . '    </security>' . "\n"
                . '    <directoryBrowse enabled="false" />' . "\n"
                . '  </system.webServer>' . "\n"
                . '</configuration>' . "\n";
            @file_put_contents($webconfig, $wc);
        }

        // Placeholder index to avoid directory listing on odd setups
        $index = $dir . DIRECTORY_SEPARATOR . 'index.html';
        if (!file_exists($index)) {
            @file_put_contents($index, "<!doctype html><title>403</title><h1>Forbidden</h1>");
        }
    }

    /**
     * Centralized backup directory: /wp-content/softdiscover/backups
     * Ensures directory exists and is hardened against direct web access.
     */
    private function get_backup_directory()
    {
        $dir = trailingslashit(WP_CONTENT_DIR) . 'softdiscover/backups';
        if (!is_dir($dir)) {
            if (function_exists('wp_mkdir_p')) {
                @wp_mkdir_p($dir);
            } else {
                @mkdir($dir, 0755, true);
            }
        }
        if (is_dir($dir) && is_writable($dir)) {
            $this->harden_dir($dir);
        }
        return $dir;
    }

    /*
    * restore record
    */
    public function ajax_restore_record()
    {

        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');

        $bkp_id = (isset($_POST['rec_id']) && $_POST['rec_id']) ? Flmbkp_Form_Helper::sanitizeInput($_POST['rec_id']) : 0;

        $log = array();
        $files_dest = WP_CONTENT_DIR . '/uploads/'; // restore into uploads root (unchanged)
        if (intval($bkp_id) > 0) {
            $rec_info = $this->model_backup->getinfo($bkp_id);
            $backup_directory = $this->get_backup_directory();

            //database
            if (file_exists($backup_directory . '/' . $rec_info->bkp_slug . '_database.zip')) {
                require_once(FLMBKP_DIR . '/classes/uiform_backup.php');
                $objClass = new Flmbkp_Backup($rec_info->bkp_slug, $backup_directory);
                if ($objClass->restoreBackup($log)) {
                    $log[] = __('<b>Database backup restored.</b>', 'FRocket_admin');
                } else {
                    $log[] = __('<b>Unable to restore DB backup.</b>', 'FRocket_admin');
                }
            }

            // Plugins
            if (file_exists($backup_directory . '/' . $rec_info->bkp_slug . '_plugins.zip')) {
                $tmp_res = Flmbkp_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug . '_plugins.zip', $files_dest);
                if ($tmp_res) {
                    $log[] = __('<b>Plugins backup restored.</b>', 'FRocket_admin');
                } else {
                    $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }
            }

            // themes
            if (file_exists($backup_directory . '/' . $rec_info->bkp_slug . '_themes.zip')) {
                $tmp_res = Flmbkp_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug . '_themes.zip', $files_dest);
                if ($tmp_res) {
                    $log[] = __('<b>Themes backup restored.</b>', 'FRocket_admin');
                } else {
                    $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }
            }

            // Uploads
            if (file_exists($backup_directory . '/' . $rec_info->bkp_slug . '_uploads.zip')) {
                $tmp_res = Flmbkp_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug . '_uploads.zip', $files_dest);
                if ($tmp_res) {
                    $log[] = __('<b>Uploads backup restored.</b>', 'FRocket_admin');
                } else {
                    $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }
            }

            // Others
            if (file_exists($backup_directory . '/' . $rec_info->bkp_slug . '_others.zip')) {
                $tmp_res = Flmbkp_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug . '_others.zip', $files_dest);
                if ($tmp_res) {
                    $log[] = __('<b>Others backup restored.</b>', 'FRocket_admin');
                } else {
                    $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }
            }
        }

        $json = array(
            'log' => $log,
            'success' => true,
            'modal_title' => __('Restored successfully', 'FRocket_admin'),
            'modal_body' => self::render_template('filemanager/views/backup/restore_message.php', array('log' => $log))
        );

        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }

    /*
    * Delete record
    */
    public function ajax_delete_record()
    {

        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');

        $bkp_id = (isset($_POST['rec_id']) && $_POST['rec_id']) ? Flmbkp_Form_Helper::sanitizeInput($_POST['rec_id']) : 0;

        $rec_info = $this->model_backup->getinfo($bkp_id);

        $backup_directory = $this->get_backup_directory();

        @unlink($backup_directory . '/' . $rec_info->bkp_slug . '_plugins.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug . '_themes.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug . '_database.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug . '_others.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug . '_uploads.zip');

        //delete record
        $this->wpdb->delete($this->model_backup->table, array('bkp_id' => $bkp_id));
    }

    /*
     * Download file
     *
     * FIXED: Prevent path traversal and enforce capability checks.
     */
    public function ajax_downloadfile()
    {
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');

        // Only privileged users may download backups.
        if (!current_user_can('manage_options')) {
            status_header(403);
            wp_die(__('Insufficient permissions.', 'FRocket_admin'));
        }

        @set_time_limit(900);

        // Raw input (no HTML decoding) then minimal normalization
        $flm_file_raw = isset($_GET['flm_file']) ? wp_unslash($_GET['flm_file']) : '';
        $flm_file = Flmbkp_Form_Helper::sanitizeInput($flm_file_raw);

        // Basic presence
        if (!is_string($flm_file) || $flm_file === '') {
            status_header(400);
            wp_die(__('Invalid file requested.', 'FRocket_admin'));
        }

        // Must be a basename only (no slashes/backslashes)
        $norm = str_replace('\\', '/', $flm_file);
        if (strpos($norm, '/') !== false || basename($norm) !== $norm) {
            status_header(400);
            wp_die(__('Invalid file requested.', 'FRocket_admin'));
        }

        // No control chars / null bytes / traversal tokens / hidden dotfiles
        if (
            strpos($flm_file, "\0") !== false ||
            preg_match('/[[:cntrl:]]/u', $flm_file) ||
            preg_match('#(^|[\\/])\.{1,2}([\\/]|$)#', $flm_file) ||
            $flm_file[0] === '.'
        ) {
            status_header(400);
            wp_die(__('Invalid file requested.', 'FRocket_admin'));
        }

        // Reasonable length + strict allowed characters
        if (strlen($flm_file) > 200 || !preg_match('/^[A-Za-z0-9._-]+$/', $flm_file)) {
            status_header(400);
            wp_die(__('Invalid file requested.', 'FRocket_admin'));
        }

        // enforce expected backup naming pattern
        if (!preg_match('/^flmbkp_\d{14,}_(plugins|themes|uploads|others|database)\.zip$/', $flm_file)) {
            status_header(400);
            wp_die(__('Invalid file name.', 'FRocket_admin'));
        }

        // Allow only specific extensions (backups are produced as .zip)
        $allowed_exts = apply_filters('flmbkp_allowed_download_exts', array('zip'));
        $ext = strtolower(pathinfo($flm_file, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_exts, true)) {
            status_header(400);
            wp_die(__('Invalid file type.', 'FRocket_admin'));
        }

        // Resolve paths safely
        $backup_directory = $this->get_backup_directory();
        $backup_directory_real = realpath($backup_directory);

        if (!$backup_directory_real || !is_dir($backup_directory_real)) {
            status_header(500);
            wp_die(__('Backup directory unavailable.', 'FRocket_admin'));
        }

        // Build candidate path within backup directory and resolve
        $candidate = $backup_directory_real . DIRECTORY_SEPARATOR . $flm_file;
        $fullpath  = realpath($candidate);

        // Ensure the resolved path is inside the backup directory
        if (!$fullpath || strpos($fullpath, $backup_directory_real . DIRECTORY_SEPARATOR) !== 0) {
            status_header(400);
            wp_die(__('Invalid path.', 'FRocket_admin'));
        }

        if (!is_file($fullpath) || !is_readable($fullpath)) {
            status_header(404);
            wp_die(__('File not found.', 'FRocket_admin'));
        }

        // Stream file to client with safe headers
        nocache_headers();
        header('Content-Type: application/octet-stream');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="' . basename($fullpath) . '";');
        header('Content-Length: ' . (string) filesize($fullpath));

        if (ob_get_length()) {
            @ob_end_clean();
        }

        readfile($fullpath);
        exit;
    }

    /**
     * list backups
     *
     * @mvc Controller
     */
    public function list_backups()
    {
        require_once(FLMBKP_DIR . '/classes/Pagination.php');
        $this->pagination = new CI_Pagination();
        $offset = (isset($_GET['offset']) && $_GET['offset']) ? Flmbkp_Form_Helper::sanitizeInput($_GET['offset']) : 0;
        //list all forms
        $data = $config = array();
        $config['base_url'] = admin_url() . '?page=flmbkp_file_manager&zgfm_mod=filemanager&zgfm_contr=backup&zgfm_action=list_backups';
        $config['total_rows'] = $this->model_backup->CountRecords();
        $config['per_page'] = $this->per_page;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'offset';

        $this->pagination->initialize($config);
        // If the pagination library doesn't recognize the current page add:
        $this->pagination->cur_page = $offset;
        $data['query'] = $this->model_backup->getListBackups($this->per_page, $offset);
        $data['pagination'] = $this->pagination->create_links();

        echo self::loadPartial('layout_blank.php', 'filemanager/views/backup/list_backups.php', $data);
    }

    /**
     * receiving header options
     *
     * @mvc Controller
     */
    public function ajax_create_records()
    {

        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');

        $tmp_data = (isset($_POST['options'])) ? Flmbkp_Form_Helper::sanitizeInput_html($_POST['options']) : '';
        $data2 = array();
        foreach (explode('&', $tmp_data) as $value) {
            $value1 = explode('=', $value);
            $data2[] = Flmbkp_Form_Helper::sanitizeInput($value1[1]);
        }

        $data = array();
        $data['bkp_slug'] = 'flmbkp_' . date("YmdHis");
        $this->wpdb->insert($this->model_backup->table, $data);
        $idActivate = $this->wpdb->insert_id;

        $json = array();
        $json['status'] = 'created';
        $json['id'] = $idActivate;
        $json['slug'] = $data['bkp_slug'];
        $json['next_task'] = $data2[0];
        $json['url_redirect'] = admin_url('admin.php?page=flmbkp_page_backups');
        $json['pending'] = $data2;

        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }


    /**
     * receiving header options
     *
     * @mvc Controller
     */
    public function ajax_submit_options_switch()
    {
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');
        $tmp_nexstep = (isset($_POST['nexstep'])) ? Flmbkp_Form_Helper::sanitizeInput($_POST['nexstep']) : '';

        switch (strval($tmp_nexstep)) {
            case 'plugins':
                $tmp_targets = array(ABSPATH . '/wp-content/plugins');
                $this->ajax_submit_options($tmp_targets, $tmp_nexstep);
                break;
            case 'themes':
                $tmp_targets = array(ABSPATH . '/wp-content/themes');
                $this->ajax_submit_options($tmp_targets, $tmp_nexstep);
                break;
            case 'uploads':
                $tmp_targets = array(ABSPATH . '/wp-content/uploads');
                $this->ajax_submit_options($tmp_targets, $tmp_nexstep);
                break;
            case 'others':
                $tmp_targets = array();
                // exclude the private backup dir & common folders we back up separately
                $tmp_targets = $this->listAndExcludeDIr(ABSPATH . 'wp-content', array('uploads', 'themes', 'plugins', 'softdiscover', 'debug.log'));
                $this->ajax_submit_options($tmp_targets, $tmp_nexstep);
                break;
            case 'database':
                $this->ajax_submit_backupdb();
                break;
            default:
                die('something happened');
        }
    }

    public function mysql_version()
    {

        if (!version_compare('5.5', phpversion(), '>=')) {
            $database_name = DB_NAME;
            $database_user = DB_USER;
            $datadase_password = DB_PASSWORD;
            $database_host = DB_HOST;

            $con = mysqli_connect($database_host, $database_user, $datadase_password, $database_name);
            if (mysqli_connect_errno()) {
                // connection error ignored
            }

            $str = mysqli_get_server_info($con);
        } else {
            $str = mysql_get_server_info();
        }

        return $str;
    }

    /*
     * backup database
     */
    public function ajax_submit_backupdb()
    {
        $tmp_flmbkp_slug = (isset($_POST['flmbkp_slug'])) ? urldecode(Flmbkp_Form_Helper::sanitizeInput($_POST['flmbkp_slug'])) : 'flmbkp_err' . date("YmdHis");
        $this->is_initial_run = !empty($_POST['is_initial_run']);
        require_once FLMBKP_DIR . '/modules/filemanager/helpers/iprogress.php';

        $backup_directory = $this->get_backup_directory();

        $this->progress  = new iProgress('zip', 200);
        $this->oFile = ($this->is_initial_run || !$this->progress->getData('oFile')) ? $backup_directory . '/' . $tmp_flmbkp_slug . '_database.zip' : $this->progress->getData('oFile');
        $this->progress->setData('oFile', $this->oFile);

        $dump = '';
        $database = DB_NAME;
        $server = DB_HOST;
        $dump .= '-- --------------------------------------------------------------------------------' . NL;
        $dump .= '-- ' . NL;
        $dump .= '-- @version: ' . $database . '.sql ' . date('M j, Y') . ' ' . date('H:i') . ' Softdiscover' . NL;
        $dump .= '-- @package Database & File Manager' . NL;
        $dump .= '-- @author softdiscover.com.' . NL;
        $dump .= '-- @copyright 2015' . NL;
        $dump .= '-- ' . NL;
        $dump .= '-- --------------------------------------------------------------------------------' . NL;
        $dump .= '-- Host: ' . $server . NL;
        $dump .= '-- Database: ' . $database . NL;
        $dump .= '-- Time: ' . date('M j, Y') . '-' . date('H:i') . NL;
        $dump .= '-- MySQL version: ' . $this->mysql_version() . NL;
        $dump .= '-- PHP version: ' . phpversion() . NL;
        $dump .= '-- --------------------------------------------------------------------------------;' . NL . NL;

        $tables = $this->getTables();
        if (!empty($tables)) {
            foreach ($tables as $key => $table) {
                $table_dump = $this->dumpTable($table);
                if (!($table_dump)) {
                    return false;
                }
                $dump .= $table_dump;
            }
        }

        $fname = $backup_directory . '/' . $tmp_flmbkp_slug . '_database.sql';
        if (!($f = fopen($fname, 'w'))) {
            return false;
        }
        fwrite($f, $dump);
        fclose($f);

        $this->zip_obj = new ZipArchive();
        $openRes = $this->zip_obj->open($this->oFile, ZipArchive::CREATE);
        if ($openRes !== true) {
            wp_send_json_error(array('message' => 'Failed to create DB zip (code '.$openRes.')'), 500);
        }
        $this->zip_obj->addFile($fname, basename($fname));
        $this->zip_obj->close();

        //delete sql file
        @unlink($fname);

        $json = array(
            'error' => false,
            'continue' => false,
            'fileURL' => '', // no public URL exposure
            'next_task' => '',
            'is_finished' => true
        );

        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }

    public function getTables()
    {
        $value = array();
        if (!($result = $this->wpdb->get_results("SHOW TABLES"))) {
            return false;
        }
        foreach ($result as $mytable) {
            foreach ($mytable as $t) {
                $value[] = $t;
            }
        }
        if (!sizeof($value)) {
            return false;
        }

        return $value;
    }


    public function dumpTable($table)
    {
        $this->wpdb->query('LOCK TABLES ' . $table . ' WRITE');

        $output = '';
        $result = $this->wpdb->get_results("SELECT * FROM {$table}", ARRAY_N);

        $output .= '-- --------------------------------------------------' . NL;
        $output .= '-- Table structure for table `' . $table . '`' . NL;
        $output .= '-- --------------------------------------------------;' . NL;
        $output .= 'DROP TABLE IF EXISTS `' . $table . '`;' . NL;
        $row2 = $this->wpdb->get_row('SHOW CREATE TABLE ' . $table, ARRAY_N);
        $output .= "\n\n" . $row2[1] . ";\n\n";
        for ($i = 0; $i < count($result); $i++) {
            $row = $result[$i];
            $output .= 'INSERT INTO ' . $table . ' VALUES(';
            for ($j = 0; $j < count($result[0]); $j++) {
                $row[$j] = $this->wpdb->_real_escape($row[$j]);
                $output .= (isset($row[$j])) ? '"' . $row[$j] . '"' : '""';
                if ($j < (count($result[0]) - 1)) {
                    $output .= ',';
                }
            }
            $output .= ");\n";
        }
        $output .= "\n";

        $this->wpdb->query('UNLOCK TABLES');
        return $output;
    }

    /**
     * receiving header options
     *
     * @mvc Controller
     */
    public function ajax_submit_options($tmp_targets, $tmp_nexstep)
    {
        try {
            $is_initial_run = (isset($_POST['is_initial_run'])) ? Flmbkp_Form_Helper::sanitizeInput($_POST['is_initial_run']) : 0;
            $flush_to_disk = (isset($_POST['flush_to_disk'])) ? Flmbkp_Form_Helper::sanitizeInput($_POST['flush_to_disk']) : 50;
            $max_execution_time = (isset($_POST['max_execution_time'])) ? Flmbkp_Form_Helper::sanitizeInput($_POST['max_execution_time']) : 20;
            $tmp_flmbkp_slug = (isset($_POST['flmbkp_slug'])) ? urldecode(Flmbkp_Form_Helper::sanitizeInput($_POST['flmbkp_slug'])) : 'flmbkp_err' . date("YmdHis");

            $this->startTime = microtime(true);

            require_once FLMBKP_DIR . '/modules/filemanager/helpers/iprogress.php';

            $this->progress  = new iProgress('zip', 200);

            $json = array();

            $this->is_initial_run = !empty($is_initial_run);

            $this->max_execution_time = !empty($max_execution_time) ? (int) $max_execution_time : 20;
            $exclude_string = array();
            $this->excludes = (!empty($exclude_string)) ? array_filter(array_map('trim', explode(',', $exclude_string))) : array();

            $targets = ($this->is_initial_run && !empty($tmp_targets)) ? $tmp_targets : $this->progress->getData('targets');

            if (!$targets) {
                $json['error'] = true;
                $json['msg'] = 'Bad targets';
                echo json_encode($json);
                wp_die();
            }

            if ($this->is_initial_run) {
                $this->progress->clear();
                $this->progress->addMsg('Scanning files to be compressed...');
                $this->progress->setData('targets', $targets);
                $this->progress->setData('abort', 0);
            }

            $this->total_targets = $this->is_initial_run ? 0 : $this->progress->getMax();
            $true_targets = array();

            clearstatcache(true);

            foreach ($targets as $target) {
                $path = realpath($target);

                if (file_exists($path)) {
                    if ($this->is_initial_run) {
                        if (is_dir($path)) {
                            $this->total_targets += $this->count_dir_files($path);
                        } else {
                            $this->total_targets++;
                        }
                    }

                    $true_targets[] = $path;
                }
            }

            if ($this->is_initial_run) {
                $this->progress->addMsg('Found ' . $this->total_targets . ' items for zipping');
                $this->progress->setMax($this->total_targets);
            }

            $backup_directory = $this->get_backup_directory();

            $this->oFile = ($this->is_initial_run || !$this->progress->getData('oFile')) ? $backup_directory . '/' . $tmp_flmbkp_slug . '_' . $tmp_nexstep . '.zip' : $this->progress->getData('oFile');

            $this->progress->setData('oFile', $this->oFile);

            chdir(sys_get_temp_dir()); // Zip always gets created in current working dir so move to tmp.

            $this->zip_obj = new ZipArchive();
            $openRes = $this->zip_obj->open($this->oFile, ZipArchive::CREATE);
            if ($openRes !== true) {
                wp_send_json_error(array('message' => 'Failed to open zip (code '.$openRes.')'), 500);
            }

            $this->iteration_number = 0;

            if ($this->total_targets && $true_targets) {
                foreach ($true_targets as $target) {
                    $this->abort_if_requested();
                    if ($this->is_excluded($target)) {
                        continue;
                    }

                    $execution_time = microtime(true) - $this->startTime;
                    if ($execution_time >= $this->max_execution_time) {
                        $this->stop_iteration();
                    }

                    set_time_limit(60);
                    if (is_dir($target)) {
                        if ($this->iteration_number > $this->progress->getProgress(false)) {
                            $this->progress->addMsg('Adding directory "' . $target . '"');
                            $this->zip_dir($target, basename($target));
                        } else {
                            $this->zip_dir($target, basename($target));
                        }
                    } else {
                        $this->iteration_number++;
                        if ($this->iteration_number > $this->progress->getProgress(false)) {
                            $this->progress->addMsg('Adding file "' . $target . '"');

                            if (file_exists($target) && is_file($target)) {
                                $this->zip_obj->addFile($target, basename($target));
                            }

                            $this->progress->iterateWith(1);

                            $this->flush_zip(); //Write to disk regularly to free memory
                        }
                    }
                }
                $this->progress->addMsg('--- The output file is: ' . $this->oFile . ' ---');
                $this->progress->addMsg('--- Finished! ---');
            }

            $this->zip_obj->close();

            // Don't expose public URLs; downloads must go through ajax_downloadfile
            $json = array(
                'error' => false,
                'continue' => false,
                'fileURL' => '', // keep empty to avoid direct access attempts
                'is_finished' => false
            );

            header('Content-Type: application/json');
            echo json_encode($json);
            wp_die();
        } catch (Exception $exception) {
            $json = array(
                'error' => true,
                'continue' => false,
                'error_msg' => $exception->getMessage(),
                'is_finished' => false
            );

            header('Content-Type: application/json');
            echo json_encode($json);
            wp_die();
        }
    }

    /**
     * list and exclude directory
     *
     * @mvc Controller
     */
    public function listAndExcludeDIr($dir, $exclude = array())
    {

        if (!is_dir($dir)) {
            return array();
        }

        $acceptedfiles = array();
        $entries = scandir($dir);
        foreach ($entries as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $full_path = $dir . '/' . $file;

            if (is_dir($full_path) && $file != "." && $file != ".." && !in_array($file, $exclude)) {
                $acceptedfiles[] = $full_path;
            } elseif ($file != "." && $file != ".." && !in_array($file, $exclude)) {
                $acceptedfiles[] = $full_path;
            } else {
                // excluded
            }
        }
        return $acceptedfiles;
    }

    /**
     * backup process progress
     *
     * @mvc Controller
     */
    public function ajax_watchprogress()
    {

        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');

        require_once FLMBKP_DIR . '/modules/filemanager/helpers/iprogress.php';

        $this->progress = new iProgress('zip', 200);

        $json = array(
            'msgs' => $this->array_flat($this->progress->getMessages()),
            'percent' => $this->progress->getProgressPercent()
        );

        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }

    /**
     * request cancel (set abort flag)
     */
    public function ajax_cancel_backup()
    {
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');

        require_once FLMBKP_DIR . '/modules/filemanager/helpers/iprogress.php';
        $this->progress = new iProgress('zip', 200);

        if (method_exists($this->progress, 'requestAbort')) {
            $this->progress->requestAbort();
        } else {
            $this->progress->setData('abort', 1);
        }

        wp_send_json_success(array('aborted' => true));
    }

    /**
     * cleanup partial files for a given slug and remove DB record
     */
    public function ajax_cleanup_backup()
    {
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');

        $slug_raw = isset($_POST['flmbkp_slug']) ? wp_unslash($_POST['flmbkp_slug']) : '';
        $slug = Flmbkp_Form_Helper::sanitizeInput($slug_raw);

        if (!$this->is_valid_slug($slug)) {
            wp_send_json_error(array('message' => 'Invalid slug'), 400);
        }

        $deleted = $this->delete_partial_files($slug);

        if (!empty($this->model_backup->table)) {
            $this->wpdb->delete($this->model_backup->table, array('bkp_slug' => $slug));
        }

        wp_send_json_success(array('cleaned' => true, 'files_removed' => $deleted));
    }

    /**
     * array flat function
     *
     * @mvc Controller
     */
    private function array_flat($arr)
    {
        $result = array();
        foreach ($arr as $el) {
            if (is_array($el)) {
                $result = array_merge($result, $this->array_flat($el));
            } else {
                $result[] = $el;
            }
        }
        return $result;
    }

    /**
     * generate zip file
     *
     * @mvc Controller
     */
    public function generate_zip_files()
    {
        $path = FLMBKP_DIR . '/assets/';
        echo "Zipping " . $path . "\n";
        $zip = new ZipArchive();
        $this->zip_obj->open('archive.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {
            if ($file->isDir()) {
                echo $name . "\n";
                flush();
                continue;
            }

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($path) + 1);

            $this->zip_obj->addFile($filePath, $relativePath);
        }
        $this->zip_obj->close();
    }

    public function flush_zip()
    {
        // Close current handle then reopen in CREATE mode to avoid
        // "Unexpected length of data" on final close.
        if ($this->zip_obj instanceof ZipArchive) {
            $this->zip_obj->close();
        }
        $this->zip_obj = new ZipArchive();
        $openRes = $this->zip_obj->open($this->oFile, ZipArchive::CREATE);
        if ($openRes !== true) {
            $json = array(
                'error'     => true,
                'continue'  => false,
                'error_msg' => 'Failed to reopen zip (code '.$openRes.') for '.$this->oFile
            );
            header('Content-Type: application/json');
            echo json_encode($json);
            exit;
        }
    }

    public function zip_dir($path, $base = '')
    {
        $progress = $this->progress;

        $entries = scandir($path);

        foreach ($entries as $entry) {
            $this->abort_if_requested();

            $execution_time = microtime(true) - $this->startTime;
            if ($execution_time >= $this->max_execution_time) {
                $this->stop_iteration();
            }

            if (in_array($entry, array('.', '..'))) {
                continue;
            }
            set_time_limit(60);

            $full_path = rtrim($path) . '/' . $entry;
            if ($this->is_excluded($full_path)) {
                continue;
            }

            if (is_dir($full_path)) {
                if ($this->iteration_number > $this->progress->getProgress(false)) {
                    $this->progress->addMsg('Adding directory "' . $full_path . '"');
                    $this->zip_dir($full_path, $base . '/' . $entry);
                } else {
                    $this->zip_dir($full_path, $base . '/' . $entry);
                }
            } else {
                $this->iteration_number++;
                if ($this->iteration_number > $this->progress->getProgress(false)) {
                    $this->progress->addMsg('Adding file "' . $full_path . '"');
                    $this->zip_obj->addFile($full_path, $base . '/' . $entry);
                    $this->progress->iterateWith(1);

                    if ($this->zip_obj->numFiles % 50 == 0) {
                        $this->flush_zip(); //Write to disk every 50 files
                    }
                }
            }
        }
    }

    public function stop_iteration()
    {
        if ($this->zip_obj instanceof ZipArchive) {
            $this->zip_obj->close();
        }

        $json = array(
            'error' => false,
            'continue' => true
        );
        echo json_encode($json);
        exit;
    }

    private function is_excluded($path)
    {
        $excludes = $this->excludes;

        if (!empty($excludes)) {
            foreach ($excludes as $e) {
                if (strpos($path, $e) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function build_exclude_find_params()
    {
        $excludes = $this->excludes;
        $params = '';

        if (!empty($excludes)) {
            foreach ($excludes as $e) {
                $params .= ' -not -path "*' . $e . '*"';
            }
        }
        return $params;
    }

    public function count_dir_files($path)
    {
        $use_system_calls = false;

        $path = rtrim($path, '/');
        if ($use_system_calls) {
            exec('find ' . $path . ' -follow -type f' . $this->build_exclude_find_params() . ' | wc -l', $output);
            if (!empty($output[0])) {
                return (int) trim($output[0]);
            }
            return 0;
        } else {
            $total = 0;
            if (!$this->is_excluded($path)) {
                if (is_dir($path)) {
                    $dh = opendir($path);
                    while (false !== ($entry = readdir($dh))) {
                        if (!in_array($entry, array('.', '..')) && !$this->is_excluded($entry)) {
                            $full_path = $path . '/' . $entry;
                            if (is_dir($full_path)) {
                                $total += $this->count_dir_files($full_path);
                            } else {
                                $total++;
                            }
                        }
                    }
                } else {
                    $total++;
                }
            }
            return $total;
        }
    }

    public function abort_if_requested()
    {
        $progress = $this->progress;
        $last_abort_check = $this->last_abort_check;
        if ((microtime(true) - $last_abort_check) > 0.5) {
            if ($progress->abortCalled()) {
                $this->stop_iteration();
            }
            $last_abort_check = microtime(true);
        }
    }

    /**
     * Helper: validate backup slug
     */
    private function is_valid_slug($slug)
    {
        return is_string($slug) && preg_match('/^flmbkp_\d{14,}$/', $slug);
    }

    /**
     * Helper: delete partial files for a slug
     * @return int number of files deleted
     */
    private function delete_partial_files($slug)
    {
        $backup_directory = $this->get_backup_directory();
        $deleted = 0;

        $suffixes = array('plugins', 'themes', 'uploads', 'others', 'database');
        foreach ($suffixes as $sfx) {
            $file = $backup_directory . '/' . $slug . '_' . $sfx . '.zip';
            if (is_file($file)) {
                @unlink($file);
                if (!file_exists($file)) {
                    $deleted++;
                }
            }
        }

        // Also clean any stray .sql temporary (for database step before zipping)
        $sql = $backup_directory . '/' . $slug . '_database.sql';
        if (is_file($sql)) {
            @unlink($sql);
            if (!file_exists($sql)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Register callbacks for actions and filters
     *
     * @mvc Controller
     */
    public function register_hook_callbacks()
    {
    }

    /**
     * Initializes variables
     *
     * @mvc Controller
     */
    public function init()
    {

        try {
            // initialization
        } catch (Exception $exception) {
            add_notice(__METHOD__ . ' error: ' . $exception->getMessage(), 'error');
        }
    }

    /*
     * Instance methods
     */

    /**
     * Prepares sites to use the plugin during single or network-wide activation
     *
     * @mvc Controller
     *
     * @param bool $network_wide
     */
    public function activate($network_wide)
    {
        return true;
    }

    /**
     * Rolls back activation procedures when de-activating the plugin
     *
     * @mvc Controller
     */
    public function deactivate()
    {
        return true;
    }

    /**
     * Checks if the plugin was recently updated and upgrades if necessary
     *
     * @mvc Controller
     *
     * @param string $db_version
     */
    public function upgrade($db_version = 0)
    {
        return true;
    }

    /**
     * Checks that the object is in a correct state
     *
     * @mvc Model
     *
     * @param string $property An individual property to check, or 'all' to check all of them
     * @return bool
     */
    protected function is_valid($property = 'all')
    {
        return true;
    }
}
