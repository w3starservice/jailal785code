<?php
/**
 * @package Xtendify
 */
/*
Plugin Name: Fornt End Post Submission
Plugin URI: https://xtendify.com/
Description: User submit post from fornt end
Version: 1.0.0
Author: Xtendify
Author URI: https://xtendify.com/
License: GPLv2 or later
Text Domain: xtendify
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin version.
if ( ! defined( 'WPFEPS_VERSION' ) ) {
	define( 'WPFEPS_VERSION', '0.0.1' );
}

// Plugin Folder Path.
if ( ! defined( 'WPFEPS_PLUGIN_DIR' ) ) {
	define( 'WPFEPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL.
if ( ! defined( 'WPFEPS_PLUGIN_URL' ) ) {
	define( 'WPFEPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin Root File.
if ( ! defined( 'WPFEPS_PLUGIN_FILE' ) ) {
	define( 'WPFEPS_PLUGIN_FILE', __FILE__ );
}





require_once WPFEPS_PLUGIN_DIR . 'includes/functions.php';



?>