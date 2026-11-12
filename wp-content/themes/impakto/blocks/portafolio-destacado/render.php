<?php
/**
 * Portafolio en bento: tarjeta grande si el proyecto es "Destacado".
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( ! post_type_exists( 'proyecto' ) ) {
	return;
}

$impakto_args = array(
	'post_type'      => 'proyecto',
	'post_status'    => 'publish',
	'posts_per_page' => max( 1, (int) $attributes['cantidad'] ),
	'orderby'        => array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	),
	'no_found_rows'  => true,
);
if ( $attributes['soloDestacados'] ) {
	$impakto_args['meta_key']   = '_impakto_destacado'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	$impakto_args['meta_value'] = '1'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
}
if ( $attributes['categoria'] ) {
	$impakto_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'categoria-portafolio',
			'field'    => 'slug',
			'terms'    => $attributes['categoria'],
		),
	);
}
$impakto_proyectos = get_posts( $impakto_args );
if ( ! $impakto_proyectos ) {
	return;
}
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'bento' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
	// Composición del bento según la cantidad: el primero siempre va grande.
	$impakto_total   = count( $impakto_proyectos );
	$impakto_anchos  = array(
		2 => array( 1 => 'es-alta' ),
		3 => array(),
		4 => array( 1 => 'es-ancha' ),
		6 => array( 5 => 'es-completa' ),
	);
	foreach ( $impakto_proyectos as $impakto_i => $impakto_p ) :
		$impakto_grande     = 0 === $impakto_i;
		$impakto_extra      = $impakto_anchos[ $impakto_total ][ $impakto_i ] ?? '';
		$impakto_resultados = function_exists( 'impakto_resultados' ) ? impakto_resultados( $impakto_p->ID ) : array();
		$impakto_caso       = get_post_meta( $impakto_p->ID, '_impakto_caso_exito', true ) && $impakto_resultados;
		$impakto_cliente    = get_post_meta( $impakto_p->ID, '_impakto_cliente', true );
		$impakto_cats       = get_the_terms( $impakto_p, 'categoria-portafolio' );
		?>
		<li class="<?php echo esc_attr( trim( ( $impakto_grande ? 'es-grande ' : '' ) . $impakto_extra ) ); ?>">
			<a class="tarjeta-proyecto" href="<?php echo esc_url( get_permalink( $impakto_p ) ); ?>">
				<div class="tarjeta-proyecto__media">
					<?php
					if ( has_post_thumbnail( $impakto_p ) ) {
						echo get_the_post_thumbnail( $impakto_p, $impakto_grande ? 'impakto-destacado' : 'impakto-proyecto', array( 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw' ) );
					} else {
						echo '<div class="marcador-imagen" aria-hidden="true"></div>';
					}
					?>
				</div>
				<div class="tarjeta-proyecto__cuerpo">
					<?php if ( $impakto_caso ) : ?>
						<p class="tarjeta-proyecto__cifra"><span><?php echo esc_html( $impakto_resultados[0]['metrica'] ); ?></span><strong><?php echo esc_html( $impakto_resultados[0]['valor'] ); ?></strong></p>
					<?php endif; ?>
					<?php if ( $impakto_cliente ) : ?>
						<p class="tarjeta-proyecto__cliente"><?php echo esc_html( $impakto_cliente ); ?></p>
					<?php endif; ?>
					<h3 class="tarjeta-proyecto__titulo"><?php echo esc_html( get_the_title( $impakto_p ) ); ?></h3>
					<?php if ( $impakto_cats && ! is_wp_error( $impakto_cats ) ) : ?>
						<ul class="is-style-pills">
							<?php foreach ( array_slice( $impakto_cats, 0, 3 ) as $impakto_c ) : ?>
								<li><?php echo esc_html( $impakto_c->name ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
