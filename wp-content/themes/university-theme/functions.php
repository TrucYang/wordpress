<?php
function university_files()
{
    // Google Fonts
    wp_enqueue_style(
        'university_google_fonts',
        'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i'
    );

    // Font Awesome
    wp_enqueue_style(
        'university_font_awesome',
        'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        array(),
        null,
        'all'
    );
    
    // CSS của theme
    wp_enqueue_style('university_index_css', get_theme_file_uri('build/index.css'));
    wp_enqueue_style('university_style_index_css', get_theme_file_uri('build/style-index.css'));

    // Nạp JS chính
    wp_enqueue_script(
        'university_main_js',
        get_theme_file_uri('build/index.js'),
        array(),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
  add_theme_support('title-tag'); 
}
add_action('after_setup_theme', 'university_features');

// Hàm đăng ký các khu vực menu
function university_theme_setup() {
    add_theme_support('title-tag');
    register_nav_menus(array(
        'footerLocationOne'  => 'Menu 1',
        'footerLocationTwo'  => 'Menu 2'
    ));
}
add_action('after_setup_theme', 'university_theme_setup');

function university_post_types() {

    $labels = array(
        'name'                  => _x( 'Events', 'Post type general name', 'event' ),
        'singular_name'         => _x( 'Event', 'Post type singular name', 'event' ),
        'menu_name'             => _x( 'Events', 'Admin Menu text', 'event' ),
        'name_admin_bar'        => _x( 'Events', 'Add New on Toolbar', 'event' ),
        'add_new'               => __( 'Add New', 'event' ),
        'add_new_item'          => __( 'Add New Event', 'event' ),
        'new_item'              => __( 'New Event', 'event' ),
        'edit_item'             => __( 'Edit Event', 'event' ),
        'view_item'             => __( 'View Event', 'event' ),
        'all_items'             => __( 'All Events', 'event' ),
    );

    $args = array(
        'labels'            => $labels,
        'description'       => 'Event custom post type.',
        'public'            => true,
        'show_in_menu'      => true,
        'rewrite'           => array( 'slug' => 'event' ),
        'menu_position'     => 20,
        'supports'          => array( 'title', 'editor', 'custom-field'),
        'show_in_rest'      => true,
        'menu_icon'         => 'dashicons-calendar',
    );

    register_post_type( 'events', $args );
}

add_action( 'init', 'university_post_types' );

add_theme_support('woocommerce');
add_theme_support('wc-product-gallery-zoom');
add_theme_support('wc-product-gallery-lightbox');
add_theme_support('wc-product-gallery-slider');

