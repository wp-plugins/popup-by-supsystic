<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('PPS_WPLANG', 'en_GB');
    } else {
        define('PPS_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('PPS_PLUG_NAME', basename(dirname(__FILE__)));
    define('PPS_DIR', WP_PLUGIN_DIR. DS. PPS_PLUG_NAME. DS);
    define('PPS_TPL_DIR', PPS_DIR. 'tpl'. DS);
    define('PPS_CLASSES_DIR', PPS_DIR. 'classes'. DS);
    define('PPS_TABLES_DIR', PPS_CLASSES_DIR. 'tables'. DS);
	define('PPS_HELPERS_DIR', PPS_CLASSES_DIR. 'helpers'. DS);
    define('PPS_LANG_DIR', PPS_DIR. 'lang'. DS);
    define('PPS_IMG_DIR', PPS_DIR. 'img'. DS);
    define('PPS_TEMPLATES_DIR', PPS_DIR. 'templates'. DS);
    define('PPS_MODULES_DIR', PPS_DIR. 'modules'. DS);
    define('PPS_FILES_DIR', PPS_DIR. 'files'. DS);
    define('PPS_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('PPS_SITE_URL', get_bloginfo('wpurl'). '/');
    define('PPS_JS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/js/');
    define('PPS_CSS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/css/');
    define('PPS_IMG_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/img/');
    define('PPS_MODULES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/modules/');
    define('PPS_TEMPLATES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/templates/');
    define('PPS_JS_DIR', PPS_DIR. 'js/');

    define('PPS_URL', PPS_SITE_URL);

    define('PPS_LOADER_IMG', PPS_IMG_PATH. 'loading.gif');
	define('PPS_TIME_FORMAT', 'H:i:s');
    define('PPS_DATE_DL', '/');
    define('PPS_DATE_FORMAT', 'm/d/Y');
    define('PPS_DATE_FORMAT_HIS', 'm/d/Y ('. PPS_TIME_FORMAT. ')');
    define('PPS_DATE_FORMAT_JS', 'mm/dd/yy');
    define('PPS_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('PPS_WPDB_PREF', $wpdb->prefix);
    define('PPS_DB_PREF', 'pps_');
    define('PPS_MAIN_FILE', 'pps.php');

    define('PPS_DEFAULT', 'default');
    define('PPS_CURRENT', 'current');
	
	define('PPS_EOL', "\n");    
    
    define('PPS_PLUGIN_INSTALLED', true);
    define('PPS_VERSION', '1.1.3');
    define('PPS_USER', 'user');
    
    define('PPS_CLASS_PREFIX', 'ppsc');     
    define('PPS_FREE_VERSION', false);
	define('PPS_TEST_MODE', true);
    
    define('PPS_SUCCESS', 'Success');
    define('PPS_FAILED', 'Failed');
	define('PPS_ERRORS', 'ppsErrors');
	
	define('PPS_ADMIN',	'admin');
	define('PPS_LOGGED','logged');
	define('PPS_GUEST',	'guest');
	
	define('PPS_ALL',		'all');
	
	define('PPS_METHODS',		'methods');
	define('PPS_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('PPS_CODE', 'pps');

	define('PPS_LANG_CODE', 'pps_lng');
	/**
	 * Plugin name
	 */
	define('PPS_WP_PLUGIN_NAME', 'PopUp by Supsystic');
	/**
	 * Custom defined for plugin
	 */
	define('PPS_COMMON', 'common');
	define('PPS_FB_LIKE', 'fb_like');
	define('PPS_VIDEO', 'video');
	define('PPS_SHORTCODE_CLICK', 'supsystic-show-popup');
