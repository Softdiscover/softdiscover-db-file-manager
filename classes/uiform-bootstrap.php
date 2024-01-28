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
 * @link      https://www.softdiscover.com/
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('Flmbkp_Bootstrap')) {
    return;
}

class Flmbkp_Bootstrap extends Flmbkp_Base_Module
{

    protected $modules;
    protected $addons;
    protected $models;

    const VERSION = '1.2';
    const PREFIX = 'flmbkp_';

    /*
     * Magic methods
     */

    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct()
    {
        $this->register_hook_callbacks();
    }

    /**
     * Register callbacks for actions and filters
     *
     * @mvc Controller
     */
    public function register_hook_callbacks()
    {
        global $wp_version;



        add_action('admin_menu', array(&$this, 'loadMenu'));

        //add lang dir
        add_filter('rockfm_languages_directory', array(&$this, 'rockfm_lang_dir_filter'));
        add_filter('rockfm_languages_domain', array(&$this, 'rockfm_lang_domain_filter'));
        add_filter('plugin_locale', array(&$this, 'rockfm_lang_locale_filter'));
        
      
            
        //load admin
        if (is_admin() && Flmbkp_Form_Helper::is_flmbkp_page()) {
            //add class to body
             add_filter('body_class', array(&$this, 'filter_body_class'));
            
            //deregister bootstrap in child themes
            add_action('admin_enqueue_scripts', array(&$this, 'remove_unwanted_css'), 1000);
            //admin resources
            add_action('admin_enqueue_scripts', array(&$this, 'load_admin_resources'), 20, 1);
            
            $this->loadBackendControllers();
            //disabling wordpress update message
            add_action('admin_menu', array(&$this, 'wphidenag'));
            

            //end format wordpress editor
            add_action('init', array($this, 'init'));
        } else {
            //load frontend
            //$this->loadFrontendControllers();
        }

        //  i18n
        add_action('init', array(&$this, 'i18n'));

        //call post processing
        if (isset($_POST['_rockfm_type_submit']) && absint($_POST['_rockfm_type_submit']) === 0) {
            add_action('plugins_loaded', array(&$this, 'flmbkp_process_form'));
        }
        
          // register API endpoints
        add_action('init', array(&$this, 'add_endpoint'), 0);
        // handle  endpoint requests
        add_action('parse_request', array(&$this, 'handle_api_requests'), 0);
        add_action('uifm_fbuilder_api_paypal_ipn_handler', array(&$this, 'paypal_ipn_handler'));
        add_action('uifm_fbuilder_api_lmode_iframe_handler', array(&$this, 'lmode_iframe_handler'));
        add_action('uifm_fbuilder_api_pdf_show_record', array(&$this, 'action_pdf_show_record'));
        add_action('uifm_fbuilder_api_csv_show_allrecords', array(&$this, 'action_csv_show_allrecords'));
         
        //add_action( 'init',                  array( $this, 'upgrade' ), 11 );
        
        //disable update notifications
        if (is_admin()) {
            add_filter('site_transient_update_plugins', array(&$this, 'disable_plugin_updates'));
            
            //if(FLMBKP_F_LITE===1){
              add_filter((is_multisite() ? 'network_admin_' : '').'plugin_action_links', array($this, 'plugin_add_links'), 10, 2);
              
              // ZigaForm Upgrade
              add_action('admin_notices', array( $this, 'zigaform_upgrade' ));
            //}
        }
    }
    
    /**
    * Registers with the SDK
    *
    * @since    1.0.0
    */
    public function zigaform_register_sdk($products)
    {
           $products[] = FLMBKP_ABSFILE;
           return $products;
    }
    
    public function zigaform_upgrade()
    {
    }
    
    
    public function plugin_add_links($links, $file)
    {
    
        if (is_array($links) && (strpos($file, "db-file-manager.php") !== false)) {
            $settings_link = '<a href="'.admin_url('admin.php').'?page=flmbkp_file_manager">'.__("Settings", "FRocket_admin").'</a>';
            array_unshift($links, $settings_link);
            $settings_link = '<a style="color: #08AA17;font-weight:bold;" target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=checkout@softdiscover.com&lc=US&item_name=Donation+to+Managefy+wordpress+plugin&no_note=0&cn=&currency_code=USD&bn=PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">'.__("Donate", "FRocket_admin").'</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }
    
    /**
     * add class to body
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function filter_body_class($classes)
    {
            $classes[] = 'sfdc-wrap';
            return $classes;
    }
    
    
    /**
     * add_endpoint function.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function add_endpoint()
    {
        //assigning variable to rewrite
        add_rewrite_endpoint('uifm_fbuilder_api_handler', EP_ALL);
    }
    
    /**
     * API request - Trigger any API requests
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function handle_api_requests()
    {
        global $wp;
        if (isset($_GET['flmbkp_action']) && $_GET['flmbkp_action'] == 'uifm_fb_api_handler') {
            $wp->query_vars['uifm_fbuilder_api_handler'] = $_GET['flmbkp_action'];
        }

        // paypal-ipn-for-wordpress-api endpoint requests
        if (!empty($wp->query_vars['uifm_fbuilder_api_handler'])) {
            // Buffer, we won't want any output here
            ob_start();

            // Get API trigger
            $api = $this->route_api_handler();
            // Trigger actions
            do_action('uifm_fbuilder_api_' . $api);

            // Done, clear buffer and exit
            ob_end_clean();
            die('1');
        }
    }
    
    
    private function route_api_handler()
    {
      
        $mode=isset($_GET['uifm_mode']) ? Flmbkp_Form_Helper::sanitizeInput($_GET['uifm_mode']) :'';
        $return='';
        switch ($mode) {
            case 'lmode':
                $type_mode=isset($_GET['uifm_action']) ? Flmbkp_Form_Helper::sanitizeInput($_GET['uifm_action']) :'';
                switch ($type_mode) {
                    case 1:
                        $return='lmode_iframe_handler';
                        break;
                    default:
                        break;
                }
                break;
            case 'pdf':
                $process=isset($_GET['uifm_action']) ? Flmbkp_Form_Helper::sanitizeInput($_GET['uifm_action']) :'';
                switch ($process) {
                    case 'show_record':
                        $return='pdf_show_record';
                        break;
                    default:
                        break;
                };
                break;
            case 'csv':
                $process=isset($_GET['uifm_action']) ? Flmbkp_Form_Helper::sanitizeInput($_GET['uifm_action']) :'';
                switch ($process) {
                    case 'show_allrecords':
                        $return='csv_show_allrecords';
                        break;
                    default:
                        break;
                };
                break;
            default:
                break;
        }
        
        return $return;
    }
     
    public function action_pdf_show_record()
    {
         
        self::$_modules['formbuilder']['frontend']->pdf_show_record();
    }
    
    public function action_csv_show_allrecords()
    {
       
        $form_id=isset($_GET['id']) ? Flmbkp_Form_Helper::sanitizeInput($_GET['id']) :'';
       
        self::$_modules['formbuilder']['records']->csv_showAllForms($form_id);
       
        die();
    }
    
    
    public function lmode_iframe_handler()
    {
        $form_id=isset($_GET['id']) ? Flmbkp_Form_Helper::sanitizeInput($_GET['id']) :'';
        //removing actions
        remove_all_actions('wp_footer');
        remove_all_actions('wp_head');
        
        echo $this->modules['formbuilder']['frontend']->get_form_iframe($form_id);
        die();
    }
    
    public function disable_plugin_updates($value)
    {
        if (isset($value->response['uiform-form-builder/uiform-form-builder.php'])) {
            unset($value->response['uiform-form-builder/uiform-form-builder.php']);
        }
        return $value;
    }
    
    public function remove_unwanted_css()
    {
       /*
        //style
        wp_dequeue_style( 'bootstrap_css' );
        wp_deregister_style( 'bootstrap_css' );

        //script
        wp_dequeue_script( 'bootstrap.min_script' );*/
    }
     
                    
    public function rockfm_lang_dir_filter($lang_dir)
    {
        if (is_admin() && Flmbkp_Form_Helper::is_flmbkp_page()) {
            $lang_dir = FLMBKP_DIR . '/i18n/languages/backend/';
        } else {
        }
        return $lang_dir;
    }

    public function rockfm_lang_locale_filter($locale)
    {
                    
        return $locale;
    }

    public function rockfm_lang_domain_filter($domain)
    {
        if (is_admin() && Flmbkp_Form_Helper::is_flmbkp_page()) {
            $domain = 'FRocket_admin';
        } else {
            //load frontend
            $domain = 'FRocket_front';
        }
        return $domain;
    }

    public function flmbkp_process_form()
    {
        $this->modules['formbuilder']['frontend']->process_form();
    }
    
    
    public function my_external_plugins($plugin_array)
    {
         $plugin_array['fullpage'] = FLMBKP_URL.'/assets/backend/js/tinymce/plugins/fullpage/plugin-4.0.js';
        return $plugin_array;
    }
    
    public function wpver411_tiny_mce_before_init($initArray)
    {
       
        $initArray['plugins'] = 'tabfocus,paste,media,wpeditimage,wpgallery,wplink,wpdialogs,fullpage';
        $initArray['wpautop'] = true;
        
        $initArray['cleanup_on_startup'] = false;
        $initArray['trim_span_elements'] = false;
        $initArray['verify_html' ] = false;
        $initArray['fix_table_elements' ] = false;
        $initArray['cleanup'] = false;
        $initArray['convert_urls'] = false;
        
        $initArray["forced_root_block"] = false;
        $initArray["force_br_newlines"] = false;
        $initArray["force_p_newlines"] = false;
        $initArray["convert_newlines_to_brs"] = false;
        $initArray['apply_source_formatting'] = false;
        $initArray['theme_advanced_buttons1'] = 'formatselect,forecolor,|,bold,italic,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,|,wp_adv';
        $initArray['theme_advanced_buttons2'] = 'fontsizeselect,pastetext,pasteword,removeformat,|,charmap,|,outdent,indent,|,undo,redo';
        $initArray['theme_advanced_buttons3'] = '';
        $initArray['theme_advanced_buttons4'] = '';
        $initArray['fontsize_formats'] = "7px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 26px 27px 28px 29px 30px 31px 32px 34px 36px 45px";
        // html elements being stripped
        $initArray['extended_valid_elements'] = '*[*]';
        $initArray['valid_elements'] = '*[*]';
        $initArray['valid_children'] = "+head[style],+body[meta],+div[h2|span|meta|object],+object[param|embed]";
           // don't remove line breaks
        $initArray['remove_linebreaks'] = false;

        // convert newline characters to BR
        $initArray['convert_newlines_to_brs'] = true;

        // don't remove redundant BR
        $initArray['remove_redundant_brs'] = false;
        
        
        
        $initArray['setup'] = <<<JS
[function(ed) {
      ed.on('change KeyUp', function(e) {
         rocketform.captureEventTinyMCE(ed,e);
      });
    ed.on('BeforeSetContent', function (e) {
        
    });
}][0]
JS;
        return $initArray;
    }

    public function wpse24113_tiny_mce_before_init($initArray)
    {
        $initArray['plugins'] = 'tabfocus,paste,media,wpeditimage,wpgallery,wplink,wpdialogs';
        $initArray['wpautop'] = true;
        $initArray['verify_html' ] = false;
        $initArray["forced_root_block"] = false;
        $initArray["force_br_newlines"] = true;
        $initArray["force_p_newlines"] = false;
        $initArray["convert_newlines_to_brs"] = true;
        $initArray['apply_source_formatting'] = true;
        $initArray['theme_advanced_buttons1'] = 'formatselect,forecolor,|,bold,italic,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,|,wp_adv';
        $initArray['theme_advanced_buttons2'] = 'fontsizeselect,pastetext,pasteword,removeformat,|,charmap,|,outdent,indent,|,undo,redo';
        $initArray['theme_advanced_buttons3'] = '';
        $initArray['theme_advanced_buttons4'] = '';
        $initArray['fontsize_formats'] = "7px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 26px 27px 28px 29px 30px 31px 32px 34px 36px 45px";
        // html elements being stripped
        $initArray['extended_valid_elements'] = '*[*]';
        $initArray['valid_elements'] = '*[*]';
        $initArray['valid_children'] = "+head[style],+body[meta],+div[h2|span|meta|object],+object[param|embed]";
           // don't remove line breaks
        $initArray['remove_linebreaks'] = false;

        // convert newline characters to BR
        $initArray['convert_newlines_to_brs'] = true;

        // don't remove redundant BR
        $initArray['remove_redundant_brs'] = false;
        $initArray['setup'] = <<<JS
[function(ed) {
    ed.onKeyUp.add(function(ed, e) {
        rocketform.captureEventTinyMCE(ed,e);
    });
    ed.onClick.add(function(ed, e) {
        rocketform.captureEventTinyMCE(ed,e);
        });
    ed.onChange.add(function(ed, e) {
        rocketform.captureEventTinyMCE(ed,e);
    });
}][0]
JS;
        return $initArray;
    }
                    
    protected function loadBackendControllers()
    {
        
                    
         //default
        require_once(FLMBKP_DIR . '/modules/default/controllers/backend.php');
        
        //filemanager
        require_once(FLMBKP_DIR . '/modules/filemanager/controllers/backend.php');
        require_once(FLMBKP_DIR . '/modules/filemanager/controllers/backup.php');
        
        //database
        require_once(FLMBKP_DIR . '/modules/settings/controllers/backend.php');
        //settings
        require_once(FLMBKP_DIR . '/modules/database/controllers/backend.php');
        
        
        require_once(FLMBKP_DIR . '/modules/filemanager/models/backup.php');
                    
        $this->models = array(
            'filemanager' => array('backup' => new flmbkp_Model_Backup())
        );
        self::$_models = $this->models;
        
        $this->modules = array(
            'default' => array('backend' => flmbkp_Default_Controller_Back::get_instance()),
            'filemanager' => array('backend' => flmbkp_Filemanager_Controller_Back::get_instance(),
                   'backup' => flmbkp_Filemanager_Controller_Backup::get_instance()
                ),
            'database' => array('backend' => flmbkp_database_Controller_Back::get_instance()),
            'settings' => array('backend' => flmbkp_settings_Controller_Back::get_instance())
                    
        );
        self::$_modules = $this->modules;
    }

    protected function loadFrontendControllers()
    {
    }

    public function wphidenag()
    {
        remove_action('admin_notices', 'update_nag', 3);
    }
    
    
    /**
     *  Redirects the clicked menu item to the correct location
     *
     * @return null
     */
    public function get_menu()
    {
        $current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : 'flmbkp_file_manager';
                    
        switch ($current_page) {
            case 'flmbkp_file_manager':
                $this->route_page();
                break;
            case 'flmbkp_page_backups':
                $this->modules['filemanager']['backup']->list_backups();
                exit;
                    break;
            case 'flmbkp_page_database':
                $this->modules['database']['backend']->list_tables();
                exit;
                    break;
            case 'flmbkp_page_settings':
                $this->modules['settings']['backend']->list_options();
                exit;
                    break;
            case 'zigaform-builder-about':
                include(dirname(__DIR__) . '/views/help/about.php');
                break;
            case 'zigaform-builder-debug':
                include(dirname(__DIR__) . '/views/help/debug.php');
                break;
            case 'zigaform-builder-gopro':
                include(dirname(__DIR__) . '/views/help/gopro.php');
                break;
            default:
                break;
        }
    }
    
    /**
     *  Hooked into `admin_menu`
     *
     * @return null
     */
    public function loadMenu()
    {
        
        if (!Flmbkp_Form_Helper::check_User_Access()) {
            return;
        }
                
        add_menu_page('Managefy', 'Managefy', "edit_posts", "flmbkp_file_manager", array(&$this, "get_menu"), FLMBKP_URL . "/assets/backend/image/codemirror-icon.png");
                
        $perms = 'manage_options';
        add_submenu_page("flmbkp_file_manager", __('File Manager', 'FRocket_admin'), __('File Manager', 'FRocket_admin'), $perms, "flmbkp_file_manager", array(&$this, "get_menu"));
        add_submenu_page("flmbkp_file_manager", __('Backups', 'FRocket_admin'), __('Backups', 'FRocket_admin'), $perms, "flmbkp_page_backups", array(&$this, "get_menu"));
        add_submenu_page("flmbkp_file_manager", __('Database', 'FRocket_admin'), __('Database', 'FRocket_admin'), $perms, "flmbkp_page_database", array(&$this, "get_menu"));
        add_submenu_page("flmbkp_file_manager", __('Settings', 'FRocket_admin'), __('Settings', 'FRocket_admin'), $perms, "flmbkp_page_settings", array(&$this, "get_menu"));
        
        /*$page_help = add_submenu_page("flmbkp_file_manager", __('Help', 'FRocket_admin'), __('Help', 'FRocket_admin'), $perms, "zigaform-builder-help", array(&$this, "get_menu"));
        $page_about = add_submenu_page("flmbkp_file_manager", __('About', 'FRocket_admin'), __('About', 'FRocket_admin'), $perms, "zigaform-builder-about", array(&$this, "get_menu"));

        if (FLMBKP_DEBUG == 1) {
         $page_debug = add_submenu_page("flmbkp_file_manager", __('Debug', 'FRocket_admin'), __('Debug', 'FRocket_admin'), $perms, "zigaform-builder-debug", array(&$this, "get_menu"));
         add_action('admin_print_styles-' . $page_debug, array(&$this, "load_admin_resources"));
        }


        //load styles
        add_action('admin_print_styles-' . $page_help, array(&$this, "load_admin_resources"));
        add_action('admin_print_styles-' . $page_about, array(&$this, "load_admin_resources"));*/
                
        add_filter("plugin_row_meta", array(&$this, 'get_extra_meta_links'), 10, 4);
        add_action('admin_head', array($this, 'add_star_styles'));
    }
    
    
    /**
     * Adds extra links to the plugin activation page
     *
     * @param  array  $meta   Extra meta links
     * @param  string $file   Specific file to compare against the base plugin
     * @param  string $data   Data for the meat links
     * @param  string $status Staus of the meta links
     * @return array          Return the meta links array
     */
    public function get_extra_meta_links($meta, $file, $data, $status)
    {
                
          $pos_coincidencia = strpos($file, 'db-file-manager.php');
        if ($pos_coincidencia !== false) {
             $plugin_page = admin_url('admin.php?page=flmbkp_file_manager');
             $meta[] = "<a href='https://www.softdiscover.com/#contact' target='_blank'><span class='dashicons  dashicons-admin-users'></span>" . __('Contact Us', 'FRocket_admin') . "</a>";
            
               
             $meta[] = "<a href='https://github.com/Softdiscover/softdiscover-db-file-manager/issues' target='_blank'>" . __('Support', 'FRocket_admin') . "</a>";
              
           // $meta[] = "<a href='https://kb.softdiscover.com/docs/zigaform-wordpress-form-builder/' target='_blank'><span class='dashicons  dashicons-search'></span>" . __('Documentation', 'FRocket_admin') . "</a>";
            
            $meta[] = "<a href='https://wordpress.org/support/plugin/softdiscover-db-file-manager/reviews#new-post' target='_blank' title='" . __('Leave a review', 'FRocket_admin') . "'><i class='ml-stars'><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg></i></a>";
        }
                
        return $meta;
    }
    
    /**
    * Adds styles to admin head to allow for stars animation and coloring
    */
    public function add_star_styles()
    {
        if (Flmbkp_Form_Helper::zigaform_user_is_on_admin_page('plugins.php')) {?>
            <style>
                .ml-stars{display:inline-block;color:#ffb900;position:relative;top:3px}
                .ml-stars svg{fill:#ffb900}
                .ml-stars svg:hover{fill:#ffb900}
                .ml-stars svg:hover ~ svg{fill:none}
            </style>
        <?php }
    }

    public function route_page()
    {
           
        $route = Flmbkp_Form_Helper::getroute();
        if (!empty($route['module']) && !empty($route['controller']) && !empty($route['action'])) {
            if (method_exists($this->modules[$route['module']][$route['controller']], $route['action'])) {
               // $this->modules[$route['module']][$route['controller']]->$route['action']();
                //this call function work in php7 too
                call_user_func(array($this->modules[$route['module']][$route['controller']],$route['action']));
            } else {
                echo 'wrong url';
            }
        } else {
            $this->modules['default']['backend']->main();
        }
    }
            
    
    /*
     * Static methods
     */

    /**
     * Enqueues CSS, JavaScript, etc
     *
     * @mvc Controller
     */
    public static function load_admin_resources()
    {
        //admin
        global $wp_scripts;
            $jquery_ui_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.12.1';
            /* load css */
            //loas ui
        switch ($jquery_ui_version) {
            case "1.11.4":
                wp_register_style('jquery-ui-style', FLMBKP_URL . '/assets/common/css/jqueryui/1.11.4/themes/start/jquery-ui.min.css', array(), $jquery_ui_version);
                wp_enqueue_style('jquery-ui-style');
                break;
            case "1.10.4":
                wp_register_style('jquery-ui-style', FLMBKP_URL . '/assets/common/css/jqueryui/1.10.4/themes/start/jquery-ui.min.css', array(), $jquery_ui_version);
                wp_enqueue_style('jquery-ui-style');
                break;
            case "1.12.1":
                wp_register_style('jquery-ui-style', FLMBKP_URL . '/assets/common/css/jqueryui/1.12.1/themes/smoothness/jquery-ui.css', array(), $jquery_ui_version);
                wp_enqueue_style('jquery-ui-style');
                break;
            default:
                wp_enqueue_style('jquery-ui');
                wp_enqueue_style('wp-jquery-ui-dialog');
        }
                
            wp_register_style(self::PREFIX . 'admin', FLMBKP_URL . '/assets/backend/css/admin.css?v'.date('YmdHis'), array(), FLMBKP_VERSION, 'all');
                    
            wp_enqueue_style('rockefform-bootstrap', FLMBKP_URL . '/assets/common/bootstrap/4.3.1/css/bootstrap.css');
            wp_enqueue_style('flmbkp-style', FLMBKP_URL . '/assets/backend/css/style.css');
                    
            wp_enqueue_style('rockefform-fontawesome', FLMBKP_URL . '/assets/common/css/fontawesome/4.7.0/css/font-awesome.min.css');
            
            //custom fonts
            wp_enqueue_style('rockefform-customfonts', FLMBKP_URL . '/assets/backend/css/custom/style.css');
            //animate
            wp_enqueue_style('rockefform-animate', FLMBKP_URL . '/assets/backend/css/animate.css');
                    
            //load rocketform
            wp_enqueue_style(self::PREFIX . 'admin');

            /* load js */
            //load jquery
            wp_enqueue_script('jquery');
            // load jquery ui
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-mouse');
            wp_enqueue_script("jquery-ui-dialog");
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-position');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script('jquery-ui-menu');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_script('jquery-ui-spinner');
            wp_enqueue_script('jquery-ui-button');
                    
            //prev jquery
            wp_enqueue_script('rockefform-prev-jquery', FLMBKP_URL . '/assets/common/js/init.js', array('jquery'));
            
            //bootstrap
            wp_enqueue_script('rockefform-bootstrap', FLMBKP_URL . '/assets/common/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery','rockefform-prev-jquery'));
            
            //md5
            wp_enqueue_script('rockefform-md5', FLMBKP_URL . '/assets/backend/js/md5.js');
                    
            //retina
            wp_enqueue_script('rockefform-retina', FLMBKP_URL . '/assets/backend/js/retina.js');
                    
            //bootbox
            wp_enqueue_script('rockefform-bootbox', FLMBKP_URL . '/assets/backend/js/bootbox/bootbox.js');
                
            wp_enqueue_script(self::PREFIX . 'init', FLMBKP_URL . '/assets/backend/js/init.js');
            wp_enqueue_script(self::PREFIX . 'global-mod-backup', FLMBKP_URL . '/assets/backend/js/global-mod-backup.js', array('jquery-ui-dialog'));
            wp_register_script(self::PREFIX . 'admin', FLMBKP_URL . '/assets/backend/js/admin.js?v'.date('YmdHis'), array('jquery', 'rockefform-bootstrap'));
                
            //load rocket form
           
            $flmbkp_vars = apply_filters('flmbkp_back_filter_globalvars', array('url_site' => site_url(),
                'url_admin' => admin_url(),
                'url_plugin' => FLMBKP_URL,
                'app_version' => FLMBKP_VERSION,
                'app_is_lite' => FLMBKP_F_LITE,
                'app_demo_st' => FLMBKP_DEMO,
                'url_assets' => FLMBKP_URL . "/assets",
                'ajax_nonce' => wp_create_nonce('flmbkp_ajax_nonce')));
            
            
            wp_localize_script(self::PREFIX . 'admin', 'flmbkp_vars', $flmbkp_vars);
            wp_enqueue_script(self::PREFIX . 'admin');
            
            
            //load form variables
            $form_variables=array();
            $form_variables['ajaxurl']='';
            $form_variables['siteurl']=FLMBKP_URL;
            
            $form_variables['imagesurl']=FLMBKP_URL . "/assets/frontend/images";

            wp_localize_script('rockefform-prev-jquery', 'flmbkp2_vars', $form_variables);
    }

    /**
     * Internationalization.
     * Loads the plugin language files
     *
     * @access public
     * @return void
     */
    public function i18n()
    {

        // Set filter for plugin's languages directory
        $lang_dir = FLMBKP_DIR . '/i18n/languages/';
        $lang_dir = apply_filters('rockfm_languages_directory', $lang_dir);

        $lang_domain = 'FRocket_admin';
        $lang_domain = apply_filters('rockfm_languages_domain', $lang_domain);

        // Traditional WordPress plugin locale filter
        $locale = apply_filters('plugin_locale', get_locale(), 'flmbkp_file_manager');
        $mofile = sprintf('%1$s-%2$s.mo', 'wprockf', $locale);

        // Setup paths to current locale file
        $mofile_local = $lang_dir . $mofile;
 
        if (file_exists($mofile_local)) {
            // Look in local /wp-content/plugins/wpbp/languages/ folder
            load_textdomain($lang_domain, $mofile_local);
        } else {
            // Load the default language files - but this is not working for some reason
            load_plugin_textdomain($lang_domain, false, dirname(plugin_basename(__FILE__)) . '/i18n/languages/');
        }
    }

    /**
     * Initializes variables
     *
     * @mvc Controller
     */
    public function init()
    {
        try {
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
    public function activate($network_wide = false)
    {
        require_once(FLMBKP_DIR . '/classes/uiform-installdb.php');
        $installdb = new Flmbkp_InstallDB();
        $installdb->install($network_wide);
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

?>
