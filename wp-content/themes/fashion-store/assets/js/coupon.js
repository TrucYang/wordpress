jQuery(document).ready(function($){

    $('.apply-button').on('click', function(e){
        e.preventDefault();

        var coupon_code = $('#coupon').val().trim();
        if (!coupon_code) return;

        $.ajax({
            type: 'POST',
            url: wc_checkout_params.ajax_url, 
            data: {
                action: 'apply_custom_coupon',
                coupon_code: coupon_code
            },
            success: function(response){
                if (response.success){
                    $('.coupon-message').html('<p class="success">Đã áp dụng mã giảm giá thành công.</p>');
                    $('body').trigger('update_checkout');
                } else {
                    $('.coupon-message').html('<p class="error">' + response.data.message + '</p>');
                }
            }
        });

    });

});


jQuery(document).on('change', 'input[name="shipping_method[0]"]', function(){
    jQuery('body').trigger('update_checkout'); 
});
