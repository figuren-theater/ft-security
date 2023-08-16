<?php
/**
 * Figuren_Theater Security Disable_Login_Errors.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\Disable_Login_Errors;

use function add_action;

use function add_filter;
use function __;
use WP_Error;

/**
 * Set up hooks.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'wp_login_failed', __NAMESPACE__ . '\\load', 10, 2 );
}

/**
 * Use a login message that does not reveal the type of login error in an attempted brute-force.
 *
 * Disables default Login-Error hints and
 * fires after a user login has failed.
 *
 * @param      string   $username Username or email address.
 * @param      WP_Error $error    A WP_Error object with the authentication failure details.
 *
 * @subpackage [subpackage]
 * @version    2022-10-13
 * @author     Carsten Bach
 */
function load( string $username, WP_Error $error ) : void {

	if ( empty( $error->errors ) ) {
		return;
	}

	// the error-types
	// we want our security shield to trigger.
	$error_types = [
		'invalid_username',
		'invalid_email',
		'incorrect_password',
		'invalidcombo',
	];

	// Get keys of currently returned errors from the WP_Error object.
	$error_types_given = array_keys( $error->errors );

	// unorthodox way of asking:
	// are there some of A inside B?
	$errors_count = count(
		array_merge(
			array_flip( $error_types ),
			array_flip( $error_types_given )
		)
	);

	// only run
	// if new errors were silently merged into what we asked.
	if ( $errors_count === count( $error_types ) ) {

		// Add our filter to prevent theese error message for security reasons.
		add_filter( 'login_errors', __NAMESPACE__ . '\\nice_hint', 99, 1 );
	}

}

/**
 * Replace Login-Error Hints for security.
 *
 * Prevents Login Errors to syaing something about the reason,
 * why a password might be wrong.
 * This helps securing the site, against external attacks.
 *
 * @subpackage [subpackage]
 * @version    2022-10-10
 * @author     Carsten Bach
 *
 * @param string $error Login error message.
 *
 * @return     string       General error text.
 */
function nice_hint( string $error ) : string {

	return sprintf(
		'<strong>%1$s</strong><br>%2$s',
		__( 'Nicht textsicher?', 'figurentheater' ),
		__( 'Alles auf Anfang, bitte!', 'figurentheater' )
	);
}


