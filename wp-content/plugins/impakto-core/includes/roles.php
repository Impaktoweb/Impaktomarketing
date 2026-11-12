<?php
/**
 * Capacidades propias: Administrador y Editor pueden ver Impakto → Solicitudes (Fase 5).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Añade la capacidad impakto_solicitudes.
 */
function impakto_roles_activar() {
	foreach ( array( 'administrator', 'editor' ) as $rol_nombre ) {
		$rol = get_role( $rol_nombre );
		if ( $rol && ! $rol->has_cap( 'impakto_solicitudes' ) ) {
			$rol->add_cap( 'impakto_solicitudes' );
		}
	}
}
