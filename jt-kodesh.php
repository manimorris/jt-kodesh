<?php
/*
Plugin Name: Jewtech Shabbat and Kodesh days Plugin
Plugin URI: https://www.jewtech.co.il"
Description: This plugin will help the website owner to keep Shabbat and Jewish holidays by closing the website during the 'Kodesh' days.
Author: Cohen David, Morris Mani.
Contributors: mamprog.
Version: 1.0
License: GPLv2 or later

Text Domain: Jt-Kodesh
Domain Path: /languages

Requires at least: 4.6
*/

if ( !defined('ABSPATH') ) { 
    die;
}

// Main functions for this Plugin
include 'jt-kodesh-functions.php';


add_action("init", "jt_kodesh");

/** INIT function */
function jt_kodesh() {
	
	if( !get_next_kodesh_option() ) {
		add_next_kodesh_option();
	}

	$next_kodesh_option = get_next_kodesh_option();

	$next_s_time = $next_kodesh_option["next_kodeshDay_starts"];
	$next_e_time = $next_kodesh_option["next_kodeshDay_ends"];
	$now = time();

	if($now < $next_s_time) {

		/**
		 * If the time to check didn't arrive:
		 * exit the checking proccess.
		 */
		return;

	} elseif ($now > $next_e_time) {

		/**
		 * If the time to check already passed:
		 * the 'next_kodesh' option should be updated 
		 */
		update_next_kodesh_option();

		// Go over the checking process from begining
		$next_kodesh_option = get_next_kodesh_option();
		$next_s_time = $next_kodesh_option["next_kodeshDay_starts"];

		if($now < $next_s_time){

			return;
		}
	}
	

	//** From here and on, will work only if now is in the checking time range. */
	
	// Here we get back the instruction whether to pop the jt-Kodesh blocker 
	$kodesh_status = get_client_kodeshDay_status();


	if( $kodesh_status == "KODESH") {

		pop_site_blocker($kodesh_status);
	}
	
}



//** Pop's the site blocker on Kodesh days. */
function pop_site_blocker($msgArr) {
	
	//
	include_once "src/jt-kodesh-blockPage.php";
}



/**
 * PLUGIN SETTINGS .
 */
#region PLUGINS Settings.

/**
 *  Create default values when user activates plugin.
 */
function jt_kodesh_activate() {
	
	remove_next_kodesh_option();
	add_next_kodesh_option();			
}

register_activation_hook( __FILE__, 'jt_kodesh_activate' );


/** 
 * Remove all plugin's data from option when user deactivates plugin 
**/ 
function jt_kodesh_deactivate() {
			
	remove_next_kodesh_option();	
}

register_deactivation_hook( __FILE__, 'jt_kodesh_activate' );



/** 
 * Create the setting page 
 **/

// Setting the page content
function jt_kodesh_option_page() {
	
	include "jt-kodesh-settingPage.php";
}

// Create the page
function jt_settings_page() {
	add_options_page('Jewtech Kodesh plugin settings', 'jt-kodesh settings', 'manage_options', 'jt_kodesh', 'jt_kodesh_option_page');
  }

// Add the settings page actoin
add_action('admin_menu', 'jt_settings_page');


/** Set a link to the settings page from the plugin page */
function plugin_settings_link($links) { 

	$settings_link = '<a href="options-general.php?page=jt_kodesh">Settings</a>'; 
	array_unshift($links, $settings_link); 
	
	return $links; 
  }

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'plugin_settings_link' );



/** Loading Translation text domain */
function jt_kodesh_load_textdomain() {
	
	load_plugin_textdomain( 'Jt-Kodesh', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action('plugins_loaded', 'jt_kodesh_load_textdomain');



/**Adding test short code (needed?)*/
function test_shortcode() {

	$testArray = array(
		"day_name" => "Testing",
		"ends" => strtotime("+ 1 minute", time())
	);
	pop_site_blocker($testArray);
}
add_shortcode('jtk-test', 'test_shortcode');



#endregion

  

?>