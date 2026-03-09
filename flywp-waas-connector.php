<?php
/**
 * Plugin Name: FlyWP WaaS Connector
 * Description: Connects your WordPress site to the FlyWP API to spin up new sites from blueprints.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: flywp-waas
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'FLYWP_WAAS_VERSION', '1.0.0' );
define( 'FLYWP_WAAS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLYWP_WAAS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include core classes
require_once FLYWP_WAAS_PLUGIN_DIR . 'includes/class-flywp-api.php';
require_once FLYWP_WAAS_PLUGIN_DIR . 'includes/class-flywp-settings.php';

// Initialize the plugin
function flywp_waas_init() {
    new FlyWP_Settings();
}
add_action( 'plugins_loaded', 'flywp_waas_init' );
