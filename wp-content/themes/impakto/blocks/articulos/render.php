<?php
/**
 * Artículos recientes del blog.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => max( 1, (int) $attributes['cantidad'] ),
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);
if ( $attributes['categoria'] ) {
	$impakto_args['category_name'] = $attributes['categoria'];
}
$impakto_posts = get_posts( $impakto_args );
if ( ! $impakto_posts ) {
	return;
}
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'articulos' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $impakto_posts as $impakto_p ) : ?>
		<?php
		$impakto_cat     = get_the_category( $impakto_p->ID );
		$impakto_minutos = max( 1, (int) round( str_word_count( wp_strip_all_tags( $impakto_p->post_content ) ) / 220 ) );
		?>
		<li>
			<article class="tarjeta-articulo">
				<div class="tarjeta-articulo__media">
					<?php
					if ( has_post_thumbnail( $impakto_p ) ) {
						echo get_the_post_thumbnail( $impakto_p, 'impakto-articulo', array( 'loading' => 'lazy', 'sizes' => '(min-width: 768px) 33vw, 100vw' ) );
					} else {
						echo '<div class="marcador-imagen" style="--ratio: 16 / 9" aria-hidden="true"></div>';
					}
					?>
				</div>
				<p class="tarjeta-articulo__meta">
					<?php if ( $impakto_cat ) : ?>
						<span><?php echo esc_html( $impakto_cat[0]->name ); ?></span>
					<?php endif; ?>
					<?php /* translators: %d: minutos de lectura. */ ?>
					<span><?php echo esc_html( sprintf( _n( '%d min de lectura', '%d min de lectura', $impakto_minutos, 'impakto' ), $impakto_minutos ) ); ?></span>
				</p>
				<h3 class="tarjeta-articulo__titulo"><a href="<?php echo esc_url( get_permalink( $impakto_p ) ); ?>"><?php echo esc_html( get_the_title( $impakto_p ) ); ?></a></h3>
				<?php if ( has_excerpt( $impakto_p ) ) : ?>
					<p class="tarjeta-articulo__extracto"><?php echo esc_html( wp_trim_words( get_the_excerpt( $impakto_p ), 22 ) ); ?></p>
				<?php endif; ?>
			</article>
		</li>
	<?php endforeach; ?>
</ul>
