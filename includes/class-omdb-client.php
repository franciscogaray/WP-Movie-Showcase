<?php
namespace WPMovieShowcase;

defined( 'ABSPATH' ) || exit;

class OMDb_Client {

	private const ENDPOINT  = 'https://www.omdbapi.com/';
	private const CACHE_TTL = HOUR_IN_SECONDS * 12;

	public function search_by_title( string $title ): array {
		$title = trim( $title );
		if ( '' === $title ) {
			return array( 'error' => __( 'Title is required.', 'wp-movie-showcase' ) );
		}

		$api_key = Settings::get_api_key();
		if ( '' === $api_key ) {
			return array( 'error' => __( 'OMDb API key is not configured.', 'wp-movie-showcase' ) );
		}

		$cache_key = 'wpms_movie_' . md5( strtolower( $title ) );
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$url = add_query_arg(
			array(
				't'      => rawurlencode( $title ),
				'apikey' => $api_key,
				'plot'   => 'short',
				'r'      => 'json',
			),
			self::ENDPOINT
		);

		$response = wp_remote_get( $url, array( 'timeout' => 10 ) );

		if ( is_wp_error( $response ) ) {
			return array( 'error' => $response->get_error_message() );
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			return array( 'error' => sprintf( 'OMDb returned HTTP %d', $code ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || 'False' === ( $body['Response'] ?? '' ) ) {
			return array( 'error' => $body['Error'] ?? __( 'Movie not found.', 'wp-movie-showcase' ) );
		}

		$data = $this->normalize( $body );
		set_transient( $cache_key, $data, self::CACHE_TTL );
		return $data;
	}

	private function normalize( array $raw ): array {
		return array(
			'title'       => $raw['Title'] ?? '',
			'year'        => $raw['Year'] ?? '',
			'rated'       => $raw['Rated'] ?? '',
			'released'    => $raw['Released'] ?? '',
			'runtime'     => $raw['Runtime'] ?? '',
			'genre'       => $raw['Genre'] ?? '',
			'director'    => $raw['Director'] ?? '',
			'actors'      => $raw['Actors'] ?? '',
			'plot'        => $raw['Plot'] ?? '',
			'poster'      => ( isset( $raw['Poster'] ) && 'N/A' !== $raw['Poster'] ) ? $raw['Poster'] : '',
			'imdb_id'     => $raw['imdbID'] ?? '',
			'imdb_rating' => $raw['imdbRating'] ?? '',
		);
	}
}
