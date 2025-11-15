<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="multikart">
    <meta name="keywords" content="multikart">
    <meta name="author" content="multikart">

    <!-- Favicon -->
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.png"
        type="image/x-icon">

    <!-- Dynamic Title -->
    <title><?php bloginfo('name'); ?><?php wp_title('|'); ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&amp;family=Montserrat:ital,wght@0,100..900;1,100..900&amp;display=swap">

    <?php wp_head(); ?>
</head>

<body class="theme-color-1">

    <!-- header start -->
    <header id="site-header">
        <div class="top-header">
            <div class="mobile-fix-option"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="header-contact">
                            <ul>
                                <li>Welcome to Our store Multikart</li>
                                <li><i class="ri-phone-fill"></i>Call Us: 123 - 456 - 7890</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 text-end">
                        <ul class="header-dropdown">
                            <li class="mobile-wishlist"><a href="#!"><i class="ri-heart-fill"></i></a>
                            </li>
                            <li class="onhover-dropdown mobile-account">
                                <i class="ri-user-fill"></i>
                                <?php if (is_user_logged_in()):
                                    $current_user = wp_get_current_user(); ?>
                                    Hello, <?php echo esc_html($current_user->display_name); ?>
                                    <ul class="onhover-show-div">
                                        <li><a href="<?php echo wc_get_page_permalink('myaccount'); ?>">My Account</a></li>
                                        <li><a href="<?php echo wc_get_account_endpoint_url('customer-logout'); ?>">Logout</a></i>
                                    </ul>
                                <?php else: ?>
                                    My Account
                                    <ul class="onhover-show-div">
                                        <li><a href="<?php echo site_url('/login'); ?>">Login</a></li>
                                        <li><a href="<?php echo site_url('/register'); ?>">Register</a></li>
                                    </ul>
                                <?php endif; ?>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="main-menu">
                        <div class="menu-left">
                            <div class="navbar">
                                <a href="#!" onclick="openNav()">
                                    <div class="bar-style"><i class="ri-bar-chart-horizontal-line sidebar-bar"></i>
                                    </div>
                                </a>
                                <div id="mySidenav" class="sidenav">
                                    <a href="#!" class="sidebar-overlay" onclick="closeNav()"></a>
                                    <nav>
                                        <div onclick="closeNav()">
                                            <div class="sidebar-back text-start"><i
                                                    class="ri-arrow-left-s-line pe-2"></i>
                                                Back</div>
                                        </div>
                                        <ul id="sub-menu" class="sm pixelstrap sm-vertical">
                                            <li> <a href="#!">clothing</a>
                                                <ul class="mega-menu clothing-menu">
                                                    <li>
                                                        <div class="row m-0">
                                                            <div class="col-xl-4">
                                                                <div class="link-section">
                                                                    <h5>women's fashion</h5>
                                                                    <ul>
                                                                        <li><a href="#!">dresses</a></li>
                                                                        <li><a href="#!">skirts</a></li>
                                                                        <li><a href="#!">western wear</a></li>
                                                                        <li><a href="#!">ethic wear</a></li>
                                                                        <li><a href="#!">sport wear</a></li>
                                                                    </ul>
                                                                    <h5>men's fashion</h5>
                                                                    <ul>
                                                                        <li><a href="#!">sports wear</a></li>
                                                                        <li><a href="#!">western wear</a></li>
                                                                        <li><a href="#!">ethic wear</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-4">
                                                                <div class="link-section">
                                                                    <h5>accessories</h5>
                                                                    <ul>
                                                                        <li><a href="#!">fashion jewellery</a>
                                                                        </li>
                                                                        <li><a href="#!">caps and hats</a></li>
                                                                        <li><a href="#!">precious jewellery</a>
                                                                        </li>
                                                                        <li><a href="#!">necklaces</a></li>
                                                                        <li><a href="#!">earrings</a></li>
                                                                        <li><a href="#!">wrist wear</a></li>
                                                                        <li><a href="#!">ties</a></li>
                                                                        <li><a href="#!">cufflinks</a></li>
                                                                        <li><a href="#!">pockets squares</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-4">
                                                                <a href="#!" class="mega-menu-banner"><img
                                                                        src="../assets/images/mega-menu/fashion.jpg"
                                                                        alt="" class="img-fluid blur-up lazyload"></a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li> <a href="#!">bags</a>
                                                <ul>
                                                    <li><a href="#!">shopper bags</a></li>
                                                    <li><a href="#!">laptop bags</a></li>
                                                    <li><a href="#!">clutches</a></li>
                                                    <li> <a href="#!">purses</a>
                                                        <ul>
                                                            <li><a href="#!">purses</a></li>
                                                            <li><a href="#!">wallets</a></li>
                                                            <li><a href="#!">leathers</a></li>
                                                            <li><a href="#!">satchels</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li> <a href="#!">bags</a>
                                                <ul>
                                                    <li><a href="#!">shopper bags</a></li>
                                                    <li><a href="#!">laptop bags</a></li>
                                                    <li><a href="#!">clutches</a></li>
                                                    <li> <a href="#!">purses</a>
                                                        <ul>
                                                            <li><a href="#!">purses</a></li>
                                                            <li><a href="#!">wallets</a></li>
                                                            <li><a href="#!">leathers</a></li>
                                                            <li><a href="#!">satchels</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li> <a href="#!">bags</a>
                                                <ul>
                                                    <li><a href="#!">shopper bags</a></li>
                                                    <li><a href="#!">laptop bags</a></li>
                                                    <li><a href="#!">clutches</a></li>
                                                    <li> <a href="#!">purses</a>
                                                        <ul>
                                                            <li><a href="#!">purses</a></li>
                                                            <li><a href="#!">wallets</a></li>
                                                            <li><a href="#!">leathers</a></li>
                                                            <li><a href="#!">satchels</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li> <a href="#!">footwear</a>
                                                <ul>
                                                    <li><a href="#!">sport shoes</a></li>
                                                    <li><a href="#!">formal shoes</a></li>
                                                    <li><a href="#!">casual shoes</a></li>
                                                </ul>
                                            </li>
                                            <li><a href="#!">watches</a></li>
                                            <li> <a href="#!">Accessories</a>
                                                <ul>
                                                    <li><a href="#!">fashion jewellery</a></li>
                                                    <li><a href="#!">caps and hats</a></li>
                                                    <li><a href="#!">precious jewellery</a></li>
                                                    <li> <a href="#!">more..</a>
                                                        <ul>
                                                            <li><a href="#!">necklaces</a></li>
                                                            <li><a href="#!">earrings</a></li>
                                                            <li><a href="#!">wrist wear</a></li>
                                                            <li> <a href="#!">accessories</a>
                                                                <ul>
                                                                    <li><a href="#!">ties</a></li>
                                                                    <li><a href="#!">cufflinks</a></li>
                                                                    <li><a href="#!">pockets squares</a></li>
                                                                    <li><a href="#!">helmets</a></li>
                                                                    <li><a href="#!">scarves</a></li>
                                                                    <li> <a href="#!">more...</a>
                                                                        <ul>
                                                                            <li><a href="#!">accessory gift
                                                                                    sets</a>
                                                                            </li>
                                                                            <li><a href="#!">travel
                                                                                    accessories</a>
                                                                            </li>
                                                                            <li><a href="#!">phone cases</a></li>
                                                                        </ul>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="#!">belts & more</a></li>
                                                            <li><a href="#!">wearable</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a href="#!">house of design</a></li>
                                            <li> <a href="#!">beauty & personal care</a>
                                                <ul>
                                                    <li><a href="#!">makeup</a></li>
                                                    <li><a href="#!">skincare</a></li>
                                                    <li><a href="#!">premium beauty</a></li>
                                                    <li> <a href="#!">more</a>
                                                        <ul>
                                                            <li><a href="#!">fragrances</a></li>
                                                            <li><a href="#!">luxury beauty</a></li>
                                                            <li><a href="#!">hair care</a></li>
                                                            <li><a href="#!">tools & brushes</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a href="#!">home & decor</a></li>
                                            <li><a href="#!">kitchen</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="brand-logo">
                                <?php if (function_exists('the_custom_logo'))
                                    the_custom_logo();
                                ?>
                            </div>
                        </div>
                        <div class="menu-right pull-right">
                            <div>
                                <nav id="main-nav">
                                    <div class="toggle-nav"><i class="ri-bar-chart-horizontal-line sidebar-bar"></i>
                                    </div>
                                    <?php
                                    wp_nav_menu(array(
                                        'theme_location' => 'primary',
                                        'container' => false,
                                        'menu_class' => 'sm pixelstrap sm-horizontal',
                                        'menu_id' => 'main-menu',
                                        'walker' => new FS_Walker_Nav_Menu()
                                    ));
                                    ?>
                                </nav>
                            </div>
                            <div>
                                <div class="icon-nav">
                                    <ul>
                                        <li class="onhover-div mobile-search">
                                            <div data-bs-toggle="modal" data-bs-target="#searchModal">
                                                <i class="ri-search-line"></i>
                                            </div>
                                        </li>
                                        <li class="onhover-div mobile-setting">
                                            <div><i class="ri-equalizer-2-line"></i></div>
                                            <div class="show-div setting">
                                                <h6>language</h6>
                                                <ul>
                                                    <li><a href="#!">english</a></li>
                                                    <li><a href="#!">french</a></li>
                                                </ul>
                                                <h6>currency</h6>
                                                <ul class="list-inline">
                                                    <li><a href="#!">euro</a></li>
                                                    <li><a href="#!">rupees</a></li>
                                                    <li><a href="#!">pound</a></li>
                                                    <li><a href="#!">dollar</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="onhover-div mobile-cart">
                                            <div data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                                                <i class="ri-shopping-cart-line"></i>
                                            </div>
                                            <span
                                                class="cart_qty_cls"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header end -->