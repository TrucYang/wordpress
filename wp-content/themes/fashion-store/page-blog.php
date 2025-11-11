<?php get_header(); ?>
<!-- breadcrumb start -->
<div class="breadcrumb-section">
    <div class="container">
        <h2>Blog</h2>
    </div>
</div>
<!-- breadcrumb End -->


<!-- section start -->
<section class="blog-page section-b-space ratio2_3">
    <div class="container">
        <div class="row g-sm-4 g-3">
            <div class="col-lg-8 col-xxl-9">
                <div class="sticky-details">
                    <div class="row g-4">
                        <?php
                        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                        $args = array(
                            'post_type' => 'post',
                            'posts_per_page' => 9,
                            'paged' => $paged,
                        );
                        $query = new WP_Query($args);

                        if ($query->have_posts()):
                            while ($query->have_posts()):
                                $query->the_post(); ?>
                                <div class="col-sm-6 col-xxl-4">
                                    <div class="blog-box sticky-blog-box">
                                        <div class="blog-image">
                                            <div class="blog-label-tag"><i class="ri-pushpin-fill"></i></div>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php
                                                if (has_post_thumbnail()) {
                                                    the_post_thumbnail('medium', ['alt' => get_the_title()]);
                                                } else { ?>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/no-image.jpg"
                                                        alt="<?php the_title(); ?>">
                                                <?php } ?>
                                            </a>
                                        </div>
                                        <div class="blog-contain">
                                            <a href="<?php the_permalink(); ?>">
                                                <h3><?php the_title(); ?></h3>
                                            </a>
                                            <div class="blog-label">
                                                <span class="time"><i
                                                        class="ri-time-line"></i><span><?php echo get_the_date('d M Y'); ?></span></span>
                                                <span class="super"><i
                                                        class="ri-user-line"></i><span><?php the_author(); ?></span></span>
                                            </div>
                                            <p><?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?></p>
                                            <a class="blog-button" href="<?php the_permalink(); ?>">
                                                Read More <i class="ri-arrow-right-line"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile;
                        endif;
                        wp_reset_postdata(); ?>
                    </div>

                    <!-- Pagination -->
                    <div class="product-pagination">
                        <div class="theme-paggination-block">
                            <nav>
                                <ul class="pagination">
                                    <?php
                                    $total_pages = $query->max_num_pages;
                                    if ($total_pages > 1) {
                                        $current_page = max(1, get_query_var('paged'));

                                        // Previous
                                        if ($current_page > 1) {
                                            echo '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($current_page - 1) . '" aria-label="Previous"><span><i class="ri-arrow-left-s-line"></i></span></a></li>';
                                        }

                                        // Page numbers
                                        for ($i = 1; $i <= $total_pages; $i++) {
                                            $active = ($i == $current_page) ? ' active' : '';
                                            echo '<li class="page-item' . $active . '"><a class="page-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                                        }

                                        // Next
                                        if ($current_page < $total_pages) {
                                            echo '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($current_page + 1) . '" aria-label="Next"><span><i class="ri-arrow-right-s-line"></i></span></a></li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-lg-4">
                <div class="blog-sidebar">
                    <div class="theme-card">
                        <h4>Recent Blog</h4>
                        <ul class="recent-blog">
                            <?php
                            $recent_posts = new WP_Query(array(
                                'post_type' => 'post',
                                'posts_per_page' => 5,
                                'orderby' => 'date',
                                'order' => 'DESC'
                            ));
                            if ($recent_posts->have_posts()) {
                                while ($recent_posts->have_posts()) {
                                    $recent_posts->the_post();
                                    $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : get_template_directory_uri() . '/assets/images/no-image.jpg';
                                    $date = get_the_date('d M Y h:i');
                                    ?>
                                    <li>
                                        <div class="media blog-box">
                                            <div class="blog-image">
                                                <img class="img-fluid lazyload" src="<?php echo esc_url($thumb); ?>"
                                                    alt="<?php the_title_attribute(); ?>">
                                            </div>
                                            <div class="media-body blog-content">
                                                <h6><?php echo esc_html($date); ?></h6>
                                                <a href="<?php the_permalink(); ?>">
                                                    <h5 class="recent-name"><?php the_title(); ?></h5>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                }
                                wp_reset_postdata();
                            }
                            ?>
                        </ul>
                    </div>


                    <!-- Categories -->
                    <div class="theme-card">
                        <h4>Categories</h4>
                        <ul class="categories">
                            <?php
                            $all_categories = get_categories(array(
                                'orderby' => 'name',
                                'order' => 'ASC'
                            ));
                            foreach ($all_categories as $category) {
                                echo '<li>
                    <a class="category-name" href="' . get_category_link($category->term_id) . '">
                        <h5>' . esc_html($category->name) . '</h5>
                        <span>(' . $category->count . ')</span>
                    </a>
                  </li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Tags -->
                    <div class="theme-card">
                        <h4>Tags</h4>
                        <ul class="tags">
                            <?php
                            $all_tags = get_tags();
                            if ($all_tags) {
                                foreach ($all_tags as $tag) {
                                    echo '<li><a href="' . get_tag_link($tag->term_id) . '">' . esc_html($tag->name) . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->
<?php get_footer(); ?>