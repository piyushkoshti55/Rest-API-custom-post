"# Rest-API-custom-post"
# This is REST API Custom post
<<<<<<< HEAD
# <h1> Book Posts </h1>
# <div id="book-list"></div>
# <div><button id="prev-page">Previous</button>
# <button id="next-page">Next</button></div>
# [book_filter posts_per_page="5"]
=======
# add following custom HTML code in your editor
#<h1> Book Posts </h1>
#<div id="book-list"></div>
#<div><button id="prev-page">Previous</button>
#<button id="next-page">Next</button></div>

>>>>>>> origin/main


function acf_slider_shortcode() {
    ob_start();
    if (have_rows('slider_images', 'option')): ?>
        <div class="slider">
            <?php while (have_rows('slider_images', 'option')) : the_row();
                $image = get_sub_field('image'); ?>
                <div class="slide">
                    <img src="<?php echo esc_url($image); ?>" alt="Slider Image">
                </div>
            <?php endwhile; ?>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let slides = document.querySelectorAll(".slide");
                let index = 0;
                function showSlide() {
                    slides.forEach(slide => slide.style.display = "none");
                    slides[index].style.display = "block";
                    index = (index + 1) % slides.length;
                    setTimeout(showSlide, 3000);
                }
                showSlide();
            });
        </script>
        <style>
            .slider { max-width: 600px; margin: auto; overflow: hidden; }
            .slide { display: none; text-align: center; }
            .slide img { width: 100%; height: auto; border-radius: 10px; }
        </style>
    <?php endif;
    return ob_get_clean();
}
add_shortcode('acf_slider', 'acf_slider_shortcode');
