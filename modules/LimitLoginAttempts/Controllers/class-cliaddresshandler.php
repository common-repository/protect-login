<?php
/**
 * File class-cliaddresshandler.php
 *
 * Contains controller for CLI namespace "protectone login address"
 *
 * @since 2024-08-28
 * @license GPL-3.0-or-later
 *
 * @package ProtectLogin/CLI
 */

namespace ProtectLogin\Modules\LimitLoginAttempts\Controllers;

use DateTimeZone;

/**
 * Handler for CLI namespace "protectone login address"
 */
class CliAddressHandler {

	/**
	 *  Handler vor command protectone login address list-blocked
	 *
	 * @return void
	 */
	public static function list_blocked() {
		$items = array();

		$blocked_ips = get_option( 'protect_login_limit_login_blocklist', array() );
		foreach ( $blocked_ips as $ip ) {
			$ip      = str_replace( '-', '.', $ip );
			$items[] = array(
				'address'       => $ip,
				'blocked_until' => 'permanently',
			);
		}

		$blocked_ips = get_option( 'protect_login_limit_login_lockouts', array() );
		$timezone    = new DateTimeZone( get_option( 'timezone', 'UTC' ) );

		foreach ( $blocked_ips as $ip => $blocked_until ) {

			$ip   = str_replace( '-', '.', $ip );
			$date = wp_date( 'd.m.Y H:i', $blocked_until, $timezone );

			$items[] = array(
				'address'       => $ip,
				'blocked_until' => $date,
			);
		}
		\WP_CLI\Utils\format_items( 'table', $items, array( 'address', 'blocked_until' ) );
	}

	/**
	 * Handler vor command protectone login address release
	 *
	 * @param array $args Arguments given by CLI.
	 *
	 * @return void
	 */
	public static function release( $args ) {
		if ( ! isset( $args[0] ) ) {
			\WP_CLI::error( 'Usage: protectone login address release <IPADDRES|IPV6ADDRESS>' );
		}

		$address           = $args[0];
		$blocked_addresses = get_option( 'protect_login_limit_login_lockouts', array() );
		if ( ! array( $blocked_addresses ) ) {
			$blocked_addresses = unserialize( $blocked_addresses );
		}
		unset( $blocked_addresses[ $address ] );
		unset( $blocked_addresses[ str_replace( '.', '-', $address ) ] );

		protect_login_update_option_on_mulitsite( 'protect_login_limit_login_lockouts', $blocked_addresses );

		\WP_CLI::success( 'done' );
	}

	/**
	 *   Handler vor command protectone login address remove
	 *
	 * @param array $args Arguments given by CLI.
	 *
	 * @return void
	 */
	public static function remove( $args ) {
		if ( count( $args ) < 2 ) {
			\WP_CLI::error( 'Usage: protectone login address remove <allowlist|blocklist> <IPADDRES|IPV6ADDRESS>' );
		}

		$list_type = $args[0];
		if ( ! in_array( $list_type, array( 'allowlist', 'blocklist' ), true ) ) {
			\WP_CLI::error( 'Usage: protectone login address remove <allowlist|blocklist> <IPADDRES|IPV6ADDRESS>' );
		}

		$address = $args[1];

		protect_login_update_option_on_mulitsite(
			'protect_login_limit_login_' . $list_type,
			array_diff( get_option( 'protect_login_limit_login_' . $list_type, array() ), array( $address, str_replace( '.', '-', $address ) ) )
		);

		\WP_CLI::success( 'done' );
	}

	/**
	 *   Handler vor command protectone login address block
	 *
	 * @param array $args Arguments given by CLI.
	 *
	 * @return void
	 */
	public static function block( $args ) {
		if ( count( $args ) < 2 ) {
			\WP_CLI::error(
				'Usage: protectone login address block <IPADDRES|IPV6ADDRESS> <true|false>' . PHP_EOL .
				'The last param decides, if the ip should be blocked permanently <true|false>'
			);
		}

		$permanently_deny = $args[1];
		if ( ! in_array( $permanently_deny, array( 'true', 'false' ), true ) ) {
			\WP_CLI::error(
				'Usage: protectone login address block <IPADDRES|IPV6ADDRESS> <true|false>' . PHP_EOL .
				'The last param decides, if the ip should be blocked permanently <true|false>'
			);
		}

		$address = $args[0];

		if ( 'true' === $permanently_deny ) {
			protect_login_add_to_list_on_multisite( 'blocklist', $address );
		} else {
			protect_login_lockout_on_multisite( $address );
		}
		\WP_CLI::success( 'done' );
	}

	/**
	 *   Handler vor command protectone login address allow
	 *
	 * @param array $args Arguments given by CLI.
	 *
	 * @return void
	 */
	public static function allow( $args ) {
		if ( ! isset( $args[0] ) ) {
			\WP_CLI::error( 'Usage: protectone login address allow <IPADDRES|IPV6ADDRESS>' );
		}

		$address = $args[0];
		protect_login_add_to_list_on_multisite( 'allowlist', $address );
		\WP_CLI::success( 'done' );
	}
}
