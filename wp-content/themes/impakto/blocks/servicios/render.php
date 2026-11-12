<?php
/**
 * Tarjetas de servicios (CPT servicio). Texto de la tarjeta = extracto del servicio.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

// Modo manual: "Título | Texto | URL" por línea (p. ej. la franja de Inicio).
$impakto_tarjetas = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) $attributes['manual'] ) as $impakto_linea ) {
	$impakto_partes = array_map( 'trim', explode( '|', $impakto_linea ) );
	if ( count( $impakto_partes ) >= 3 && '' !== $impakto_partes[0] ) {
		$impakto_tarjetas[] = array(
			'titulo' => $impakto_partes[0],
			'texto'  => $impakto_partes[1],
			'url'    => str_starts_with( $impakto_partes[2], '/' ) ? home_url( $impakto_partes[2] ) : $impakto_partes[2],
		);
	}
}

if ( ! $impakto_tarjetas && post_type_exists( 'servicio' ) ) {

	$impakto_args = array(
		'post_type'      => 'servicio',
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, (int) $attributes['cantidad'] ),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'  => true,
	);
	$impakto_ids = array_filter( array_map( 'absint', explode( ',', (string) $attributes['ids'] ) ) );
	if ( $impakto_ids ) {
		$impakto_args['post__in'] = $impakto_ids;
		$impakto_args['orderby']  = 'post__in';
	} elseif ( $attributes['bloque'] ) {
		$impakto_args['meta_key']   = '_impakto_bloque'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$impakto_args['meta_value'] = $attributes['bloque']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	}
	foreach ( get_posts( $impakto_args ) as $impakto_s ) {
		$impakto_tarjetas[] = array(
			'titulo' => get_the_title( $impakto_s ),
			'texto'  => has_excerpt( $impakto_s ) ? get_the_excerpt( $impakto_s ) : '',
			'url'    => get_permalink( $impakto_s ),
		);
	}
}
if ( ! $impakto_tarjetas ) {
	return;
}
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'servicios-franja' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $impakto_tarjetas as $impakto_i => $impakto_t ) : ?>
		<li>
			<a class="tarjeta-servicio" href="<?php echo esc_url( $impakto_t['url'] ); ?>">
				<span class="tarjeta-servicio__numero" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $impakto_i + 1 ) ); ?></span>
				<h3 class="tarjeta-servicio__titulo"><?php echo esc_html( $impakto_t['titulo'] ); ?></h3>
				<?php if ( $impakto_t['texto'] ) : ?>
					<p class="tarjeta-servicio__texto"><?php echo esc_html( $impakto_t['texto'] ); ?></p>
				<?php endif; ?>
				<span class="tarjeta-servicio__flecha" aria-hidden="true"><?php echo impakto_icono( 'flecha-diagonal', 'icono--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
