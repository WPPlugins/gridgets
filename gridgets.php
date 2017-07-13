<?php
/*
Plugin Name: Gridgets
Plugin URI: http://catapultthemes.com/gridgets/
Description: Grids for Widgets
Version: 1.2.2
Author: Catapult Themes
Author URI: http://catapultthemes.com/
Text Domain: gridgets
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gridgets_load_plugin_textdomain() {
    load_plugin_textdomain( 'gridgets', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'gridgets_load_plugin_textdomain' );

/**
 * Define constants
 **/
if ( ! defined( 'GRIDGETS_PLUGIN_URL' ) ) {
	define( 'GRIDGETS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( is_admin() ) {
	require_once dirname( __FILE__ ) . '/admin/class-gridgets-admin.php';
	require_once dirname( __FILE__ ) . '/admin/class-gridgets-metaboxes.php';
	require_once dirname( __FILE__ ) . '/admin/metaboxes.php';		
	// Admin
	$Gridgets_Admin = new Gridgets_Admin();
	$Gridgets_Admin -> init();
	// Our metaboxes
	$metaboxes = gridgets_metaboxes();
	$Gridgets_Metaboxes = new Gridgets_Metaboxes( $metaboxes );
	$Gridgets_Metaboxes -> init();
}

require_once dirname( __FILE__ ) . '/public/class-register-gridgets.php';
require_once dirname( __FILE__ ) . '/public/class-gridgets-public.php';
require_once dirname( __FILE__ ) . '/widgets/class-widget-gridget-styles.php';

$Register_Gridgets = new Register_Gridgets();
$Register_Gridgets -> init();
$Gridgets_Public = new Gridgets_Public();
$Gridgets_Public -> init();