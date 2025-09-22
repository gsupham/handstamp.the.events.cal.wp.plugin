<?php
/**
 * Event synchronization functionality for The Events Calendar integration
 *
 * @package LiveConnect
 */

/**
 * Event sync class for synchronizing platform events with The Events Calendar.
 */
class LiveConnect_Event_Sync {

    /**
     * API client instance
     */
    private $api_client;

    /**
     * Initialize the event sync.
     */
    public function __construct() {
        $this->api_client = new LiveConnect_API_Client();
    }

    /**
     * Synchronize all events from the platform.
     */
    public function sync_all_events() {
        if (!class_exists('Tribe__Events__Main')) {
            return new WP_Error('missing_tec', __('The Events Calendar plugin is not active.', 'live-connect'));
        }

        $events_data = $this->api_client->get_events();
        
        if (is_wp_error($events_data)) {
            return $events_data;
        }

        $synced_count = 0;
        $error_count = 0;

        if (isset($events_data['events']) && is_array($events_data['events'])) {
            foreach ($events_data['events'] as $event_data) {
                $result = $this->sync_single_event($event_data);
                if (is_wp_error($result)) {
                    $error_count++;
                    if (get_option('live_connect_debug_mode') === 'enabled') {
                        error_log('Live Connect sync error: ' . $result->get_error_message());
                    }
                } else {
                    $synced_count++;
                }
            }
        }

        return array(
            'synced_count' => $synced_count,
            'error_count' => $error_count,
        );
    }

    /**
     * Synchronize a single event.
     */
    public function sync_single_event($event_data) {
        if (!isset($event_data['id']) || !isset($event_data['title'])) {
            return new WP_Error('invalid_event_data', __('Event data is missing required fields.', 'live-connect'));
        }

        // Check if event already exists
        $existing_event = $this->get_event_by_platform_id($event_data['id']);
        
        if ($existing_event) {
            return $this->update_event($existing_event, $event_data);
        } else {
            return $this->create_event($event_data);
        }
    }

    /**
     * Create a new event in The Events Calendar.
     */
    private function create_event($event_data) {
        $event_args = array(
            'post_title' => sanitize_text_field($event_data['title']),
            'post_content' => wp_kses_post($event_data['description'] ?? ''),
            'post_status' => 'publish',
            'post_type' => 'tribe_events',
            'meta_input' => array(
                '_EventStartDate' => $this->format_date($event_data['start_date'] ?? ''),
                '_EventEndDate' => $this->format_date($event_data['end_date'] ?? ''),
                '_EventVenueID' => $this->get_or_create_venue($event_data['venue'] ?? array()),
                '_live_connect_platform_id' => sanitize_text_field($event_data['id']),
            )
        );

        $event_id = wp_insert_post($event_args);
        
        if (is_wp_error($event_id)) {
            return $event_id;
        }

        // Store additional event metadata
        $this->store_event_metadata($event_id, $event_data);

        return $event_id;
    }

    /**
     * Update an existing event.
     */
    private function update_event($event_id, $event_data) {
        $event_args = array(
            'ID' => $event_id,
            'post_title' => sanitize_text_field($event_data['title']),
            'post_content' => wp_kses_post($event_data['description'] ?? ''),
            'meta_input' => array(
                '_EventStartDate' => $this->format_date($event_data['start_date'] ?? ''),
                '_EventEndDate' => $this->format_date($event_data['end_date'] ?? ''),
                '_EventVenueID' => $this->get_or_create_venue($event_data['venue'] ?? array()),
            )
        );

        $result = wp_update_post($event_args);
        
        if (is_wp_error($result)) {
            return $result;
        }

        // Update additional event metadata
        $this->store_event_metadata($event_id, $event_data);

        return $event_id;
    }

    /**
     * Get event by platform ID.
     */
    private function get_event_by_platform_id($platform_id) {
        $events = get_posts(array(
            'post_type' => 'tribe_events',
            'meta_key' => '_live_connect_platform_id',
            'meta_value' => $platform_id,
            'posts_per_page' => 1,
        ));

        return !empty($events) ? $events[0]->ID : false;
    }

    /**
     * Store additional event metadata.
     */
    private function store_event_metadata($event_id, $event_data) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'live_connect_event_meta';
        
        $metadata = array(
            'event_id' => $event_id,
            'platform_event_id' => sanitize_text_field($event_data['id']),
            'artist_data' => json_encode($event_data['artists'] ?? array()),
            'venue_data' => json_encode($event_data['venue'] ?? array()),
            'social_media_data' => json_encode($event_data['social_media'] ?? array()),
            'multimedia_data' => json_encode($event_data['multimedia'] ?? array()),
            'sync_status' => 'completed',
            'last_sync' => current_time('mysql'),
        );

        $wpdb->replace($table_name, $metadata);
    }

    /**
     * Get or create venue for the event.
     */
    private function get_or_create_venue($venue_data) {
        if (empty($venue_data['name'])) {
            return 0;
        }

        // Check if venue already exists
        $venues = get_posts(array(
            'post_type' => 'tribe_venue',
            'title' => $venue_data['name'],
            'posts_per_page' => 1,
        ));

        if (!empty($venues)) {
            return $venues[0]->ID;
        }

        // Create new venue
        $venue_args = array(
            'post_title' => sanitize_text_field($venue_data['name']),
            'post_content' => wp_kses_post($venue_data['description'] ?? ''),
            'post_status' => 'publish',
            'post_type' => 'tribe_venue',
            'meta_input' => array(
                '_VenueAddress' => sanitize_text_field($venue_data['address'] ?? ''),
                '_VenueCity' => sanitize_text_field($venue_data['city'] ?? ''),
                '_VenueState' => sanitize_text_field($venue_data['state'] ?? ''),
                '_VenueZip' => sanitize_text_field($venue_data['zip'] ?? ''),
                '_VenueCountry' => sanitize_text_field($venue_data['country'] ?? ''),
            )
        );

        return wp_insert_post($venue_args);
    }

    /**
     * Format date for The Events Calendar.
     */
    private function format_date($date_string) {
        if (empty($date_string)) {
            return '';
        }

        $date = date_create($date_string);
        return $date ? $date->format('Y-m-d H:i:s') : '';
    }
}