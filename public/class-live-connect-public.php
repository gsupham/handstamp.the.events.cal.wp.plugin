<?php
/**
 * The public-facing functionality of the plugin
 *
 * @package LiveConnect
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 */
class LiveConnect_Public {

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
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        // Only enqueue on event-related pages
        if ((function_exists('is_singular') && is_singular('tribe_events')) || 
            (function_exists('is_post_type_archive') && is_post_type_archive('tribe_events')) || 
            (function_exists('tribe_is_events_home') && tribe_is_events_home())) {
            wp_enqueue_style(
                $this->plugin_name,
                LIVE_CONNECT_PLUGIN_URL . 'public/css/live-connect-public.css',
                array(),
                $this->version,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        // Only enqueue on event-related pages
        if ((function_exists('is_singular') && is_singular('tribe_events')) || 
            (function_exists('is_post_type_archive') && is_post_type_archive('tribe_events')) || 
            (function_exists('tribe_is_events_home') && tribe_is_events_home())) {
            wp_enqueue_script(
                $this->plugin_name,
                LIVE_CONNECT_PLUGIN_URL . 'public/js/live-connect-public.js',
                array('jquery'),
                $this->version,
                false
            );

            // Localize script for frontend functionality
            wp_localize_script(
                $this->plugin_name,
                'live_connect_public',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('live_connect_nonce'),
                    'is_events_page' => true,
                    'enhanced_modals' => get_option('live_connect_modal_enhancement', 'enabled') === 'enabled'
                )
            );
        }
    }
}