<?php

/**
 * Intranet
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://softdiscover.com/
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('flmbkp_Model_Backup')) {
    return;
}

/**
 * Model Form class
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      https://softdiscover.com/
 */
class flmbkp_Model_Backup
{

    private $wpdb = "";
    public $table = "";

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . "flmbkp_backup";
    }

    private function get_safe_table()
    {
        $table = (string) $this->table;
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            return '';
        }
        return esc_sql($table);
    }
    
    
    public function getinfo($id)
    {
        $id = absint($id);
        if ($id < 1) {
            return null;
        }

        $table = $this->get_safe_table();
        if ('' === $table) {
            return null;
        }

        $query = $this->wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated by get_safe_table().
            "SELECT bkp_slug FROM `{$table}` WHERE bkp_id = %d",
            $id
        );

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Query is prepared above and table name is validated.
        return $this->wpdb->get_row($query);
    }
    
    /**
     * formsmodel::getListBackups()
     */
    public function getListBackups($per_page = '', $segment = '')
    {
        $per_page = absint($per_page);
        $segment = absint($segment);
        $table   = $this->get_safe_table();

        if ('' === $table) {
            return array();
        }

        if ($per_page > 0) {
            $query = $this->wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated by get_safe_table().
                "SELECT * FROM `{$table}` ORDER BY created_date DESC LIMIT %d, %d",
                $segment,
                $per_page
            );
        } else {
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated by get_safe_table().
            $query = "SELECT * FROM `{$table}` ORDER BY created_date DESC";
        }

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Query is prepared above when needed and table name is validated.
        return $this->wpdb->get_results($query);
    }
    
    public function CountRecords()
    {
        $table = $this->get_safe_table();
        if ('' === $table) {
            return 0;
        }
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is validated by get_safe_table().
        $query = "SELECT COUNT(*) FROM `{$table}`";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Query uses validated table name above.
        $counted = (int) $this->wpdb->get_var($query);
        return ($counted > 0) ? $counted : 0;
    }
}
