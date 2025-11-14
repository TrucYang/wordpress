<section class="section-b-space checkout-section-2">
    <div class="container">
        <div class="checkout-page">
            <div class="checkout-form">
                <div class="row g-sm-4 g-3">
                    <div class="col-lg-7">
                        <div class="left-sidebar-checkout">
                            <div class="checkout-detail-box">
                                <ul>
                                    <li>
                                        <div class="checkout-box">
                                            <div class="checkout-title">
                                                <h4>Shipping Address</h4>
                                                <button data-bs-toggle="modal" data-bs-target="#addAddress"
                                                    class="d-flex align-items-center btn"><i
                                                        class="ri-add-line me-1"></i> Add New</button>
                                            </div>

                                            <div class="checkout-detail">
                                                <div class="row g-3">
                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="delivery-address-box">
                                                            <input class="form-check-input" type="radio"
                                                                name="flexRadioDefault" id="check" checked>
                                                            <label class="form-check-label" for="check">
                                                                <span class="name">New Home</span>
                                                                <span class="address text-content"><span
                                                                        class="text-title">Name :</span>
                                                                    Harry Potter</span>
                                                                <span class="address text-content"><span
                                                                        class="text-title">Address :</span> 26,
                                                                    Starts Hollow Colony, Denver, Colorado, United
                                                                    States</span>
                                                                <span class="address text-content"><span
                                                                        class="text-title">Phone :</span> +1
                                                                    5551855359</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="delivery-address-box">
                                                            <input class="form-check-input" type="radio"
                                                                name="flexRadioDefault" id="check1">
                                                            <label class="form-check-label" for="check1">
                                                                <span class="name">Old Home</span>
                                                                <span class="address text-content"><span
                                                                        class="text-title">Address :</span> 53B,
                                                                    Claire New Street, San Jose, Colorado, United
                                                                    States</span>
                                                                <span class="address text-content"><span
                                                                        class="text-title">Pin Code :</span>
                                                                    36954</span>
                                                                <span class="address text-content"><span
                                                                        class="text-title">Phone :</span> +1
                                                                    5551855359</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="checkout-box">
                                            <div class="checkout-title">
                                                <h4>Delivery Options</h4>
                                            </div>

                                            <div class="checkout-detail">
                                                <div class="row g-3">
                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="delivery-address-box">
                                                            <input class="form-check-input" type="radio"
                                                                name="checkbox2" id="check7">
                                                            <label class="form-check-label" for="check7">Standard
                                                                Delivery | Approx 5 to 7 Days</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-xxl-6 col-lg-12 col-md-6">
                                                        <div class="delivery-address-box">
                                                            <input class="form-check-input" type="radio"
                                                                name="checkbox2" id="check8" checked>
                                                            <label class="form-check-label" for="check8">Express
                                                                Delivery | Schedule </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="checkout-box">
                                            <div class="checkout-title">
                                                <h4>Payment Options</h4>
                                            </div>
                                            <div class="checkout-detail">
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <div class="delivery-address-box">
                                                            <input class="form-check-input" type="radio"
                                                                name="checkbox3" id="check9">
                                                            <label class="form-check-label" for="check9">CASH ON
                                                                DELIVERY</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="delivery-address-box">
                                                            <input class="form-check-input" type="radio"
                                                                name="checkbox3" id="check16" checked>
                                                            <label class="form-check-label" for="check16">BANK
                                                                TRANSFER</label>
                                                        </div>
                                                    </div>
                                                </div>
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
                                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                            $product = $cart_item['data'];
                                            $quantity = $cart_item['quantity'];
                                            $price = $product->get_price();
                                            $subtotal = WC()->cart->get_product_subtotal($product, $quantity);
                                            $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'medium')[0];
                                            ?>
                                            <li>
                                                <div class="cart-image">
                                                    <img src="<?php echo esc_url($image); ?>" class="img-fluid"
                                                        alt="<?php echo esc_attr($product->get_name()); ?>">
                                                </div>
                                                <div class="cart-content">
                                                    <div>
                                                        <h4><?php echo esc_html($product->get_name()); ?></h4>
                                                        <h5><?php echo wc_price($price); ?> X <?php echo $quantity; ?></h5>
                                                    </div>
                                                    <span class="text-theme"><?php echo $subtotal; ?></span>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
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
                                                    data-bs-target="#couponModal"><i class="ri-coupon-line"></i>View
                                                    All</button>
                                            </div>
                                            <div class="row g-sm-3 g-2 mb-3">
                                                <div class="col-md-6">
                                                    <div class="coupon-box">
                                                        <div class="card-name">
                                                            <h6>Holiday Savings</h6>
                                                        </div>
                                                        <div class="coupon-content">
                                                            <div class="coupon-apply">
                                                                <h6 class="coupon-code success-color">#HOLIDAY40
                                                                </h6>
                                                                <a class="btn theme-btn border-btn copy-btn mt-0"
                                                                    href="#!">Copy Code</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="coupon-box">
                                                        <div class="card-name">
                                                            <h6>Holiday Savings</h6>
                                                        </div>
                                                        <div class="coupon-content">
                                                            <div class="coupon-apply">
                                                                <h6 class="coupon-code success-color">#HOLIDAY40
                                                                </h6>
                                                                <a class="btn theme-btn border-btn copy-btn mt-0"
                                                                    href="#!">Copy Code</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="coupon-input-box">
                                                <input type="text" id="coupon" class="form-control"
                                                    placeholder="Enter Coupon Code Here...">
                                                <button class="apply-button btn">Apply now</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-box-loader">
                                        <ul class="sub-total">
                                            <li>Sub Total <span
                                                    class="count"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                                            </li>
                                            <li>Shipping <span
                                                    class="count"><?php echo WC()->cart->get_cart_shipping_total(); ?></span>
                                            </li>
                                            <li>Tax <span
                                                    class="count"><?php echo WC()->cart->get_taxes_total() ? wc_price(WC()->cart->get_taxes_total()) : '$0.00'; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <ul class="total">
                                        <li>Total <span class="count"><?php echo WC()->cart->get_total(); ?></span></li>
                                    </ul>
                                    <div class="text-end">
                                        <button class="btn order-btn">Place Order</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>