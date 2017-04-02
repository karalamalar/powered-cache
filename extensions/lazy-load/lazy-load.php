<?php
/**
 * Plugin Name: Lazy Load
 * Plugin URI: https://poweredcache.com/extensions/lazy-load
 * Description: Loads images and iframes only when visible to the user.
 * Author: Powered Cache Team
 * Version: 1.0
 * Author URI: https://poweredcache.com
 * Plugin Image: extension-image.png
 * License: GPLv2 (or later)
 * This extension based on Bjørn Johansen's lazy load plugin
 *
 * @see https://wordpress.org/plugins/bj-lazy-load/
 * If you need stand-alone solution, highly recommended that.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PC_LAZY_LOAD_DIR' ) ) {
	define( 'PC_LAZY_LOAD_DIR', plugin_dir_path( __FILE__ ) );
}

require_once 'inc/class-pc-lazy-load.php';
new PC_Lazy_Load();

if ( is_admin() ) {
	require_once 'inc/class-pc-lazy-load-admin.php';
	PC_Lazy_Load_Admin::factory();
}

// make description translatable
__( 'Loads images and iframes only when visible to the user.', 'powered-cache' );
