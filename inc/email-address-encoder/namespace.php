<?php
/**
 * Figuren_Theater Security Email_Address_Encoder.
 *
 * @package figuren-theater/security/email_address_encoder
 */

namespace Figuren_Theater\Security\Email_Address_Encoder;

use FT_VENDOR_DIR;

use Figuren_Theater\Options;

use function add_action;
use function add_filter;
use function remove_action;
use function remove_submenu_page;

const BASENAME   = 'email-address-encoder/email-address-encoder.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	// define( 'EAE_FILTER_PRIORITY', '' );
	// define( 'EAE_REGEXP', '' ); // <-- this is the place to add "tel:12345" support

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
	
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {

	require_once PLUGINPATH;
	
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );

	// remove_action( 'plugins_loaded', 'eae_load_textdomain' ); // not working :(
	add_filter( 'load_textdomain_mofile', __NAMESPACE__ . '\\unload_i18n', 0, 2 );

	//
	remove_action( 'admin_enqueue_scripts', 'eae_enqueue_admin_scripts' );
}

function unload_i18n( string $mofile, string $domain ) : string {
	if ( 'email-address-encoder' === $domain ) {
		return '';
	}
	return $mofile;
}

function filter_options() {
	
	$_options = [
		'eae_filter_priority' => 1000,
		'eae_search_in' => 'filters',
		// lets see what the automatic email-detector will tell us,
		// but only us and only locally
		// 
		// but beware
		// '0' shows notices
		// '1' hides them
		// 
		// 'eae_notices' => ( 'local' === \WP_ENVIRONMENT_TYPE && \is_super_admin() ) ? '0' : '1', // TATAL ERROR // fn is undefined
		// 
		// 'eae_notices' => ( \WP_DEBUG ) ? '0' : '1', 
		// the Admin_Bar/email-encoder.js is not working
		// 
		'eae_notices' => '1', 
	];

	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new Options\Factory( 
		$_options, 
		'Figuren_Theater\Options\Option', 
		BASENAME, 
	);
}

function remove_menu() : void {
	
	//
	remove_submenu_page( 'options-general.php', 'eae' );
}
