<?php
/**
 * Plugin Name: Simple Reaction
 * Description: Plugin Reaction đơn giản – đổi reaction, không double-count, chống spam, AJAX.
 * Version: 1.3
 * Author: Hiếu – Hiền – Giang – Kiệt
 */

if (!defined('ABSPATH')) exit;

/*
|--------------------------------------------------------------------------
| Tạo HTML Reaction Buttons
|--------------------------------------------------------------------------
*/
function sr_get_reaction_html($post_id) {
    $reactions = [
        'like' => '👍',
        'love' => '❤️',
        'haha' => '😂',
        'wow'  => '😮',
        'sad'  => '😢'
    ];

    $html = '<div class="sr-reactions" data-post="' . $post_id . '">';
    foreach ($reactions as $key => $icon) {
        $count = intval(get_post_meta($post_id, "sr_$key", true));
        $html .= "
            <button class='sr-btn' data-type='$key' data-post='$post_id'>
                $icon <span class='sr-count'>$count</span>
            </button>
        ";
    }
    $html .= '</div>';
    return $html;
}

/*
|--------------------------------------------------------------------------
| Chèn tự động vào cuối bài viết single
|--------------------------------------------------------------------------
*/
add_filter('the_content', function($content) {
    if (!is_single()) return $content;
    return $content . sr_get_reaction_html(get_the_ID());
});

/*
|--------------------------------------------------------------------------
| Shortcode
|--------------------------------------------------------------------------
*/
function sr_shortcode_reactions($atts) {
    $a = shortcode_atts(['post_id' => 0], $atts);
    $post_id = $a['post_id'] ? intval($a['post_id']) : get_the_ID();
    if (!$post_id) return '';
    return sr_get_reaction_html($post_id);
}
add_shortcode('simple_reaction', 'sr_shortcode_reactions');

/*
|--------------------------------------------------------------------------
| Enqueue Scripts
|--------------------------------------------------------------------------
*/
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('sr-style', plugin_dir_url(__FILE__) . 'assets/reaction.css');
    wp_enqueue_script('sr-script', plugin_dir_url(__FILE__) . 'assets/reaction.js', ['jquery'], null, true);

    wp_localize_script('sr-script', 'sr_ajax', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('sr_nonce')
    ]);
});

/*
|--------------------------------------------------------------------------
| AJAX xử lý reaction
|--------------------------------------------------------------------------
*/
add_action('wp_ajax_sr_add_reaction', 'sr_add_reaction');
add_action('wp_ajax_nopriv_sr_add_reaction', 'sr_add_reaction');

function sr_add_reaction() {
    check_ajax_referer('sr_nonce', 'nonce');

    $post_id  = intval($_POST['post_id']);
    $new_type = sanitize_text_field($_POST['type']);
    if (!$post_id || !$new_type) wp_send_json_error('Invalid data');

    $reactions = ['like','love','haha','wow','sad'];

    // Xác định user: login user ID, nếu chưa login fallback IP
    if (is_user_logged_in()) {
        $user_identifier = 'user_' . get_current_user_id();
    } else {
        $user_identifier = 'ip_' . $_SERVER['REMOTE_ADDR'];
    }
    $user_key = "sr_user_reaction_{$post_id}_{$user_identifier}";

    // Lấy reaction cũ
    $old_type = get_post_meta($post_id, $user_key, true);
    $old_type = $old_type ? $old_type : '';

    // Nếu bấm y chang -> trả về counts hiện tại
    if ($old_type === $new_type) {
        $counts = [];
        foreach ($reactions as $r) $counts[$r] = intval(get_post_meta($post_id, "sr_$r", true));
        wp_send_json_success([
            'old_type' => $old_type,
            'new_type' => $new_type,
            'counts'   => $counts
        ]);
    }

    // Nếu có old_type -> giảm count cũ
    if (!empty($old_type) && in_array($old_type, $reactions)) {
        $old_count = intval(get_post_meta($post_id, "sr_$old_type", true));
        update_post_meta($post_id, "sr_$old_type", max(0, $old_count-1));
    }

    // Tăng reaction mới
    $new_count = intval(get_post_meta($post_id, "sr_$new_type", true));
    update_post_meta($post_id, "sr_$new_type", $new_count+1);

    // Lưu lựa chọn mới
    update_post_meta($post_id, $user_key, $new_type);

    // Trả về counts mới
    $counts = [];
    foreach ($reactions as $r) $counts[$r] = intval(get_post_meta($post_id, "sr_$r", true));

    wp_send_json_success([
        'old_type' => $old_type,
        'new_type' => $new_type,
        'counts'   => $counts
    ]);
}
