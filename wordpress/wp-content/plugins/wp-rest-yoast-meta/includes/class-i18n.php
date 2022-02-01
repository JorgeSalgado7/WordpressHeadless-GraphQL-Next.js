<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2018.1.0
 *
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Includes
 */

namespace WP_Rest_Yoast_Meta_Plugin\Includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2018.1.0
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Includes
 * @author     Richard Korthuis - Acato <richardkorthuis@acato.nl>
 */
class I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2018.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-rest-yoast-meta',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
