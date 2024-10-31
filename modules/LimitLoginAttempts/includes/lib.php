<?php
/**
 *  Function definitions for and helper for reading transients
 *
 * @package Protect Login
 */

/**
 * Checks if any setting is overridden by a filter
 *
 * @return bool
 */
function protect_login_check_if_any_filter_set(): bool {
	if (
		false !== get_transient( 'protect_login_limit_login_lockout_duration' ) ||
		false !== get_transient( 'protect_login_limit_login_allowed_retries' ) ||
		false !== get_transient( 'protect_login_limit_login_allowed_lockouts' ) ||
		false !== get_transient( 'protect_login_limit_login_long_duration' ) ||
		false !== get_transient( 'protect_login_show_in_widget' )
	) {
		return true;
	}
	return false;
}

/**
 * Checks if a filter is set for password policy
 *
 * @return bool
 */
function protect_login_check_if_password_filter_set(): bool {
	if (
		false !== get_transient( 'protect_login_password_minimal_strength' )
	) {
		return true;
	}
	return false;
}

/**
 * Returns the current value for a setting, if no transient found, it returns get_option
 *
 * @param string $key Key to get value of.
 * @param int    $default_value Default value if neither transient or option found.
 *
 * @return int
 */
function protect_login_get_setting_by_transient_or_option( string $key, int $default_value ): int {
	$value = get_transient( $key );
	if ( false !== $value ) {
		return (int) $value;
	}
	return get_option( $key, $default_value );
}

/**
 * Function to set a value to transient
 *
 * @param string $key Key to set to transient.
 * @param int    $value Value to set to transient.
 *
 * @return bool
 */
function protect_login_set_setting_by_transient( string $key, int $value ): bool {
	set_transient( $key, $value, HOUR_IN_SECONDS );
	protect_login_set_transient_on_mulitsite( $key, $value, HOUR_IN_SECONDS );
	return true;
}

/**
 * Returns current lockout duration.
 *
 * @param int $default_value Default value if no value found in transient or options.
 * @return int
 */
function protect_login_get_current_lockout_duration( int $default_value = 0 ): int {
	return protect_login_get_setting_by_transient_or_option( 'protect_login_limit_login_lockout_duration', $default_value );
}


/**
 * Returns current setting how many retries are allowed until locking out
 *
 * @param int $default_value Default value if no value found in transient or options.
 * @return int
 */
function protect_login_get_current_max_retries( int $default_value = 0 ): int {
	return protect_login_get_setting_by_transient_or_option( 'protect_login_limit_login_allowed_retries', $default_value );
}

/**
 * Returns current setting how many lockouts are allowed until locking out for long-term
 *
 * @param int $default_value Default value if no value found in transient or options.
 * @return int
 */
function protect_login_get_current_max_allowed_lockouts( int $default_value = 0 ): int {
	return protect_login_get_setting_by_transient_or_option( 'protect_login_limit_login_allowed_lockouts', $default_value );
}

/**
 * Returns current longterm lockout duration.
 *
 * @param int $default_value Default value if no value found in transient or options.
 * @return int
 */
function protect_login_get_current_long_duration( int $default_value = 0 ): int {
	return protect_login_get_setting_by_transient_or_option( 'protect_login_limit_login_long_duration', $default_value );
}

/**
 * Returns current minimal password policy.
 *
 * @param int $default_value Default value if no value found in transient or options.
 *
 * @return int
 */
function protect_login_get_current_password_minimal_strength( int $default_value = 0 ): int {
	return protect_login_get_setting_by_transient_or_option( 'protect_login_password_minimal_strength', $default_value );
}

/**
 * Function for filter "protect_login_lockout_duration"
 * Stores the new value in database and stores the information, the security level is customized
 *
 * @param int $value Seconds for how lon a lockout is in effect.
 *
 * @return void
 */
function protecct_login_set_lockout_duration( int $value ) {
	protect_login_set_setting_by_transient( 'protect_login_limit_login_lockout_duration', $value );
}

/**
 * Function for filter "protect_login_max_retries"
 * Stores the new value in database and stores the information, the security level is customized
 *
 * @param int $value Number of failed logins until the ip address is locked out.
 *
 * @return void
 */
function protecct_login_set_max_retries( int $value ) {
	protect_login_set_setting_by_transient( 'protect_login_limit_login_allowed_retries', $value );
}

/**
 * Function for filter "protect_login_max_allowed_lockouts"
 * Stores the new value in database and stores the information, the security level is customized
 *
 * @param int $value Number of lockout until the ip address is locked out fpr long duration.
 *
 * @return void
 */
function protecct_login_set_max_allowed_lockouts( int $value ) {
	protect_login_set_setting_by_transient( 'protect_login_limit_login_allowed_lockouts', $value );
}

/**
 * Function for filter "protect_login_long_duration"
 * Stores the new value in database and stores the information, the security level is customized
 *
 * @param int $value Seconds for how lon a long-term lockout is in effect.
 *
 * @return void
 */
function protecct_login_set_long_duration( int $value ) {
	protect_login_set_setting_by_transient( 'protect_login_limit_login_long_duration', $value );
}

/**
 * Function for filter "protect_login_password_minimum_strength"
 * Stores the new value in database and stores the information, the password strength is customized
 *
 * @param int $value New password minimum strength.
 *
 * @return void
 */
function protect_login_set_password_minimum_strength( int $value ) {
	protect_login_set_setting_by_transient( 'protect_login_password_minimal_strength', $value );
}

/**
 * Function for filter "protect_login_show_in_widget"
 * Stores the in database, if count of locked-out addresses should be displayed in widget "At a glance"
 *
 * @param bool $value New value for setting.
 *
 * @return void
 */
function protect_login_set_show_in_widget( bool $value ) {
	protect_login_set_setting_by_transient( 'protect_login_show_in_widget', $value );
}

/**
 * Returns if count of locked-out addresses should be displayed in widget "At a glance"
 *
 * @return bool
 */
function protect_login_get_current_show_in_widget(): bool {
	return (bool) protect_login_get_setting_by_transient_or_option( 'protect_login_show_in_widget', false );
}
