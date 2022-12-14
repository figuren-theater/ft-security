<?php
/**
 * Plugin Name:     figuren.theater | Security
 * Plugin URI:      https://github.com/figuren-theater/ft-security
 * Description:     Security module for all sites of the figuren.theater multisite network.
 * Author:          figuren.theater
 * Author URI:      https://figuren.theater
 * Text Domain:     figurentheater
 * Domain Path:     /languages
 * Version:         1.0.19
 *
 * @package         figuren-theater/security
 */

namespace Figuren_Theater\Security;

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
