<?php
/**
 * Fired during plugin activation
 *
 * @package LiveConnect
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class LiveConnect_Activator {

    /**
     * Short Description.
     *
     * Long Description.
     */
    public static function activate() {
        // Check if The Events Calendar is active
        if (!class_exists('Tribe__Events__Main')) {
            deactivate_plugins('live-connect/live-connect.php');
            wp_die(
                __('Live Connect requires The Events Calendar plugin to be installed and activated.', 'live-connect'),
                __('Plugin dependency error', 'live-connect'),
                array('back_link' => true)
            );
        }

        // Create database tables if needed
        self::create_tables();

        // Set default options
        self::set_default_options();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create custom database tables for additional event data storage.
     */
    private static function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'live_connect_event_meta';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_id INTEGER NOT NULL,
            platform_event_id varchar(255) NOT NULL,
            artist_data TEXT,
            venue_data TEXT,
            social_media_data TEXT,
            multimedia_data TEXT,
            sync_status varchar(50) DEFAULT 'pending',
            last_sync TEXT DEFAULT '',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (platform_event_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Set default plugin options.
     */
    private static function set_default_options() {
        add_option('live_connect_api_endpoint', '');
        add_option('live_connect_api_token', '');
        add_option('live_connect_sync_interval', 3600); // 1 hour
        add_option('live_connect_modal_enhancement', 'enabled');
        add_option('live_connect_debug_mode', 'disabled');
    }
}