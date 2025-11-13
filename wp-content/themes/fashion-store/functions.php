<?php
// Thêm support cơ bản
function fs_theme_setup()
{
    load_theme_textdomain('fashion-store', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 100,
    ));
    // cho logo
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
