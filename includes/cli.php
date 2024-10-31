<?php
/**
 * File cli.php
 *
 * Contains the CLI functions of this plugin.
 *
 * @since 2024-08-28
 * @license GPL-3.0-or-later
 *
 * @package ProtectLogin/CLI
 */

WP_CLI::add_command(
	'protectone login address list-blocked',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliAddressHandler', 'list_blocked' )
);

WP_CLI::add_command(
	'protectone login address release',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliAddressHandler', 'release' )
);

WP_CLI::add_command(
	'protectone login address remove',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliAddressHandler', 'remove' )
);

WP_CLI::add_command(
	'protectone login address allow',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliAddressHandler', 'allow' )
);

WP_CLI::add_command(
	'protectone login address block',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliAddressHandler', 'block' )
);

WP_CLI::add_command(
	'protectone login settings level',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliSettingsHandler', 'security_level' )
);

WP_CLI::add_command(
	'protectone login settings lockout-duration',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliSettingsHandler', 'get_lockout_duration' )
);

WP_CLI::add_command(
	'protectone login settings long-duration',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliSettingsHandler', 'get_lockout_long_duration' )
);

WP_CLI::add_command(
	'protectone login settings max-retries',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliSettingsHandler', 'get_retries' )
);

WP_CLI::add_command(
	'protectone login settings allowed-lockouts',
	array( '\ProtectLogin\Modules\LimitLoginAttempts\Controllers\CliSettingsHandler', 'get_allowed_lockouts' )
);

WP_CLI::add_command(
	'protectone password settings minimum-strength',
	array( '\ProtectLogin\Modules\PasswordStrength\Controllers\CliPasswordHandler', 'minimum_strength' )
);
