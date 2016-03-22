<?php defined('ABSPATH') or die('-1');

global $wpdb;

if (!defined('GALLERYPROPLUS_BASENAME'))        define('GALLERYPROPLUS_BASENAME', plugin_basename(__FILE__));
if (!defined('GALLERYPROPLUS_DEBUG_HOST'))      define('GALLERYPROPLUS_DEBUG_HOST', '192.168.1.204');
if (!defined('GALLERYPROPLUS_DEBUG_PORT'))      define('GALLERYPROPLUS_DEBUG_PORT', 7777);
if (!defined('GALLERYPROPLUS_DIR'))             define('GALLERYPROPLUS_DIR', untrailingslashit(dirname(__FILE__)));
if (!defined('GALLERYPROPLUS_NAME'))            define('GALLERYPROPLUS_NAME', 'Gallery Pro Plus');
if (!defined('GALLERYPROPLUS_PATH'))            define('GALLERYPROPLUS_PATH', 'galleries');
if (!defined('GALLERYPROPLUS_TYPE'))            define('GALLERYPROPLUS_TYPE', 'galleryproplus');
if (!defined('GALLERYPROPLUS_TABLE_REQUESTS'))  define('GALLERYPROPLUS_TABLE_REQUESTS', $wpdb->prefix . GALLERYPROPLUS_TYPE .'_requests');
if (!defined('GALLERYPROPLUS_URL'))             define('GALLERYPROPLUS_URL', untrailingslashit(plugins_url('',__FILE__)));
