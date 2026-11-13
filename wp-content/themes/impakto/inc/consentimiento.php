<?php
/**
 * Consentimiento de cookies (BRIEF §12): banner propio, Google Consent Mode v2
 * (todo "denied" por defecto) y carga de GA4 / Pixel de Meta solo con consentimiento.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Consent Mode v2 por defecto, antes de cualquier etiqueta.
 */
add_action(
	'wp_head',
	static function () {
		?>
<script>
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',functionality_storage:'granted',security_storage:'granted',wait_for_update:500});
(function(){var m=document.cookie.match(/(?:^|; )impakto_consent=([^;]+)/);if(!m)return;try{var c=JSON.parse(decodeURIComponent(m[1]));gtag('consent','update',{analytics_storage:c.a?'granted':'denied',ad_storage:c.p?'granted':'denied',ad_user_data:c.p?'granted':'denied',ad_personalization:c.p?'granted':'denied'});}catch(e){}})();
</script>
		<?php
		$gsc = impakto_dato( 'gsc_verificacion' );
		if ( $gsc ) {
			printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $gsc ) );
		}
	},
	0
);

/**
 * Banner y panel de preferencias.
 */
add_action(
	'wp_footer',
	static function () {
		$config = array(
			'ga4'    => preg_match( '/^G-[A-Z0-9]+$/', impakto_dato( 'ga4_id' ) ) ? impakto_dato( 'ga4_id' ) : '',
			'pixel'  => preg_match( '/^\d{6,20}$/', impakto_dato( 'meta_pixel_id' ) ) ? impakto_dato( 'meta_pixel_id' ) : '',
			'cookies' => home_url( '/politica-de-cookies/' ),
		);
		?>
<div class="cookies" id="cookies" role="region" aria-label="<?php esc_attr_e( 'Preferencias de cookies', 'impakto' ); ?>" data-cookies data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>" hidden>
	<div class="cookies__caja">
		<p class="cookies__texto"><?php echo wp_kses( sprintf( /* translators: %s: URL. */ __( 'Usamos cookies propias para que el sitio funcione y, si aceptas, cookies de analítica y de publicidad para medir visitas y campañas. <a href="%s">Política de cookies</a>.', 'impakto' ), esc_url( $config['cookies'] ) ), array( 'a' => array( 'href' => true ) ) ); ?></p>
		<div class="cookies__panel" id="cookies-panel" hidden>
			<label class="interruptor"><input type="checkbox" role="switch" checked disabled><span class="interruptor__pista" aria-hidden="true"></span><span><strong><?php esc_html_e( 'Necesarias', 'impakto' ); ?></strong> <small><?php esc_html_e( 'Siempre activas: funcionamiento, seguridad y tus preferencias.', 'impakto' ); ?></small></span></label>
			<label class="interruptor"><input type="checkbox" role="switch" data-cookie="a"><span class="interruptor__pista" aria-hidden="true"></span><span><strong><?php esc_html_e( 'Analítica', 'impakto' ); ?></strong> <small><?php esc_html_e( 'Google Analytics 4 y origen de campaña (90 días).', 'impakto' ); ?></small></span></label>
			<label class="interruptor"><input type="checkbox" role="switch" data-cookie="p"><span class="interruptor__pista" aria-hidden="true"></span><span><strong><?php esc_html_e( 'Publicidad', 'impakto' ); ?></strong> <small><?php esc_html_e( 'Pixel de Meta para medir campañas.', 'impakto' ); ?></small></span></label>
		</div>
		<div class="cookies__botones">
			<button type="button" class="boton boton--sm" data-cookies-aceptar><?php esc_html_e( 'Aceptar', 'impakto' ); ?></button>
			<button type="button" class="boton boton--sm boton--secundario" data-cookies-rechazar><?php esc_html_e( 'Rechazar', 'impakto' ); ?></button>
			<button type="button" class="boton boton--sm boton--enlace" data-cookies-configurar aria-expanded="false" aria-controls="cookies-panel"><?php esc_html_e( 'Configurar', 'impakto' ); ?></button>
			<button type="button" class="boton boton--sm" data-cookies-guardar hidden><?php esc_html_e( 'Guardar preferencias', 'impakto' ); ?></button>
		</div>
	</div>
</div>
		<?php
	},
	5
);
