<?php
defined('ABSPATH') || exit;
?>

<div class="checkout-detail">
    <?php
    if (!empty($checkout->get_checkout_fields('billing'))) :
        foreach ($checkout->get_checkout_fields('billing') as $key => $field) : ?>
            <div class="billing-field mb-3">
                <?php
                woocommerce_form_field(
                    $key,
                    $field,
                    $checkout->get_value($key)
                );
                ?>
            </div>
        <?php endforeach;
    endif;
    ?>
</div>
