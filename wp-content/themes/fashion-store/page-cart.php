<?php get_header(); ?>
<!-- breadcrumb start -->
<div class="breadcrumb-section">
    <div class="container">
        <h2>Cart</h2>
        <nav class="theme-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Home</a>
                </li>
                <li class="breadcrumb-item active">Cart</li>
            </ol>
        </nav>
    </div>
</div>
<!-- breadcrumb End -->


<!--section start-->
<section class="cart-section section-b-space">
    <div class="container">
        <!-- <div class="cart_counter">
                <div class="countdownholder">
                    Your cart will be expired in<span id="timer"></span> minutes!
                </div>
                <a href="checkout.html" class="cart_checkout btn btn-solid btn-xs">check out</a>
            </div> -->
        <div class="table-responsive">
            <table class="table cart-table">
                <thead>
                    <tr class="table-head">
                        <th>image</th>
                        <th>product name</th>
                        <th>price</th>
                        <th>quantity</th>
                        <th>total</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (WC()->cart->get_cart_contents_count() == 0) {
                        echo '<tr><td colspan="6" class="text-center">Không có sản phẩm nào trong giỏ.</td></tr>';
                    } else {
                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item):
                            $_product = $cart_item['data'];
                            $product_id = $cart_item['product_id'];
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo get_permalink($product_id); ?>">
                                        <?php echo $_product->get_image('thumbnail'); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo get_permalink($product_id); ?>">
                                        <?php echo $_product->get_name(); ?>
                                    </a>
                                </td>
                                <td class="table-price">
                                    <h2><?php echo wc_price($_product->get_price()); ?></h2>
                                </td>
                                <td>
                                    <form action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                                        <div class="qty-box">
                                            <div class="input-group qty-container">
                                                <button type="submit" name="update_cart" value="1" class="btn qty-btn-minus"
                                                    onclick="this.form.quantity.value = Math.max(1, parseInt(this.form.quantity.value)-1)">
                                                    <i class="ri-arrow-left-s-line"></i>
                                                </button>
                                                <input type="number" name="cart[<?php echo $cart_item_key; ?>][qty]" min="1"
                                                    class="form-control input-qty"
                                                    value="<?php echo esc_attr($cart_item['quantity']); ?>">
                                                <button type="submit" name="update_cart" value="1" class="btn qty-btn-plus"
                                                    onclick="this.form.quantity.value = parseInt(this.form.quantity.value)+1">
                                                    <i class="ri-arrow-right-s-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <h2 class="td-color">
                                        <?php echo wc_price($cart_item['line_total']); ?>
                                    </h2>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
                                        class="icon remove-btn" title="Xóa sản phẩm">
                                        <i class="ri-close-line"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                    }
                    ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="4" class="d-md-table-cell d-none">Tổng cộng :</td>
                        <td class="d-md-none">Tổng cộng :</td>
                        <td>
                            <h2><?php echo WC()->cart->get_cart_total(); ?></h2>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row cart-buttons">
            <div class="col-6">
                <a href="category-page(category-slider).html" class="btn btn-solid text-capitalize">continue
                    shopping</a>
            </div>
            <div class="col-6">
                <a href="checkout.html" class="btn btn-solid text-capitalize">check out</a>
            </div>
        </div>
    </div>
</section>
<!--section end-->
<?php get_footer(); ?>