(function ($) {
    function initHeroSlider($slider) {
        if (typeof $.fn.slick === 'undefined') return;
        if ($slider.data('slick-initialized')) return;
        $slider.slick({
            autoplay: $slider.data('autoplay') === true || $slider.data('autoplay') === 'true',
            autoplaySpeed: parseInt($slider.data('speed'), 10) || 5000,
            arrows: true,
            dots: true,
            adaptiveHeight: true,
        });
        $slider.data('slick-initialized', true);
    }

    function initTestimonialSlider($slider) {
        if (typeof $.fn.slick === 'undefined') return;
        if ($slider.data('slick-initialized')) return;
        $slider.slick({
            autoplay: $slider.data('autoplay') === true || $slider.data('autoplay') === 'true',
            arrows: false,
            dots: true,
            slidesToShow: 2,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                    },
                },
            ],
        });
        $slider.data('slick-initialized', true);
    }

    $(function () {
        $('.fs-hero-slider').each(function () {
            initHeroSlider($(this));
        });

        $('.fs-testimonial-slider').each(function () {
            initTestimonialSlider($(this));
        });
    });
})(jQuery);

