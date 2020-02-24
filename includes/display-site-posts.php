<?php

namespace Pagansites\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . '/get-current-permalink.php';

/**
 * PaganSites Post Display
 *
 * Description goes here
 *
 * @param string $atts  List of shortcode attributes
 * @return string
 *
 * @global $wpdb
 *
 * @author Ken Kitchen kenn@kmd.enterprises
 * @author KMD Enterprises, LLC
 * @package PaganSites
 */
function pagansites_post_display($atts) {
	global $wpdb;

	$shortcode_atts = shortcode_atts( array(
		"limit" => '',
		"imgsize" => '',
        "display" => ''
	), $atts );

//	if ((isset($shortcode_atts['limit'])) && (is_numeric($shortcode_atts['limit']))) {
//		$display_limit = (int)$shortcode_atts['limit'];
//	} else {
		$display_limit = 12;
//	}

//    if ((isset($shortcode_atts['display'])) && (is_numeric($shortcode_atts['display']))) {
//        $display_columns = $shortcode_atts['display'];
//    } else {
        $display_columns = 4;
//    }

	$valid_image_sizes = array('medium', 'medium-large', 'large');

	if ((isset($shortcode_atts['imgsize'])) && (in_array($shortcode_atts['imgsize'], $valid_image_sizes))) {
		$display_img_size = $shortcode_atts['imgsize'];
	} else {
		$display_img_size = 'medium';
	}

	$current_blog_id = get_current_blog_id();

	// check transients first
	$transient_result = get_transient( 'pagansites_all_posts' );

	if ( false === $transient_result ) {
		$all_blogs = $wpdb->get_results(
			'SELECT * FROM ' . $wpdb->blogs .
			' WHERE blog_id != ' . $current_blog_id .
			' AND public = 1 AND archived = 0 AND spam = 0 AND deleted = 0');

		$posts_array = array();

		foreach ($all_blogs as $a_blog) {
			$prefix = $wpdb->prefix . $a_blog->blog_id . '_';

			$query = "
SELECT (" . $a_blog->blog_id . ") AS blog_id,
(SELECT op.option_value FROM " . $prefix . "options op WHERE op.option_name = 'home') AS home,
(SELECT op.option_value FROM " . $prefix . "options op WHERE op.option_name = 'blogname') AS blogname,
(SELECT op.option_value FROM " . $prefix . "options op WHERE op.option_name = 'admin_email') AS admin_email,
p.ID, p.post_title, p.post_name, p.post_excerpt, p.post_content, p.post_date, p.guid, (SELECT u.user_nicename FROM " . $wpdb->prefix ."users u WHERE u.ID = p.post_author) AS post_author,
(SELECT pm.meta_value FROM " . $prefix . "postmeta pm WHERE pm.post_id = p.id AND pm.meta_key = '_thumbnail_id' LIMIT 1) AS thumbnail_id
FROM " . $prefix . "posts p
WHERE p.post_status = 'publish'
AND p.post_type = 'post'";

			$blog_posts = $wpdb->get_results($query);

			$posts_array = array_merge($posts_array, $blog_posts);
		}

		usort($posts_array, function($a, $b) {
			if($a->post_date == $b->post_date){ return 0 ; }
			return ($a->post_date > $b->post_date) ? -1 : 1;
		});

		$cached_posts = serialize($posts_array);
		$return_code = set_transient( 'pagansites_all_posts', $cached_posts, 900 );

	} else {
		$posts_array = unserialize($transient_result);
	}



    /* display posts */
    $displayed = 0;
    $column_count = 0;

    $number_of_posts = count($posts_array);
    $number_of_pages = round($number_of_posts / $display_limit);


    $html_output = '<div class="ps-post-container">';

    foreach ($posts_array as $post) {

        if ($column_count == 0) {
            $html_output .= '<div class="rposts-row">';
        }


        $html_output .= '<div class="rposts-column">';

        /* do one post */
        $html_output .= '<div class="ps-card">';

        if ($post->thumbnail_id) {
            switch_to_blog($post->blog_id);
            $post_thumbnail = get_the_post_thumbnail($post->ID, $display_img_size);
            restore_current_blog();
            $html_output .= $post_thumbnail;
        } else {
            $post_thumbnail = null;
        }

        $html_output .= '<div class="ps-card-container">';
        $html_output .= '<br />';
        $post_permalink = get_current_permalink($post->blog_id, $post->ID);

        $html_output .= '<h3><a href="' . $post_permalink . '">' . $post->post_title . '</a></h3>';

        $html_output .= '<h5>Posted by ' . $post->post_author . ' on '
            . $post->post_date . ' <em> (posted on <a href="' . $post->home . '">' . $post->blogname
            . '</a>)</em>' . '</h5>';

        if ($post->post_excerpt) {
            $post_output = (strlen($post->post_excerpt) > 303) ? substr($post->post_excerpt,0,300) . '...' : $post->post_excerpt;
        } else {
            $post_output = (strlen($post->post_content) > 303) ? substr($post->post_content,0,300) . '...' : $post->post_content;
        }

        $html_output .= '<p>' . strip_tags($post_output) . '</p>';
        $html_output .= '<p><a href="' . $post_permalink . '">Read More</a></p>';

        $html_output .= '</div> <!-- ps-card-container -->';

        $html_output .= '</div> <!-- ps-card -->';

        $html_output .= '</div> <!-- rposts-column -->';
        /* end one post */

        if ($column_count == ($display_columns - 1)) {
            $html_output .= '</div> <!-- rposts-row -->';
            $html_output .= '<br />';
            $column_count = 0;
        } else {
            $column_count++;
        }

        $displayed++;
        if ($displayed >= $display_limit) {
            break;
        }
    }

    //$html_output .= '</div> <!-- rposts-row -->';
    $html_output .= '</div> <!-- ps-post-container -->';
	/* end display */

    return $html_output;

}
add_shortcode('pagansites-posts', __NAMESPACE__.'\\pagansites_post_display');

function odd_even($number_to_check) {
    if (($number_to_check == 0) || ($number_to_check % 2 == 0)) {
        return 'even';
    } else {
        return 'odd';
    }
}
