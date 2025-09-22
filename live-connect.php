<?php
/**
 * Plugin Name: Live Connect - Events Calendar Bridge
 * Plugin URI: https://github.com/liveconnect/live-connect-plugin
 * Description: Professional bridge between your event management platform and The Events Calendar plugin. Automatically syncs rich artist profiles, social media content, and multimedia with events, replacing basic event modals with enhanced interactive displays featuring Spotify integration, YouTube content, and social media feeds.
 * Version: 1.0.0
 * Author: Live Connect
 * Author URI: https://github.com/liveconnect
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: live-connect
 * Domain Path: /languages
 * Requires at least: 5.6
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 * 
 * @package LiveConnect
 * @version 1.0.0
 * @author Live Connect
 * @copyright 2024 Live Connect
 * @license GPL-2.0+
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('LIVE_CONNECT_VERSION', '1.0.0');

/**
 * Plugin paths and URLs
 */
define('LIVE_CONNECT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('LIVE_CONNECT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Initialize Plugin Update Checker for automatic updates from GitHub
 */
require LIVE_CONNECT_PLUGIN_PATH . 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$liveConnectUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/liveconnect/live-connect-plugin/',
    __FILE__,
    'live-connect'
);

// Enable release assets (ZIP files attached to GitHub releases)
$liveConnectUpdateChecker->getVcsApi()->enableReleaseAssets();

/**
 * The code that runs during plugin activation.
 */
function activate_live_connect() {
    require_once LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect-activator.php';
    LiveConnect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_live_connect() {
    require_once LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect-deactivator.php';
    LiveConnect_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_live_connect');
register_deactivation_hook(__FILE__, 'deactivate_live_connect');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require LIVE_CONNECT_PLUGIN_PATH . 'includes/class-live-connect.php';

/**
 * Begins execution of the plugin.
 */
function run_live_connect() {
    $plugin = new LiveConnect();
    $plugin->run();
}

run_live_connect();