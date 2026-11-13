<?php
/**
 * Blog: tiempo de lectura, anclas de los H2 para la tabla de contenido y tarjeta de artículo.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Minutos de lectura (220 palabras por minuto).
 *
 * @param int|WP_Post $post Entrada.
 * @return int
 */
function impakto_minutos_lectura( $post ) {
	$texto = wp_strip_all_tags( (string) get_post_field( 'post_content', $post ) );
	return max( 1, (int) round( count( preg_split( '/\s+/u', trim( $texto ) ) ) / 220 ) );
}

/**
 * Id de ancla a partir del texto de un encabezado.
 *
 * @param string $texto Texto.
 * @return string
 */
function impakto_ancla( $texto ) {
	return sanitize_title( wp_strip_all_tags( $texto ) );
}

/**
 * Los H2 de los artículos reciben un id para la tabla de contenido.
 */
add_filter(
	'render_block_core/heading',
	static function ( $html, $bloque ) {
		if ( ! is_singular( 'post' ) || 2 !== (int) ( $bloque['attrs']['level'] ?? 2 ) || str_contains( $html, ' id="' ) ) {
			return $html;
		}
		$texto = wp_strip_all_tags( $html );
		return preg_replace( '/<h2\b/', '<h2 id="' . esc_attr( impakto_ancla( $texto ) ) . '"', $html, 1 );
	},
	10,
	2
);

/**
 * Encabezados H2 de un artículo (texto + ancla).
 *
 * @param int $post_id Id.
 * @return array<int,array{texto:string,ancla:string}>
 */
function impakto_indice_articulo( $post_id ) {
	$salida = array();
	foreach ( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ) as $b ) {
		if ( 'core/heading' === $b['blockName'] && 2 === (int) ( $b['attrs']['level'] ?? 2 ) ) {
			$texto    = trim( wp_strip_all_tags( $b['innerHTML'] ) );
			$salida[] = array(
				'texto' => $texto,
				'ancla' => impakto_ancla( $texto ),
			);
		}
	}
	return $salida;
}

/**
 * Tarjeta de artículo.
 *
 * @param WP_Post $p Entrada.
 * @return string
 */
function impakto_tarjeta_articulo( $p ) {
	$cat     = get_the_category( $p->ID );
	$minutos = impakto_minutos_lectura( $p );
	ob_start();
	?>
	<article class="tarjeta-articulo">
		<div class="tarjeta-articulo__media">
			<?php echo has_post_thumbnail( $p ) ? get_the_post_thumbnail( $p, 'impakto-articulo', array( 'loading' => 'lazy', 'sizes' => '(min-width: 768px) 33vw, 100vw' ) ) : '<div class="marcador-imagen" style="--ratio: 16 / 9" aria-hidden="true"></div>'; ?>
		</div>
		<p class="tarjeta-articulo__meta">
			<?php if ( $cat ) : ?><span><?php echo esc_html( $cat[0]->name ); ?></span><?php endif; ?>
			<span><?php echo esc_html( sprintf( /* translators: %d: minutos. */ __( '%d min de lectura', 'impakto' ), $minutos ) ); ?></span>
		</p>
		<h3 class="tarjeta-articulo__titulo"><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></h3>
		<?php if ( has_excerpt( $p ) ) : ?>
			<p class="tarjeta-articulo__extracto"><?php echo esc_html( wp_trim_words( get_the_excerpt( $p ), 22 ) ); ?></p>
		<?php endif; ?>
	</article>
	<?php
	return (string) ob_get_clean();
}
