(function($) {

    const sliders = jQuery('.main-simpleslider-container .main-slider');
    
    if(sliders.length) {

        let number_sliders = jQuery('.main-simpleslider-container .slide').length;

        const settings = {
            infinite: true,
            autoplay: false,
            autoplaySpeed: 4000,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: number_sliders > 1 ? true : false,
            arrows: number_sliders > 1 ? true : false,
            prevArrow: number_sliders > 1 ? jQuery('.main-simpleslider-container .prevArrow') : false,
            nextArrow: number_sliders > 1 ? jQuery('.main-simpleslider-container .nextArrow') : false
        }

        sliders.slick(settings);

    }

})(jQuery);