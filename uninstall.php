<?php

namespace PaganSites\Core;

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://pagansites.net/
 * @since      1.0.0
 *
 * @author Ken Kitchen me@kenneth.kitchen
 * @package    PaganSites
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Nothing to do here!
