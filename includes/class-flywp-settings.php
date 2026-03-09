<?php
/**
 * FlyWP Settings Page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'FlyWP_Settings' ) ) {
    class FlyWP_Settings {
        public function __construct() {
            add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'admin_post_flywp_create_site', array( $this, 'handle_create_site' ) );
        }

        public function add_settings_page() {
            add_menu_page(
                __( 'FlyWP WaaS', 'flywp-waas' ),
                __( 'FlyWP WaaS', 'flywp-waas' ),
                'manage_options',
                'flywp-waas',
                array( $this, 'render_dashboard' ),
                'dashicons-cloud',
                30
            );

            add_submenu_page(
                'flywp-waas',
                __( 'Settings', 'flywp-waas' ),
                __( 'Settings', 'flywp-waas' ),
                'manage_options',
                'flywp-waas-settings',
                array( $this, 'render_settings_page' )
            );
        }

        public function register_settings() {
            register_setting( 'flywp_waas_settings_group', 'flywp_api_key', 'sanitize_text_field' );
            register_setting( 'flywp_waas_settings_group', 'flywp_team_id', 'sanitize_text_field' );
        }

        public function render_settings_page() {
            ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'FlyWP WaaS Settings', 'flywp-waas' ); ?></h1>
                <form method="post" action="options.php">
                    <?php settings_fields( 'flywp_waas_settings_group' ); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'API Key', 'flywp-waas' ); ?></th>
                            <td>
                                <input type="password" name="flywp_api_key" value="<?php echo esc_attr( get_option( 'flywp_api_key' ) ); ?>" class="regular-text" />
                                <p class="description"><?php esc_html_e( 'Enter your FlyWP API Key.', 'flywp-waas' ); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Team ID', 'flywp-waas' ); ?></th>
                            <td>
                                <input type="text" name="flywp_team_id" value="<?php echo esc_attr( get_option( 'flywp_team_id' ) ); ?>" class="regular-text" />
                                <p class="description"><?php esc_html_e( 'Enter your FlyWP Team ID (optional depending on endpoint requirements).', 'flywp-waas' ); ?></p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }

        public function render_dashboard() {
            $template_file = FLYWP_WAAS_PLUGIN_DIR . 'templates/dashboard.php';
            if ( file_exists( $template_file ) ) {
                include $template_file;
            } else {
                echo '<div class="wrap"><div class="error"><p>' . esc_html__( 'Dashboard template file is missing. Please ensure templates/dashboard.php exists.', 'flywp-waas' ) . '</p></div></div>';
            }
        }

        public function handle_create_site() {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'Unauthorized', 'flywp-waas' ) );
            }

            check_admin_referer( 'flywp_create_site_nonce', 'flywp_nonce' );

            $site_name = isset( $_POST['site_name'] ) ? sanitize_text_field( $_POST['site_name'] ) : '';
            $blueprint_id = isset( $_POST['blueprint_id'] ) ? sanitize_text_field( $_POST['blueprint_id'] ) : '';

            if ( empty( $site_name ) || empty( $blueprint_id ) ) {
                wp_redirect( add_query_arg( 'error', 'missing_fields', admin_url( 'admin.php?page=flywp-waas' ) ) );
                exit;
            }

            if ( class_exists( 'FlyWP_API' ) ) {
                $api = new FlyWP_API();
                $response = $api->create_site( array(
                    'name' => $site_name,
                    'blueprint_id' => $blueprint_id,
                ) );

                if ( is_wp_error( $response ) ) {
                    wp_redirect( add_query_arg( 'error', 'api_failed', admin_url( 'admin.php?page=flywp-waas' ) ) );
                    exit;
                }

                wp_redirect( add_query_arg( 'success', 'site_created', admin_url( 'admin.php?page=flywp-waas' ) ) );
                exit;
            }
        }
    }
}
