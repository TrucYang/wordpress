<?php
defined('ABSPATH') || exit;

$checkout = WC()->checkout();

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', 'You must be logged in to checkout.'));
    return;
}
?>

<section class="section-b-space checkout-section-2">
    <form name="checkout" method="post" class="checkout woocommerce-checkout"
        action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

        <div class="container">
            <div class="checkout-page">
                <div class="checkout-form">
                    <div class="row g-sm-4 g-3">
                        <div class="col-lg-7">
                            <div class="left-sidebar-checkout">
                                <div class="checkout-detail-box">
                                    <ul>

                                        <!-- Billing Fields -->
                                        <li>
                                            <div class="checkout-box">
                                                <div class="checkout-title"><h4>Shipping Address</h4></div>
                                                <div class="checkout-detail">
                                                    <?php
                                                    if (!empty($checkout->get_checkout_fields('billing'))) :
                                                        foreach ($checkout->get_checkout_fields('billing') as $key => $field) :
                                                            woocommerce_form_field($key, $field, $checkout->get_value($key));
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                </div>
                                            </div>
                                        </li>

                                        <!-- Shipping Methods -->
                                        <li>
                                            <div class="checkout-box">
                                                <div class="checkout-title"><h4>Delivery Options</h4></div>
                                                <div class="checkout-detail">
                                                    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                                                        <div class="row g-3">
                                                            <?php
                                                            $packages = WC()->shipping()->get_packages();
                                                            foreach ($packages as $i => $package) :
                                                                $chosen_method = isset(WC()->session->get('chosen_shipping_methods')[$i]) ? WC()->session->get('chosen_shipping_methods')[$i] : '';
                                                                foreach ($package['rates'] as $rate_id => $rate) : ?>
                                                                    <div class="col-12">
                                                                        <div class="delivery-address-box d-flex justify-content-between align-items-center">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="shipping_method[<?php echo $i; ?>]"
                                                                                id="shipping_<?php echo esc_attr($rate_id); ?>"
                                                                                value="<?php echo esc_attr($rate_id); ?>"
                                                                                <?php checked($rate_id, $chosen_method); ?> />
                                                                            <label class="form-check-label" for="shipping_<?php echo esc_attr($rate_id); ?>">
                                                                                <?php echo esc_html($rate->get_label()); ?>
                                                                            </label>
                                                                            <span class="text-theme"><?php echo wc_price($rate->get_cost()); ?></span>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach;
                                                            endforeach; ?>
                                                        </div>
                                                    <?php else : ?>
                                                        <p><?php esc_html_e('No shipping methods available. Please ensure your address is correct.', 'woocommerce'); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>

                                        <!-- Payment Methods -->
                                        <li>
                                            <div class="checkout-box">
                                                <div class="checkout-title"><h4>Payment Options</h4></div>
                                                <div class="checkout-detail">
                                                    <?php if (WC()->cart->needs_payment()) : ?>
                                                        <div class="row g-3">
                                                            <?php foreach (WC()->payment_gateways()->get_available_payment_gateways() as $gateway) : ?>
                                                                <div class="col-sm-6">
                                                                    <div class="delivery-address-box">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="payment_method"
                                                                            id="payment_<?php echo esc_attr($gateway->id); ?>"
                                                                            value="<?php echo esc_attr($gateway->id); ?>"
                                                                            <?php checked($gateway->chosen, true); ?> />
                                                                        <label class="form-check-label" for="payment_<?php echo esc_attr($gateway->id); ?>">
                                                                            <?php echo wp_kses_post($gateway->get_title()); ?>
                                                                        </label>
                                                                        <?php if ($gateway->has_fields() || $gateway->get_description()) : ?>
                                                                            <div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" style="display:none;">
                                                                                <!-- <?php $gateway->payment_fields(); ?> -->
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="checkout-right-box">
                                <div class="checkout-details">
                                    <div class="order-box">
                                        <div class="title-box">
                                            <h4>Summary Order</h4>
                                            <p>For a better experience, verify your goods and choose your shipping
                                            option.</p>
                                        </div>

                                        <ul class="qty">
                                            <?php 
                                            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                                                $product = $cart_item['data'];
                                                $quantity = $cart_item['quantity'];
                                                $price = $product->get_price();
                                                $subtotal = WC()->cart->get_product_subtotal($product, $quantity);
                                                $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'medium')[0]; ?>
                                                <li>
                                                    <div class="cart-image">
                                                        <img src="<?php echo esc_url($image); ?>" class="img-fluid" alt="<?php echo esc_attr($product->get_name()); ?>">
                                                    </div>
                                                    <div class="cart-content">
                                                        <div>
                                                            <h4><?php echo esc_html($product->get_name()); ?></h4>
                                                            <h5><?php echo wc_price($price); ?> X <?php echo $quantity; ?></h5>
                                                        </div>
                                                        <span class="text-theme"><?php echo $subtotal; ?></span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>

                                <div class="checkout-details">
                                <div class="order-box">
                                    <div class="title-box">
                                        <h4>Billing Summary</h4>
                                        <div class="promo-code-box">
                                            <div class="coupon-input-box">
                                                <input type="text" id="coupon" class="form-control"
                                                    placeholder="Enter Coupon Code Here...">
                                                <button class="apply-button btn">Apply now</button>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="custom-box-loader">
                                        <ul class="sub-total">
                                            <li>Sub Total <span class="count"><?php echo WC()->cart->get_cart_subtotal(); ?></span></li>
                                            <li>Shipping <span class="count"><?php echo WC()->cart->get_cart_shipping_total(); ?></span></li>
                                            <li>Tax <span class="count"><?php echo WC()->cart->get_taxes_total() ? wc_price(WC()->cart->get_taxes_total()) : '$0.00'; ?></span></li>
                                        </ul>
                                        </div>
                                        <ul class="total">
                                            <li>Total <span class="count"><?php echo WC()->cart->get_total(); ?></span></li>
                                        </ul>

                                        <div class="text-end mt-3">
                                            <?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="btn order-btn" name="woocommerce_checkout_place_order" id="place_order" value="Place order">Place Order</button>'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
    </form>

    <?php do_action('woocommerce_after_checkout_form', $checkout); ?>
</section>
