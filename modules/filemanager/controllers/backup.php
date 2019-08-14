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
 * @link      http://wordpress-cost-estimator.zigaform.com
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
 * @link      http://wordpress-cost-estimator.zigaform.com
 */
class flmbkp_Filemanager_Controller_Backup extends Uiform_Base_Module {
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
    var $per_page = 10;

    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct() {

        global $wpdb;
        $this->wpdb = $wpdb;
        $this->model_backup = self::$_models['filemanager']['backup'];

        //create records
        add_action('wp_ajax_flmbkp_backup_createrec', array(&$this, 'ajax_create_records'));
        
        //submit header options
        add_action('wp_ajax_flmbkp_backup_sendoptions', array(&$this, 'ajax_submit_options_switch'));

        //backup process
        add_action('wp_ajax_flmbkp_backup_watchprogress', array(&$this, 'ajax_watchprogress'));
        
        //download file
        add_action('wp_ajax_flmbkp_backup_downloadfile', array(&$this, 'ajax_downloadfile'));
        
        //delete record
        add_action('wp_ajax_flmbkp_backup_delete_records', array(&$this, 'ajax_delete_record'));
        
        //restore record
        add_action('wp_ajax_flmbkp_backup_restore_records', array(&$this, 'ajax_restore_record'));
        
        define('nl', "\r\n");
        
       
        
    }
    
 
     /*
     * restore record
     */
    public function ajax_restore_record() {
        
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');
        
        $bkp_id = (isset($_POST['rec_id']) && $_POST['rec_id']) ? Uiform_Form_Helper::sanitizeInput($_POST['rec_id']) : 0;
        
        
        $log = array();
        $files_dest = WP_CONTENT_DIR.'/';
        if(intval($bkp_id)>0){
            $rec_info=$this->model_backup->getinfo($bkp_id);
            $backup_directory = Uiform_Form_Helper::backup_directory();
            
            //database
            if(file_exists($backup_directory . '/' . $rec_info->bkp_slug .'_database.zip')) {              
                    require_once( FLMBKP_DIR . '/classes/uiform_backup.php');
                    $objClass = new Uiform_Backup($rec_info->bkp_slug,$backup_directory);
                    if($objClass->restoreBackup($log)) {
                      $log[] = __('<b>Database backup restored.</b>', 'FRocket_admin');
                    } else {
                      $log[] = __('<b>Unable to restore DB backup.</b>', 'FRocket_admin'); 
                    }
                }
            
            // Plugins
            if(file_exists($backup_directory . '/' . $rec_info->bkp_slug .'_plugins.zip')) {
                $tmp_res = Uiform_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug .'_plugins.zip',$files_dest);
                if($tmp_res) {
                  $log[] = __('<b>Plugins backup restored.</b>', 'FRocket_admin');  
                } else {
                  $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }                                      
            }
            
            // themes
            if(file_exists($backup_directory . '/' . $rec_info->bkp_slug .'_themes.zip')) {
                $tmp_res = Uiform_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug .'_themes.zip',$files_dest);
                if($tmp_res) {
                  $log[] = __('<b>Themes backup restored.</b>', 'FRocket_admin'); 
                } else {
                  $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }                                      
            }

            // Uploads
            if(file_exists($backup_directory . '/' . $rec_info->bkp_slug .'_uploads.zip')) {
                $tmp_res = Uiform_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug .'_uploads.zip',$files_dest);
                if($tmp_res) {
                  $log[] = __('<b>Uploads backup restored.</b>', 'FRocket_admin');
                } else {
                  $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }                                      
            }
            
            // Others
            if(file_exists($backup_directory . '/' . $rec_info->bkp_slug .'_others.zip')) {
                $tmp_res = Uiform_Form_Helper::unzipFiles($backup_directory . '/' . $rec_info->bkp_slug .'_others.zip',$files_dest);
                if($tmp_res) {
                  $log[] = __('<b>Others backup restored.</b>', 'FRocket_admin');
                } else {
                  $log[] = __('<b>Unable to restore plugins.</b>', 'FRocket_admin');
                }                                      
            }            
            
        }
        
        $json = array(
            'log' => $log,
            'success' => true,
            'modal_title'=>__('Restored successfully', 'FRocket_admin'),
            'modal_body'=>self::render_template('filemanager/views/backup/restore_message.php', array('log'=>$log))
        );
        
        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
        
    } 
    
     /*
     * Delete record
     */
    public function ajax_delete_record() {
        
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');
        
        $bkp_id = (isset($_POST['rec_id']) && $_POST['rec_id']) ? Uiform_Form_Helper::sanitizeInput($_POST['rec_id']) : 0;
            
        $rec_info=$this->model_backup->getinfo($bkp_id);
            
        $backup_directory = Uiform_Form_Helper::backup_directory();
        
        @unlink($backup_directory . '/' . $rec_info->bkp_slug .'_plugins.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug .'_themes.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug .'_database.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug .'_others.zip');
        @unlink($backup_directory . '/' . $rec_info->bkp_slug .'_uploads.zip');
        
        //de;ete recprd
        $this->wpdb->delete($this->model_backup->table, array( 'bkp_id' => $bkp_id));
        
    } 
    
    /*
     * Download file
     */
    public function ajax_downloadfile(){
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');
        @set_time_limit(900);
        $flm_file = (isset($_GET['flm_file'])) ? Uiform_Form_Helper::sanitizeInput_html($_GET['flm_file']) : '';
        
        $backup_directory=Uiform_Form_Helper::backup_directory();
        $fullpath = $backup_directory.'/'.$flm_file;
        
        header("Content-Length: ".filesize($fullpath));
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".basename($fullpath)."\";");
        readfile($fullpath);
        exit;
    }
    
    /**
     * list backups
     *
     * @mvc Controller
     */
    public function list_backups() {
        require_once( FLMBKP_DIR . '/classes/Pagination.php');
        $this->pagination = new CI_Pagination();
        $offset = (isset($_GET['offset']) && $_GET['offset']) ? Uiform_Form_Helper::sanitizeInput($_GET['offset']) : 0;
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
    public function ajax_create_records() {

        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');
        $tmp_nexstep = (isset($_POST['nexstep'])) ? urldecode(Uiform_Form_Helper::sanitizeInput_html($_POST['nexstep'])) : '';
         $tmp_data = (isset($_POST['options'])) ? Uiform_Form_Helper::sanitizeInput_html($_POST['options']) : '';
        $data2 = array();
        foreach (explode('&', $tmp_data) as $value) {
            $value1 = explode('=', $value);
            $data2[] = Uiform_Form_Helper::sanitizeInput($value1[1]);
        }
        
        
        $data=array();
        $data['bkp_slug']='flmbkp_'.date("YmdHis");
        $this->wpdb->insert($this->model_backup->table, $data);
        $idActivate = $this->wpdb->insert_id;
        $json=array();
        $json['status'] = 'created';
        $json['id'] = $idActivate;
        $json['slug'] = $data['bkp_slug'];
        $json['next_task']=$data2[0];
        $json['url_redirect']=admin_url( 'admin.php?page=flmbkp_page_backups');
        $json['pending']=$data2;
        
        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }
    
    
    /**
    * receiving header options
    *
    * @mvc Controller
    */
    public function ajax_submit_options_switch() {
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');
        $tmp_nexstep = (isset($_POST['nexstep'])) ? Uiform_Form_Helper::sanitizeInput_html($_POST['nexstep']) : '';
            
        switch(strval($tmp_nexstep)){
            case 'plugins':
             //assigning targets
                $tmp_targets=array(ABSPATH.'/wp-content/plugins');
                
                $this->ajax_submit_options($tmp_targets,$tmp_nexstep);
            break;
            case 'themes':
             //assigning targets
                $tmp_targets=array(ABSPATH.'/wp-content/themes');
                
                $this->ajax_submit_options($tmp_targets,$tmp_nexstep);
            break;
            case 'uploads':
                $tmp_targets=array(ABSPATH.'/wp-content/uploads');
                
                $this->ajax_submit_options($tmp_targets,$tmp_nexstep);
                break;
            case 'others':
                $tmp_targets=array();
                $tmp_targets=$this->listAndExcludeDIr(ABSPATH.'wp-content', array('uploads','themes','plugins','softdiscover','debug.log'));
                
                $this->ajax_submit_options($tmp_targets,$tmp_nexstep);
                break;
            case 'database':
                $this->ajax_submit_backupdb();
                break;
            default:
                die('something happened');
                break;
        }
        
    }
    
    public function mysql_version()
	{
            
            if (!version_compare('5.5', phpversion(), '>=')) {
                    
                 $database_name=DB_NAME;
                $database_user=DB_USER;				
                $datadase_password=DB_PASSWORD;
                $database_host=DB_HOST;
                
                    $con=mysqli_connect($database_host,$database_user,$datadase_password,$database_name);
                    // Check connection
                    if (mysqli_connect_errno())
                    {
                   // echo "Failed to connect to MySQL: " . mysqli_connect_error();
                    }

                   $str = mysqli_get_server_info($con);
                }else{
                   $str = mysql_get_server_info();
                }

		return $str;
	}
    
    /*
     * backup database
     */
    public function ajax_submit_backupdb() {
        $tmp_flmbkp_slug = (isset($_POST['flmbkp_slug'])) ? urldecode(Uiform_Form_Helper::sanitizeInput_html($_POST['flmbkp_slug'])) : 'flmbkp_err'.date("YmdHis");
        $this->is_initial_run = !empty($_POST['is_initial_run']);
        require_once FLMBKP_DIR . '/modules/filemanager/helpers/iprogress.php';
        
        $backup_directory = Uiform_Form_Helper::backup_directory();
        
        $this->progress  = new iProgress('zip', 200);
         $this->oFile = ($this->is_initial_run || !$this->progress->getData('oFile')) ? $backup_directory . '/' . $tmp_flmbkp_slug .'_database.zip' : $this->progress->getData('oFile');
        $this->progress->setData('oFile', $this->oFile);
        
          $dump = '';
            $database = DB_NAME;
            $server = DB_HOST;
          $dump .= '-- --------------------------------------------------------------------------------' . nl;
          $dump .= '-- ' . nl;
          $dump .= '-- @version: ' . $database . '.sql ' . date('M j, Y') . ' ' . date('H:i') . ' Softdiscover' . nl;
          $dump .= '-- @package Database & File Manager' . nl;
          $dump .= '-- @author softdiscover.com.' . nl;
          $dump .= '-- @copyright 2015' . nl;
          $dump .= '-- ' . nl;
          $dump .= '-- --------------------------------------------------------------------------------' . nl;
          $dump .= '-- Host: ' . $server . nl;
          $dump .= '-- Database: ' . $database . nl;
          $dump .= '-- Time: ' . date('M j, Y') . '-' . date('H:i') . nl;
          $dump .= '-- MySQL version: ' . $this->mysql_version() . nl;
          $dump .= '-- PHP version: ' . phpversion() . nl;
          $dump .= '-- --------------------------------------------------------------------------------;' . nl . nl;
        
          $tables = $this->getTables();
          if(!empty($tables)){
             foreach ($tables as $key=>$table) {
                $table_dump = $this->dumpTable($table); 
                 
                 if (!($table_dump)) {
                    return false;
                }
                $dump .= $table_dump;
            }   
          }
          
        
          $fname = $backup_directory;
              $fname .= '/'.$tmp_flmbkp_slug .'_database';
              $fname .= '.sql';
              if (!($f = fopen($fname, 'w'))) {
                  return false;
              }
              fwrite($f,$dump);
              fclose($f);
       
        $this->zip_obj = new ZipArchive();
        $this->zip_obj->open($this->oFile, ZipArchive::CREATE);
        $this->zip_obj->addFile($fname, basename($fname));
        $this->zip_obj->close();
        
        //delete sql file
        unlink($fname);
        
        $json = array(
            'error' => false,
            'continue' => false,
            'fileURL' =>'',
            'next_task'=>'',
            'is_finished'=>true
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
        foreach ($result as $mytable)
        {
            foreach ($mytable as $t) 
            {
                
                     $value[]= $t;
                
            }
        }
        if (!sizeof($value)) {
            return false;
        }
        
        return $value;
       
    }
  
    
    public function dumpTable($table)
      {
         
         // $dump = '';
          $this->wpdb->query('LOCK TABLES ' . $table . ' WRITE');
          
        // $tables = $this->wpdb->get_col('SHOW TABLES');
	$output = '';
	//foreach($tables as $table) {
		$result = $this->wpdb->get_results("SELECT * FROM {$table}", ARRAY_N);
            
                $output .= '-- --------------------------------------------------' . nl;
          $output .= '-- Table structure for table `' . $table . '`' . nl;
          $output .= '-- --------------------------------------------------;' . nl;
          $output .= 'DROP TABLE IF EXISTS `' . $table . '`;' . nl;
		$row2 = $this->wpdb->get_row('SHOW CREATE TABLE '.$table, ARRAY_N); 
		$output .= "\n\n".$row2[1].";\n\n";
		for($i = 0; $i < count($result); $i++) {
			$row = $result[$i];
			$output .= 'INSERT INTO '.$table.' VALUES(';
			for($j=0; $j<count($result[0]); $j++) {
				$row[$j] = $this->wpdb->_real_escape($row[$j]);
				$output .= (isset($row[$j])) ? '"'.$row[$j].'"'	: '""'; 
				if ($j < (count($result[0])-1)) {
					$output .= ',';
				}
			}
			$output .= ");\n";
		}
		$output .= "\n";
	//}
          
          $this->wpdb->query('UNLOCK TABLES');
          return $output;
      }
    /**
     * receiving header options
     *
     * @mvc Controller
     */
    public function ajax_submit_options($tmp_targets,$tmp_nexstep) {

        try {
        
        $is_initial_run = (isset($_POST['is_initial_run'])) ? Uiform_Form_Helper::sanitizeInput_html($_POST['is_initial_run']) : 0;
        $flush_to_disk = (isset($_POST['flush_to_disk'])) ? Uiform_Form_Helper::sanitizeInput_html($_POST['flush_to_disk']) : 50;
        $max_execution_time = (isset($_POST['max_execution_time'])) ? Uiform_Form_Helper::sanitizeInput_html($_POST['max_execution_time']) : 20;
        $tmp_flmbkp_slug = (isset($_POST['flmbkp_slug'])) ? urldecode(Uiform_Form_Helper::sanitizeInput_html($_POST['flmbkp_slug'])) : 'flmbkp_err'.date("YmdHis");

        $this->startTime = microtime(true);
            
        //language
        if (isset($data['flpbkp_opt_files']) && intval($data['flpbkp_opt_files']) === 1) {
            // $this->generate_zip_files();
        }
            
        require_once FLMBKP_DIR . '/modules/filemanager/helpers/iprogress.php';
        
        $this->progress  = new iProgress('zip', 200);

        $json = array();

        $this->is_initial_run = !empty($is_initial_run);
        $flush_to_disk = !empty($flush_to_disk) ? (int) $flush_to_disk : 50;
        $this->max_execution_time = !empty($max_execution_time) ? (int) $max_execution_time : 20;
        $exclude_string = array();
        $this->excludes = (!empty($exclude_string)) ? array_filter(array_map('trim', explode(',', $exclude_string))) : array();
//        $use_system_calls = (!empty($_POST['use_system_calls']) && $_POST['use_system_calls'] == 'true') ? true : false;
        $use_system_calls =  false;
        $last_abort_check = microtime(true);
        
        
        
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

        $backup_directory = Uiform_Form_Helper::backup_directory();
        
        $this->oFile = ($this->is_initial_run || !$this->progress->getData('oFile')) ? $backup_directory.'/'. $tmp_flmbkp_slug .'_'.$tmp_nexstep. '.zip' : $this->progress->getData('oFile');
        
        
        
        $this->progress->setData('oFile', $this->oFile);
        
        chdir( sys_get_temp_dir() ); // Zip always get's created in current working dir so move to tmp.

        $this->zip_obj = new ZipArchive();
        $this->zip_obj->open($this->oFile, ZipArchive::CREATE);
            
        $this->iteration_number = 0;

        if ($this->total_targets && $true_targets) {
            foreach ($true_targets as $target) {
                $this->abort_if_requested();
                if ($this->is_excluded($target))
                    continue;

                $execution_time = microtime(true) - $this->startTime;
                if ($execution_time >= $this->max_execution_time)
                    $this->stop_iteration();
             
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
                        
                        if (file_exists($target) && is_file($target))
                            $this->zip_obj->addFile($target, basename($target));
                        
                        
                        $this->progress->iterateWith(1);

                        //if ($this->zip_obj->numFiles % 50 === 0)
                            $this->flush_zip(); //Write to disk every 50 files. This should free the memory taken up to this point
                    }
                }
            }
            $this->progress->addMsg('--- The output file is: ' . $this->oFile . ' ---');
            $this->progress->addMsg('--- Finished! ---');
        }

        $this->zip_obj->close();

        $file_url = FLMBKP_URL . '/' . basename($this->oFile);
        $json = array(
            'error' => false,
            'continue' => false,
            'fileURL' => $file_url,
            'is_finished'=>false
        );
        
        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
        
        } catch (Exception $exception) {
            
            $json = array(
                'error' => true,
                'continue' => false,
                'error_msg'=>$exception->getMessage(),
                'is_finished'=>false
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
    public function listAndExcludeDIr($dir , $exclude=array()) {    
        
        if (!is_dir($dir)) {
                 return array();
            }
        
           $acceptedfiles=array();  
            $entries = scandir($dir);
            //reads the filenames, one by one   
            foreach ($entries as $file) {
                if ($file == '.' || $file == '..') continue;
                
                $full_path = $dir.'/'.$file;
            
                if(is_dir($full_path) && $file!="." && $file!=".." && !in_array($file, $exclude)){
                    $acceptedfiles[]=$full_path; 
                }elseif($file!="." && $file!=".." && !in_array($file, $exclude)){
                    $acceptedfiles[]=$full_path;
                }else{
                    
                }
                
            }
            //closedir($handle); 
            return $acceptedfiles; 
    }
    /**
     * backup process
     *
     * @mvc Controller
     */
    public function ajax_watchprogress() {

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
     * array flat function
     *
     * @mvc Controller
     */
    private function array_flat($arr) {
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
    public function generate_zip_files() {
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

    public function flush_zip() {
       //$zip=$this->zip_obj;
       $this->zip_obj->close();
       $this->zip_obj->open($this->oFile);
       //$this->zip_obj=$zip;
    }

    
    public function zip_dir($path, $base = '') {
        
        $progress = $this->progress;
            
        
        $entries = scandir($path);

        foreach ($entries as $entry) {
            $this->abort_if_requested();

            $execution_time = microtime(true) - $this->startTime;
            if ($execution_time >= $this->max_execution_time)
                $this->stop_iteration();

            if (in_array($entry, array('.', '..')))
                continue;
            set_time_limit(60);

            $full_path = rtrim($path) . '/' . $entry;
            if ($this->is_excluded($full_path))
                continue;

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

                    if ($this->zip_obj->numFiles % 50 == 0)
                        $this->flush_zip(); //Write to disk every 50 files. This should free the memory taken up to this point
                }
            }
        }
    }
    
    public function stop_iteration() {
        //$zip=$this->zip_obj;
        $this->zip_obj->close();

        $json = array(
            'error' => false,
            'continue' => true
        );
        echo json_encode($json);
        exit;
    }

    private function is_excluded($path) {
        $excludes =$this->excludes;

        if (!empty($excludes))
            foreach ($excludes as $e) {
                if (strpos($path, $e) !== false)
                    return true;
            }

        return false;
    }

    public function build_exclude_find_params() {
        $excludes =$this->excludes;
        $params = '';

        if (!empty($excludes))
            foreach ($excludes as $e) {
                $params .= ' -not -path "*' . $e . '*"';
            }
        return $params;
    }

    public function count_dir_files($path) {
        //global $use_system_calls;
        
        $use_system_calls=false;
        
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

    public function abort_if_requested() {
        
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
     * Register callbacks for actions and filters
     *
     * @mvc Controller
     */
    public function register_hook_callbacks() {
        
    }

    /**
     * Initializes variables
     *
     * @mvc Controller
     */
    public function init() {

        try {
            //$instance_example = new WPPS_Instance_Class( 'Instance example', '42' );
            //add_notice('ba');
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
    public function activate($network_wide) {

        return true;
    }

    /**
     * Rolls back activation procedures when de-activating the plugin
     *
     * @mvc Controller
     */
    public function deactivate() {
        return true;
    }

    /**
     * Checks if the plugin was recently updated and upgrades if necessary
     *
     * @mvc Controller
     *
     * @param string $db_version
     */
    public function upgrade($db_version = 0) {
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
    protected function is_valid($property = 'all') {
        return true;
    }

}

?>
