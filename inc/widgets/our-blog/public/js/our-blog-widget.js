(function ($) {

    function initUrbanTaxiBlog($scope) {
        $scope = $($scope);

        let wrapper;
        if ($scope.hasClass('urbantaxi-blog-slider-wrapper')) {
            wrapper = $scope;
        } else {
            wrapper = $scope.find('.urbantaxi-blog-slider-wrapper');
        }

        if (!wrapper.length) return;

        const slides   = parseInt(wrapper.data('slides')) || 3;
        const slides320  = parseInt(wrapper.data('slides-320')) || 1;
        const slides576  = parseInt(wrapper.data('slides-576')) || 1;
        const slides768  = parseInt(wrapper.data('slides-768')) || 2;
        const slides992  = parseInt(wrapper.data('slides-992')) || 2;
        const slides1025 = parseInt(wrapper.data('slides-1025')) || 3;
        const slides1200 = parseInt(wrapper.data('slides-1200')) || slides;

        const space    = parseInt(wrapper.data('space')) || 30;
        const autoplay = wrapper.data('autoplay') === 'yes';
        const loop     = wrapper.data('loop') === 'yes';
        const pagination = wrapper.data('pagination') === 'yes';
        const navigation = wrapper.data('navigation') === 'yes';

        // NEW
        const customPaginationEnabled = wrapper.data('custom-pagination') === 'yes';
        const paginationType = wrapper.data('pagination-type');
        const prevText = wrapper.data('prev-text') || 'Prev';
        const nextText = wrapper.data('next-text') || 'Next';


        //new for grid
        const enableGrid = wrapper.data('enable-grid') === 'yes';
        const gridRows = parseInt(wrapper.data('grid-rows')) || 1;
        const gridFill = wrapper.data('grid-fill') || 'row';

        const swiperEl = wrapper.find('.urbantaxi-blog-swiper')[0];
        if (!swiperEl) return;

        const swiper = new Swiper(swiperEl, {
            slidesPerView: slides,
            // Change slidesPerGroup to 1 to slide one item at a time
            slidesPerGroup: 1, // Always slide 1 item regardless of slides per view
            spaceBetween: space,
            loop: loop,
            autoplay: autoplay ? {
                delay: 3000,
                disableOnInteraction: false,
            } : false,

            grid: enableGrid ? {
                rows: gridRows,
                fill: gridFill
            } : undefined,

            // Disable default pagination if custom enabled
            pagination: (pagination && !customPaginationEnabled) ? {
                el: wrapper.find('.swiper-pagination')[0],
                clickable: true,
            } : false,

            navigation: navigation ? {
                nextEl: wrapper.find('.swiper-button-next')[0],
                prevEl: wrapper.find('.swiper-button-prev')[0],
            } : false,

            breakpoints: {
                320:  { 
                    slidesPerView: slides320,
                    slidesPerGroup: 1 // Ensure it slides 1 item at each breakpoint
                },
                576:  { 
                    slidesPerView: slides576,
                    slidesPerGroup: 1
                },
                768:  { 
                    slidesPerView: slides768,
                    slidesPerGroup: 1
                },
                992:  { 
                    slidesPerView: slides992,
                    slidesPerGroup: 1
                },
                1025: { 
                    slidesPerView: slides1025,
                    slidesPerGroup: 1
                },
                1200: { 
                    slidesPerView: slides1200,
                    slidesPerGroup: 1
                }
            }
        });

        /* =========================
           CUSTOM PAGINATION LOGIC
        ========================== */
        if (customPaginationEnabled) {

            const customWrapper = wrapper.find('.urbantaxi-custom-pagination')[0];
            if (!customWrapper) return;

            function renderCustomPagination() {

                customWrapper.innerHTML = '';
                customWrapper.setAttribute('role', 'navigation');
                customWrapper.setAttribute('aria-label', 'Blog posts pagination');

                // const totalSlides = swiper.slides.length - (swiper.loopedSlides || 0);
                const totalSlides = swiper.snapGrid.length;
                const activeIndex = swiper.realIndex;

                function createPagerButton(className, label, text, isDisabled) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = className;
                    button.setAttribute('aria-label', label);
                    if (text !== null) {
                        button.innerText = text;
                    }
                    if (isDisabled) {
                        button.disabled = true;
                        button.classList.add('disabled');
                    }
                    return button;
                }

                // =========================
                // TEXT ONLY MODE
                // =========================
                if (paginationType === 'text') {

                    const prevBtn = createPagerButton('utx-prev', 'Previous slide', prevText, !loop && swiper.isBeginning);
                    prevBtn.onclick = () => swiper.slidePrev();
                    customWrapper.appendChild(prevBtn);

                    const nextBtn = createPagerButton('utx-next', 'Next slide', nextText, !loop && swiper.isEnd);
                    nextBtn.onclick = () => swiper.slideNext();
                    customWrapper.appendChild(nextBtn);

                    return;
                }

                // =========================
                // TEXT + NUMBER / TEXT + DOT
                // =========================
                if (paginationType === 'text_number' || paginationType === 'text_dot' || paginationType === 'number_arrow') {
                    const prevBtn = createPagerButton('utx-prev', 'Previous slide', paginationType === 'number_arrow' ? null : prevText, !loop && swiper.isBeginning);
                    if (paginationType === 'number_arrow') {
                        prevBtn.innerHTML = '<i class="fas fa-chevron-left" aria-hidden="true"></i>';
                    }
                    prevBtn.onclick = () => swiper.slidePrev();
                    customWrapper.appendChild(prevBtn);
                }

                // =========================
                // NUMBER / DOT / TEXT_NUMBER / TEXT_DOT
                // =========================
                if ( paginationType === 'number' ||  paginationType === 'text_number' || paginationType === 'number_arrow') {

                    const windowSize = 3; // show 3 dynamic numbers
                    let start = activeIndex;
                    let end = start + windowSize;

                    if (end > totalSlides) {
                        end = totalSlides;
                        start = end - windowSize;
                        if (start < 0) start = 0;
                    }

                    // First dynamic window numbers
                    for (let i = start; i < end; i++) {

                        const pageText = (i + 1) < 10 ? '0' + (i + 1) : (i + 1).toString();
                        const item = createPagerButton('utx-page-item', 'Go to slide ' + (i + 1), pageText, false);

                        if (i === activeIndex) {
                            item.classList.add('active');
                            item.setAttribute('aria-current', 'page');
                        }

                        item.onclick = () => swiper.slideToLoop(i);
                        customWrapper.appendChild(item);
                    }

                    // Add separator if needed
                    if (end < totalSlides - 1) {
                        const dots = document.createElement('span');
                        dots.className = 'utx-separator';
                        dots.innerText = '.....';
                        dots.setAttribute('aria-hidden', 'true');
                        customWrapper.appendChild(dots);
                    }

                    // Always show last number if not included
                    if (end <= totalSlides - 1) {
                        const lastText = totalSlides < 10 ? '0' + totalSlides : totalSlides.toString();
                        const lastItem = createPagerButton('utx-page-item', 'Go to slide ' + totalSlides, lastText, false);

                        if (activeIndex === totalSlides - 1) {
                            lastItem.classList.add('active');
                            lastItem.setAttribute('aria-current', 'page');
                        }

                        lastItem.onclick = () => swiper.slideToLoop(totalSlides - 1);
                        customWrapper.appendChild(lastItem);
                    }
                } else if(paginationType === 'dot' ||  paginationType === 'text_dot' ) {
                    for (let i = 0; i < totalSlides; i++) {

                        const item = createPagerButton('utx-page-item', 'Go to slide ' + (i + 1), null, false);

                        if (paginationType === 'dot' || paginationType === 'text_dot') {
                            item.classList.add('utx-dot');
                        }

                        if (i === activeIndex) {
                            item.classList.add('active');
                            item.setAttribute('aria-current', 'page');
                        }

                        item.onclick = () => swiper.slideToLoop(i);
                        customWrapper.appendChild(item);
                    }
                }


                // =========================
                // ADD NEXT FOR TEXT MODES
                // =========================
                if (paginationType === 'text_number' || paginationType === 'text_dot' || paginationType === 'number_arrow') {
                    const nextBtn = createPagerButton('utx-next', 'Next slide', paginationType === 'number_arrow' ? null : nextText, !loop && swiper.isEnd);
                    if (paginationType === 'number_arrow') {
                        nextBtn.innerHTML = '<i class="fas fa-chevron-right" aria-hidden="true"></i>';
                    }
                    nextBtn.onclick = () => swiper.slideNext();
                    customWrapper.appendChild(nextBtn);
                }
            }


            renderCustomPagination();

            swiper.on('slideChange', function () {
                renderCustomPagination();
            });
        }
    }

    function waitForElementorHooks(callback) {
        const interval = setInterval(function () {
            if (window.elementorFrontend && elementorFrontend.hooks) {
                clearInterval(interval);
                callback();
            }
        }, 50);
    }

    $(window).on('elementor/frontend/init', function () {
        waitForElementorHooks(function () {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/urbantaxi_our_blog.default',
                function ($scope) {
                    initUrbanTaxiBlog($scope);
                }
            );
        });
    });

    $(window).on('load', function () {
        $('.urbantaxi-blog-slider-wrapper').each(function () {
            initUrbanTaxiBlog($(this));
        });
    });

})(jQuery);