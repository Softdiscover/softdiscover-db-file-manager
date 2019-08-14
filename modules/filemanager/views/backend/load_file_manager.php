<?php
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
ob_start();
?>
 <div class="zgfm-fmanager-container">
     <form id="flmbkp_header_opt" method="post">
    <div class="uiform-editing-header">
        
        
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a href="javascript:void(0);" class="navbar-brand"><img title="Zigaform Form" src="<?php echo FLMBKP_URL;?>/assets/backend/image/rockfm-logo-header.png"></a> <div class="flmbkp-header-logo-txt"><?php echo __('File manager','FRocket_admin');?></div>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" >
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="divider-menu"></li>
      <li class="nav-item active">
         <div>
            <span><?php echo __('Language: ','FRocket_admin'); ?></span>
              <select name="flmbkp_header_language" class="browser-default custom-select">
            <option value="en" <?php echo ($opt_lang=='en')?'selected':'';?> > <?php echo __('English','FRocket_admin'); ?></option>
            <option value="bg" <?php echo ($opt_lang=='bg')?'selected':'';?> ><?php echo __('Bulgarian','FRocket_admin'); ?></option>
            <option value="ar" <?php echo ($opt_lang=='ar')?'selected':'';?> ><?php echo __('Arabic','FRocket_admin'); ?></option>
            <option value="ca" <?php echo ($opt_lang=='ca')?'selected':'';?> ><?php echo __('Catalan','FRocket_admin'); ?></option>
            <option value="cs" <?php echo ($opt_lang=='cs')?'selected':'';?> ><?php echo __('Czech','FRocket_admin'); ?></option>
            <option value="da" <?php echo ($opt_lang=='da')?'selected':'';?> ><?php echo __('Danish','FRocket_admin'); ?></option>
            <option value="de" <?php echo ($opt_lang=='de')?'selected':'';?> ><?php echo __('German','FRocket_admin'); ?></option>
            <option value="el" <?php echo ($opt_lang=='el')?'selected':'';?> ><?php echo __('Greek','FRocket_admin'); ?></option>
            <option value="es" <?php echo ($opt_lang=='es')?'selected':'';?> ><?php echo __('Spanish','FRocket_admin'); ?></option>
            <option value="fa" <?php echo ($opt_lang=='fa')?'selected':'';?> ><?php echo __('Farsi','FRocket_admin'); ?></option>
            <option value="fo" <?php echo ($opt_lang=='fo')?'selected':'';?> ><?php echo __('Faeroese','FRocket_admin'); ?></option>
            <option value="fr" <?php echo ($opt_lang=='fr')?'selected':'';?> ><?php echo __('French','FRocket_admin'); ?></option>
            <option value="he" <?php echo ($opt_lang=='he')?'selected':'';?> ><?php echo __('Hebrew','FRocket_admin'); ?></option>
            <option value="hr" <?php echo ($opt_lang=='hr')?'selected':'';?> ><?php echo __('Croatian','FRocket_admin'); ?></option>
            <option value="hu" <?php echo ($opt_lang=='hu')?'selected':'';?> ><?php echo __('Hungarian','FRocket_admin'); ?></option>
            <option value="id" <?php echo ($opt_lang=='id')?'selected':'';?> ><?php echo __('Indonesian','FRocket_admin'); ?></option>
            <option value="it" <?php echo ($opt_lang=='it')?'selected':'';?> ><?php echo __('Italian','FRocket_admin'); ?></option>
            <option value="ja" <?php echo ($opt_lang=='ja')?'selected':'';?> ><?php echo __('Japanese','FRocket_admin'); ?></option>
            <option value="ko" <?php echo ($opt_lang=='ko')?'selected':'';?> ><?php echo __('Korean','FRocket_admin'); ?></option>
            <option value="nl" <?php echo ($opt_lang=='nl')?'selected':'';?> ><?php echo __('Dutch','FRocket_admin'); ?></option>
            <option value="no" <?php echo ($opt_lang=='no')?'selected':'';?> ><?php echo __('Norwegian','FRocket_admin'); ?></option>
            <option value="pl" <?php echo ($opt_lang=='pl')?'selected':'';?> ><?php echo __('Polish','FRocket_admin'); ?></option>
            <option value="ro" <?php echo ($opt_lang=='ro')?'selected':'';?> ><?php echo __('Romanian','FRocket_admin'); ?></option>
            <option value="ru" <?php echo ($opt_lang=='ru')?'selected':'';?> ><?php echo __('Russian','FRocket_admin'); ?></option>
            <option value="sl" <?php echo ($opt_lang=='sl')?'selected':'';?> ><?php echo __('Slovenian','FRocket_admin'); ?></option>
            <option value="sk" <?php echo ($opt_lang=='sk')?'selected':'';?> ><?php echo __('Slovak','FRocket_admin'); ?></option>
            <option value="sr" <?php echo ($opt_lang=='sr')?'selected':'';?> ><?php echo __('Serbian','FRocket_admin'); ?></option>
            <option value="sv" <?php echo ($opt_lang=='sv')?'selected':'';?> ><?php echo __('Swedish','FRocket_admin'); ?></option>
            <option value="tr" <?php echo ($opt_lang=='tr')?'selected':'';?> ><?php echo __('Turkish','FRocket_admin'); ?></option>
            <option value="zh_CN" <?php echo ($opt_lang=='zh_CN')?'selected':'';?> ><?php echo __('Chinese','FRocket_admin'); ?></option>
            <option value="uk" <?php echo ($opt_lang=='uk')?'selected':'';?> ><?php echo __('Ukrainian','FRocket_admin'); ?></option>
            <option value="vi" <?php echo ($opt_lang=='vi')?'selected':'';?> ><?php echo __('Vietnamese','FRocket_admin'); ?></option>
            <option value="zh_TW" <?php echo ($opt_lang=='zh_TW')?'selected':'';?> ><?php echo __('Taiwan','FRocket_admin'); ?></option>
          </select>
            </div>
      </li>
      <li class="divider-menu"></li>
      <li class="nav-item">
         <div>
          <span><?php echo __('Theme: ','FRocket_admin'); ?></span>
              <select name="flmbkp_header_theme"  class="browser-default custom-select">
            <option value="default" <?php echo ($opt_theme=='default')?'selected':'';?> ><?php echo __('Default','FRocket_admin'); ?></option>
            <option value="gray" <?php echo ($opt_theme=='gray')?'selected':'';?>><?php echo __('Gray','FRocket_admin'); ?></option>
            <option value="light" <?php echo ($opt_theme=='light')?'selected':'';?>><?php echo __('Light','FRocket_admin'); ?></option>
            <option value="dark" <?php echo ($opt_theme=='dark')?'selected':'';?>><?php echo __('dark','FRocket_admin'); ?></option>
          </select>
            </div>  
      </li>
      
    </ul>
     
  </div>
</nav>
 
</div>
     </form>
    <div id="elfinder"></div>
</div>
<?php
$cntACmp = ob_get_contents();
$cntACmp = Flmbkp_Form_Helper::sanitize_output($cntACmp);
ob_end_clean();
echo $cntACmp;
?>