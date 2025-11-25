<?php get_header(); ?>

<div class="main-content">
    <?php 
    // Render Page Builder Components
    fs_render_page_components();
    ?>
    <?php the_content(); ?>
</div>

<?php get_footer(); ?>
