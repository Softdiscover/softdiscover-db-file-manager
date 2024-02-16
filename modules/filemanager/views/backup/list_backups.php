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
                        <?php echo __('Backup manager.', 'FRocket_admin') ?>
                    </h5>

                </div>  
                <div class="widget-body">

                    <div class="flmbkp-bkpoptions-wrap">
                        <form class="rockfm-form" 
                              action="" 
                              name="" 
                              method="post" 
                              enctype="multipart/form-data" 
                              id="flmbkp_backup_form">
                            <div class="alert alert-info" role="alert">
                                <h2><?php echo __('Backup options', 'FRocket_admin'); ?></h2>
                                <div class="">
                                    <div class="row">
                                      
                                        <div class="col-sm-4">
                                            <fieldset class="col-md-12">        
                                                <legend><?php echo __('Files', 'FRocket_admin'); ?></legend>

                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <p>
                                                            <label class = "checkbox-inline">
                                                                <?php echo __('Include your files in the backup', 'FRocket_admin'); ?>
                                                            </label>
                                                        <div class="alert alert-secondary" role="alert">
                                                            <div class="form-check">
                                                                <input name="flpbkp_opt_plugins" class="" type="checkbox" value="plugins" id="defaultCheck1" checked  >
                                                                <label class="form-check-label" for="defaultCheck1">
                                                                    <?php echo __('Plugins', 'FRocket_admin'); ?>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input name="flpbkp_opt_themes" class="" type="checkbox" value="themes" id="defaultCheck2" checked >
                                                                <label class="form-check-label" for="defaultCheck2">
                                                                    <?php echo __('Themes', 'FRocket_admin'); ?>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input name="flpbkp_opt_uploads" class="" type="checkbox" value="uploads" id="defaultCheck3" checked >
                                                                <label class="form-check-label" for="defaultCheck2">
                                                                    <?php echo __('Uploads', 'FRocket_admin'); ?>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input name="flpbkp_opt_others" class="" type="checkbox" value="others" id="defaultCheck4" checked >
                                                                <label class="form-check-label" for="defaultCheck2">
                                                                    <?php echo __('Any other directories found inside wp-content', 'FRocket_admin'); ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        </p>
                                                    </div>
                                                </div>

                                            </fieldset>
                                        </div>
                                          <div class="col-sm-4">

                                            <fieldset class="col-md-12">        
                                                <legend><?php echo __('Database', 'FRocket_admin'); ?></legend>

                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <p>
                                                            <label class = "checkbox-inline">
                                                                <input name="flpbkp_opt_database" type = "checkbox" id = "databaseCheckbox1" value = "database" checked > <?php echo __('Include your database in the backup', 'FRocket_admin'); ?>
                                                            </label>
                                                        <div class="alert alert-secondary" role="alert">
                                                            <?php echo __('All WordPress tables will be backed up.', 'FRocket_admin'); ?>
                                                        </div>
                                                        </p>
                                                    </div>
                                                </div>

                                            </fieldset>


                                        </div>
                                        <div class="col-sm-4">
                                            <button type="button" class="btn btn-primary btn-lg btn-block text-monospace"><?php echo __('Backup Now', 'FRocket_admin'); ?></button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form> 
                    </div>

                    <div id="flmbkp_progress_graph" class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div><strong><?php echo __('Backuping Now', 'FRocket_admin'); ?></strong> <?php echo __('Wait until backup is finished', 'FRocket_admin'); ?> <i class="fa fa-spin fa-8x fa-spinner" id="loading-icon"></i></div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div id="flmbkp_progress_plugins" class="mb-3" style="display:none;">
                            <div class="badge badge-primary text-wrap" style="width: 9rem;">
                                <?php echo __('Plugins', 'FRocket_admin'); ?>
                            </div>
                            <div id="flmbkp_plugins_progress" class="progress">
                                <div id="flmbkp_plugins_progress_msg" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 1%">0%</div>
                            </div>
                        </div>
                        <div id="flmbkp_progress_themes" class="mb-3" style="display:none;">
                            <div class="badge badge-success text-wrap" style="width: 9rem;">
                                <?php echo __('Themes', 'FRocket_admin'); ?>
                            </div>
                            <div id="flmbkp_themes_progress" class="progress">
                                <div id="flmbkp_themes_progress_msg" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">25%</div>
                            </div>
                        </div>
                        <div id="flmbkp_progress_uploads" class="mb-3" style="display:none;">
                            <div class="badge badge-warning text-wrap" style="width: 9rem;">
                                <?php echo __('Uploads', 'FRocket_admin'); ?>
                            </div>
                            <div id="flmbkp_uploads_progress" class="progress">
                                <div id="flmbkp_uploads_progress_msg" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">25%</div>
                            </div>
                        </div>
                        <div id="flmbkp_progress_others" class="mb-3" style="display:none;">
                            <div class="badge badge-info text-wrap" style="width: 9rem;">
                                <?php echo __('Others', 'FRocket_admin'); ?>
                            </div>
                            <div id="flmbkp_others_progress" class="progress">
                                <div id="flmbkp_others_progress_msg" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">25%</div>
                            </div>
                        </div>
                        <div id="flmbkp_progress_database" class="mb-3" style="display:none;">
                            <div class="badge badge-dark text-wrap" style="width: 9rem;">
                                <?php echo __('Database', 'FRocket_admin'); ?>
                            </div>
                            <div id="flmbkp_database_progress" class="progress">
                                <div id="flmbkp_database_progress_msg" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">25%</div>
                            </div>
                        </div>

                    </div>
 
                    <div id="flmbkp_directoryListing" style="display:none;"></div>
                    <textarea  id="flmbkp_processLog" style="width: 100%;"></textarea>
 
                    <div class="alert alert-secondary" role="alert">

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dataTable" id="users">
                                <thead>
                                    <tr>
                                        <th><?php echo __('File name', 'FRocket_admin'); ?></th>
                                        <th><?php echo __('Backup Created', 'FRocket_admin'); ?></th>
                                        <th><?php echo __('Backup Data', 'FRocket_admin'); ?></th>
                                        <th><?php echo __('Options', 'FRocket_admin'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($query)) { ?>
                                        <?php foreach ($query as $row) : ?>
                                            <tr>
                                                <td><?php echo $row->bkp_slug; ?></td>
                                                <td><?php echo $row->created_date; ?></td>
                                                <td> 
                                                <?php if (file_exists(WP_CONTENT_DIR.'/uploads/softdiscover/'.$row->bkp_slug.'_plugins.zip')) {   ?>
                                                    <button onclick="flmbkp_back_backup.options_downloadFiles('<?php echo $row->bkp_slug;?>_plugins.zip')"  class="btn btn-warning">
                                                    <i class="fa fa-download"></i> <?php echo __('Plugins', 'FRocket_admin'); ?>
                                                    </button>
                                                <?php } ?>
                                                <?php if (file_exists(WP_CONTENT_DIR.'/uploads/softdiscover/'.$row->bkp_slug.'_themes.zip')) {   ?>
                                                    <button onclick="flmbkp_back_backup.options_downloadFiles('<?php echo $row->bkp_slug;?>_themes.zip')"  class="btn btn-warning">
                                                    <i class="fa fa-download"></i> <?php echo __('Themes', 'FRocket_admin'); ?>
                                                    </button>
                                                <?php } ?>
                                                    <?php if (file_exists(WP_CONTENT_DIR.'/uploads/softdiscover/'.$row->bkp_slug.'_uploads.zip')) {   ?>
                                                    <button onclick="flmbkp_back_backup.options_downloadFiles('<?php echo $row->bkp_slug;?>_uploads.zip')"  class="btn btn-warning">
                                                    <i class="fa fa-download"></i> <?php echo __('Uploads', 'FRocket_admin'); ?>
                                                    </button>
                                                    <?php } ?>
                                                    <?php if (file_exists(WP_CONTENT_DIR.'/uploads/softdiscover/'.$row->bkp_slug.'_others.zip')) {   ?>
                                                    <button onclick="flmbkp_back_backup.options_downloadFiles('<?php echo $row->bkp_slug;?>_others.zip')"  class="btn btn-warning">
                                                    <i class="fa fa-download"></i> <?php echo __('Others', 'FRocket_admin'); ?>
                                                    </button>
                                                    <?php } ?>
                                                    <?php if (file_exists(WP_CONTENT_DIR.'/uploads/softdiscover/'.$row->bkp_slug.'_database.zip')) {   ?>
                                                    <button onclick="flmbkp_back_backup.options_downloadFiles('<?php echo $row->bkp_slug;?>_database.zip')"  class="btn btn-warning">
                                                    <i class="fa fa-download"></i> <?php echo __('Database', 'FRocket_admin'); ?>
                                                    </button>
                                                    <?php } ?>
                                                
                                                </td>
                                                <td>
                                                    <div class="sfdc-btn-group">
                                                        <ul class="unstyled">
                                                             
                                                            <li><a href="javascript:void(0);" 
                                                                   class="btn btn-danger uiform-confirmation-func-action"
                                                                   data-dialog-title="<?php echo __('Delete', 'FRocket_admin') ?>"
                                                                   data-dialog-callback="flmbkp_back_backup.records_delreg(<?php echo $row->bkp_id; ?>);"
                                                                   data-recid="<?php echo $row->bkp_id; ?>">
                                                                    <i class="fa fa-trash-o"></i> <?php echo __('Delete', 'FRocket_admin'); ?></a></li>
                                                                    <li><a href="javascript:void(0);" 
                                                                   class="btn btn-info uiform-confirmation-func-action"
                                                                   data-dialog-title="<?php echo __('Backup', 'FRocket_admin') ?>"
                                                                   data-dialog-callback="flmbkp_back_backup.records_restore(<?php echo $row->bkp_id; ?>);"
                                                                   data-recid="<?php echo $row->bkp_id; ?>">
                                                                    <i class="fa fa-window-restore"></i> <?php echo __('Restore', 'FRocket_admin'); ?></a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        endforeach;
                                        ?>
                                    <?php }else { ?>
                                        <tr>
                                            <td colspan="5">
                                                <div class="sfdc-alert sfdc-alert-info"><i class="fa fa-exclamation-triangle"></i> <?php echo __('there is not Backups', 'FRocket_admin'); ?></div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>


                    <center>
                        <div  class="pagination-wrap"><?php echo $pagination; ?></div></center>
                </div> 
            </div> 
        </div>
    </div>
</div>
<div id="uiform-confirmation-func-action-dialog" style="display: none;">
    <?php echo __('Are you sure about this?', 'FRocket_admin'); ?>
</div>
