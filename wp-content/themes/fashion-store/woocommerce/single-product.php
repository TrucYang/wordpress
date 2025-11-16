<!-- breadcrumb start -->
<?php
defined('ABSPATH') || exit;
get_header();

if (have_posts()):
    while (have_posts()):
        the_post();
        // wc_get_template_part( 'content', 'single-product' );

        $product = wc_get_product(get_the_ID());
        if (!$product)
            continue;

        $attachment_ids = $product->get_gallery_image_ids(); // Lấy gallery
        $main_image = wp_get_attachment_url($product->get_image_id()); // Hình chính
        ?>
        <div class="breadcrumb-section">
            <div class="container">
                <h2><?php the_title(); ?></h2>
                <nav class="theme-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">Product</li>
                        <li class="breadcrumb-item active"><?php the_title(); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- breadcrumb End -->


        <!-- section start -->
        <section>
            <div class="collection-wrapper">
                <div class="container">
                    <div class="collection-wrapper">
                        <div class="row g-4">

                            <!-- Product gallery -->
                            <div class="col-lg-4">
                                <div class="product-slick">
                                    <?php if ($main_image): ?>
                                        <div><img src="<?php echo esc_url($main_image); ?>" alt=""
                                                class="w-100 img-fluid blur-up lazyload"></div>
                                    <?php endif; ?>
                                    <?php foreach ($attachment_ids as $id): ?>
                                        <div><img src="<?php echo esc_url(wp_get_attachment_url($id)); ?>" alt=""
                                                class="w-100 img-fluid blur-up lazyload"></div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- <div class="row">
                                    <div class="col-12">
                                        <div class="slider-nav">
                                            <?php if ($main_image): ?>
                                                <div><img src="<?php echo esc_url($main_image); ?>" alt=""
                                                        class="img-fluid blur-up lazyload"></div>
                                            <?php endif; ?>
                                            <?php foreach ($attachment_ids as $id): ?>
                                                <div><img src="<?php echo esc_url(wp_get_attachment_url($id)); ?>" alt=""
                                                        class="img-fluid blur-up lazyload"></div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div> -->
                            </div>

                            <!-- Product details -->
                            <div class="col-lg-4">
                                <div class="product-page-details product-description-box sticky-details mt-0">

                                    <div class="trending-text">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/product-details/trending.gif"
                                            class="img-fluid" alt="">
                                        <h5>Selling fast! 4 people have this in their carts.</h5>
                                    </div>

                                    <h2 class="main-title"><?php the_title(); ?></h2>

                                    <div class="product-rating">
    <div class="rating-list">
        <?php
        $average = $product->get_average_rating();
        for ($i = 1; $i <= 5; $i++) {
            echo $i <= $average ? '<i class="ri-star-fill"></i>' : '<i class="ri-star-line"></i>';
        }
        ?>
    </div>
    <span class="divider">|</span>
    <a href="#!"><?php echo $product->get_review_count(); ?> Reviews</a>
    <span class="divider">|</span>
    <span>
        <?php
        $views = get_post_meta(get_the_ID(), '_product_views', true);
        echo $views ? $views : 0;
        ?> Views
    </span>
</div>






                                    <div class="price-text">
                                        <h3><span class="fw-normal d-inline">MRP:
                                            </span>
                                            <?php echo '$' . number_format($product->get_price(), 2); ?>
                                        </h3>
                                        </h3>
                                        <span>Inclusive all the taxes</span>
                                    </div>

                                    <div class="size-delivery-info flex-wrap">
                                        <a href="#return" data-bs-toggle="modal"><i class="ri-truck-line"></i> Delivery &
                                            Return</a>
                                        <a href="#ask-question" data-bs-toggle="modal"><i class="ri-questionnaire-line"></i> Ask
                                            a Question</a>
                                    </div>

                                    <div class="accordion accordion-flush product-accordion" id="accordionFlushExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                                    aria-expanded="false" aria-controls="flush-collapseOne">
                                                    Product Description
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <?php the_content(); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                                    aria-controls="flush-collapseTwo">
                                                    Information
                                                </button>
                                            </h2>
                                            <div id="flush-collapseTwo" class="accordion-collapse collapse show"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <div class="bordered-box border-0 mt-0 pt-0">
                                                        <h4 class="sub-title">Product Info</h4>
                                                        <ul class="shipping-info">
                                                            <li><span>SKU: </span><?php echo $product->get_sku(); ?></li>
                                                            <li><span>Unit: </span>1 Item</li>
                                                            <li><span>Weight:
                                                                </span><?php echo $product->get_weight() ? $product->get_weight() . ' Gms' : 'N/A'; ?>
                                                            </li>
                                                            <li><span>Stock Status:
                                                                </span><?php echo $product->is_in_stock() ? 'In stock' : 'Out of stock'; ?>
                                                            </li>
                                                            <li><span>Quantity:
                                                                </span><?php echo $product->get_stock_quantity(); ?> Items Left
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <div class="bordered-box">
                                                        <h4 class="sub-title">Delivery Details</h4>
                                                        <ul class="delivery-details">
                                                            <li><i class="ri-truck-line"></i> Your order is likely to reach you
                                                                within 7 days.</li>
                                                            <li><i class="ri-arrow-left-right-line"></i> Hassle free returns
                                                                within 7 Days.</li>
                                                        </ul>
                                                    </div>

                                                    <div class="dashed-border-box mb-0">
                                                        <h4 class="sub-title">Guaranteed Safe Checkout</h4>
                                                        <img class="img-fluid payment-img" alt=""
                                                            src="<?php echo get_template_directory_uri(); ?>/assets/images/product-details/payments.png">
                                                    </div>

                                                    <div class="dashed-border-box mb-0">
                                                        <h4 class="sub-title">Secure Checkout</h4>
                                                        <img class="img-fluid payment-img" alt=""
                                                            src="<?php echo get_template_directory_uri(); ?>/assets/images/product-details/secure_payments.png">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product options / buy box -->
                            <div class="col-lg-4">
                                <div class="product-page-details product-form-box product-right-box d-flex
                                align-items-center flex-column my-0">
                                <h4 class="sub-title">Quantity:</h4>
                                    <!-- <h4 class="sub-title">Colour:</h4>
                                    <div class="variation-box size-box">
                                        <ul class="image-box image">
                                            <?php
                                            $color_images = array_slice($attachment_ids, 0, 3);
                                            foreach ($color_images as $index => $id):
                                                ?>
                                                <li class="<?php echo $index === 0 ? 'active' : ''; ?>">
                                                    <a><img src="<?php echo esc_url(wp_get_attachment_url($id)); ?>" alt=""></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div> -->

                                    <div class="product-buttons">
                                        <div class="qty-section">
                                            <div class="qty-box">
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <button type="button" class="btn quantity-left-minus" data-type="minus"
                                                            data-field="quantity">
                                                            <i class="ri-arrow-left-s-line"></i>
                                                        </button>
                                                    </span>

                                                    <?php
                                                    $max_qty = $product->managing_stock() ? $product->get_stock_quantity() : '';
                                                    $in_stock = $product->is_in_stock();
                                                    ?>
                                                    <input type="number" name="quantity" class="form-control input-number"
                                                        value="1" min="1" <?php echo $max_qty ? 'max="' . $max_qty . '"' : ''; ?>
                                                        <?php echo !$in_stock ? 'disabled' : ''; ?>>

                                                    <span class="input-group-prepend">
                                                        <button type="button" class="btn quantity-right-plus" data-type="plus"
                                                            data-field="quantity">
                                                            <i class="ri-arrow-right-s-line"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-buttons">
                                        <div class="d-flex align-items-center gap-3">
                                            <button class="btn btn-animation btn-solid hover-solid scroll-button disabled"
                                                type="button"> Out Of Stock </button>
                                            <a href="#!" class="btn btn-solid buy-button disabled">Buy Now</a>
                                        </div>
                                    </div>

                                    <div class="left-progressbar w-100">
                                        <h6>Please Hurry Only <?php echo $product->get_stock_quantity(); ?> Left In Stock</h6>
                                        <div role="progressbar" class="progress">
                                            <div class="progress-bar"
                                                style="width: <?php echo $product->get_stock_quantity() ? '100%' : '0%'; ?>;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="buy-box justify-content-center gap-3">
                                        <a href="#!"><i class="ri-heart-line"></i><span>Add To Wishlist</span></a>
                                        <a href="#!" class="add-compare"><i class="ri-refresh-line"></i><span>Add To
                                                Compare</span></a>
                                        <a href="#share" data-bs-toggle="modal"><i
                                                class="ri-share-line"></i><span>Share</span></a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php
    endwhile;
endif;
?>

<!-- Section ends -->


<!-- product-tab starts -->
<section class="tab-product m-0">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <ul class="nav nav-tabs nav-material" id="top-tab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="top-home-tab" data-bs-toggle="tab"
                            href="#top-home" role="tab" aria-selected="true"><i
                                class="icofont icofont-ui-home"></i>Description</a>
                    </li>

                    <li class="nav-item"><a class="nav-link" id="review-top-tab" data-bs-toggle="tab" href="#top-review"
                            role="tab" aria-selected="false"><i class="icofont icofont-contacts"></i>Review</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab"
                            href="#top-contact" role="tab" aria-selected="false"><i
                                class="icofont icofont-contacts"></i>Q & A</a>
                    </li>
                </ul>
                <div class="tab-content nav-material" id="top-tabContent">
                    <div class="tab-pane fade show active" id="top-home" role="tabpanel" aria-labelledby="top-home-tab">
                        <div class="product-tab-description">
                            <div class="part">
                                <p>The Model is wearing a white blouse from our stylist's
                                    collection, see the image for a mock-up of what the actual
                                    blouse would look like.it has text written on it in a black
                                    cursive language which looks great on a white color.</p>
                            </div>
                            <div class="part">
                                <h5 class="inner-title">fabric:</h5>
                                <p>Art silk is manufactured by synthetic fibres like rayon. It's
                                    light in weight and is soft on the skin for comfort in
                                    summers.Art silk is manufactured by synthetic fibres like rayon.
                                    It's light in weight and is soft on the skin for comfort in
                                    summers.</p>
                            </div>
                            <div class="part">
                                <h5 class="inner-title">size & fit:</h5>
                                <p>The model (height 5'8") is wearing a size S</p>
                            </div>
                            <div class="part">
                                <h5 class="inner-title">Material & Care:</h5>
                                <p>Top fabric: pure cotton</p>
                                <p>Bottom fabric: pure cotton</p>
                                <p>Hand-wash</p>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade" id="top-review" role="tabpanel" aria-labelledby="review-top-tab">
                        <?php wc_get_template('single-product-reviews.php'); ?>
                    </div>
                    <div class="tab-pane fade" id="top-contact" role="tabpanel" aria-labelledby="contact-top-tab">
                        <div class="post-question-box">
                            <h4>Have Doubts Regarding This Product ? <a href="#ask-question" data-bs-toggle="modal">Post
                                    Your Question</a>
                            </h4>
                        </div>
                        <div class="question-answer">
                            <ul>
                                <li>
                                    <div class="question-box">
                                        <h5>Q1</h5>
                                        <h6 class="font-weight-bold que">Does
                                            the dress offer any UV
                                            protection?</h6>
                                        <ul class="link-dislike-box">
                                            <li><a href="#!"><span><i class="ri-thumb-up-fill"></i>
                                                        0</span></a></li>
                                            <li><a href="#!"><span><i class="ri-thumb-down-fill"></i>
                                                        0</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="answer-box">
                                        <div class="answer-box">
                                            <h5>A1</h5>
                                            <p class="ans">Yes, the dress
                                                offers UV protection. It blocks
                                                harmful UV rays, providing an additional layer of sun
                                                safety. </p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="question-box">
                                        <h5>Q2</h5>
                                        <h6 class="font-weight-bold que">Are
                                            there any pockets, and if so,
                                            how many and where are they located?</h6>
                                        <ul class="link-dislike-box">
                                            <li><a href="#!"><span><i class="ri-thumb-up-fill"></i>
                                                        0</span></a></li>
                                            <li><a href="#!"><span><i class="ri-thumb-down-fill"></i>
                                                        0</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="answer-box">
                                        <div class="answer-box">
                                            <h5>A2</h5>
                                            <p class="ans">Yes, there are
                                                pockets. There are two pockets,
                                                one on each side of the garment. </p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="question-box">
                                        <h5>Q3</h5>
                                        <h6 class="font-weight-bold que">Is the
                                            fabric breathable and
                                            quick-drying?</h6>
                                        <ul class="link-dislike-box">
                                            <li><a href="#!"><span><i class="ri-thumb-up-fill"></i>
                                                        0</span></a></li>
                                            <li><a href="#!"><span><i class="ri-thumb-down-fill"></i>
                                                        0</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="answer-box">
                                        <div class="answer-box">
                                            <h5>A3</h5>
                                            <p class="ans">Yes, the fabric is
                                                breathable, allowing for
                                                excellent airflow. Additionally, it is quick-drying,
                                                ensuring comfort during and after
                                                activities. </p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- product-tab ends -->

<!-- related products -->
<?php
global $product;

if ( ! $product ) return;

// Lấy sản phẩm liên quan theo category
$related_products = wc_get_related_products( $product->get_id(), 5 ); // lấy 5 sản phẩm

if ( $related_products ) :
?>
<section class="section-b-space ratio_asos">
    <div class="container">
        <div class="row">
            <div class="col-12 product-related">
                <h2>related products</h2>
            </div>
        </div>

        <div class="product-5 product-m no-arrow">
            <?php foreach ( $related_products as $related_id ) :
                $related_product = wc_get_product( $related_id );
                $permalink = get_permalink( $related_id );
                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $related_id ), 'full' );
                $price_html = $related_product->get_price_html();
                $title = $related_product->get_name();
            ?>
            <div class="basic-product theme-product-1">
                <div class="overflow-hidden">
                    <div class="img-wrapper">
                        <a href="<?php echo esc_url($permalink); ?>">
                            <img src="<?php echo esc_url($image[0]); ?>" class="img-fluid blur-up lazyload" alt="<?php echo esc_attr($title); ?>">
                        </a>
                        <?php if ( $related_product->get_average_rating() > 0 ) : ?>
                        <div class="rating-label">
                            <i class="ri-star-fill"></i>
                            <span><?php echo esc_html($related_product->get_average_rating()); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="cart-info">
                            <ul class="hover-action">
                                <li>
                                    <button class="add_to_cart_button" data-product_id="<?php echo esc_attr($related_id); ?>" title="Add to cart">
                                        <i class="ri-shopping-cart-line"></i>
                                    </button>
                                </li>
                                <li>
                                    <a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>?add_to_wishlist=1" title="Add to Wishlist">
                                        <i class="ri-heart-line"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo esc_url($permalink); ?>" title="Quick View">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo esc_url($permalink); ?>?add_to_compare=1" title="Compare">
                                        <i class="ri-loop-left-line"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="product-detail">
                        <div>
                            <div class="brand-w-color">
                                <a class="product-title" href="<?php echo esc_url($permalink); ?>">
                                    <?php echo esc_html($title); ?>
                                </a>
                            </div>
                            <h6><?php echo esc_html($related_product->get_short_description()); ?></h6>
                            <h4 class="price"><?php echo $price_html; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- related products -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const minusBtn = document.querySelector('.quantity-left-minus');
    const plusBtn = document.querySelector('.quantity-right-plus');
    const input = document.querySelector('input[name="quantity"]');
    const max = parseInt(input.getAttribute('max')) || 1000; 

    minusBtn.addEventListener('click', function() {
        let val = parseInt(input.value);
        if (val > 1) input.value = val - 1;
    });

    plusBtn.addEventListener('click', function() {
        let val = parseInt(input.value);
        if (val < max) input.value = val + 1;
    });
});
</script>

<?php get_footer(); ?>