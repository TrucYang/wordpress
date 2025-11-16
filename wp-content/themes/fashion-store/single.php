<?php get_header(); ?>

<!-- breadcrumb start-->
<div class="breadcrumb-section">
    <div class="container">
        <h2><?php the_title(); ?></h2>
        <nav class="theme-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo home_url(); ?>">Home</a>
                </li>
                <li class="breadcrumb-item active"><?php the_title(); ?></li>
            </ol>
        </nav>
    </div>
</div>
<!-- breadcrumb end-->

<!--section start-->
<section class="blog-detail-page section-b-space ratio2_3">
    <div class="container">
        <?php
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <div class="blog-detail">
                    <?php if (has_post_thumbnail()): ?>
                        <img class="img-fluid" src="<?php the_post_thumbnail_url('large'); ?>"
                            alt="<?php the_title_attribute(); ?>">
                    <?php endif; ?>
                    <h3><?php the_title(); ?></h3>
                    <ul class="post-social">
                        <li><?php echo get_the_date('d M Y h:i'); ?></li>
                        <li>Posted By : <?php echo get_the_author(); ?></li>
                    </ul>
                </div>

                <div class="blog-detail-contain">
                    <?php the_content(); ?>
                </div>
                <?php
            }
        }
        ?>
        <?php
        if (comments_open() || get_comments_number()):
            comments_template();
        endif;
        ?>
    </div>

</section>

<!--Section ends-->

<?php get_footer(); ?>