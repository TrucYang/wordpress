<?php
defined('ABSPATH') || exit;
?>

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
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shipping_method[<?php echo $i; ?>]"
                                    id="shipping_<?php echo esc_attr($rate_id); ?>" value="<?php echo esc_attr($rate_id); ?>"
                                    <?php checked($rate_id, $chosen_method); ?> />
                                <label class="form-check-label" style="margin-left: 10px" for="shipping_<?php echo esc_attr($rate_id); ?>">
                                    <?php echo esc_html($rate->get_label()); ?>
                                </label>
                            </div>
                            <span class="text-theme"><?php echo wc_price($rate->get_cost()); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p><?php esc_html_e('No shipping methods available. Please ensure your address is correct.', 'woocommerce'); ?></p>
    <?php endif; ?>
</div>
