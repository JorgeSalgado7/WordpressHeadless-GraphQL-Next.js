<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      2018.1.0
 *
 * @package    Wp_Rest_Yoast_Meta
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
