<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_account_navigation');
?>


<section class="dashboard-section section-b-space user-dashboard-section">
    <div class="container">
        <div class="row">

            <div class="col-lg-3">
                <?php wc_get_template('myaccount/navigation.php'); ?>
            </div>

            <div class="col-lg-9">
                <button class="show-btn btn d-lg-none d-block">Show Menu</button>
                <div class="faq-content tab-content" id="myTabContent">
                    <!-- TAB 1: DASHBOARD -->
                    <?php wc_get_template('myaccount/dashboard.php'); ?>

                    <!-- TAB 2: NOTIFICATIONS -->
                    <?php wc_get_template('myaccount/orders.php'); ?>

                    <!-- TAB 3: ORDERS -->
                    <?php wc_get_template('myaccount/notifications.php'); ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?php do_action('woocommerce_after_account_navigation'); ?>