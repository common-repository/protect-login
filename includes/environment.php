<?php
/**
 * Defining the exuction environment
 *
 * @package Protect Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PROTECT_LOGIN_SLUG', 'protect-login' );

define( 'PROTECT_LOGIN_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR );
define( 'PROTECT_LOGIN_STARTUP_FILE', PROTECT_LOGIN_PATH . PROTECT_LOGIN_SLUG . '.php' );

define( 'PROTECT_LOGIN_URL', plugin_dir_url( PROTECT_LOGIN_STARTUP_FILE ) );

define( 'PROTECT_LOGIN_API_VERSION', '1.0' );

define( 'PROTECT_LOGIN_API_HOST', site_url() . '/wp-json/protect-one/' . PROTECT_LOGIN_API_VERSION );

/**
 * Function to check if plugin runs on a multisite
 *
 * @return bool
 */
function protect_login_is_on_multisite() {
	if ( is_multisite() && is_plugin_active_for_network( 'protect-login/protect-login.php' ) ) {
		return true;
	}
	return false;
}
