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
 * @link      https://softdiscover.com/
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('Flmbkp_InstallDB')) {
    return;
}

class Flmbkp_InstallDB
{

    private $backup;

    public function __construct()
    {
        global $wpdb;
        $this->backup         = $wpdb->prefix . "flmbkp_backup";
    }
    
    public function install($networkwide = false)
    {
        if ( $networkwide) {
            deactivate_plugins(plugin_basename(UIFORM_ABSFILE));
            wp_die(__('The plugin can not be network activated. You need to activate the plugin per site.', 'FRocket_admin'));
        }
        global $wpdb;
        $charset = '';
        if ( $wpdb->has_cap('collation')) {
            if ( !empty($wpdb->charset)) {
                $charset = "DEFAULT CHARACTER SET $wpdb->charset";
            }
            if ( !empty($wpdb->collate)) {
                $charset .= " COLLATE $wpdb->collate";
            }
        }
        //forms
        $sql = "CREATE  TABLE IF NOT EXISTS $this->backup (
            `bkp_id` INT(10) NOT NULL AUTO_INCREMENT ,
            `bkp_slug` longtext NULL ,
            `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            `created_ip` VARCHAR(100) NULL ,
            `created_by` VARCHAR(100) NULL ,
            PRIMARY KEY (`bkp_id`) ) " . $charset . ";";
        $wpdb->query($sql);
    
        //ajax mode by default
        update_option('flmbkpbuild_version', 1);
        update_option('flmbkp_opt_theme', 'gray');
    }
    
    public function uninstall()
    {
        global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS '. $this->backup);
    }
}
