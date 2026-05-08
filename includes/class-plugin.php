<?php
namespace WPMovieShowcase;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	private static ?Plugin $instance = null;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function init(): void {
		( new Settings() )->register();
		( new Rest_Controller() )->register();
		( new Block() )->register();
	}
}
