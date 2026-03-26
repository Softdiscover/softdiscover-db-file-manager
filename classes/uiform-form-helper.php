<?php

/**
 * Frontend
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://www.softdiscover.com/
 */
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if (class_exists('Flmbkp_Form_Helper')) {
    return;
}

class Flmbkp_Form_Helper
{

    public static function human_filesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    public static function getroute()
    {
        $return = array(
            'module' => '',
            'controller' => '',
            'action' => ''
        );

        $request_method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper(sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD']))) : 'GET';

        if ('POST' === $request_method) {
            $return['module'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_POST, 'flmbkp_mod', FILTER_UNSAFE_RAW));
            $return['controller'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_POST, 'flmbkp_contr', FILTER_UNSAFE_RAW));
            $return['action'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_POST, 'flmbkp_action', FILTER_UNSAFE_RAW));
        } elseif ('GET' === $request_method) {
            $return['module'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, 'flmbkp_mod', FILTER_UNSAFE_RAW));
            $return['controller'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, 'flmbkp_contr', FILTER_UNSAFE_RAW));
            $return['action'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, 'flmbkp_action', FILTER_UNSAFE_RAW));
        } else {
            $return['module'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, 'flmbkp_mod', FILTER_UNSAFE_RAW));
            $return['controller'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, 'flmbkp_contr', FILTER_UNSAFE_RAW));
            $return['action'] = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, 'flmbkp_action', FILTER_UNSAFE_RAW));
        }
        return $return;
    }

    public static function getHttpRequest($var)
    {
        $var = strval($var);
        $value = '';
        $request_method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper(sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD']))) : 'GET';

        if ('POST' === $request_method) {
            $value = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_POST, $var, FILTER_UNSAFE_RAW));
        } elseif ('GET' === $request_method) {
            $value = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, $var, FILTER_UNSAFE_RAW));
        } else {
            $value = Flmbkp_Form_Helper::sanitizeInput((string) filter_input(INPUT_GET, $var, FILTER_UNSAFE_RAW));
        }

        return $value;
    }


    public static function array2xml($array, $xml = null)
    {
        if (!isset($xml)) {
            $xml = new SimpleXMLElement('<params/>');
        }
        foreach ($array as $key => $value) {
            if (is_array($value) || is_object($value)) {
                Flmbkp_Form_Helper::array2xml($value, $xml);
            } else {
                if (is_numeric($key)) {
                    if (is_string($value)) {
                        $xml->addChild('item', htmlentities($value, ENT_NOQUOTES, "UTF-8"));
                    } else {
                        $xml->addChild('item', $value);
                    }
                } elseif (is_string($value)) {
                    $xml->addChild($key, htmlentities($value, ENT_NOQUOTES, "UTF-8"));
                } else {
                    $xml->addChild($key, $value);
                }
            }
        }
        return $xml->asXML();
    }

    public static function generate_pagination()
    {
    }

    public static function convert_HexToRGB($hex)
    {
        // Format the hex color string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }

        // Get decimal values
        $arr = array();
        $arr[] = $r = hexdec(substr($hex, 0, 2));
        $arr[] = $g = hexdec(substr($hex, 2, 2));
        $arr[] = $b = hexdec(substr($hex, 4, 2));

        return $arr;
    }

    /**
     * Sanitize input
     *
     * @param string $string input
     *
     * @return array
     */
    public static function sanitizeInput($string)
    {
        $string = filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $string = stripslashes($string);
        $string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = preg_replace('/[\n\r\t]/', ' ', $string);
        $string = trim($string, "\x00..\x1F");
        $string = sanitize_text_field($string);
        return $string;
    }

    /**
     * Sanitize input, temporal. it will be removed in future
     *
     * @param string $string input
     *
     * @return array
     */
    public static function sanitizeInput_html($string)
    {
        $string = stripslashes($string);
        $string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = preg_replace('/[\n\r\t]/', ' ', $string);
        $string = trim($string, "\x00..\x1F");
        return $string;
    }

    /**
     * Sanitize input
     *
     * @param string $string input
     *
     * @return array
     */
    public static function sanitizeFnamestring($string)
    {
        $string = preg_replace('/\s+/', '', $string);
        $string= preg_replace("/'/i", '', $string);
        $string= preg_replace('/"/i', '', $string);
        $string= preg_replace('/[^\pL\pN]+/', '', $string);
        $string = preg_replace("/[^a-zA-Z0-9]+/", "", $string);
        $string= strtolower($string);
        return $string;
    }


    /**
     * Sanitize input
     *
     * @param string $string input
     *
     * @return array
     */
    public static function sanitizeFileName($string)
    {
        $string = preg_replace('/\s+/', '', $string);
        $string= preg_replace("/'/i", '', $string);
        $string= preg_replace('/"/i', '', $string);
        $string= preg_replace('/[^\pL\pN_-]+/', '', $string);
        $string = preg_replace("/[^a-zA-Z0-9_-]+/", "", $string);
        $string= strtolower($string);
        return $string;
    }


    /**
     * Sanitize recursive
     *
     * @param string $data array
     *
     * @return array
     */
    public static function sanitizeRecursive($data)
    {
        if (is_array($data)) {
            return array_map(array('Flmbkp_Form_Helper', 'sanitizeRecursive'), $data);
        } else {
            return Flmbkp_Form_Helper::sanitizeInput($data);
        }
    }

    /**
     * Sanitize recursive
     *
     * @param string $data array
     *
     * @return array
     */
    public static function sanitizeRecursive_html($data)
    {
        if (is_array($data)) {
            return array_map(array('Flmbkp_Form_Helper', 'sanitizeRecursive_html'), $data);
        } else {
            return Flmbkp_Form_Helper::sanitizeInput_html($data);
        }
    }


    public static function data_encrypt($string, $key)
    {
        $string = (string) $string;
        $key = (string) $key;

        if ('' === $key) {
            return '';
        }

        if (function_exists('openssl_encrypt') && function_exists('openssl_cipher_iv_length')) {
            $cipher = 'aes-256-cbc';
            $iv_length = openssl_cipher_iv_length($cipher);
            if (is_int($iv_length) && $iv_length > 0) {
                $iv = false;
                if (function_exists('random_bytes')) {
                    try {
                        $iv = random_bytes($iv_length);
                    } catch (Exception $exception) {
                        $iv = false;
                    }
                }
                if (false === $iv && function_exists('openssl_random_pseudo_bytes')) {
                    $iv = openssl_random_pseudo_bytes($iv_length);
                }

                if (is_string($iv) && strlen($iv) === $iv_length) {
                    $key_material = hash('sha256', $key, true);
                    $ciphertext = openssl_encrypt($string, $cipher, $key_material, OPENSSL_RAW_DATA, $iv);
                    if (false !== $ciphertext) {
                        $mac = hash_hmac('sha256', $iv . $ciphertext, $key_material, true);
                        return 'v2:' . base64_encode($iv . $mac . $ciphertext);
                    }
                }
            }
        }

        // Legacy fallback for environments without OpenSSL support.
        $result = '';
        $key_length = strlen($key);
        $string_length = strlen($string);
        for ($i = 0; $i < $string_length; $i++) {
            $char = $string[$i];
            $key_index = ($i % $key_length) - 1;
            if ($key_index < 0) {
                $key_index = $key_length - 1;
            }
            $keychar = $key[$key_index];
            $result .= chr((ord($char) + ord($keychar)) % 256);
        }

        return base64_encode($result);
    }

    public static function data_decrypt($string, $key)
    {
        $string = (string) $string;
        $key = (string) $key;

        if ('' === $key || '' === $string) {
            return '';
        }

        if (0 === strpos($string, 'v2:') && function_exists('openssl_decrypt') && function_exists('openssl_cipher_iv_length')) {
            $payload = base64_decode(substr($string, 3), true);
            if (false === $payload) {
                return '';
            }

            $cipher = 'aes-256-cbc';
            $iv_length = openssl_cipher_iv_length($cipher);
            $mac_length = 32; // raw sha256 HMAC length

            if (!is_int($iv_length) || $iv_length <= 0 || strlen($payload) <= ($iv_length + $mac_length)) {
                return '';
            }

            $iv = substr($payload, 0, $iv_length);
            $mac = substr($payload, $iv_length, $mac_length);
            $ciphertext = substr($payload, $iv_length + $mac_length);

            $key_material = hash('sha256', $key, true);
            $calc_mac = hash_hmac('sha256', $iv . $ciphertext, $key_material, true);
            if (!self::timing_safe_equals($mac, $calc_mac)) {
                return '';
            }

            $decrypted = openssl_decrypt($ciphertext, $cipher, $key_material, OPENSSL_RAW_DATA, $iv);
            return (false !== $decrypted) ? $decrypted : '';
        }

        return self::legacy_data_decrypt($string, $key);
    }

    private static function timing_safe_equals($known, $user)
    {
        if (!is_string($known) || !is_string($user)) {
            return false;
        }

        if (function_exists('hash_equals')) {
            return hash_equals($known, $user);
        }

        $known_length = strlen($known);
        if ($known_length !== strlen($user)) {
            return false;
        }

        $status = 0;
        for ($i = 0; $i < $known_length; $i++) {
            $status |= ord($known[$i]) ^ ord($user[$i]);
        }

        return (0 === $status);
    }

    private static function legacy_data_decrypt($string, $key)
    {
        $decoded = base64_decode($string, true);
        if (false === $decoded || '' === $key) {
            return '';
        }

        $result = '';
        $key_length = strlen($key);
        $decoded_length = strlen($decoded);

        for ($i = 0; $i < $decoded_length; $i++) {
            $char = $decoded[$i];
            $key_index = ($i % $key_length) - 1;
            if ($key_index < 0) {
                $key_index = $key_length - 1;
            }
            $keychar = $key[$key_index];
            $result .= chr((ord($char) - ord($keychar) + 256) % 256);
        }

        return $result;
    }

    public static function base64url_encode($s)
    {
        return str_replace(array('+', '/'), array('-', '_'), base64_encode($s));
    }

    public static function base64url_decode($s)
    {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $s));
    }

    // Javascript/HTML hex encode
    public static function encodeHex($input)
    {
        $temp = '';
        $length = strlen($input);
        for ($i = 0; $i < $length; $i++) {
            $temp .= '%' . bin2hex($input[$i]);
        }
        return $temp;
    }

    public static function check_field_length($data, $length)
    {
        return (strlen($data) > intval($length))? substr($data, 0, intval($length)):'';
    }

    public static function sql_quote($value)
    {
        if ( get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }

        $value = addslashes($value);

        return $value;
    }

    public static function form_store_fonts($font_temp)
    {
        global $global_fonts_stored;
        if (!empty($font_temp['import_family']) && !in_array($font_temp['import_family'], $global_fonts_stored)) {
            $global_fonts_stored[]= $font_temp['import_family'];
        }
    }

    public static function is_flmbkp_page()
    {
        $search = Flmbkp_Form_Helper::getHttpRequest('page');

        $allow = array('flmbkp_file_manager', 'flmbkp_page_backups', 'flmbkp_page_database', 'flmbkp_page_settings');

        if (in_array($search, $allow, true)) {
            return true;
        }

        return false;
    }

    public static function remove_non_tag_space($text)
    {
        $len = strlen($text);
        $out = "";
        $in_tag = false;
        for ($i = 0; $i < $len; $i++) {
            $c = $text[$i];
            if ($c == '<') {
                $in_tag = true;
            } elseif ($c == '>') {
                $in_tag = false;
            }

            $out .= $c == " " ? ($in_tag ? $c : "") : $c;
        }
        return $out;
    }

    public static function assign_alert_container($msg, $type)
    {
        $return_msg='';
        switch ($type) {
            case 1:
                /*success*/
                $return_msg.='<div class="alert alert-success" role="alert">'.$msg.'</div>';
                break;
            case 2:
                /*info*/
                $return_msg.='<div class="alert alert-info" role="alert">'.$msg.'</div>';
                break;
            case 3:
                /*warning*/
                $return_msg.='<div class="alert alert-warning" role="alert">'.$msg.'</div>';
                break;
            case 4:
                /*danger*/
                $return_msg.='<div class="alert alert-danger" role="alert">'.$msg.'</div>';
                break;
            default:
                break;
        }
        return $return_msg;
    }

    /**
    * Verify if field is checked
    *
    * @param int $row    value field
    * @param int $status status check
    *
    * @return array
    */
    public static function getChecked($row, $status)
    {
        if ($row == $status) {
            echo "checked=\"checked\"";
        }
    }

    public static function sanitize_output($buffer)
    {

        $search = array(
        '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
        '/[^\S ]+\</s',  // strip whitespaces before tags, except space
        '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
        '>',
        '<',
        '\\1'
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

    /**
     * Allowed admin HTML for plugin-rendered templates.
     *
     * @return array
     */
    public static function get_allowed_admin_html()
    {
        static $allowed_html = null;
        static $style_filter_registered = false;

        if (!$style_filter_registered) {
            add_filter('safe_style_css', array('Flmbkp_Form_Helper', 'allow_admin_safe_style_css'));
            $style_filter_registered = true;
        }

        if (null !== $allowed_html) {
            return $allowed_html;
        }

        $allowed_html = wp_kses_allowed_html('post');

        $common_attrs = array(
            'id' => true,
            'class' => true,
            'style' => true,
            'title' => true,
            'onclick' => true,
            'role' => true,
            'aria-label' => true,
            'aria-hidden' => true,
            'aria-controls' => true,
            'aria-expanded' => true,
            'aria-valuenow' => true,
            'aria-valuemin' => true,
            'aria-valuemax' => true,
            'data-toggle' => true,
            'data-target' => true,
            'data-dialog-title' => true,
            'data-dialog-callback' => true,
            'data-recid' => true,
        );

        $common_tags = array(
            'div',
            'span',
            'ul',
            'ol',
            'li',
            'a',
            'button',
            'nav',
            'form',
            'input',
            'select',
            'option',
            'optgroup',
            'textarea',
            'label',
            'i',
            'fieldset',
            'legend',
            'table',
            'thead',
            'tbody',
            'tfoot',
            'tr',
            'th',
            'td',
            'center',
        );

        foreach ($common_tags as $tag) {
            if (!isset($allowed_html[$tag])) {
                $allowed_html[$tag] = array();
            }
            $allowed_html[$tag] = array_merge($allowed_html[$tag], $common_attrs);
        }

        $allowed_html['a'] = array_merge(
            $allowed_html['a'],
            array(
                'href' => true,
                'target' => true,
                'rel' => true,
            )
        );

        $allowed_html['img'] = array_merge(
            isset($allowed_html['img']) ? $allowed_html['img'] : array(),
            array(
                'id' => true,
                'class' => true,
                'src' => true,
                'alt' => true,
                'title' => true,
                'width' => true,
                'height' => true,
            )
        );

        $allowed_html['form'] = array_merge(
            $allowed_html['form'],
            array(
                'action' => true,
                'method' => true,
                'name' => true,
                'enctype' => true,
                'autocomplete' => true,
                'novalidate' => true,
            )
        );

        $allowed_html['input'] = array_merge(
            $allowed_html['input'],
            array(
                'type' => true,
                'name' => true,
                'value' => true,
                'checked' => true,
                'selected' => true,
                'placeholder' => true,
                'disabled' => true,
                'readonly' => true,
                'required' => true,
                'multiple' => true,
                'size' => true,
                'min' => true,
                'max' => true,
                'step' => true,
                'autocomplete' => true,
            )
        );

        $allowed_html['select'] = array_merge(
            $allowed_html['select'],
            array(
                'name' => true,
                'multiple' => true,
                'size' => true,
                'disabled' => true,
                'required' => true,
            )
        );

        $allowed_html['option'] = array_merge(
            $allowed_html['option'],
            array(
                'value' => true,
                'selected' => true,
                'disabled' => true,
                'label' => true,
            )
        );

        $allowed_html['optgroup'] = array_merge(
            $allowed_html['optgroup'],
            array(
                'label' => true,
                'disabled' => true,
            )
        );

        $allowed_html['textarea'] = array_merge(
            $allowed_html['textarea'],
            array(
                'name' => true,
                'rows' => true,
                'cols' => true,
                'disabled' => true,
                'readonly' => true,
                'required' => true,
                'placeholder' => true,
            )
        );

        $allowed_html['button'] = array_merge(
            $allowed_html['button'],
            array(
                'type' => true,
                'name' => true,
                'value' => true,
                'disabled' => true,
            )
        );

        return $allowed_html;
    }

    public static function allow_admin_safe_style_css($allowed_attr)
    {
        if (!is_array($allowed_attr)) {
            return $allowed_attr;
        }

        if (!in_array('display', $allowed_attr, true)) {
            $allowed_attr[] = 'display';
        }

        return $allowed_attr;
    }


    /**
     * Escape String
     *
     * @access  public
     * @param   string
     * @param   bool    whether or not the string will be used in a LIKE condition
     * @return  string
     */
    public static function escape_str($str, $like = false)
    {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = $this->escape_str($val, $like);
            }

            return $str;
        }

        if (!version_compare('5.5', phpversion(), '>=')) {
            $str = addslashes($str);
        }else {
            if (function_exists('mysql_real_escape_string')) {
                    $str = mysql_real_escape_string($str);
            }
            elseif (function_exists('mysql_escape_string')) {
                    $str = mysql_escape_string($str);
            }
            else {
                    $str = addslashes($str);
            }
        }




        return $str;
    }


    public static function mysql_version()
    {




        if (!version_compare('5.5', phpversion(), '>=')) {
             $database_name=DB_NAME;
            $database_user=DB_USER;
            $datadase_password=DB_PASSWORD;
            $database_host=DB_HOST;

                $con=mysqli_connect($database_host, $database_user, $datadase_password, $database_name);
                // Check connection
            if (mysqli_connect_errno()) {
               // echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

               $str = mysqli_get_server_info($con);
        }else {
            $str = mysql_get_server_info();
        }

        return $str;
    }

    public static function isValidUrl_structure($url)
    {
        // first do some quick sanity checks:
        if (!$url || !is_string($url)) {
            return false;
        }
        // quick check url is roughly a valid http request: ( http://blah/... )
        if ( ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)) {
            return false;
        }

        // all good!
        return true;
    }

    public static function json_encode_advanced(array $arr, $sequential_keys = false, $quotes = false, $beautiful_json = false)
    {

        $output = Flmbkp_Form_Helper::isAssoc($arr) ? "{" : "[";
         $count = 0;
        foreach ($arr as $key => $value) {
            if (Flmbkp_Form_Helper::isAssoc($arr) || (!Flmbkp_Form_Helper::isAssoc($arr) && $sequential_keys == true )) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ' : ';
            }

            if (is_array($value)) {
                $output .= Flmbkp_Form_Helper::json_encode_advanced($value, $sequential_keys, $quotes, $beautiful_json);
            }
            else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            }
            else if (is_numeric($value)) {
                $output .= $value;
            }
            else if (is_object($value)) {
                $output .= '0';
            }
            else {
                $output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
            }

            if (++$count < count($arr)) {
                $output .= ', ';
            }
        }

         $output .= Flmbkp_Form_Helper::isAssoc($arr) ? "}" : "]";

         return $output;
    }

    public static function isAssoc(array $arr)
    {
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function zigaform_user_is_on_admin_page($page_name = 'admin.php')
    {
        global $pagenow;
        return ($pagenow == $page_name);
    }

    public static function get_font_library()
    {
        require_once(FLMBKP_DIR . '/libraries/styles-font-menu/plugin.php');
        $objsfm = new SFM_Plugin();

        return $objsfm;
    }



        /*
         * Create and get backup directoy
         */
    public static function backup_directory()
    {

        $backup_dir = WP_CONTENT_DIR.'/uploads/softdiscover';

        if ((!is_dir($backup_dir) ||
                !is_file($backup_dir.'/index.html') ||
                !is_file($backup_dir.'/.htaccess')) &&
                !is_file($backup_dir.'/index.php') ||
                !is_file($backup_dir.'/web.config')) {
            @mkdir($backup_dir, 0775, true);
            @file_put_contents($backup_dir.'/index.html', "<html><body><a href=\"https://softdiscover.com\" target=\"_blank\">WordPress backups by Softdiscover</a></body></html>");

            if (!is_file($backup_dir.'/.htaccess')) {
                            @file_put_contents($backup_dir.'/.htaccess', 'deny from all');
            }

            if (!is_file($backup_dir.'/web.config')) {
                            @file_put_contents($backup_dir.'/web.config', "<configuration>\n<system.webServer>\n<authorization>\n<deny users=\"*\" />\n</authorization>\n</system.webServer>\n</configuration>\n");
            }
        }

        return $backup_dir;
    }

    public static function get_user_roles()
    {

        if ( !current_user_can('promote_users')) {
            $answer = array('result'=>'error', 'message'=>esc_html__('Not enough permissions', 'FRocket_admin'));
            return $answer;
        }


        $user_id = get_current_user_id();

        if (empty($user_id)) {
            $answer = array('result'=>'error', 'message'=>esc_html__('Wrong request, valid user ID was missed', 'FRocket_admin'));
            return $answer;
        }

        $user = get_user_by('id', $user_id);
        if (empty($user)) {
            $answer = array('result'=>'error', 'message'=>esc_html__('Requested user does not exist', 'FRocket_admin'));
            return $answer;
        }

        $other_roles = array_values($user->roles);
        $primary_role = array_shift($other_roles);

        global $wp_roles;

        $roles = $wp_roles->roles;
        $roles_main=array();
        foreach ($roles as $key => $value) {
            $roles_main[]=$key;
        }

        $answer = array('result'=>'success', 'primary_role'=>$primary_role, 'other_roles'=>$roles_main);

        return $answer;
    }

    /**
     * Check user access
     */
    public static function check_User_Access()
    {

        // for logged users
        if (!is_user_logged_in()) {
            return false;
        }

        if ( current_user_can('promote_users')) {
            return true;
        }

         // make sure the user have manage options
        if (!current_user_can('manage_options')) {
            return false;
        }

        $user = wp_get_current_user();
        $allowed_roles = get_option('dbflm_fmanager_roles', array());
        //var_dump($user->roles[0]);
        if (!in_array($user->roles[0], $allowed_roles)) {
            return false;
        }

       /* if( !array_intersect($allowed_roles, $user->roles ) ) {
            return false;
        }*/





        return true;
    }

    public static function format_size($rawSize)
    {
        if ($rawSize / 1073741824 > 1) {
            return number_format_i18n($rawSize/1048576, 1) . ' '.__('GiB', 'FRocket_admin');
        } else if ($rawSize / 1048576 > 1) {
            return number_format_i18n($rawSize/1048576, 1) . ' '.__('MiB', 'FRocket_admin');
        } else if ($rawSize / 1024 > 1) {
            return number_format_i18n($rawSize/1024, 1) . ' '.__('KiB', 'FRocket_admin');
        } else {
            return number_format_i18n($rawSize, 0) . ' '.__('bytes', 'FRocket_admin');
        }
    }


    /*
     * Restore files
     */
    public static function unzipFiles($source, $destination)
    {
        if (extension_loaded('zip') === true) {
            if (file_exists($source) === true) {
                $zip = new ZipArchive();
                $res = $zip->open($source);
                if ($res === true) {
                    $zip->extractTo($destination);
                    $zip->close();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }
}
