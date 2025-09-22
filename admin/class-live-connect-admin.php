<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @package LiveConnect
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class LiveConnect_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            LIVE_CONNECT_PLUGIN_URL . 'admin/css/live-connect-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            LIVE_CONNECT_PLUGIN_URL . 'admin/js/live-connect-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name,
            'live_connect_ajax',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('live_connect_nonce'),
                'strings' => array(
                    'sync_success' => __('Events synchronized successfully!', 'live-connect'),
                    'sync_error' => __('Error synchronizing events. Please try again.', 'live-connect'),
                    'sync_in_progress' => __('Synchronizing events...', 'live-connect'),
                    'update_check_success' => __('Update check completed successfully!', 'live-connect'),
                    'update_check_error' => __('Error checking for updates. Please try again.', 'live-connect'),
                    'update_check_in_progress' => __('Checking for updates...', 'live-connect'),
                    'update_check_button' => __('Check for Updates Now', 'live-connect'),
                )
            )
        );
    }

    /**
     * Add admin menu for the plugin.
     */
    public function add_admin_menu() {
        add_options_page(
            __('Live Connect Settings', 'live-connect'),
            __('Live Connect', 'live-connect'),
            'manage_options',
            'live-connect-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display the settings page.
     */
    public function display_settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Save settings
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['live_connect_nonce'], 'live_connect_settings')) {
            update_option('live_connect_api_endpoint', sanitize_text_field($_POST['api_endpoint']));
            update_option('live_connect_api_token', sanitize_text_field($_POST['api_token']));
            update_option('live_connect_sync_interval', absint($_POST['sync_interval']));
            update_option('live_connect_modal_enhancement', sanitize_text_field($_POST['modal_enhancement']));
            update_option('live_connect_debug_mode', sanitize_text_field($_POST['debug_mode']));
            
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'live-connect') . '</p></div>';
        }

        // Get current settings
        $api_endpoint = get_option('live_connect_api_endpoint', '');
        $api_token = get_option('live_connect_api_token', '');
        $sync_interval = get_option('live_connect_sync_interval', 3600);
        $modal_enhancement = get_option('live_connect_modal_enhancement', 'enabled');
        $debug_mode = get_option('live_connect_debug_mode', 'disabled');

        include_once LIVE_CONNECT_PLUGIN_PATH . 'admin/partials/live-connect-admin-display.php';
    }

    /**
     * AJAX handler for manual event synchronization.
     */
    public function ajax_sync_events() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'live_connect_nonce')) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        try {
            $sync = new LiveConnect_Event_Sync();
            $result = $sync->sync_all_events();
            
            wp_send_json_success(array(
                'message' => sprintf(
                    __('Successfully synchronized %d events.', 'live-connect'),
                    $result['synced_count']
                ),
                'synced_count' => $result['synced_count'],
                'error_count' => $result['error_count']
            ));
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => __('Error during synchronization: ', 'live-connect') . $e->getMessage()
            ));
        }
    }

    /**
     * AJAX handler for manual update check.
     */
    public function ajax_check_updates() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'live_connect_nonce')) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        try {
            // Access the global update checker
            global $liveConnectUpdateChecker;
            
            if ($liveConnectUpdateChecker) {
                // Force check for updates
                $update = $liveConnectUpdateChecker->checkForUpdates();
                
                if ($update) {
                    wp_send_json_success(array(
                        'message' => sprintf(
                            __('Update available! Version %s is ready to install. <a href="%s">View updates</a>', 'live-connect'),
                            $update->version,
                            admin_url('plugins.php')
                        ),
                        'has_update' => true,
                        'version' => $update->version
                    ));
                } else {
                    wp_send_json_success(array(
                        'message' => __('Your plugin is up to date! No updates available.', 'live-connect'),
                        'has_update' => false
                    ));
                }
            } else {
                throw new Exception(__('Update checker not available.', 'live-connect'));
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => __('Error checking for updates: ', 'live-connect') . $e->getMessage()
            ));
        }
    }
}