<?php
defined( 'ABSPATH' ) || exit;
?>

<section class="section-b-space order-track-section">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-lg-6 col-md-8">
                <div class="tracking-box">
                    <h2 class="text-center mb-3">Track Your Order</h2>
                    <p class="text-center text-muted mb-4">
                        Enter your <strong>Order ID</strong> and <strong>Email</strong> to check your current order status.
                    </p>

                    <form action="" method="post" class="track-form mb-4">
                        <div class="form-group mb-3">
                            <label for="orderid">Order ID</label>
                            <input class="form-control" name="orderid" id="orderid" type="text"
                                   placeholder="Example: 1456" required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="order_email">Billing Email</label>
                            <input class="form-control" name="order_email" id="order_email" type="email"
                                   placeholder="you@example.com" required>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-solid w-100" type="submit">Check Order Status</button>
                        </div>

                        <?php wp_nonce_field('woocommerce-order_tracking'); ?>
                        <input type="hidden" name="track_order" value="1">
                    </form>

                    <?php
                    if ( isset($_POST['track_order']) ) {

                        $order_id     = sanitize_text_field($_POST['orderid']);
                        $order_email  = sanitize_email($_POST['order_email']);
                        $order        = wc_get_order($order_id);

                        if ( $order && $order->get_billing_email() === $order_email ) :

                            $status = wc_get_order_status_name( $order->get_status() ); ?>

                            <div class="tracking-result p-3 mt-3">
                                <h4 class="mb-3">Order Status: <span class="text-success"><?php echo $status; ?></span></h4>

                                <ul>
                                    <li><strong>Order ID:</strong> #<?php echo $order_id; ?></li>
                                    <li><strong>Total:</strong> <?php echo $order->get_formatted_order_total(); ?></li>
                                    <li><strong>Date:</strong> <?php echo wc_format_datetime($order->get_date_created()); ?></li>
                                    <li><strong>Payment:</strong> <?php echo $order->get_payment_method_title(); ?></li>
                                </ul>

                                <a class="btn btn-outline w-100 mt-3" 
                                   href="<?php echo $order->get_view_order_url(); ?>">
                                    View Full Order
                                </a>
                            </div>

                        <?php else : ?>

                            <p class="text-danger text-center mt-3">
                                Order not found. Please check again.
                            </p>

                        <?php endif;
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>
