<?php
/**
 * File class-randompasswortrequest.php
 *
 * Helper for creating a random string
 *
 * @since 2024-09-04
 * @license GPL-3.0-or-later
 *
 * @package Protect-Login/RemoteApi
 */

namespace ProtectLogin\Modules\RemoteApi\Requests;

/**
 * Helper for creating a random string
 */
class RandomPasswortRequest {

	/**
	 * Generates a random string
	 *
	 * @param int $length Length of the string to generate.
	 *
	 * @return string
	 */
	public static function generate( int $length = 128 ): string {
		$characters = '-_.,:=0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$randstring .= $characters[ wp_rand( 0, strlen( $characters ) - 1 ) ];
		}

		return $randstring;
	}
}
