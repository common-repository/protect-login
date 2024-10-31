<?php
/**
 * File class-savehostkey.php
 *
 * Action for updating the Host key
 *
 * @since 2024-09-16
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

namespace ProtectLogin\Modules\RemoteApi\Actions;

/**
 * Class to update the host key
 */
class SaveHostKey {

	/**
	 * Updates the given API key for the network options
	 *
	 * @param string $host_key New API key for network.
	 *
	 * @return void
	 */
	public static function execute( string $host_key ) {
		protect_login_update_option_on_mulitsite( 'protect_login_remote_api_host_key', $host_key );
	}
}
