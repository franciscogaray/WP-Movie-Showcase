<?php
namespace WPMovieShowcase;

defined( 'ABSPATH' ) || exit;

class Settings {

	public const OPTION_GROUP = 'wpms_settings';
	public const OPTION_NAME  = 'wpms_options';
	public const PAGE_SLUG    = 'wp-movie-showcase';

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_menu(): void {
		add_options_page(
			__( 'WP Movie Showcase', 'wp-movie-showcase' ),
			__( 'Movie Showcase', 'wp-movie-showcase' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	public function register_settings(): void {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array( 'api_key' => '' ),
				'show_in_rest'      => false,
			)
		);

		add_settings_section(
			'wpms_main_section',
			__( 'OMDb API Configuration', 'wp-movie-showcase' ),
			static function () {
				echo '<p>' . esc_html__( 'Get a free API key at omdbapi.com.', 'wp-movie-showcase' ) . '</p>';
			},
			self::PAGE_SLUG
		);

		add_settings_field(
			'api_key',
			__( 'OMDb API Key', 'wp-movie-showcase' ),
			array( $this, 'render_api_key_field' ),
			self::PAGE_SLUG,
			'wpms_main_section'
		);
	}

	public function sanitize( $input ): array {
		$output  = array();
		$api_key = isset( $input['api_key'] ) ? trim( (string) $input['api_key'] ) : '';

		if ( '' !== $api_key ) {
			if ( ! preg_match( '/^\*+$/', $api_key ) ) {
				$output['api_key'] = self::encrypt( $api_key );
			} else {
				$existing          = get_option( self::OPTION_NAME, array() );
				$output['api_key'] = $existing['api_key'] ?? '';
			}
		}

		return $output;
	}

	public function render_api_key_field(): void {
		$options = get_option( self::OPTION_NAME, array() );
		$has_key = ! empty( $options['api_key'] );
		$masked  = $has_key ? str_repeat( '*', 12 ) : '';
		?>
		<input
			type="password"
			id="wpms_api_key"
			name="<?php echo esc_attr( self::OPTION_NAME . '[api_key]' ); ?>"
			value="<?php echo esc_attr( $masked ); ?>"
			class="regular-text"
			autocomplete="new-password"
		/>
		<p class="description">
			<?php esc_html_e( 'Stored encrypted. Leave masked value to keep current key.', 'wp-movie-showcase' ); ?>
		</p>
		<?php
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/* ---------- Encryption helpers ---------- */

	public static function get_api_key(): string {
		$options   = get_option( self::OPTION_NAME, array() );
		$encrypted = $options['api_key'] ?? '';
		return $encrypted ? self::decrypt( $encrypted ) : '';
	}

	private static function encryption_key(): string {
		return hash( 'sha256', wp_salt( 'auth' ), true );
	}

	private static function encrypt( string $value ): string {
		if ( ! function_exists( 'openssl_encrypt' ) ) {
			return base64_encode( $value );
		}
		$iv        = openssl_random_pseudo_bytes( 16 );
		$encrypted = openssl_encrypt( $value, 'AES-256-CBC', self::encryption_key(), OPENSSL_RAW_DATA, $iv );
		return base64_encode( $iv . $encrypted );
	}

	private static function decrypt( string $value ): string {
		if ( ! function_exists( 'openssl_decrypt' ) ) {
			return base64_decode( $value );
		}
		$decoded   = base64_decode( $value );
		$iv        = substr( $decoded, 0, 16 );
		$encrypted = substr( $decoded, 16 );
		return (string) openssl_decrypt( $encrypted, 'AES-256-CBC', self::encryption_key(), OPENSSL_RAW_DATA, $iv );
	}
}