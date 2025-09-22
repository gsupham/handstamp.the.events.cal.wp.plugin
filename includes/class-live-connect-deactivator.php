<?php
/**
 * Fired during plugin deactivation
 *
 * @package LiveConnect
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class LiveConnect_Deactivator {

    /**
     * Short Description.
     *
     * Long Description.
     */
    public static function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('live_connect_sync_events');
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Note: We don't delete options or database tables on deactivation
        // This preserves user data in case they reactivate the plugin
    }
}