<?php
/**
 * Pie del artículo: autor (con biografía profesional) y artículos relacionados.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_id  = get_queried_object_id();
$impakto_aut = (int) get_post_field( 'post_author', $impakto_id );
$impakto_bio = get_the_author_meta( 'description', $impakto_aut );
$impakto_cat = wp_get_post_categories( $impakto_id );
$impakto_rel = get_posts(
	array(
		'post__not_in'        => array( $impakto_id ),
		'category__in'        => $impakto_cat,
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
	)
);
if ( count( $impakto_rel ) < 3 ) {
	$impakto_rel = array_merge(
		$impakto_rel,
		get_posts(
			array(
				'post__not_in'   => array_merge( array( $impakto_id ), wp_list_pluck( $impakto_rel, 'ID' ) ),
				'posts_per_page' => 3 - count( $impakto_rel ),
			)
		)
	);
}
?>
<div class="articulo-pie">
	<div class="contenedor contenedor--articulo">
		<aside class="autor-caja" aria-label="<?php esc_attr_e( 'Sobre el autor', 'impakto' ); ?>">
			<?php echo get_avatar( $impakto_aut, 72, '', '', array( 'class' => 'autor-caja__avatar' ) ); ?>
			<div>
				<p class="kicker"><?php esc_html_e( 'Escrito por', 'impakto' ); ?></p>
				<p class="autor-caja__nombre"><a href="<?php echo esc_url( get_author_posts_url( $impakto_aut ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $impakto_aut ) ); ?></a></p>
				<?php if ( $impakto_bio ) : ?>
					<p class="autor-caja__bio"><?php echo esc_html( $impakto_bio ); ?></p>
				<?php endif; ?>
			</div>
		</aside>
	</div>
	<?php if ( $impakto_rel ) : ?>
		<section class="alignfull is-style-seccion-gris seccion-relacionados">
			<div class="contenedor">
				<div class="encabezado-seccion"><h2><?php echo wp_kses( __( 'Sigue <mark class="acento">leyendo</mark>', 'impakto' ), array( 'mark' => array( 'class' => true ) ) ); ?></h2></div>
				<ul class="articulos">
					<?php foreach ( $impakto_rel as $impakto_p ) : ?>
						<li><?php echo impakto_tarjeta_articulo( $impakto_p ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif; ?>
</div>
