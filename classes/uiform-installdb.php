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
            wp_die(esc_html__('The plugin can not be network activated. You need to activate the plugin per site.', 'FRocket_admin'));
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
        $backup_table = preg_replace('/[^A-Za-z0-9_]/', '', (string) $this->backup);
        if ('' === $backup_table) {
            return;
        }

        $sql = "CREATE  TABLE IF NOT EXISTS `{$backup_table}` (
            `bkp_id` INT(10) NOT NULL AUTO_INCREMENT ,
            `bkp_slug` longtext NULL ,
            `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            `created_ip` VARCHAR(100) NULL ,
            `created_by` VARCHAR(100) NULL ,
            PRIMARY KEY (`bkp_id`) ) " . $charset . ";";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    
        //ajax mode by default
        update_option('flmbkpbuild_version', 1);
        update_option('flmbkp_opt_theme', 'gray');
    }
    
    public function uninstall()
    {
        global $wpdb;
        $backup_table = preg_replace('/[^A-Za-z0-9_]/', '', (string) $this->backup);
        $backup_table = esc_sql($backup_table);
        if ('' === $backup_table) {
            return;
        }
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is validated above and this is uninstall schema cleanup.
        $wpdb->query("DROP TABLE IF EXISTS `{$backup_table}`");
    }
}
