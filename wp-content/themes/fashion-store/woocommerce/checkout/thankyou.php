<?php
defined('ABSPATH') || exit;

if (!$order)
    return;

$items = $order->get_items();
$billing = $order->get_address('billing');
$shipping = $order->get_address('shipping');
?>

<!-- thank-you section start -->
<section class="section-b-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <div class="checkmark">
                        <svg class="star" height="19" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.296.747c.532-.972 1.393-.973 1.925 0l2.665 4.872 4.876 2.66c.974.532.975 1.393 0 1.926l-4.875 2.666-2.664 4.876c-.53.972-1.39.973-1.924 0l-2.664-4.876L.76 10.206c-.972-.532-.973-1.393 0-1.925l4.872-2.66L8.296.746z">
                            </path>
                        </svg>
                        <svg class="star" height="19" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.296.747c.532-.972 1.393-.973 1.925 0l2.665 4.872 4.876 2.66c.974.532.975 1.393 0 1.926l-4.875 2.666-2.664 4.876c-.53.972-1.39.973-1.924 0l-2.664-4.876L.76 10.206c-.972-.532-.973-1.393 0-1.925l4.872-2.66L8.296.746z">
                            </path>
                        </svg>
                        <svg class="star" height="19" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.296.747c.532-.972 1.393-.973 1.925 0l2.665 4.872 4.876 2.66c.974.532.975 1.393 0 1.926l-4.875 2.666-2.664 4.876c-.53.972-1.39.973-1.924 0l-2.664-4.876L.76 10.206c-.972-.532-.973-1.393 0-1.925l4.872-2.66L8.296.746z">
                            </path>
                        </svg>
                        <svg class="star" height="19" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.296.747c.532-.972 1.393-.973 1.925 0l2.665 4.872 4.876 2.66c.974.532.975 1.393 0 1.926l-4.875 2.666-2.664 4.876c-.53.972-1.39.973-1.924 0l-2.664-4.876L.76 10.206c-.972-.532-.973-1.393 0-1.925l4.872-2.66L8.296.746z">
                            </path>
                        </svg>
                        <svg class="star" height="19" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.296.747c.532-.972 1.393-.973 1.925 0l2.665 4.872 4.876 2.66c.974.532.975 1.393 0 1.926l-4.875 2.666-2.664 4.876c-.53.972-1.39.973-1.924 0l-2.664-4.876L.76 10.206c-.972-.532-.973-1.393 0-1.925l4.872-2.66L8.296.746z">
                            </path>
                        </svg>
                        <svg class="star" height="19" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.296.747c.532-.972 1.393-.973 1.925 0l2.665 4.872 4.876 2.66c.974.532.975 1.393 0 1.926l-4.875 2.666-2.664 4.876c-.53.972-1.39.973-1.924 0l-2.664-4.876L.76 10.206c-.972-.532-.973-1.393 0-1.925l4.872-2.66L8.296.746z">
                            </path>
                        </svg>
                        <svg class="checkmark__check" height="36" viewBox="0 0 48 36" width="48"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M47.248 3.9L43.906.667a2.428 2.428 0 0 0-3.344 0l-23.63 23.09-9.554-9.338a2.432 2.432 0 0 0-3.345 0L.692 17.654a2.236 2.236 0 0 0 .002 3.233l14.567 14.175c.926.894 2.42.894 3.342.01L47.248 7.128c.922-.89.922-2.34 0-3.23">
                            </path>
                        </svg>
                        <svg class="checkmark__background" height="115" viewBox="0 0 120 115" width="120"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M107.332 72.938c-1.798 5.557 4.564 15.334 1.21 19.96-3.387 4.674-14.646 1.605-19.298 5.003-4.61 3.368-5.163 15.074-10.695 16.878-5.344 1.743-12.628-7.35-18.545-7.35-5.922 0-13.206 9.088-18.543 7.345-5.538-1.804-6.09-13.515-10.696-16.877-4.657-3.398-15.91-.334-19.297-5.002-3.356-4.627 3.006-14.404 1.208-19.962C10.93 67.576 0 63.442 0 57.5c0-5.943 10.93-10.076 12.668-15.438 1.798-5.557-4.564-15.334-1.21-19.96 3.387-4.674 14.646-1.605 19.298-5.003C35.366 13.73 35.92 2.025 41.45.22c5.344-1.743 12.628 7.35 18.545 7.35 5.922 0 13.206-9.088 18.543-7.345 5.538 1.804 6.09 13.515 10.696 16.877 4.657 3.398 15.91.334 19.297 5.002 3.356 4.627-3.006 14.404-1.208 19.962C109.07 47.424 120 51.562 120 57.5c0 5.943-10.93 10.076-12.668 15.438z">
                            </path>
                        </svg>
                    </div>
                    <h2>Thank you!</h2>
                    <p>Payment is successfully processed and your order is on the way.</p>
                    <p class="font-weight-bold">Transaction ID: <?php echo esc_html($order->get_id()); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->

<!-- order-detail section start -->
<section class="section-b-space">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product-order">
                    <table class="table product-order-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item_id => $item):
                                $product = $item->get_product();
                                $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'thumbnail')[0];
                                ?>
                                <tr>
                                    <td><img src="<?php echo esc_url($image); ?>" alt="" class="img-fluid blur-up lazyload">
                                    </td>
                                    <td><?php echo esc_html($item->get_name()); ?></td>
                                    <td><?php echo esc_html($item->get_quantity()); ?></td>
                                    <td><?php echo wc_price($item->get_total()); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Subtotal</td>
                                <td colspan="2"><?php echo $order->get_subtotal_to_display(); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Shipping</td>
                                <td colspan="2"><?php echo $order->get_shipping_to_display(); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Tax</td>
                                <td colspan="2"><?php echo wc_price($order->get_total_tax()); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">Total</td>
                                <td colspan="2"><?php echo $order->get_formatted_order_total(); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="order-success-sec">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4>Summary</h4>
                            <ul class="order-detail">
                                <li>Order ID: <?php echo esc_html($order->get_id()); ?></li>
                                <li>Order Date: <?php echo wc_format_datetime($order->get_date_created()); ?></li>
                                <li>Order Total: <?php echo $order->get_formatted_order_total(); ?></li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <h4>Shipping Address</h4>
                            <ul class="order-detail">
                                <li><?php echo esc_html($shipping['first_name'] . ' ' . $shipping['last_name']); ?></li>
                                <li><?php echo esc_html($shipping['address_1']); ?></li>
                                <li><?php echo esc_html($shipping['city'] . ', ' . $shipping['postcode']); ?></li>
                                <li><?php echo esc_html($shipping['country']); ?></li>
                            </ul>
                        </div>
                        <div class="col-sm-12 payment-mode">
                            <h4>Payment Method</h4>
                            <p><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                        </div>
                        <div class="col-md-12">
                            <div class="delivery-sec">
                                <h3>Expected delivery date:
                                    <span><?php echo date_i18n('F j, Y', strtotime('+5 days')); ?></span></h3>
                                <a href="<?php echo esc_url(home_url('/order-tracking')); ?>">Track Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->