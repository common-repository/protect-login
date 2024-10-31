<?php
/**
 * File to print permanently blocked ip address
 *
 * @package Protect Login
 */

/**
 * Prints IP-Blocklist
 *
 * @param string $page Menu Slug.
 * @param array  $remote_blocked_addresses Array of addresses blocked by remote API.
 *
 * @return void
 */
function protect_login_print_blocklist( string $page, array $remote_blocked_addresses ) {

	$url = admin_url( $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=tab3' );
	echo '<form action="' . esc_html( $url ) . '&_nonce=' . esc_html( wp_create_nonce() ) . '" method="post">';
	protect_login_print_block_allow_form( 'blocklist', $page, $remote_blocked_addresses );
	submit_button();
	echo '</form>';
}
