<?php
/**
 * File class-restapi.php
 *
 * DESCRIPTION
 *
 * @since 2024-09-16
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

namespace ProtectLogin\Modules\LimitLoginAttempts\Controllers;

use WP_REST_Request;

/**
 * Contains endpoints for remote API for address handling
 */
class RestApi {
	/**
	 * Returns array of allow-listed addresses
	 *
	 * @return array
	 */
	public static function list_allowlisted_addresses() {
		$items = array();

		$allowlisted_addresses = get_option( 'protect_login_limit_login_allowlist', array() );
		foreach ( $allowlisted_addresses as $address ) {
			$address = str_replace( '-', '.', $address );
			$items[] = $address;
		}

		return $items;
	}

	/**
	 * Returns array of blocklisted addresses
	 *
	 * @return array
	 */
	public static function list_blocklisted_addresses() {
		$items = array();

		$allowlisted_addresses = get_option( 'protect_login_limit_login_blocklist', array() );
		foreach ( $allowlisted_addresses as $address ) {
			$address = str_replace( '-', '.', $address );
			$items[] = $address;
		}

		return $items;
	}

	/**
	 * Returns array of all addresses, that are not allowed to login at this time
	 *
	 * @return array
	 */
	public static function list_blocked_addresses() {
		$items = array();

		$blocked_ips = get_option( 'protect_login_limit_login_blocklist', array() );
		foreach ( $blocked_ips as $ip ) {
			$ip      = str_replace( '-', '.', $ip );
			$items[] = $ip;
		}

		return array_unique( array_merge( $items, LoginHandler::get_current_lockedout_address() ) );
	}

	/**
	 * Endpoint for locking out an address
	 *
	 * @param WP_REST_Request $request wp-json - Request.
	 *
	 * @return string
	 */
	public static function lockout( WP_REST_Request $request ) {
		if ( ! $request->has_param( 'addresses' ) || ! $request->has_param( 'permanently' ) ) {
			return 'Missing parameters $address or $permanently';
		}

		$addresses   = $request->get_param( 'addresses' );
		$permanently = $request->get_param( 'permanently' );

		foreach ( $addresses as $address ) {
			if ( $permanently ) {
				protect_login_add_to_list_on_multisite( 'blocklist', $address );
			} else {
				protect_login_lockout_on_multisite( $addresses );
			}
		}

		return 'Ok';
	}

	/**
	 * Endpoint for releasing an address
	 *
	 * @param WP_REST_Request $request wp-json - Request.
	 *
	 * @return string
	 */
	public static function release( WP_REST_Request $request ) {
		if ( ! $request->has_param( 'addresses' ) || ! $request->has_param( 'permanently' ) ) {
			return 'Missing parameters $address or $permanently';
		}

		$addresses   = $request->get_param( 'addresses' );
		$permanently = $request->get_param( 'permanently' );

		$addresses = array_unique( $addresses );
		foreach ( $addresses as $address ) {
			if ( $permanently ) {
				protect_login_add_to_list_on_multisite( 'allowlist', $address );
			} else {
				protect_login_release_on_multisite( $addresses );
			}
		}

		return 'Ok';
	}

	/**
	 * Endpoint for removing an address from a list
	 *
	 * @param WP_REST_Request $request wp-json - Request.
	 *
	 * @return string
	 */
	public static function remove( WP_REST_Request $request ) {
		if ( ! $request->has_param( 'address' ) || ! $request->has_param( 'listtype' ) ) {
			return 'Missing parameters $address or $listtype';
		}

		$address  = $request->get_param( 'address' );
		$listtype = $request->get_param( 'listtype' );

		if ( ! in_array( $listtype, array( 'allowlist', 'blocklist' ), true ) ) {
			return '$lisstype must be <allowlist|blocklist>';
		}

		protect_login_update_option_on_mulitsite(
			'protect_login_limit_login_' . $listtype,
			array_diff(
				get_option( 'protect_login_limit_login_' . $listtype, array() ),
				array( $address, str_replace( '.', '-', $address ) )
			)
		);

		return 'ok';
	}

	/**
	 * Endpoint for unblocking an address
	 *
	 * @param WP_REST_Request $request wp-json - Request.
	 *
	 * @return string
	 */
	public static function unblock( WP_REST_Request $request ) {
		if ( ! $request->has_param( 'address' ) ) {
			return 'Missing parameters $address';
		}

		$address = $request->get_param( 'address' );

		$list_type = 'blocklist';
		protect_login_update_option_on_mulitsite(
			'protect_login_limit_login_' . $list_type,
			array_diff(
				get_option( 'protect_login_limit_login_' . $list_type, array() ),
				array( $address, str_replace( '.', '-', $address ) )
			)
		);

		return 'Ok';
	}
}
