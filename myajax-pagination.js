jQuery(document).ready(function ($) {
    jQuery(document).on('click', '.page-numbers', function (e) {
        e.preventDefault(); // Prevent the default link behavior

        var page = jQuery(this).data('page'); // Get the page number from the data attribute

        var data = {
            'action': 'load_posts', // Action for the WordPress AJAX
            'page': page, // Current page number
            'nonce': my_ajax_object.nonce // Nonce for security
        };

        
        // Make an AJAX POST request
        $.post(my_ajax_object.ajax_url, data, function (response) {
            jQuery('#post-container').html(response); // Replace the posts with new ones
        });
    });
});