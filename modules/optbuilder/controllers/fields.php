<?php
/**
 * Frontend
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   sfdc_theme
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://wordpress-cost-estimator.zigaform.com
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('Zgpb_Optb_Controller_Fields')) {
    return;
}

/**
 * Controller Frontend class
 *
 * @category  PHP
 * @package   sfdc_theme
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      http://wordpress-cost-estimator.zigaform.com
 */
class Zgpb_Optb_Controller_Fields extends Flmbkp_Base_Module {
    
    private $wpdb = "";
    
    protected $modules;
    
    private $theme_options = array();
    
    /**
     * Constructor
     *
     * @mvc Controller
     */
    protected function __construct() {
 
        global $wpdb;
        $this->wpdb = $wpdb;
            
            
    }
    
    /**
     * Parser html textbox
     *
     * @mvc Controller
     */
    public function parsehtml_textbox($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_textbox.php', $data, 'always');
    }
    
    /**
     * Parser html textarea
     *
     * @mvc Controller
     */
    public function parsehtml_textarea($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_textarea.php', $data, 'always');
    }
    
    /**
     * Parser html select
     *
     * @mvc Controller
     */
    public function parsehtml_select($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_select.php', $data, 'always');
    }
    
    /**
     * Parser html select
     *
     * @mvc Controller
     */
    public function parsehtml_radiobutton($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_radiobutton.php', $data, 'always');
    }
    
    /**
     * Parser html boolean
     *
     * @mvc Controller
     */
    public function parsehtml_boolean($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_boolean.php', $data, 'always');
    }
    
    /**
     * Parser html image
     *
     * @mvc Controller
     */
    public function parsehtml_image($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_image.php', $data, 'always');
    }
    
    /**
     * Parser html numeric
     *
     * @mvc Controller
     */
    public function parsehtml_numeric($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_numeric.php', $data, 'always');
    }
    
    /**
     * Parser html multiselect
     *
     * @mvc Controller
     */
    public function parsehtml_multiselect($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_multiselect.php', $data, 'always');
    }
    
    /**
     * Parser html button
     *
     * @mvc Controller
     */
    public function parsehtml_button($options) {
        $data = array();
        $data = array_merge($data, $options);
        return self::render_template('optbuilder/views/fields/parsehtml_button.php', $data, 'always');
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