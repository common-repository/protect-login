<?php
/**
 * File multisite-functions.php
 *
 * Contains all functions for multisite updates
 *
 * @since 2024-09-24
 * @license GPL-3.0-or-later
 *
 * @package ProtectLogin\MultiSite
 */

use ProtectLogin\Modules\LimitLoginAttempts\Controllers\LoginHandler;
use ProtectLogin\Modules\LimitLoginAttempts\Controllers\OptionsPage;

/**
 * Updates an option on main site, and if existing, on all subpages
 *
 * @param string                $option_name Name of the option to update. Expected to not be SQL-escaped.
 * @param string|int|bool|array $option_value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 *
 * @return void
 */
function protect_login_update_option_on_mulitsite( string $option_name, mixed $option_value ) {
	update_option( $option_name, $option_value );

	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			update_option( $option_name, $option_value );
			restore_current_blog();
		}
	}
}

/**
 * Deletes an option on main site, and if existing, on all subpages
 *
 * @param string $option_name Name of the option to delete.
 *
 * @return void
 */
function protect_login_delete_option_on_mulitsite( string $option_name ) {
	delete_option( $option_name );

	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			delete_option( $option_name );
			restore_current_blog();
		}
	}
}

/**
 * Deletes a user meta key for all users of a site and if existing on all subpages
 *
 * @param string $option_name Key to delete.
 *
 * @return void
 */
function protect_login_delete_user_meta_on_mulitsite( string $option_name ) {
	foreach ( get_users() as $current_user ) {
		delete_user_meta( $current_user->ID, $option_name );
	}

	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			foreach ( get_users() as $current_user ) {
				delete_user_meta( $current_user->ID, $option_name );
			}
			restore_current_blog();
		}
	}
}


/**
 * Sets a transient on site and on all subpage if existing
 *
 * @param string                $option_name Transient name. Expected to not be SQL-escaped.
 *                                           Must be 172 characters or fewer in length.
 * @param string|int|bool|array $option_value Transient value. Must be serializable if non-scalar.
 *                           Expected to not be SQL-escaped.
 * @param int                   $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
 *
 * @return void
 */
function protect_login_set_transient_on_mulitsite( string $option_name, mixed $option_value, int $expiration = 0 ) {
	set_transient( $option_name, $option_value, $expiration );

	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			set_transient( $option_name, $option_value, $expiration );
			restore_current_blog();
		}
	}
}

/**
 * Deletes a transient on site and on all subpage if existing
 *
 * @param string $option_name Transient name. Expected to not be SQL-escaped.
 *                            Must be 172 characters or fewer in length.
 *
 * @return void
 */
function protect_login_delete_transient_on_multisite( string $option_name ) {
	delete_transient( $option_name );
	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			delete_transient( $option_name );
			restore_current_blog();
		}
	}
}

/**
 * Adds an address to a specified list on page and all subpages if existing
 *
 * @param string $list_type List type (allowlist or blocklist).
 * @param string $address Address to add.
 *
 * @return void
 */
function protect_login_add_to_list_on_multisite( string $list_type, string $address ) {
	if ( 'blocklist' === $list_type ) {
		OptionsPage::add_to_blocklist( $address );
	} elseif ( 'allowlist' === $list_type ) {
		OptionsPage::add_to_allowlist( $address );
	}

	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			if ( 'blocklist' === $list_type ) {
				OptionsPage::add_to_blocklist( $address );
			} elseif ( 'allowlist' === $list_type ) {
				OptionsPage::add_to_allowlist( $address );
			}

			restore_current_blog();
		}
	}
}

/**
 * Locks out an address on the site and all subpages if existing
 *
 * @param string $address Address to lockout.
 *
 * @return void
 */
function protect_login_lockout_on_multisite( string $address ) {
	LoginHandler::lockout( $address );

	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			LoginHandler::lockout( $address );
			restore_current_blog();
		}
	}
}

/**
 * Releases an address and site and all subpages if existing
 *
 * @param string $address Address to release.
 *
 * @return void
 */
function protect_login_release_on_multisite( string $address ) {
	OptionsPage::release_ip( $address );

	if ( protect_login_is_on_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			OptionsPage::release_ip( $address );
			restore_current_blog();
		}
	}
}
