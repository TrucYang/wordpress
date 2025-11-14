jQuery(function($){
    $(document).on('click', '.wpplugines-survey-notice .notice-dismiss, .wpplugines-survey-btn', function(e) {
        $(this).prop('disabled', true);
        var $slug = $(this).closest('.wpplugines-survey-notice').data('slug')
        $.ajax({
            url: ajaxurl,
            data: { 'action' : $slug + '_survey', 'participate' : $(this).data('participate') },
            type: 'POST',
            success: function(ret) {
                $('#'+$slug+'-survey-notice').slideToggle(500)
            }
        })
    })
})