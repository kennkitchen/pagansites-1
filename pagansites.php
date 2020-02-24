<?php
/*
Plugin Name: PaganSites
Plugin URI: https://pagansites.net/
Description: The software that makes PaganSites, well... PaganSites.
Version: 1.0.0
Author: Kenn Kitchen
Author URI: https://kenneth.kitchen/
License: GPLv2
Tags:
Text Domain: pagansites
*/

namespace Pagansites\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


require_once plugin_dir_path( __FILE__ ) . '/includes/activate-deactivate.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/display-site-posts.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/display-network-footer.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/network-wide-index.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/network-wide-search.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/dashboard-widget.php';

function pagansites_wp_scripts() {
	wp_register_style('pagansites-css', plugins_url('pagansites') . '/public/css/pagansites.css');
	wp_enqueue_style('pagansites-css');

	wp_register_script( 'fontawesome-js', 'https://kit.fontawesome.com/8562599284.js', '', '', true );
	wp_enqueue_script( 'fontawesome-js' );
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\pagansites_wp_scripts');
