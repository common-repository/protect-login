<?php
/**
 * Plugin Name: Protect Login
 * Plugin URI:  https://www.group.one/en/wordpress
 * Description: Add an additional layer of protection to your WordPress login and make sure bad actors have a hard time guessing your user's login credentials.
 * Version: 1.3.0
 * Tags: login, security, authentication
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author: protect.one
 * Author URI: https://www.group.one/en/wordpress
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Protect Login
 *
 * Copyright 2008 - 2011 Johan Eenfeldt
 *
 * Thanks to Michael Skerwiderski for reverse proxy handling suggestions.
 *
 * Licenced under the GNU GPL:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

use ProtectLogin\Modules\RemoteApi\Requests\RandomPasswortRequest;
use ProtectLogin\Modules\LimitLoginAttempts\Controllers\OptionsPage as ProtectLoginLimitLoginsOptionsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/setup.php';

add_action(
	'admin_menu',
	function () {
		new ProtectLoginLimitLoginsOptionsPage();
	}
);
add_action(
	'network_admin_menu',
	function () {
		new ProtectLoginLimitLoginsOptionsPage();
	}
);

use ProtectLogin\Modules\LimitLoginAttempts\Controllers\LoginHandler;

if ( ! isset( $loginhandler ) ) {
	$loginhandler = LoginHandler::get_instance();
}

add_action( 'plugins_loaded', 'protect_login_setup' );

register_activation_hook(
	PROTECT_LOGIN_PATH . '/includes/setup.php',
	'protect_login_ensure_activation'
);

register_uninstall_hook(
	PROTECT_LOGIN_STARTUP_FILE . '/includes/setup.php',
	'protect_login_uninstall_plugin'
);

add_action( 'wpmu_new_blog', 'protect_login_new_multisite', 10, 6 );

add_filter( 'protect_login_lockout_duration', 'protecct_login_set_lockout_duration', 10, 1 );
add_filter( 'protect_login_max_retries', 'protecct_login_set_max_retries', 10, 1 );
add_filter( 'protect_login_max_allowed_lockouts', 'protecct_login_set_max_allowed_lockouts', 10, 1 );
add_filter( 'protect_login_long_duration', 'protecct_login_set_long_duration', 10, 1 );
add_filter( 'protect_login_password_minimum_strength', 'protect_login_set_password_minimum_strength', 10, 1 );
add_filter( 'protect_login_show_in_widget', 'protect_login_set_show_in_widget', 10, 1 );


add_action( 'wp_login_failed', array( $loginhandler, 'protect_login_on_failed_login' ) );
add_filter( 'wp_authenticate_user', array( $loginhandler, 'protect_login_on_successfull_login' ), 99999, 2 );
add_filter( 'admin_enqueue_scripts', 'protect_login_enqueue_custom_password_js', 10 );
add_filter( 'login_errors', array( 'ProtectLogin\Modules\LimitLoginAttempts\Controllers\LoginHandler', 'print__error_message' ) );

add_action( 'admin_init', 'protect_login_admin_init' );

add_action( 'rest_api_init', 'protect_login_register_api_endpoints' );

if ( isset( $_GET['_nonce'] ) && isset( $_POST['save_protect_login_balist_list_type'] ) ) {
	wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_nonce'] ) ) );

	$list_type  = sanitize_text_field( wp_unslash( $_POST['save_protect_login_balist_list_type'] ) );
	$ips_to_add = '';

	if ( isset( $_POST['new_ips'] ) && isset( $_POST['new_ips'][0] ) && '' !== $_POST['new_ips'][0] ) {
		$ips_to_add = trim( sanitize_text_field( wp_unslash( $_POST['new_ips'][0] ) ) );
	}

	protect_login_update_block_or_allowlist( $list_type, $ips_to_add );
}

if ( null === get_option( 'protect_login_remote_api_host_key', null ) ) {
	update_option( 'protect_login_remote_api_host_key', RandomPasswortRequest::generate( 16 ) );
}

add_filter( 'plugin_action_links_' . PROTECT_LOGIN_SLUG . '/' . PROTECT_LOGIN_SLUG . '.php', 'protect_login_settings' );
add_filter( 'dashboard_glance_items', 'protect_login_add_at_a_glance_item', 10, 1 );
