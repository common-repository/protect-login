<?php
/**
 * Function to print currently blocked ips
 *
 * @package Protect Login
 */

/**
 * Prints the list of currently blocked ips
 *
 * @param array  $blocked_ips Currently blocked ips.
 * @param string $page Menu Slug.
 *
 * @return void
 */
function protect_login_print_lockedout_list( array $blocked_ips, string $page ) {
	$admin_url = $page . '?page=' . PROTECT_LOGIN_SLUG . '&tab=';
	if ( count( $blocked_ips ) === 0 ) {
		?>
		<div class="protect-login-no-blocked-ips">
		<?php echo esc_html__( 'There are no IP addresses blocked.', 'protect-login' ); ?>
		</div>
		<?php
	} else {
		?>
		<p style="width: 100%; text-align: right">
			<input type="text" id="searchInput"
					onkeyup="protectLoginSearchtable('protect_login_blocked_ip_table', this)"
					placeholder="<?php echo esc_html__( 'Search for IP address', 'protect-login' ); ?>">
		</p>

		<table class="wp-list-table widefat fixed striped table-view-list" id="protect_login_blocked_ip_table">
			<thead>
			<tr>
				<th scope="col" class="manage-column column-name"><?php echo esc_html__( 'IP address', 'protect-login' ); ?></th>
				<th class="manage-column column-name"><?php echo esc_html__( 'Blocked until', 'protect-login' ); ?></th>
				<th class="manage-column column-name"><?php echo esc_html__( 'Actions', 'protect-login' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $blocked_ips as $ip => $blocked_until ) {
				$timezone           = new DateTimeZone( get_option( 'timezone', 'UTC' ) );
				$permantely_blocked = in_array( $ip, get_option( 'protect_login_limit_login_blocklist', array() ), true );
				?>
					<tr>
						<td><?php echo esc_html( str_replace( '-', '.', $ip ) ); ?></td>
				<?php

				if ( $permantely_blocked ) {
					?>
						<td><?php echo esc_html__( 'Permanently blocked', 'protect-login' ); ?></td>
					<?php
				} else {
					?>
					<td>
						<?php echo esc_html( wp_date( 'd.m.Y', $blocked_until, $timezone ) ); ?>
						<br />
						<?php echo esc_html( wp_date( 'H:i', $blocked_until, $timezone ) ); ?>
					<?php
				}
				?>
				<td>
					<?php
					if ( $permantely_blocked ) {
						echo esc_html__( 'No actions allowed', 'protect-login' );
					} else {
						$encoded_ip = str_replace( '.', '_', $ip );
						?>
						<a href=" <?php echo esc_url( admin_url( $admin_url . 'tab5&action=release&ip=' . $encoded_ip ) ); ?>">
							<?php echo esc_html__( 'Release IP address', 'protect-login' ); ?> </a><br />

						<a href="<?php echo esc_url( admin_url( $admin_url . 'tab5&action=toBlock&ip=' . $encoded_ip ) ); ?>">
							<?php echo esc_html__( 'Add IP address to blocklist', 'protect-login' ); ?> </a><br />

						<a href="<?php echo esc_url( admin_url( $admin_url . 'tab5&action=toAllow&ip=' . $encoded_ip ) ); ?> ">
							<?php echo esc_html__( 'Add IP address to allowlist and release', 'protect-login' ); ?> </a>
						<?php
					}
					?>

				</td>
			</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
	}
}
