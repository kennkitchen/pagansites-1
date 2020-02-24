<?php

namespace Pagansites\Core;

function get_current_permalink($blog_id, $post_id) {
	switch_to_blog($blog_id);
	$current_permalink = get_permalink($post_id);
	restore_current_blog();

	return $current_permalink;
}
