<?php
/**
 * Figuren_Theater Security Two_Factor.
 *
 * @package figuren-theater/security/two_factor
 */

namespace Figuren_Theater\Security\Two_Factor;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use DOING_AUTOSAVE;
use DOING_CRON;
use WP_INSTALLING;

use function add_action;
use function add_filter;
use function get_userdata;
use function wp_strip_all_tags;

const BASENAME   = 'two-factor/two-factor.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['security'];
	if ( ! $config['two-factor'] )
		return; // early
	
	if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING )
		return;

	if ( defined( 'DOING_CRON' ) && DOING_CRON )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	require_once PLUGINPATH;

	// add_filter( 'two_factor_token_email_subject', '');
	add_filter( 'two_factor_token_email_message', __NAMESPACE__ . '\\email_message', 10, 3 );
	// add_filter( 'two_factor_rememberme', '__return_false' ); // false is the default
	add_filter( 'two_factor_providers', __NAMESPACE__ . '\\remove_providers' );
	add_filter( 'two_factor_enabled_providers_for_user', __NAMESPACE__ . '\\enable_email_provider' );
	add_filter( 'two_factor_primary_provider_for_user', __NAMESPACE__ . '\\email_as_default_primary_provider', 10, 2 );
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


function email_message( string $message, string $token, int $user_id ) : string {
	
	/* translators: %s: token */
	$message = wp_strip_all_tags( 
		sprintf( 
			__( 'Enter %s to log in.', 'two-factor' ),
			// by using a dummy over here
			// we can stay with the i18n
			// and still and have the security of
			// wp_strip_all_tags() on the 
			// possible unsecure-transaltions 
			// 
			// $token
			'DUMMYTOKEN'
		)
	);

	// add a paragraph
	$message = sprintf(
		'<p>%s</p>',
		$message
	);


	// now re-add the token
	// and wrap in comments to make copy & paste easy
	$message = str_replace(
		'DUMMYTOKEN',
		sprintf(
			'<pre>%s</pre>',
			$token
		),
		$message
	);

	//
	$user = get_userdata( $user_id );
	if ( false === $user )
		return $message;

	// Say 'hello'
	$message  = sprintf(
		'<h1 style="text-transform: uppercase;"><em style=" color:#d20394;">Hi</em> %s</h1>',
		$user->user_login
	) . "\r\n" . $message;

	return $message;
}

