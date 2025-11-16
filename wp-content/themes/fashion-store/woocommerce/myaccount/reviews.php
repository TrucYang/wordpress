<div class="tab-pane fade" id="tab-reviews">
    <div class="row">
        <div class="card mb-0 dashboard-table mt-0">
            <div class="card-body">
                <div class="top-sec">
                    <h3>My Reviews</h3>
                </div>

                <div class="wallet-table mt-0">
                    <div class="table-responsive">
                        <?php
                        $user_id = get_current_user_id();
                        $args = array(
                            'user_id' => $user_id,
                            'post_type' => 'product',
                            'status' => 'approve',
                            'number' => 20,
                        );
                        $comments = get_comments( $args );

                        if ( empty( $comments ) ) {
                            echo '<div class="text-center p-4"><p>You haven\'t reviewed any products yet.</p></div>';
                        } else {
                            echo '<table class="table cart-table order-table">';
                            echo '<thead>';
                            echo '<tr class="table-head">';
                            echo '<th>Product</th>';
                            echo '<th>Rating</th>';
                            echo '<th>Review</th>';
                            echo '<th>Date</th>';
                            echo '<th>Status</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            foreach ( $comments as $comment ) {
                                $product_id = $comment->comment_post_ID;
                                $product = wc_get_product( $product_id );
                                $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
                                $status = $comment->comment_approved == '1' ? 'Approved' : 'Pending';

                                if ( ! $product ) continue;

                                echo '<tr>';
                                echo '<td>';
                                echo '<div class="d-flex align-items-center gap-2">';
                                echo '<a href="' . esc_url( get_permalink( $product_id ) ) . '">';
                                $image_id = $product->get_image_id();
                                $image_url = $image_id ? wp_get_attachment_image_src( $image_id, 'thumbnail' )[0] : wc_placeholder_img_src( 'thumbnail' );
                                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">';
                                echo '</a>';
                                echo '<div>';
                                echo '<a href="' . esc_url( get_permalink( $product_id ) ) . '" class="fw-bold">' . esc_html( $product->get_name() ) . '</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</td>';

                                echo '<td>';
                                echo '<ul class="product-rating d-flex list-unstyled mb-0">';
                                for ( $i = 1; $i <= 5; $i++ ) {
                                    if ( $i <= $rating ) {
                                        echo '<li class="star-rating"><i class="ri-star-fill text-warning"></i></li>';
                                    } else {
                                        echo '<li class="star-rating"><i class="ri-star-line"></i></li>';
                                    }
                                }
                                echo '</ul>';
                                echo '</td>';

                                echo '<td>';
                                $comment_text = wp_trim_words( $comment->comment_content, 30 );
                                echo '<p class="mb-0">' . esc_html( $comment_text ) . '</p>';
                                echo '</td>';

                                echo '<td>' . esc_html( get_comment_date( 'M d, Y', $comment->comment_ID ) ) . '</td>';

                                echo '<td>';
                                if ( $status == 'Approved' ) {
                                    echo '<span class="badge bg-success">' . esc_html( $status ) . '</span>';
                                } else {
                                    echo '<span class="badge bg-warning">' . esc_html( $status ) . '</span>';
                                }
                                echo '</td>';

                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Products Available for Review -->
                <div class="mt-4">
                    <h4 class="mb-3">Products You Can Review</h4>
                    <?php
                    $customer_orders = wc_get_orders( array(
                        'customer_id' => $user_id,
                        'status' => array( 'wc-completed', 'wc-processing' ),
                        'limit' => -1,
                    ) );

                    $purchased_products = array();
                    foreach ( $customer_orders as $order ) {
                        foreach ( $order->get_items() as $item ) {
                            $product_id = $item->get_product_id();
                            if ( ! in_array( $product_id, $purchased_products ) ) {
                                $purchased_products[] = $product_id;
                            }
                        }
                    }

                    $reviewed_products = array();
                    foreach ( $comments as $comment ) {
                        $reviewed_products[] = $comment->comment_post_ID;
                    }

                    $products_to_review = array_diff( $purchased_products, $reviewed_products );

                    if ( empty( $products_to_review ) ) {
                        echo '<p class="text-muted">You have reviewed all your purchased products.</p>';
                    } else {
                        echo '<div class="row g-3">';
                        foreach ( array_slice( $products_to_review, 0, 6 ) as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( ! $product ) continue;

                            echo '<div class="col-md-4">';
                            echo '<div class="card">';
                            echo '<div class="card-body">';
                            echo '<div class="d-flex align-items-center gap-2 mb-2">';
                            $image_id = $product->get_image_id();
                            $image_url = $image_id ? wp_get_attachment_image_src( $image_id, 'thumbnail' )[0] : wc_placeholder_img_src( 'thumbnail' );
                            echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">';
                            echo '<div class="flex-grow-1">';
                            echo '<h6 class="mb-0"><a href="' . esc_url( get_permalink( $product_id ) ) . '">' . esc_html( $product->get_name() ) . '</a></h6>';
                            echo '<p class="text-muted mb-0 small">' . $product->get_price_html() . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '<a href="' . esc_url( get_permalink( $product_id ) . '#reviews' ) . '" class="btn btn-sm w-100 write-review-btn">Write Review</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>
</div>

