<?php
/**
 * Conatins function for displaying the status box
 *
 * @package ProtectLogin
 */

/**
 * Function to display status message box
 *
 * @param string $message   The messafe to display.
 * @param bool   $succeeded   Show if a success message (error message otherwise).
 *
 * @return void
 */
function protect_login_show_message( string $message, bool $succeeded = true ) {
	echo '<div class="notice notice-' . ( $succeeded ? 'success' : 'error' ) . '" style="padding: 5px 10px;">';
	echo esc_html( $message );
	echo '</div>';
}
