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
                 
               
                  <?php $cats = get_categories('hide_empty=0'); ?>
                    <?php
                     // $cat_sel=backend_get_option('blog','blog_exclude_category',$this->options);
                      $cat_sel=zgth_get_option('blog', 'zgth_blog_exclude_category');
                    ?>  
                    <!-- Build your select: -->
                    <select 
                        name="<?php echo $id; ?>[]"
                         id="<?php echo $id; ?>"
                        class="zgth-option-inp-multisel"
                        multiple="true">
                    <?php foreach ($cats as $value) { ?>
                    <option value="<?php echo $value->cat_ID; ?>" <?php if (is_array($cat_sel) && in_array($value->cat_ID, $cat_sel)) {
                        echo 'selected="selected"';
                                   }?> ><?php echo $value->name;?></option>
                    <?php } ?> 
                    </select>
                    
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
echo $cntACmp;
?>
