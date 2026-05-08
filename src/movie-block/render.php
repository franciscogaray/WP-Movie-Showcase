<?php
/**
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

if ( empty( $attributes['movie'] ) || ! is_array( $attributes['movie'] ) ) {
    return;
}

$movie       = $attributes['movie'];
$show_poster = isset( $attributes['showPoster'] ) ? (bool) $attributes['showPoster'] : true;
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'wpms-movie' ] ); ?>>
    <div class="wpms-movie__inner" style="display:flex;gap:1.5rem;">
        <?php if ( $show_poster && ! empty( $movie['poster'] ) ) : ?>
            <img
                class="wpms-movie__poster"
                src="<?php echo esc_url( $movie['poster'] ); ?>"
                alt="<?php echo esc_attr( $movie['title'] ); ?>"
                loading="lazy"
                width="200"
            />
        <?php endif; ?>

        <div class="wpms-movie__meta">
            <h3 class="wpms-movie__title">
                <?php echo esc_html( $movie['title'] ); ?>
                <?php if ( ! empty( $movie['year'] ) ) : ?>
                    <span>(<?php echo esc_html( $movie['year'] ); ?>)</span>
                <?php endif; ?>
            </h3>

            <ul class="wpms-movie__details">
                <?php
                $fields = [
                    'rated'       => __( 'Rated', 'wp-movie-showcase' ),
                    'runtime'     => __( 'Runtime', 'wp-movie-showcase' ),
                    'genre'       => __( 'Genre', 'wp-movie-showcase' ),
                    'director'    => __( 'Director', 'wp-movie-showcase' ),
                    'actors'      => __( 'Cast', 'wp-movie-showcase' ),
                    'imdb_rating' => __( 'IMDb Rating', 'wp-movie-showcase' ),
                ];
                foreach ( $fields as $key => $label ) :
                    if ( empty( $movie[ $key ] ) ) {
                        continue;
                    }
                    ?>
                    <li>
                        <strong><?php echo esc_html( $label ); ?>:</strong>
                        <?php echo esc_html( $movie[ $key ] ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ( ! empty( $movie['plot'] ) ) : ?>
                <p class="wpms-movie__plot"><?php echo esc_html( $movie['plot'] ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
