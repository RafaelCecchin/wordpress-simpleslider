(function($) {

    const sliders = jQuery('.main-simpleslider-container .main-slider');

    
    if(sliders.length) {

        const settings= {
            infinite: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            arrows: true,
            prevArrow: jQuery('.main-simpleslider-container .prevArrow'),
            nextArrow: jQuery('.main-simpleslider-container .nextArrow')
        }

        sliders.slick(settings);

    }

})(jQuery);