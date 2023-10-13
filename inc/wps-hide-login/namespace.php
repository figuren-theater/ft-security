<?php
/**
 * Figuren_Theater Security Wps_Hide_Login.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\Wps_Hide_Login;

use Figuren_Theater\Options;

use FT_VENDOR_DIR;

use function add_action;

use function add_filter;
use function remove_action;
use function remove_submenu_page;
use WPS\WPS_Hide_Login;

const BASENAME   = 'wps-hide-login/wps-hide-login.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	// Do not "require_once FT_VENDOR_DIR . '/' . BASENAME;" like normally, but instead load the later called autoloader.

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_settings_section', 11 );

	// Define Plugin constants, that are normally loaded by the plugin itself.
	define( 'WPS_HIDE_LOGIN_VERSION', '1.9.6' );
	define( 'WPS_HIDE_LOGIN_FOLDER', 'wps-hide-login' );

	// Usually this would look like: define( 'WPS_HIDE_LOGIN_URL', plugin_dir_url( __FILE__ ) );.
	define( 'WPS_HIDE_LOGIN_URL', FT_VENDOR_URL . '/wpackagist-plugin/' . WPS_HIDE_LOGIN_FOLDER . '/' );

	// Usually this would look like: define( 'WPS_HIDE_LOGIN_DIR', plugin_dir_path( __FILE__ ) );.
	define( 'WPS_HIDE_LOGIN_DIR', FT_VENDOR_DIR . '/wpackagist-plugin/' . WPS_HIDE_LOGIN_FOLDER . '/' );
	define( 'WPS_HIDE_LOGIN_BASENAME', BASENAME );

	require_once WPS_HIDE_LOGIN_DIR . 'autoload.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	// Do not use and run the normal activation routine
	// register_activation_hook( __FILE__, array( '\WPS\WPS_Hide_Login\Plugin', 'activate' ) );
	// ... for some unknown reason.

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\plugins_loaded_wps_hide_login_plugin' );
}
/**
 * Prevent an admin_notice
 *
 * Which shows an updated login_url even,
 * if it was not updated (because its defined in code)
 * on the general options page.
 *
 * @return void
 */
function plugins_loaded_wps_hide_login_plugin() :void {

	$_wps_plugin = WPS_Hide_Login\Plugin::get_instance();
	add_filter( 'load_textdomain_mofile', __NAMESPACE__ . '\\unload_i18n', 0, 2 );
	remove_action( 'admin_notices', [ $_wps_plugin, 'admin_notices' ] );
}

/**
 * Unloads the specified MO file for localization based on the domain.
 *
 * This function unloads the specified MO file for localization based on the provided domain.
 * If the domain is 'wps-hide-login', the function returns an empty string, effectively
 * preventing the MO file from being loaded. Otherwise, the function returns the original MO file path.
 *
 * @param string $mofile The path to the MO file for localization.
 * @param string $domain The domain associated with the localization.
 *
 * @return string The path to the MO file or an empty string if unloading is needed.
 */
function unload_i18n( string $mofile, string $domain ) : string {
	// Check if the domain is 'wps-hide-login'.
	if ( 'wps-hide-login' === $domain ) {
		// If the domain is 'wps-hide-login', prevent loading and return an empty string.
		return '';
	}

	// If the domain is not 'wps-hide-login', return the original MO file path.
	return $mofile;
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {

	$_options = [
		'whl_page'           => getenv( 'FT_SECURITY_LOGIN_SLUG' ),
		'whl_redirect_admin' => '', // Maybe it was better to send the requests to a trackable endpoint like 'wissen-ist-macht', but it should be a cacheable one!
		'whl_redirect'       => null, // Deprecated and unused, but not uncalled. The plugin looks if this exists and wants to delete_option() if any, so we add a nice return to prevent the lookup.
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
	remove_submenu_page( 'options-general.php', 'whl_settings' );
}

/**
 * There is no remove_settings_section(), since this ticket was opened in 2012.
 *
 * @see  https://core.trac.wordpress.org/ticket/37355
 *
 * @return void
 */
function remove_settings_section() :void {
	global $wp_settings_sections;

	unset( $wp_settings_sections['general']['wps-hide-login-section'] );
}
