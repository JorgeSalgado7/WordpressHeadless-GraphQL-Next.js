<?php
/**
 * The plugin bootstrap file
 *
 * @since             2018.1.0
 * @package           WP_Rest_Yoast_Meta
 *
 * @wordpress-plugin
 * Plugin Name:       WP REST Yoast Meta
 * Description:       Add yoast meta information to the WP REST API
 * Version:           2021.1.2
 * Author:            Acato
 * Author URI:        https://www.acato.nl
 * Text Domain:       wp-rest-yoast-meta
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-autoloader.php';
spl_autoload_register( [ '\WP_Rest_Yoast_Meta_Plugin\Includes\Autoloader', 'autoload' ] );

/**
 * Begins execution of the plugin.
 */
new \WP_Rest_Yoast_Meta_Plugin\Includes\Plugin();
