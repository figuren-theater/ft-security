<?php
/**
 * Figuren_Theater Security.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security;

use Altis;

use WP_ENVIRONMENT_TYPE;

/**
 * Register module.
 *
 * @return void
 */
function register() :void {

	$default_settings = [
		'enabled'                       => true, // Needs to be set.
		'limit-login-attempts-reloaded' => 'local' !== WP_ENVIRONMENT_TYPE,
		'smtp'                          => 'local' !== WP_ENVIRONMENT_TYPE,
		'two-factor'                    => 'local' !== WP_ENVIRONMENT_TYPE,
	];

	$options = [
		'defaults' => $default_settings,
	];

	Altis\register_module(
		'security',
		DIRECTORY,
		'Security',
		$options,
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	// Plugins.
	Email_Address_Encoder\bootstrap();
	Limit_Login_Attempts_Reloaded\bootstrap();
	Passwords_Evolved\bootstrap();
	Two_Factor\bootstrap();
	WP_Author_Slug\bootstrap();
	Wps_Hide_Login\bootstrap();

	// Best practices.
	Disable_Login_Errors\bootstrap();
	Passwords_Not_Weak\bootstrap();
	Passwords_Strong\bootstrap();
	SMTP\bootstrap();
}
