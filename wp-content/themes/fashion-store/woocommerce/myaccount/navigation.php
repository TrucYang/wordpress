<?php
$user = wp_get_current_user();
$avatar_letter = strtoupper( substr( $user->display_name, 0, 1 ) );
?>

<div class="dashboard-sidebar">
    <button class="btn back-btn">
        <i class="ri-close-line"></i><span>Close</span>
    </button>

    <div class="profile-top">
        <div class="profile-top-box">
            <div class="profile-image">
                <div class="position-relative">
                    <div class="user-round">
                        <h4><?php echo $avatar_letter; ?></h4>
                    </div>
                    <div class="user-icon">
                        <input type="file" accept="image/*">
                        <i class="ri-image-edit-line d-lg-block d-none"></i>
                        <i class="ri-pencil-fill edit-icon d-lg-none"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-detail">
            <h5><?php echo esc_html( $user->display_name ); ?></h5>
            <h6><?php echo esc_html( $user->user_email ); ?></h6>
        </div>
    </div>

    <div class="faq-tab">
        <ul id="pills-tab" role="tablist" class="nav nav-tabs">
            <li role="presentation" class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-dashboard">
                    <i class="ri-home-line"></i> Dashboard
                </button>
            </li>
            <li role="presentation" class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notifications">
                    <i class="ri-notification-line"></i> Notifications
                </button>
            </li>
            <li role="presentation" class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-orders">
                    <i class="ri-file-text-line"></i> My Orders
                </button>
            </li>
            <li role="presentation" class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reviews">
                    <i class="ri-star-line"></i> My Reviews
                </button>
            </li>
            <li role="presentation" class="nav-item logout-cls">
                <a href="<?php echo wc_logout_url(); ?>" class="btn loagout-btn">
                    <i class="ri-logout-box-r-line"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
