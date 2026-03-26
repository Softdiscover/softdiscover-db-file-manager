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
 * @link      https://softdiscover.com/
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
?>

<div class="sfdc-block1-container" >
    <div class="space20"></div>
    <div class="sfdc-row">
        <div class="col-lg-12">
            <div class="widget widget-padding span12">
                <div class="widget-header">
                    <i class="fa fa-list-alt"></i>
                    <h5>
                        <?php esc_html_e('Database manager.', 'FRocket_admin'); ?>
                    </h5>

                </div>  
                <div class="widget-body">  
                    
                
                    <h2><?php esc_html_e('Tables Information', 'FRocket_admin'); ?></h2>
                    <table class="table table-hover table-striped">

                        <thead class="mdb-color darken-3">
                            <tr class="text-white">
                                <th>#</th>
                                <th><?php esc_html_e('Tables', 'FRocket_admin'); ?></th>
                                <th><?php esc_html_e('Records', 'FRocket_admin'); ?></th>
                                <th><?php esc_html_e('Data Usage', 'FRocket_admin'); ?></th>
                                <th><?php esc_html_e('Index Usage', 'FRocket_admin'); ?></th>
                                <th><?php esc_html_e('Overhead', 'FRocket_admin'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $key => $value) {?>
                                <tr>
                                    <th scope="row"><?php echo esc_html($value['number']); ?></th>
                                    <td><?php echo esc_html($value['table']); ?></td>
                                    <td><?php echo esc_html($value['Records']); ?></td>
                                    <td><?php echo esc_html($value['datausage']); ?></td>
                                    <td><?php echo esc_html($value['indexusage']); ?></td>
                                    <td><?php echo esc_html($value['overhead']); ?></td>
                                </tr>
                            <?php } ?>
                         
                        </tbody>

                    </table>
                    <br>
                    <hr>
                    <br>
                         <h2><?php esc_html_e('Database Information', 'FRocket_admin'); ?></h2>
                    <table class="table table-hover table-striped">

                        <thead class="mdb-color darken-3">
                            <tr class="text-white">
                                <th><?php esc_html_e('Setting', 'FRocket_admin'); ?></th>
                                <th><?php esc_html_e('Value', 'FRocket_admin'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables2 as $key => $value) {?>
                                <tr>
                                    <td><?php echo esc_html($value['option']); ?></td>
                                    <td><?php echo esc_html($value['value']); ?></td>
                                  
                                </tr>
                            <?php } ?>
                         
                        </tbody>
                </div> 
            </div> 
        </div>
    </div>
</div>
