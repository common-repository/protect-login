<?php
/**
 * File class-clispasswordhandler.php
 *
 * * Contains controller for CLI namespace "protectone passwords"
 *
 * @since 2024-08-29
 * @license GPL-3.0-or-later
 *
 * @package ProtectLogin/CLI
 */

namespace ProtectLogin\Modules\PasswordStrength\Controllers;

/**
 * Handler for CLI namespace "protectone passwords"
 */
class CliPasswordHandler {

	/**
	 * Handler vor command protectone passwords minimum-strength
	 *
	 * @param array $args Arguments given by CLI.
	 *
	 * @return void
	 */
	public static function minimum_strength( $args ) {
		if ( ! isset( $args[0] ) ) {
			$password_level = protect_login_get_current_password_minimal_strength();
			if ( null === $password_level ) {
				\WP_CLI::error( 'No password minimal strength set' );
			}

			\WP_CLI::success( $password_level );

		} else {
			$new_level = (int) $args[0];
			if ( ! in_array( $new_level, array( 1, 2, 3 ), true ) ) {
				\WP_CLI::error( 'Allowed password strength levels are 1=weak 2=medium 3=strong' );
			}

			if ( protect_login_check_if_password_filter_set() ) {
				\WP_CLI::error( 'The minimal password strength could not be set, because a filter inside this installation prevents the setting of the minimal password strength.' );
			}

			protect_login_update_option_on_mulitsite( 'protect_login_password_minimal_strength', $new_level );

			\WP_CLI::success( 'Password strength set to ' . $new_level );
		}
	}
}
