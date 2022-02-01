<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      2018.1.0
 *
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Includes
 */

namespace WP_Rest_Yoast_Meta_Plugin\Includes;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2018.1.0
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Includes
 * @author     Richard Korthuis - Acato <richardkorthuis@acato.nl>
 */
class Plugin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2018.1.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2018.1.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2018.1.0
	 */
	public function __construct() {
		$this->version     = '2021.1.2';
		$this->plugin_name = 'wp-rest-yoast-meta';

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Rest_Yoast_Meta_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2018.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2018.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$admin = new \WP_Rest_Yoast_Meta_Plugin\Admin\Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_init', [ $admin, 'check_requirements' ] );
		add_action( 'init', [ $admin, 'upgrade' ] );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2018.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$frontend = new \WP_Rest_Yoast_Meta_Plugin\Frontend\Frontend( $this->get_plugin_name(), $this->get_version() );

		if ( version_compare( WPSEO_VERSION, '14.0', '<' ) ) {
			add_action( 'rest_api_init', 'wpseo_frontend_head_init' );
		}
		add_action( 'rest_api_init', [ $frontend, 'register_redirects_endpoint' ] );
		add_action( 'rest_api_init', [ $frontend, 'register_home_endpoint' ] );
		add_action( 'init', [ $this, 'register_rest_prepare_hooks' ], 100 );
		add_action( 'save_post', [ $frontend, 'update_yoast_meta' ], 10, 2 );
		add_action( 'delete_post', [ $frontend, 'delete_yoast_meta' ] );
		add_filter( 'wpseo_frontend_presentation', [ $frontend, 'fix_frontend_presentation' ], 10, 2 );
	}

	/**
	 * Register `rest_prepare_{$post_type}` hooks for all post types visible in REST API.
	 */
	public function register_rest_prepare_hooks() {
		$frontend = new \WP_Rest_Yoast_Meta_Plugin\Frontend\Frontend( $this->get_plugin_name(), $this->get_version() );

		/**
		 * Filter post types array.
		 *
		 * Allows to alter the post types to which the Yoast meta is applied.
		 *
		 * @since   2019.6.0
		 *
		 * @param   array $yoast_meta An array of meta key/value pairs.
		 */
		$post_types = apply_filters( 'wp_rest_yoast_meta/filter_post_types', get_post_types( array( 'show_in_rest' => true ), 'objects' ) );

		foreach ( $post_types as $post_type ) {
			add_filter( 'rest_prepare_' . $post_type->name, [ $frontend, 'rest_add_yoast' ], 10, 3 );
		}
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2018.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2018.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
