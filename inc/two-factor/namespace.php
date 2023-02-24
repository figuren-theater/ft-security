<?php
/**
 * Figuren_Theater Security Two_Factor.
 *
 * @package figuren-theater/security/two_factor
 */

namespace Figuren_Theater\Security\Two_Factor;

use FT_VENDOR_DIR;

use DOING_AUTOSAVE;
use DOING_CRON;
use WP_INSTALLING;

use function add_action;
use function add_filter;

const BASENAME   = 'two-factor/two-factor.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {

	if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING )
		return;

	if ( defined( 'DOING_CRON' ) && DOING_CRON )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	require_once PLUGINPATH;

	// add_filter( 'two_factor_token_email_subject', '');
	// add_filter( 'two_factor_token_email_message', '');
	// add_filter( 'two_factor_rememberme', '__return_false' ); // false is the default
	add_filter( 'two_factor_providers', __NAMESPACE__ . '\\remove_providers' );
	add_filter( 'two_factor_enabled_providers_for_user', __NAMESPACE__ . '\\enable_email_provider' );
	add_filter( 'two_factor_primary_provider_for_user', __NAMESPACE__ . '\\email_as_default_primary_provider', 10, 2 );
	
	// ONLY avail. in humanmade/two-factor fork
	// add_filter( 'two_factor_universally_forced', __NAMESPACE__ . '\\override_two_factor_universally_forced' );
	// ONLY avail. in humanmade/two-factor fork
	// add_filter( 'two_factor_forced_user_roles', __NAMESPACE__ . '\\override_two_factor_forced_user_roles' );
}


/**
 * Remove the Dummy provider from the 2FA options, 
 * even when WP_DEBUG is enabled, which is the default to show it.
 *
 * @source https://github.com/humanmade/altis-security/blob/5312fb2078ce0ef6abdf27f53e9cb09c4899a00e/inc/namespace.php#L103
 *
 * @param array $providers 2FA providers list.
 * @return array
 */
function remove_providers( array $providers ) : array {
	if ( isset( $providers['Two_Factor_Dummy'] ) ) {
		unset( $providers['Two_Factor_Dummy'] );
	}

	/**
	 * TEMP
	 * 
	 * Disable FIDO keys until plugin version 0.8.0 is released
	 * and the issue will hopefully be fixed.
	 *
	 * @see  https://github.com/figuren-theater/ft-security/issues/9
	 */
	if ( isset( $providers['Two_Factor_FIDO_U2F'] ) ) {
		unset( $providers['Two_Factor_FIDO_U2F'] );
	}

	return $providers;
}


/**
 * Filter the enabled two-factor authentication providers for this user.
 *
 * And enables 'Email', if its not present yet.
 *
 * @param array  $providers The enabled providers.
 * @param int    $user_id   The user ID.
 */
function enable_email_provider( array $providers ) : array {
	if ( ! isset( array_flip( $providers )['Two_Factor_Email'] ) ) {
		$providers[] = 'Two_Factor_Email';
	}
	return $providers;
}


/**
 * Filter the two-factor authentication provider used for this user.
 *
 * And sets 'Email' as default, if nothing (=no 2FA) is set.
 *
 * @param string $provider The provider currently being used.
 * @param int    $user_id  The user ID.
 */
function email_as_default_primary_provider( string $provider, int $user_id ) : string {
	if ( empty( $provider ) ) {
		$provider = 'Two_Factor_Email';
	}
	return $provider;
}


/**
 * ONLY avail. in humanmade/two-factor fork
 * 
 * Override the two factor forced setting with values from the Altis configuration.
 *
 * @source https://github.com/humanmade/altis-security/blob/5312fb2078ce0ef6abdf27f53e9cb09c4899a00e/inc/namespace.php#L116
 * 
 * @param bool $is_forced If true forces 2FA to be required.
 * @return bool
function override_two_factor_universally_forced( bool $is_forced ) : bool {
	$config = Altis\get_config()['modules']['security']['2-factor-authentication'];
	if ( is_array( $config ) && isset( $config['required'] ) && is_bool( $config['required'] ) ) {
		return $config['required'];
	}

	return $is_forced;
}
 */

/**
 * ONLY avail. in humanmade/two-factor fork
 * 
 * Override the two factor forced setting for enabled roles with values
 * from the Altis configuration.
 *
 * @source https://github.com/humanmade/altis-security/blob/5312fb2078ce0ef6abdf27f53e9cb09c4899a00e/inc/namespace.php#L132
 * 
 * @param array|null $roles Roles required to use 2FA.
 * @return array|null
function override_two_factor_forced_user_roles( $roles ) {
	$config = Altis\get_config()['modules']['security']['2-factor-authentication'];
	if ( ! empty( $config['required'] ) && is_array( $config['required'] ) ) {
		return $config['required'];
	}

	return $roles;
}
 */
