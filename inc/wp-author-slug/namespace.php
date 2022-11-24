<?php
/**
 * Figuren_Theater Security WP_Author_Slug.
 *
 * @package figuren-theater/security/wp_author_slug
 */

namespace Figuren_Theater\Security\WP_Author_Slug;

use FT_VENDOR_DIR;

use function add_action;

const BASENAME   = 'wp-author-slug/wp-author-slug.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {

	require_once PLUGINPATH;
}
