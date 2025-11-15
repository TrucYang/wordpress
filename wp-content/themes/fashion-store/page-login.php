<?php
get_header();

if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
?>

<div class="breadcrumb-section">
    <div class="container">
        <h2>Customer's login</h2>
        <nav class="theme-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo site_url(); ?>">Home</a></li>
                <li class="breadcrumb-item active">Customer's login</li>
            </ol>
        </nav>
    </div>
</div>

<section class="login-page section-b-space">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h3>Login</h3>
                <div class="theme-card">
                    <?php
                    woocommerce_login_form(
                        array(
                            'redirect' => home_url(), //redirect về Home
                        )
                    );
                    ?>

                </div>
            </div>

            <div class="col-lg-6 right-login">
                <h3>New Customer</h3>
                <div class="theme-card authentication-right">
                    <h6 class="title-font">Create An Account</h6>
                    <p>Sign up for a free account at our store. Registration is quick and easy. It allows you to be
                        able to order from our shop. To start shopping click register.</p>
                    <a href="<?php echo site_url('/register'); ?>" class="btn btn-solid">Create an Account</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>