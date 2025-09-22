<?php
/**
 * Modal enhancer for rich content display
 *
 * @package LiveConnect
 */

/**
 * Modal enhancer class for replacing basic TEC modals with enhanced versions.
 */
class LiveConnect_Modal_Enhancer {

    /**
     * Initialize the modal enhancer.
     */
    public function __construct() {
        // Constructor - no initialization needed for now
    }

    /**
     * Inject modal enhancer JavaScript into the footer.
     */
    public function inject_modal_enhancer() {
        if (!$this->should_enhance_modals()) {
            return;
        }

        // Only on event-related pages
        if (!(function_exists('is_singular') && is_singular('tribe_events')) && 
            !(function_exists('is_post_type_archive') && is_post_type_archive('tribe_events')) && 
            !(function_exists('tribe_is_events_home') && tribe_is_events_home())) {
            return;
        }
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Enhance TEC event modals with rich content
            $('.tribe-events-calendar-month__calendar-event-tooltip').each(function() {
                var $tooltip = $(this);
                var eventId = $tooltip.data('event-id');
                
                if (eventId) {
                    liveConnectEnhanceModal($tooltip, eventId);
                }
            });
            
            // Handle dynamically created modals
            $(document).on('DOMNodeInserted', '.tribe-events-calendar-month__calendar-event-tooltip', function() {
                var $tooltip = $(this);
                var eventId = $tooltip.data('event-id');
                
                if (eventId) {
                    liveConnectEnhanceModal($tooltip, eventId);
                }
            });
        });
        
        function liveConnectEnhanceModal($tooltip, eventId) {
            // Add loading indicator
            $tooltip.append('<div class="live-connect-loading">Loading enhanced content...</div>');
            
            // Fetch enhanced content via AJAX
            $.ajax({
                url: live_connect_public.ajax_url,
                type: 'POST',
                data: {
                    action: 'live_connect_get_enhanced_content',
                    event_id: eventId,
                    nonce: live_connect_public.nonce
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $tooltip.find('.live-connect-loading').remove();
                        $tooltip.append(response.data.html);
                    } else {
                        $tooltip.find('.live-connect-loading').text('Enhanced content unavailable');
                    }
                },
                error: function() {
                    $tooltip.find('.live-connect-loading').text('Error loading enhanced content');
                }
            });
        }
        </script>
        <?php
    }

    /**
     * Enhance event modal content.
     */
    public function enhance_event_modal($content, $event_id) {
        if (!$this->should_enhance_modals()) {
            return $content;
        }

        $enhanced_data = $this->get_enhanced_event_data($event_id);
        
        if (!$enhanced_data) {
            return $content;
        }

        $enhanced_content = $this->build_enhanced_content($enhanced_data);
        
        return $content . $enhanced_content;
    }

    /**
     * Check if modals should be enhanced.
     */
    private function should_enhance_modals() {
        return get_option('live_connect_modal_enhancement', 'enabled') === 'enabled';
    }

    /**
     * Get enhanced event data from our custom table.
     */
    private function get_enhanced_event_data($event_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'live_connect_event_meta';
        
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE event_id = %d",
            $event_id
        ));

        if (!$result) {
            return false;
        }

        return array(
            'artist_data' => json_decode($result->artist_data, true),
            'venue_data' => json_decode($result->venue_data, true),
            'social_media_data' => json_decode($result->social_media_data, true),
            'multimedia_data' => json_decode($result->multimedia_data, true),
        );
    }

    /**
     * AJAX handler for getting enhanced content.
     */
    public function ajax_get_enhanced_content() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'live_connect_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }

        $event_id = absint($_POST['event_id']);
        if (!$event_id) {
            wp_send_json_error(array('message' => 'Invalid event ID'));
        }

        $enhanced_data = $this->get_enhanced_event_data($event_id);
        
        if (!$enhanced_data) {
            wp_send_json_error(array('message' => 'No enhanced data available'));
        }

        $html = $this->build_enhanced_content($enhanced_data);
        
        wp_send_json_success(array(
            'html' => $html,
            'event_id' => $event_id
        ));
    }

    /**
     * Build enhanced content HTML.
     */
    private function build_enhanced_content($data) {
        ob_start();
        ?>
        <div class="live-connect-enhanced-content">
            <?php if (!empty($data['artist_data'])): ?>
                <div class="live-connect-artists">
                    <h4><?php _e('Artists', 'live-connect'); ?></h4>
                    <?php foreach ($data['artist_data'] as $artist): ?>
                        <div class="live-connect-artist">
                            <?php if (!empty($artist['name'])): ?>
                                <h5><?php echo esc_html($artist['name']); ?></h5>
                            <?php endif; ?>
                            <?php if (!empty($artist['bio'])): ?>
                                <p><?php echo wp_kses_post($artist['bio']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($artist['spotify_url'])): ?>
                                <a href="<?php echo esc_url($artist['spotify_url']); ?>" target="_blank" class="live-connect-spotify">
                                    <?php _e('Listen on Spotify', 'live-connect'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['social_media_data'])): ?>
                <div class="live-connect-social">
                    <h4><?php _e('Follow', 'live-connect'); ?></h4>
                    <div class="live-connect-social-links">
                        <?php foreach ($data['social_media_data'] as $platform => $url): ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" class="live-connect-social-<?php echo esc_attr($platform); ?>">
                                <?php echo esc_html(ucfirst($platform)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['multimedia_data'])): ?>
                <div class="live-connect-multimedia">
                    <h4><?php _e('Media', 'live-connect'); ?></h4>
                    <?php foreach ($data['multimedia_data'] as $media): ?>
                        <?php if ($media['type'] === 'image'): ?>
                            <img src="<?php echo esc_url($media['url']); ?>" alt="<?php echo esc_attr($media['caption'] ?? ''); ?>" class="live-connect-image">
                        <?php elseif ($media['type'] === 'video'): ?>
                            <video controls class="live-connect-video">
                                <source src="<?php echo esc_url($media['url']); ?>" type="video/mp4">
                            </video>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}