<?php
/**
 * Prints the form for block and allowlist
 *
 * @package Protect Login
 */

use ProtectLogin\Modules\LimitLoginAttempts\Controllers\OptionsPage as LimitLoginAttemptsOptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Updates entries on Block or allowlist
 *
 * @param string $list_type Are we on blocklist or allowlist.
 * @param string $ips_to_add Array with ip address to add.
 *
 * @return void
 */
function protect_login_update_block_or_allowlist( string $list_type, string $ips_to_add ) {
	foreach ( explode( ' ', $ips_to_add ) as $new_ip ) {
		$new_ip = trim( $new_ip );
		if ( '' !== $new_ip ) {
			if ( 'blocklist' === $list_type ) {
				LimitLoginAttemptsOptions::add_to_blocklist( $new_ip );
			} else {
				LimitLoginAttemptsOptions::add_to_allowlist( $new_ip );
			}
		}
	}
}

/**
 * Prints a block- or allowlist
 *
 * @param string $list_type Which list to print (blocklist|allowlist).
 * @param string $page Menu Slug.
 * @param array  $remote_allowlisted_addresses allowlisted addressed from Remote API.
 *
 * @return void
 */
function protect_login_print_block_allow_form( string $list_type, string $page, array $remote_allowlisted_addresses ) {
	$elements = get_option( 'protect_login_limit_login_' . $list_type, array() );
	$elements = array_unique( array_merge( $elements, $remote_allowlisted_addresses ) );
	?>

	<input type="hidden" name="save_protect_login_balist_list_type" value="<?php echo esc_html( $list_type ); ?>" />
	<p style="width: 100%; text-align: right">
		<input type="text" id="searchInput"
				onkeyup="protectLoginSearchtable('myTable', this)"
				placeholder="<?php echo esc_html__( 'Search for IP address', 'protect-login' ); ?>">
	</p>
	<table class="wp-list-table widefat fixed striped table-view-list" id="myTable">
		<thead>
		<tr>
			<th scope="col" class="manage-column column-name"><?php echo esc_html__( 'IP address', 'protect-login' ); ?></th>
			<th style="width: 100px;" class="manage-column column-name"><?php echo esc_html__( 'Actions', 'protect-login' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $elements as $current_ip ) {
			echo '<tr>';
			echo '<td>' . esc_html( str_replace( '-', '.', $current_ip ) ) . '</td>';
			echo '<td><a href="' . esc_html(
				admin_url(
					$page . '?page=' . PROTECT_LOGIN_SLUG . '&action=removeFromList' .
					'&list=' . esc_html( $list_type ) . '&ip=' . esc_html( str_replace( '.', '_', $current_ip ) )
				)
			) . '">'
				. esc_html__( 'Delete', 'protect-login' ) . '</a></td>';
			echo '</tr>';
		}
		?>
		</tbody>
	</table>


	</div>
	<div class="protect_login_setting_box">
		<h3><?php echo esc_html__( 'Add IP address', 'protect-login' ); ?></h3>
		<p>
			<textarea
					placeholder="<?php echo esc_html__( 'Please use line breaks to enter multiple IP addresses', 'protect-login' ); ?>"
					name="new_ips[]"
					style="width: 350px;" rows="5"></textarea>
		</p>
	</div>
	<?php
}
