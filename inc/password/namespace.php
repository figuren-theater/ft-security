<?php
/**
 * Figuren_Theater Security.
 *
 * @package figuren-theater/security/password
 */

namespace Figuren_Theater\Security\Password;

/**
 * Set up hooks.
 *
 * @return void
 */
function bootstrap() {
	add_action('login_head', __NAMESPACE__ . '\\no_weak_password_header');
	add_action('load-profile.php', __NAMESPACE__ . '\\disable_weak_pws_checkbox_admin');
}



function disable_weak_pws_checkbox_admin() {
	add_action('admin_head', __NAMESPACE__ . '\\no_weak_password_header');
}


function no_weak_password_header() {    
	echo"<style>.pw-weak{display:none!important}</style>";
	echo'<script>var ft_pwc = document.getElementById("pw-checkbox");  if (ft_pwc !== null) ft_pwc.disabled = true;</script>';
}
