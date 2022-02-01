<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      2018.1.0
 *
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Frontend
 */

namespace WP_Rest_Yoast_Meta_Plugin\Frontend;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WP_Rest_Yoast_Meta_Plugin
 * @subpackage WP_Rest_Yoast_Meta_Plugin/Frontend
 * @author     Richard Korthuis - Acato <richardkorthuis@acato.nl>
 */
class Frontend {

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
	 * Variable to store the current post object.
	 *
	 * @since   2018.1.0
	 * @access  private
	 * @var     \WP_Post
	 */
	private $post;

	/**
	 * Variable to store the original WP Query object (needed to restore it).
	 *
	 * @since   2018.1.0
	 * @access  private
	 * @var     \WP_Query
	 */
	private $old_wp_query;

	/**
	 * Array containing filters that need to be removed prior to resetting WPSEO_Frontend
	 *
	 * @since   2018.1.1
	 * @access  private
	 * @var     array
	 */
	private $remove_filters;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2018.1.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->remove_filters = [
			'wp_head'    => [
				'front_page_specific_init' => 0,
				'head'                     => 1,
			],
			'wpseo_head' => [
				'debug_mark'         => 2,
				'metadesc'           => 6,
				'robots'             => 10,
				'canonical'          => 20,
				'adjacent_rel_links' => 21,
				'publisher'          => 22,
			],
		];
	}

	/**
	 * Add the Yoast meta data to the WP REST output
	 *
	 * @since   2018.1.0
	 * @access  public
	 *
	 * @param \WP_REST_Response $response The response object.
	 * @param \WP_Post          $post Post object.
	 * @param \WP_REST_Request  $request Request object.
	 *
	 * @return  \WP_REST_Response
	 */
	public function rest_add_yoast( $response, $post, $request ) {

		$yoast_data = $this->get_yoast_data( $post, $response );

		/**
		 * Filter Yoast title.
		 *
		 * Allows to alter the Yoast title.
		 *
		 * @since   2019.5.2
		 */
		$yoast_title = apply_filters( 'wp_rest_yoast_meta/filter_yoast_title', $yoast_data['title'] );

		$response->data['yoast_title'] = $yoast_title;

		/**
		 * Filter meta array.
		 *
		 * Allows to alter the meta array in order to add or remove meta keys and values.
		 *
		 * @since   2018.1.2
		 *
		 * @param   array $yoast_meta An array of meta key/value pairs.
		 */
		$yoast_meta = apply_filters( 'wp_rest_yoast_meta/filter_yoast_meta', $yoast_data['meta'] );

		$response->data['yoast_meta'] = $yoast_meta;

		/**
		 * Filter json ld array.
		 *
		 * Allows to alter the json ld array.
		 *
		 * @since   2019.4.0
		 *
		 * @param   array $yoast_json_ld An array of json ld data.
		 */
		$yoast_json_ld = apply_filters( 'wp_rest_yoast_meta/filter_yoast_json_ld', $yoast_data['json_ld'] );

		$response->data['yoast_json_ld'] = $yoast_json_ld;

		return $response;
	}

	/**
	 * Update transient with new yoast meta upon post update
	 *
	 * @param int      $post_ID Post ID.
	 * @param \WP_Post $post Post object.
	 */
	public function update_yoast_meta( $post_ID, $post ) {
		if ( $this->should_cache() ) {
			delete_transient( 'yoast_meta_' . $post_ID );
		}
	}

	/**
	 * Delete yoast meta transient upon post deletion.
	 * This function does not look if the plugin should use it's cache at the moment of deletion, it might've been
	 * cached in the past.
	 *
	 * @param int $post_ID Post ID.
	 */
	public function delete_yoast_meta( $post_ID ) {
		delete_transient( 'yoast_meta_' . $post_ID );
	}

	/**
	 * Check if the plugin should use it's own cache based on the activation of other plugins
	 *
	 * @return bool
	 */
	protected function should_cache() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		return ! is_plugin_active( 'wp-rest-cache/wp-rest-cache.php' );
	}

	/**
	 * Get the cached yoast data
	 *
	 * @param int $post_ID Post ID.
	 *
	 * @return bool|mixed
	 */
	protected function get_cache( $post_ID ) {
		if ( ! $this->should_cache() ) {
			return false;
		}

		$transient_key = 'yoast_meta_' . $post_ID;

		return get_transient( $transient_key );
	}

	/**
	 * Fetch yoast meta and possibly json ld and store in transient if needed
	 *
	 * @param \WP_Post          $post Post object.
	 * @param \WP_REST_Response $response The response object.
	 *
	 * @return array|mixed
	 */
	public function get_yoast_data( $post, $response ) {
		global $wp_query;

		if ( 'home' === $post ) {
			$this->post = null;
			$yoast_data = false;
		} else {
			$this->post = $post;
			$yoast_data = $this->get_cache( $post->ID );
		}
		if ( false === $yoast_data || ! isset( $yoast_data['meta'] ) || ! isset( $yoast_data['json_ld'] ) || ! isset( $yoast_data['title'] ) ) {
			if ( version_compare( WPSEO_VERSION, '14.0', '>=' ) ) {
				if ( isset( $response->data['yoast_head'] ) ) {
					$html = $response->data['yoast_head'];

					global $wp_version;
					// As of WP 5.7.0 the robots meta doesn't come from Yoast SEO, but from WP Core.
					if ( version_compare( $wp_version, '5.7', '>=' ) ) {
						ob_start();
						wp_robots();
						$html .= ob_get_clean();
					}

					// Parse the xml to create an array of meta items.
					$yoast_data = $this->parse( $html );
					if ( empty( $yoast_data['title'] ) ) {
						$this->setup_postdata_and_wp_query();
						$yoast_data['title'] = wp_title( '&raquo;', false );
					}
				}
			} else {

				remove_action( 'wpseo_head', array( 'WPSEO_Twitter', 'get_instance' ), 40 );

				// Let Yoast generate the html and fetch it.
				$frontend = \WPSEO_Frontend::get_instance();
				ob_start();
				add_action( 'wpseo_head', [ $this, 'setup_postdata_and_wp_query' ], 1 );
				add_action( 'wpseo_opengraph', [ $this, 'setup_postdata_and_wp_query' ], 1 );

				// Remove filters to prevent double output.
				if ( class_exists( 'WPSEO_Schema' ) ) { // WPSEO_Schema is only available since Yoast 11.x.
					$this->remove_filter( 'wpseo_head', array( new \WPSEO_Schema(), 'json_ld' ), 91 );
					$this->remove_filter( 'wpseo_json_ld', array( new \WPSEO_Schema(), 'generate' ), 1 );
					$this->remove_filter( 'amp_post_template_head', array( new \WPSEO_Schema(), 'json_ld' ), 9 );
					add_action( 'wpseo_head', array( new \WPSEO_Schema(), 'json_ld' ), 91 );
					add_action( 'wpseo_json_ld', array( new \WPSEO_Schema(), 'generate' ), 1 );
				}

				$frontend->head();
				$twitter = new \WPSEO_Twitter();
				$html    = ob_get_clean();

				// Remove filters to prevent double output these are added again on reinitializing WPSEO_Frontend.
				foreach ( $this->remove_filters as $filter => $functions ) {
					foreach ( $functions as $function => $prio ) {
						remove_filter( $filter, [ $frontend, $function ], $prio );
					}
				}
				$frontend->reset();

				// Parse the xml to create an array of meta items.
				$yoast_data = $this->parse( $html );

				if ( 'home' === $post ) {
					$yoast_data['title'] = $frontend->title( '' );
				} else {
					$yoast_data['title'] = $frontend->title( $post->post_title );
				}
			}

			if ( is_array( $yoast_data['meta'] ) && count( $yoast_data['meta'] ) ) {
				if ( 'home' !== $post ) {
					$transient_key = 'yoast_meta_' . $post->ID;
					set_transient( $transient_key, $yoast_data, MONTH_IN_SECONDS );
				}
			}

			// Reset postdata & wp_query.
			if ( ! is_null( $this->old_wp_query ) ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$wp_query = $this->old_wp_query;
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
				wp_reset_postdata();
			}
		}

		return $yoast_data;
	}

	/**
	 * Remove filters that can not be removed with WordPress' core `remove_filter` function.
	 *
	 * @param string $hook The hook for which a filter needs to be removed.
	 * @param mixed  $function The callback function that needs to be removed.
	 * @param int    $prio The priority at which the filter was added.
	 *
	 * @return bool False if filter was not found, true if it was found and removed.
	 */
	private function remove_filter( $hook, $function, $prio ) {
		global $wp_filter;

		if ( ! isset( $wp_filter[ $hook ] ) || ! isset( $wp_filter[ $hook ]->callbacks[ $prio ] ) ) {
			return false;
		}

		$found = false;
		foreach ( $wp_filter[ $hook ]->callbacks[ $prio ] as $index => $callback ) {
			if ( $callback['function'] === $function ) {
				unset( $wp_filter[ $hook ]->callbacks[ $prio ][ $index ] );
				$found = true;
			} elseif ( is_array( $function ) && is_array( $callback['function'] )
						&& get_class( $callback['function'][0] ) === $function[0]
						&& $function[1] === $callback['function'][1] ) {
				unset( $wp_filter[ $hook ]->callbacks[ $prio ][ $index ] );
				$found = true;
			} elseif ( is_array( $function ) && is_array( $callback['function'] )
						&& get_class( $function[0] ) === get_class( $callback['function'][0] )
						&& $function[1] === $callback['function'][1] ) {
				unset( $wp_filter[ $hook ]->callbacks[ $prio ][ $index ] );
				$found = true;
			}
		}

		return $found;
	}

	/**
	 * Parse HTML to an array of meta key/value pairs using \DOMDocument or simplexml.
	 *
	 * @since 2019.3.0
	 *
	 * @param string $html The HTML as generated by Yoast SEO.
	 *
	 * @return array An array containing all meta key/value pairs.
	 */
	private function parse( $html ) {
		if ( class_exists( 'DOMDocument' ) ) {
			return $this->parse_using_domdocument( $html );
		} else {
			return $this->parse_using_simplexml( $html );
		}
	}

	/**
	 * Parse HTML to an array of meta key/value pairs using \DOMDocument.
	 *
	 * @since 2019.3.0
	 *
	 * @param string $html The HTML as generated by Yoast SEO.
	 *
	 * @return array An array containing all meta key/value pairs.
	 */
	private function parse_using_domdocument( $html ) {
		$dom = new \DOMDocument();

		$internal_errors = libxml_use_internal_errors( true );
		$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ) );

		$metas       = $dom->getElementsByTagName( 'meta' );
		$yoast_metas = [];
		foreach ( $metas as $meta ) {
			if ( $meta->hasAttributes() ) {
				$yoast_meta = [];
				foreach ( $meta->attributes as $attr ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$yoast_meta[ $attr->nodeName ] = esc_html( wp_strip_all_tags( stripslashes( $attr->nodeValue ), true ) );
				}
				$yoast_metas[] = $yoast_meta;
			}
		}

		$nodes = $dom->getElementsByTagName( 'title' );
		$title = null;
		if ( $nodes->length ) {
			$title = esc_html( wp_strip_all_tags( stripslashes( $nodes[0]->nodeValue ), true ) );
		}

		$xpath         = new \DOMXPath( $dom );
		$yoast_json_ld = [];
		foreach ( $xpath->query( '//script[@type="application/ld+json"]' ) as $node ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$yoast_json_ld[] = json_decode( (string) $node->nodeValue, true );
		}
		libxml_use_internal_errors( $internal_errors );

		return [
			'meta'    => $yoast_metas,
			'title'   => $title,
			'json_ld' => $yoast_json_ld,
		];
	}

	/**
	 * Parse HTML to an array of meta key/value pairs using simplexml as a fallback if \DOMDocument is unavailable.
	 *
	 * @since 2019.3.0
	 *
	 * @param string $html The HTML as generated by Yoast SEO.
	 *
	 * @return array An array containing all meta key/value pairs.
	 */
	private function parse_using_simplexml( $html ) {
		$yoast_metas   = [];
		$title         = null;
		$yoast_json_ld = [];
		$xml           = simplexml_load_string( '<yoast>' . $html . '</yoast>' );
		if ( $xml ) {
			foreach ( $xml->meta as $meta ) {
				$yoast_meta = [];
				$attributes = $meta->attributes();
				foreach ( $attributes as $key => $value ) {
					$yoast_meta[ (string) $key ] = esc_html( wp_strip_all_tags( stripslashes( (string) $value ), true ) );
				}
				$yoast_metas[] = $yoast_meta;
			}

			$title = isset( $xml->title ) ? esc_html( wp_strip_all_tags( stripslashes( (string) $xml->title ), true ) ) : null;

			foreach ( $xml->xpath( '//script[@type="application/ld+json"]' ) as $node ) {
				$yoast_json_ld[] = json_decode( (string) $node, true );
			}
		}

		return [
			'meta'    => $yoast_metas,
			'title'   => $title,
			'json_ld' => $yoast_json_ld,
		];
	}

	/**
	 * Temporary set up postdata and wp_query to represent the current post (so Yoast will process it correctly)
	 *
	 * @since   2018.1.0
	 * @access  public
	 */
	public function setup_postdata_and_wp_query() {
		global $wp_query;

		$post = $this->post;
		setup_postdata( $post );
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['post'] = $post;
		if ( ! is_null( $post ) ) {
			$this->old_wp_query = $wp_query;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_query = new \WP_Query(
				[
					'p'         => $post->ID,
					'post_type' => $post->post_type,
				]
			);
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
		}
	}

	/**
	 * Register an endpoint for retrieving redirects.
	 */
	public function register_redirects_endpoint() {
		register_rest_route(
			'wp-rest-yoast-meta/v1',
			'redirects',
			array(
				'methods'             => 'GET',
				'callback'            => [ $this, 'return_redirects' ],
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Retrieve an array of all redirects as set in Yoast SEO Premium.
	 *
	 * @return array An array containing all redirects.
	 */
	public function return_redirects() {
		$manager   = new \WPSEO_Redirect_Manager();
		$redirects = $manager->get_all_redirects();

		$data = [];
		foreach ( $redirects as $redirect ) {
			$origin = $this->leadingslashit( trailingslashit( $redirect->get_origin() ) );
			$target = $this->leadingslashit( trailingslashit( $redirect->get_target() ) );
			$type   = $redirect->get_type();
			$data[] = sprintf( '%s %s %d', $origin, $target, $type );
		}

		return $data;
	}

	/**
	 * Appends a leading slash.
	 *
	 * Will remove leading forward and backslashes if it exists already before adding
	 * a leading forward slash. This prevents double slashing a string or path.
	 *
	 * This function is inspired by the WordPress function `trailingslashit`.
	 *
	 * @param string $string What to add the leading slash to.
	 * @return string String with leading slash added.
	 */
	private function leadingslashit( $string ) {
		return '/' . ltrim( $string, '/\\' );
	}

	/**
	 * Register an endpoint for retrieving Yoast meta for the homepage.
	 */
	public function register_home_endpoint() {
		register_rest_route(
			'wp-rest-yoast-meta/v1',
			'home',
			array(
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_home_yoast_meta' ],
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get the Yoast meta for the homepage as an array.
	 *
	 * @return array An array of Yoast meta and possibly json ld.
	 */
	public function get_home_yoast_meta() {
		$yoast_data = [];
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$post       = get_post( get_option( 'page_on_front' ) );
			$yoast_data = $this->get_yoast_data( $post, new \WP_REST_Response() );
		} else {
			global $wp_query;

			$this->old_wp_query = $wp_query;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_query          = new \WP_Query();
			$wp_query->is_home = true;
			$wp_query->get_posts();

			add_action( 'wpseo_head', [ $this, 'override_query_reset' ], 1 );
			$yoast_data = $this->get_yoast_data( 'home', new \WP_REST_Response() );
			remove_action( 'wpseo_head', [ $this, 'override_query_reset' ], 1 );
		}

		$return = [];

		/**
		 * Filter Yoast title.
		 *
		 * Allows to alter the Yoast title.
		 *
		 * @since   2019.5.2
		 */
		$yoast_title = apply_filters( 'wp_rest_yoast_meta/filter_yoast_title', $yoast_data['title'] );

		$return['yoast_title'] = $yoast_title;

		/**
		 * Filter meta array.
		 *
		 * Allows to alter the meta array in order to add or remove meta keys and values.
		 *
		 * @since   2018.1.2
		 *
		 * @param   array $yoast_meta An array of meta key/value pairs.
		 */
		$yoast_meta = apply_filters( 'wp_rest_yoast_meta/filter_yoast_meta', $yoast_data['meta'] );

		$return['yoast_meta'] = $yoast_meta;

		/**
		 * Filter json ld array.
		 *
		 * Allows to alter the json ld array.
		 *
		 * @since   2019.4.0
		 *
		 * @param   array $yoast_json_ld An array of json ld data.
		 */
		$yoast_json_ld = apply_filters( 'wp_rest_yoast_meta/filter_yoast_json_ld', $yoast_data['json_ld'] );

		$return['yoast_json_ld'] = $yoast_json_ld;

		return $return;
	}

	/**
	 * Override the query reset by Yoast SEO, since we want to do a query for the home page.
	 */
	public function override_query_reset() {
		global $wp_query;

		$wp_query->is_home = true;
	}

	/**
	 * The Front_End_Integration keeps using the first post for the filter_title function, fix it.
	 *
	 * @param \Yoast\WP\SEO\Presentations\Indexable_Post_Type_Presentation $presentation Presentation object for indexables.
	 * @param \Yoast\WP\SEO\Context\Meta_Tags_Context                      $context The meta tags context.
	 *
	 * @return \Yoast\WP\SEO\Presentations\Indexable_Post_Type_Presentation
	 */
	public function fix_frontend_presentation( $presentation, $context ) {
		global $post;

		if ( isset( $presentation->source ) && $presentation->source->ID !== $post->ID ) {
			unset( $presentation->model->title );
			$presentation->title   = $presentation->generate_title();
			$presentation->source  = $post;
			$context->presentation = $presentation;
			$presentation->title   = $context->generate_title();
		}

		return $presentation;
	}
}
