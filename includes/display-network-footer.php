<?php

namespace Pagansites\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function pagansites_footer_display() {
	$html = '<div id="ps-navbar" class="ps-navbar">';
	$html .= '<a href="/" class="active"><i class="fas fa-home"></i> Home</a>';
	$html .= '<a href="/">Latest Posts</a>';
	$html .= '<a href="/about/">About Us</a>';
	$html .= '<a href=" /contact/">Contact Us</a>';
	$html .= '<a href="/" class="active"><i class="fas fa-users"></i> Connect</a>';
	$html .= '<a href="/plans/">Sign Up!</a>';
	$html .= '<a href="/" class="active"><i class="fas fa-life-ring"></i> Support</a>';
	//$html .= '<span>You\'re visiting a PaganSpace community website. Eventually this section will have other stuff in it!</span>';
	$html .= '<img src="/wp-content/uploads/2020/02/PPTextLogo-800-142.png" class="ps-footer-img">';
	$html .= '</div>';

	return $html;
}


function pagansites_add_footer() {

	echo pagansites_footer_display();
}
add_action( 'wp_footer', __NAMESPACE__.'\\pagansites_add_footer' );
