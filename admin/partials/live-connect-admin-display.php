<?php
/**
 * Provide a admin area view for the plugin
 *
 * @package LiveConnect
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('live_connect_settings', 'live_connect_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="api_endpoint"><?php _e('API Endpoint', 'live-connect'); ?></label>
                </th>
                <td>
                    <input type="url" 
                           id="api_endpoint" 
                           name="api_endpoint" 
                           value="<?php echo esc_attr($api_endpoint); ?>" 
                           class="regular-text" 
                           placeholder="https://your-platform.com/api/v1"/>
                    <p class="description">
                        <?php _e('The base URL of your event management platform API.', 'live-connect'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="api_token"><?php _e('API Token', 'live-connect'); ?></label>
                </th>
                <td>
                    <input type="password" 
                           id="api_token" 
                           name="api_token" 
                           value="<?php echo esc_attr($api_token); ?>" 
                           class="regular-text"/>
                    <p class="description">
                        <?php _e('Your API authentication token. This will be stored securely.', 'live-connect'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="sync_interval"><?php _e('Sync Interval (seconds)', 'live-connect'); ?></label>
                </th>
                <td>
                    <input type="number" 
                           id="sync_interval" 
                           name="sync_interval" 
                           value="<?php echo esc_attr($sync_interval); ?>" 
                           min="300" 
                           max="86400" 
                           class="small-text"/>
                    <p class="description">
                        <?php _e('How often to automatically sync events (minimum 5 minutes, maximum 24 hours).', 'live-connect'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="modal_enhancement"><?php _e('Enhanced Modals', 'live-connect'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <label for="modal_enhancement_enabled">
                            <input type="radio" 
                                   id="modal_enhancement_enabled" 
                                   name="modal_enhancement" 
                                   value="enabled" 
                                   <?php checked($modal_enhancement, 'enabled'); ?>/>
                            <?php _e('Enabled', 'live-connect'); ?>
                        </label><br>
                        <label for="modal_enhancement_disabled">
                            <input type="radio" 
                                   id="modal_enhancement_disabled" 
                                   name="modal_enhancement" 
                                   value="disabled" 
                                   <?php checked($modal_enhancement, 'disabled'); ?>/>
                            <?php _e('Disabled', 'live-connect'); ?>
                        </label>
                    </fieldset>
                    <p class="description">
                        <?php _e('Replace basic event modals with enhanced versions containing artist profiles, social media, and multimedia.', 'live-connect'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="debug_mode"><?php _e('Debug Mode', 'live-connect'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <label for="debug_mode_enabled">
                            <input type="radio" 
                                   id="debug_mode_enabled" 
                                   name="debug_mode" 
                                   value="enabled" 
                                   <?php checked($debug_mode, 'enabled'); ?>/>
                            <?php _e('Enabled', 'live-connect'); ?>
                        </label><br>
                        <label for="debug_mode_disabled">
                            <input type="radio" 
                                   id="debug_mode_disabled" 
                                   name="debug_mode" 
                                   value="disabled" 
                                   <?php checked($debug_mode, 'disabled'); ?>/>
                            <?php _e('Disabled', 'live-connect'); ?>
                        </label>
                    </fieldset>
                    <p class="description">
                        <?php _e('Enable detailed logging for troubleshooting. Only enable when needed.', 'live-connect'); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <hr>
    
    <h2><?php _e('Manual Sync', 'live-connect'); ?></h2>
    <p><?php _e('Click the button below to manually synchronize events from your platform.', 'live-connect'); ?></p>
    <button type="button" id="manual-sync-btn" class="button button-secondary">
        <?php _e('Sync Events Now', 'live-connect'); ?>
    </button>
    <div id="sync-status" style="margin-top: 10px;"></div>
    
    <hr>
    
    <h2><?php _e('Plugin Updates', 'live-connect'); ?></h2>
    <p><?php _e('Check for plugin updates and manage automatic update settings.', 'live-connect'); ?></p>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('Current Version', 'live-connect'); ?></th>
            <td>
                <strong><?php echo esc_html(LIVE_CONNECT_VERSION); ?></strong>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Update Check', 'live-connect'); ?></th>
            <td>
                <button type="button" id="check-updates-btn" class="button button-secondary">
                    <?php _e('Check for Updates Now', 'live-connect'); ?>
                </button>
                <div id="update-status" style="margin-top: 10px;"></div>
                <p class="description">
                    <?php _e('Manually check for available plugin updates from GitHub releases.', 'live-connect'); ?>
                </p>
            </td>
        </tr>
    </table>
    
    <hr>
    
    <h2><?php _e('System Status', 'live-connect'); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('The Events Calendar', 'live-connect'); ?></th>
            <td>
                <?php if (class_exists('Tribe__Events__Main')): ?>
                    <span style="color: green;">✓ <?php _e('Active', 'live-connect'); ?></span>
                <?php else: ?>
                    <span style="color: red;">✗ <?php _e('Not Active', 'live-connect'); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('API Connection', 'live-connect'); ?></th>
            <td>
                <?php if (!empty($api_endpoint) && !empty($api_token)): ?>
                    <span style="color: orange;">⚠ <?php _e('Not Tested', 'live-connect'); ?></span>
                <?php else: ?>
                    <span style="color: red;">✗ <?php _e('Not Configured', 'live-connect'); ?></span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>