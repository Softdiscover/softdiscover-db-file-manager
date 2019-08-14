<?php
if (!defined('ABSPATH')) {exit('No direct script access allowed');}  
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
                         for=""><?php echo $label; ?></label>
                 <a href="javascript:void(0);" 
                            data-toggle="tooltip" 
                            class="zgth-tooltip"
                            data-placement="right" 
                            data-original-title="<?php echo addslashes($help_note); ?>">
                         <span class="fa fa-question-circle"></span>
                     </a>
              </div>
             <div class="sfdc-col-sm-8">
                 <input class="zgth-switch-field"
                       id="<?php echo $id; ?>"
                       name="<?php echo $id; ?>"
                       type="checkbox" 
                       <?php echo ((string)$value==="on")?'checked':''; ?>
                       />
                   
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
$cntACmp = preg_replace("/\s+/"," ", $cntACmp);
ob_end_clean();
echo $cntACmp;
?>