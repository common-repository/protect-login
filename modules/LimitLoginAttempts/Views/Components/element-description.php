<?php
/**
 * Library for descriptions
 *
 * @package Protect Login
 */

/**
 * Prints a description
 *
 * @param string $description Translated description to print.
 *
 * @return void
 */
function protect_login_print_description( string $description ) {
	echo '<p class="description">' . esc_html( $description ) . '</p>';
}
