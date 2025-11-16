<?php

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

// Get approved reviews/comments for this product
$args = array(
    'post_id' => $product->get_id(),
    'status' => 'approve',
    'type' => 'review',
);

$comments_query = new WP_Comment_Query;
$comments = $comments_query->query( $args );

$count = $product->get_review_count();
$average = $product->get_average_rating();
$rating_counts = $product->get_rating_counts();

// Get rating distribution
$rating_distribution = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
foreach ($rating_counts as $rating => $num) {
    $rating_distribution[$rating] = $num;
}
$total_ratings = array_sum($rating_distribution);
?>

<div id="reviews" class="woocommerce-Reviews">
    <div class="single-product-tables">
        <div class="row g-3 w-100">
            <!-- Rating Summary -->
            <div class="col-xl-5">
                <div class="product-rating-box">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <h2 class="mb-0 rating-number"><?php echo number_format($average, 2); ?></h2>
                                <div>
                                    <span class="base-rating">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $average ? '<i class="ri-star-s-fill"></i>' : '<i class="ri-star-s-line"></i>';
                                        }
                                        ?>
                                    </span>
                                    <h4 class="rating-count">Based on <?php echo $count; ?> <?php echo $count == 1 ? 'Rating' : 'Ratings'; ?></h4>
                                </div>
                            </div>

                            <div class="review-title-2">
                                <?php if ($count > 0) : ?>
                                <h4>Review this product</h4>
                                <p>Let other customers know what you think</p>
                                <ul class="product-rating-list">
                                    <?php for ($star = 5; $star >= 1; $star--) : 
                                        $star_count = isset($rating_distribution[$star]) ? $rating_distribution[$star] : 0;
                                        $percentage = $total_ratings > 0 ? ($star_count / $total_ratings) * 100 : 0;
                                    ?>
                                    <li>
                                        <div class="rating-product">
                                            <h5><?php echo $star; ?><i class="ri-star-fill"></i></h5>
                                            <div class="progress" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                            <h5 class="total"><?php echo $star_count; ?></h5>
                                        </div>
                                    </li>
                                    <?php endfor; ?>
                                </ul>
                                <?php else : ?>
                                <h4>Be the first to review this product</h4>
                                <p>Share your thoughts with other customers</p>
                                <?php endif; ?>
                                
                                <?php if (is_user_logged_in() && (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id()))) : ?>
                                <button class="btn write-review-btn" data-bs-toggle="modal" data-bs-target="#write-review-modal" type="button">
                                    Write Review
                                </button>
                                <?php elseif (!is_user_logged_in()) : ?>
                                <p class="text-muted small mb-2">You must be logged in to write a review.</p>
                                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="btn write-review-btn">Login to Review</a>
                                <?php else : ?>
                                <p class="text-muted small">Only customers who have purchased this product can leave a review.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="col-xl-7">
                <div class="review-people">
                    <?php if ( !empty( $comments ) ) : ?>
                        <ul class="review-list">
                            <?php
                            foreach ( $comments as $comment ) {
                                wc_get_template( 'single-product/review.php', array( 'comment' => $comment ) );
                            }
                            ?>
                        </ul>
                    <?php else : ?>
                        <p class="woocommerce-noreviews">There are no reviews yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Form (Hidden, will be shown in modal) -->
    <?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
        <div id="review_form_wrapper" style="display: none;">
            <div id="review_form">
                <?php
                $commenter    = wp_get_current_commenter();
                $comment_form = array(
                    'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'woocommerce' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
                    'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
                    'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
                    'title_reply_after'   => '</span>',
                    'comment_notes_after' => '',
                    'label_submit'        => esc_html__( 'Submit', 'woocommerce' ),
                    'logged_in_as'        => '',
                    'comment_field'       => '',
                );

                $name_email_required = (bool) get_option( 'require_name_email', 1 );
                $fields              = array(
                    'author' => array(
                        'label'        => __( 'Name', 'woocommerce' ),
                        'type'         => 'text',
                        'value'        => $commenter['comment_author'],
                        'required'     => $name_email_required,
                        'autocomplete' => 'name',
                    ),
                    'email'  => array(
                        'label'        => __( 'Email', 'woocommerce' ),
                        'type'         => 'email',
                        'value'        => $commenter['comment_author_email'],
                        'required'     => $name_email_required,
                        'autocomplete' => 'email',
                    ),
                );

                $comment_form['fields'] = array();

                foreach ( $fields as $key => $field ) {
                    $field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
                    $field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

                    if ( $field['required'] ) {
                        $field_html .= '&nbsp;<span class="required">*</span>';
                    }

                    $field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

                    $comment_form['fields'][ $key ] = $field_html;
                }

                $account_page_url = wc_get_page_permalink( 'myaccount' );
                if ( $account_page_url ) {
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
                }

                if ( wc_review_ratings_enabled() ) {
                    $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating" id="comment-form-rating-label">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
                        <option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
                        <option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
                        <option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
                        <option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
                        <option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
                        <option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
                    </select></div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
                ?>
            </div>
        </div>
    <?php else : ?>
        <p class="woocommerce-verification-required">Only logged in customers who have purchased this product may leave a review.</p>
    <?php endif; ?>

    <div class="clear"></div>
</div>

<!-- Write Review Modal -->
<?php if ( is_user_logged_in() && (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())) ) : ?>
<div class="modal fade theme-modal-2" id="write-review-modal" tabindex="-1" aria-labelledby="writeReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <i class="ri-close-line"></i>
            </button>
            <div class="modal-body">
                <h3 class="modal-title mb-4" id="writeReviewModalLabel">Write a Review</h3>
                <div id="review_form_wrapper_modal">
                    <?php
                    $commenter = wp_get_current_commenter();
                    $comment_form = array(
                        'title_reply'         => '',
                        'title_reply_to'      => '',
                        'title_reply_before'  => '',
                        'title_reply_after'   => '',
                        'comment_notes_after' => '',
                        'label_submit'        => esc_html__( 'Submit', 'woocommerce' ),
                        'logged_in_as'        => '',
                        'comment_field'       => '',
                        'id_form'             => 'commentform-modal',
                        'id_submit'           => 'submit-modal',
                        'class_form'          => 'comment-form',
                        'class_submit'        => 'btn btn-primary',
                    );

                    $name_email_required = (bool) get_option( 'require_name_email', 1 );
                    $fields = array(
                        'author' => array(
                            'label'        => __( 'Name', 'woocommerce' ),
                            'type'         => 'text',
                            'value'        => $commenter['comment_author'],
                            'required'     => $name_email_required,
                            'autocomplete' => 'name',
                        ),
                        'email'  => array(
                            'label'        => __( 'Email', 'woocommerce' ),
                            'type'         => 'email',
                            'value'        => $commenter['comment_author_email'],
                            'required'     => $name_email_required,
                            'autocomplete' => 'email',
                        ),
                    );

                    $comment_form['fields'] = array();
                    foreach ( $fields as $key => $field ) {
                        $field_html  = '<p class="comment-form-' . esc_attr( $key ) . ' mb-3">';
                        $field_html .= '<label for="' . esc_attr( $key ) . '-modal">' . esc_html( $field['label'] );
                        if ( $field['required'] ) {
                            $field_html .= '&nbsp;<span class="required">*</span>';
                        }
                        $field_html .= '</label><input id="' . esc_attr( $key ) . '-modal" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" class="form-control" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';
                        $comment_form['fields'][ $key ] = $field_html;
                    }

                    if ( wc_review_ratings_enabled() ) {
                        $comment_form['comment_field'] = '<div class="comment-form-rating mb-3"><label for="rating-modal">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><div class="star-rating-input d-flex gap-1">';
                        for ($i = 5; $i >= 1; $i--) {
                            $comment_form['comment_field'] .= '<input type="radio" name="rating" id="rating-' . $i . '-modal" value="' . $i . '" required style="display: none;"><label for="rating-' . $i . '-modal" class="star-label" style="cursor: pointer; font-size: 24px; color: #ddd;"><i class="ri-star-line"></i></label>';
                        }
                        $comment_form['comment_field'] .= '</div></div>';
                    }

                    $comment_form['comment_field'] .= '<p class="comment-form-comment mb-3"><label for="comment-modal">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment-modal" name="comment" class="form-control" cols="45" rows="6" required></textarea></p>';

                    comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

