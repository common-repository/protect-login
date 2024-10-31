<?php
/**
 * File routes.php
 *
 * Contains Remote API routes
 *
 * @since 2024-09-17
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

/**
 * Registers allowed routes in remote API
 *
 * @param string $prefix wp-remote-api prefix ( Namespace /protect-one / API-Version).
 *
 * @return void
 */
function protect_login_register_api_login_routes( string $prefix ) {
	$module = 'login/';

	register_rest_route(
		$prefix . $module,
		'/list-blocked-addresses',
		array(
			'methods'             => 'POST',
			'callback'            => array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\RestApi', 'list_blocked_addresses' ),
			'permission_callback' => 'protect_login_api_key_check',
		)
	);

	register_rest_route(
		$prefix . $module,
		'/list-allowlisted-addresses',
		array(
			'methods'             => 'POST',
			'callback'            => array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\RestApi', 'list_allowlisted_addresses' ),
			'permission_callback' => 'protect_login_api_key_check',
		)
	);

	register_rest_route(
		$prefix . $module,
		'/list-blocklisted-addresses',
		array(
			'methods'             => 'POST',
			'callback'            => array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\RestApi', 'list_blocklisted_addresses' ),
			'permission_callback' => 'protect_login_api_key_check',
		)
	);

	register_rest_route(
		$prefix . $module,
		'/lockout',
		array(
			'methods'             => 'POST',
			'callback'            => array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\RestApi', 'lockout' ),
			'permission_callback' => 'protect_login_api_key_check',
		)
	);

	register_rest_route(
		$prefix . $module,
		'/release',
		array(
			'methods'             => 'POST',
			'callback'            => array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\RestApi', 'release' ),
			'permission_callback' => 'protect_login_api_key_check',
		)
	);

	register_rest_route(
		$prefix . $module,
		'/unblock',
		array(
			'methods'             => 'POST',
			'callback'            => array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\RestApi', 'unblock' ),
			'permission_callback' => 'protect_login_api_key_check',
		)
	);

	register_rest_route(
		$prefix . $module,
		'/remove',
		array(
			'methods'             => 'POST',
			'callback'            => array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\RestApi', 'remove' ),
			'permission_callback' => 'protect_login_api_key_check',
		)
	);
}
