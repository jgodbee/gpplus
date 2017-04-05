<?php defined('ABSPATH') or die('-1');

/**
 * Plugin Name: Gallery Pro Plus
 * Plugin URI: http://www.galleryproplus.com
 * Description: Create beautiful photo galleries.
 * Version: 1.0.0
 * Author: Gallery Pro Plus
 * Author URI: http://www.galleryproplus.com
 * License: Copyright (c) 2015-2017 Gallery Pro Plus.
 */

ini_set('display_errors','1');

require_once(dirname(__FILE__).'/constants.php');
require_once(dirname(__FILE__).'/classes.php');
require_once(dirname(__FILE__).'/dependencies.php');

$gpp = new GalleryProPlus();

register_activation_hook(__FILE__, array($gpp, 'activate'));
//register_deactivation_hook(__FILE__, array($gpp, 'deactivate'));

if (is_admin())
{
	add_action('admin_enqueue_scripts', array($gpp, 'admin_enqueue_scripts'));
	add_action('admin_head', array($gpp, 'admin_head'));
	add_action('admin_init', array($gpp, 'admin_init'));
	add_action('admin_menu', array($gpp, 'admin_menu'));
	add_action('before_delete_post', array($gpp, 'before_delete_post'));
	add_action('init', array($gpp, 'init'));
	add_action('manage_galleryproplus_posts_columns', array($gpp, 'columns'), 10, 2);
	add_action('manage_galleryproplus_posts_custom_column', array($gpp, 'custom_column'), 11, 2);
	add_action('pre_get_posts', array($gpp, 'pre_get_posts'));
	//add_action('plugins_loaded', array($gpp, 'plugins_loaded'));
	//add_action('wp_enqueue_scripts', array($gpp, 'wp_enqueue_scripts'));
}
else
{
	add_action('parse_request', array($gpp, 'parse_request'));
}
