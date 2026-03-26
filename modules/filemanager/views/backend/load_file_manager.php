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
  <a href="#" class="navbar-brand"><img title="<?php echo esc_attr__('Zigaform Form', 'FRocket_admin'); ?>" src="<?php echo esc_url(FLMBKP_URL . '/assets/backend/image/rockfm-logo-header.png'); ?>"></a> <div class="flmbkp-header-logo-txt"><?php esc_html_e('File manager', 'FRocket_admin'); ?></div>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" >
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="divider-menu"></li>
      <li class="nav-item active">
         <div>
            <span><?php esc_html_e('Language: ', 'FRocket_admin'); ?></span>
              <select name="flmbkp_header_language" class="browser-default custom-select">
            <option value="en" <?php echo selected($opt_lang, 'en', false); ?> > <?php esc_html_e('English', 'FRocket_admin'); ?></option>
            <option value="bg" <?php echo selected($opt_lang, 'bg', false); ?> ><?php esc_html_e('Bulgarian', 'FRocket_admin'); ?></option>
            <option value="ar" <?php echo selected($opt_lang, 'ar', false); ?> ><?php esc_html_e('Arabic', 'FRocket_admin'); ?></option>
            <option value="ca" <?php echo selected($opt_lang, 'ca', false); ?> ><?php esc_html_e('Catalan', 'FRocket_admin'); ?></option>
            <option value="cs" <?php echo selected($opt_lang, 'cs', false); ?> ><?php esc_html_e('Czech', 'FRocket_admin'); ?></option>
            <option value="da" <?php echo selected($opt_lang, 'da', false); ?> ><?php esc_html_e('Danish', 'FRocket_admin'); ?></option>
            <option value="de" <?php echo selected($opt_lang, 'de', false); ?> ><?php esc_html_e('German', 'FRocket_admin'); ?></option>
            <option value="el" <?php echo selected($opt_lang, 'el', false); ?> ><?php esc_html_e('Greek', 'FRocket_admin'); ?></option>
            <option value="es" <?php echo selected($opt_lang, 'es', false); ?> ><?php esc_html_e('Spanish', 'FRocket_admin'); ?></option>
            <option value="fa" <?php echo selected($opt_lang, 'fa', false); ?> ><?php esc_html_e('Farsi', 'FRocket_admin'); ?></option>
            <option value="fo" <?php echo selected($opt_lang, 'fo', false); ?> ><?php esc_html_e('Faeroese', 'FRocket_admin'); ?></option>
            <option value="fr" <?php echo selected($opt_lang, 'fr', false); ?> ><?php esc_html_e('French', 'FRocket_admin'); ?></option>
            <option value="he" <?php echo selected($opt_lang, 'he', false); ?> ><?php esc_html_e('Hebrew', 'FRocket_admin'); ?></option>
            <option value="hr" <?php echo selected($opt_lang, 'hr', false); ?> ><?php esc_html_e('Croatian', 'FRocket_admin'); ?></option>
            <option value="hu" <?php echo selected($opt_lang, 'hu', false); ?> ><?php esc_html_e('Hungarian', 'FRocket_admin'); ?></option>
            <option value="id" <?php echo selected($opt_lang, 'id', false); ?> ><?php esc_html_e('Indonesian', 'FRocket_admin'); ?></option>
            <option value="it" <?php echo selected($opt_lang, 'it', false); ?> ><?php esc_html_e('Italian', 'FRocket_admin'); ?></option>
            <option value="ja" <?php echo selected($opt_lang, 'ja', false); ?> ><?php esc_html_e('Japanese', 'FRocket_admin'); ?></option>
            <option value="ko" <?php echo selected($opt_lang, 'ko', false); ?> ><?php esc_html_e('Korean', 'FRocket_admin'); ?></option>
            <option value="nl" <?php echo selected($opt_lang, 'nl', false); ?> ><?php esc_html_e('Dutch', 'FRocket_admin'); ?></option>
            <option value="no" <?php echo selected($opt_lang, 'no', false); ?> ><?php esc_html_e('Norwegian', 'FRocket_admin'); ?></option>
            <option value="pl" <?php echo selected($opt_lang, 'pl', false); ?> ><?php esc_html_e('Polish', 'FRocket_admin'); ?></option>
            <option value="ro" <?php echo selected($opt_lang, 'ro', false); ?> ><?php esc_html_e('Romanian', 'FRocket_admin'); ?></option>
            <option value="ru" <?php echo selected($opt_lang, 'ru', false); ?> ><?php esc_html_e('Russian', 'FRocket_admin'); ?></option>
            <option value="sl" <?php echo selected($opt_lang, 'sl', false); ?> ><?php esc_html_e('Slovenian', 'FRocket_admin'); ?></option>
            <option value="sk" <?php echo selected($opt_lang, 'sk', false); ?> ><?php esc_html_e('Slovak', 'FRocket_admin'); ?></option>
            <option value="sr" <?php echo selected($opt_lang, 'sr', false); ?> ><?php esc_html_e('Serbian', 'FRocket_admin'); ?></option>
            <option value="sv" <?php echo selected($opt_lang, 'sv', false); ?> ><?php esc_html_e('Swedish', 'FRocket_admin'); ?></option>
            <option value="tr" <?php echo selected($opt_lang, 'tr', false); ?> ><?php esc_html_e('Turkish', 'FRocket_admin'); ?></option>
            <option value="zh_CN" <?php echo selected($opt_lang, 'zh_CN', false); ?> ><?php esc_html_e('Chinese', 'FRocket_admin'); ?></option>
            <option value="uk" <?php echo selected($opt_lang, 'uk', false); ?> ><?php esc_html_e('Ukrainian', 'FRocket_admin'); ?></option>
            <option value="vi" <?php echo selected($opt_lang, 'vi', false); ?> ><?php esc_html_e('Vietnamese', 'FRocket_admin'); ?></option>
            <option value="zh_TW" <?php echo selected($opt_lang, 'zh_TW', false); ?> ><?php esc_html_e('Taiwan', 'FRocket_admin'); ?></option>
          </select>
            </div>
      </li>
      <li class="divider-menu"></li>
      <li class="nav-item">
         <div>
          <span><?php esc_html_e('Theme: ', 'FRocket_admin'); ?></span>
              <select name="flmbkp_header_theme"  class="browser-default custom-select">
            <option value="default" <?php echo selected($opt_theme, 'default', false); ?> ><?php esc_html_e('Default', 'FRocket_admin'); ?></option>
            <option value="gray" <?php echo selected($opt_theme, 'gray', false); ?>><?php esc_html_e('Gray', 'FRocket_admin'); ?></option>
            <option value="light" <?php echo selected($opt_theme, 'light', false); ?>><?php esc_html_e('Light', 'FRocket_admin'); ?></option>
            <option value="dark" <?php echo selected($opt_theme, 'dark', false); ?>><?php esc_html_e('dark', 'FRocket_admin'); ?></option>
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
echo wp_kses($cntACmp, Flmbkp_Form_Helper::get_allowed_admin_html());
?>
