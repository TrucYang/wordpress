<div class="tab-pane fade" id="tab-orders">
    <div class="row">
        <div class="card mb-0 dashboard-table mt-0">
            <div class="card-body">
                <div class="top-sec">
                    <h3>My Orders</h3>
                </div>

                <div class="wallet-table mt-0">
                    <div class="table-responsive">
                        <table class="table cart-table order-table">
                            <thead>
                                <tr class="table-head">
                                    <th>Order</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Cancel</th>
                                    <th>View</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders = wc_get_orders([
                                    'customer_id' => get_current_user_id(),
                                    'limit' => -1
                                ]);

                                if (empty($orders)) {
                                    echo '<tr><td colspan="6">No orders found.</td></tr>';
                                } else {
                                    foreach ($orders as $order) {
                                        echo '<tr>';
                                        echo '<td>#' . $order->get_id() . '</td>';
                                        echo '<td>' . wc_format_datetime($order->get_date_created()) . '</td>';
                                        echo '<td>' . $order->get_formatted_order_total() . '</td>';
                                        echo '<td class="status">' . wc_get_order_status_name($order->get_status()) . '</td>';
                                        echo '<td>' . $order->get_payment_method_title() . '</td>';
                                        echo '<td>';
                                        if (in_array($order->get_status(), ['pending', 'on-hold', 'failed'])) {
                                            echo '<a href="#" class="cancel-order-btn" data-order-id="' . esc_attr($order->get_id()) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel Order">
                                                    <i class="ri-close-circle-line"></i>
                                                 </a>';
                                        } else {
                                            echo '-';
                                        }
                                        echo '</td>';

                                        echo '<td><a href="' . $order->get_view_order_url() . '"><i class="ri-eye-line"></i></a></td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Confirmation Modal -->
<div class="modal fade theme-modal-2" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <i class="ri-close-line"></i>
            </button>
            <div class="modal-body text-center">
                <div class="cancel-order-icon mb-3">
                    <i class="ri-error-warning-line" style="font-size: 64px; color: #ff6b6b;"></i>
                </div>
                <h3 class="modal-title mb-3" id="cancelOrderModalLabel">Cancel Order?</h3>
                <p class="mb-4">Are you sure you want to cancel this order? This action cannot be undone.</p>
                <div class="modal-buttons d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> No, Keep Order
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmCancelOrder">
                        <i class="ri-check-line me-1"></i> Yes, Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>