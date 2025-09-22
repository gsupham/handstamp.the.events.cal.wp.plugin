/**
 * Public JavaScript for Live Connect plugin
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Only run on events pages
        if (!live_connect_public.is_events_page) {
            return;
        }

        // Initialize enhanced modals if enabled
        if (live_connect_public.enhanced_modals) {
            initializeModalEnhancements();
        }

        // Handle AJAX requests for enhanced content
        setupAjaxHandlers();
    });

    function initializeModalEnhancements() {
        // Enhance existing event tooltips/modals
        $('.tribe-events-calendar-month__calendar-event-tooltip').each(function() {
            enhanceModal($(this));
        });

        // Watch for dynamically created modals
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    $(mutation.addedNodes).find('.tribe-events-calendar-month__calendar-event-tooltip').each(function() {
                        enhanceModal($(this));
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    function enhanceModal($modal) {
        // Check if already enhanced
        if ($modal.hasClass('live-connect-enhanced')) {
            return;
        }

        $modal.addClass('live-connect-enhanced');

        // Get event ID from various possible sources
        var eventId = $modal.data('event-id') || 
                     $modal.find('[data-event-id]').data('event-id') ||
                     $modal.attr('data-event-id');

        if (!eventId) {
            // Try to extract from URL or other attributes
            var href = $modal.find('a').attr('href');
            if (href) {
                var match = href.match(/event\/(\d+)/);
                if (match) {
                    eventId = match[1];
                }
            }
        }

        if (eventId) {
            loadEnhancedContent($modal, eventId);
        }
    }

    function loadEnhancedContent($modal, eventId) {
        // Add loading indicator
        var $loading = $('<div class="live-connect-loading">Loading enhanced content...</div>');
        $modal.append($loading);

        // Make AJAX request
        $.ajax({
            url: live_connect_public.ajax_url,
            type: 'POST',
            data: {
                action: 'live_connect_get_enhanced_content',
                event_id: eventId,
                nonce: live_connect_public.nonce
            },
            timeout: 10000,
            success: function(response) {
                $loading.remove();
                
                if (response.success && response.data.html) {
                    $modal.append(response.data.html);
                    
                    // Trigger custom event for other scripts
                    $modal.trigger('live-connect:enhanced', [eventId, response.data]);
                } else {
                    $modal.append('<div class="live-connect-loading">Enhanced content unavailable</div>');
                }
            },
            error: function() {
                $loading.remove();
                $modal.append('<div class="live-connect-loading">Error loading enhanced content</div>');
            }
        });
    }

    function setupAjaxHandlers() {
        // Handle clicks on enhanced content links
        $(document).on('click', '.live-connect-enhanced-content a[target="_blank"]', function() {
            // Track clicks if analytics are available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    event_category: 'Live Connect',
                    event_label: $(this).attr('href'),
                    transport_type: 'beacon'
                });
            }
        });

        // Handle media interactions
        $(document).on('play', '.live-connect-video', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'video_play', {
                    event_category: 'Live Connect',
                    event_label: 'Enhanced Modal Video'
                });
            }
        });
    }

    // Utility function to safely parse JSON
    function safeJsonParse(str, defaultValue) {
        try {
            return JSON.parse(str);
        } catch (e) {
            return defaultValue || {};
        }
    }

    // Expose public API
    window.LiveConnect = {
        enhanceModal: enhanceModal,
        loadEnhancedContent: loadEnhancedContent
    };

})(jQuery);