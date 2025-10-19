<?php
/**
 * Plugin name: Unit Testing Sample
 * Description: Does amazing stuff, but might break the website
 * Version: 1.0.0
 * Author: Philipp Stracker (p.stracker@syde.com)
 */

declare( strict_types = 1 );

namespace WorkshopPlugin;

defined( 'ABSPATH' ) || exit;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Initialize plugin
 *
 * This is just a demo plugin for the workshop.
 * In a real plugin, you'd initialize your classes here.
 */
function init_plugin(): void {
	// Plugin initialization would happen here
	// For this workshop, we're focusing on testing the classes
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init_plugin' );
