    <div class="tab-pane fade show active" id="tab-dashboard">
        <div class="counter-section">
            <div class="welcome-msg">
                <h4>Hello, <?php echo wp_get_current_user()->display_name; ?>!</h4>
                <p>From your My Account Dashboard you have the ability to view a snapshot of your recent account activity and update your account information. Select a link below to view or edit information.</p>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="counter-box">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/dashboard/balance.png" class="img-fluid">
                        <div>
                            <h3>
                                <?php
                                $orders = wc_get_orders( ['customer' => get_current_user_id()] );
                                echo count($orders);
                                ?>
                            </h3>
                            <h5>Total Orders</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-account box-info">
                <div class="box-head"><h4>Account Information</h4></div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box">
                            <ul class="box-content">
                                <li class="w-100"><h6>Full Name: <?php echo wp_get_current_user()->display_name; ?></h6></li>
                                <li class="w-100"><h6>Email: <?php echo wp_get_current_user()->user_email; ?></h6></li>
                                <li class="w-100">
                                    <h6>Address:
                                        <?php echo WC()->customer->get_billing_address_1(); ?>
                                    </h6>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>