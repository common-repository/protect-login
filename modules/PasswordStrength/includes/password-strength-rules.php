<?php
/**
 * Used for preapring the .js file to show the user the required password strength
 *
 * @package Protect Login
 */

/**
 * Returns the minimum passpword strength the user has to set
 *
 * @return string
 */
function protect_login_get_minimal_password_strength(): string {
	$min_password_strength = protect_login_get_current_password_minimal_strength();

	$possible_strengths = array(
		'1' => 'short, bad, good, strong',
		'2' => 'good, strong',
		'3' => 'strong',
	);

	return ' ' . $possible_strengths[ $min_password_strength ];
}
