jQuery(document).ready(function ($) {

    // --- 1. Clear toàn bộ giỏ hàng ---
    $('#clear-cart').off('click').on('click', function (e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to clear your cart?')) return;

        $.post(wc_add_to_cart_params.ajax_url, { action: 'clear_cart' }, function (response) {
            if (response.success) {
                $('.cart-product').html('<li><p>Your cart is empty.</p></li>');
                $('.cart_total .total span').html(response.data.cart_subtotal);
                $('.cart-count').text(response.data.cart_count);
            }
        });
    });

    // --- 2. Xóa từng sản phẩm ---
    $(document).on('click', '.remove-from-cart', function (e) {
        e.preventDefault();
        var cart_key = $(this).data('cart-key');
        var $btn = $(this);

        $.post(wc_add_to_cart_params.ajax_url, { 
            action: 'woocommerce_remove_cart_item', 
            cart_item_key: cart_key 
        }, function (response) {
            if (response) {
                $btn.closest('li').remove();
                $('.cart_total .total span').html(response.cart_subtotal);
                $('.cart-count').text(response.cart_count);

                if(response.cart_count == 0){
                    $('.cart-product').html('<li><p>Your cart is empty.</p></li>');
                }
            }
        });
    });

    // --- 3. Bind tăng giảm số lượng ---
    bindQtyButtons();

});

function bindQtyButtons() {
    $(document).off('click', '.qty-btn-minus, .qty-btn-plus');

    $(document).on('click', '.qty-btn-minus, .qty-btn-plus', function (e) {
        e.preventDefault();

        var $input = $(this).siblings('input.input-qty');
        var cart_key = $(this).data('cart-key');
        var current_val = parseInt($input.val());

        if ($(this).hasClass('qty-btn-minus')) current_val = Math.max(1, current_val - 1);
        else current_val += 1;

        // Gửi AJAX cập nhật cart
        $.post(wc_add_to_cart_params.ajax_url, {
            action: 'update_cart_quantity',
            cart_item_key: cart_key,
            quantity: current_val
        }, function (response) {
            if (response) {
                $input.val(current_val); // cập nhật input
                $('.cart_total .total span').html(response.cart_subtotal); // subtotal
                $input.closest('.media-body').find('.quantity span')
                      .html(current_val + ' × ' + response.product_price); // giá
                $('.cart-count').text(response.cart_count); // badge
            }
        });
    });
}
