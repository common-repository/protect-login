<?php
/**
 * File class-saveendpointsettings.php
 *
 * Saves information about the new endpoint of the ProtectLogin newtwork
 *
 * @since 2024-09-16
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

namespace ProtectLogin\Modules\RemoteApi\Actions;

use ProtectLogin\Modules\LimitLoginAttempts\Controllers\LoginHandler;

/**
 * Saves information about the remote endpoint of ProtectLogin Remote API
 */
class SaveEndpointSettings {

	/**
	 * Saves information about the remote endpoint of ProtectLogin Remote API
	 *
	 * @param string $endpoint_url URL of remote Endpoint.
	 * @param string $endpoint_key API Key of remote endpoint.
	 *
	 * @return void
	 */
	public static function execute( string $endpoint_url, string $endpoint_key ) {

		protect_login_update_option_on_mulitsite( 'protect_login_remote_api_endpoint_url', $endpoint_url );
		protect_login_update_option_on_mulitsite( 'protect_login_remote_api_endpoint_key', $endpoint_key );

		if ( '' !== $endpoint_url && '' !== $endpoint_key ) {
			$api_client = ApiClient::get_instance();
			$api_client->block_addresses(
				get_option( 'protect_login_limit_login_blocklist', array() )
			);

			$api_client->allow_addresses(
				get_option( 'protect_login_limit_login_allowlist', array() )
			);

			foreach ( LoginHandler::get_current_lockedout_address() as $current_lockedout_address ) {
				$api_client->block_remote_address( $current_lockedout_address, false );
			}

			protect_login_delete_transient_on_multisite( 'protect_login_is_in_sync' );
			protect_login_sync_lists_from_remote_api();
		}
	}
}
