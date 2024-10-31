<?php
/**
 * File form-elements.php
 *
 * Contains control functions for remote API settings page
 *
 * @since 2024-09-16
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

/**
 * Header of Remote API client section
 *
 * @return void
 */
function protect_login_remote_api_client() {
	echo '<input type="hidden" name="remote-api-update" value="true" />';
	protect_login_print_description( __( 'You can synchronise your blocked and allowed addresses with a WP website using Protect Login. Configure the synchronisation adding the URL and API key of the Protect Login network, you want to join.', 'protect-login' ) );
}

/**
 * Header of Remote API host section
 *
 * @return void
 */
function protect_login_remote_api_host() {
	protect_login_print_description( __( 'If you want to declare this page as the network host of your Protect Login network, please copy the following data to the remote clients:', 'protect-login' ) );
}

$settings_page = PROTECT_LOGIN_SLUG . '-remote-api-section';
add_settings_section(
	'remote_api_section',
	__( 'Remote API Settings', 'protect-login' ),
	'protect_login_remote_api_client',
	$settings_page
);

add_settings_field(
	'protect_login_remote_api_endpoint_url',
	__( 'Remote API URL', 'protect-login' ),
	'protect_login_limit_logins_settings_callback',
	$settings_page,
	'remote_api_section',
	array(
		'setting'     => 'protect_login_remote_api_endpoint_url',
		'description' => __( 'Please enter URL of the remote Protect Login page', 'protect-login' ),
	)
);


add_settings_field(
	'protect_login_remote_api_endpoint_key',
	__( 'Remote API Key', 'protect-login' ),
	'protect_login_limit_logins_settings_callback',
	$settings_page,
	'remote_api_section',
	array(
		'setting'     => 'protect_login_remote_api_endpoint_key',
		'description' => __( 'Please enter the API key of the remote Protect Login page', 'protect-login' ),
	)
);


$settings_page = PROTECT_LOGIN_SLUG . '-remote-api-host-section';
add_settings_section(
	'remote_api_host_section',
	__( 'Configure as host', 'protect-login' ),
	'protect_login_remote_api_host',
	$settings_page
);

add_settings_field(
	'remote_api_host_url',
	__( 'Remote API Host URL', 'protect-login' ),
	'protect_login_readonly_textfield',
	$settings_page,
	'remote_api_host_section',
	array(
		'value' => PROTECT_LOGIN_API_HOST,
	)
);

add_settings_field(
	'protect_login_remote_api_host_key',
	__( 'Remote API Host key', 'protect-login' ),
	'protect_login_limit_logins_settings_callback',
	$settings_page,
	'remote_api_host_section',
	array(
		'setting'     => 'protect_login_remote_api_host_key',
		'minlength'   => 16,
		'description' => __( 'You can define a new API key here. Please copy it to the clients of your Protect Login network', 'protect-login' ),
	)
);
