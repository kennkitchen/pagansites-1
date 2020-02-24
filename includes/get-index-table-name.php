<?php

namespace Pagansites\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function get_index_table_name() {
	global $wpdb;

	switch_to_blog(1);
	$index_table = $wpdb->prefix . 'pagansites_post_index';
	restore_current_blog();

	return $index_table;
}
