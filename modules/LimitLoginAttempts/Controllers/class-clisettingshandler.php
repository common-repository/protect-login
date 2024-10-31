<?php
/**
 * File class-clisettingshandler.php
 *
 * * Contains controller for CLI namespace "protectone login settings"
 *
 * @since 2024-08-29
 * @license GPL-3.0-or-later
 *
 * @package ProtectLogin/CLI
 */

namespace ProtectLogin\Modules\LimitLoginAttempts\Controllers;

/**
 * Handler for CLI namespace "protectone login settings"
 */
class CliSettingsHandler {

	/**
	 *   Handler vor command protectone login setting security-level
	 *
	 * @param array $args Arguments given by CLI.
	 *
	 * @return void
	 */
	public static function security_level( $args ) {
		if ( ! isset( $args[0] ) ) {
			if ( protect_login_check_if_any_filter_set() ) {
				\WP_CLI::success( '-1' );
			}

			$security_level = get_option( 'protect_login_protection_level', null );
			if ( null === $security_level ) {
				\WP_CLI::error( 'No security level set' );
			}

			\WP_CLI::success( $security_level );
		} else {
			$new_level = (int) $args[0];
			if ( ! in_array( $new_level, array( 1, 2, 3 ), true ) ) {
				\WP_CLI::error( 'Allowed security levels are 1=low 2=medium 3=high' );
			}
			if ( protect_login_check_if_any_filter_set() ) {
				\WP_CLI::error( 'The security level could not be set, because a filter inside this installation prevents the setting of the security level.' );
			}

			$settings_params = array( 'protect_login_protection_level' => $new_level );
			switch ( $new_level ) {
				case 1:
					$settings_params['protect_login_limit_login_lockout_duration'] = 10 * MINUTE_IN_SECONDS;
					$settings_params['protect_login_limit_login_allowed_retries']  = 5;
					$settings_params['protect_login_limit_login_allowed_lockouts'] = 5;
					$settings_params['protect_login_limit_login_long_duration']    = 5 * HOUR_IN_SECONDS;
					break;
				case 2:
					$settings_params['protect_login_limit_login_lockout_duration'] = 15 * MINUTE_IN_SECONDS;
					$settings_params['protect_login_limit_login_allowed_retries']  = 3;
					$settings_params['protect_login_limit_login_allowed_lockouts'] = 3;
					$settings_params['protect_login_limit_login_long_duration']    = 24 * HOUR_IN_SECONDS;
					break;
				case 3:
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

			\WP_CLI::success( 'Security level set to ' . $new_level );
		}
	}

	/**
	 *    Handler vor command protectone login setting lockout-duration
	 *
	 * @return void
	 */
	public static function get_lockout_duration() {
		\WP_CLI::success( protect_login_get_current_lockout_duration() );
	}

	/**
	 *    Handler vor command protectone login long-duration
	 *
	 * @return void
	 */
	public static function get_lockout_long_duration() {
		\WP_CLI::success( protect_login_get_current_long_duration() );
	}

	/**
	 *    Handler vor command protectone login setting max-retries
	 *
	 * @return void
	 */
	public static function get_retries() {
		\WP_CLI::success( protect_login_get_current_max_retries() );
	}

	/**
	 *    Handler vor command protectone login setting allowed-lockouts
	 *
	 * @return void
	 */
	public static function get_allowed_lockouts() {
		\WP_CLI::success( protect_login_get_current_max_allowed_lockouts() );
	}
}
