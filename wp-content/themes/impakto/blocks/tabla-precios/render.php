<?php
/**
 * Tablas de tarifas en HTML real (BRIEF §13: precios en texto, nunca solo en imágenes).
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'impakto_tarifas' ) ) {
	return;
}
$impakto_t = impakto_tarifas();
$impakto_c = static fn( $v ) => impakto_tema_cop( (int) $v, false );
$impakto_w = get_block_wrapper_attributes( array( 'class' => 'wp-block-table is-style-tarifas tabla-precios' ) );

if ( 'estudio' === $attributes['tabla'] ) :
	$impakto_e = $impakto_t['estudio']['espacios'] ?? array();
	?>
	<figure <?php echo $impakto_w; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<table>
			<thead><tr><th scope="col"></th><?php foreach ( $impakto_e as $impakto_x ) : ?><th scope="col"><?php echo esc_html( $impakto_x['nombre'] ); ?></th><?php endforeach; ?></tr></thead>
			<tbody>
				<tr><th scope="row"><?php esc_html_e( 'Área', 'impakto' ); ?></th><?php foreach ( $impakto_e as $impakto_x ) : ?><td><?php echo esc_html( $impakto_x['area_m2'] . ' m²' ); ?></td><?php endforeach; ?></tr>
				<tr><th scope="row"><?php esc_html_e( 'Fondo', 'impakto' ); ?></th><?php foreach ( $impakto_e as $impakto_x ) : ?><td><?php echo esc_html( trim( explode( '·', $impakto_x['descripcion'] )[0] ) ); ?></td><?php endforeach; ?></tr>
				<?php foreach ( array( 1, 4, 8 ) as $impakto_h ) : ?>
					<tr class="es-precio"><th scope="row"><?php echo esc_html( 1 === $impakto_h ? '1 hora' : $impakto_h . ' horas' ); ?></th><?php foreach ( $impakto_e as $impakto_x ) : ?><td><?php echo esc_html( $impakto_c( $impakto_x['bloques'][ $impakto_h ] ?? 0 ) ); ?></td><?php endforeach; ?></tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<figcaption class="wp-element-caption"><?php esc_html_e( 'Valores en COP, más IVA.', 'impakto' ); ?></figcaption>
	</figure>
<?php elseif ( 'podcast' === $attributes['tabla'] ) : ?>
	<figure <?php echo $impakto_w; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<table>
			<thead><tr><th scope="col"><?php esc_html_e( 'Paquete', 'impakto' ); ?></th><th scope="col"><?php esc_html_e( 'Valor por hora', 'impakto' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( $impakto_t['podcast']['paquetes'] ?? array() as $impakto_p ) : ?>
					<tr class="es-precio"><td><?php echo esc_html( $impakto_p['nombre'] ); ?></td><td><?php echo esc_html( $impakto_c( $impakto_p['precio_hora'] ) ); ?></td></tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<figcaption class="wp-element-caption"><?php echo esc_html( sprintf( /* translators: %d: horas. */ __( 'Valores en COP, más IVA. Tarifa publicada hasta %d horas.', 'impakto' ), (int) ( $impakto_t['podcast']['horas_tarifa_publicada'] ?? 4 ) ) ); ?></figcaption>
	</figure>
<?php else : ?>
	<?php $impakto_ed = $impakto_t['podcast']['edicion'] ?? array(); ?>
	<figure <?php echo $impakto_w; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<table>
			<thead><tr><th scope="col"><?php esc_html_e( 'Horas grabadas', 'impakto' ); ?></th><th scope="col"><?php esc_html_e( 'Edición estándar', 'impakto' ); ?></th><th scope="col"><?php esc_html_e( 'Edición avanzada', 'impakto' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( array_keys( $impakto_ed['estandar'] ?? array() ) as $impakto_h ) : ?>
					<tr class="es-precio"><td><?php echo esc_html( 1 === (int) $impakto_h ? '1 hora' : $impakto_h . ' horas' ); ?></td><td><?php echo esc_html( $impakto_c( $impakto_ed['estandar'][ $impakto_h ] ) ); ?></td><td><?php echo esc_html( $impakto_c( $impakto_ed['avanzada'][ $impakto_h ] ?? 0 ) ); ?></td></tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<figcaption class="wp-element-caption"><?php esc_html_e( 'Valores en COP, más IVA.', 'impakto' ); ?></figcaption>
	</figure>
<?php endif; ?>
