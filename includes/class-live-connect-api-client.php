<?php
/**
 * API Client for communicating with the event management platform
 *
 * @package LiveConnect
 */

/**
 * API Client class for secure communication with the event platform.
 */
class LiveConnect_API_Client {

    /**
     * API endpoint URL
     */
    private $api_endpoint;

    /**
     * API authentication token
     */
    private $api_token;

    /**
     * Initialize the API client.
     */
    public function __construct() {
        $this->api_endpoint = get_option('live_connect_api_endpoint', '');
        $this->api_token = get_option('live_connect_api_token', '');
    }

    /**
     * Test API connection.
     */
    public function test_connection() {
        if (empty($this->api_endpoint) || empty($this->api_token)) {
            return new WP_Error('missing_credentials', __('API endpoint or token not configured.', 'live-connect'));
        }

        $response = wp_remote_get($this->api_endpoint . '/health', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_token,
                'Content-Type' => 'application/json',
            ),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return new WP_Error('api_error', sprintf(__('API returned status code %d', 'live-connect'), $status_code));
        }

        return true;
    }

    /**
     * Get events from the platform API.
     */
    public function get_events($limit = 50, $offset = 0) {
        if (empty($this->api_endpoint) || empty($this->api_token)) {
            return new WP_Error('missing_credentials', __('API credentials not configured.', 'live-connect'));
        }

        $url = add_query_arg(array(
            'limit' => $limit,
            'offset' => $offset,
        ), $this->api_endpoint . '/events');

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_token,
                'Content-Type' => 'application/json',
            ),
            'timeout' => 60,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return new WP_Error('api_error', sprintf(__('Failed to fetch events. Status: %d', 'live-connect'), $status_code));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('Invalid JSON response from API.', 'live-connect'));
        }

        return $data;
    }

    /**
     * Get specific event details including artist and venue data.
     */
    public function get_event($event_id) {
        if (empty($this->api_endpoint) || empty($this->api_token)) {
            return new WP_Error('missing_credentials', __('API credentials not configured.', 'live-connect'));
        }

        $response = wp_remote_get($this->api_endpoint . '/events/' . $event_id, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_token,
                'Content-Type' => 'application/json',
            ),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return new WP_Error('api_error', sprintf(__('Failed to fetch event %s. Status: %d', 'live-connect'), $event_id, $status_code));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('Invalid JSON response from API.', 'live-connect'));
        }

        return $data;
    }
}