<?php
/**
 * File for settings for the pluging
 *
 * @package Protect Login
 */

/**
 * Function to print the Protect Login options page
 *
 * @param string $page Menu Slug.
 *
 * @return void
 */
function protect_login_print_password_page( string $page ) {
	$url = admin_url( $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=tab2' );

	echo '<form action="' . esc_html( $url ) . '&_nonce=' . esc_html( wp_create_nonce() ) . '" method="post">';
	do_settings_sections( PROTECT_LOGIN_SLUG . '-password-section' );

	do_settings_sections( PROTECT_LOGIN_SLUG . '-2fa-section' );

	submit_button();
	echo '</form>';
}

/**
 * Displays the setting page in read-only mode in case the settings are individually set.
 *
 * @param string $page Menu Slug.
 *
 * @return void
 */
function protect_login_print_password_page_readonly( string $page ) {
	$url = admin_url( $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=tab2' );

	$strengths = array(
		1 => esc_html__( 'weak (WordPress Default)', 'protect-login' ),
		2 => esc_html__( 'medium', 'protect-login' ),
		3 => esc_html__( 'strong (recommended)', 'protect-login' ),
	);

	?>
	<p id="protect-login-readonly-reason">
		<?php echo esc_html__( 'This page is read-only because the security settings are set programmatically. Please get in touch with your admin if you need help.', 'protect-login' ); ?>
	</p>
	<p> <?php echo esc_html__( 'The current settings are:', 'protect-login' ); ?></p>
	<table>
		<tr>
			<td><?php echo esc_html__( 'Minimum password strength:', 'protect-login' ); ?></td>
			<td><?php echo esc_html( $strengths[ protect_login_get_current_password_minimal_strength() ] ); ?></td>
		</tr>
	</table>
	<?php
	do_settings_sections( PROTECT_LOGIN_SLUG . '-2fa-section' );
}
