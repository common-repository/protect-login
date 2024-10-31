<?php
/**
 * Handles the login and the lockout
 *
 * @package Protect Login
 */

namespace ProtectLogin\Modules\LimitLoginAttempts\Controllers;

use ProtectLogin\Modules\LimitLoginAttempts\Requests\IpAddress;
use ProtectLogin\Modules\RemoteApi\Actions\ApiClient;

require_once PROTECT_LOGIN_PATH . '/includes/multisite-functions.php';

/**
 * Class to handle login on failure and blocks ip address
 */
class LoginHandler {
	/**
	 * Contains the class instance
	 *
	 * @var null
	 */
	private static $instance = null;

	public const DIRECT_ADDR = 'REMOTE_ADDR';
	public const PROXY_ADDR  = 'HTTP_X_FORWARDED_FOR';

	/**
	 * Return instance of this class.
	 *
	 * @return LoginHandler
	 */
	public static function get_instance(): LoginHandler {
		\protect_login_ensure_activation();
		if ( null === self::$instance ) {
			self::$instance = new LoginHandler();
		}

		return self::$instance;
	}

	/**
	 * Executed when login was successfull
	 *
	 * @param  Object $user  User Object.
	 * @param string $password The entered password.
	 *
	 * @return mixed|\WP_Error
	 */
	public function protect_login_on_successfull_login( $user, string $password ) {
		if ( ! is_wp_error( $user ) && $this->is_login_from_ip_allowed() ) {
			return $user;
		}

		$error = new \WP_Error();
		$error->add( 'too_many_retries', $this->compose_error_message() );
		return $error;
	}

	/**
	 * Displays an individual error message which does not tell if user OR password is invalid. It even prints
	 * remaining retries
	 *
	 * @param string $error Contains previous errors.
	 *
	 * @return string
	 */
	public static function print__error_message( $error ): string {
		global $errors;
		$err_codes = $errors->get_error_codes();
		if (
			in_array( 'invalid_username', $err_codes, true ) ||
			in_array( 'incorrect_password', $err_codes, true )
		) {
			$retries = get_option( 'protect_login_limit_login_retries', array() );
			$ip      = self::get_remote_address();

			$max_allowed_lockouts = protect_login_get_current_max_retries();

			$retries_left = $max_allowed_lockouts - $retries[ $ip ];

			$error = '<strong>' . __( 'Error', 'protect-login' ) . '</strong>: ' .
				__( 'Incorrect username or password.', 'protect-login' );

			if ( 0 < $retries_left ) {
				$error .= '<br /><strong>' .
					/* translators: %d is number of retries until ip address will be blocked */
					wp_sprintf(
						/* translators: %d is number of retries */
						_n(
							'%d retry left',
							'%d retries left',
							$retries_left,
							'protect-login'
						),
						$retries_left
					) . '</strong>';
			}
		}

		return $error;
	}

	/**
	 * Executed when login fails
	 *
	 * @param string $username Entered username.
	 *
	 * @return void
	 */
	public function protect_login_on_failed_login( string $username ) {
		$address = $this->get_remote_address();

		$lockouts = get_option( 'protect_login_limit_login_lockouts', array() );

		if ( isset( $lockouts[ $address ] ) && time() < $lockouts[ $address ] ) {
			return;
		}

		/* Get the arrays with retries and retries-valid information */
		$retries = get_option( 'protect_login_limit_login_retries', array() );
		$valid   = get_option( 'protect_login_limit_login_retries_valid', array() );

		/* Check validity and add one to retries */
		if ( isset( $retries[ $address ] ) ) {
			++$retries[ $address ];
		} else {
			$retries[ $address ] = 1;
		}

		protect_login_update_option_on_mulitsite( 'protect_login_limit_login_retries', $retries );

		/* lockout? */
		if ( $retries[ $address ] % protect_login_get_current_max_retries() !== 0 ) {
			return;
		}

		self::lockout( $address );
		$this->notify();
	}

	/**
	 * Locks out a given IP Address
	 *
	 * @param string $address Address (IPv4 or IPv6) to lockout.
	 *
	 * @return void
	 */
	public static function lockout( string $address ) {
		$retries = get_option( 'protect_login_limit_login_retries', array() );

		$retries_long = protect_login_get_current_max_retries( 1 )
			* protect_login_get_current_max_allowed_lockouts( 1 );

		if ( isset( $retries[ $address ] ) && $retries[ $address ] >= $retries_long ) {
			$lockouts[ $address ] = time() + protect_login_get_current_long_duration( 86400 );

		} else {
			$lockouts[ $address ] = time() + protect_login_get_current_lockout_duration( 900 );
		}

		$api = ApiClient::get_instance();
		$api->block_remote_address( $address, false );

		protect_login_update_option_on_mulitsite( 'protect_login_limit_login_lockouts', $lockouts );
	}

	/**
	 * Notificate the site admin on lockouts
	 *
	 * @return void
	 */
	private function notify_by_email() {
		$ip = $this->get_remote_address();

		$lockouts = get_option( 'protect_login_limit_login_lockouts', array() );
		if ( ! isset( $lockouts[ $ip ] ) ) {
			return;
		}

		$blocked_until = $lockouts[ $ip ];

		$retries         = get_option( 'protect_login_limit_login_retries', array() );
		$current_retries = $retries[ $ip ];

		$notify_after = get_option( 'protect_login_limit_login_notify_email_after', 1 );
		if ( ( $current_retries % $notify_after ) !== 0 ) {
			return;
		}

		$blogname = get_option( 'blogname', 'none' );
		$subject  = sprintf(
		/* translators: %s: page title */
			__( '[%s] Too many failed login attempts', 'protect-login' ),
			$blogname
		);

		$message = sprintf(
		/* translators: %s: Formatting instructions */
			__( 'New lockout on your website:%1$s: IP address: %2$s %3$s Locked out until: %4$s', 'protect-login' ),
			PHP_EOL,
			$ip,
			PHP_EOL,
			gmdate( 'd.m.Y H:i', $blocked_until )
		);

		$admin_email = get_option( 'admin_email' );
		wp_mail( $admin_email, $subject, $message );
	}


	/**
	 * Handle notification in event of lockout
	 *
	 * @return void
	 */
	private function notify() {
		$args = get_option( 'protect_login_limit_login_lockout_notify', array() );
		if ( ! is_array( $args ) ) {
			$args = array( $args );
		}
		foreach ( $args as $mode ) {
			switch ( trim( $mode ) ) {
				case 'email':
					$this->notify_by_email();
					break;
			}
		}
	}

	/**
	 * Generates the message for email notification of a lockout
	 *
	 * @return string
	 */
	private function compose_error_message(): string {
		$ip       = $this->get_remote_address();
		$lockouts = get_option( 'protect_login_limit_login_lockouts' );

		$msg = __( 'Too many failed login attempts.', 'protect-login' ) . ' ';

		if ( ! is_array( $lockouts ) || ! isset( $lockouts[ $ip ] ) || time() >= $lockouts[ $ip ] ) {
			/* Huh? No timeout active? */
			$msg .= __( 'Please try again later.', 'protect-login' );
			return $msg;
		}

		$when = ceil( ( $lockouts[ $ip ] - time() ) / 60 );
		if ( $when > 60 ) {
			$when = ceil( $when / 60 );
			return $msg . sprintf(
				/* translators: %d: only a number */
				_n(
					'Please try again in %d hour.',
					'Please try again in %d hours.',
					$when,
					'protect-login'
				),
				$when
			);
		}

		return $msg . sprintf(
			/* translators: %d: only a number */
			_n(
				'Please try again in %d minute.',
				'Please try again in %d minutes.',
				$when,
				'protect-login'
			),
			$when
		);
	}

	/**
	 * Gets remote ip address of logging in user
	 *
	 * @param string $type_name REMOTE_ADDR or PROXY_ADDR.
	 *
	 * @return mixed|string
	 */
	private static function get_remote_address( string $type_name = '' ) {
		$type_original = $type_name;
		if ( empty( $type_name ) ) {
			$type_name = get_option( 'protect_login_limit_loginclient_type', self::DIRECT_ADDR );
		}

		if ( isset( $_SERVER[ $type_name ] ) && filter_var( wp_unslash( $_SERVER[ $type_name ] ), FILTER_VALIDATE_IP ) ) {
			$ip_address = sanitize_text_field( wp_unslash( $_SERVER[ $type_name ] ) );
			return $ip_address;
		}

		/*
		 * Not found. Did we get proxy type from option?
		 * If so, try to fall back to direct address.
		 */
		if ( empty( $type_name ) && self::PROXY_ADDR === $type_original
			&& isset( $_SERVER[ self::DIRECT_ADDR ] )
			&& filter_var( wp_unslash( $_SERVER[ self::DIRECT_ADDR ] ), FILTER_VALIDATE_IP ) ) {
				$ip_address = sanitize_text_field( wp_unslash( $_SERVER[ self::DIRECT_ADDR ] ) );
				return $ip_address;
		}

		return '';
	}

	/**
	 * Checks if a login is allowed from the users ip address
	 *
	 * @return bool
	 */
	public function is_login_from_ip_allowed(): bool {
		$ip = $this->get_remote_address();
		protect_login_sync_lists_from_remote_api();

		$lockedout_addresses   = self::get_current_lockedout_address();
		$blocklisted_addresses = get_option( 'protect_login_limit_login_blocklist', array() );
		$allowlisted_addresses = get_option( 'protect_login_limit_login_allowlist', array() );

		if ( in_array( $ip, $blocklisted_addresses, true ) ) {
			return false;
		}

		if ( in_array( $ip, $allowlisted_addresses, true ) ) {
			return true;
		}

		if ( in_array( $ip, $lockedout_addresses, true ) ) {
			return false;
		}

		protect_login_delete_transient_on_multisite( 'protect_login_is_in_sync' );
		protect_login_sync_lists_from_remote_api();

		$new_lockedout_addresses   = self::get_current_lockedout_address();
		$new_blocklisted_addresses = get_option( 'protect_login_limit_login_blocklist', array() );
		$new_allowlisted_addresses = get_option( 'protect_login_limit_login_allowlist', array() );

		if ( in_array( $ip, $new_blocklisted_addresses, true ) ) {
			return false;
		}

		if ( in_array( $ip, $new_allowlisted_addresses, true ) ) {
			return true;
		}

		if ( in_array( $ip, $new_lockedout_addresses, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns an array of all currently locked-out address, where the lockout is still active.
	 *
	 * @return array
	 */
	public static function get_current_lockedout_address(): array {
		$addresses       = get_option( 'protect_login_limit_login_lockouts', array() );
		$active_lockouts = array();
		$now             = current_time( 'timestamp' );
		foreach ( $addresses as $address => $lockedout_until ) {
			if ( $lockedout_until < $now ) {
				continue;
			}

			$active_lockouts[] = $addresses;
		}

		return $active_lockouts;
	}
}
