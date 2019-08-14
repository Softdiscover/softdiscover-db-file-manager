<?php

/**
 * Backend
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Zigapage_wp
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://landera.softdiscover.com
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('Zgpb_optb_Controller_Backend')) {
    return;
}

/**
 * Controller Settings class
 *
 * @category  PHP
 * @package   Zigapage_wp
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      https://landera.softdiscover.com
 */
class Zgpb_Optb_Controller_Backend extends Uiform_Base_Module {

    const VERSION = '0.1';

    private $wpdb = "";
    protected $modules;
    private $model_settings = "";

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
     * render_options_one()
     * generate options  
     * 
     * @return json
     */
    
    public function render_options_one($data) {
        
        if(empty($data)){
            return;
        }
        
        //process ordering way
        $tmp_var1=array();
        $tmp_var2=array();
        
        
        $tmp_opts=$data['options'];
        $tmp_fields=array();
        
        //creating sections
        foreach ($data['sections'] as  $key=>$value) {
                $tmp_var1['section'.$key]['title'] = $value;
        }
        
        $tmp_groups_title=$data['groups'];
        
        foreach ($tmp_opts as  $value) {
            //check section
            switch (intval($value['sec_order'])) {
                case 1:
                    //section1
                    $tmp_var2['section1']['opts'][] = $value;
                    break;
                case 2:
                    //section2
                    $tmp_var2['section2']['opts'][] = $value;
                    break;
                default:
                    break;
            }
        }
        
        
        //creating groups
        foreach ($tmp_var2 as $key => $value) {
            //sections
             $tmp_groups=array();
            foreach ($value['opts'] as $key2 => $value2) {
                $tmp_groups[$value2['group_order']]['opts'][] = $value2;
                //temp - multiple same writting
                $tmp_groups[$value2['group_order']]['title'] = $tmp_groups_title[$value2['group_order']];
            }
                
             $tmp_var1[$key]['opts'] = $tmp_groups;    
        }
        
        //parse all options
        
        $output=array();
        $html_delimiter='<div class="space10 zgth-opt-divider-stl1"></div>';
        
        foreach ($tmp_var1 as $key => $value) {
            //starting the section content
            $tmp_output='';
            
            if (!empty($value['opts'])) 
            foreach ($value['opts'] as $key2 => $value2) {
                //creating separator of group
                $tmp_output.='<div class="zgth-sect-opt-divider"><h3>'.$value2['title'].'</h3></div>';
                
                $tmp_inner_opt=array();
                foreach ($value2['opts'] as $key3 => $value3) {
                   $tmp_inner_opt[] = $this->parse_field_html_byType($value3['type'],$value3);
                   
                }
                
                //adding delimiter between options
                $tmp_output.= implode($html_delimiter, $tmp_inner_opt);
                
            }
            
            $output[$key] = $tmp_output;
        }
        
        
        //buttons
        $tmp_render=array();
        if(!empty($data['buttons'])){
            foreach ($data['buttons'] as $key => $value) {
               $tmp_render[] = $this->parse_field_html_byType($value['type'],$value);
            }
        }
        
        
        $output['buttons'] = implode('', $tmp_render);
        
        
      
        
        return $output;
        
        
    }
    
    /**
     * Parse option html
     *
     * @mvc Controller
     */
    public function parse_field_html_byType($type,$options){
        $str_output='';
        switch ((string)$type) {
            case 'textbox':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_textbox($options);
                break;
            case 'textarea':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_textarea($options);
                break;
            case 'select':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_select($options);
                break;
            case 'boolean':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_boolean($options);
                break;
            case 'image':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_image($options);
                break;
            case 'numeric':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_numeric($options);
                break;
            case 'multiselect':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_multiselect($options);
                break;
            case 'button':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_button($options);
                break;
            case 'radiobutton':
                $str_output.=self::$_modules['optbuilder']['fields']->parsehtml_radiobutton($options);
                break;
            default:
                break;
        }
        
        return $str_output;
        
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