<?php
/**
 * Plugin Name:       WP Movie Showcase
 * Description:       Search movies via OMDb API and display them in posts/pages using a Gutenberg block.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Francisco Garay
 * Author URI:        https://franciscogaray.me
 * License:           GPL-2.0-or-later
 * Text Domain:       wp-movie-showcase
 *
 * @package WPMovieShowcase
 */

namespace WPMovieShowcase;

defined( 'ABSPATH' ) || exit;

define( 'WPMS_VERSION', '1.0.0' );
define( 'WPMS_PLUGIN_FILE', __FILE__ );
define( 'WPMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPMS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WPMS_PLUGIN_DIR . 'includes/class-plugin.php';
require_once WPMS_PLUGIN_DIR . 'includes/class-settings.php';
require_once WPMS_PLUGIN_DIR . 'includes/class-omdb-client.php';
require_once WPMS_PLUGIN_DIR . 'includes/class-rest-controller.php';
require_once WPMS_PLUGIN_DIR . 'includes/class-block.php';

add_action(
	'plugins_loaded',
	static function () {
		Plugin::instance()->init();
	}
);
