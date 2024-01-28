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
 <div class="alert alert-success" role="alert">
     <ul>
         <?php foreach ($log as $key => $value) { ?>
            <li><?php echo $value;?></li>
         <?php } ?>
     </ul>
</div>
