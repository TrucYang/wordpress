<?php defined('ABSPATH') || exit; ?>

  <form method="post" action="<?php echo esc_url( site_url('wp-login.php?action=lostpassword', 'login_post') ); ?>" class="woocommerce-ResetPassword lost_reset_password">

    <p>Enter your email, we will send you a password reset link.</p>

     <p class="form-row form-row-wide">
       <label for="user_login">Email <span class="required">*</span></label>
       <input type="text" class="input-text" name="user_login" id="user_login" autocomplete="username" />
    </p>

     <?php do_action( 'woocommerce_lostpassword_form' ); ?>

    <p class="form-row">
        <input type="hidden" name="wc_reset_password" value="true" />
        <?php wp_nonce_field( 'lost_password', 'woocommerce-reset-password-nonce' ); ?>
        <button type="submit" class="btn btn-solid">Reset Password</button>
    </p>

</form>

<a href="<?php echo esc_url( site_url('/login') ); ?>" class="back-btn">← Return to login</a>
