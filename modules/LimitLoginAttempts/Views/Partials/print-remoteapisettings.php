<?php
/**
 * File print-remoteapisettings.php
 *
 * Displays Remote API settings form
 *
 * @since 2024-09-16
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

/**
 * Displays remote API settings form
 *
 * @param string $page Menu Slug.
 *
 * @return void
 */
function protect_login_print_remote_api_settings( string $page ) {
	$url = admin_url( $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=tab-remoteapi' );

	echo '<form action="' . esc_html( $url ) . '&_nonce=' . esc_html( wp_create_nonce() ) . '" method="post">';
	do_settings_sections( PROTECT_LOGIN_SLUG . '-remote-api-section' );

	do_settings_sections( PROTECT_LOGIN_SLUG . '-remote-api-host-section' );

	submit_button();
	echo '</form>';
}
