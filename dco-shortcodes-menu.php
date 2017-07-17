<?php

/*
  Plugin Name: DCO Shortcodes Menu
  Plugin URI: https://github.com/Denis-co/dco-shortcode-menu
  Description: Allows you to add shortcodes menu to the editor
  Version: 1.0.0
  Author: Denis co.
  Author URI: http://denisco.pro
  License: GPLv2 or later
  Text Domain: dco-sm
  Domain Path: /languages
 */

if (!defined('ABSPATH'))
    exit;

define('DCO_SM__PLUGIN_URL', plugin_dir_url(__FILE__));
define('DCO_SM__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DCO_SM__PLUGIN_BASENAME', plugin_basename(__FILE__));

if (is_admin()) {
    load_plugin_textdomain( 'dco-sm', false, plugin_basename( DCO_SM__PLUGIN_DIR ) . '/languages' );
    
    global $pagenow;
    require_once DCO_SM__PLUGIN_DIR . 'dco-sm-posttype.php';
    if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
        require_once DCO_SM__PLUGIN_DIR . 'dco-sm.php';
        require_once DCO_SM__PLUGIN_DIR . 'dco-sm-metaboxes.php';
    }
}