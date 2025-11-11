<?php
defined('ABSPATH') || exit;
get_header();
?>

<section class="section-b-space checkout-section-2">
    <div class="container">
        <form name="checkout" method="post" class="checkout woocommerce-checkout"
            action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
            <div class="checkout-page">
                <div class="checkout-form">
                    <div class="row g-sm-4 g-3">

                        <!-- Left: Shipping & Billing -->
                        <div class="col-lg-7">
                            <div class="left-sidebar-checkout">

                                <!-- Shipping Address -->
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Shipping Address</h4>
                                        <button data-bs-toggle="modal" data-bs-target="#addAddress"
                                            class="d-flex align-items-center btn"><i class="ri-add-line me-1"></i> Add
                                            New</button>
                                    </div>
                                    <div class="checkout-detail">
                                        <?php do_action('woocommerce_checkout_shipping'); ?>
                                    </div>
                                </div>

                                <!-- Billing Address -->
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Billing Address</h4>
                                        <button data-bs-toggle="modal" data-bs-target="#addAddress"
                                            class="d-flex align-items-center btn">
                                            <i class="ri-add-line me-1"></i> Add New
                                        </button>
                                    </div>
                                    <div class="checkout-detail">
                                        <?php do_action('woocommerce_checkout_billing'); ?>
                                    </div>
                                </div>

                                <!-- Delivery Options (WooCommerce shipping methods) -->
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Delivery Options</h4>
                                    </div>
                                    <div class="checkout-detail">
                                        <?php do_action('woocommerce_checkout_shipping'); ?>
                                    </div>
                                </div>

                                <!-- Payment Options -->
                                <div class="checkout-box">
                                    <div class="checkout-title">
                                        <h4>Payment Options</h4>
                                    </div>
                                    <div class="checkout-detail">
                                        <?php do_action('woocommerce_checkout_order_review'); ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Right: Order Summary -->
                        <div class="col-lg-5">
                            <div class="checkout-right-box">
                                <div class="checkout-details">
                                    <div class="order-box">
                                        <div class="title-box">
                                            <h4>Summary Order</h4>
                                            <p>For a better experience, verify your goods and choose your shipping
                                                option.</p>
                                        </div>
                                        <div class="woocommerce-checkout-review-order">
                                            <?php woocommerce_order_review(); ?>
                                        </div>
                                    </div>

                                    <div class="checkout-details">
                                        <div class="order-box">
                                            <div class="title-box">
                                                <h4>Billing Summary</h4>
                                                <div class="promo-code-box">
                                                    <div class="promo-title">
                                                        <h5>Promo code</h5>
                                                        <button class="btn" data-bs-toggle="modal"
                                                            data-bs-target="#couponModal">
                                                            <i class="ri-coupon-line"></i> View All
                                                        </button>
                                                    </div>
                                                    <div class="coupon-input-box">
                                                        <?php if (wc_coupons_enabled()): ?>
                                                            <input type="text" id="coupon_code" class="form-control"
                                                                placeholder="Enter Coupon Code Here..." name="coupon_code">
                                                            <button class="apply-button btn" name="apply_coupon"
                                                                value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">Apply
                                                                now
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php do_action('woocommerce_checkout_before_order_review'); ?>

                                            <ul class="total">
                                                <li>Total <span
                                                        class="count"><?php wc_cart_totals_order_total_html(); ?></span>
                                                </li>
                                            </ul>

                                            <div class="text-end">
                                                <?php
                                                // WooCommerce place order button
                                                woocommerce_checkout_payment();
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Right -->
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<?php get_footer(); ?>