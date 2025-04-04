<?php

/**
 * Plugin Name: CardanoPress Edge User
 * Plugin URI:  https://github.com/kermage/cardanopress-edge-user
 * Author:      Gene Alyson Fortunado Torcende
 * Author URI:  https://genealysontorcende.wordpress.com/
 * Description: Allow to easily update to latest GitHub version releases.
 * Version:     0.9.0
 * License:     GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: cardanopress-edge-user
 *
 * @package ThemePlate
 * @since 0.1.0
 */

use kermage\CardanoPress\EdgeUser\Main;

// Accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==================================================
Global constants
================================================== */

if ( ! defined( 'CP_EDGE_USER_FILE' ) ) {
	define( 'CP_EDGE_USER_FILE', __FILE__ );
}

// Autoload classes with Composer
require_once plugin_dir_path( CP_EDGE_USER_FILE ) . 'dependencies/vendor/autoload.php';

// Instantiate the updater
EUM_Handler::run( CP_EDGE_USER_FILE, 'https://raw.githubusercontent.com/kermage/cardanopress-edge-user/main/update-data.json' );

// Load the main plugin class
add_action( 'plugins_loaded', array( new Main(), 'load' ) );
