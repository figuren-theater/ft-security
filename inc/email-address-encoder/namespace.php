<?php
/**
 * Figuren_Theater Security Email_Address_Encoder.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\Email_Address_Encoder;

use Figuren_Theater\Options;

use FT_VENDOR_DIR;

use function add_action;
use function add_filter;
use function remove_action;
use function remove_submenu_page;

const BASENAME   = 'email-address-encoder/email-address-encoder.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {
	/*
	 * The Plugin allows to define the following constants for convenience.
	 *
	 * Those constants should be defined now, if needed!
	 *
	 * Constant to adjust default filter priority:
	 * define( 'EAE_FILTER_PRIORITY', '' );
	 *
	 * This is the place to add "tel:12345" support:
	 * define( 'EAE_REGEXP', '' );
	 */

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );

	// The more intuitive way to just "remove_action( 'plugins_loaded', 'eae_load_textdomain' );" is not working :( .
	add_filter( 'load_textdomain_mofile', __NAMESPACE__ . '\\unload_i18n', 0, 2 );

	remove_action( 'admin_enqueue_scripts', 'eae_enqueue_admin_scripts' );
}

/**
 * Unloads the specified MO file for localization based on the domain.
 *
 * This function unloads the specified MO file for localization based on the provided domain.
 * If the domain is 'email-address-encoder', the function returns an empty string, effectively
 * preventing the MO file from being loaded. Otherwise, the function returns the original MO file path.
 *
 * @param string $mofile The path to the MO file for localization.
 * @param string $domain The domain associated with the localization.
 *
 * @return string The path to the MO file or an empty string if unloading is needed.
 */
function unload_i18n( string $mofile, string $domain ) : string {
	// Check if the domain is 'email-address-encoder'.
	if ( 'email-address-encoder' === $domain ) {
		// If the domain is 'email-address-encoder', prevent loading and return an empty string.
		return '';
	}

	// If the domain is not 'email-address-encoder', return the original MO file path.
	return $mofile;
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {

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
		// the Admin_Bar/email-encoder.js is not working.
		'eae_notices' => '1',
	];

	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	new Options\Factory(
		$_options,
		'Figuren_Theater\Options\Option',
		BASENAME
	);
}

/**
 * Hide the plugins admin-menu
 *
 * @return void
 */
function remove_menu() :void {
	remove_submenu_page( 'options-general.php', 'eae' );
}
