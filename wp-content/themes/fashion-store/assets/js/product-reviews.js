(function($){
    'use strict';

    // Star rating input
    $(document).ready(function() {
        // Handle star rating click
        $('.star-rating-input input[type="radio"]').on('change', function() {
            var rating = $(this).val();
            var $labels = $(this).closest('.star-rating-input').find('label');
            
            $labels.each(function(index) {
                var labelIndex = $labels.length - index;
                if (labelIndex <= rating) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
        });

        // Initialize star rating display
        $('.star-rating-input label').on('mouseenter', function() {
            var $input = $(this).prev('input');
            var rating = $input.val();
            var $labels = $(this).closest('.star-rating-input').find('label');
            
            $labels.each(function(index) {
                var labelIndex = $labels.length - index;
                if (labelIndex <= rating) {
                    $(this).addClass('hover');
                } else {
                    $(this).removeClass('hover');
                }
            });
        });

        $('.star-rating-input').on('mouseleave', function() {
            $(this).find('label').removeClass('hover');
        });

        // Handle star rating visual feedback
        $('.star-rating-input label').on('click', function() {
            var $input = $(this).prev('input');
            var rating = $input.val();
            var $labels = $(this).closest('.star-rating-input').find('label');
            
            $labels.each(function(index) {
                var labelIndex = $labels.length - index;
                if (labelIndex <= rating) {
                    $(this).find('i').removeClass('ri-star-line').addClass('ri-star-fill');
                    $(this).css('color', '#ffc107');
                } else {
                    $(this).find('i').removeClass('ri-star-fill').addClass('ri-star-line');
                    $(this).css('color', '#ddd');
                }
            });
        });

        // Hover effect for stars
        $('.star-rating-input label').on('mouseenter', function() {
            var $input = $(this).prev('input');
            var rating = $input.val();
            var $labels = $(this).closest('.star-rating-input').find('label');
            var currentIndex = $labels.index($(this));
            var hoverRating = $labels.length - currentIndex;
            
            $labels.each(function(index) {
                var labelIndex = $labels.length - index;
                if (labelIndex <= hoverRating) {
                    $(this).css('color', '#ffc107');
                } else {
                    $(this).css('color', '#ddd');
                }
            });
        });

        $('.star-rating-input').on('mouseleave', function() {
            var $checked = $(this).find('input:checked');
            if ($checked.length) {
                var rating = $checked.val();
                var $labels = $(this).find('label');
                
                $labels.each(function(index) {
                    var labelIndex = $labels.length - index;
                    if (labelIndex <= rating) {
                        $(this).css('color', '#ffc107');
                    } else {
                        $(this).css('color', '#ddd');
                    }
                });
            } else {
                $(this).find('label').css('color', '#ddd');
            }
        });
    });

})(jQuery);

