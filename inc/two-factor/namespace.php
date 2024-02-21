<?php
/**
 * Figuren_Theater Security Two_Factor.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\Two_Factor;

use DOING_AUTOSAVE;
use DOING_CRON;
use Figuren_Theater;
use FT_VENDOR_DIR;
use WP_INSTALLING;
use function add_action;
use function add_filter;
use function get_userdata;
use function wp_strip_all_tags;

const BASENAME   = 'two-factor/two-factor.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin(): void {

	$config = Figuren_Theater\get_config()['modules']['security'];
	if ( ! $config['two-factor'] ) {
		return;
	}

	if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
		return;
	}

	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	// Additional avail. filter:
	//
	// `two_factor_user_authenticated` action which receives the logged in `WP_User` object as the first argument for determining the logged in user right after the authentication workflow.
	// `two_factor_token_ttl` filter overrides the time interval in seconds that an email token is considered after generation. Accepts the time in seconds as the first argument and the ID of the `WP_User` object being authenticated.
	// `two_factor_token_email_subject` filter overrides the email subject of the message, sent to the user.
	// `two_factor_token_email_message` filter overrides the token emails plaintext message, sent to the user.
	// `two_factor_rememberme` filter overrides ...
	// add_filter( 'two_factor_rememberme', '__return_false' ); // false is the default.

	add_filter( 'two_factor_token_email_message', __NAMESPACE__ . '\\email_message', 10, 3 );

	// `two_factor_providers` filter overrides the available two-factor providers
	// such as email and time-based one-time passwords. Array values are PHP classnames of the two-factor providers.
	add_filter( 'two_factor_providers', __NAMESPACE__ . '\\remove_providers' );

	// `two_factor_enabled_providers_for_user` filter overrides the list of two-factor providers enabled for a user.
	// First argument is an array of enabled provider classnames as values, the second argument is the user ID.
	add_filter( 'two_factor_enabled_providers_for_user', __NAMESPACE__ . '\\enable_email_provider' );
	add_filter( 'two_factor_primary_provider_for_user', __NAMESPACE__ . '\\email_as_default_primary_provider', 10 );
}

/**
 * Remove the Dummy provider from the 2FA options,
 * even when WP_DEBUG is enabled, which is the default to show it.
 *
 * @source https://github.com/humanmade/altis-security/blob/5312fb2078ce0ef6abdf27f53e9cb09c4899a00e/inc/namespace.php#L103
 *
 * @param string[] $providers 2FA providers list.
 *
 * @return string[]
 */
function remove_providers( array $providers ): array {
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
 * Filter the enabled two-factor authentication providers for this user or
 * `two_factor_enabled_providers_for_user` filter overrides the list of two-factor providers enabled for a user.
 *
 * And enables 'Email', if its not present yet.
 * First argument is an array of enabled provider classnames as values, the second argument is the user ID.
 *
 * @param string[] $providers The enabled providers.
 *
 * @return string[]
 */
function enable_email_provider( array $providers ): array {
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
 * @param  string $provider The provider currently being used.
 * 
 * @return string
 */
function email_as_default_primary_provider( string $provider ): string {
	if ( empty( $provider ) ) {
		$provider = 'Two_Factor_Email';
	}
	return $provider;
}

/**
 * Filter the token email message.
 *
 * @see https://github.com/WordPress/two-factor/blob/master/providers/class-two-factor-email.php#L241C6-L241C6
 *
 * @param string $message The email message.
 * @param string $token   The token.
 * @param int    $user_id The ID of the user.
 *
 * @return string
 */
function email_message( string $message, string $token, int $user_id ): string {

	/* translators: %s: token */
	$message = wp_strip_all_tags(
		sprintf(
			/* translators: %s: 2FA Login Token */
			__( 'Dein aktueller Login-Bestätigungs-Code lautet: %s', 'two-factor' ),
			// by using a dummy over here
			// we can stay with the i18n
			// and still and have the security of
			// wp_strip_all_tags() on the
			// possible unsecure-transaltions.
			//
			// So we don't use $token over here, but DUMMYTOKEN.
			'DUMMYTOKEN'
		)
	);

	// Wrap the message in a paragraph.
	$message = sprintf(
		'<p>%s</p>',
		$message
	);

	// Re-add the token
	// and wrap in comments to make copy & paste easy.
	$message = str_replace(
		'DUMMYTOKEN',
		sprintf(
			'<pre style="display:block;font-size:32px;font-weight:bold;line-height:36px;text-align:center">%s</pre>',
			$token
		),
		$message
	);

		$user = get_userdata( $user_id );
	if ( false === $user ) {
		return $message;
	}

	// Say 'Hi %USERNAME%'.
	$message = sprintf(
		'<h1 style="text-transform: uppercase;"><em style=" color:#d20394;">Hi</em> %s</h1>',
		$user->user_login
	) . "\r\n" . $message;

	return $message;
}
