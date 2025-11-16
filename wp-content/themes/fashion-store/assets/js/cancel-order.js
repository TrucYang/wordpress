(function($){
    'use strict';

    var currentOrderId = null;
    var currentButton = null;

    // Open modal when cancel button is clicked
    $(document).on('click', '.cancel-order-btn', function(e){
        e.preventDefault();

        var $button = $(this);
        
        // Prevent click if already cancelled or disabled
        if ($button.hasClass('cancelled') || $button.prop('disabled')) {
            return;
        }

        var orderId = $button.data('order-id');
        if (!orderId) return;

        currentOrderId = orderId;
        currentButton = $button;
        
        // Reset modal content to original
        resetModalContent();
        
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
        modal.show();
    });

    // Handle confirm cancel button
    $(document).on('click', '#confirmCancelOrder', function(e){
        e.preventDefault();

        if (!currentOrderId || !currentButton) return;

        // Prevent double click
        if ($(this).hasClass('processing')) return;
        $(this).addClass('processing');

        var $button = currentButton;
        var orderId = currentOrderId;
        var $modal = $('#cancelOrderModal');
        var $modalBody = $modal.find('.modal-body');

        // Disable cancel button in table to prevent double click
        $button.prop('disabled', true).css('opacity', '0.5').addClass('cancelled');
        
        // Disable confirm button and show loading
        $('#confirmCancelOrder').prop('disabled', true).html('<i class="ri-loader-4-line me-1"></i> Cancelling...');
        $modal.find('.btn-outline-secondary').prop('disabled', true);
        $modal.find('.btn-close').prop('disabled', true);

        // Perform AJAX request
        $.ajax({
            url: cancelOrderData.ajax_url,
            type: 'POST',
            data: {
                action: 'cancel_customer_order',
                order_id: orderId,
                nonce: cancelOrderData.nonce
            },
            success: function(response){
                if(response.success){
                    // Update table row
                    var $row = $button.closest('tr');
                    $row.find('td.status').text('Cancelled');
                    $row.find('td:has(.cancel-order-btn)').html('-');
                    
                    // Change modal content to success message
                    $modalBody.html(
                        '<div class="cancel-order-icon mb-3">' +
                        '<i class="ri-checkbox-circle-line" style="font-size: 64px; color: #28a745;"></i>' +
                        '</div>' +
                        '<h3 class="modal-title mb-3" style="color: #28a745;">Order cancelled successfully.</h3>' +
                        '<p class="mb-4">Your order has been cancelled.</p>' +
                        '<div class="modal-buttons d-flex gap-3 justify-content-center">' +
                        '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">' +
                        '<i class="ri-close-line me-1"></i> Close' +
                        '</button>' +
                        '</div>'
                    );

                    // Auto close modal after 2 seconds
                    setTimeout(function(){
                        var modal = bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal'));
                        if (modal) {
                            modal.hide();
                        }
                    }, 2000);
                } else {
                    // Show error message in modal
                    $modalBody.html(
                        '<div class="cancel-order-icon mb-3">' +
                        '<i class="ri-error-warning-line" style="font-size: 64px; color: #dc3545;"></i>' +
                        '</div>' +
                        '<h3 class="modal-title mb-3" style="color: #dc3545;">Error</h3>' +
                        '<p class="mb-4">' + (response.data.message || cancelOrderData.error_message) + '</p>' +
                        '<div class="modal-buttons d-flex gap-3 justify-content-center">' +
                        '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">' +
                        '<i class="ri-close-line me-1"></i> Close' +
                        '</button>' +
                        '</div>'
                    );
                    $button.prop('disabled', false).css('opacity', '1').removeClass('cancelled');
                }
            },
            error: function(){
                // Show error message in modal
                $modalBody.html(
                    '<div class="cancel-order-icon mb-3">' +
                    '<i class="ri-error-warning-line" style="font-size: 64px; color: #dc3545;"></i>' +
                    '</div>' +
                    '<h3 class="modal-title mb-3" style="color: #dc3545;">Error</h3>' +
                    '<p class="mb-4">' + cancelOrderData.error_message + '</p>' +
                    '<div class="modal-buttons d-flex gap-3 justify-content-center">' +
                    '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">' +
                    '<i class="ri-close-line me-1"></i> Close' +
                    '</button>' +
                    '</div>'
                );
                $button.prop('disabled', false).css('opacity', '1').removeClass('cancelled');
            }
        });
    });

    // Reset modal content function
    function resetModalContent() {
        var $modal = $('#cancelOrderModal');
        $modal.find('.modal-body').html(
            '<div class="cancel-order-icon mb-3">' +
            '<i class="ri-error-warning-line" style="font-size: 64px; color: #ff6b6b;"></i>' +
            '</div>' +
            '<h3 class="modal-title mb-3" id="cancelOrderModalLabel">Cancel Order?</h3>' +
            '<p class="mb-4">Are you sure you want to cancel this order? This action cannot be undone.</p>' +
            '<div class="modal-buttons d-flex gap-3 justify-content-center">' +
            '<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">' +
            '<i class="ri-close-line me-1"></i> No, Keep Order' +
            '</button>' +
            '<button type="button" class="btn btn-danger" id="confirmCancelOrder">' +
            '<i class="ri-check-line me-1"></i> Yes, Cancel Order' +
            '</button>' +
            '</div>'
        );
    }

    // Reset button state when modal is closed
    $('#cancelOrderModal').on('hidden.bs.modal', function () {
        resetModalContent();
        $('#confirmCancelOrder').removeClass('processing');
        currentOrderId = null;
        currentButton = null;
    });

    // Initialize tooltips
    $(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Show notification function
    function showNotification(type, message) {
        // Check if Bootstrap notify is available
        if (typeof $.notify !== 'undefined') {
            $.notify({
                message: message
            }, {
                type: type === 'success' ? 'success' : 'danger',
                placement: {
                    from: 'top',
                    align: 'center'
                },
                delay: 3000
            });
        } else {
            // Fallback to alert if notify is not available
            alert(message);
        }
    }

})(jQuery);
