<?php
/**
 * Archivo del portafolio (/portafolio/, /portafolio/categoria/{slug}/ y ?caso=1).
 * Los chips son enlaces reales (cada filtro es indexable); portafolio-filtros.js los mejora
 * para cambiar sin recargar y "Ver más" carga la siguiente página.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
$impakto_termino = is_tax( 'categoria-portafolio' ) ? get_queried_object() : null;
$impakto_caso    = (bool) get_query_var( 'caso' );
$impakto_base    = get_post_type_archive_link( 'proyecto' );
$impakto_cats    = get_terms(
	array(
		'taxonomy'   => 'categoria-portafolio',
		'hide_empty' => true,
	)
);
$impakto_orden   = function_exists( 'impakto_categorias_portafolio' ) ? array_keys( impakto_categorias_portafolio() ) : array();
if ( ! is_wp_error( $impakto_cats ) ) {
	usort( $impakto_cats, static fn( $a, $b ) => array_search( $a->slug, $impakto_orden, true ) <=> array_search( $b->slug, $impakto_orden, true ) );
}
$impakto_chips = array(
	array( __( 'Todos', 'impakto' ), $impakto_base, ! $impakto_termino && ! $impakto_caso ),
	array( __( 'Casos de éxito', 'impakto' ), add_query_arg( 'caso', 1, $impakto_base ), $impakto_caso ),
);
foreach ( is_wp_error( $impakto_cats ) ? array() : $impakto_cats as $impakto_c ) {
	$impakto_chips[] = array( $impakto_c->name, get_term_link( $impakto_c ), $impakto_termino && $impakto_termino->term_id === $impakto_c->term_id );
}
$impakto_siguiente = get_next_posts_page_link( $wp_query->max_num_pages );
$impakto_hay_mas   = $wp_query->max_num_pages > max( 1, (int) get_query_var( 'paged' ) );
?>
<div class="portafolio" data-portafolio>
	<section class="alignfull is-style-hero-curvas portafolio__hero">
		<div class="contenedor">
			<?php if ( $impakto_termino ) : ?>
				<p class="kicker"><?php esc_html_e( 'Portafolio', 'impakto' ); ?></p>
			<?php endif; ?>
			<h1 class="is-style-display portafolio__titulo" data-portafolio-titulo><?php echo esc_html( $impakto_termino ? $impakto_termino->name : ( $impakto_caso ? __( 'Casos de éxito', 'impakto' ) : __( 'Portafolio', 'impakto' ) ) ); ?></h1>
			<p class="is-style-bajada"><?php echo esc_html( $impakto_termino && $impakto_termino->description ? $impakto_termino->description : __( 'Marcas que construimos, campañas que movimos y resultados que medimos.', 'impakto' ) ); ?></p>
			<nav class="filtros" aria-label="<?php esc_attr_e( 'Filtrar el portafolio', 'impakto' ); ?>">
				<ul>
					<?php foreach ( $impakto_chips as list( $impakto_nombre, $impakto_url, $impakto_activo ) ) : ?>
						<li><a href="<?php echo esc_url( $impakto_url ); ?>" data-filtro<?php echo $impakto_activo ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $impakto_nombre ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
	</section>

	<?php if ( ! $impakto_termino && ! $impakto_caso && ! is_paged() ) : ?>
		<section class="alignfull is-style-seccion-oscura es-oscuro portafolio__cifras">
			<div class="contenedor">
				<div class="cifras">
					<?php
					$impakto_cifras = array(
						array( 'prefijo' => '', 'valor' => '836', 'sufijo' => '', 'texto' => 'ROAS', 'nota' => 'Constructora Hungría, ene 2026' ),
						array( 'prefijo' => '', 'valor' => '300', 'sufijo' => '', 'texto' => 'leads', 'nota' => 'Altaviva, 1 feb – 18 mar 2026' ),
						array( 'prefijo' => '$', 'valor' => '1.990', 'sufijo' => '', 'texto' => 'CPL desde', 'nota' => 'Pauta en Meta, Manizales, 2026' ),
						array( 'prefijo' => '', 'valor' => '12,5', 'sufijo' => ' %', 'texto' => 'CTR orgánico', 'nota' => 'Constructora Hungría, ene–jun 2026' ),
					);
					foreach ( $impakto_cifras as $impakto_attrs ) {
						echo render_block( array( 'blockName' => 'impakto/cifra', 'attrs' => $impakto_attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="alignfull is-style-seccion-clara portafolio__lista">
		<div class="contenedor">
			<?php if ( have_posts() ) : ?>
				<ul class="bento bento--archivo" data-portafolio-rejilla>
					<?php
					while ( have_posts() ) :
						the_post();
						$impakto_grande = (bool) get_post_meta( get_the_ID(), '_impakto_destacado', true );
						?>
						<li class="<?php echo $impakto_grande ? 'es-grande' : ''; ?>"><?php echo impakto_tarjeta_proyecto( get_the_ID(), $impakto_grande ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
					<?php endwhile; ?>
				</ul>
				<?php if ( $impakto_hay_mas && $impakto_siguiente ) : ?>
					<p class="portafolio__mas"><a class="boton boton--secundario" href="<?php echo esc_url( $impakto_siguiente ); ?>" data-ver-mas><?php esc_html_e( 'Ver más proyectos', 'impakto' ); ?></a></p>
				<?php endif; ?>
			<?php else : ?>
				<p class="is-style-bajada"><?php esc_html_e( 'Pronto publicaremos proyectos en esta categoría.', 'impakto' ); ?></p>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>
</div>
