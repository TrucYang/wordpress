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
                    <tr>
                        <td>
                            <a href="product-page(accordian).html">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/fashion-1/product/17.jpg" class="img-fluid" alt="">
                            </a>
                        </td>
                        <td>
                            <a href="product-page(accordian).html">Orange Coords Set</a>
                            <div class="mobile-cart-content row">
                                <div class="col">
                                    <div class="qty-box">
                                        <div class="input-group qty-container">
                                            <button class="btn qty-btn-minus">
                                                <i class="ri-arrow-left-s-line"></i>
                                            </button>
                                            <input type="number" readonly="" name="qty"
                                                class="form-control input-qty" value="1">
                                            <button class="btn qty-btn-plus">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col table-price">
                                    <h2 class="td-color">$15.00</h2>
                                </div>
                                <div class="col">
                                    <h2 class="td-color">
                                        <a href="product-page(accordian).html" class="icon remove-btn">
                                            <i class="ri-close-line"></i>
                                        </a>
                                    </h2>
                                </div>
                            </div>
                        </td>
                        <td class="table-price">
                            <h2>$15.00</h2>
                        </td>
                        <td>
                            <div class="qty-box">
                                <div class="input-group qty-container">
                                    <button class="btn qty-btn-minus">
                                        <i class="ri-arrow-left-s-line"></i>
                                    </button>
                                    <input type="number" readonly="" name="qty" class="form-control input-qty"
                                        value="1">
                                    <button class="btn qty-btn-plus">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <h2 class="td-color">$15.00</h2>
                        </td>
                        <td>
                            <a href="#!" class="icon remove-btn">
                                <i class="ri-close-line"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td>
                            <a href="product-page(accordian).html">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/fashion-1/product/18.jpg" class="img-fluid" alt="">
                            </a>
                        </td>
                        <td><a href="product-page(accordian).html">Tan Cargo Shorts</a>
                            <div class="mobile-cart-content row">
                                <div class="col">
                                    <div class="qty-box">
                                        <div class="input-group qty-container">
                                            <button class="btn qty-btn-minus">
                                                <i class="ri-arrow-left-s-line"></i>
                                            </button>
                                            <input type="number" readonly="" name="qty"
                                                class="form-control input-qty" value="3">
                                            <button class="btn qty-btn-plus">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col table-price">
                                    <h2 class="td-color">$9.96 <del>$12.00</del></h2>
                                </div>
                                <div class="col">
                                    <h2 class="td-color">
                                        <a href="#!" class="icon remove-btn">
                                            <i class="ri-close-line"></i>
                                        </a>
                                    </h2>
                                </div>
                            </div>
                        </td>
                        <td class="table-price">
                            <h2>$9.96 <del>$12.00</del></h2>
                            <h6 class="theme-color">You Save : $2.04</h6>
                        </td>
                        <td>
                            <div class="qty-box">
                                <div class="input-group qty-container">
                                    <button class="btn qty-btn-minus">
                                        <i class="ri-arrow-left-s-line"></i>
                                    </button>
                                    <input type="number" readonly="" name="qty" class="form-control input-qty"
                                        value="3">
                                    <button class="btn qty-btn-plus">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <h2 class="td-color">$29.88</h2>
                        </td>
                        <td>
                            <a href="#!" class="icon remove-btn">
                                <i class="ri-close-line"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td>
                            <a href="product-page(accordian).html">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/product-details/product/17.jpg" class="img-fluid" alt="">
                            </a>
                        </td>
                        <td><a href="product-page(accordian).html">Gym Coords Set (Brown)</a>
                            <div class="mobile-cart-content row">
                                <div class="col">
                                    <div class="qty-box">
                                        <div class="input-group qty-container">
                                            <button class="btn qty-btn-minus">
                                                <i class="ri-arrow-left-s-line"></i>
                                            </button>
                                            <input type="number" readonly="" name="qty"
                                                class="form-control input-qty" value="1">
                                            <button class="btn qty-btn-plus">
                                                <i class="ri-arrow-right-s-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col table-price">
                                    <h2 class="td-color">$63.00</h2>
                                </div>
                                <div class="col">
                                    <h2 class="td-color">
                                        <a href="#!" class="icon remove-btn">
                                            <i class="ri-close-line"></i>
                                        </a>
                                    </h2>
                                </div>
                            </div>
                        </td>
                        <td class="table-price">
                            <h2>$20.00</h2>
                        </td>
                        <td>
                            <div class="qty-box">
                                <div class="input-group qty-container">
                                    <button class="btn qty-btn-minus">
                                        <i class="ri-arrow-left-s-line"></i>
                                    </button>
                                    <input type="number" readonly="" name="qty" class="form-control input-qty"
                                        value="1">
                                    <button class="btn qty-btn-plus">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <h2 class="td-color">$20.00</h2>
                        </td>
                        <td>
                            <a href="#!" class="icon remove-btn">
                                <i class="ri-close-line"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="d-md-table-cell d-none">total price :</td>
                        <td class="d-md-none">total price :</td>
                        <td>
                            <h2>$64.88</h2>
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