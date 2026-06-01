jQuery(window).on('elementor/frontend/init', function () {

    elementorFrontend.hooks.addAction(
        'frontend/element_ready/team_carousel.default',
        function ($scope) {

            var $carousel = $scope.find('.team-carousel');

            if ($carousel.length) {

                var desktop = $carousel.data('desktop') || 4;
                var laptop = $carousel.data('laptop') || 3;
                var tabletLandscape = $carousel.data('tablet-landscape') || 2;
                var tablet = $carousel.data('tablet') || 2;
                var mobileLandscape = $carousel.data('mobile-landscape') || 1;
                var mobile = $carousel.data('mobile') || 1;
                var loop = $carousel.data('loop') === 'yes';
                var dots = $carousel.data('dots') === 'yes';

                var swiperOptions = {
                    slidesPerView: desktop,
                    spaceBetween: 20,
                    loop: loop,

                    watchOverflow: true,

                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },

                    breakpoints: {
                        320: { slidesPerView: mobile },        // Mobile: 320px-767px
                        768: { slidesPerView: mobileLandscape }, // Mobile Landscape: 768px-880px
                        881: { slidesPerView: tablet },        // Tablet: 881px-1024px
                        1025: { slidesPerView: tabletLandscape }, // Tablet Landscape: 1025px-1200px
                        1200: { slidesPerView: laptop },       // Laptop: 1201px-1366px
                        1367: { slidesPerView: desktop }       // Desktop: 1367px+
                    }
                };

                if (dots) {
                    swiperOptions.pagination = {
                        el: $scope.find('.swiper-pagination')[0],
                        clickable: true,
                    };
                }

                var swiperInstance = new Swiper($carousel[0], swiperOptions);

                // Pause autoplay while hovering a team card, then resume on leave.
                $scope.find('.team-item').on('mouseenter', function () {
                    if (swiperInstance.autoplay && swiperInstance.autoplay.running) {
                        swiperInstance.autoplay.stop();
                    }
                });

                $scope.find('.team-item').on('mouseleave', function () {
                    if (swiperInstance.autoplay && !swiperInstance.autoplay.running) {
                        swiperInstance.autoplay.start();
                    }
                });
            }
        }
    );

});