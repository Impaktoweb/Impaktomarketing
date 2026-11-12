<?php
/**
 * Guía de estilo: tokens de color (con contraste calculado), tipografía y espaciado.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'impakto_contraste' ) ) {
	/**
	 * Relación de contraste WCAG entre dos colores hex.
	 *
	 * @param string $a Hex.
	 * @param string $b Hex.
	 * @return float
	 */
	function impakto_contraste( $a, $b ) {
		$lum = static function ( $hex ) {
			$hex = ltrim( $hex, '#' );
			$rgb = array_map( static fn( $c ) => hexdec( $c ) / 255, str_split( $hex, 2 ) );
			$rgb = array_map( static fn( $c ) => $c <= 0.03928 ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4, $rgb );
			return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
		};
		$l1 = $lum( $a );
		$l2 = $lum( $b );
		return ( max( $l1, $l2 ) + 0.05 ) / ( min( $l1, $l2 ) + 0.05 );
	}
}

$impakto_wrapper = get_block_wrapper_attributes( array( 'class' => 'guia guia--' . $attributes['seccion'] ) );

if ( 'color' === $attributes['seccion'] ) :
	$impakto_colores = array(
		array( '--orange-500', '#FF8000', 'Acentos, números, botones, líneas' ),
		array( '--orange-700', '#B35600', 'Texto naranja pequeño sobre claro' ),
		array( '--graphite-900', '#111110', 'Fondos oscuros' ),
		array( '--graphite-800', '#2B2B29', 'Fondos oscuros secundarios' ),
		array( '--graphite-700', '#373734', 'Titulares sobre claro, fondos de sección' ),
		array( '--gray-600', '#5E5E5C', 'Texto de párrafo' ),
		array( '--gray-300', '#CFCFCB', 'Líneas finas y bordes' ),
		array( '--gray-100', '#F1F1EF', 'Fondo de sección claro' ),
		array( '--gray-50', '#F7F7F5', 'Fondo general (gris papel)' ),
		array( '--white', '#FFFFFF', 'Superficies y tarjetas' ),
	);
	$impakto_pares = array(
		array( '#5E5E5C', '#F7F7F5', 'Párrafo sobre fondo papel', 4.5 ),
		array( '#373734', '#F7F7F5', 'Titular sobre fondo papel', 4.5 ),
		array( '#B35600', '#F7F7F5', 'Naranja 700 (texto pequeño) sobre papel', 4.5 ),
		array( '#B35600', '#FFFFFF', 'Naranja 700 sobre blanco', 4.5 ),
		array( '#FF8000', '#FFFFFF', 'Naranja 500 sobre blanco (solo ≥ 24 px)', 3.0 ),
		array( '#111110', '#FF8000', 'Texto de botón sobre naranja', 4.5 ),
		array( '#FF8000', '#111110', 'Naranja como texto sobre oscuro', 4.5 ),
		array( '#FFFFFF', '#111110', 'Blanco sobre oscuro', 4.5 ),
		array( '#FFFFFF', '#FF8000', 'Blanco sobre naranja (solo títulos ≥ 24 px)', 3.0 ),
	);
	?>
	<div <?php echo $impakto_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<ul class="guia__colores">
			<?php foreach ( $impakto_colores as list( $impakto_var, $impakto_hex, $impakto_uso ) ) : ?>
				<li>
					<span class="guia__muestra" style="background: <?php echo esc_attr( $impakto_hex ); ?>"></span>
					<code><?php echo esc_html( $impakto_var ); ?></code>
					<strong><?php echo esc_html( $impakto_hex ); ?></strong>
					<span><?php echo esc_html( $impakto_uso ); ?></span>
				</li>
			<?php endforeach; ?>
			<li>
				<span class="guia__muestra" style="background: var(--orange-gradient)"></span>
				<code>--orange-gradient</code><strong><?php esc_html_e( 'Degradado radial', 'impakto' ); ?></strong><span><?php esc_html_e( 'Sección naranja con texto blanco', 'impakto' ); ?></span>
			</li>
		</ul>
		<table class="guia__contraste">
			<caption><?php esc_html_e( 'Contraste WCAG calculado', 'impakto' ); ?></caption>
			<thead><tr><th scope="col"><?php esc_html_e( 'Muestra', 'impakto' ); ?></th><th scope="col"><?php esc_html_e( 'Uso', 'impakto' ); ?></th><th scope="col"><?php esc_html_e( 'Relación', 'impakto' ); ?></th><th scope="col"><?php esc_html_e( 'Mínimo', 'impakto' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( $impakto_pares as list( $impakto_fg, $impakto_bg, $impakto_uso, $impakto_min ) ) : ?>
					<?php $impakto_r = impakto_contraste( $impakto_fg, $impakto_bg ); ?>
					<tr>
						<td><span class="guia__par" style="color: <?php echo esc_attr( $impakto_fg ); ?>; background: <?php echo esc_attr( $impakto_bg ); ?>">Aa</span></td>
						<td><?php echo esc_html( $impakto_uso ); ?></td>
						<td><strong><?php echo esc_html( number_format( $impakto_r, 2, ',', '' ) . ':1' ); ?></strong></td>
						<td><?php echo esc_html( ( $impakto_r >= $impakto_min ? '✔ ' : '✘ ' ) . number_format( $impakto_min, 1, ',', '' ) . ':1' ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php elseif ( 'tipografia' === $attributes['seccion'] ) : ?>
	<div <?php echo $impakto_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
		$impakto_tipos = array(
			array( 'Display · 300 · 48–160 px', '<p class="is-style-display guia__display" style="margin:0">Naming</p>' ),
			array( 'H1 · 600 · 36–72 px · 1,05', '<p class="guia__h1">Características <mark class="acento">del Estudio</mark></p>' ),
			array( 'H2 · 600 · 28–48 px', '<p class="guia__h2">Un solo aliado para <mark class="acento">todo el proceso comercial</mark></p>' ),
			array( 'H3 · 600 · 20–24 px', '<p class="guia__h3">Estrategia que entiende a las personas</p>' ),
			array( 'Bajada · 400 · 19–22 px', '<p class="is-style-bajada">Naming, branding, marketing digital y producción audiovisual, conectados con un mismo objetivo: vender más.</p>' ),
			array( 'Párrafo · 400 · 17–18 px · 1,6', '<p>Somos la agencia de Manizales que acompaña todo tu proceso comercial: investigación, marca, producción audiovisual, marketing digital y ventas. Con estudio y sala de podcast propios.</p>' ),
			array( 'Kicker · 500 · 12–13 px · 0,2 em', '<p class="is-style-kicker">The Marketing Factory · Manizales</p>' ),
			array( 'Número de paso · 300 · 48–64 px', '<p class="guia__paso">01 02 03</p>' ),
			array( 'Nota · 400 · 13 px', '<p class="is-style-nota">Pauta en Meta, 1 feb – 18 mar 2026</p>' ),
		);
		foreach ( $impakto_tipos as list( $impakto_etq, $impakto_html ) ) :
			?>
			<div class="guia__tipo">
				<p class="guia__etiqueta"><?php echo esc_html( $impakto_etq ); ?></p>
				<?php echo wp_kses_post( $impakto_html ); ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<div <?php echo $impakto_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<ul class="guia__espacios">
			<?php foreach ( array( 4, 8, 12, 16, 24, 32, 48, 64, 96, 128 ) as $impakto_i => $impakto_px ) : ?>
				<li><span style="width: <?php echo esc_attr( $impakto_px ); ?>px"></span><code>--s-<?php echo esc_html( $impakto_i + 1 ); ?></code> <?php echo esc_html( $impakto_px ); ?> px</li>
			<?php endforeach; ?>
		</ul>
		<ul class="guia__formas">
			<li><span style="border-radius: 4px"></span><?php esc_html_e( 'Campos · 4 px', 'impakto' ); ?></li>
			<li><span style="border-radius: 12px"></span><?php esc_html_e( 'Tarjetas · 12 px', 'impakto' ); ?></li>
			<li><span style="border-radius: 999px"></span><?php esc_html_e( 'Chips, pills y botones · completo', 'impakto' ); ?></li>
		</ul>
		<p class="is-style-nota"><?php esc_html_e( 'Retícula: 4 columnas en móvil, 8 en tableta y 12 en escritorio · margen lateral 16 / 24 / 40 px · contenedor 1280 px · texto largo 760 px · puntos de corte 768 · 1024 · 1280 · 1440 px · zonas táctiles ≥ 44 × 44 px · animación 300 ms (fade + 16 px).', 'impakto' ); ?></p>
	</div>
<?php endif; ?>
