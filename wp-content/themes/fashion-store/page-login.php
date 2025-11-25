<?php
get_header();

if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
?>

<section class="login-page section-b-space">
    <div class="container">
        <div class="row">

            <!-- LEFT SIDE -->
            <div class="col-lg-6">

                <?php if ($action === 'lost_password') : ?>

                    <h3>FORGOT PASSWORD</h3>
                    <div class="theme-card">
                        <?php wc_get_template('myaccount/form-lost-password.php'); ?>
                    </div>

                <?php else : ?>

                    <h3>Login</h3>
                    <div class="theme-card">
                        <?php
                        woocommerce_login_form([
                            'redirect' => home_url()
                        ]);
                        ?>
                    </div>

                <?php endif; ?>

            </div>

            <!-- RIGHT SIDE -->
            <div class="col-lg-6 right-login">
                <h3>New Customer</h3>
                <div class="theme-card authentication-right">
                    <h6 class="title-font">Create An Account</h6>
                    <p>
                        Sign up for a free account at our store. Registration is quick and easy.
                        It allows you to be able to order from our shop. To start shopping click register.
                    </p>
                    <a href="<?php echo site_url('/register'); ?>" class="btn btn-solid">Create an Account</a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
