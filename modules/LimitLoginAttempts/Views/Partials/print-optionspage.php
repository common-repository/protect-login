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
function protect_login_print_options_page( string $page ) {

	$url = admin_url( $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=tab1' );

	echo '<form action="' . esc_html( $url ) . '&_nonce=' . esc_html( wp_create_nonce() ) . '" method="post">';
	do_settings_sections( PROTECT_LOGIN_SLUG . '-limit-login-attempts' );
	do_settings_sections( PROTECT_LOGIN_SLUG . '-notify-section' );
	do_settings_sections( PROTECT_LOGIN_SLUG . '-advanced-section' );
	submit_button();
	echo '</form>';
}

/**
 * Displays the setting page in read-only mode in case the settings are individually set.
 *
 * @return void
 */
function protect_login_options_page_readonly() {
	$lockout_duration      = protect_login_get_current_lockout_duration() / MINUTE_IN_SECONDS;
	$long_lockout_duration = protect_login_get_current_long_duration() / HOUR_IN_SECONDS;

	?>
	<p id="protect-login-readonly-reason">
		<?php echo esc_html__( 'This page is read-only because the security settings are set programmatically. Please get in touch with your admin if you need help.', 'protect-login' ); ?>
	</p>
	<p> <?php echo esc_html__( 'The current settings are:', 'protect-login' ); ?></p>
	<table id="protect-login-settings-readonly-table">
		<tr>
			<td><?php echo esc_html__( 'Retries until lockout:', 'protect-login' ); ?></td>
			<td><?php echo esc_html( protect_login_get_current_max_retries() ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Duration of lockout:', 'protect-login' ); ?></td>
			<td>
				<?php
				echo esc_html(
					wp_sprintf(
					/* translators: %n is the number of minutes, for how long a user is locked out */
						_n(
							'%d minute',
							'%d minutes',
							$lockout_duration,
							'protect-login'
						),
						$lockout_duration
					)
				);
				?>
			</td>
		</tr>

		<tr>
			<td><?php echo esc_html__( 'Lockouts until long duration lockout:', 'protect-login' ); ?></td>
			<td><?php echo esc_html( protect_login_get_current_max_allowed_lockouts() ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Duration of long-term lockout:', 'protect-login' ); ?></td>
			<td>
			<?php
			echo esc_html(
				wp_sprintf(
				/* translators: %n is the number of hours, for how long a user is locked when the long-term lockout is active */
					_n(
						'%d hour',
						'%d hours',
						$long_lockout_duration,
						'protect-login'
					),
					$long_lockout_duration
				)
			);
			?>
				</td>
		</tr>

		<tr>
			<td><?php echo esc_html__( 'Show count of lockouts in widget:', 'protect-login' ); ?></td>
			<td>
			<?php
			if ( protect_login_get_current_show_in_widget() ) {
				echo esc_html__( 'Yes', 'protect-login' );
			} else {
				echo esc_html__( 'No', 'protect-login' );
			}
			?>
			</td>
		</tr>

	</table>
	<?php
}
