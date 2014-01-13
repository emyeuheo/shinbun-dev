<?php

//NOTICE: save me to UTF8 without BOM
define('VERSION', '2.5');
define('BASE_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('PARENT_PATH', dirname(BASE_PATH));
define('APP_PATH', BASE_PATH . 'app/');
define('VENDOR_PATH', APP_PATH . 'Vendor/');
define('MODEL_PATH', APP_PATH . 'Model/');
define('VIEW_PATH', APP_PATH . 'View/');
define('LIB_PATH', APP_PATH . 'Lib/');
define('PLUGIN_PATH', APP_PATH . 'Plugin/');
define('CONFIG_PATH', dirname(__FILE__) . '/');
define('WEBROOT_PATH', getcwd() . '/');
define('LANG_PATH', LIB_PATH . 'lang/');
define('LOG_PATH', APP_PATH . 'log/');
define('DIR_NAME', basename(BASE_PATH));

define('IMAGE_UPLOAD_PATH', WEBROOT_PATH . 'img/image/');

/* Import library */
App::import('Lib', 'GlobalVar');
App::import('Lib', 'UserAgentInfo');
App::uses('FB', 'Lib');
App::uses('Lang', 'Lib/lang');
App::import('Lib', 'Spiral');
App::import('Lib', 'Validator');
App::import('Lib', 'QuizLogic');
App::import('Lib', 'UranaiLogic');
App::import('Lib', 'OJMPHP');
App::import('Vendor', 'htmLawed');
App::import('Lib', 'bitly');



if ($_SERVER['HTTP_HOST'] == 'localhost') {
    define('SYSTEM_EVIRONMENT', 'localhost');
} else {
    if (strrpos(__FILE__, "/home/wannabies2/") !== false) {
        define('SYSTEM_EVIRONMENT', 'development'); // x10
    } else {
        define('SYSTEM_EVIRONMENT', 'production');
    }
}

function is_localhost() {
    return SYSTEM_EVIRONMENT == 'localhost';
}

function is_development() {
    return SYSTEM_EVIRONMENT == 'development';
}

function is_production() {
    return SYSTEM_EVIRONMENT == 'production';
}

date_default_timezone_set('Asia/Singapore');
ini_set('default_charset', 'UTF-8');
ini_set('display_errors', 0);
//ini_set("log_errors", "On");
//ini_set("error_log", LOG_PATH . "/error_" . date("Ymd") . ".log");
if (is_development()) {
    define('SETTINGS_FILE', CONFIG_PATH . 'settings_dev.dat');
    define('DEBUG_MODE', TRUE);
} else {
    define('SETTINGS_FILE', CONFIG_PATH . 'settings.dat');
    define('DEBUG_MODE', TRUE);
}


if (!file_exists(SETTINGS_FILE)) {
    $setting_ojm = new OJMPHP(SETTINGS_FILE, array(
        'fb_app_id' => '',
        'fb_app_secret' => '',
        'fb_info_fields' => '',
        'spiral_api_url' => 'admin',
        'spiral_transaction' => 'a56f5b18e181b3c85b9a040c0c9e645170279669',
        'spiral_token' => '',
        'spiral_token_secret' => '',
        'spiral_thanks_id' => '',
        'spiral_thanks_send' => FALSE,
    ));
    $setting_ojm->save();
} else {
    $setting_ojm = new OJMPHP(SETTINGS_FILE);
}
if ($_SERVER['SERVER_PORT'] == 80) {
    $protocol = 'http';
} else {
    $protocol = 'https';
}
$protocol = 'https'; //force use SSL

define('SERVER_NAME', $_SERVER['HTTP_HOST']);
define('APP_NAME', $setting_ojm->get('app_name'));
define('HTTP_PROTOCOL', $protocol);
define('CALLBACK_PROTOCOL', 'http'); //callback function must be predifined

define('ROOT_URL', HTTP_PROTOCOL . "://" . SERVER_NAME . "/" . DIR_NAME . "/");
define('ROOT_URL_CALLBACK', CALLBACK_PROTOCOL . "://" . SERVER_NAME . "/" . DIR_NAME . "/");
// define web root url
define('ADMIN_HOME_URL', HTTP_PROTOCOL . '://' . SERVER_NAME . '/' . DIR_NAME . '/admin/');
define('ADMIN_ROOT_URL', HTTP_PROTOCOL . '://' . SERVER_NAME . '/' . DIR_NAME . '/');
// Database config
define('DB_HOSTNAME', $setting_ojm->get('db_host'));
define('DB_USERNAME', $setting_ojm->get('db_user'));
define('DB_PASSWORD', $setting_ojm->get('db_password'));
define('DB_DATABASE', $setting_ojm->get('db_name'));

define('SPIRAL_API_URL', $setting_ojm->get('spiral_api_url'));
define('SPIRAL_TOKEN', $setting_ojm->get('spiral_token'));
define('SPIRAL_SECRET', $setting_ojm->get('spiral_token_secret'));
define('SPIRAL_DB_NAME', $setting_ojm->get('spiral_transaction'));

/* Thanks mail */
define('SPIRAL_DELIVERY_THANKS_ID', '1');
define('SPIRAL_DELIVERY_THANKS_RULE_ID', $setting_ojm->get('spiral_thanks_id'));
define('SEND_THANKYOU', $setting_ojm->get('spiral_thanks_send'));
define('FB_APP_NAME', $setting_ojm->get('fb_app_name'));
define('FB_APP_ID', $setting_ojm->get('fb_app_id'));
define('FB_APP_KEY', $setting_ojm->get('fb_app_secret'));
define('FB_PAGE_USERNAME', $setting_ojm->get('fb_page_username'));
define('FB_PAGE_NAME', $setting_ojm->get('fb_page_name'));
define('PAGENAME_HTML', $setting_ojm->get('page_name_html'));
define('FB_FAN_PAGE_ID', $setting_ojm->get('fb_page_id'));
define('CALLBACK_URI', ROOT_URL_CALLBACK . 'callback/');
define('FB_FIELDS', $setting_ojm->get('fb_info_fields'));
define('FB_PERMISSIONS', $setting_ojm->get('fb_permission'));
define('ROW_PER_PAGE', $setting_ojm->get('row_per_page'));
define('GA_CODE', $setting_ojm->get('google_analytics'));
define('DEFAULT_LANG', 'ja');
define('RETURN_URL', HTTP_PROTOCOL . "://" . SERVER_NAME . $_SERVER["REQUEST_URI"]);

define('BITLY_KEY', 'bc766f8aeff0890f88bd5bf68070d0ae7bbdd8ff');
define('BITLY_OAUTH_API', 'https://api-ssl.bit.ly/v3/');

define('TERMS_LINK', $setting_ojm->get('terms_link'));
define('PRIVACY_LINK', $setting_ojm->get('privacy_link'));
define('ADS_LINK', $setting_ojm->get('ads_link'));
define('COMPANY_LINK', $setting_ojm->get('company_link'));
define('DFP_PC_INDEX', $setting_ojm->get('dfp_pc_index'));
define('DFP_PC_RESULT', $setting_ojm->get('dfp_pc_result'));
define('DFP_PC_QUIZ_LEFT', $setting_ojm->get('dfp_pc_quiz_left'));
define('DFP_PC_QUIZ_RIGHT', $setting_ojm->get('dfp_pc_quiz_right'));
define('DFP_SP_INDEX', $setting_ojm->get('dfp_sp_index'));
define('DFP_SP_QUIZ', $setting_ojm->get('dfp_sp_quiz'));
define('DFP_SP_RESULT', $setting_ojm->get('dfp_sp_result'));
define('DFP_OVERLAY', $setting_ojm->get('dfp_overlay'));

if (is_development()) { // development
    $admin_access_list_arr = array(
        'admin' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
        'anhnn' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
        'kiendt' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
        'hungnt' => '7c4a8d09ca3762af61e59520943dc26494f8941b',
    );
    GlobalVar::set_admin_acl($admin_access_list_arr);
} else {
    $admin_access_list_arr = array(
        'admin' => 'a56f5b18e181b3c85b9a040c0c9e645170279669', // honemsaydam
        'wannabies' => 'c88f995434e0a3b980326ffb77b9af49467af540', // seibannaw13
    );
    GlobalVar::set_admin_acl($admin_access_list_arr);
}

define('SESSION_USER', 'session_user');
define('SESSION_USER_ID', 'session_user_id');



/*
  USAGE:
  $html_mod = htmLawed($html_org, GlobalVar::get_html_filter());
 */

GlobalVar::set_html_filter(array(
    'keep_bad' => 0,
    'safe' => 1,
    'balance' => 1,
    'comment' => 0,
    'css_expression' => 1,
    'style_pass' => 1,
    'cdata' => 1,
    'make_tag_strict' => 0,
    //'deny_attribute' => '* -title -href -src -style -height -width -target',
    'elements' => 'a, img, p, span, div, strong',
));

GlobalVar::write('spiral_field_list', array(
    'record_reg_date' => array('label' => 'TRDB_登録日時', 'update' => true), //0, mandatory, added by tuyennq
    'mail' => array('label' => 'メールアドレス', 'update' => true),
    'fb_username' => array('label' => 'facebook ユーザー名', 'update' => true), //2
    'fb_name' => array('label' => '名前-facebook', 'update' => true), //3
    'reg_date' => array('label' => '登録年月日（○年○月○日）', 'update' => true), //4
    'residence' => array('label' => '居住地(1024 bytes)', 'update' => true), //5
    'birthday' => array('label' => '生年月日（○年○月○日）', 'update' => true), //6
    'gender' => array('label' => '性別', 'update' => true), //7
    'optin_gate' => array('label' => '流入元管理', 'update' => true, 'value' => '14'), //shindangame
));

GlobalVar::set_admin_ip(array(
    'codelovers.vn' => '117.6.79.75'
));



GlobalVar::set_sidebar_nav(array(
   array('type' => 'nav_header', 'title' => 'ツール'),
    array('type' => 'nav_link', 'title' => '・通知', 'url' => ROOT_URL . 'admin/notification'),
    array('type' => 'nav_link', 'title' => '・システム設定', 'url' => ROOT_URL . 'admin/settings'),
));



