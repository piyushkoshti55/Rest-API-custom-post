<?php
/**
 * Plugin Name: Book Filter Plugin
 * Description: Custom post type for books with publisher taxonomy and AJAX filtering.
 * Version: 1.0
 * Author: Your Name
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


// Register Custom Post Type and Taxonomy
function bfp_register_custom_post_type() {
    register_post_type('book', [
        'labels' => [
            'name' => __('Books'),
            'singular_name' => __('Book'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    register_taxonomy('publisher', 'book', [
        'labels' => [
            'name' => __('Publishers'),
            'singular_name' => __('Publisher'),
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
}
add_action('init', 'bfp_register_custom_post_type');

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/books', [
        'methods' => 'GET',
        'callback' => function ($data) {
            $args = [
                'post_type' => 'book',
                'posts_per_page' => 5,
                'paged' => $data['page'] ?? 1,
            ];

            $query = new WP_Query($args);
            $posts = [];

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $posts[] = [
                        'title' => get_the_title(),
                        'excerpt' => get_the_excerpt(),
                        'link' => get_permalink(),
                    ];
                }
            }

            return rest_ensure_response($posts);
        },
        'args' => [
            'page' => [
                'default' => 1,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                },
            ],
        ],
    ]);
});


//Register ACF Fields (Assuming ACF is already installed)
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_book_info',
        'title' => 'Book Information',
        'fields' => [
            [
                'key' => 'field_book_isbn',
                'label' => 'ISBN',
                'name' => 'isbn',
                'type' => 'text',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'book',
                ],
            ],
        ],
    ]);
}

// Enqueue Scripts for AJAX
function bfp_enqueue_scripts() {
    wp_enqueue_script('bfp-ajax-script', plugin_dir_url(__FILE__) . 'ajax-filter.js', ['jquery'], null, true);
    wp_localize_script('bfp-ajax-script', 'ajaxfilter', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
     //   'nonce' => wp_create_nonce('bfp_nonce') // Create the 
        'security' => wp_create_nonce('ajax_filter_nonce'), // Create nonce here
    ));
}
add_action('wp_enqueue_scripts', 'bfp_enqueue_scripts');

// AJAX handler for filtering books
function ajax_filter_books() {
    // Security checks, if necessary
    check_ajax_referer('ajax_filter_nonce', 'security');

    $selected_publisher = !empty($_POST['publisher']) ? intval($_POST['publisher']) : '';
    $posts_per_page = !empty($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 10;

    // Prepare the query arguments
    $args = [
        'post_type' => 'book',
        'posts_per_page' => $posts_per_page,
        'tax_query' => [
            [
                'taxonomy' => 'publisher',
                'field' => 'term_id',
                'terms' => $selected_publisher,
            ],
        ],
    ];

    // Execute the query
    $query = new WP_Query($args);
    
    // Start output buffering
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $isbn = get_field('isbn', $post_id );
            echo '<h2>' . get_the_title() . '</h2>'; // Display post title
            echo '<p>'. wp_trim_words( get_the_content(), 20, '...' ) . '<p>';
            //echo get_fields('isbn' , $post_id);
            if( $isbn ) {
            echo '<p> ISBN No :'. esc_html($isbn) . '<p>';
            }
        }
    } else {
        echo 'No books found for the selected publisher!';
    }

    // Reset post data
    wp_reset_postdata();

    // Return the output
    $response = ob_get_clean();
    
    wp_send_json_success($response);
}
add_action('wp_ajax_filter_books', 'ajax_filter_books');
add_action('wp_ajax_nopriv_filter_books', 'ajax_filter_books');

// Shortcode to display book filter form
function book_filter_shortcode($atts) {
    ob_start();

    // Parse shortcode attributes
    $atts = shortcode_atts([
        'posts_per_page' => 10,
    ], $atts);

    // Get all publishers
    $publishers = get_terms([
        'taxonomy' => 'publisher',
        'hide_empty' => false,
    ]);

    // Render the filter form
    if (!empty($publishers) && !is_wp_error($publishers)) {
        ?>
        <form method="GET" id="book_filter_form">
            <select name="publisher" id="publisher">
                <option value="">Select Publisher</option>
                <?php foreach ($publishers as $publisher) : ?>
                    <option value="<?php echo esc_attr($publisher->term_id); ?>">
                        <?php echo esc_html($publisher->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="posts_per_page" value="<?php echo absint($atts['posts_per_page']); ?>" min="1" max="100">
            <button type="submit">Filter</button>
        </form>

        <div id="results"></div> <!-- Container where results will be shown -->

        <?php
    }

    return ob_get_clean(); // Return the buffered output
}
add_shortcode('book_filter', 'book_filter_shortcode');

// Hide Book Filter Page Title 
function hide_title_on_specific_page($title, $id) {
    // Check if we are on the specific page (replace 'book-filter' with your page slug)
    if (is_page('book-filter')) {
        return ''; // Return an empty string to hide the title
    }
    return $title; // Return the default title for other pages
}
add_filter('the_title', 'hide_title_on_specific_page', 10, 2);
 