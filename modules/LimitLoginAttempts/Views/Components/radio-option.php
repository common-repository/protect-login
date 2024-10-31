<?php
/**
 * Library for radio elements
 *
 * @package Protect Login
 */

/**
 * Displays a radio element. When the setting name is found in wp_options, the
 * corresponding option is prefilled
 *
 * @param string $setting_name  The name of the setting.
 *
 * @return void
 */
function protect_login_print_radio( string $setting_name ) {
	$current_setting = get_option( $setting_name, '' );
	$options         = array(
		'protect_login_limit_login_client_type'   => array(
			/* translators: "Direct connection" means, that the page is directly accessible and not behind a proxy */
			'REMOTE_ADDR'          => esc_html__( 'Direct connection', 'protect-login' ),
			/* translators: "Behind a proxy" means, that the page is behind a proxy (maybe a CDN) */
			'HTTP_X_FORWARDED_FOR' => esc_html__( 'Behind a proxy', 'protect-login' ),
		),
		'protect_login_password_minimal_strength' => array(
			/* translators: "Weak (WordPress Default)": The lowest password security level available */
			'1' => esc_html__( 'weak (WordPress Default)', 'protect-login' ),
			/* translators: "Medium": The WordPress password level needs to be medium or higher */
			'2' => esc_html__( 'medium', 'protect-login' ),
			/* translators: "Strong (recommended)": Only passwords which WordPress marks as strong or very strong are allowed */
			'3' => esc_html__( 'strong (recommended)', 'protect-login' ),
		),

		'protect_login_protection_level'          => array(
			/* translators: Description for the lowest protection level */
			'1' => esc_html__( 'low', 'protect-login' ),
			/* translators: Description for the medium protection level */
			'2' => esc_html__( 'medium', 'protect-login' ),
			/* translators: Description for the highest protection level */
			'3' => esc_html__( 'high', 'protect-login' ),
		),
	);

	if ( ! isset( $options[ $setting_name ] ) ) {
		return;
	}

	$setting = $options[ $setting_name ];

	foreach ( $setting as $radio_option  => $option_text ) {
		$checked = $current_setting === (string) $radio_option ? 'checked ' : '';
		echo '<input  
             type="radio"
              name="' . esc_html( $setting_name ) . '"
              value="' . esc_html( $radio_option ) . '" ' . esc_html( $checked ) .
			' id="setting_' . esc_html( $setting_name . '_' . $radio_option ) . '" />' .
			'<label for="setting_' . esc_html( $setting_name . '_' . $radio_option ) . '">' .
			esc_html( $option_text ) .
			'</label><br />';
	}
}
