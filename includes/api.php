<?php
/**
 * File api.php
 *
 * Contains functions for initalizing the Remote API component
 *
 * @since 2024-09-16
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

use ProtectLogin\Modules\LimitLoginAttempts\Controllers\LoginHandler;
use ProtectLogin\Modules\RemoteApi\Actions\ApiClient;

/**
 * Checks if the sent API_KEY in WP_REST_Request matches the configured API key
 *
 * @param WP_REST_Request $request wp-json - Request.
 *
 * @return bool
 */
function protect_login_api_key_check( WP_REST_Request $request ): bool {
	$current_api_key = get_option( 'protect_login_remote_api_host_key', null );
	if ( null === $current_api_key ) {
		return false;
	}

	if ( strlen( $current_api_key ) < 16 ) {
		return false;
	}

	if ( '' === $request->get_header( 'api_key' ) ) {
		return false;
	}

	$sent_api_key = $request->get_header( 'api_key' );
	return $sent_api_key === $current_api_key;
}

/**
 * Main function to register the endpoints for REST API
 *
 * @return void
 */
function protect_login_register_api_endpoints() {
	$route_slug = 'protect-one';
	$version_10 = '1.0';

	$prefix = $route_slug . '/' . $version_10 . '/';

	protect_login_register_api_login_routes( $prefix );
}

/**
 * Checks if this instance is in sync with Remote instance of Protect Login.
 * This is, if last sync is newer than 6 hours.
 * If we are not in sync, download list data from remote and merge into local list.
 *
 * @return void
 */
function protect_login_sync_lists_from_remote_api() {
	if ( false !== get_transient( 'protect_login_is_in_sync' ) ) {
		$lockedout_addresses   = LoginHandler::get_current_lockedout_address();
		$blocklisted_addresses = get_option( 'protect_login_limit_login_blocklist', array() );
		$allowlisted_addresses = get_option( 'protect_login_limit_login_allowlist', array() );

		$api = ApiClient::get_instance();

		$remote_lockedout_addresses = $api->get_remote_blocked_ips();
		foreach ( $remote_lockedout_addresses as $current_address ) {
			if ( ! in_array( $current_address, $lockedout_addresses ) ) {
				protect_login_lockout_on_multisite( $current_address );
			}
		}

		$remote_blocklisted_addresses = $api->get_remote_blocklisted_ips();
		protect_login_update_option_on_mulitsite(
			'protect_login_limit_login_blocklist',
			array_unique( $remote_blocklisted_addresses, $blocklisted_addresses )
		);

		$remote_allowlisted_addresses = $api->get_remote_allowlisted_ips();
		protect_login_update_option_on_mulitsite(
			'protect_login_limit_login_allowlist',
			array_unique( $remote_allowlisted_addresses, $allowlisted_addresses )
		);

		protect_login_set_transient_on_mulitsite(
			'protect_login_is_in_sync',
			true,
			6 * HOUR_IN_SECONDS
		);
	}
}
