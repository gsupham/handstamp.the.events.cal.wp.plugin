/**
 * Admin JavaScript for Live Connect plugin
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle manual sync button
        $('#manual-sync-btn').on('click', function() {
            var $button = $(this);
            var $status = $('#sync-status');
            
            // Disable button and show loading
            $button.prop('disabled', true);
            $button.html('<span class="live-connect-spinner"></span>' + live_connect_ajax.strings.sync_in_progress);
            
            // Show loading status
            $status.removeClass('success error').addClass('loading');
            $status.text(live_connect_ajax.strings.sync_in_progress);
            
            // Make AJAX request
            $.ajax({
                url: live_connect_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'live_connect_sync_events',
                    nonce: live_connect_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.removeClass('loading error').addClass('success');
                        $status.text(response.data.message);
                    } else {
                        $status.removeClass('loading success').addClass('error');
                        $status.text(response.data.message || live_connect_ajax.strings.sync_error);
                    }
                },
                error: function() {
                    $status.removeClass('loading success').addClass('error');
                    $status.text(live_connect_ajax.strings.sync_error);
                },
                complete: function() {
                    // Re-enable button
                    $button.prop('disabled', false);
                    $button.text(live_connect_ajax.strings.sync_button || 'Sync Events Now');
                }
            });
        });

        // Handle manual update check button
        $('#check-updates-btn').on('click', function() {
            var $button = $(this);
            var $status = $('#update-status');
            
            // Disable button and show loading
            $button.prop('disabled', true);
            $button.html('<span class="live-connect-spinner"></span>' + live_connect_ajax.strings.update_check_in_progress);
            
            // Show loading status
            $status.removeClass('success error').addClass('loading');
            $status.text(live_connect_ajax.strings.update_check_in_progress);
            
            // Make AJAX request
            $.ajax({
                url: live_connect_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'live_connect_check_updates',
                    nonce: live_connect_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.removeClass('loading error').addClass('success');
                        $status.html(response.data.message);
                    } else {
                        $status.removeClass('loading success').addClass('error');
                        $status.text(response.data.message || live_connect_ajax.strings.update_check_error);
                    }
                },
                error: function() {
                    $status.removeClass('loading success').addClass('error');
                    $status.text(live_connect_ajax.strings.update_check_error);
                },
                complete: function() {
                    // Re-enable button
                    $button.prop('disabled', false);
                    $button.text(live_connect_ajax.strings.update_check_button || 'Check for Updates Now');
                }
            });
        });

        // Handle API token field toggle
        var $tokenField = $('#api_token');
        if ($tokenField.length) {
            var $toggleButton = $('<button type="button" class="button" style="margin-left: 5px;">Show</button>');
            $tokenField.after($toggleButton);
            
            $toggleButton.on('click', function() {
                if ($tokenField.attr('type') === 'password') {
                    $tokenField.attr('type', 'text');
                    $toggleButton.text('Hide');
                } else {
                    $tokenField.attr('type', 'password');
                    $toggleButton.text('Show');
                }
            });
        }

        // Form validation
        $('form').on('submit', function() {
            var endpoint = $('#api_endpoint').val().trim();
            var token = $('#api_token').val().trim();
            
            if (endpoint && !isValidUrl(endpoint)) {
                alert('Please enter a valid API endpoint URL.');
                return false;
            }
            
            if (token && token.length < 10) {
                alert('API token seems too short. Please check your token.');
                return false;
            }
            
            return true;
        });
    });

    // Helper function to validate URLs
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

})(jQuery);