<?php
/**
 * Figuren_Theater Security SMTP.
 *
 * @package figuren-theater/security/smtp
 */

namespace Figuren_Theater\Security\SMTP;

# use FT_VENDOR_DIR;

# use SMTP_HOST;
# use SMTP_PORT;
# use SMTP_USER;
# use SMTP_PASSWORD;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;
use function get_bloginfo;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'phpmailer_init', __NAMESPACE__ . '\\load_plugin' );
}

function load_plugin( $phpmailer ) {

	$config = Figuren_Theater\get_config()['modules']['security'];
	if ( ! $config['smtp'] )
		return; // early
	
	if (! getenv('FT_SMTP_HOST') || ! getenv('FT_SMTP_PORT') || ! getenv('FT_SMTP_USER') || ! getenv('FT_SMTP_PASSWORD') )
		return;


	// define( 'SMTP_HOST', 'smtp.gmail.com' );
	// define( 'SMTP_PORT', 465 );
	// define( 'SMTP_USER', 'your email' );
	// define( 'SMTP_PASSWORD', 'your password' );

	// defined( 'SMTP_HOST' )     || define( 'SMTP_HOST',     getenv('FT_SMTP_HOST') );
	// defined( 'SMTP_PORT' )     || define( 'SMTP_PORT',     getenv('FT_SMTP_PORT') );
	// defined( 'SMTP_USER' )     || define( 'SMTP_USER',     getenv('FT_SMTP_USER') );
	// defined( 'SMTP_PASSWORD' ) || define( 'SMTP_PASSWORD', getenv('FT_SMTP_PASSWORD') );

	$phpmailer->isSMTP();     
	$phpmailer->Host       = getenv('FT_SMTP_HOST');
	// Ask it to use authenticate using the Username and Password properties
	$phpmailer->SMTPAuth   = true; 
	$phpmailer->Port       = getenv('FT_SMTP_PORT');
	$phpmailer->Username   = getenv('FT_SMTP_USER');
	$phpmailer->Password   = getenv('FT_SMTP_PASSWORD');

	// Additional settings…
	// Choose 'ssl' for SMTPS on port 465, or 'tls' for SMTP+STARTTLS on port 25 or 587
	$phpmailer->SMTPSecure = ( 465 === getenv('FT_SMTP_PORT') ) ? 'ssl' : 'tls'; 
	$phpmailer->From       = getenv('FT_SMTP_USER');
	$phpmailer->FromName   = get_bloginfo( 'name' );

}


