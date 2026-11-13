<?php
/**
 * Listado del blog: /blog/, /blog/categoria/{slug}/, /blog/autor/{slug}/ y búsqueda.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_obj    = get_queried_object();
$impakto_kicker = __( 'Blog', 'impakto' );
$impakto_titulo = __( 'Ideas para <mark class="acento">vender más</mark>', 'impakto' );
$impakto_texto  = __( 'Marketing digital, producción audiovisual, podcast, branding y marketing inmobiliario, con datos y ejemplos reales.', 'impakto' );
$impakto_autor  = null;
if ( is_category() ) {
	$impakto_titulo = esc_html( $impakto_obj->name );
	$impakto_texto  = $impakto_obj->description;
} elseif ( is_author() ) {
	$impakto_autor  = $impakto_obj;
	$impakto_kicker = __( 'Autor', 'impakto' );
	$impakto_titulo = esc_html( $impakto_obj->display_name );
	$impakto_texto  = get_the_author_meta( 'description', $impakto_obj->ID );
} elseif ( is_search() ) {
	/* translators: %s: búsqueda. */
	$impakto_titulo = esc_html( sprintf( __( 'Resultados para "%s"', 'impakto' ), get_search_query() ) );
	$impakto_texto  = '';
}
$impakto_cats = get_categories( array( 'hide_empty' => true ) );
$impakto_blog = get_permalink( get_option( 'page_for_posts' ) );
?>
<div class="blog">
	<section class="alignfull is-style-hero-curvas blog__hero">
		<div class="contenedor">
			<p class="kicker"><?php echo esc_html( $impakto_kicker ); ?></p>
			<h1><?php echo wp_kses( $impakto_titulo, array( 'mark' => array( 'class' => true ) ) ); ?></h1>
			<?php if ( $impakto_texto ) : ?>
				<p class="is-style-bajada"><?php echo esc_html( $impakto_texto ); ?></p>
			<?php endif; ?>
			<nav class="filtros" aria-label="<?php esc_attr_e( 'Categorías del blog', 'impakto' ); ?>">
				<ul>
					<li><a href="<?php echo esc_url( $impakto_blog ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Todos', 'impakto' ); ?></a></li>
					<?php foreach ( $impakto_cats as $impakto_c ) : ?>
						<li><a href="<?php echo esc_url( get_category_link( $impakto_c ) ); ?>"<?php echo is_category( $impakto_c->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $impakto_c->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
	</section>
	<section class="alignfull is-style-seccion-blanca blog__lista">
		<div class="contenedor">
			<?php if ( have_posts() ) : ?>
				<ul class="articulos">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li><?php echo impakto_tarjeta_articulo( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
					<?php endwhile; ?>
				</ul>
				<?php
				$impakto_pag = paginate_links(
					array(
						'type'      => 'list',
						'prev_text' => __( '← Anteriores', 'impakto' ),
						'next_text' => __( 'Siguientes →', 'impakto' ),
					)
				);
				if ( $impakto_pag ) {
					echo '<nav class="paginacion" aria-label="' . esc_attr__( 'Paginación', 'impakto' ) . '">' . wp_kses_post( $impakto_pag ) . '</nav>';
				}
				?>
			<?php else : ?>
				<p class="is-style-bajada"><?php esc_html_e( 'Aún no hay artículos aquí.', 'impakto' ); ?></p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>
</div>
