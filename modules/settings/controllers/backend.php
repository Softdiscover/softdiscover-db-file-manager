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
if (class_exists('flmbkp_settings_Controller_Back')) {
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
class flmbkp_settings_Controller_Back extends Flmbkp_Base_Module {

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
        //save record
        add_action('wp_ajax_flmbkp_settings_saveoptions', array(&$this, 'ajax_save_options'));
    }

    /**
     * save options
     *
     * @mvc Controller
     */
    public function ajax_save_options() {
        check_ajax_referer('flmbkp_ajax_nonce', 'flmbkp_security');
        $tmp_data = (isset($_POST['options'])) ? urldecode(Flmbkp_Form_Helper::sanitizeInput_html($_POST['options'])) : '';
        $data = array();
        if(!empty($tmp_data))
        foreach (explode('&', $tmp_data) as $value) {
            $value1 = explode('=', $value);
            if(!empty($value1[1]))
            $data[] = Flmbkp_Form_Helper::sanitizeInput($value1[1]);
        }

        update_site_option('dbflm_fmanager_roles', $data);

        $json = array(
            'error' => false,
            'success' => true,
            'msg' => $data
        );

        header('Content-Type: application/json');
        echo json_encode($json);
        wp_die();
    }

    /*
     * list tables
     */

    public function list_options() {
        $data = array();
        $roles = Flmbkp_Form_Helper::get_user_roles();
        $wp_default_role = get_option('default_role');

        $saved_roles = get_option('dbflm_fmanager_roles', array());

        $temp_roles = array();

        foreach ($roles['other_roles'] as $key => $value) {
            $temp_role_inner = array();
            $temp_role_inner['role'] = $value;

            $ischecked = false;
            if (in_array($value, $saved_roles) || $roles['primary_role'] == $value) {
                $ischecked = true;
            }

            $temp_role_inner['ischecked'] = $ischecked;
            $temp_role_inner['primaryrole'] = ($roles['primary_role'] == $value) ? true : false;
            $temp_roles[] = $temp_role_inner;
        }
        $data['roles'] = $temp_roles;

        //$data['role']
        echo self::loadPartial('layout_blank.php', 'settings/views/backend/list_options.php', $data);
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