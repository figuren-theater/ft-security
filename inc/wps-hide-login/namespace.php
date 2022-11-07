<?php
/**
 * Figuren_Theater Security Wps_Hide_Login.
 *
 * @package figuren-theater/security/wps_hide_login
 */

namespace Figuren_Theater\Security\Wps_Hide_Login;

use Figuren_Theater\Options;

use function add_action;
use function remove_submenu_page;

const BASENAME   = 'wps-hide-login/wps-hide-login.php';
const LOGIN_SLUG = 'buehneneingang';

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
	
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

function load_plugin() {

	require_once FT_VENDOR_DIR . '/' . BASENAME;
	
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}


function filter_options() {
	
	$_options = [
		'whl_page'           => LOGIN_SLUG,
		'whl_redirect_admin' => 'wp-admin/wissen-ist-macht',
		'whl_redirect'       => null, // deprecated and unused, but not uncalled. The plugin looks if this exists and wants to delete_option() if any, so we add a nice return to prevent the lookup
	];

	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new Options\Factory( 
		$_options, 
		'Figuren_Theater\Options\Option', 
		BASENAME, 
	);
	new Options\Factory( 
		$_options, 
		'Figuren_Theater\Options\Option', 
		BASENAME,
		'site_option'
	);
}

function remove_menu() : void {
	remove_submenu_page( 'options-general.php', 'whl_settings' );
}
