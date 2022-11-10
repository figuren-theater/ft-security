<?php
/**
 * Figuren_Theater Security Passwords_Evolved.
 *
 * @package figuren-theater/security/passwords_evolved
 */

namespace Figuren_Theater\Security\Passwords_Evolved;

use FT_VENDOR_DIR;

use Figuren_Theater\Options;

use function add_action;
use function remove_submenu_page;

const BASENAME   = 'passwords-evolved/passwords-evolved.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
	
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

function load_plugin() {

	require_once PLUGINPATH;

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}


function filter_options() {
	
	$_options = [
		'passwords_evolved_enforced_roles' => [
			'administrator',
			'editor',
			'author',
			'contributor',
			'subscriber',
		], 
	];

	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new Options\Factory( 
		$_options, 
		'Figuren_Theater\Options\Option', 
		BASENAME,
		'site_option'
	);
}

function remove_menu() : void {
	remove_submenu_page( 'settings.php', 'passwords-evolved' );
}
