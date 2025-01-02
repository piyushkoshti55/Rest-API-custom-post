<?php
/** Template Name: Load More  
 * Page Template for displaying posts with a load more button
 *
 * @package WordPress
 * @subpackage twentytwentyfive
 */

get_header(); ?>

<div id="post-container">
    <?php 
    // Your loop here to display posts initially
    if (have_posts()) :
        while (have_posts()) : the_post(); ?>
            <div class="post">
                <h2><?php the_title(); ?></h2>
                <div><?php the_excerpt(); ?></div>
            </div>
        <?php endwhile;
    endif; ?>
</div>
<button id="load-more">Load More</button>

<?php get_footer(); ?>