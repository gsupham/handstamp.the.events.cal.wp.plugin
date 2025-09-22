<?php
/**
 * The file that defines the core plugin class
 *
 * @package LiveConnect
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */
class LiveConnect {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        if (defined('LIVE_CONNECT_VERSION')) {
            $this->version = LIVE_CONNECT_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'live-connect';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        // The class responsible for orchestrating the actions and filters of the core plugin.
        require_once LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect-loader.php';

        // The class responsible for defining internationalization functionality.
        require_once LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect-i18n.php';

        // The class responsible for defining all actions that occur in the admin area.
        require_once LIVE_CONNECT_PLUGIN_PATH . 'admin/class-live-connect-admin.php';

        // The class responsible for defining all actions that occur in the public-facing side.
        require_once LIVE_CONNECT_PLUGIN_PATH . 'public/class-live-connect-public.php';

        // API client for communicating with the event platform
        require_once LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect-api-client.php';

        // Event sync functionality for The Events Calendar integration
        require_once LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect-event-sync.php';

        // Modal enhancer for rich content display
        require_once LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect-modal-enhancer.php';

        $this->loader = new LiveConnect_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() {
        $plugin_i18n = new LiveConnect_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        $plugin_admin = new LiveConnect_Admin($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('wp_ajax_live_connect_sync_events', $plugin_admin, 'ajax_sync_events');
        $this->loader->add_action('wp_ajax_live_connect_check_updates', $plugin_admin, 'ajax_check_updates');
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        $plugin_public = new LiveConnect_Public($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Modal enhancer hooks
        $modal_enhancer = new LiveConnect_Modal_Enhancer();
        $this->loader->add_action('wp_footer', $modal_enhancer, 'inject_modal_enhancer');
        $this->loader->add_filter('tribe_events_single_event_details_section_content', $modal_enhancer, 'enhance_event_modal', 10, 2);
        
        // AJAX handlers for enhanced content
        $this->loader->add_action('wp_ajax_live_connect_get_enhanced_content', $modal_enhancer, 'ajax_get_enhanced_content');
        $this->loader->add_action('wp_ajax_nopriv_live_connect_get_enhanced_content', $modal_enhancer, 'ajax_get_enhanced_content');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within WordPress.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}