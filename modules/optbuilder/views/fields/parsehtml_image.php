<?php
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
?>
<?php
ob_start();
?>
<div class="sfdc-row">
    <div class="sfdc-col-md-12">
        <div class="sfdc-form-group">
            <div class="sfdc-col-sm-4">
                     <label 
                         class="zgth-form-label" 
                         for=""><?php echo esc_html($label); ?></label>
                 <a href="javascript:void(0);" 
                            data-toggle="tooltip" 
                            class="zgth-tooltip"
                            data-placement="right" 
                            data-original-title="<?php echo esc_attr($help_note); ?>">
                         <span class="fa fa-question-circle"></span>
                     </a>
              </div>
             <div class="sfdc-col-sm-8">
                 
                 <div 
                     data-dialog-title="<?php echo esc_attr__('Choose an Image', 'zgpbd_admin'); ?>"
                     data-dialog-btn="<?php echo esc_attr__('Choose', 'zgpbd_admin'); ?>"
                     class="zgth-opt-img-wrap">
                      
                     
                     
                     <div  class="sfdc-input-group">
                               
                                <input type="text" 
                                       id="<?php echo esc_attr($id); ?>"
                                       class="zgth-opt-img-inp sfdc-form-control" 
                                       value="<?php echo esc_url($value); ?>" 
                                       name="<?php echo esc_attr($id); ?>">
                         
                                <span class="sfdc-input-group-addon sfdc-btn sfdc-btn-default sfdc-btn-file">
                                    <span class=""><?php esc_html_e('Select image', 'zgpbd_admin'); ?></span>
                                    <!--<span class="fileinput-exists">Change</span><input type="hidden"><input type="file" name="...">-->
                                </span>
                                
                                  <a  style="display:none;" class=" sfdc-btn sfdc-btn-danger sfdc-input-group-addon" href="javascript:void(0);">
                                      
                                      <i class="fa fa-trash-o"></i> <?php esc_html_e('Remove', 'zgpbd_admin'); ?>
                                      
                                  </a>
                         
                       </div>
                     <div style="display:none;" class="zgth-opt-img-preview">
                         <img src="<?php echo esc_url($value); ?>"
                              class="sfdc-img-thumbnail">
                     </div>
                     
                 </div>
                     
                    
            </div>
        </div>
    </div>
</div>
<?php
$cntACmp = ob_get_contents();
$cntACmp = str_replace("\n", '', $cntACmp);
$cntACmp = str_replace("\t", '', $cntACmp);
$cntACmp = str_replace("\r", '', $cntACmp);
$cntACmp = str_replace("//-->", ' ', $cntACmp);
$cntACmp = str_replace("//<!--", ' ', $cntACmp);
$cntACmp = preg_replace("/\s+/", " ", $cntACmp);
ob_end_clean();
echo wp_kses_post($cntACmp);
?>
