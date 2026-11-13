<?php
/**
 * Aviso interno del formulario de contacto.
 *
 * @var array<string,mixed> $datos datos, pagina, utm.
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

$c = $datos['datos'];
ob_start();
?>
<p style="margin:0 0 4px;color:#B35600;font-size:12px;letter-spacing:2px;text-transform:uppercase;">Nuevo contacto desde el sitio</p>
<h1 style="margin:0 0 16px;font-size:24px;color:#111110;"><?php echo esc_html( $c['nombre'] ); ?></h1>
<table role="presentation" cellspacing="0" cellpadding="0" style="font-size:14px;">
	<?php
	$filas = array(
		'Empresa'  => $c['empresa'],
		'Correo'   => $c['correo'],
		'Celular'  => $c['celular'],
		'Servicio' => $c['servicio'],
		'Página'   => $datos['pagina'],
		'Origen'   => implode( ' · ', array_map( static fn( $k, $v ) => "$k=$v", array_keys( $datos['utm'] ), $datos['utm'] ) ),
	);
	foreach ( array_filter( $filas ) as $k => $v ) :
		?>
		<tr><td style="padding:3px 12px 3px 0;color:#5E5E5C;"><?php echo esc_html( $k ); ?></td><td><?php echo esc_html( $v ); ?></td></tr>
	<?php endforeach; ?>
</table>
<?php if ( $c['mensaje'] ) : ?>
	<p style="margin:16px 0 0;padding:12px 16px;background:#F7F7F5;border-left:3px solid #FF8000;"><?php echo nl2br( esc_html( $c['mensaje'] ) ); ?></p>
<?php endif; ?>
<p style="margin:20px 0 0;"><a href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D+/', '', $c['celular'] ) ); ?>" style="color:#B35600;font-weight:bold;">Responder por WhatsApp</a></p>
<?php
$datos['contenido'] = ob_get_clean();
$datos['titulo']    = 'Nuevo contacto';
include __DIR__ . '/base.php';
