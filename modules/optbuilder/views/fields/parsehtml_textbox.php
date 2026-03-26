<?php
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
?>
<?php
ob_start();
?>
<div id="<?php echo esc_attr($id); ?>_wrapper" style="<?php echo isset($wrapper_style) ? esc_attr($wrapper_style) : ''; ?>">
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
                     <input class="sfdc-form-control  " 
                            placeholder="<?php echo isset($placeholder) ? esc_attr($placeholder) : ''; ?>" 
                            name="<?php echo esc_attr($id); ?>" 
                            id="<?php echo esc_attr($id); ?>" 
                            value="<?php echo esc_attr($value); ?>"
                            type="text"> 
                    
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
