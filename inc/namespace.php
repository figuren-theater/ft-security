<?php
/**
 * Figuren_Theater Security.
 *
 * @package figuren-theater/security
 */

namespace Figuren_Theater\Security;

use WP_ENVIRONMENT_TYPE;

use Altis;
use function Altis\register_module;


/**
 * Register module.
 */
function register() {

	$default_settings = [
		'enabled'                       => true, // needs to be set
		'limit-login-attempts-reloaded' => 'local' !== WP_ENVIRONMENT_TYPE,
		'smtp'                          => 'local' !== WP_ENVIRONMENT_TYPE,
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
 */
function bootstrap() {

	// Plugins
	Email_Address_Encoder\bootstrap();
	Limit_Login_Attempts_Reloaded\bootstrap();
	Passwords_Evolved\bootstrap();
	WP_Author_Slug\bootstrap();
	Wps_Hide_Login\bootstrap();
	
	// Best practices
	Passwords_Not_Weak\bootstrap();
	Passwords_Strong\bootstrap();
	SMTP\bootstrap();
}
