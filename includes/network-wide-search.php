<?php

namespace Pagansites\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . '/get-index-table-name.php';


/**
 * PaganSites Search Box
 *
 * Displays the network-wide search box on any page that displays the
 * [pagansites-search] shortcode. Certified for use on main site only.
 *
 * @return string
 *
 * @author Ken Kitchen kenn@kmd.enterprises
 * @author KMD Enterprises, LLC
 * @package PaganSites
 */
function pagansites_search_box() {
	$html_output = '<div class="ps-search-container">';
	$html_output .= '<form action="' . admin_url('admin-post.php') . '" method="POST">';
	$html_output .= '<input type="hidden" name="action" value="search_response">';
	$html_output .= '<input type="text" placeholder="Search all PlanetPagan network posts..." name="search">';
	$html_output .= '<button type="submit">Search</button>';
	$html_output .= '</form>';
	$html_output .= '</div>';

	return $html_output;

}
add_shortcode('pagansites-search', __NAMESPACE__.'\\pagansites_search_box');


/**
 * Do Search Response
 *
 * Form associated with [pagansites-search] shortcode sends its content to
 * this function. This function grabs the data from the search field and
 * then redirects to required page search-results (page currently needs to
 * be added manually).
 *
 * @author Ken Kitchen kenn@kmd.enterprises
 * @author KMD Enterprises, LLC
 * @package PaganSites
 */
function do_search_response() {

	if ((isset($_POST["search"])) || (!empty($_POST["search"]))) {
		$search_value = sanitize_text_field($_POST["search"]);
	}

	$redirect_url = '/search-results/?search=' . $search_value;

	wp_redirect($redirect_url);
	exit;

}
add_action( 'admin_post_nopriv_search_response', __NAMESPACE__ . '\\do_search_response' );
add_action( 'admin_post_search_response', __NAMESPACE__ . '\\do_search_response' );


/**
 * PaganSites Search Results
 *
 * Perform a database search based on the search term provided and
 * display the results on the search-results page.
 *
 * @author Ken Kitchen kenn@kmd.enterprises
 * @author KMD Enterprises, LLC
 * @package PaganSites
 */
function pagansites_search_results() {
	global $wpdb;

	if (isset($_GET['search'])) {
		$search_value = $_GET['search'];
	} // todo: handle condition when search box is empty

	// check for saved transient search
	$transient_name = 'pagansites_search_' . str_replace(' ', '_', $search_value);
	$transient_result = get_transient($transient_name);

	if ( false === $transient_result ) {
		$index_table = get_index_table_name();

		$query = "SELECT * FROM " . $index_table . " WHERE MATCH(post_index) " .
		         "AGAINST('" . $search_value . "')";

		$search_results = $wpdb->get_results($query);

		if ($search_results) {
			$cached_results = serialize($search_results);
			$return_code = set_transient($transient_name, $cached_results, 900 );
		}
	} else {
		$search_results = unserialize($transient_result);
	}

	// todo: better formatting of output
	$html_output = '<div>';

	if ($search_results) {
		$html_output .= '<h3>Results:</h3>';

		foreach ($search_results as $search_result) {
			switch_to_blog($search_result->blog_id);
			$site_title = get_bloginfo( 'name' );
			restore_current_blog();

			$html_output .= '<div class="ps-post-container"><h4>Post "' . $search_result->post_title . '" from site "' . $site_title . '"</h4>';
			$html_output .= '<p>' . return_formatted_result($search_value, $search_result->post_body) . '</p>';
			$html_output .= '<h5><a href="' . $search_result->post_guid . '">Original Post</a></h5>';
			$html_output .= '</div><hr />';
		}
	} else {
		$html_output .= '<h5>No results found.</h5>';
	}

	$html_output .= '</div>';

	return $html_output;

}
add_shortcode('pagansites-search-results', __NAMESPACE__.'\\pagansites_search_results');

function return_formatted_result($search_value, $post_body) {
	// find location of first entry
	$first_find = strpos(strtolower($post_body), strtolower($search_value));

	if (!$first_find) {
		$result = "Found result but unable to show preview.";
	} else {
		// set $result so we can use append everywhere
		$result = "";

		// determine half of the total post size
		$half_value = ((strlen($post_body) - strlen($search_value)) / 2);

		// default to 40 if half is more
		if ($half_value > 40) {
			$half_value = 40;
		}

		// count back to a good starting point for the return string
		$start_postition = $first_find - $half_value;
		// if less than 0, set to 0
		// if not, we'll put in the starting "..."
		if ($start_postition < 0) {
			$start_postition = 0;
		} else {
			$result .= '...';
		}

		// determine the total length of characters to return
		$overall_length = $half_value + strlen($search_value) + $half_value;

		// get the return value
		$post_subset = substr($post_body, $start_postition, $overall_length);

		if ($overall_length > strlen($post_body)) {
			$result .= $post_subset;
		} else {
			$result .= $post_subset . '...';
		}

	}

	return $result;
}
