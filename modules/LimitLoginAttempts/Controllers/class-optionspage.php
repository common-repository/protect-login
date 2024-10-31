<?php
/**
 * Controller for the options page of Protect Login
 *
 * @package Protect Login
 */

namespace ProtectLogin\Modules\LimitLoginAttempts\Controllers;

use ProtectLogin\Modules\RemoteApi\Actions\ApiClient;
use ProtectLogin\Modules\RemoteApi\Actions\SaveEndpointSettings;
use ProtectLogin\Modules\RemoteApi\Actions\SaveHostKey;

/**
 * Class for Options page
 */
class OptionsPage {

	/**
	 * Displays entry in menubar
	 *
	 * @return void
	 */
	public function __construct() {
		protect_login_sync_lists_from_remote_api();

		if ( protect_login_is_on_multisite() ) {
			add_submenu_page(
				'settings.php',
				esc_html__( 'Protect Login', 'protect-login' ),
				esc_html__( 'Protect Login', 'protect-login' ),
				'manage_options',
				PROTECT_LOGIN_SLUG,
				array( $this, 'limit_login_option_page' )
			);
		} else {
			add_options_page(
				esc_html__( 'Protect Login', 'protect-login' ),
				esc_html__( 'Protect Login', 'protect-login' ),
				'manage_options',
				PROTECT_LOGIN_SLUG,
				array( $this, 'limit_login_option_page' ),
				2048
			);
		}
	}

	/**
	 * Releases an ip address from lockout
	 *
	 * @param string $ip Address to release.
	 *
	 * @return void
	 */
	public static function release_ip( string $ip ) {
		$all_ips = get_option( 'protect_login_limit_login_lockouts', array() );
		unset( $all_ips[ $ip ] );
		protect_login_update_option_on_mulitsite( 'protect_login_limit_login_lockouts', $all_ips );
	}

	/**
	 * Blocks an ip address
	 *
	 * @param string $ip Address to block.
	 *
	 * @return void
	 */
	public static function add_to_blocklist( string $ip ) {
		$blocked_ips   = get_option( 'protect_login_limit_login_blocklist', array() );
		$blocked_ips[] = $ip;
		$blocked_ips   = array_unique( $blocked_ips );

		$api = ApiClient::get_instance();
		$api->block_addresses( $blocked_ips );

		protect_login_update_option_on_mulitsite( 'protect_login_limit_login_blocklist', $blocked_ips );
	}

	/**
	 * Permanently allowlist an ip address
	 *
	 * @param string $ip Address to allow.
	 *
	 * @return void
	 */
	public static function add_to_allowlist( string $ip ) {
		$allowed_ips   = get_option( 'protect_login_limit_login_allowlist', array() );
		$allowed_ips[] = $ip;
		$allowed_ips   = array_unique( $allowed_ips );
		protect_login_update_option_on_mulitsite( 'protect_login_limit_login_allowlist', $allowed_ips );

		$api = ApiClient::get_instance();
		$api->allow_addresses( $allowed_ips );

		self::release_ip( $ip );
	}

	/**
	 * Removes an ip address from a list
	 *
	 * @param string $list_type List where to remove(allowlist|blocklist).
	 * @param string $ip        Address to remove.
	 *
	 * @return void
	 */
	public function remove_from_list( string $list_type, string $ip ) {

		$api = ApiClient::get_instance();
		$api->remove( $ip, $list_type );

		protect_login_update_option_on_mulitsite(
			'protect_login_limit_login_' . $list_type,
			array_diff( get_option( 'protect_login_limit_login_' . $list_type, array() ), array( $ip ) )
		);
	}

	/**
	 * Genereates the plugin's option page
	 *
	 * @return void
	 */
	public function limit_login_option_page() {
		global $errors;

		$show_message = null;

		if ( isset( $_GET['_nonce'] ) && isset( $_POST['update_options'] ) ) {
			wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_nonce'] ) ) );
			if (
				! isset( $_POST['protect_login_protection_level'] ) ||
				! isset( $_POST['protect_login_limit_login_client_type'] ) ||
				! isset( $_POST['protect_login_limit_login_notify_email_after'] )
			) {
				wp_die( esc_html__( 'Invalid request of data received', 'protect-login' ) );
			}
			$protection_level  = sanitize_text_field( wp_unslash( $_POST['protect_login_protection_level'] ) );
			$client_type       = sanitize_text_field( wp_unslash( $_POST['protect_login_limit_login_client_type'] ) );
			$notify_after      = (int) sanitize_text_field( wp_unslash( $_POST['protect_login_limit_login_notify_email_after'] ) );
			$notify_by_mail    = isset( $_POST['protect_login_limit_login_lockout_notify'] );
			$display_in_widget = isset( $_POST['protect_login_show_in_widget'] );
			protect_login_update_settings( $protection_level, $client_type, $notify_after, $notify_by_mail, $display_in_widget );
			$show_message = esc_html__( 'The settings were saved.', 'protect-login' );
		}

		if ( isset( $_GET['_nonce'] ) &&
			isset( $_POST['remote-api-update'] ) &&
			isset( $_POST['protect_login_remote_api_endpoint_url'] ) &&
			isset( $_POST['protect_login_remote_api_endpoint_key'] ) &&
			isset( $_POST['protect_login_remote_api_host_key'] )
		) {
			$endpoint_url = esc_url( sanitize_text_field( wp_unslash( $_POST['protect_login_remote_api_endpoint_url'] ) ) );
			$endpoint_key = sanitize_text_field( wp_unslash( $_POST['protect_login_remote_api_endpoint_key'] ) );
			$host_key     = sanitize_text_field( wp_unslash( $_POST['protect_login_remote_api_host_key'] ) );

			SaveHostKey::execute( $host_key );
			SaveEndpointSettings::execute( $endpoint_url, $endpoint_key );

			protect_login_show_message( __( 'The API settings were saved', 'protect-login' ) );
		}

		if (
			isset( $_GET['_nonce'] ) &&
				isset( $_POST['update_password_settings'] ) &&
				isset( $_POST['protect_login_password_minimal_strength'] )
			) {
				wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_nonce'] ) ) );

			protect_login_update_option_on_mulitsite(
				'protect_login_password_minimal_strength',
				sanitize_key( wp_unslash( $_POST['protect_login_password_minimal_strength'] ) )
			);

			$show_message = esc_html__( 'The settings were saved.', 'protect-login' );
		}

		if ( isset( $_GET['action'] ) && 'release' === $_GET['action'] ) {
			$show_message = esc_html__( 'The IP address was released.', 'protect-login' );
		}

		if ( isset( $_POST['save_protect_login_balist_list_type'] ) ) {
			$show_message = esc_html__( 'The list was saved.', 'protect-login' );
		}

		if ( null !== $show_message ) {
			protect_login_show_message( $show_message, true );
		}

		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'tab1';

		if ( isset( $_GET['action'] ) && isset( $_GET['list'] ) && isset( $_GET['ip'] ) && 'removeFromList' === $_GET['action'] ) {
			$this->remove_from_list(
				sanitize_key( wp_unslash( $_GET['list'] ) ),
				str_replace( '_', '.', sanitize_text_field( wp_unslash( $_GET['ip'] ) ) )
			);
			if ( 'blocklist' === $_GET['list'] ) {
				$tab = 'tab3';
			} else {
				$tab = 'tab4';
			}
		}

		$page = 'options-general.php';
		if ( protect_login_is_on_multisite() ) {
			$page = 'network/settings.php';
		}

		protect_login_print_tab_header( $page, $tab );

		switch ( $tab ) {
			case 'tab1':
				if ( ! protect_login_check_if_any_filter_set() ) {
					protect_login_print_options_page( $page );
				} else {
					protect_login_options_page_readonly();
				}
				break;

			case 'tab-remoteapi':
				protect_login_print_remote_api_settings( $page );
				break;

			case 'tab2':
				if ( protect_login_check_if_password_filter_set() ) {
					protect_login_print_password_page_readonly( $page );
				} else {
					protect_login_print_password_page( $page );
				}
				break;
			case 'tab3':
				$api                      = ApiClient::get_instance();
				$remote_blocked_addresses = $api->get_remote_blocklisted_ips();

				protect_login_print_blocklist( $page, $remote_blocked_addresses );
				break;
			case 'tab4':
				$api                      = ApiClient::get_instance();
				$remote_allowed_addresses = $api->get_remote_allowlisted_ips();
				protect_login_print_allowlist( $page, $remote_allowed_addresses );
				break;
			case 'tab5':
				if ( isset( $_GET['action'] ) && 'release' === $_GET['action'] ) {
					$this->release_ip(
						str_replace( '_', '.', sanitize_text_field( wp_unslash( $_GET['ip'] ) ) )
					);

					$api = ApiClient::get_instance();
					$api->release_remote_address(
						str_replace( '_', '.', sanitize_text_field( wp_unslash( $_GET['ip'] ) ) ),
						false
					);
				}

				if ( isset( $_GET['action'] ) && 'toBlock' === $_GET['action'] ) {
					$this->add_to_blocklist(
						str_replace( '_', '.', sanitize_text_field( wp_unslash( $_GET['ip'] ) ) )
					);

					$api = ApiClient::get_instance();
					$api->block_remote_address(
						str_replace( '_', '.', sanitize_text_field( wp_unslash( $_GET['ip'] ) ) ),
						false
					);
				}

				if ( isset( $_GET['action'] ) && 'toAllow' === $_GET['action'] ) {
					$this->add_to_allowlist(
						str_replace( '_', '.', sanitize_text_field( wp_unslash( $_GET['ip'] ) ) )
					);
				}

				protect_login_print_lockedout_list( get_option( 'protect_login_limit_login_lockouts', array() ), $page );
				break;
		}
		protect_login_print_tab_footer();
	}
}
