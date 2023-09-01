<?php
/**
 * Figuren_Theater Security Limit_Login_Attempts_Reloaded.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\Limit_Login_Attempts_Reloaded;

use Figuren_Theater;
use Figuren_Theater\Options;
use FT_VENDOR_DIR;
use function add_action;
use function add_filter;
use function remove_action;

const BASENAME   = 'limit-login-attempts-reloaded/limit-login-attempts-reloaded.php';
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

	$config = Figuren_Theater\get_config()['modules']['security'];
	if ( ! $config['limit-login-attempts-reloaded'] ) {
		return;
	}

	// Becauce the LLAR plugin checks for is_plugin_active_for_network(),
	// we need to filter 'active_sitewide_plugins'.
	add_filter( 'site_option_active_sitewide_plugins', __NAMESPACE__ . '\\filter_site_option', 0 );

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_admin_notice', 11 );

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}

/**
 * Handle site-options
 *
 * @param array<string, string> $active_sitewide_plugins Array of site-wide installed plugins
 *
 * @return array<string, string>
 */
function filter_site_option( array $active_sitewide_plugins ) :array {
	$active_sitewide_plugins[ BASENAME ] = BASENAME;
	return $active_sitewide_plugins;
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {

	$_options = [
		'limit_login_allow_local_options'            => 0, // Do not use FALSE, because it gets handled as "non existing option", so the query will be done.

		// Not only 'activation', but also the 'last updated' timestamp.
		'limit_login_activation_timestamp'           => filemtime( FT_VENDOR_DIR . PLUGINPATH ),
		'limit_login_notice_enable_notify_timestamp' => time(),
		'limit_login_active_app'                     => 'local',
		'limit_login_admin_notify_email'             => getenv( 'FT_SECURITY_LLAR_EMAIL' ),
		'limit_login_allowed_lockouts'               => 4,
		'limit_login_allowed_retries'                => 4,
		'limit_login_auto_update_choice'             => 0,
		'limit_login_gdpr'                           => 1,
		'limit_login_lockout_duration'               => 1200, // 0 // for debug
		'limit_login_lockout_notify'                 => 'email',
		'limit_login_long_duration'                  => 86400,
		'limit_login_notify_email_after'             => 4,
		'limit_login_trusted_ip_origins'             => [ 'HTTP_X_REAL_IP', 'REMOTE_ADDR' ], // @see https://wordpress.org/support/topic/reverse-proxy-7/#post-16656462
		'limit_login_valid_duration'                 => 43200,
		'limit_login_app_setup_link'                 => 0, // Premium-related // do not use FALSE ...
		'limit_login_show_top_level_menu_item'       => 0,
		'limit_login_hide_dashboard_widget'          => true,
		'limit_login_show_warning_badge'             => 0, // @since 2.25.3
		'limit_login_whitelist'                      => [],
		'limit_login_whitelist_usernames'            => [],
		'limit_login_blacklist'                      => [],
		'limit_login_blacklist_usernames'            => [ 'admin', 'administrator' ],
		'limit_login_review_notice_shown'            => 1,

		/**
		 * Options that are threated by the plugin (like normal),
		 * that maybe used for debugging and are kept here
		 * for documentation.
		 *
		 * 'limit_login_lockouts'       => NULL,
		 * 'limit_login_lockouts_total' => 2,
		 * 'limit_login_logged'         => NULL,
		 * 'limit_login_long_duration'  => 0,
		 * 'limit_login_retries'        => a:0:{},
		 * 'limit_login_retries_valid'  => a:0:{},
		 */

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
 * Disables the annoying dashboard admin notice to leave a review on the plugin.
 */
function remove_admin_notice() : void {

	global $limit_login_attempts_obj;
	remove_action( 'admin_notices', [ $limit_login_attempts_obj, 'show_leave_review_notice' ] );
}

/**
 * Hide the plugins admin-menu
 *
 * @return void
 */
function remove_menu() :void {
	remove_submenu_page( 'settings.php', 'limit-login-attempts' );
}
