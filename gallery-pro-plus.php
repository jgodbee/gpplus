<?php defined('ABSPATH') or die('-1');

/**
 * Plugin Name: Gallery Pro Plus
 * Plugin URI: http://www.galleryproplus.com
 * Description: Create beautiful photo galleries.
 * Version: 1.0.0
 * Author: Gallery Pro Plus
 * Author URI: http://www.galleryproplus.com
 * License: Copyright (c) 2015 Gallery Pro Plus.
 */

//ini_set('display_errors','1');

require_once(dirname(__FILE__).'/constants.php');
require_once(dirname(__FILE__).'/classes.php');
require_once(dirname(__FILE__).'/dependencies.php');
require_once(dirname(__FILE__).'/functions.php');

register_activation_hook(__FILE__, 'galleryproplus_activate');
//register_deactivation_hook(__FILE__, 'galleryproplus_deactivate');
add_action('admin_head', 'galleryproplus_admin_head');
add_action('admin_init', 'galleryproplus_admin_init');
add_action('admin_menu', 'galleryproplus_admin_menu');
add_action('init', 'galleryproplus_init');
add_action('manage_galleryproplus_posts_columns', 'galleryproplus_columns', 10, 2);
add_action('manage_galleryproplus_posts_custom_column', 'galleryproplus_custom_column', 11, 2);
add_action('parse_request', 'galleryproplus_parse_request');
add_action('pre_get_posts', 'galleryproplus_pre_get_posts');
//add_action('plugins_loaded', 'galleryproplus_plugins_loaded');
//add_action('wp_enqueue_scripts', 'galleryproplus_wp_enqueue_scripts');
