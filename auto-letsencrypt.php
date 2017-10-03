<?php
/**
 * Plugin Name:  Auto Letsencrypt
 * Plugin URI:   https://gagan0123.com
 * Description:  Automatically add new domains to SSL Certificate
 * Version:      1.0.0
 * Author:       Gagan Deep Singh
 * Author URI:   https://gagan0123.com
 * License:      GPLv2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  auto-letsencrypt
 * Domain Path:  /languages
 * Network:      True
 *
 * @package Auto_Letsencrypt
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'GS_AL_PATH' ) ) {
	/**
	 * Path to the plugin folder.
	 *
	 * @since 0.0.1
	 */
	define( 'GS_AL_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

/**
 * The core plugin class.
 */
require_once GS_AL_PATH . 'includes/class-auto-letsencrypt.php';
