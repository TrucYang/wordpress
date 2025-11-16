<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

// Set global comment for WordPress functions
$GLOBALS['comment'] = $comment;

$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
$verified = wc_review_is_from_verified_owner( $comment->comment_ID );
$author = get_comment_author( $comment->comment_ID );
$author_initial = strtoupper( substr( $author, 0, 1 ) );
$date = get_comment_date( 'd M Y h:i:A', $comment->comment_ID );
$comment_text = get_comment_text( $comment->comment_ID );
?>

<li <?php comment_class( '', $comment->comment_ID ); ?> id="li-comment-<?php echo $comment->comment_ID; ?>">
    <div class="people-box">
        <div>
            <div class="people-image people-text">
                <div class="user-round">
                    <h4><?php echo esc_html( $author_initial ); ?></h4>
                </div>
            </div>
        </div>
        <div class="people-comment">
            <div class="people-name">
                <a href="#!" class="name"><?php echo esc_html( $author ); ?></a>
                <?php if ( $verified ) : ?>
                    <span class="verified-buyer badge bg-success ms-2">Verified Purchase</span>
                <?php endif; ?>
                <h6 class="text-content"><?php echo esc_html( $date ); ?></h6>
                <ul class="product-rating">
                    <?php
                    for ( $i = 1; $i <= 5; $i++ ) {
                        if ( $i <= $rating ) {
                            echo '<li class="star-rating"><i class="ri-star-fill"></i></li>';
                        } else {
                            echo '<li class="star-rating"><i class="ri-star-line"></i></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="reply">
                <p><?php echo wp_kses_post( $comment_text ); ?></p>
            </div>
        </div>
    </div>
</li>

