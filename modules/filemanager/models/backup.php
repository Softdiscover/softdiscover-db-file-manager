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
 * @link      https://wordpress-form-builder.zigaform.com/
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
 * @link      https://wordpress-form-builder.zigaform.com/
 */
class flmbkp_Model_Backup {

    private $wpdb = "";
    public $table = "";

    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . "flmbkp_backup";
    }
    
    
     function getinfo($id) {
        $query = sprintf('
            select bkp_slug
            from %s c
            where c.bkp_id=%s
            ', $this->table,$id);
        return $this->wpdb->get_row($query);
    }
    
    /**
     * formsmodel::getListBackups()
     */
    function getListBackups($per_page = '', $segment = '') {
        $query = sprintf('
            select *
            from %s uf
            ORDER BY uf.created_date desc
            ', $this->table);

        if ($per_page != '' || $segment != '') {
            $segment=(!empty($segment))?$segment:0;
        $query.=sprintf(' limit %s,%s', (int)$segment, (int)$per_page);
        }
        return $this->wpdb->get_results($query);
    }
    
    function CountRecords() {
        $query = sprintf('
            select COUNT(*) AS counted
            from %s c
            ORDER BY c.created_date desc
            ', $this->table);
        $row = $this->wpdb->get_row($query);
        if (isset($row->counted)) {
            return $row->counted;
        } else {
            return 0;
        }
    }

}

?>
