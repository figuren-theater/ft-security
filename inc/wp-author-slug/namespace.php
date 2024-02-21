<?php
/**
 * Figuren_Theater Security WP_Author_Slug.
 *
 * @package figuren-theater/ft-security
 */

namespace Figuren_Theater\Security\WP_Author_Slug;

use FT_VENDOR_DIR;

use function add_action;

const BASENAME   = 'wp-author-slug/wp-author-slug.php';
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

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
}
