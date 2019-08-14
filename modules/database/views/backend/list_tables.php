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
?>

<div class="sfdc-block1-container" >
    <div class="space20"></div>
    <div class="sfdc-row">
        <div class="col-lg-12">
            <div class="widget widget-padding span12">
                <div class="widget-header">
                    <i class="fa fa-list-alt"></i>
                    <h5>
                        <?php echo __('Database manager.', 'FRocket_admin') ?>
                    </h5>

                </div>  
                <div class="widget-body">  
                    
                
                    <h2><?php echo __('Tables Information', 'FRocket_admin') ?></h2>
                    <table class="table table-hover table-striped">

                        <thead class="mdb-color darken-3">
                            <tr class="text-white">
                                <th>#</th>
                                <th><?php echo __('Tables', 'FRocket_admin'); ?></th>
                                <th><?php echo __('Records', 'FRocket_admin'); ?></th>
                                <th><?php echo __('Data Usage', 'FRocket_admin'); ?></th>
                                <th><?php echo __('Index Usage', 'FRocket_admin'); ?></th>
                                <th><?php echo __('Overhead', 'FRocket_admin'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $key => $value) {?>
                                <tr>
                                    <th scope="row"><?php echo $value['number'];?></th>
                                    <td><?php echo $value['table'];?></td>
                                    <td><?php echo $value['Records'];?></td>
                                    <td><?php echo $value['datausage'];?></td>
                                    <td><?php echo $value['indexusage'];?></td>
                                    <td><?php echo $value['overhead'];?></td>
                                </tr>
                            <?php } ?>
                         
                        </tbody>

                    </table>
                    <br>
                    <hr>
                    <br>
                         <h2><?php echo __('Database Information', 'FRocket_admin') ?></h2>
                    <table class="table table-hover table-striped">

                        <thead class="mdb-color darken-3">
                            <tr class="text-white">
                                <th><?php echo __('Setting', 'FRocket_admin'); ?></th>
                                <th><?php echo __('Value', 'FRocket_admin'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables2 as $key => $value) {?>
                                <tr>
                                    <td><?php echo $value['option'];?></td>
                                    <td><?php echo $value['value'];?></td>
                                  
                                </tr>
                            <?php } ?>
                         
                        </tbody>
                </div> 
            </div> 
        </div>
    </div>
</div>
