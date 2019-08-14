<?php
if (!defined('ABSPATH')) {exit('No direct script access allowed');}  
?>
<?php
ob_start();
?>

<button  
        <?php if(!empty($onclick)){?>
        onclick="<?php echo $onclick; ?>"
        <?php }?>
        class="sfdc-btn sfdc-btn-primary"
        type="button"><?php echo $value; ?></button> 

 
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