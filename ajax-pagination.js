jQuery(document).ready(function ($) {
    let page = 1; // Start with the first page

    jQuery('#load-more').click(function (e) {
        e.preventDefault();
        loadMorePosts();
    });

    // Function to load more posts
    function loadMorePosts() {
        page++;
        const data = {
            action: 'load_more_posts',
            page: page,
            nonce: ajaxpagination.nonce,
        };

        $.post(ajaxpagination.ajaxurl, data, function (response) {
            if($.trim(response)) {
                jQuery('#post-container').append(response); // Append new posts
            } else {
                jQuery('#load-more').hide(); // Hide button if no more posts
            }
        });
    }
});


