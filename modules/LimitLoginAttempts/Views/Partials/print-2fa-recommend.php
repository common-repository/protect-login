<?php
/**
 * Library for displaying two-factor authentication.
 *
 * @package Protect Login
 */

/**
 * Prints the 2FA-Block: Offer install, if not existing, notfify if not active, link to user profiles if active.
 *
 * @return void
 */
function protect_login_print_2fa_data() {

	$twofa_slug = 'two-factor';
	if ( is_dir( WP_PLUGIN_DIR . '/' . $twofa_slug ) ) {
		if ( ! is_plugin_active( $twofa_slug . '/' . $twofa_slug . '.php' ) ) {
			/* translators: 'Two Factor' is the name of a plugin, please do not translate. */
			protect_login_print_description( __( 'You have installed the Two Factor plugin. Please activate it to increase login security.', 'protect-login' ) );
			return;
		}

		/* translators: 'Two Factor' is the name of a plugin, please do not translate. */
		protect_login_print_description( __( 'Great! You are already using the Two Factor plugin. You can manage your users 2FA setting on the individual user profiles.', 'protect-login' ) );
		echo '<a href="' . esc_url( admin_url( 'users.php' ) ) . '">' .
				esc_html__( 'To user list', 'protect-login' )
			. '</a>';

		return;
	}

	$action      = 'install-plugin';
	$install_url = wp_nonce_url(
		add_query_arg(
			array(
				'action' => $action,
				'plugin' => $twofa_slug,
			),
			admin_url( 'update.php' )
		),
		$action . '_' . $twofa_slug
	);
	?>

<div class="protect_login_item_container">
	<div class="protect_login_rcp--left">
		<div class="protect_login_rcp_image">
			<img width=100% src="<?php echo esc_url( PROTECT_LOGIN_URL . '/assets/images/twofactor.svg' ); ?>" alt="Two Factor"/>
		</div>
	</div>
	<div class="protect_login_rcp--right">
	<div>
	<h3 class="protect_login_rcp_title">
		<?php
			/* translators: 'Two Factor' is the name of a plugin, please do not translate. */
			echo esc_html__( 'Two Factor', 'protect-login' );
		?>
	</h3>
	</div>
	<div>
		<br />
		<p class="protect_login_rcp_description">
		<?php
			/* translators: 'Two Factor' is the name of a plugin, please do not translate. */
			echo esc_html__( 'Use the Two Factor plugin for a robust additional layer of authentication to your user\'s login credentials. This extra layer of security makes it significantly harder for unauthorized users to gain access, even if they have the user\'s password.', 'protect-login' );
		?>
		</p>
		<a class="button" href="<?php echo esc_url( $install_url ); ?>"><?php echo esc_html__( 'Install now', 'protect-login' ); ?></a>
		<a class="secondary-link publisher-site" href="https://wordpress.org/plugins/two-factor/" target="_blank">
			<span><?php echo esc_html__( 'View on WordPress.org', 'protect-login' ); ?></span>
			<svg class="new-tab-icon" width="10" height="10" viewBox="0 0 75 76" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M32.5 16H20.75H4V44V72H60V43.5" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M33.5 44.5L69 7" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M46.5 4.5H70.5V28.5" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</a>
	</div>
</div>
</div>
	<?php
}
