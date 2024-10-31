<?php
/**
 * Checks inputs and save to wp_options
 *
 * @package Protect Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks that element is an integer
 *
 * @param int $input Element toi check.
 *
 * @return int
 */
function protect_login_check_for_integer( int $input ) {
	return $input;
}

/**
 * Converts hours to seconds
 *
 * @param int $input Hours to convert.
 *
 * @return float|int
 */
function protect_login_hours_to_seconds_converter( int $input ) {
	return protect_login_minutes_to_seconds_converter( $input ) * 60;
}

/**
 * Converts minutes to seconds
 *
 * @param int $input Minutes to convert.
 *
 * @return float|int
 */
function protect_login_minutes_to_seconds_converter( int $input ) {
	return protect_login_check_for_integer( $input ) * 60;
}

/**
 * Ruleset for fields and how to validate
 *
 * @return void
 */
function protect_login_settings_validators() {
	$slug = PROTECT_LOGIN_SLUG . '-limit-login-attempts';

	register_setting(
		$slug,
		'protect_login_limit_login_allowed_retries',
		'check_for_integer'
	);

	register_setting(
		$slug,
		'protect_login_limit_login_allowed_lockouts',
		'check_for_integer'
	);

	register_setting(
		$slug,
		'protect_login_limit_login_lockout_duration',
		'minutes_to_seconds_converter'
	);

	register_setting(
		$slug,
		'protect_login_limit_login_long_duration',
		'hours_to_seconds_converter'
	);

	register_setting(
		$slug,
		'protect_login_limit_login_notify_email_after',
		'check_for_integer'
	);
}

/**
 * Updates the options to wp_options
 *
 * @param string $protection_level Level which security level should be set.
 * @param string $client_type Is page directly accessible or behind a proxy.
 * @param int    $notify_after Number of failed logins by one ip address after an email is sent.
 * @param bool   $notify_by_mail Should the page admin be informed by mail after invalid logins.
 * @param bool   $display_in_widget Display locked-out address count in "At a Glance" - widget.
 *
 * @return void
 */
function protect_login_update_settings( string $protection_level, string $client_type, int $notify_after, bool $notify_by_mail, bool $display_in_widget ) {
	$settings_params = array(
		'protect_login_limit_login_notify_email_after' => $notify_after,
		'protect_login_limit_login_client_type'        => $client_type,
		'protect_login_protection_level'               => $protection_level,
	);

	if ( ! $notify_by_mail ) {
		protect_login_update_option_on_mulitsite( 'protect_login_limit_login_lockout_notify', '' );
	} else {
		$settings_params['protect_login_limit_login_lockout_notify'] = array( 'email' );
	}

	if ( ! $display_in_widget ) {
		protect_login_update_option_on_mulitsite( 'protect_login_show_in_widget', false );
	} else {
		$settings_params['protect_login_show_in_widget'] = true;
	}

	switch ( $protection_level ) {
		case '1':
			$settings_params['protect_login_limit_login_lockout_duration'] = 10 * MINUTE_IN_SECONDS;
			$settings_params['protect_login_limit_login_allowed_retries']  = 5;
			$settings_params['protect_login_limit_login_allowed_lockouts'] = 5;
			$settings_params['protect_login_limit_login_long_duration']    = 5 * HOUR_IN_SECONDS;
			break;
		case '2':
			$settings_params['protect_login_limit_login_lockout_duration'] = 15 * MINUTE_IN_SECONDS;
			$settings_params['protect_login_limit_login_allowed_retries']  = 3;
			$settings_params['protect_login_limit_login_allowed_lockouts'] = 3;
			$settings_params['protect_login_limit_login_long_duration']    = 24 * HOUR_IN_SECONDS;
			break;
		case '3':
		default:
			$settings_params['protect_login_limit_login_lockout_duration'] = 30 * MINUTE_IN_SECONDS;
			$settings_params['protect_login_limit_login_allowed_retries']  = 3;
			$settings_params['protect_login_limit_login_allowed_lockouts'] = 2;
			$settings_params['protect_login_limit_login_long_duration']    = 72 * HOUR_IN_SECONDS;
			break;
	}

	foreach ( $settings_params as $key => $cur_setting ) {
		protect_login_update_option_on_mulitsite( $key, $cur_setting );
	}
}
