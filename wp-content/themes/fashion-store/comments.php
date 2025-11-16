<?php
if ( post_password_required() ) return;
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            $count = get_comments_number();
            if ( $count == 1 ) {
                echo '1 Comment';
            } else {
                echo $count . ' Comments';
            }
            ?>
        </h2>

        <ul class="comment-list">
            <?php
            wp_list_comments( array(
                'style'      => 'ul',
                'short_ping' => true,
                'avatar_size'=> 60,
                'callback'   => 'custom_comment_layout'
            ) );
            ?>
        </ul>

        <?php the_comments_pagination(array(
            'prev_text' => '← Older Comments',
            'next_text' => 'Newer Comments →',
        )); ?>

    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply' => 'Leave a Comment',
        'label_submit' => 'Post Comment',
        'comment_notes_after' => '',
        'class_submit' => 'btn-submit-comment'
    ));
    ?>

</div>

<?php
function custom_comment_layout($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class('single-comment'); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-avatar"><?php echo get_avatar($comment, 60); ?></div>
        <div class="comment-content">
            <div class="comment-meta">
                <span class="comment-author"><?php echo get_comment_author_link(); ?></span>
                <span class="comment-date"><?php echo get_comment_date('j F, Y \a\t g:i a'); ?></span>
            </div>
            <div class="comment-text"><?php comment_text(); ?></div>
            <div class="reply"><?php comment_reply_link(array_merge($args, array('depth'=>$depth,'max_depth'=>$args['max_depth']))); ?></div>
        </div>
    </li>
<?php }
?>
