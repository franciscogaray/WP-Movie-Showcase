<?php
namespace WPMovieShowcase;

defined( 'ABSPATH' ) || exit;

class Rest_Controller {

	public const NAMESPACE = 'wp-movie-showcase/v1';

	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/search',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'handle_search' ),
				'permission_callback' => array( $this, 'permission_check' ),
				'args'                => array(
					'title' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => static function ( $v ) {
							return is_string( $v ) && '' !== trim( $v );
						},
					),
				),
			)
		);
	}

	public function permission_check(): bool {
		return current_user_can( 'edit_posts' );
	}

	public function handle_search( \WP_REST_Request $request ) {
		$client = new OMDb_Client();
		$result = $client->search_by_title( $request->get_param( 'title' ) );

		if ( isset( $result['error'] ) ) {
			return new \WP_Error( 'wpms_omdb_error', $result['error'], array( 'status' => 404 ) );
		}

		return rest_ensure_response( $result );
	}
}
