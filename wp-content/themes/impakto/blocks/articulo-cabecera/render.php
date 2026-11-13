<?php
/**
 * Encabezado del artículo.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_id  = get_queried_object_id();
$impakto_cat = get_the_category( $impakto_id );
$impakto_pub = get_post_time( 'U', true, $impakto_id );
$impakto_mod = get_post_modified_time( 'U', true, $impakto_id );
$impakto_aut = (int) get_post_field( 'post_author', $impakto_id );
?>
<header class="articulo-cabecera">
	<div class="alignfull is-style-hero-curvas">
		<div class="contenedor contenedor--articulo">
			<?php if ( $impakto_cat ) : ?>
				<p class="kicker"><a href="<?php echo esc_url( get_category_link( $impakto_cat[0] ) ); ?>"><?php echo esc_html( $impakto_cat[0]->name ); ?></a></p>
			<?php endif; ?>
			<h1><?php echo esc_html( get_the_title( $impakto_id ) ); ?></h1>
			<p class="articulo-meta">
				<span><?php esc_html_e( 'Por', 'impakto' ); ?> <a href="<?php echo esc_url( get_author_posts_url( $impakto_aut ) ); ?>" rel="author"><?php echo esc_html( get_the_author_meta( 'display_name', $impakto_aut ) ); ?></a></span>
				<span><?php esc_html_e( 'Publicado el', 'impakto' ); ?> <time datetime="<?php echo esc_attr( gmdate( 'c', $impakto_pub ) ); ?>"><?php echo esc_html( wp_date( get_option( 'date_format' ), $impakto_pub ) ); ?></time></span>
				<?php if ( $impakto_mod - $impakto_pub > DAY_IN_SECONDS ) : ?>
					<span><?php esc_html_e( 'Actualizado el', 'impakto' ); ?> <time datetime="<?php echo esc_attr( gmdate( 'c', $impakto_mod ) ); ?>"><?php echo esc_html( wp_date( get_option( 'date_format' ), $impakto_mod ) ); ?></time></span>
				<?php endif; ?>
				<span><?php echo esc_html( sprintf( /* translators: %d: minutos. */ __( '%d min de lectura', 'impakto' ), impakto_minutos_lectura( $impakto_id ) ) ); ?></span>
			</p>
		</div>
	</div>
	<div class="contenedor articulo-portada">
		<?php echo has_post_thumbnail( $impakto_id ) ? get_the_post_thumbnail( $impakto_id, 'impakto-articulo', array( 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '(min-width: 1280px) 1200px, 100vw' ) ) : '<div class="marcador-imagen" style="--ratio: 16 / 9" role="img" aria-label="' . esc_attr__( 'Imagen destacada pendiente', 'impakto' ) . '"><span>' . esc_html__( 'Imagen destacada 1600 × 900', 'impakto' ) . '</span></div>'; ?>
	</div>
</header>
