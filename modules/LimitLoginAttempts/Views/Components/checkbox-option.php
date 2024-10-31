<?php
/**
 * Library for checkbox elements
 *
 * @package Protect Login
 */

/**
 * Displays a checkbox element. When the setting name is found in wp_options, the default is checked.
 *
 * @param string $setting_name  The name of the setting.
 * @param string $description Translated description to print.
 *
 * @return void
 */
function protect_login_print_checkbox( string $setting_name, ?string $description ) {
	$current_setting = get_option( $setting_name, array() );
	if ( ! is_array( $current_setting ) ) {
		$current_setting = array( $current_setting );
	}

	$options = array(
		'protect_login_limit_login_lockout_notify' => array(
			'email' => esc_html__( 'E-Mail to site admin', 'protect-login' ),
		),

		'protect_login_show_in_widget'             => array(
			'1' => esc_html__( 'Display counter on Dashboard', 'protect-login' ),
		),
	);

	if ( ! isset( $options[ $setting_name ] ) ) {
		return;
	}

	$setting = $options[ $setting_name ];

	foreach ( $setting as $cb_option => $option_text ) {
		if ( 'protect_login_show_in_widget' === $setting_name ) {
			$cb_option = (string) $cb_option;
		}

		$checked = in_array( $cb_option, $current_setting, true ) ? 'checked ' : '';

		echo '<input ' .
			esc_html( $checked ) .
			'type="checkbox" 
            name="' . esc_html( $setting_name ) . '[]" 
            value="' . esc_html( $cb_option ) . '"
            id="setting_' . esc_html( $setting_name . '_' . $cb_option ) . '" />' .
			'<label for="setting_' .
					esc_html( $setting_name . '_' . $cb_option ) . '">' .
						esc_html( $option_text ) . '</label><br />';
		if ( null !== $description ) {
			protect_login_print_description( $description );
		}
	}
}
