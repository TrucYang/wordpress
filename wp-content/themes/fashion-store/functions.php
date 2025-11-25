<?php
// Thêm support cơ bản
function fs_theme_setup()
{
    load_theme_textdomain('fashion-store', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo');// cho logo
    add_theme_support('html5', array('search-form', 'comment-form', 'gallery', 'caption'));
    add_theme_support('woocommerce'); // support woocommerce
    add_image_size('fs-archive-thumb', 400, 300, true);
}
add_action('after_setup_theme', 'fs_theme_setup');

// Đăng ký menu
function fs_register_menus()
{
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'fashion-store'),
        'footerLocationOne' => __('Footer Menu Location One', 'fashion-store'),
        'footerLocationTwo' => __('Footer Menu Location Two', 'fashion-store'),
    ));
}
add_action('after_setup_theme', 'fs_register_menus');

function mytheme_enqueue_assets()
{
    // Vendors CSS
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/assets/css/vendors/font-awesome.css');
    wp_enqueue_style('remixicon', get_template_directory_uri() . '/assets/css/vendors/remixicon.css');
    wp_enqueue_style('slick', get_template_directory_uri() . '/assets/css/vendors/slick.css');
    wp_enqueue_style('animate', get_template_directory_uri() . '/assets/css/vendors/animate.css');
    wp_enqueue_style('themify-icons', get_template_directory_uri() . '/assets/css/vendors/themify-icons.css');
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/vendors/bootstrap.css');

    // Theme main CSS
    wp_enqueue_style('theme-style', get_template_directory_uri() . '/assets/css/style.css');
    wp_enqueue_style('fs-section-builder', get_template_directory_uri() . '/assets/css/section-builder.css', array('theme-style'), '1.0.0');

    // --- JS ---
    // WordPress có sẵn jQuery, không cần file jquery-3.3.1.min.js
    wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/assets/js/jquery-ui.min.js', array('jquery'), false, true);
    wp_enqueue_script('jquery-exitintent', get_template_directory_uri() . '/assets/js/jquery.exitintent.js', array('jquery'), false, true);
    wp_enqueue_script('exit', get_template_directory_uri() . '/assets/js/exit.js', array('jquery'), false, true);
    wp_enqueue_script('slick', get_template_directory_uri() . '/assets/js/slick.js', array('jquery'), false, true);
    wp_enqueue_script('menu', get_template_directory_uri() . '/assets/js/menu.js', array('jquery'), false, true);
    wp_enqueue_script('lazyload', get_template_directory_uri() . '/assets/js/lazysizes.min.js', array(), false, true);
    wp_enqueue_script('bootstrap-bundle', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array('jquery'), false, true);
    wp_enqueue_script('bootstrap-notify', get_template_directory_uri() . '/assets/js/bootstrap-notify.min.js', array('jquery'), false, true);
    wp_enqueue_script('fly-cart', get_template_directory_uri() . '/assets/js/fly-cart.js', array('jquery'), false, true);
    wp_enqueue_script('theme-setting', get_template_directory_uri() . '/assets/js/theme-setting.js', array('jquery'), false, true);
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/assets/js/script.js', array('jquery'), false, true);
    wp_enqueue_script('fs-section-builder', get_template_directory_uri() . '/assets/js/section-builder.js', array('jquery', 'slick'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_assets');


function mytheme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'mytheme_add_woocommerce_support');


class FS_Walker_Nav_Menu extends Walker_Nav_Menu
{
    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $output .= "\n<ul class=\"sub-menu\">\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', array_filter($classes));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $output .= '<li' . $class_names . '>';
        $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $output .= '<a' . $attributes . '>';
        $output .= esc_html($item->title);

        if (in_array('new-label', $classes)) {
            $output .= '<div class="lable-nav">new</div>';
        }

        $output .= '</a>';
    }

    function end_el(&$output, $item, $depth = 0, $args = array())
    {
        $output .= "</li>\n";
    }
}


function my_enqueue_search_modal()
{
    wp_enqueue_script(
        'aws-modal',
        get_template_directory_uri() . '/assets/js/aws-modal.js',
        array('jquery'),
        false,
        true
    );

    wp_localize_script('aws-modal', 'aws_search', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'my_enqueue_search_modal');

function theme_enqueue_woocommerce_ajax()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('wc-add-to-cart');
    wp_localize_script('wc-add-to-cart', 'wc_add_to_cart_params', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'theme_enqueue_woocommerce_ajax');

add_action('woocommerce_thankyou', 'after_order_created');
function after_order_created($order_id)
{
    $order = wc_get_order($order_id);

}

//đổi tên categoty "Chưa phân loại" mặc định thành All và slug all-products
add_action('init', function () {
    $term = get_term_by('slug', 'uncategorized', 'product_cat');
    if ($term && !is_wp_error($term)) {
        wp_update_term($term->term_id, 'product_cat', array( //wp_update_term dùng đổi tên hiển thị
            'name' => 'All',
            'slug' => 'all-products',
        ));
    }
});

add_action('pre_get_posts', function ($query) { //hook pre_get_posts dùng để xóa filter 
    if (!is_admin() && $query->is_main_query() && is_tax('product_cat')) {
        $term = get_queried_object();
        if ($term && $term->slug === 'all-products') {
            $query->set('post_type', 'product');
            $query->set('tax_query', array()); // xóa điều kiện category
        }
    }
});


function enqueue_clear_cart_script()
{
    wp_enqueue_script(
        'clear-cart-js',
        get_template_directory_uri() . '/assets/js/clear-cart.js',
        array('jquery'),
        null,
        true
    );

    // Cung cấp ajax_url cho JS
    wp_localize_script('clear-cart-js', 'wc_add_to_cart_params', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_clear_cart_script');

// --- Tắt Cart Fragments để tránh overwrite ---
add_filter('woocommerce_cart_fragment_refresh', '__return_false');

// --- Clear toàn bộ giỏ hàng ---
add_action('wp_ajax_clear_cart', 'custom_clear_cart');
add_action('wp_ajax_nopriv_clear_cart', 'custom_clear_cart');
function custom_clear_cart()
{
    if (WC()->cart) {
        WC()->cart->empty_cart();
        wp_send_json_success(array(
            'cart_subtotal' => WC()->cart->get_cart_subtotal(),
            'cart_count' => WC()->cart->get_cart_contents_count()
        ));
    } else {
        wp_send_json_error(array('message' => 'Cart not found'));
    }
}

// ---  Xóa từng sản phẩm ---
add_action('wp_ajax_woocommerce_remove_cart_item', 'custom_remove_cart_item');
add_action('wp_ajax_nopriv_woocommerce_remove_cart_item', 'custom_remove_cart_item');
function custom_remove_cart_item()
{
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    WC()->cart->remove_cart_item($cart_item_key);

    wp_send_json(array(
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'cart_count' => WC()->cart->get_cart_contents_count()
    ));
}

// ---  Cập nhật số lượng ---
add_action('wp_ajax_update_cart_quantity', 'custom_update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'custom_update_cart_quantity');
function custom_update_cart_quantity()
{
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity = intval($_POST['quantity']);

    WC()->cart->set_quantity($cart_item_key, $quantity);

    $cart_item = WC()->cart->get_cart()[$cart_item_key];
    $product_price_html = WC()->cart->get_product_price($cart_item['data']);

    wp_send_json(array(
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'product_price' => $product_price_html,
        'cart_count' => WC()->cart->get_cart_contents_count()
    ));
}

add_filter('woocommerce_logout_redirect', function ($redirect) {
    return home_url();
});

//checkout
function enqueue_wc_custom_checkout_js()
{
    if (is_checkout()) {
        wp_enqueue_script('wc-checkout');
        wp_enqueue_script('wc-country-select');
        wp_enqueue_script('wc-address-i18n');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_wc_custom_checkout_js');


//Coupon
add_action('wp_ajax_apply_custom_coupon', 'apply_custom_coupon');
add_action('wp_ajax_nopriv_apply_custom_coupon', 'apply_custom_coupon');


function apply_custom_coupon()
{
    if (!isset($_POST['coupon_code']))
        wp_send_json_error(['message' => 'No coupon code provided']);

    $coupon_code = sanitize_text_field($_POST['coupon_code']);
    if (WC()->cart->has_discount($coupon_code)) {
        wp_send_json_error(['message' => 'Coupon already applied']);
    }

    $applied = WC()->cart->apply_coupon($coupon_code);

    if ($applied) {
        WC()->cart->calculate_totals();
        wp_send_json_success(['message' => 'Coupon applied successfully!']);
    } else {
        wp_send_json_error(['message' => 'Invalid coupon']);
    }
}

function enqueue_custom_checkout_js()
{
    if (is_checkout()) {
        wp_enqueue_script(
            'custom-checkout-js',
            get_template_directory_uri() . '/assets/js/coupon.js',
            array('jquery'),
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_checkout_js');


//cancel order
add_action('wp_ajax_cancel_customer_order', 'cancel_customer_order');
add_action('wp_ajax_nopriv_cancel_customer_order', 'cancel_customer_order');

function cancel_customer_order() {
    check_ajax_referer('cancel_order_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in.']);
    }

    $order_id = intval($_POST['order_id']);
    $order = wc_get_order($order_id);

    if (!$order) {
        wp_send_json_error(['message' => 'Order not found.']);
    }

    if ($order->get_user_id() != get_current_user_id()) {
        wp_send_json_error(['message' => 'Permission denied.']);
    }

    if (!in_array($order->get_status(), ['pending','on-hold','failed'])) {
        wp_send_json_error(['message' => 'This order cannot be cancelled.']);
    }

    $order->update_status('cancelled', 'Order cancelled by customer.');

    wp_send_json_success([
        'message' => 'Order cancelled successfully.'
    ]);
}

add_filter('woocommerce_my_account_my_orders_actions', 'add_cancel_order_action', 10, 2);
function add_cancel_order_action($actions, $order) {
    if (in_array($order->get_status(), ['pending','on-hold','failed'])) {
        $actions['cancel'] = [
            'url'  => '#', 
            'name' => '<span class="cancel-order-btn" data-order-id="' . $order->get_id() . '"><i class="ri-close-circle-line"></i></span>',
        ];
    }
    return $actions;
}


function mytheme_enqueue_cancel_order_js() {
    if( is_account_page() ) { 
        wp_enqueue_script(
            'cancel-order-js',
            get_template_directory_uri() . '/assets/js/cancel-order.js',
            array('jquery'),
            null,
            true
        );

        wp_localize_script('cancel-order-js', 'cancelOrderData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cancel_order_nonce'),
            'confirm_message' => 'Are you sure you want to cancel this order?',
            'error_message' => 'Something went wrong, please try again.'
        ));
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_cancel_order_js');

// Enable product reviews
add_filter('woocommerce_product_review_comment_form_args', 'fs_custom_review_form');
function fs_custom_review_form($comment_form) {
    return $comment_form;
}

// Enqueue review scripts
function fs_enqueue_review_scripts() {
    if (is_product() || is_account_page()) {
        wp_enqueue_script(
            'product-reviews-js',
            get_template_directory_uri() . '/assets/js/product-reviews.js',
            array('jquery'),
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'fs_enqueue_review_scripts');

// Tùy chỉnh metadata cho social sharing
// add_action('wp_head', 'custom_og_product_info');
// function custom_og_product_info() {
//     if (is_product()) {
//         global $product;
//         $price = $product->get_price_html();
//         $title = $product->get_name();
//         $url = get_permalink($product->get_id());
//         echo '<meta property="og:title" content="'.esc_attr($title).' - Price: '.$price.'" />';
//         echo '<meta property="og:description" content="Check out this product!"/>';
//         echo '<meta property="og:url" content="'.esc_url($url).'" />';
//     }
// }

// ============================================
// SECTION BUILDER BLUEPRINT - Định nghĩa block
// ============================================

function fs_get_builder_block_definitions() {
    $common_fields = array(
        'spacing_top' => array(
            'type' => 'select',
            'label' => __('Khoảng cách phía trên', 'fashion-store'),
            'default' => 'default',
            'choices' => array(
                'none' => __('None', 'fashion-store'),
                'sm' => __('Small', 'fashion-store'),
                'default' => __('Default', 'fashion-store'),
                'lg' => __('Large', 'fashion-store'),
            ),
        ),
        'spacing_bottom' => array(
            'type' => 'select',
            'label' => __('Khoảng cách phía dưới', 'fashion-store'),
            'default' => 'default',
            'choices' => array(
                'none' => __('None', 'fashion-store'),
                'sm' => __('Small', 'fashion-store'),
                'default' => __('Default', 'fashion-store'),
                'lg' => __('Large', 'fashion-store'),
            ),
        ),
        'background' => array(
            'type' => 'background',
            'label' => __('Background', 'fashion-store'),
            'default' => array(
                'color' => '#ffffff',
                'image' => '',
                'overlay' => '',
            ),
        ),
        'container_width' => array(
            'type' => 'select',
            'label' => __('Bề rộng nội dung', 'fashion-store'),
            'default' => 'container',
            'choices' => array(
                'container' => __('Boxed', 'fashion-store'),
                'container-fluid' => __('Full width', 'fashion-store'),
                'full-bleed' => __('Edge to edge', 'fashion-store'),
            ),
        ),
    );

    $blocks = array(
        'hero_slider' => array(
            'label' => __('Hero Slider', 'fashion-store'),
            'description' => __('Slider lớn với text và CTA', 'fashion-store'),
            'fields' => array(
                'autoplay' => array(
                    'type' => 'switch',
                    'label' => __('Tự động chạy', 'fashion-store'),
                    'default' => true,
                ),
                'autoplay_speed' => array(
                    'type' => 'number',
                    'label' => __('Tốc độ chuyển (ms)', 'fashion-store'),
                    'default' => 5000,
                    'min' => 1000,
                    'max' => 15000,
                    'step' => 500,
                ),
                'slides_to_show' => array(
                    'type' => 'select',
                    'label' => __('Số slide hiển thị', 'fashion-store'),
                    'default' => 1,
                    'choices' => array(1 => '1', 2 => '2'),
                ),
                'slides' => array(
                    'type' => 'repeater',
                    'label' => __('Slides', 'fashion-store'),
                    'min' => 1,
                    'max' => 10,
                    'fields' => array(
                        'title' => array('type' => 'text', 'label' => __('Tiêu đề', 'fashion-store')),
                        'subtitle' => array('type' => 'text', 'label' => __('Phụ đề', 'fashion-store')),
                        'description' => array('type' => 'textarea', 'label' => __('Mô tả', 'fashion-store')),
                        'primary_button' => array(
                            'type' => 'link',
                            'label' => __('Primary CTA', 'fashion-store'),
                        ),
                        'secondary_button' => array(
                            'type' => 'link',
                            'label' => __('Secondary CTA', 'fashion-store'),
                        ),
                        'desktop_image' => array('type' => 'image', 'label' => __('Hình desktop', 'fashion-store')),
                        'mobile_image' => array('type' => 'image', 'label' => __('Hình mobile', 'fashion-store')),
                        'alignment' => array(
                            'type' => 'select',
                            'label' => __('Canh nội dung', 'fashion-store'),
                            'default' => 'left',
                            'choices' => array(
                                'left' => __('Trái', 'fashion-store'),
                                'center' => __('Giữa', 'fashion-store'),
                                'right' => __('Phải', 'fashion-store'),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'banner_grid' => array(
            'label' => __('Banner Grid', 'fashion-store'),
            'description' => __('Lưới banner có link', 'fashion-store'),
            'fields' => array(
                'columns' => array(
                    'type' => 'select',
                    'label' => __('Số cột', 'fashion-store'),
                    'default' => 2,
                    'choices' => array(1 => '1', 2 => '2', 3 => '3', 4 => '4'),
                ),
                'banners' => array(
                    'type' => 'repeater',
                    'label' => __('Danh sách banner', 'fashion-store'),
                    'min' => 1,
                    'max' => 8,
                    'fields' => array(
                        'image' => array('type' => 'image', 'label' => __('Hình ảnh', 'fashion-store')),
                        'title' => array('type' => 'text', 'label' => __('Tiêu đề', 'fashion-store')),
                        'subtitle' => array('type' => 'text', 'label' => __('Phụ đề', 'fashion-store')),
                        'link' => array('type' => 'link', 'label' => __('Link', 'fashion-store')),
                        'badge_text' => array('type' => 'text', 'label' => __('Badge', 'fashion-store')),
                    ),
                ),
            ),
        ),
        'product_grid' => array(
            'label' => __('Product Grid', 'fashion-store'),
            'description' => __('Hiển thị sản phẩm WooCommerce', 'fashion-store'),
            'fields' => array(
                'layout' => array(
                    'type' => 'select',
                    'label' => __('Layout', 'fashion-store'),
                    'default' => 'grid',
                    'choices' => array(
                        'grid' => __('Grid', 'fashion-store'),
                        'carousel' => __('Carousel', 'fashion-store'),
                    ),
                ),
                'columns' => array(
                    'type' => 'select',
                    'label' => __('Số cột desktop', 'fashion-store'),
                    'default' => 4,
                    'choices' => array(2 => '2', 3 => '3', 4 => '4', 5 => '5'),
                ),
                'query_type' => array(
                    'type' => 'select',
                    'label' => __('Nguồn sản phẩm', 'fashion-store'),
                    'default' => 'featured',
                    'choices' => array(
                        'featured' => __('Sản phẩm nổi bật', 'fashion-store'),
                        'recent' => __('Sản phẩm mới', 'fashion-store'),
                        'sale' => __('Đang giảm giá', 'fashion-store'),
                        'best_selling' => __('Bán chạy', 'fashion-store'),
                        'category' => __('Theo danh mục', 'fashion-store'),
                        'manual' => __('Chọn thủ công', 'fashion-store'),
                    ),
                ),
                'category' => array(
                    'type' => 'taxonomy',
                    'taxonomy' => 'product_cat',
                    'label' => __('Danh mục', 'fashion-store'),
                    'condition' => array('query_type' => 'category'),
                ),
                'product_ids' => array(
                    'type' => 'product_selector',
                    'label' => __('Chọn sản phẩm', 'fashion-store'),
                    'condition' => array('query_type' => 'manual'),
                ),
                'limit' => array(
                    'type' => 'number',
                    'label' => __('Số sản phẩm', 'fashion-store'),
                    'default' => 8,
                    'min' => 1,
                    'max' => 24,
                ),
                'show_rating' => array(
                    'type' => 'switch',
                    'label' => __('Hiển thị rating', 'fashion-store'),
                    'default' => true,
                ),
                'show_add_to_cart' => array(
                    'type' => 'switch',
                    'label' => __('Hiển thị nút Add to Cart', 'fashion-store'),
                    'default' => true,
                ),
            ),
        ),
        'blog_list' => array(
            'label' => __('Blog Posts', 'fashion-store'),
            'description' => __('Danh sách bài viết mới nhất', 'fashion-store'),
            'fields' => array(
                'style' => array(
                    'type' => 'select',
                    'label' => __('Style', 'fashion-store'),
                    'default' => 'cards',
                    'choices' => array(
                        'cards' => __('Cards', 'fashion-store'),
                        'list' => __('List', 'fashion-store'),
                        'carousel' => __('Carousel', 'fashion-store'),
                    ),
                ),
                'limit' => array(
                    'type' => 'number',
                    'label' => __('Số bài viết', 'fashion-store'),
                    'default' => 3,
                    'min' => 1,
                    'max' => 10,
                ),
                'category' => array(
                    'type' => 'taxonomy',
                    'taxonomy' => 'category',
                    'label' => __('Danh mục', 'fashion-store'),
                    'allow_multiple' => true,
                ),
                'show_excerpt' => array(
                    'type' => 'switch',
                    'label' => __('Hiển thị excerpt', 'fashion-store'),
                    'default' => true,
                ),
            ),
        ),
        'testimonial_slider' => array(
            'label' => __('Testimonials', 'fashion-store'),
            'description' => __('Slider cảm nhận khách hàng', 'fashion-store'),
            'fields' => array(
                'autoplay' => array(
                    'type' => 'switch',
                    'label' => __('Tự động chạy', 'fashion-store'),
                    'default' => true,
                ),
                'items' => array(
                    'type' => 'repeater',
                    'label' => __('Testimonials', 'fashion-store'),
                    'fields' => array(
                        'avatar' => array('type' => 'image', 'label' => __('Avatar', 'fashion-store')),
                        'name' => array('type' => 'text', 'label' => __('Tên khách hàng', 'fashion-store')),
                        'position' => array('type' => 'text', 'label' => __('Chức vụ', 'fashion-store')),
                        'rating' => array(
                            'type' => 'number',
                            'label' => __('Rating (0-5)', 'fashion-store'),
                            'default' => 5,
                            'min' => 0,
                            'max' => 5,
                        ),
                        'content' => array('type' => 'textarea', 'label' => __('Nội dung', 'fashion-store')),
                    ),
                ),
            ),
        ),
        'html_block' => array(
            'label' => __('HTML/Code Block', 'fashion-store'),
            'description' => __('Nhúng HTML/CSS/JS tùy chỉnh', 'fashion-store'),
            'fields' => array(
                'title' => array('type' => 'text', 'label' => __('Tiêu đề nội bộ', 'fashion-store')),
                'html' => array(
                    'type' => 'code',
                    'language' => 'html',
                    'label' => __('HTML Content', 'fashion-store'),
                ),
            ),
        ),
        'shortcode_block' => array(
            'label' => __('Shortcode', 'fashion-store'),
            'description' => __('Chèn shortcode bất kỳ', 'fashion-store'),
            'fields' => array(
                'shortcode' => array('type' => 'text', 'label' => __('Shortcode', 'fashion-store')),
            ),
        ),
        'newsletter' => array(
            'label' => __('Newsletter / Form', 'fashion-store'),
            'description' => __('Form đăng ký, hỗ trợ shortcode form', 'fashion-store'),
            'fields' => array(
                'heading' => array('type' => 'text', 'label' => __('Heading', 'fashion-store')),
                'description' => array('type' => 'textarea', 'label' => __('Description', 'fashion-store')),
                'form_shortcode' => array('type' => 'text', 'label' => __('Shortcode form', 'fashion-store')),
                'image' => array('type' => 'image', 'label' => __('Hình minh hoạ', 'fashion-store')),
                'layout' => array(
                    'type' => 'select',
                    'label' => __('Layout', 'fashion-store'),
                    'default' => 'left-form',
                    'choices' => array(
                        'left-form' => __('Hình trái, form phải', 'fashion-store'),
                        'right-form' => __('Form trái, hình phải', 'fashion-store'),
                        'stacked' => __('Xếp dọc', 'fashion-store'),
                    ),
                ),
            ),
        ),
        'feature_cards' => array(
            'label' => __('Feature Cards', 'fashion-store'),
            'description' => __('Các thẻ giới thiệu dịch vụ/lợi ích', 'fashion-store'),
            'fields' => array(
                'columns' => array(
                    'type' => 'select',
                    'label' => __('Số cột', 'fashion-store'),
                    'default' => 3,
                    'choices' => array(2 => '2', 3 => '3', 4 => '4'),
                ),
                'items' => array(
                    'type' => 'repeater',
                    'label' => __('Feature Items', 'fashion-store'),
                    'fields' => array(
                        'icon_type' => array(
                            'type' => 'select',
                            'label' => __('Loại icon', 'fashion-store'),
                            'default' => 'icon',
                            'choices' => array(
                                'icon' => __('Icon class', 'fashion-store'),
                                'image' => __('Hình ảnh', 'fashion-store'),
                            ),
                        ),
                        'icon' => array('type' => 'text', 'label' => __('Icon class', 'fashion-store')),
                        'image' => array('type' => 'image', 'label' => __('Image', 'fashion-store')),
                        'title' => array('type' => 'text', 'label' => __('Tiêu đề', 'fashion-store')),
                        'description' => array('type' => 'textarea', 'label' => __('Mô tả', 'fashion-store')),
                        'link' => array('type' => 'link', 'label' => __('Link', 'fashion-store')),
                    ),
                ),
            ),
        ),
        'cta_banner' => array(
            'label' => __('Call to Action Banner', 'fashion-store'),
            'description' => __('Banner CTA toàn chiều ngang', 'fashion-store'),
            'fields' => array(
                'eyebrow' => array('type' => 'text', 'label' => __('Eyebrow text', 'fashion-store')),
                'title' => array('type' => 'text', 'label' => __('Tiêu đề', 'fashion-store')),
                'description' => array('type' => 'textarea', 'label' => __('Mô tả', 'fashion-store')),
                'button_primary' => array('type' => 'link', 'label' => __('Primary CTA', 'fashion-store')),
                'button_secondary' => array('type' => 'link', 'label' => __('Secondary CTA', 'fashion-store')),
                'alignment' => array(
                    'type' => 'select',
                    'label' => __('Căn lề nội dung', 'fashion-store'),
                    'choices' => array(
                        'left' => __('Trái', 'fashion-store'),
                        'center' => __('Giữa', 'fashion-store'),
                        'right' => __('Phải', 'fashion-store'),
                    ),
                    'default' => 'center',
                ),
            ),
        ),
    );

    foreach ($blocks as $key => $block) {
        $blocks[$key]['fields'] = array_merge($block['fields'], $common_fields);
    }

    return apply_filters('fs_builder_block_definitions', $blocks);
}

// ============================================
// SECTION POST TYPE & META BOXES (Step 2)
// ============================================

function fs_register_section_post_type() {
    $labels = array(
        'name' => __('FS Sections', 'fashion-store'),
        'singular_name' => __('FS Section', 'fashion-store'),
        'add_new' => __('Add Section', 'fashion-store'),
        'add_new_item' => __('Add New Section', 'fashion-store'),
        'edit_item' => __('Edit Section', 'fashion-store'),
        'new_item' => __('New Section', 'fashion-store'),
        'view_item' => __('View Section', 'fashion-store'),
        'search_items' => __('Search Sections', 'fashion-store'),
        'not_found' => __('No sections found', 'fashion-store'),
        'not_found_in_trash' => __('No sections found in Trash', 'fashion-store'),
        'all_items' => __('All Sections', 'fashion-store'),
        'menu_name' => __('Sections', 'fashion-store'),
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-layout',
        'supports' => array('title'),
        'has_archive' => false,
        'rewrite' => false,
        'capability_type' => 'post',
        'map_meta_cap' => true,
    );

    register_post_type('fs_section', $args);
}
add_action('init', 'fs_register_section_post_type');

function fs_register_section_taxonomy() {
    $labels = array(
        'name' => __('Section Groups', 'fashion-store'),
        'singular_name' => __('Section Group', 'fashion-store'),
        'search_items' => __('Search Section Groups', 'fashion-store'),
        'all_items' => __('All Section Groups', 'fashion-store'),
        'edit_item' => __('Edit Section Group', 'fashion-store'),
        'update_item' => __('Update Section Group', 'fashion-store'),
        'add_new_item' => __('Add New Section Group', 'fashion-store'),
        'new_item_name' => __('New Section Group', 'fashion-store'),
        'menu_name' => __('Section Groups', 'fashion-store'),
    );

    register_taxonomy('fs_section_group', 'fs_section', array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => false,
    ));
}
add_action('init', 'fs_register_section_taxonomy');

function fs_add_section_meta_boxes() {
    add_meta_box(
        'fs_section_builder',
        __('Section Blocks', 'fashion-store'),
        'fs_render_section_builder_metabox',
        'fs_section',
        'normal',
        'high'
    );

    add_meta_box(
        'fs_section_options',
        __('Section Options', 'fashion-store'),
        'fs_render_section_options_metabox',
        'fs_section',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'fs_add_section_meta_boxes');

function fs_render_section_builder_metabox($post) {
    wp_nonce_field('fs_save_section', 'fs_section_nonce');

    $stored_blocks = get_post_meta($post->ID, '_fs_section_blocks', true);
    if (empty($stored_blocks)) {
        $stored_blocks = '[]';
    }

    $definitions = fs_get_builder_block_definitions();
    foreach ($definitions as $block_key => $block) {
        foreach ($block['fields'] as $field_key => $field) {
            if (isset($field['type']) && $field['type'] === 'taxonomy' && isset($field['taxonomy'])) {
                $terms = get_terms(array(
                    'taxonomy' => $field['taxonomy'],
                    'hide_empty' => false,
                ));
                if (!is_wp_error($terms)) {
                    $choices = array();
                    foreach ($terms as $term) {
                        $choices[] = array(
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'slug' => $term->slug,
                        );
                    }
                    $definitions[$block_key]['fields'][$field_key]['choices'] = $choices;
                }
            }
        }
    }

    $definitions_attr = esc_attr(wp_json_encode($definitions));
    ?>
    <div class="fs-section-builder-wrapper" data-block-definitions="<?php echo $definitions_attr; ?>">
        <div class="fs-builder-toolbar">
            <div class="fs-builder-toolbar-left">
                <label for="fs-builder-search" class="screen-reader-text"><?php esc_html_e('Search blocks', 'fashion-store'); ?></label>
                <input type="search" id="fs-builder-search" class="fs-builder-search" placeholder="<?php esc_attr_e('Search blocks…', 'fashion-store'); ?>">
            </div>
            <div class="fs-builder-toolbar-right">
                <button type="button" class="button fs-toggle-json"><?php esc_html_e('Toggle JSON', 'fashion-store'); ?></button>
                <button type="button" class="button button-primary fs-apply-json" disabled><?php esc_html_e('Apply JSON', 'fashion-store'); ?></button>
            </div>
        </div>
        <div class="fs-builder-body">
            <div class="fs-panel fs-panel-library">
                <div class="fs-panel-title"><?php esc_html_e('Block Library', 'fashion-store'); ?></div>
                <div class="fs-block-library" id="fs-block-library"></div>
            </div>
            <div class="fs-panel fs-panel-canvas">
                <div class="fs-panel-title">
                    <?php esc_html_e('Section Canvas', 'fashion-store'); ?>
                    <span class="fs-panel-subtitle"><?php esc_html_e('Drag blocks to reorder', 'fashion-store'); ?></span>
                </div>
                <ul class="fs-canvas-list" id="fs-section-canvas"></ul>
                <div class="fs-canvas-empty-state">
                    <p><?php esc_html_e('No blocks yet. Click a block in the library to add it here.', 'fashion-store'); ?></p>
                </div>
            </div>
            <div class="fs-panel fs-panel-settings">
                <div class="fs-panel-title"><?php esc_html_e('Block Settings', 'fashion-store'); ?></div>
                <div class="fs-panel-content" id="fs-block-settings">
                    <p class="fs-settings-empty"><?php esc_html_e('Select a block to edit its settings.', 'fashion-store'); ?></p>
                </div>
            </div>
        </div>
        <div class="fs-builder-json fs-builder-json-hidden">
            <label for="fs-section-blocks-raw"><strong><?php esc_html_e('Raw JSON (fallback)', 'fashion-store'); ?></strong></label>
            <textarea id="fs-section-blocks-raw" name="fs_section_blocks_raw" rows="10"><?php echo esc_textarea($stored_blocks); ?></textarea>
            <p class="description"><?php esc_html_e('Use this if you need to import/export quickly. Click "Apply JSON" after editing.', 'fashion-store'); ?></p>
        </div>
        <input type="hidden" id="fs-section-blocks" name="fs_section_blocks" value="<?php echo esc_attr($stored_blocks); ?>" />
    </div>
    <?php
}

function fs_render_section_options_metabox($post) {
    $options = get_post_meta($post->ID, '_fs_section_options', true);
    $options = wp_parse_args($options, array(
        'status' => 'draft',
        'notes' => '',
        'custom_class' => '',
        'section_id' => '',
    ));

    ?>
    <p>
        <label for="fs_section_status"><strong><?php esc_html_e('Usage status', 'fashion-store'); ?></strong></label>
        <select name="fs_section_options[status]" id="fs_section_status" class="widefat">
            <option value="draft" <?php selected($options['status'], 'draft'); ?>><?php esc_html_e('Draft', 'fashion-store'); ?></option>
            <option value="ready" <?php selected($options['status'], 'ready'); ?>><?php esc_html_e('Ready', 'fashion-store'); ?></option>
            <option value="global" <?php selected($options['status'], 'global'); ?>><?php esc_html_e('Global Section', 'fashion-store'); ?></option>
        </select>
    </p>
    <p>
        <label for="fs_section_custom_class"><strong><?php esc_html_e('Custom CSS class', 'fashion-store'); ?></strong></label>
        <input type="text" name="fs_section_options[custom_class]" id="fs_section_custom_class" class="widefat" value="<?php echo esc_attr($options['custom_class']); ?>" />
    </p>
    <p>
        <label for="fs_section_id"><strong><?php esc_html_e('Custom Section ID', 'fashion-store'); ?></strong></label>
        <input type="text" name="fs_section_options[section_id]" id="fs_section_id" class="widefat" value="<?php echo esc_attr($options['section_id']); ?>" />
        <small><?php esc_html_e('Dùng cho anchor link hoặc styling cụ thể.', 'fashion-store'); ?></small>
    </p>
    <p>
        <label for="fs_section_notes"><strong><?php esc_html_e('Internal notes', 'fashion-store'); ?></strong></label>
        <textarea name="fs_section_options[notes]" id="fs_section_notes" class="widefat" rows="3"><?php echo esc_textarea($options['notes']); ?></textarea>
    </p>
    <p>
        <strong><?php esc_html_e('Shortcode', 'fashion-store'); ?>:</strong>
        <code><?php echo esc_html('[fs_section id="' . $post->ID . '"]'); ?></code>
    </p>
    <?php
}

function fs_save_section_meta($post_id) {
    if (!isset($_POST['fs_section_nonce']) || !wp_verify_nonce($_POST['fs_section_nonce'], 'fs_save_section')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && $_POST['post_type'] === 'fs_section') {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['fs_section_blocks_raw'])) {
            $raw = wp_unslash($_POST['fs_section_blocks_raw']);
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                update_post_meta($post_id, '_fs_section_blocks', wp_json_encode($decoded));
            } else {
                // Nếu JSON lỗi, lưu thô để không mất dữ liệu nhập tay
                update_post_meta($post_id, '_fs_section_blocks', $raw);
            }
        }

        if (isset($_POST['fs_section_options'])) {
            $options = wp_array_slice_assoc(
                array_map('sanitize_text_field', wp_unslash($_POST['fs_section_options'])),
                array('status', 'notes', 'custom_class', 'section_id')
            );
            update_post_meta($post_id, '_fs_section_options', $options);
        }
    }
}
add_action('save_post', 'fs_save_section_meta');

function fs_section_admin_assets($hook) {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'fs_section') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');

    wp_enqueue_style(
        'fs-section-builder-admin',
        get_template_directory_uri() . '/assets/css/section-builder-admin.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'fs-section-builder-admin',
        get_template_directory_uri() . '/assets/js/section-builder-admin.js',
        array('jquery', 'wp-util', 'jquery-ui-sortable', 'wp-color-picker'),
        '1.0.0',
        true
    );

    wp_localize_script('fs-section-builder-admin', 'fsSectionBuilder', array(
        'nonce' => wp_create_nonce('fs_section_builder'),
        'strings' => array(
            'delete_confirm' => __('Remove this block?', 'fashion-store'),
            'delete_repeater_confirm' => __('Remove this item?', 'fashion-store'),
            'empty_settings' => __('Select a block to edit its settings.', 'fashion-store'),
            'json_parse_error' => __('Invalid JSON. Please fix errors before applying.', 'fashion-store'),
        ),
    ));
}
add_action('admin_enqueue_scripts', 'fs_section_admin_assets');

function fs_section_columns($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Section Title', 'fashion-store'),
        'fs_group' => __('Group', 'fashion-store'),
        'fs_blocks' => __('Blocks', 'fashion-store'),
        'fs_status' => __('Status', 'fashion-store'),
        'date' => __('Date', 'fashion-store'),
    );
    return $columns;
}
add_filter('manage_fs_section_posts_columns', 'fs_section_columns');

function fs_section_custom_column($column, $post_id) {
    switch ($column) {
        case 'fs_group':
            $terms = get_the_term_list($post_id, 'fs_section_group', '', ', ');
            echo $terms ? wp_kses_post($terms) : '—';
            break;
        case 'fs_blocks':
            $blocks = get_post_meta($post_id, '_fs_section_blocks', true);
            $count = 0;
            if ($blocks) {
                $decoded = json_decode($blocks, true);
                if (is_array($decoded)) {
                    $count = count($decoded);
                }
            }
            printf(esc_html__('%d blocks', 'fashion-store'), intval($count));
            break;
        case 'fs_status':
            $options = get_post_meta($post_id, '_fs_section_options', true);
            $status = isset($options['status']) ? $options['status'] : 'draft';
            echo esc_html(ucfirst($status));
            break;
    }
}
add_action('manage_fs_section_posts_custom_column', 'fs_section_custom_column', 10, 2);

function fs_section_sortable_columns($columns) {
    $columns['fs_status'] = 'fs_status';
    return $columns;
}
add_filter('manage_edit-fs_section_sortable_columns', 'fs_section_sortable_columns');

function fs_section_pre_get_posts($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') === 'fs_section') {
        $orderby = $query->get('orderby');
        if ($orderby === 'fs_status') {
            $query->set('meta_key', '_fs_section_options');
            $query->set('orderby', 'meta_value');
        }
    }
}
add_action('pre_get_posts', 'fs_section_pre_get_posts');

// ============================================
// STEP 4 - Attach Sections to Pages/Posts
// ============================================

function fs_add_page_section_metabox() {
    $screens = apply_filters('fs_section_attach_screens', array('page', 'post'));
    foreach ($screens as $screen) {
        add_meta_box(
            'fs_page_sections',
            __('Attached Sections', 'fashion-store'),
            'fs_render_page_sections_metabox',
            $screen,
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'fs_add_page_section_metabox');

function fs_get_available_sections() {
    $query = new WP_Query(array(
        'post_type' => 'fs_section',
        'posts_per_page' => -1,
        'post_status' => array('publish'),
        'orderby' => 'title',
        'order' => 'ASC',
    ));

    $sections = array();
    if ($query->have_posts()) {
        foreach ($query->posts as $section) {
            $options = get_post_meta($section->ID, '_fs_section_options', true);
            $status = isset($options['status']) ? $options['status'] : 'draft';
            $sections[] = array(
                'id' => $section->ID,
                'title' => get_the_title($section),
                'status' => $status,
                'group' => wp_get_post_terms($section->ID, 'fs_section_group', array('fields' => 'names')),
            );
        }
    }
    return $sections;
}

function fs_render_page_sections_metabox($post) {
    wp_nonce_field('fs_save_page_sections', 'fs_page_sections_nonce');
    $attached = get_post_meta($post->ID, '_fs_attached_sections', true);
    if (!is_array($attached)) {
        $attached = array();
    }

    $sections = fs_get_available_sections();
    ?>
    <div class="fs-page-sections-wrapper" data-available='<?php echo esc_attr(wp_json_encode($sections)); ?>'>
        <div class="fs-page-sections-panels">
            <div class="fs-panel-available">
                <h4><?php esc_html_e('Available Sections', 'fashion-store'); ?></h4>
                <input type="search" id="fs-section-search" placeholder="<?php esc_attr_e('Search section…', 'fashion-store'); ?>" />
                <ul class="fs-section-list" id="fs-available-section-list"></ul>
            </div>
            <div class="fs-panel-selected">
                <h4><?php esc_html_e('Selected Sections', 'fashion-store'); ?></h4>
                <p class="description"><?php esc_html_e('Drag to reorder. These sections will render for this page.', 'fashion-store'); ?></p>
                <ul class="fs-section-list fs-selected-list" id="fs-selected-section-list"></ul>
                <div class="fs-selected-empty">
                    <p><?php esc_html_e('No sections selected yet.', 'fashion-store'); ?></p>
                </div>
            </div>
        </div>
        <input type="hidden" id="fs-attached-sections" name="fs_attached_sections" value="<?php echo esc_attr(wp_json_encode($attached)); ?>" />
    </div>
    <?php
}

function fs_save_page_sections($post_id) {
    if (!isset($_POST['fs_page_sections_nonce']) || !wp_verify_nonce($_POST['fs_page_sections_nonce'], 'fs_save_page_sections')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $post_type = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : '';
    $screens = apply_filters('fs_section_attach_screens', array('page', 'post'));
    if (!in_array($post_type, $screens, true)) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['fs_attached_sections'])) {
        $raw = wp_unslash($_POST['fs_attached_sections']);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $clean = array();
            foreach ($decoded as $item) {
                if (!isset($item['id'])) {
                    continue;
                }
                $clean[] = array(
                    'id' => intval($item['id']),
                    'visibility' => isset($item['visibility']) ? sanitize_text_field($item['visibility']) : 'all',
                    'notes' => isset($item['notes']) ? sanitize_text_field($item['notes']) : '',
                );
            }
            update_post_meta($post_id, '_fs_attached_sections', $clean);
        } else {
            delete_post_meta($post_id, '_fs_attached_sections');
        }
    } else {
        delete_post_meta($post_id, '_fs_attached_sections');
    }
}
add_action('save_post', 'fs_save_page_sections');

function fs_sections_attach_admin_assets($hook) {
    $screen = get_current_screen();
    if (!$screen) {
        return;
    }
    $screens = apply_filters('fs_section_attach_screens', array('page', 'post'));
    if (!in_array($screen->post_type, $screens, true)) {
        return;
    }

    wp_enqueue_style(
        'fs-page-section-admin',
        get_template_directory_uri() . '/assets/css/page-section-admin.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'fs-page-section-admin',
        get_template_directory_uri() . '/assets/js/page-section-admin.js',
        array('jquery', 'jquery-ui-sortable'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'fs_sections_attach_admin_assets');

// ============================================
// STEP 5 - Frontend rendering
// ============================================

function fs_get_attached_sections($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    if (!$post_id) {
        return array();
    }
    $attached = get_post_meta($post_id, '_fs_attached_sections', true);
    return is_array($attached) ? $attached : array();
}

function fs_render_page_sections($post_id = null) {
    if (is_admin()) {
        return;
    }
    $sections = fs_get_attached_sections($post_id);
    if (empty($sections)) {
        return;
    }
    foreach ($sections as $section) {
        if (!isset($section['id'])) {
            continue;
        }
        fs_render_section_by_id(intval($section['id']));
    }
}

function fs_render_section_by_id($section_id) {
    $json = get_post_meta($section_id, '_fs_section_blocks', true);
    if (!$json) {
        return;
    }
    $blocks = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($blocks) || empty($blocks)) {
        return;
    }
    $options = get_post_meta($section_id, '_fs_section_options', true);
    $wrapper_classes = array('fs-section');
    if (!empty($options['custom_class'])) {
        $wrapper_classes[] = sanitize_html_class($options['custom_class']);
    }
    $wrapper_id = '';
    if (!empty($options['section_id'])) {
        $wrapper_id = sanitize_title($options['section_id']);
    }
    echo '<section class="' . esc_attr(implode(' ', $wrapper_classes)) . '" ' . ($wrapper_id ? 'id="' . esc_attr($wrapper_id) . '"' : '') . '>';
    foreach ($blocks as $block) {
        fs_render_section_block($block);
    }
    echo '</section>';
}

function fs_get_background_style($settings) {
    $style = array();
    if (empty($settings['background'])) {
        return '';
    }
    $bg = $settings['background'];
    if (!empty($bg['color'])) {
        $style[] = 'background-color:' . sanitize_hex_color($bg['color']);
    }
    if (!empty($bg['image'])) {
        $image = $bg['image'];
        $url = '';
        if (is_array($image)) {
            if (!empty($image['url'])) {
                $url = esc_url($image['url']);
            } elseif (!empty($image['id'])) {
                $url = wp_get_attachment_image_url(intval($image['id']), 'full');
            }
        } elseif (is_string($image)) {
            $url = esc_url($image);
        }
        if ($url) {
            $style[] = 'background-image:url(' . $url . ')';
            $style[] = 'background-size:cover';
            $style[] = 'background-position:center';
        }
    }
    return implode(';', $style);
}

function fs_render_section_block($block) {
    if (!isset($block['type'])) {
        return;
    }
    $settings = isset($block['settings']) ? $block['settings'] : array();
    $type = sanitize_key($block['type']);
    $wrapper_classes = array('fs-block', 'fs-block-' . $type);
    $spacing_top = isset($settings['spacing_top']) ? $settings['spacing_top'] : 'default';
    $spacing_bottom = isset($settings['spacing_bottom']) ? $settings['spacing_bottom'] : 'default';
    $wrapper_classes[] = 'fs-spacing-top-' . sanitize_html_class($spacing_top);
    $wrapper_classes[] = 'fs-spacing-bottom-' . sanitize_html_class($spacing_bottom);
    $style_attr = fs_get_background_style($settings);

    $container = isset($settings['container_width']) ? $settings['container_width'] : 'container';
    $has_inner = $container !== 'full-bleed';
    $inner_class = 'container';
    if ($container === 'container-fluid') {
        $inner_class = 'container-fluid';
    } elseif ($container === 'full-bleed') {
        $inner_class = '';
    }

    echo '<div class="' . esc_attr(implode(' ', $wrapper_classes)) . '"' . ($style_attr ? ' style="' . esc_attr($style_attr) . '"' : '') . '>';
    if ($has_inner) {
        echo '<div class="' . esc_attr($inner_class) . '">';
    }
    fs_render_block_content($type, $settings);
    if ($has_inner) {
        echo '</div>';
    }
    echo '</div>';
}

function fs_render_block_content($type, $settings) {
    switch ($type) {
        case 'hero_slider':
            fs_render_block_hero_slider($settings);
            break;
        case 'banner_grid':
            fs_render_block_banner_grid($settings);
            break;
        case 'product_grid':
            fs_render_block_product_grid($settings);
            break;
        case 'blog_list':
            fs_render_block_blog_list($settings);
            break;
        case 'testimonial_slider':
            fs_render_block_testimonial_slider($settings);
            break;
        case 'html_block':
            if (!empty($settings['html'])) {
                echo wp_kses_post($settings['html']);
            }
            break;
        case 'shortcode_block':
            if (!empty($settings['shortcode'])) {
                echo do_shortcode($settings['shortcode']);
            }
            break;
        case 'newsletter':
            fs_render_block_newsletter($settings);
            break;
        case 'feature_cards':
            fs_render_block_feature_cards($settings);
            break;
        case 'cta_banner':
            fs_render_block_cta_banner($settings);
            break;
        default:
            /**
             * Allow developers to render custom blocks.
             */
            do_action('fs_render_custom_block', $type, $settings);
            break;
    }
}

function fs_get_image_src($image, $size = 'full') {
    if (is_array($image)) {
        if (!empty($image['url'])) {
            return esc_url($image['url']);
        }
        if (!empty($image['id'])) {
            $url = wp_get_attachment_image_url(intval($image['id']), $size);
            if ($url) {
                return esc_url($url);
            }
        }
    } elseif (is_numeric($image)) {
        $url = wp_get_attachment_image_url(intval($image), $size);
        if ($url) {
            return esc_url($url);
        }
    } elseif (is_string($image)) {
        return esc_url($image);
    }
    return '';
}

function fs_render_block_hero_slider($settings) {
    $slides = isset($settings['slides']) ? $settings['slides'] : array();
    if (empty($slides)) {
        return;
    }
    $autoplay = !empty($settings['autoplay']) ? 'true' : 'false';
    $speed = isset($settings['autoplay_speed']) ? intval($settings['autoplay_speed']) : 5000;
    echo '<div class="fs-hero-slider" data-autoplay="' . esc_attr($autoplay) . '" data-speed="' . esc_attr($speed) . '">';
    foreach ($slides as $slide) {
        $image = fs_get_image_src(isset($slide['desktop_image']) ? $slide['desktop_image'] : '');
        $mobile_image = fs_get_image_src(isset($slide['mobile_image']) ? $slide['mobile_image'] : '');
        $alignment = isset($slide['alignment']) ? $slide['alignment'] : 'left';
        echo '<div class="fs-hero-slide" style="' . ($image ? 'background-image:url(' . esc_url($image) . ');' : '') . '">';
        echo '<div class="fs-hero-slide-inner fs-align-' . esc_attr($alignment) . '">';
        if (!empty($slide['subtitle'])) {
            echo '<p class="fs-hero-subtitle">' . esc_html($slide['subtitle']) . '</p>';
        }
        if (!empty($slide['title'])) {
            echo '<h2 class="fs-hero-title">' . esc_html($slide['title']) . '</h2>';
        }
        if (!empty($slide['description'])) {
            echo '<div class="fs-hero-description">' . wp_kses_post(wpautop($slide['description'])) . '</div>';
        }
        echo '<div class="fs-hero-buttons">';
        if (!empty($slide['primary_button']['url'])) {
            $btn = $slide['primary_button'];
            echo '<a class="btn btn-solid" href="' . esc_url($btn['url']) . '" target="' . esc_attr($btn['target']) . '">' . esc_html($btn['label']) . '</a>';
        }
        if (!empty($slide['secondary_button']['url'])) {
            $btn = $slide['secondary_button'];
            echo '<a class="btn btn-outline ms-2" href="' . esc_url($btn['url']) . '" target="' . esc_attr($btn['target']) . '">' . esc_html($btn['label']) . '</a>';
        }
        echo '</div>';
        echo '</div>';
        if ($mobile_image) {
            echo '<span class="fs-hero-mobile" data-src="' . esc_url($mobile_image) . '"></span>';
        }
        echo '</div>';
    }
    echo '</div>';
}

function fs_render_block_banner_grid($settings) {
    $banners = isset($settings['banners']) ? $settings['banners'] : array();
    if (empty($banners)) {
        return;
    }
    $columns = isset($settings['columns']) ? intval($settings['columns']) : 2;
    echo '<div class="row g-3 fs-banner-grid columns-' . esc_attr($columns) . '">';
    foreach ($banners as $banner) {
        $image = fs_get_image_src(isset($banner['image']) ? $banner['image'] : '');
        echo '<div class="col-lg-' . esc_attr(12 / max(1, $columns)) . ' col-md-6">';
        echo '<div class="fs-banner-card">';
        if (!empty($banner['link']['url'])) {
            echo '<a href="' . esc_url($banner['link']['url']) . '" target="' . esc_attr($banner['link']['target']) . '">';
        }
        if ($image) {
            echo '<img src="' . esc_url($image) . '" alt="' . esc_attr(isset($banner['title']) ? $banner['title'] : '') . '" class="img-fluid">';
        }
        if (!empty($banner['title']) || !empty($banner['subtitle'])) {
            echo '<div class="fs-banner-content">';
            if (!empty($banner['subtitle'])) {
                echo '<p class="fs-banner-subtitle">' . esc_html($banner['subtitle']) . '</p>';
            }
            if (!empty($banner['title'])) {
                echo '<h3 class="fs-banner-title">' . esc_html($banner['title']) . '</h3>';
            }
            if (!empty($banner['badge_text'])) {
                echo '<span class="fs-banner-badge">' . esc_html($banner['badge_text']) . '</span>';
            }
            echo '</div>';
        }
        if (!empty($banner['link']['url'])) {
            echo '</a>';
        }
        echo '</div></div>';
    }
    echo '</div>';
}

function fs_render_block_product_grid($settings) {
    if (!class_exists('WooCommerce')) {
        return;
    }
    $limit = isset($settings['limit']) ? intval($settings['limit']) : 8;
    if ($limit < 1) {
        $limit = 4;
    }
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    );
    $type = isset($settings['query_type']) ? $settings['query_type'] : 'featured';
    switch ($type) {
        case 'featured':
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_visibility',
                    'field' => 'name',
                    'terms' => 'featured',
                ),
            );
            break;
        case 'sale':
            $sale_ids = wc_get_product_ids_on_sale();
            $sale_ids = empty($sale_ids) ? array(0) : $sale_ids;
            $args['post__in'] = $sale_ids;
            break;
        case 'best_selling':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'category':
            if (!empty($settings['category'])) {
                $cats = (array) $settings['category'];
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => array_map('sanitize_title', $cats),
                    ),
                );
            }
            break;
        case 'manual':
            if (!empty($settings['product_ids'])) {
                $ids = array_filter(array_map('intval', explode(',', $settings['product_ids'])));
                if (!empty($ids)) {
                    $args['post__in'] = $ids;
                    $args['orderby'] = 'post__in';
                }
            }
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
    }
    $query = new WP_Query($args);
    if (!$query->have_posts()) {
        wp_reset_postdata();
        return;
    }
    $layout = isset($settings['layout']) ? $settings['layout'] : 'grid';
    $columns = isset($settings['columns']) ? intval($settings['columns']) : 4;
    $wrapper_class = 'fs-product-grid layout-' . esc_attr($layout);
    echo '<div class="' . $wrapper_class . '" data-columns="' . esc_attr($columns) . '">';
    echo '<div class="row g-3">';
    while ($query->have_posts()) {
        $query->the_post();
        global $product;
        echo '<div class="col-lg-' . esc_attr(12 / max(1, $columns)) . ' col-md-4 col-6">';
        wc_get_template_part('content', 'product');
        echo '</div>';
    }
    echo '</div></div>';
    wp_reset_postdata();
}

function fs_render_block_blog_list($settings) {
    $limit = isset($settings['limit']) ? intval($settings['limit']) : 3;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    );
    if (!empty($settings['category'])) {
        $cats = (array) $settings['category'];
        $args['category_name'] = implode(',', array_map('sanitize_title', $cats));
    }
    $query = new WP_Query($args);
    if (!$query->have_posts()) {
        wp_reset_postdata();
        return;
    }
    echo '<div class="fs-blog-list">';
    while ($query->have_posts()) {
        $query->the_post();
        echo '<article class="fs-blog-card">';
        if (has_post_thumbnail()) {
            echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</a>';
        }
        echo '<div class="fs-blog-card-body">';
        echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
        if (!empty($settings['show_excerpt'])) {
            echo '<p>' . esc_html(wp_trim_words(get_the_excerpt(), 20)) . '</p>';
        }
        echo '<span class="fs-blog-date">' . esc_html(get_the_date()) . '</span>';
        echo '</div>';
        echo '</article>';
    }
    echo '</div>';
    wp_reset_postdata();
}

function fs_render_block_testimonial_slider($settings) {
    $items = isset($settings['items']) ? $settings['items'] : array();
    if (empty($items)) {
        return;
    }
    $autoplay = !empty($settings['autoplay']) ? 'true' : 'false';
    echo '<div class="fs-testimonial-slider" data-autoplay="' . esc_attr($autoplay) . '">';
    foreach ($items as $item) {
        echo '<div class="fs-testimonial-card">';
        if (!empty($item['avatar'])) {
            $avatar = fs_get_image_src($item['avatar'], 'thumbnail');
            if ($avatar) {
                echo '<div class="fs-testimonial-avatar"><img src="' . esc_url($avatar) . '" alt="' . esc_attr($item['name']) . '"></div>';
            }
        }
        if (!empty($item['content'])) {
            echo '<div class="fs-testimonial-content">' . wp_kses_post(wpautop($item['content'])) . '</div>';
        }
        echo '<div class="fs-testimonial-meta">';
        if (!empty($item['name'])) {
            echo '<strong>' . esc_html($item['name']) . '</strong>';
        }
        if (!empty($item['position'])) {
            echo '<span class="fs-testimonial-position">' . esc_html($item['position']) . '</span>';
        }
        if (isset($item['rating'])) {
            $rating = max(0, min(5, intval($item['rating'])));
            echo '<div class="fs-testimonial-rating">';
            for ($i = 0; $i < 5; $i++) {
                echo '<span class="dashicons ' . ($i < $rating ? 'dashicons-star-filled' : 'dashicons-star-empty') . '"></span>';
            }
            echo '</div>';
        }
        echo '</div></div>';
    }
    echo '</div>';
}

function fs_render_block_newsletter($settings) {
    echo '<div class="fs-newsletter-block">';
    if (!empty($settings['image'])) {
        $image = fs_get_image_src($settings['image'], 'large');
        if ($image) {
            echo '<div class="fs-newsletter-image"><img src="' . esc_url($image) . '" alt="' . esc_attr($settings['heading']) . '"></div>';
        }
    }
    echo '<div class="fs-newsletter-content">';
    if (!empty($settings['heading'])) {
        echo '<h3>' . esc_html($settings['heading']) . '</h3>';
    }
    if (!empty($settings['description'])) {
        echo '<p>' . esc_html($settings['description']) . '</p>';
    }
    if (!empty($settings['form_shortcode'])) {
        echo do_shortcode($settings['form_shortcode']);
    }
    echo '</div></div>';
}

function fs_render_block_feature_cards($settings) {
    $items = isset($settings['items']) ? $settings['items'] : array();
    if (empty($items)) {
        return;
    }
    $columns = isset($settings['columns']) ? intval($settings['columns']) : 3;
    echo '<div class="row g-3 fs-feature-cards">';
    foreach ($items as $item) {
        echo '<div class="col-lg-' . esc_attr(12 / max(1, $columns)) . ' col-md-6">';
        echo '<div class="fs-feature-card">';
        if (!empty($item['icon_type']) && $item['icon_type'] === 'image' && !empty($item['image'])) {
            $image = fs_get_image_src($item['image'], 'thumbnail');
            if ($image) {
                echo '<div class="fs-feature-icon"><img src="' . esc_url($image) . '" alt="' . esc_attr($item['title']) . '"></div>';
            }
        } elseif (!empty($item['icon'])) {
            echo '<div class="fs-feature-icon"><span class="' . esc_attr($item['icon']) . '"></span></div>';
        }
        if (!empty($item['title'])) {
            echo '<h4>' . esc_html($item['title']) . '</h4>';
        }
        if (!empty($item['description'])) {
            echo '<p>' . esc_html($item['description']) . '</p>';
        }
        if (!empty($item['link']['url'])) {
            echo '<a class="fs-feature-link" href="' . esc_url($item['link']['url']) . '" target="' . esc_attr($item['link']['target']) . '">' . esc_html($item['link']['label']) . '</a>';
        }
        echo '</div></div>';
    }
    echo '</div>';
}

function fs_render_block_cta_banner($settings) {
    echo '<div class="fs-cta-banner fs-align-' . esc_attr(isset($settings['alignment']) ? $settings['alignment'] : 'center') . '">';
    if (!empty($settings['eyebrow'])) {
        echo '<p class="fs-cta-eyebrow">' . esc_html($settings['eyebrow']) . '</p>';
    }
    if (!empty($settings['title'])) {
        echo '<h3>' . esc_html($settings['title']) . '</h3>';
    }
    if (!empty($settings['description'])) {
        echo '<p>' . esc_html($settings['description']) . '</p>';
    }
    echo '<div class="fs-cta-buttons">';
    if (!empty($settings['button_primary']['url'])) {
        $btn = $settings['button_primary'];
        echo '<a class="btn btn-solid" href="' . esc_url($btn['url']) . '" target="' . esc_attr($btn['target']) . '">' . esc_html($btn['label']) . '</a>';
    }
    if (!empty($settings['button_secondary']['url'])) {
        $btn = $settings['button_secondary'];
        echo '<a class="btn btn-outline ms-2" href="' . esc_url($btn['url']) . '" target="' . esc_attr($btn['target']) . '">' . esc_html($btn['label']) . '</a>';
    }
    echo '</div></div>';
}

function fs_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
    ), $atts);
    ob_start();
    fs_render_section_by_id(intval($atts['id']));
    return ob_get_clean();
}
add_shortcode('fs_section', 'fs_section_shortcode');

function fs_sections_append_to_content($content) {
    if (is_admin() || !is_singular() || !in_the_loop() || !is_main_query()) {
        return $content;
    }
    ob_start();
    fs_render_page_sections(get_the_ID());
    $sections_html = ob_get_clean();
    if (!$sections_html) {
        return $content;
    }
    return $sections_html . $content;
}
add_filter('the_content', 'fs_sections_append_to_content', 5);

function fs_customize_register($wp_customize) {
    // Thêm section mới trong Customizer
    $wp_customize->add_section('fs_homepage_settings', array(
        'title' => __('Tùy chỉnh giao diện trang chủ', 'fashion-store'),
        'description' => __('Tùy chỉnh các phần tử trên trang chủ', 'fashion-store'),
        'priority' => 30,
    ));
    
    // ===== TOGGLE SWITCHES - Ẩn/Hiện các section =====
    
    // Home Slider
    $wp_customize->add_setting('fs_home_slider_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_home_slider_enable', array(
        'label' => __('Hiển thị Home Slider', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    // Collection Banner
    $wp_customize->add_setting('fs_collection_banner_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_collection_banner_enable', array(
        'label' => __('Hiển thị Collection Banner', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    
    // Paragraph Section
    $wp_customize->add_setting('fs_paragraph_section_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_paragraph_section_enable', array(
        'label' => __('Hiển thị Paragraph Section', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
     // Product Slider (Latest Drops)
     $wp_customize->add_setting('fs_product_slider_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_product_slider_enable', array(
        'label' => __('Hiển thị Product Slider (Latest Drops)', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    
    // Full Banner
    $wp_customize->add_setting('fs_full_banner_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_full_banner_enable', array(
        'label' => __('Hiển thị Full Banner', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    // Tab Product Section
    $wp_customize->add_setting('fs_tab_product_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_tab_product_enable', array(
        'label' => __('Hiển thị Tab Product Section', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    
    // Service Layout
    $wp_customize->add_setting('fs_service_layout_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_service_layout_enable', array(
        'label' => __('Hiển thị Service Layout', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
     // Register Form
     $wp_customize->add_setting('fs_register_form_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_register_form_enable', array(
        'label' => __('Hiển thị Register Form', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    
    // Blog Section
    $wp_customize->add_setting('fs_blog_section_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_blog_section_enable', array(
        'label' => __('Hiển thị Blog Section', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    // Logo Section
    $wp_customize->add_setting('fs_logo_section_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('fs_logo_section_enable', array(
        'label' => __('Hiển thị Logo Section', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'checkbox',
    ));
    
    // ===== PRODUCT SETTINGS =====
    
    // Số sản phẩm hiển thị trong Product Slider
    $wp_customize->add_setting('fs_product_slider_count', array(
        'default' => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('fs_product_slider_count', array(
        'label' => __('Số sản phẩm hiển thị (Latest Drops)', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 20,
            'step' => 1,
        ),
    ));
     // Loại sản phẩm hiển thị
     $wp_customize->add_setting('fs_product_slider_type', array(
        'default' => 'featured',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('fs_product_slider_type', array(
        'label' => __('Loại sản phẩm hiển thị', 'fashion-store'),
        'section' => 'fs_homepage_settings',
        'type' => 'select',
        'choices' => array(
            'featured' => __('Featured Products', 'fashion-store'),
            'recent' => __('Recent Products', 'fashion-store'),
            'sale' => __('Sale Products', 'fashion-store'),
            'best_selling' => __('Best Selling', 'fashion-store'),
        ),
    ));
}
add_action('customize_register', 'fs_customize_register');

// Helper function để kiểm tra section có được bật không
function fs_is_section_enabled($section_name) {
    $setting = get_theme_mod('fs_' . $section_name . '_enable', true);
    return $setting;
}

// Helper function để lấy số sản phẩm
function fs_get_product_count() {
    return get_theme_mod('fs_product_slider_count', 4);
}

// Helper function để lấy loại sản phẩm
function fs_get_product_type() {
    return get_theme_mod('fs_product_slider_type', 'featured');
}