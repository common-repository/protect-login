<?php
/**
 * Library for text elements
 *
 * @package Protect Login
 */

/**
 * Prints a textbox
 *
 * @param string $setting_name  Name of the html element.
 * @param string $setting_value Preiflled value.
 * @param string $setting_description Translated description to print.
 * @param ?int   $min_length Minimum required length of input, can be NULL.
 *
 * @return void
 */
function protect_login_print_textbox( string $setting_name, string $setting_value, ?string $setting_description, ?int $min_length ) {
	echo '<input ';
	if ( null !== $min_length ) {
		echo 'minlength="' . esc_html( $min_length ) . '"'; }
		echo 'type="text"
	    name="' . esc_html( $setting_name ) . '" 
	    value="' . esc_html( $setting_value ) . '" />';
	if ( null !== $setting_description ) {
		protect_login_print_description( $setting_description );
	}
}
