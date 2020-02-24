<?php

namespace Pagansites\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Activate PaganSites Plugin
 *
 * On plugin activation, this creates the custom table used by the
 * search/cross-referencing index.
 *
 * @global $wpdb
 *
 * @author Ken Kitchen kenn@kmd.enterprises
 * @author KMD Enterprises, LLC
 * @package PaganSites
 */
function activate_pagansites_plugin() {

	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . "pagansites_post_index";

	$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
		    id INT AUTO_INCREMENT PRIMARY KEY,
		    blog_id INT NOT NULL,
		    post_id INT NOT NULL,
		    user_id INT NOT NULL,
		    post_title VARCHAR(255) NOT NULL,
		    post_slug VARCHAR(255) NOT NULL,
		    post_excerpt TEXT,
		    post_index TEXT NOT NULL,
		    post_body TEXT NOT NULL,
		    post_guid VARCHAR(255) NOT NULL,
		    post_created DATETIME,
		    post_modified DATETIME,
    		INDEX ndx_post_title (post_title),
    		INDEX ndx_post_slug (post_slug(255)),
    		FULLTEXT INDEX ftndx_post_body (post_index(20480))
        ) ENGINE=INNODB;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);

}
register_activation_hook( __FILE__, __NAMESPACE__.'\\activate_pagansites_plugin');
