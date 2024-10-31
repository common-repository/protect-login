<?php
/**
 * Gui elements of settings form
 *
 * @package Protect Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once PROTECT_LOGIN_PATH . '/modules/LimitLoginAttempts/includes/lib.php';


protect_login_load_language();

/**
 * Start of formular
 *
 * @return void
 */
function protect_login_options() {
	echo '<input type="hidden" name="update_options" value="true" />';
	protect_login_print_description( __( 'Protect Login helps you harden your login against Brute-Force Attacks. It adds delays after too many failed attempts to make it harder for bad actors to guess your users\' login credentials by repeatedly trying.', 'protect-login' ) );
}

/**
 * Prints head of notifications section
 *
 * @return void
 */
function protect_login_notify_options() {
	protect_login_print_description( __( 'Would you like to be notified if IP addresses get blocked?', 'protect-login' ) );
}

/**
 * Prints head of advanced options section
 *
 * @return void
 */
function protect_login_advanced_options() {
	protect_login_print_description( __( 'Some web hosting setups utilize a reverse proxy. Have you never heard of reverse proxies before? Chances are, you can leave this setting as is.', 'protect-login' ) );
}

/**
 * Prints head of password options section
 *
 * @return void
 */
function protect_login_password_options() {
	echo '<input type="hidden" name="update_password_settings" value="true" />';
	protect_login_print_description( __( 'Enforce the minimum strength for every new password used on this website. Your users cannot set a weaker password for their accounts. The password strength is measured locally in your user\'s browser. Please note: this only applies to new passwords; existing ones will not be affected.', 'protect-login' ) );
}

/**
 * Handler for the textboxes
 *
 * @param array $args Arguments of element.
 *
 * @return void
 */
function protect_login_limit_logins_settings_callback( array $args ) {
	$setting = get_option( $args['setting'], null );
	if ( null === $setting ) {
		$value = '';
	} else {
		$value = esc_attr( $setting );
		if ( isset( $args['unit_division'] ) ) {
			$value = (int) $value / (int) $args['unit_division'];
		}
	}
	$description = $args['description'] ?? null;
	$minlength   = $args['minlength'] ?? null;

	protect_login_print_textbox( $args['setting'], $value, $description, $minlength );
}

/**
 * Handler for radio elements
 *
 * @param array $args Argument of the element.
 *
 * @return void
 */
function protect_login_limit_logins_settings_radio_callback( array $args ) {
	protect_login_print_radio( $args['setting'] );
}

/**
 * Prints a readonly label
 *
 * @param array $args Arguments of the element.
 *
 * @return void
 */
function protect_login_readonly_textfield( array $args ) {
	if ( isset( $args['value'] ) ) {
		$value = $args['value'];
	} else {
		$setting = get_option( $args['setting'], null );
		if ( null === $setting ) {
			$value = '';
		} else {
			$value = esc_attr( $setting );
			if ( isset( $args['unit_division'] ) ) {
				$value = (int) $value / (int) $args['unit_division'];
			}
		}
	}
	$description = $args['description'] ?? null;

	protect_login_print_label( $value, $description );
}

/**
 * Handler for checkbox elements
 *
 * @param array $args Arguments of the element.
 *
 * @return void
 */
function protect_login_limit_logins_settings_checkbox_callback( array $args ) {
	$description = $args['description'] ?? null;
	protect_login_print_checkbox( $args['setting'], $description );
}

$settings_page = PROTECT_LOGIN_SLUG . '-limit-login-attempts';
add_settings_section(
	'protect_login_options',
	__( 'Brute Force Protection', 'protect-login' ),
	'protect_login_options',
	$settings_page
);

add_settings_field(
	'protect_login_lla_1',
	__( 'Protection level', 'protect-login' ),
	'protect_login_limit_logins_settings_radio_callback',
	$settings_page,
	'protect_login_options',
	array(
		'setting' => 'protect_login_protection_level',
	)
);

$settings_page = PROTECT_LOGIN_SLUG . '-notify-section';
add_settings_section(
	'custom_settings_section',
	__( 'Notifications', 'protect-login' ),
	'protect_login_notify_options',
	$settings_page
);

add_settings_field(
	'protect_login_lla_widget',
	__( 'Show blocked addresses', 'protect-login' ),
	'protect_login_limit_logins_settings_checkbox_callback',
	$settings_page,
	'custom_settings_section',
	array(
		'setting'     => 'protect_login_show_in_widget',
		'description' => __( 'If enabled, the number of current locked-out addresses is displayed in "at a glance" widget.', 'protect-login' ),
	)
);

add_settings_field(
	'protect_login_lla_7',
	__( 'Notify if blocked', 'protect-login' ),
	'protect_login_limit_logins_settings_checkbox_callback',
	$settings_page,
	'custom_settings_section',
	array(
		'setting'     => 'protect_login_limit_login_lockout_notify',
		'description' => __( 'This uses the Administration Email Address defined in Settings > General.', 'protect-login' ),
	)
);

add_settings_field(
	'protect_login_lla_8',
	__( 'Failed attempts until notification', 'protect-login' ),
	'protect_login_limit_logins_settings_callback',
	$settings_page,
	'custom_settings_section',
	array(
		'setting'     => 'protect_login_limit_login_notify_email_after',
		'description' => __( 'After how many failed attempts would you like to be notified?', 'protect-login' ),
	)
);

$settings_page = PROTECT_LOGIN_SLUG . '-advanced-section';
add_settings_section(
	'advanced_settings_section',
	__( 'Advanced', 'protect-login' ),
	'protect_login_advanced_options',
	$settings_page
);

add_settings_field(
	'protect_login_lla_6',
	__( 'Page accessible via', 'protect-login' ),
	'protect_login_limit_logins_settings_radio_callback',
	$settings_page,
	'advanced_settings_section',
	array(
		'setting' => 'protect_login_limit_login_client_type',
	)
);

$settings_page = PROTECT_LOGIN_SLUG . '-password-section';
add_settings_section(
	'password_section',
	__( 'Password strength', 'protect-login' ),
	'protect_login_password_options',
	$settings_page
);

add_settings_field(
	'protect_login_lla_5',
	__( 'Minimum password strength', 'protect-login' ),
	'protect_login_limit_logins_settings_radio_callback',
	$settings_page,
	'password_section',
	array(
		'setting' => 'protect_login_password_minimal_strength',
	)
);

$settings_page = PROTECT_LOGIN_SLUG . '-2fa-section';
add_settings_section(
	'2fa_section',
	__( 'Two Factor Authentication', 'protect-login' ),
	'protect_login_print_2fa_data',
	$settings_page
);
