<?php
/*
 * Plugin Name: File Manager, Code editor, backup by Managefy
 * Plugin URI: https://softdiscover.com/managefy/
 * Description: Managefy Plugin for wordpress, allow user to access folders, download files, upload files, create folders, sub folders. Also Managefy allows to backup your files and database, and restore them as well.
 * Version: 1.4.7
 * Author: SoftDiscover.Com
 * Author URI: https://github.com/Softdiscover
 */

if (!defined('ABSPATH')) {
    die('Access denied.');
}
if (!class_exists('WpFileManagerBkp')) {

    final class WpFileManagerBkp {

        /**
         * The only instance of the class
         *
         * @var RocketForm
         * @since 1.0
         */
        private static $instance;

        /**
         * The Plug-in version.
         *
         * @var string
         * @since 1.0
         */
        public $version = '1.4.7';

        /**
         * The minimal required version of WordPress for this plug-in to function correctly.
         *
         * @var string
         * @since 1.0
         */
        public $wp_version = '3.6';

        /**
         * The minimal required version of WordPress for this plug-in to function correctly.
         *
         * @var string
         * @since 1.0
         */
        public $php_version = '5.3';

        /**
         * Class name
         *
         * @var string
         * @since 1.0
         */
        public $class_name;

        /**
         * An array of defined constants names
         *
         * @var array
         * @since 1.0
         */
        public $defined_constants;

        /**
         * Create a new instance of the main class
         *
         * @since 1.0
         * @static
         * @return RocketForm
         */
        public static function instance() 
        {
            $class_name = __CLASS__;
            if (!isset(self::$instance) && !( self::$instance instanceof $class_name )) {
                self::$instance = new $class_name;
            }

            return self::$instance;
        }

        public function __construct() 
        {
            // Save the class name for later use
            $this->class_name = __CLASS__;
             //
            //  Plug-in requirements
            //
            if (!$this->check_requirements()) {
                add_action('admin_notices', array(&$this, 'flmbkp_requirements_error'));
                return;
            }
            
            //
            // Declare constants and load dependencies
            //
            $this->define_constants();
            $this->load_dependencies();
            $this->check_updateChanges();
            try {

                if (class_exists('Flmbkp_Bootstrap')) {
                    $GLOBALS['wprockf'] = Flmbkp_Bootstrap::get_instance();
                    register_activation_hook(__FILE__, array($GLOBALS['wprockf'], 'activate'));
                    register_deactivation_hook(__FILE__, array($GLOBALS['wprockf'], 'deactivate'));
                }
            } catch (exception $e) {
                $error = $e->getMessage() . "\n";
                echo $error;
            }
        }

       
        /**
        * check_requirements()
        * Checks that the WordPress setup meets the plugin requirements
        * 
        * @return boolean
        */
        private function check_requirements() {
            global $wp_version;
            if (!version_compare($wp_version, $this->wp_version, '>=')) {
                add_action('admin_notices', array(&$this, 'display_req_notice'));

                return false;
            }

            if (version_compare(PHP_VERSION, $this->php_version, '<')) {
                return false;
            }
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if (is_plugin_active( 'rocket-forms-express/rocket-forms-express.php' ) ) {
               return false;
            }
	

            return true;
        }

        public function flmbkp_requirements_error() {
            global $wp_version;
            require_once dirname(__FILE__) . '/views/requirements-error.php';
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            $this->define('FLMBKP_FILE', __FILE__);
            $this->define('FLMBKP_FOLDER', plugin_basename(dirname(__FILE__)));
            $this->define('FLMBKP_BASENAME', plugin_basename(__FILE__));
            $this->define('FLMBKP_ABSFILE', __FILE__);
            $this->define('FLMBKP_ADMINPATH', get_admin_url());
            $this->define('FLMBKP_APP_NAME', "Database & File Manager");
            $this->define('FLMBKP_VERSION', $this->version);
            $this->define('FLMBKP_DIR', dirname(__FILE__));
            $this->define('FLMBKP_URL', plugins_url() . '/'.FLMBKP_FOLDER);
            $this->define('FLMBKP_LIBS', FLMBKP_DIR . '/libraries');
            $this->define('FLMBKP_DEMO', 0);
            $this->define('FLMBKP_DEV', 0);
            
             
            $this->define('FLMBKP_F_LITE', 0);
            $this->define('FLMBKP_DEBUG', 0);
            if (FLMBKP_DEBUG == 1) {
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
            }
            
        }

        /**
         * Define constant if not already set
         * @param  string $name
         * @param  string|bool $value
         */
        private function define($name, $value) 
        {
            if (!defined($name)) {
                define($name, $value);
                $this->defined_constants[] = $name;
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        private function load_dependencies() {
            // Admin Panel
            if (is_admin()) {
                require_once FLMBKP_DIR . '/classes/uiform-base-module.php';
                require_once FLMBKP_DIR . '/classes/uiform-form-helper.php';
                require_once FLMBKP_DIR . '/classes/uiform-bootstrap.php';
            }
            
            // shortcode show version info
            add_action('wp_head', array( &$this, 'shortcode_show_version' ));
        }
        
        /**
         * Loads PHP files that required by the plug-in
         */
        private function check_updateChanges() {
            global $wpdb;
            $version=FLMBKP_VERSION;
            $install_ver = get_option("flmbkpbuild_version");
            
            update_option("flmbkpbuild_version", $version);
        }
        
        
        /**
         * shortcode show version.
         *
         * @author	Unknown
         * @since	v0.0.1
         * @version	v1.0.0	Saturday, January 27th, 2024.
         * @access	public
         * @return	void
         */
        public function shortcode_show_version()
        {
            $output  = '<noscript>';
            $output .= '<a href="https://softdiscover.com/?mngfy_v=' . FLMBKP_VERSION . '" title="WordPress File Manager" >Managefy </a> version ' . FLMBKP_VERSION;
            $output .= '</noscript>';
            echo $output;
        }

    }

}

function flmbkp_uninstall()
{
    
   require_once( FLMBKP_DIR . '/classes/uiform-installdb.php');
   $installdb = new Flmbkp_InstallDB();
   $installdb->uninstall();
   //removing options
    delete_option('flmbkpbuild_version' );
   return true;
}

function wpFMKP() {
    register_uninstall_hook(__FILE__, 'flmbkp_uninstall');
    return WpFileManagerBkp::instance();
}

wpFMKP();
?>
