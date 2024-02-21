<?php
/**
 * Figuren_Theater Security SMTP.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\SMTP;

use Figuren_Theater;
use PHPMailer\PHPMailer;
use function add_action;
use function get_bloginfo;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'phpmailer_init', __NAMESPACE__ . '\\load_plugin' );
}

/**
 * Conditionally load the modifications.
 *
 * @param PHPMailer\PHPMailer $phpmailer The PHPMailer instance (passed by reference).
 *
 * @return void
 */
function load_plugin( PHPMailer\PHPMailer $phpmailer ): void {

	$config = Figuren_Theater\get_config()['modules']['security'];
	if ( ! $config['smtp'] ) {
		return;
	}

	if ( ! getenv( 'FT_SMTP_HOST' ) || ! getenv( 'FT_SMTP_PORT' ) || ! getenv( 'FT_SMTP_USER' ) || ! getenv( 'FT_SMTP_PASSWORD' ) ) {
		return;
	}

	defined( 'SMTP_HOST' ) || define( 'SMTP_HOST', getenv( 'FT_SMTP_HOST' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	defined( 'SMTP_PORT' ) || define( 'SMTP_PORT', getenv( 'FT_SMTP_PORT' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	defined( 'SMTP_USER' ) || define( 'SMTP_USER', getenv( 'FT_SMTP_USER' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
	defined( 'SMTP_PASSWORD' ) || define( 'SMTP_PASSWORD', getenv( 'FT_SMTP_PASSWORD' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound

	$phpmailer->isSMTP();
	$phpmailer->Host     = SMTP_HOST;
	$phpmailer->Port     = (int) SMTP_PORT;
	$phpmailer->Username = SMTP_USER;
	$phpmailer->Password = SMTP_PASSWORD;
	$phpmailer->SMTPAuth = true; // Ask it to authenticate using the Username and Password properties.

	// Additional settings…
	// Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587.
	$phpmailer->SMTPSecure = ( 465 === (int) SMTP_PORT ) ? 'ssl' : 'tls';
	$phpmailer->From       = SMTP_USER;
	$phpmailer->FromName   = get_bloginfo( 'name' );
}
