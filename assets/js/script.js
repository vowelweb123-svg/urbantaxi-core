/**
 * UrbanTaxi Sticky Mission - Card stacking + scroll tabs
 */
(function($) {
    'use strict';

    let animationObserver;

    $(document).ready(initAll);
    $(window).on('load', initAll);

    function initAll() {
        initStickyMission();
        initCardAnimations();
        initScrollTabs();
    }

    /**
     * Initialize card entrance animations using Intersection Observer
     */
    function initCardAnimations() {
        const cards = $('.urbantaxi-card');

        if (cards.length === 0) return;

        if ('IntersectionObserver' in window) {
            if (animationObserver) {
                animationObserver.disconnect();
            }

            animationObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('animate-in');
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.2,
                rootMargin: '0px 0px -50px 0px'
            });

            cards.each(function() {
                animationObserver.observe(this);
            });
        } else {
            cards.addClass('animate-in');
        }
    }

    /**
     * Handle sticky stacking state per card
     */
    function initStickyMission() {
        const $wrapper = $('.urbantaxi-mission-wrapper');
        const $cards = $wrapper.find('.urbantaxi-card');

        // remove any previous namespaced handlers to allow re-init on breakpoint changes
        $(window).off('scroll.urbantaxiStack resize.urbantaxiStack');

        // Disable sticky behavior for small screens — allow normal flow so content is fully visible
        const windowWidth = $(window).width();
        if (windowWidth <= 640) {
            // ensure cards are non-sticky and wrapper height is cleared
            $cards.css({ position: 'relative', top: 'auto', 'z-index': 'auto', 'margin-top': '' });
            $('.urbantaxi-cards-wrapper').css('min-height', '');
            // disconnect animation observer so animations don't conflict on mobile
            if (animationObserver) {
                animationObserver.disconnect();
            }
            return;
        }

        if ($cards.length === 0) return;

        // Get settings from data attributes (responsive values from Elementor)
        const baseTop = parseInt($wrapper.attr('data-base-top')) || 100;
        const gap = parseInt($wrapper.attr('data-cards-gap')) || 50;

        // Set wrapper height FIRST so sticky behavior works correctly
        function setWrapperHeight() {

            let totalCardsHeight = 0;

            $cards.each(function() {
                totalCardsHeight += $(this).outerHeight(true);
            });

            const totalOffset = baseTop + (($cards.length - 1) * gap);

            const finalHeight = totalCardsHeight + totalOffset;

            $('.urbantaxi-cards-wrapper')
                .css('min-height', finalHeight + 'px');
        }

        setWrapperHeight();

        // Small delay to ensure layout settles after min-height is set
        setTimeout(function() {
            // Now record original offsets AFTER wrapper height is set (layout has expanded)
            $cards.each(function(index) {
                const $c = $(this);
                $c.data('origOffset', $c.offset().top);
            });

            // apply computed top and z-index so nth-child CSS won't be required
            $cards.each(function(index) {
                const topValue = baseTop + (index * gap);
                $(this).data('targetTop', topValue);
                $(this).css({ top: topValue + 'px', 'z-index': 100 + index });
            });

            // initial run after everything is set
            updateStackReleaseAndStates();
        }, 100);

        // release logic: when page scroll passes stacked area, add class to release
        let ticking = false;

        function updateStackReleaseAndStates() {

            const windowScrollTop = $(window).scrollTop();

            $cards.each(function() {
                const $c = $(this);
                const topVal = parseInt($c.css('top')) || 0;

                // determine when this card should be considered 'stacked'
                const orig = parseInt($c.data('origOffset')) || 0;
                const trigger = orig - topVal;

                if (windowScrollTop >= trigger - 5) {
                    $c.addClass('stacked');
                } else {
                    $c.removeClass('stacked');
                }
            });

            // Check if all cards are stacked
            const stackedCount = $cards.filter('.stacked').length;
            const allStacked = stackedCount === $cards.length;

            if (allStacked) {
                // Convert to flowing layout while in the stacked region
                if (!$wrapper.hasClass('all-stacked')) {
                    $wrapper.addClass('all-stacked');

                    // Set dynamic margins based on Elementor settings
                    $cards.each(function(index) {
                        const $c = $(this);
                        if (index === 0) {
                            $c.css('margin-top', baseTop + 'px');
                        } else {
                            $c.css('margin-top', gap + 'px');
                        }
                    });
                }
            } else {
                // If leaving the stacked region, remove flowing layout adjustments
                if ($wrapper.hasClass('all-stacked')) {
                    $wrapper.removeClass('all-stacked');
                    $cards.each(function() {
                        $(this).css('margin-top', '');
                    });
                }
            }
        }

        $(window).off('scroll.urbantaxiStack').on('scroll.urbantaxiStack', function() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(function() {
                updateStackReleaseAndStates();
                ticking = false;
            });
        });

        $(window).off('resize.urbantaxiStack').on('resize.urbantaxiStack', function() {
            // recalc wrapper height first
            setWrapperHeight();
            
            // then recalc original offsets after layout adjusts
            setTimeout(function() {
                $cards.each(function() {
                    const $c = $(this);
                    $c.data('origOffset', $c.offset().top);
                });
                updateStackReleaseAndStates();
            }, 100);
        });
    }

    /**
     * Sync tabs with scroll position
     */
    function initScrollTabs() {
        const cards = $('.urbantaxi-card');
        const tabs = $('.urbantaxi-tab');

        if (cards.length === 0 || tabs.length === 0) return;

        let ticking = false;

        $(window).off('scroll.urbantaxiTabs').on('scroll.urbantaxiTabs', function() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(function() {
                updateActiveTab();
                ticking = false;
            });
        });

        $(window).off('resize.urbantaxiTabs').on('resize.urbantaxiTabs', function() {
            updateActiveTab();
        });

        updateActiveTab();

        function updateActiveTab() {
            const windowScrollTop = $(window).scrollTop();
            let activeIndex = 0;

            const windowWidth = $(window).width();

            if (windowWidth <= 768) {
                // On small screens calculate active card by viewport center proximity
                const viewportCenter = $(window).height() / 2;
                let minDist = Infinity;
                cards.each(function(index) {
                    const rect = this.getBoundingClientRect();
                    const cardCenter = rect.top + rect.height / 2;
                    const dist = Math.abs(cardCenter - viewportCenter);
                    if (dist < minDist) {
                        minDist = dist;
                        activeIndex = index;
                    }
                });

                // If at bottom of document, activate last tab
                const windowHeight = $(window).height();
                if (windowScrollTop + windowHeight >= $(document).height() - 100) {
                    activeIndex = cards.length - 1;
                }
            } else {
                // Desktop: activate tab slightly before the card gets .stacked
                // so the highlight matches overlap timing without changing stack logic
                const $wrapper = $('.urbantaxi-mission-wrapper');
                const gap = parseInt($wrapper.attr('data-cards-gap'), 10) || 50;
                const earlyOffset = Math.max(gap + 30, 80);

                cards.each(function(index) {
                    const $card = $(this);
                    const orig = parseInt($card.data('origOffset'), 10);
                    const topVal = parseInt($card.data('targetTop'), 10) ||
                        parseInt($card.css('top'), 10) || 0;

                    if (!isNaN(orig)) {
                        const trigger = orig - topVal;
                        if (windowScrollTop >= trigger - earlyOffset) {
                            activeIndex = index;
                        }
                    } else if ($card.hasClass('stacked')) {
                        activeIndex = index;
                    }
                });

                // If at bottom of document, activate last tab
                const windowHeight = $(window).height();
                if (windowScrollTop + windowHeight >= $(document).height() - 100) {
                    activeIndex = cards.length - 1;
                }
            }

            tabs.removeClass('is-active');
            tabs.filter('[data-index="' + activeIndex + '"]').addClass('is-active');
        }
    }

    /**
     * Cleanup observers when needed
     */
    function cleanup() {
        if (animationObserver) {
            animationObserver.disconnect();
        }
    }

    $(window).on('beforeunload', cleanup);

    $(document).on('mouseenter', '.urbantaxi-card', function() {
        const $card = $(this);
        if (!$card.hasClass('stacked')) {
            $card.css({
                'transform': $card.css('transform') + ' translateZ(0)'
            });
        }
    });

    $(document).on('mouseleave', '.urbantaxi-card', function() {
        const $card = $(this);
        if (!$card.hasClass('stacked')) {
            const currentTransform = $card.css('transform');
            if (currentTransform.includes('translateZ')) {
                $card.css({
                    'transform': currentTransform.replace(' translateZ(0)', '')
                });
            }
        }
    });
})(jQuery);
