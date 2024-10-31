<?php
/**
 * Basic plugin setup logic
 *
 * @package Protect Login
 */

use ProtectLogin\Modules\LimitLoginAttempts\Controllers\LoginHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . '/wp-admin/includes/plugin.php';
require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';
require_once ABSPATH . '/wp-includes/pluggable.php';
require_once ABSPATH . '/wp-admin/includes/template.php';

if ( is_multisite() ) {
	if ( ! defined( 'COOKIEHASH' ) ) {
		$siteurl = get_network_option( get_current_blog_id(), 'site_url', get_home_url() );
		define( 'COOKIEHASH', md5( $siteurl ) );
	}

	if ( ! defined( 'SECURE_AUTH_COOKIE' ) ) {
		define( 'SECURE_AUTH_COOKIE', 'wordpress_sec_' . COOKIEHASH );
	}
}

require_once __DIR__ . '/environment.php';

$modules = array( 'LimitLoginAttempts', 'PasswordStrength', 'RemoteApi' );
$subdirs = array( 'includes', 'Controllers', 'Views', 'Views/Components', 'Views/Partials', 'Requests', 'Actions' );

foreach ( $modules as $cur_module ) {
	foreach ( $subdirs as $dir ) {
		$include_path = PROTECT_LOGIN_PATH . 'modules/' . $cur_module . '/' . $dir . '/';
		if ( is_dir( $include_path ) ) {
			$handle = opendir( $include_path );
			while ( false !== ( $entry = readdir( $handle ) ) ) {
				if ( preg_match( '/\.php$/', $entry ) ) {
					require_once $include_path . $entry;
				}
			}
			closedir( $handle );
		}
	}
}

spl_autoload_register(
	function ( $class_name ) {

		if ( strpos( $class_name, 'ProtectLogin\\' ) !== 0 ) {
			return;
		}

		$class_name_file = 'class-' . strtolower( $class_name );
		$filename        = str_replace( '\\', '/', $class_name_file );
		$filename        = str_replace( 'ProtectLogin/Modules/', 'ProtectLogin/modules/', $filename );
		$filename        = str_replace( 'ProtectLogin/', '', $filename );

		$filename = PROTECT_LOGIN_PATH . $filename . '.php';
		if ( ! file_exists( $filename ) ) {
			return;
		}

		require_once $filename;
	}
);

/**
 * Function to load when wp-admin is initiated
 *
 * @return void
 */
function protect_login_admin_init() {
	protect_login_ensure_activation();
	protect_login_settings_validators();
}

/**
 * Function to initialise the language
 *
 * @return void
 */
function protect_login_load_language() {
	if ( defined( 'LOGGED_IN_COOKIE' ) ) {
		load_plugin_textdomain( PROTECT_LOGIN_SLUG );
	}
}

/**
 * Function to load on plugin setup
 *
 * @return void
 */
function protect_login_setup() {
	protect_login_load_language();
}

/**
 * Function to load on activation
 *
 * @return void
 */
function protect_login_ensure_activation() {
	$settings = array(
		'protect_login_limit_login_lockout_duration'   => 15 * MINUTE_IN_SECONDS,
		'protect_login_limit_login_allowed_retries'    => 3,
		'protect_login_limit_login_allowed_lockouts'   => 3,
		'protect_login_password_minimal_strength'      => 3,
		'protect_login_limit_login_client_type'        => 'REMOTE_ADDR',
		'protect_login_limit_login_long_duration'      => 24 * HOUR_IN_SECONDS,
		'protect_login_limit_login_lockout_notify'     => '',
		'protect_login_limit_login_notify_email_after' => 3,
		'protect_login_protection_level'               => '2',
	);

	foreach ( $settings as $cur_setting => $value ) {
		if ( get_option( $cur_setting, null ) !== null ) {
			continue;
		}

		protect_login_update_option_on_mulitsite( $cur_setting, $value );
	}
}

/**
 * Function to load individual .js - Files in /assets/js/
 *
 * @return void
 */
function protect_login_enqueue_custom_password_js() {
	$plugin_data = get_plugin_data( PROTECT_LOGIN_STARTUP_FILE );

	wp_enqueue_script(
		'searchable-table',
		PROTECT_LOGIN_URL .
		'/assets/js/protect-login-searchtable.js',
		array( 'jquery' ),
		$plugin_data['Version'],
		array( 'in_footer' => false )
	);

	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$page = explode( '/', esc_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
		$page = $page[ count( $page ) - 1 ];
		$page = explode( '?', $page );
		$page = $page[0];

		if ( in_array( $page, array( 'profile.php', 'user-edit.php' ), true ) ) {
			wp_enqueue_script(
				'custom-password-js',
				PROTECT_LOGIN_URL . '/assets/js/password.js',
				array( 'jquery' ),
				$plugin_data['Version'],
				array( 'in_footer' => false )
			);

			wp_localize_script(
				'custom-password-js',
				'php_vars',
				array(
					'allowed_strengths'       => protect_login_get_minimal_password_strength(),
					'password_too_short_text' => __( 'The password does not correspond to the requirements.', 'protect-login' ),
				)
			);
		}
	}

	wp_enqueue_style( 'protect-login-css', PROTECT_LOGIN_URL . '/assets/css/protect-login.css', array(), $plugin_data['Version'], );
}

/**
 * Function to uninstall the plugin.
 *
 * @return void
 */
function protect_login_uninstall_plugin() {
	$settings = array(
		'protect_login_limit_login_lockout_duration',
		'protect_login_limit_login_allowed_retries',
		'protect_login_limit_login_allowed_lockouts',
		'protect_login_password_minimal_strength',
		'protect_login_limit_login_client_type',
		'protect_login_limit_login_long_duration',
		'protect_login_limit_login_lockout_notify',
		'protect_login_limit_login_notify_email_after',
		'protect_login_protection_level',
	);

	foreach ( $settings as $cur_setting ) {
		protect_login_delete_option_on_mulitsite( $cur_setting );

	}
}


/**
 * Sets protect-login options on a new blog in case of mulitisite
 *
 * @param int    $blog_id Id of the new blog.
 * @param int    $user_id Id of the creating user.
 * @param string $domain The new domain.
 * @param string $path The new path.
 * @param int    $site_id The site id.
 * @param mixed  $meta Meta data for blog.
 *
 * @return void
 */
function protect_login_new_multisite( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	$data = array(
		'protect_login_limit_login_lockout_duration'   => get_option( 'protect_login_limit_login_lockout_duration', null ),
		'protect_login_limit_login_allowed_retries'    => get_option( 'protect_login_limit_login_allowed_retries', null ),
		'protect_login_limit_login_allowed_lockouts'   => get_option( 'protect_login_limit_login_allowed_lockouts', null ),
		'protect_login_password_minimal_strength'      => get_option( 'protect_login_password_minimal_strength', null ),
		'protect_login_limit_login_client_type'        => get_option( 'protect_login_limit_login_client_type', null ),
		'protect_login_limit_login_lockout_notify'     => get_option( 'protect_login_limit_login_lockout_notify', null ),
		'protect_login_limit_login_notify_email_after' => get_option( 'protect_login_limit_login_notify_email_after', null ),
		'protect_login_protection_level'               => get_option( 'protect_login_protection_level', null ),
	);

	switch_to_blog( $blog_id );
	foreach ( $data as $setting_key => $setting_value ) {
		update_option( $setting_key, $setting_value );
	}
	restore_current_blog();
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once PROTECT_LOGIN_PATH . 'includes/cli.php';
}

require_once PROTECT_LOGIN_PATH . 'includes/api.php';

/**
 * Displays the link to settings in plugin overview
 *
 * @param array $links Already existing links.
 *
 * @return mixed New Links including our plugin link
 */
function protect_login_settings( $links ) {
	$url = admin_url( 'options-general.php?page=' . PROTECT_LOGIN_SLUG );

	if ( protect_login_is_on_multisite() ) {
		$url = admin_url( 'settings.php?page=' . PROTECT_LOGIN_SLUG );
	}

	$settings_link = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'protect-login' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

$loginhandler = LoginHandler::get_instance();

/**
 * Adds count of locked out addresses in "At a Glance" widget
 *
 * @param array $items Currently existing items.
 *
 * @return array|mixed
 */
function protect_login_add_at_a_glance_item( $items = array() ) {
	if ( ! current_user_can( 'manage_options' ) || true !== protect_login_get_current_show_in_widget() ) {
		return $items;
	}

	$locekd_out_address = LoginHandler::get_current_lockedout_address();

	$items[] =
		'<li class="protect-login-locked-out-addresses">' .
		'<a href="' . esc_url( admin_url( 'options-general.php?page=protect-login&tab=tab5' ) ) . '" >' .
		count( $locekd_out_address ) .
		_n(
			' locked-out address',
			' locked-out addresses',
			count( $locekd_out_address ),
			'protect-login'
		) .
		'</a></li>';

	return $items;
}
