<?php
/**
 * FlyWP API Connection Class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FlyWP_API {
    private $api_url = 'https://app.flywp.com/api/v1';
    private $api_key;
    private $team_id;

    public function __construct() {
        $this->api_key = get_option( 'flywp_api_key' );
        $this->team_id = get_option( 'flywp_team_id' );
    }

    private function request( $endpoint, $method = 'GET', $body = array() ) {
        if ( empty( $this->api_key ) ) {
            return new WP_Error( 'missing_api_key', __( 'FlyWP API Key is missing.', 'flywp-waas' ) );
        }

        $url = rtrim( $this->api_url, '/' ) . '/' . ltrim( $endpoint, '/' );
        
        $args = array(
            'method'  => $method,
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ),
            'timeout' => 30,
        );

        if ( ! empty( $body ) && in_array( $method, array( 'POST', 'PUT', 'PATCH' ) ) ) {
            $args['body'] = wp_json_encode( $body );
        }

        $response = wp_remote_request( $url, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body_json   = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $status_code >= 400 ) {
            $message = isset( $body_json['message'] ) ? $body_json['message'] : __( 'An error occurred.', 'flywp-waas' );
            return new WP_Error( 'api_error', $message, $body_json );
        }

        return $body_json;
    }

    public function get_servers() {
        return $this->request( '/servers' );
    }

    public function get_sites() {
        return $this->request( '/sites' );
    }

    public function get_blueprints() {
        return $this->request( '/blueprints' );
    }

    public function create_site( $data ) {
        return $this->request( '/sites', 'POST', $data );
    }
}
