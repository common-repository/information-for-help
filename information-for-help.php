<?php
/*
Plugin Name: Information for Help
Description: It gives you all the useful information you should share for asking help about your website
Author: Jose Mortellaro
Author URI: https://josemortellaro.com/
Text Domain: wh
Domain Path: /languages/
Version: 0.0.3
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( is_admin() ){
	define( 'EOS_WH_PLUGIN_DIR',untrailingslashit( dirname( __FILE__ ) ) );
	require EOS_WH_PLUGIN_DIR.'/class.admin.system.report.php';
	add_filter( 'plugin_action_links_'.untrailingslashit( plugin_basename( __FILE__ ) ), 'eos_wh_plugin_add_settings_link' );
	add_action( 'admin_menu', 'eos_wh_menu_pages',90 );
	add_action( 'admin_enqueue_scripts','eos_wh_enqueue_scripts' );
}
//Filter function to read plugin translation files
function eos_wh_load_translation_file( $mofile, $domain ) {
	if ( 'wh' === $domain ) {
		$loc = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$mofile = EOS_WH_PLUGIN_DIR . '/languages/wh-' . $loc . '.mo';
	}
	return $mofile;
}
function eos_wh_menu_pages(){
	add_menu_page( __( 'Information for Help','wh' ),__( 'Information for Help','wh' ), 'manage_options', 'eos-wh', 'eos_wh_support_page_callback','dashicons-tickets',2 );
}	
//Generate the Status page
function eos_wh_support_page_callback(){
	Eos_WH_Admin_System_Report::output();
}
//Enqueue the needed admin script
function eos_wh_enqueue_scripts(){
	wp_enqueue_script( 'eos-wh',untrailingslashit( plugins_url( '', __FILE__ ) ).'/assets/js/wh.js',array( 'jquery' ) );
}
//It adds a link to the action links in the plugins page
function eos_wh_plugin_add_settings_link( $links ){
    $settings_link = '<a class="eos-wh-setts" href="'.admin_url( 'admin.php?page=eos-wh' ).'">'. __( 'Get Info for help','eos-rc' ). '</a>';
    $settings_link .= ' | <a class="eos-wh-setts" target="_blank" rel="noopener" href="https://wordpress.org/support/plugin/information-for-help/">'. __( 'Support','eos-rc' ). '</a>';
    array_push( $links, $settings_link );
  	return $links;	
}