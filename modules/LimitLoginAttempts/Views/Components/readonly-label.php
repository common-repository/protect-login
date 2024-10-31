<?php
/**
 * File readonly-label.php
 *
 * Conatins function for displaying a readonly labek
 *
 * @since 2024-09-16
 * @license GPL-3.0-or-later
 *
 * @package ProtectLogin
 */

/**
 * Prints a read-only label
 *
 * @param string      $setting_value Value to display.
 * @param string|null $setting_description Optional: A description to print after the label.
 *
 * @return void
 */
function protect_login_print_label( string $setting_value, ?string $setting_description ) {
	echo '<label>' . esc_html( $setting_value ) . '</label>';
	if ( null !== $setting_description ) {
		protect_login_print_description( $setting_description );
	}
}
