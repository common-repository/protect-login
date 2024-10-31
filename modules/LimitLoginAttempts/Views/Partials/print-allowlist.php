<?php
/**
 * File to print permanently allowed ip address
 *
 * @package Protect Login
 */

/**
 * Prints IP-Allowlist
 *
 * @param string $page Menu Slug.
 * @param array  $remote_allowlisted_addresses Array of addresses allowed by remote API.
 *
 * @return void
 */
function protect_login_print_allowlist( string $page, array $remote_allowlisted_addresses ) {
	$url = admin_url( $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=tab4' );
	echo '<form action="' . esc_html( $url ) . '&_nonce=' . esc_html( wp_create_nonce() ) . '" method="post">';
	protect_login_print_block_allow_form( 'allowlist', $page, $remote_allowlisted_addresses );
	submit_button();
	echo '</form>';
}
