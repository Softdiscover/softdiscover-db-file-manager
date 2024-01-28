<?php
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
?>

<div class="sfdclauncher uiform-wrap" id="rocketform-bk-dashboard">
    <div id="rocketform-bk-header">
      <?php include('header.php');?>
    </div>
    <div id="rocketform-bk-content">
        <?php echo $content;?>
        <div class="clear"></div>
    </div>
    <div id="rocketform-bk-footer">
        <?php include('footer.php');?>
    </div>
</div> 

