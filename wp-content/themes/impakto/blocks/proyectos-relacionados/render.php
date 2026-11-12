<?php
/**
 * Proyectos relacionados: consulta automática al portafolio por categoría (BRIEF §6.2.5).
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_cat = $attributes['categoria'];
if ( ! $impakto_cat && is_singular( 'servicio' ) ) {
	$impakto_cat = get_post_meta( get_the_ID(), '_impakto_categoria_portafolio', true );
}
if ( ! $impakto_cat || ! taxonomy_exists( 'categoria-portafolio' ) ) {
	return;
}
$impakto_ids = get_posts(
	array(
		'post_type'      => 'proyecto',
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, (int) $attributes['cantidad'] ),
		'post__not_in'   => is_singular( 'proyecto' ) ? array( get_the_ID() ) : array(),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'categoria-portafolio',
				'field'    => 'slug',
				'terms'    => $impakto_cat,
			),
		),
	)
);
if ( ! $impakto_ids ) {
	return;
}
$impakto_termino = get_term_by( 'slug', $impakto_cat, 'categoria-portafolio' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'alignfull is-style-seccion-clara seccion-relacionados' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="contenedor">
		<div class="fila-encabezado">
			<div class="encabezado-seccion">
				<p class="kicker"><?php esc_html_e( 'Portafolio', 'impakto' ); ?></p>
				<h2><?php echo wp_kses( $attributes['titulo'], array( 'mark' => array( 'class' => true ) ) ); ?></h2>
			</div>
			<a class="boton boton--enlace" href="<?php echo esc_url( $impakto_termino ? get_term_link( $impakto_termino ) : home_url( '/portafolio/' ) ); ?>"><?php echo esc_html( $impakto_termino ? sprintf( /* translators: %s: categoría. */ __( 'Ver más de %s', 'impakto' ), $impakto_termino->name ) : __( 'Ver portafolio', 'impakto' ) ); ?></a>
		</div>
		<ul class="rejilla-proyectos">
			<?php foreach ( $impakto_ids as $impakto_id ) : ?>
				<?php
				$impakto_res     = function_exists( 'impakto_resultados' ) ? impakto_resultados( $impakto_id ) : array();
				$impakto_caso    = get_post_meta( $impakto_id, '_impakto_caso_exito', true ) && $impakto_res;
				$impakto_cliente = get_post_meta( $impakto_id, '_impakto_cliente', true );
				?>
				<li>
					<a class="tarjeta-proyecto" href="<?php echo esc_url( get_permalink( $impakto_id ) ); ?>">
						<div class="tarjeta-proyecto__media">
							<?php echo has_post_thumbnail( $impakto_id ) ? get_the_post_thumbnail( $impakto_id, 'impakto-proyecto', array( 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 33vw, 100vw' ) ) : '<div class="marcador-imagen" aria-hidden="true"></div>'; ?>
						</div>
						<div class="tarjeta-proyecto__cuerpo">
							<?php if ( $impakto_caso ) : ?>
								<p class="tarjeta-proyecto__cifra"><span><?php echo esc_html( $impakto_res[0]['metrica'] ); ?></span><strong><?php echo esc_html( $impakto_res[0]['valor'] ); ?></strong></p>
							<?php endif; ?>
							<?php if ( $impakto_cliente ) : ?>
								<p class="tarjeta-proyecto__cliente"><?php echo esc_html( $impakto_cliente ); ?></p>
							<?php endif; ?>
							<h3 class="tarjeta-proyecto__titulo"><?php echo esc_html( get_the_title( $impakto_id ) ); ?></h3>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
