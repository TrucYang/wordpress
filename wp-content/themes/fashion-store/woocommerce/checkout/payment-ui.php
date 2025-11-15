<?php
defined('ABSPATH') || exit;
?>

<div class="row g-3">
    <?php if (WC()->cart->needs_payment()) : ?>
        <?php foreach (WC()->payment_gateways()->get_available_payment_gateways() as $gateway) : ?>
            <div class="col-sm-6">
                <div class="delivery-address-box">
                    <input class="form-check-input" type="radio" name="payment_method"
                        id="payment_<?php echo esc_attr($gateway->id); ?>" value="<?php echo esc_attr($gateway->id); ?>"
                        <?php checked($gateway->chosen, true); ?> />
                    <label class="form-check-label" for="payment_<?php echo esc_attr($gateway->id); ?>">
                        <?php echo wp_kses_post($gateway->get_title()); ?>
                    </label>

                    <?php if ($gateway->has_fields() || $gateway->get_description()) : ?>
                        <div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>" style="display:none; margin-top:10px;">
                            <!-- <?php $gateway->payment_fields(); ?> -->
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p><?php esc_html_e('No payment methods available. Please contact us for assistance.', 'woocommerce'); ?></p>
    <?php endif; ?>
</div>
