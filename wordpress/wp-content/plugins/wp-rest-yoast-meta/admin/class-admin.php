<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      2018.1.0
 *
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Admin
 */

namespace WP_Rest_Yoast_Meta_Plugin\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version.
 *
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Admin
 * @author     Richard Korthuis - Acato <richardkorthuis@acato.nl>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2018.1.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2018.1.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2018.1.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Check if Yoast SEO (Premium) is installed and activated.
	 */
	public function check_requirements() {
		if ( ! defined( 'WPSEO_VERSION' ) ) {
			deactivate_plugins( 'wp-rest-yoast-meta/wp-rest-yoast-meta.php' );
			add_action( 'admin_notices', [ $this, 'requirements_notice' ] );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['activate'] ) ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				unset( $_GET['activate'] );
			}
		}
	}

	/**
	 * Show a notice that Yoast SEO (Premium) is required for this plugin.
	 */
	public function requirements_notice() {
		?>
		<div class="error">
			<p>
				<?php echo esc_html( __( 'WP REST Yoast Meta requires Yoast SEO or Yoast SEO Premium to be installed and activated', 'wp-rest-yoast-meta' ) ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Execute code needed for the upgrade.
	 */
	public function upgrade() {
		$version = get_option( 'wp_rest_yoast_meta_version', false );
		if ( ! $version || version_compare( '2019.4.2', $version, '>' ) || version_compare( '2021.1.1', $version, '>' ) ) {
			if ( ! wp_using_ext_object_cache() ) {
				global $wpdb;

				$sql = "DELETE FROM $wpdb->options WHERE `option_name` LIKE '%transient\_yoast\_meta\_%'";
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$wpdb->query( $sql );
			}
		}
		if ( $this->version !== $version ) {
			update_option( 'wp_rest_yoast_meta_version', $this->version, false );
		}
	}
}
