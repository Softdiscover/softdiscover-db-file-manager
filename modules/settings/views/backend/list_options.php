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
<form class="rockfm-form" 
                              action="" 
                              name="" 
                              method="post" 
                              enctype="multipart/form-data" 
                              id="dbflm_page_settings_form">
    
<div id="dbflm_page_settings" class="sfdc-block1-container" >
    <div class="space20"></div>
    <div class="sfdc-row">
        <div class="col-lg-12">
            <div class="widget widget-padding span12">
                <div class="widget-header">
                    <i class="fa fa-list-alt"></i>
                    <h5>
                        <?php echo __('Settings', 'FRocket_admin'); ?>
                    </h5>

                </div>  
                <div class="widget-body">  
                   <!-- form user info -->
            <div class="card card-outline-secondary">
              <div class="card-header">
                <h3 class="mb-0"><?php echo __('General', 'FRocket_admin'); ?></h3>
              </div>
              <div class="card-body">
                
                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"><?php echo __('Select User Roles to access this plugin', 'FRocket_admin'); ?></label>
                    <div class="col-lg-9">
                        
                       <?php foreach ($roles as $key => $value) { ?>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="flm_roles[]" value="<?php echo $value['role'];?>" <?php echo ($value['ischecked'])?'checked':''; ?>  <?php echo ($value['primaryrole'])?'disabled':''; ?>  > <?php echo $value['role'];?>
                                </label>
                              </div>
                        <?php } ?> 
                        
              
                    </div>
                  </div>
                  <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><?php echo __('Note', 'FRocket_admin'); ?></strong> <?php echo __("Allow user roles to access this plugin. Once User Role is added, you need to give 'manage_options' capability to those selected User Roles. there are many plugins out there to give 'manage_options' capability. ", 'FRocket_admin'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="form-group row">
                    <label class="col-lg-3 col-form-label form-control-label"></label>
                    <div class="col-lg-9">
                      <input class="btn btn-secondary" type="reset" value="Cancel"> 
			<input class="btn btn-primary" type="button" value="Save Changes">
                    </div>
                  </div>
                
              </div>
            </div><!-- /form user info -->
                </div> 
            </div> 
        </div>
    </div>
</div>
</form>