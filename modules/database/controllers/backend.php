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
if (class_exists('flmbkp_database_Controller_Back')) {
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
 * @link      https://softdiscover.com
 */
class flmbkp_database_Controller_Back extends Flmbkp_Base_Module
{

    const VERSION = '1.2';

    private $wpdb = "";
 
    private $pagination = "";
    
    protected $modules;
    private $per_page = 10;
 

    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /*
     * list tables
     */
    public function list_tables()
    {
        $data=array();
        
        $tables=array();
        $no = 0;
        $row_usage = 0;
        $data_usage = 0;
        $index_usage = 0;
        $overhead_usage = 0;
        $tablesstatus = $this->wpdb->get_results("SHOW TABLE STATUS");
        foreach ($tablesstatus as $tablestatus) {
             $table_inner = array();
              $no++;
               $table_inner['number']=number_format_i18n($no);
               $table_inner['table']=$tablestatus->Name;
               $table_inner['Records']=number_format_i18n($tablestatus->Rows);
               $table_inner['datausage']=Flmbkp_Form_Helper::format_size($tablestatus->Data_length);
               $table_inner['indexusage']=Flmbkp_Form_Helper::format_size($tablestatus->Index_length);
               $table_inner['overhead']=Flmbkp_Form_Helper::format_size($tablestatus->Data_free);
                
                $row_usage += $tablestatus->Rows;
                $data_usage += $tablestatus->Data_length;
                $index_usage +=  $tablestatus->Index_length;
                $overhead_usage += $tablestatus->Data_free;
               $tables[]=$table_inner;
        }
        $table_inner = array();
        $table_inner['number']=__('Total', 'FRocket_admin');
               $table_inner['table']=sprintf(_n('%s Table', '%s Tables', $no, 'FRocket_admin'), number_format_i18n($no));
               $table_inner['Records']=sprintf(_n('%s Record', '%s Records', $row_usage, 'FRocket_admin'), number_format_i18n($row_usage));
               $table_inner['datausage']=Flmbkp_Form_Helper::format_size($data_usage);
               $table_inner['indexusage']=Flmbkp_Form_Helper::format_size($index_usage);
               $table_inner['overhead']=Flmbkp_Form_Helper::format_size($overhead_usage);
        $tables[]=$table_inner;
        $data['tables']=$tables;
        
        $tables2=array();
        
        $sqlversion = $this->wpdb->get_var("SELECT VERSION() AS version");
        $tables2[]=array('option'=>__('Database Host', 'FRocket_admin'),'value'=>DB_HOST);
        $tables2[]=array('option'=>__('Database Name', 'FRocket_admin'),'value'=>DB_NAME);
        $tables2[]=array('option'=>__('Database User', 'FRocket_admin'),'value'=>DB_USER);
        $tables2[]=array('option'=>__('Database Type', 'FRocket_admin'),'value'=>'MYSQL');
        $tables2[]=array('option'=>__('Database Version', 'FRocket_admin'),'value'=>$sqlversion);
        $tables2[]=array('option'=>__('PHP Version', 'FRocket_admin'),'value'=>phpversion());
        $data['tables2']=$tables2;
        
        echo self::loadPartial('layout_blank.php', 'database/views/backend/list_tables.php', $data);
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
