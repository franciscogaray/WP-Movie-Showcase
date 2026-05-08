<?php
namespace WPMovieShowcase;

defined( 'ABSPATH' ) || exit;

class Block {

	public function register(): void {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	public function register_block(): void {
		register_block_type( WPMS_PLUGIN_DIR . 'build/movie-block' );
	}
}
