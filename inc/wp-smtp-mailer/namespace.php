<?php
/**
 * Figuren_Theater Security WP_SMTP_Mailer.
 *
 * @package figuren-theater/security/wp_smtp_mailer
 */

namespace Figuren_Theater\Security\WP_SMTP_Mailer;

use FT_VENDOR_DIR;

use SMTP_HOST;
use SMTP_PORT;
use SMTP_USER;
use SMTP_PASSWORD;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;

const BASENAME   = 'wp-smtp-mailer/smtp-mailer.php';
const PLUGINPATH = FT_VENDOR_DIR . '/dannyvankooten/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'phpmailer_init', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['security'];
	if ( ! $config['wp-smtp-mailer'] )
		return; // early

	// define( 'SMTP_HOST', 'smtp.gmail.com' );
	// define( 'SMTP_PORT', 465 );
	// define( 'SMTP_USER', 'your email' );
	// define( 'SMTP_PASSWORD', 'your password' );

	defined( 'SMTP_HOST' )     || define( 'SMTP_HOST',     get_env('FT_SMTP_HOST') );
	defined( 'SMTP_PORT' )     || define( 'SMTP_PORT',     get_env('FT_SMTP_PORT') );
	defined( 'SMTP_USER' )     || define( 'SMTP_USER',     get_env('FT_SMTP_USER') );
	defined( 'SMTP_PASSWORD' ) || define( 'SMTP_PASSWORD', get_env('FT_SMTP_PASSWORD') );

	require_once PLUGINPATH;

}

