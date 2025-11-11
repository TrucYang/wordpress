<?php get_header() ?>
<!-- breadcrumb start -->
<div class="breadcrumb-section">
    <div class="container">
        <h2>Shop</h2>
        <nav class="theme-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            </ol>
        </nav>
    </div>
</div>
<!-- breadcrumb end -->


<!-- product listing section start -->
<section class="section-b-space ratio_square category-shop-section">
    <div class="collection-wrapper">
        <div class="container">
            <a href="javascript:void(0)" class="d-xl-none d-inline-block category-mobile-button"><i
                    class="fa fa-bars"></i> Category</a>
            <div class="row">
                <!-- SIDEBAR CATEGORY -->
                <div class="col-xl-3">
                    <div class="sidebar-overlay"></div>
                    <div class="nav flex-column" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link d-xl-none d-block sidebar-back">Back</a>
                        <?php
                        $args = array(
                            'taxonomy' => 'product_cat',
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                        );
                        $product_categories = get_terms($args);

                        if (!empty($product_categories) && !is_wp_error($product_categories)) {
                            foreach ($product_categories as $category) {
                                $active_class = (is_tax('product_cat', $category->slug)) ? 'active' : '';
                                echo '<a class="nav-link ' . $active_class . '" href="' . get_term_link($category) . '">';
                                echo esc_html($category->name);
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- PRODUCT LIST -->
                <div class="col-xl-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active">
                            <div class="title8">
                                <?php
                                if (is_tax('product_cat')) {
                                    $term = get_queried_object();
                                    echo '<h2>' . esc_html($term->name) . '</h2>';
                                    echo '<p>' . esc_html($term->description) . '</p>';
                                } else {
                                    echo '<h2>All Products</h2>';
                                }
                                ?>
                            </div>
                            <div class="row g-sm-4 g-3">
                                <?php
                                $args = array(
                                    'post_type' => 'product',
                                    'posts_per_page' => 8,
                                );

                                if (is_tax('product_cat')) {
                                    $args['tax_query'] = array(
                                        array(
                                            'taxonomy' => 'product_cat',
                                            'field' => 'slug',
                                            'terms' => $term->slug,
                                        ),
                                    );
                                }

                                $loop = new WP_Query($args);
                                if ($loop->have_posts()) {
                                    while ($loop->have_posts()):
                                        $loop->the_post();
                                        global $product;
                                        ?>
                                        <div class="col-lg-3 col-md-4 col-6">
                                            <div class="product-box product-style-5">
                                                <a href="<?php the_permalink(); ?>">
                                                    <h6><?php the_title(); ?></h6>
                                                </a>
                                                <h4><?php echo $product->get_price_html(); ?></h4>
                                                <div class="addtocart_btn">
                                                    <button class="add-button add_cart" title="Add to cart">
                                                        <i class="ri-add-fill"></i>
                                                    </button>
                                                    <div class="qty-box cart_qty">
                                                        <!-- <div class="input-group">
                                                    <button type="button" class="btn quantity-left-minus"
                                                        data-type="minus" data-field="">
                                                        <i class="ri-subtract-fill"></i>
                                                    </button>
                                                    <input type="text" name="quantity" readonly
                                                        class="form-control input-number qty-input" value="1">
                                                    <button type="button" class="btn quantity-right-plus"
                                                        data-type="plus" data-field="">
                                                        <i class="ri-add-fill"></i>
                                                    </button>
                                                </div> -->
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php echo woocommerce_get_product_thumbnail('medium'); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="img-wrapper">
                                                    <div class="front">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php echo woocommerce_get_product_thumbnail('medium'); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    endwhile;
                                    wp_reset_postdata();
                                } else {
                                    echo '<p class="ps-3">No products found in this category.</p>';
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- product listing section end -->
<?php get_footer(); ?>