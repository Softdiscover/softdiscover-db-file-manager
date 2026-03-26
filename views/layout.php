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
        <?php echo wp_kses($content, Flmbkp_Form_Helper::get_allowed_admin_html()); ?>
        <div class="clear"></div>
    </div>
    <div id="rocketform-bk-footer">
        <?php include('footer.php');?>
    </div>
</div>
