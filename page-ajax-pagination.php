<?php
/**
 * Template Name: AJAX Pagination
 */

get_header(); ?>

<div id="post-container">
    <?php
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; // Get current page number
    $args = array(
        'post_type' => 'book',
        'paged' => $paged,
        'posts_per_page' => 5, // Number of posts per page
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
            <div class="post">
                <h2><?php the_title(); ?></h2>
                <div><?php the_excerpt(); ?></div>
            </div>
        <?php endwhile; 
        // Display pagination
        $total_pages = $query->max_num_pages;
        ?>

        <div id="pagination">
    <span class="page-numbers prev" data-page="<?php echo max(1, $paged - 1); ?>">Prev</span>
    <?php
    for ($i = 1; $i <= $total_pages; $i++) {
        // Highlight the current page
        $active_class = ($i == $paged) ? 'active' : '';
        echo '<span class="page-numbers ' . $active_class . '" data-page="' . $i . '">' . $i . '</span>';
    }
    ?>
    <span class="page-numbers next" data-page="<?php echo min($total_pages, $paged + 1); ?>">Next</span>
</div>
    <?php else : ?>
        <p>No posts found.</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>

<?php get_footer(); ?>