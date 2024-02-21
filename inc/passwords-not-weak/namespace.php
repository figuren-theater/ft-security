<?php
/**
 * Figuren_Theater Security Passwords_Not_Weak.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\Passwords_Not_Weak;

use function add_action;

/**
 * Set up hooks.
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'login_head', __NAMESPACE__ . '\\no_weak_password_header' );
	add_action( 'load-profile.php', __NAMESPACE__ . '\\disable_weak_pws_checkbox_admin' );
}

/**
 * Assign our action to remove the 'weak-passwords-allowed' checkbox on the login-page.
 *
 * @return void
 */
function disable_weak_pws_checkbox_admin(): void {
	add_action( 'admin_head', __NAMESPACE__ . '\\no_weak_password_header' );
}

/**
 * Prints CSS and JS to hide and disable the <input> element to allow the usage of weak passwords.
 *
 * @return void
 */
function no_weak_password_header(): void {
	echo '<style>.pw-weak{display:none!important}</style>';
	echo '<script>var ft_pwc = document.getElementById("pw-checkbox");  if (ft_pwc !== null) ft_pwc.disabled = true;</script>';
}
