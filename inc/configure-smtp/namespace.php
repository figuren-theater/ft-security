<?php
/**
 * Figuren_Theater Security Configure_SMTP.
 *
 * @package figuren-theater/security/configure_smtp
 */

namespace Figuren_Theater\Security\Configure_SMTP;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;

const BASENAME   = 'configure-smtp/configure-smtp.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	#add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
	
	add_action( 'init', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {

	// $config = Figuren_Theater\get_config()['modules']['performance'];
	// if ( ! $config['configure-smtp'] )
	// 	return; // early

	require_once PLUGINPATH;

	add_action( 'init', __NAMESPACE__ . '\\unload_plugin_parts', 11 );
}

function unload_plugin_parts() {
	
	$plugin_class = $GLOBALS['c2c_configure_smtp'];

	// Disable, as it is handled by 'WP Better Emails'
	remove_filter( 'wp_mail_from',      [ $plugin_class, 'wp_mail_from' ] );
	remove_filter( 'wp_mail_from_name', [ $plugin_class, 'wp_mail_from_name' ] );

	global $pagenow;
	if ( 'options-general.php' == $pagenow )
		remove_action( 'admin_print_footer_scripts', [ $plugin_class, 'add_js' ] );
}

function filter_options() {
	
	$_options = [

	];

	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new Options\Option(
		'wpbe_options',
		$_options,
		BASENAME,
	);
}
