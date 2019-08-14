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
if (class_exists('flmbkp_Filemanager_Controller_Back')) {
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
class flmbkp_Filemanager_Controller_Back extends Flmbkp_Base_Module {

    const VERSION = '1.2';

    private $wpdb = "";
 
    private $pagination = "";
    
    protected $modules;
    var $per_page = 10;
 

    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct() {
       
      //init fm
        add_action('wp_ajax_flmbkp_back_initfm', array(&$this, 'ajax_fm_connector'));
        
      //submit header options
        add_action('wp_ajax_flmbkp_header_options', array(&$this, 'ajax_header_options'));  
        
    }
    
    /**
     * receiving header options
     *
     * @mvc Controller
     */
     public function ajax_header_options() {
         
         check_ajax_referer( 'flmbkp_ajax_nonce', 'flmbkp_security' );
         $tmp_data = (isset($_POST['options']))?urldecode(Flmbkp_Form_Helper::sanitizeInput_html($_POST['options'])):'';
         
         
         $data = array();
        foreach(explode('&',$tmp_data) as $value)
        {
            $value1 = explode('=', $value);
            $data[$value1[0]] = Flmbkp_Form_Helper::sanitizeInput($value1[1]);
        }
        
        //language
         if(isset($data['flmbkp_header_language']) && strval($data['flmbkp_header_language'])!=''){
             update_option( 'flmbkp_opt_lang', $data['flmbkp_header_language'] );
         }
        
         //theme
         if(isset($data['flmbkp_header_theme']) && strval($data['flmbkp_header_theme'])!=''){
             update_option( 'flmbkp_opt_theme', $data['flmbkp_header_theme'] );
         }
        $json=array();
        $json['url']=admin_url( 'admin.php?page=flmbkp_file_manager');
        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
     }
    
    
    /**
     * index
     *
     * @mvc Controller
     */
     public function ajax_fm_connector() {
        
        check_ajax_referer( 'flmbkp_ajax_nonce', 'flmbkp_security' );
        
        //$_POST      = array_map( 'stripslashes_deep', $_POST );
        $_POST['content'] = (isset($_POST['content'])) ? stripslashes_deep($_POST['content']) : '';    
            
        // elFinder autoload
        require FLMBKP_DIR.'/libraries/elfinder/php/autoload.php';
        
        // Enable FTP connector netmount
        elFinder::$netDrivers['ftp'] = 'FTP';
        
        
        /**
        * Simple function to demonstrate how to control file access using "accessControl" callback.
        * This method will disable accessing files/folders starting from '.' (dot)
        *
        * @param  string    $attr    attribute name (read|write|locked|hidden)
        * @param  string    $path    absolute file path
        * @param  string    $data    value of volume option `accessControlData`
        * @param  object    $volume  elFinder volume driver object
        * @param  bool|null $isDir   path is directory (true: directory, false: file, null: unknown)
        * @param  string    $relpath file path relative to volume root directory started with directory separator
        * @return bool|null
        **/
       function access($attr, $path, $data, $volume, $isDir, $relpath) {
               $basename = basename($path);
               return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
                                && strlen($relpath) !== 1           // but with out volume root
                       ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
                       :  null;                                 // else elFinder decide it itself
       }
       
       // Documentation for connector options:
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        $opts = array(
                'debug' => true,
                'roots' => array(
                        // Items volume
                        array(
                                'driver'				 => 'LocalFileSystem',
                                                        'startPath'			 => ABSPATH ,
                                                        'path'					 => ABSPATH ,
                                                        'quarantine' => FLMBKP_DIR . '/temp/'.'.quarantine',
                                                        'URL'						 => site_url(),
                                                       
                                'mimeDetect'		 => 'internal',
						'tmbPath'				 => FLMBKP_DIR . '/temp/'.'.tmb',
                                                'tmbURL'				 => FLMBKP_URL. '/temp/'.'.tmb',
						'utf8fix'         => false,
						'tmbCrop'				 => false,
						'tmbBgColor'		 => 'transparent',                                               
                                'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                                'uploadDeny' => array(),
                                'uploadAllow' => array('image', 'text/plain'),
                                'uploadOrder' => array('deny', 'allow'),
                                'accessControl' => 'access',                     // disable and hide dot starting files (OPTIONAL)
                                'accessControl' => 'access',
                                'acceptedName' => 'validName',
                              
                        )
                        

                )
        );

        // run elFinder
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
       
        
        wp_die();
    }
    
    
    /**
     * index
     *
     * @mvc Controller
     */
    public function load_file_manager() {
   
        //codemirror 
        //wp_enqueue_style('fmbkp_codemirror', FLMBKP_URL . '/librariesold/codemirror/5.48.2/codemirror.css');
        wp_enqueue_style('fmbkp_codemirror_theme', FLMBKP_URL . '/libraries/codemirror/5.48.2/theme/monokai.css');
      
        //codemirror
        //wp_enqueue_script('fmbkp_codemirror', FLMBKP_URL . '/librariesold/codemirror/5.48.2/codemirror.js', array(), '1.0');

        wp_enqueue_script('fmbkp_require', FLMBKP_URL . '/assets/common/js/require/2.1.22/require.js', array('jquery'));
        
        wp_enqueue_script('fmbkp_fm_init', FLMBKP_URL . '/assets/backend/js/fm_init.js', array('jquery','fmbkp_require'));
        
        wp_register_script('fmbkp_maindefault', FLMBKP_URL . '/libraries/elfinder/main.default.js', array('fmbkp_fm_init'));
        
        $tmp_theme=get_option( 'flmbkp_opt_theme', 'default' );
        $tmp_theme_path='';
        switch(strval($tmp_theme)){
            case 'gray':
                $tmp_theme_path='/libraries/elfinder/themes/css/theme-gray.css';
                break;
            case 'light':
                $tmp_theme_path='/libraries/elfinder/themes/css/theme-light.css';
                break;
            case 'dark':
                $tmp_theme_path='/libraries/elfinder/themes/css/theme.css';
                break;
            default:
                $tmp_theme_path='/libraries/elfinder/css/theme.css';
                break;
        }
        
          //load form variables
            $form_variables=array();
            $form_variables['url_site']=site_url();
            $form_variables['ajax_nonce']=wp_create_nonce('flmbkp_ajax_nonce');
            $form_variables['ajaxurl']=site_url('wp-admin/admin-ajax.php');
            $form_variables['opt_lang']=get_option( 'flmbkp_opt_lang', 'en' );
            $form_variables['opt_theme']=$tmp_theme_path;
            $form_variables['plugin_url']=FLMBKP_URL;
            wp_localize_script('fmbkp_fm_init', 'flmbkp_vars', $form_variables);
        
        wp_enqueue_script('fmbkp_maindefault'); 
        
        
        
        
        $data=array();
        $data['opt_theme']= get_option( 'flmbkp_opt_theme', 'default' );
        $data['opt_lang']= get_option( 'flmbkp_opt_lang', 'en' );
        echo self::loadPartial('layout.php', 'filemanager/views/backend/load_file_manager.php', $data);
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
