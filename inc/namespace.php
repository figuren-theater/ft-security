<?php
/**
 * Figuren_Theater Security.
 *
 * @package figuren-theater/security
 */

namespace Figuren_Theater\Security;

use Altis;

/**
 * Register module.
 */
function register() {
	Altis\register_module(
		'security',
		DIRECTORY,
		'Security',
		[
			'defaults' => [
				'enabled' => true,
			],
		],
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	Email_Address_Encoder\bootstrap();
	Password\bootstrap();
	Passwords_Evolved\bootstrap();
	Wps_Hide_Login\bootstrap();
}
