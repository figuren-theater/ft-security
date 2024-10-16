<?php
/**
 * Plugin Name:     figuren.theater | Security
 * Plugin URI:      https://github.com/figuren-theater/ft-security
 * Description:     Security related components for a WordPress Multisite network, like figuren.theater
 * Author:          figuren.theater
 * Author URI:      https://figuren.theater
 * Text Domain:     figurentheater
 * Domain Path:     /languages
 * Version:         1.5.0
 *
 * @package         figuren-theater/ft-security
 */

namespace Figuren_Theater\Security;

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
