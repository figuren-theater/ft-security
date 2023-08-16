<?php
/**
 * Figuren_Theater Security Passwords_Evolved.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\Passwords_Evolved;

use Figuren_Theater\Options;

use FT_VENDOR_DIR;

use function add_action;
use function remove_submenu_page;

const BASENAME   = 'passwords-evolved/passwords-evolved.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {

	$_options = [
		'passwords_evolved_enforced_roles' => [
			'administrator',
			'editor',
			'author',
			'contributor',
			'subscriber',
		],
	];

	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	new Options\Factory(
		$_options,
		'Figuren_Theater\Options\Option',
		BASENAME,
		'site_option'
	);
}

/**
 * Hide the plugins admin-menu
 *
 * @return void
 */
function remove_menu() :void {
	remove_submenu_page( 'settings.php', 'passwords-evolved' );
}
