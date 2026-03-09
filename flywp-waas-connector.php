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

// Check if required files exist to prevent fatal errors (e.g., if directories weren't copied)
$flywp_api_file      = FLYWP_WAAS_PLUGIN_DIR . 'includes/class-flywp-api.php';
$flywp_settings_file = FLYWP_WAAS_PLUGIN_DIR . 'includes/class-flywp-settings.php';

if ( file_exists( $flywp_api_file ) && file_exists( $flywp_settings_file ) ) {
    require_once $flywp_api_file;
    require_once $flywp_settings_file;

    if ( ! function_exists( 'flywp_waas_init' ) ) {
        function flywp_waas_init() {
            if ( class_exists( 'FlyWP_Settings' ) ) {
                new FlyWP_Settings();
            }
        }
        add_action( 'plugins_loaded', 'flywp_waas_init' );
    }
} else {
    add_action( 'admin_notices', 'flywp_waas_missing_files_notice' );
    if ( ! function_exists( 'flywp_waas_missing_files_notice' ) ) {
        function flywp_waas_missing_files_notice() {
            echo '<div class="error"><p>' . esc_html__( 'FlyWP WaaS Connector: Required files are missing. Please ensure the "includes" and "templates" directories were uploaded correctly.', 'flywp-waas' ) . '</p></div>';
        }
    }
}
