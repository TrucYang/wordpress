<?php
if (is_user_logged_in()) {
    echo '<p>You are already logged in.</p>';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $password = $_POST['password'];

        // Tạo user WooCommerce
        $user_id = wc_create_new_customer($email, '', $password);

        if (!is_wp_error($user_id)) {
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name
            ]);
            update_user_meta($user_id, 'billing_phone', $phone);

            //Auto login
            wp_set_current_user($user_id); // đăng nhập
            wp_set_auth_cookie($user_id); //tạo session

            // Redirect về trang chủ
            wp_safe_redirect(home_url());
            exit;
        } else {
            echo '<p>Error: ' . $user_id->get_error_message() . '</p>';
        }
    }
    ?>

    <?php get_header(); ?>

    <!-- breadcrumb start -->
    <div class="breadcrumb-section">
        <div class="container">
            <h2>Create account</h2>
            <nav class="theme-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?php echo site_url(); ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Create account</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- breadcrumb End -->

    <!--section start-->
    <section class="login-page section-b-space">
        <div class="container">
            <h3>Create Account</h3>
            <div class="theme-card">
                <form method="post" class="theme-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-box">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" id="first_name"
                                    placeholder="First Name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-box">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="last_name"
                                    placeholder="Last Name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-box">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-box">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-box">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Enter your password" required>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" name="register" class="btn btn-solid w-auto">Create Account</button>
                        </div>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</section>
<!--Section ends-->

<?php get_footer(); ?>