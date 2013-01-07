<?php
/*
Plugin Name: Wordpress Ad Manager
Plugin URI: http://geardev.de/codecanyon/ad-manager.php
Description: Powerful Ad Manager for Wordpress.
Version: 1.0
Author: GearDev
Author URI: http://geardev.de
*/

require 'adManager.class.php'; // Load the base class

// Wordpress stuff
add_action( 'admin_menu', array( 'adManager', 'add_admin_menu' ) );
add_action( 'widgets_init', array( 'adManager', 'register_widget' ) );

add_filter( 'the_content', array( 'adManager', 'filter_content' ) );

// Make the upload panel work
if( isset( $_GET['page'] ) && ( $_GET['page'] == 'ad-manager-new' || $_GET['page'] == 'ad-manager' ) ) {
	add_action('admin_print_scripts', array( 'adManager', 'admin_upload_scripts' ) );
	add_action('admin_print_styles', array( 'adManager', 'admin_styles' ) );
}

// Make the AJAX search work
if( isset( $_GET['page'] ) && ( $_GET['page'] == 'ad-manager-zone-new' || $_GET['page'] == 'ad-manager-zones' ) ) {
	add_action( 'admin_print_scripts', array( 'adManager', 'admin_zone_scripts' ) );
	add_action( 'admin_print_styles', array( 'adManager', 'admin_styles' ) );
}