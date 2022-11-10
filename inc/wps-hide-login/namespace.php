<?php
/**
 * Figuren_Theater Security Wps_Hide_Login.
 *
 * @package figuren-theater/security/wps_hide_login
 */

namespace Figuren_Theater\Security\Wps_Hide_Login;

use FT_VENDOR_DIR;

use Figuren_Theater\Options;

use WPS\WPS_Hide_Login;

use function add_action;
use function add_filter;
use function remove_submenu_page;

const BASENAME   = 'wps-hide-login/wps-hide-login.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
	
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	// require_once FT_VENDOR_DIR . '/' . BASENAME;
	
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_settings_section', 11 );

####################################


	// Plugin constants
	define( 'WPS_HIDE_LOGIN_VERSION', '1.9.6' );
	define( 'WPS_HIDE_LOGIN_FOLDER', 'wps-hide-login' );

	// define( 'WPS_HIDE_LOGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'WPS_HIDE_LOGIN_URL', FT_VENDOR_URL . '/wpackagist-plugin/' . WPS_HIDE_LOGIN_FOLDER . '/' );
	// define( 'WPS_HIDE_LOGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'WPS_HIDE_LOGIN_DIR', FT_VENDOR_DIR . '/wpackagist-plugin/' . WPS_HIDE_LOGIN_FOLDER . '/' );
	define( 'WPS_HIDE_LOGIN_BASENAME', BASENAME );

	// wp_die(var_export([
	// 	WPS_HIDE_LOGIN_URL,
	// 	WPS_HIDE_LOGIN_DIR,
	// 	WPS_HIDE_LOGIN_BASENAME,
	// ],true));


	require_once WPS_HIDE_LOGIN_DIR . 'autoload.php';

	// register_activation_hook( __FILE__, array( '\WPS\WPS_Hide_Login\Plugin', 'activate' ) );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\plugins_loaded_wps_hide_login_plugin' );
}

function plugins_loaded_wps_hide_login_plugin() {
	WPS_Hide_Login\Plugin::get_instance();

	// paath relative to WP_PLUGIN_DIR
#	$_rel_path = '../../vendor/' . WPS_HIDE_LOGIN_FOLDER . '/languages';

	// load_plugin_textdomain( 'wps-hide-login', false, dirname( WPS_HIDE_LOGIN_BASENAME ) . '/languages' );
#	load_plugin_textdomain( 'wps-hide-login', false, $_rel_path );


	add_filter( 'load_textdomain_mofile', __NAMESPACE__ . '\\unload_i18n', 0, 2 );
}

function unload_i18n( string $mofile, string $domain ) : string {
	if ( 'wps-hide-login' === $domain ) {
		return '';
	}
	return $mofile;
}



function filter_options() {
	
	$_options = [
		'whl_page'           => getenv( 'FT_SECURITY_LOGIN_SLUG' ),
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

function remove_settings_section() {
	/**
	 * there is no remove_settings_section()
	 *
	 * @see  https://core.trac.wordpress.org/ticket/37355
	 */
	global $wp_settings_sections;
	// \do_action( 'qm/debug', $wp_settings_sections );

	unset( $wp_settings_sections[ 'general' ][ 'wps-hide-login-section' ] );
}
