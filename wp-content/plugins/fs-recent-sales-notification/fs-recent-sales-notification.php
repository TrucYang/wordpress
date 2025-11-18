<?php
/*
Plugin Name: FS Recent Sales Notification
Description: Popup thông báo khách vừa mua gì, tăng tin tưởng và chuyển đổi!
Version: 1.0
Author: Your Name
*/

add_action('wp_ajax_nopriv_fs_get_recent_orders', 'fs_get_recent_orders');
add_action('wp_ajax_fs_get_recent_orders', 'fs_get_recent_orders');
function fs_get_recent_orders()
{
    $args = [
        'status' => ['on-hold' ,'processing', 'completed'],
        'limit' => 20,
        'return' => 'ids',
    ];
    $orders = wc_get_orders($args);
    $notifications = [];
    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        $billing_name = $order->get_billing_first_name() ?: 'Khách hàng';
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            $notifications[] = [
                'product_image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
                'customer' => $billing_name,
                'product' => $item->get_name(),
                'product_url' => get_permalink($product->get_id()),
                'minutes_ago' => ceil((time() - strtotime($order->get_date_created())) / 60)
            ];
        }
    }
    shuffle($notifications);
    wp_send_json(array_slice($notifications, 0, 5));
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('fs-sales-noti', plugin_dir_url(__FILE__) . 'sales-noti.js', ['jquery'], null, true);
    wp_enqueue_style('fs-sales-noti', plugin_dir_url(__FILE__) . 'sales-noti.css');
    wp_localize_script('fs-sales-noti', 'fs_sales_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});

// hiển thị popup ngay order vừa đặt trên Thank You page
add_action('woocommerce_thankyou', 'fs_show_order_popup', 20);
function fs_show_order_popup($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order)
        return;

    $billing_name = $order->get_billing_first_name() ?: 'Khách hàng';
    $items = $order->get_items();
    $notifications = [];

    foreach ($items as $item) {
        $product = $item->get_product();
        $notifications[] = [
            'product_image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
            'customer' => $billing_name,
            'product' => $item->get_name(),
            'product_url' => get_permalink($product->get_id()),
            'minutes_ago' => 0
        ];
    }

    if ($notifications) {
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                <?php foreach ($notifications as $notif): ?>
                    fs_show_notification_immediately(<?php echo json_encode($notif); ?>);
                <?php endforeach; ?>
            });
        </script>
        <?php
    }
}


