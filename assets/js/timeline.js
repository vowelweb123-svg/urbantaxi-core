/**
 * UrbanTaxi Timeline Widget JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initTimeline();
        
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                initTimeline();
            }, 250);
        });
    });

    function getExpandedStep() {
        const w = window.innerWidth; 
        
        console.log('w', w);

        // tweak these values as you like
        if (w <= 425)  return 450;
        if (w <= 576)  return 450;  // mobile
        if (w <= 768)  return 300;  // large mobile / small tablets
        if (w <= 1024) return 350;  // tablet
        return 250;                // desktop
    }

    function getStickyThresholds($timeline) {
        const w = window.innerWidth;
        
        // Get data attributes from the timeline container
        const stickyStartDesktop = parseFloat($timeline.data('sticky-start-desktop')) || 0.3;
        const stickyStartTablet = parseFloat($timeline.data('sticky-start-tablet')) || 0.4;
        const stickyStartMobile = parseFloat($timeline.data('sticky-start-mobile')) || 0.5;
        
        const stickyEndDesktop = parseFloat($timeline.data('sticky-end-desktop')) || 0.7;
        const stickyEndTablet = parseFloat($timeline.data('sticky-end-tablet')) || 0.6;
        const stickyEndMobile = parseFloat($timeline.data('sticky-end-mobile')) || 0.5;
        
        let startThreshold, endThreshold;
        
        // Determine which breakpoint we're in
        if (w <= 576) { // mobile
            startThreshold = stickyStartMobile;
            endThreshold = stickyEndMobile;
        } else if (w <= 1024) { // tablet
            startThreshold = stickyStartTablet;
            endThreshold = stickyEndTablet;
        } else { // desktop
            startThreshold = stickyStartDesktop;
            endThreshold = stickyEndDesktop;
        }
        
        return {
            start: startThreshold,
            end: endThreshold
        };
    }
    
    function initTimeline() {
        const $timeline = $('.urbantaxi-timeline-container');
        
        if ($timeline.length === 0) {
            return;
        }
        
        // Add fast transition to year elements to smooth text changes
        $timeline.find('.year-number').css('transition', 'color 0.05s ease-out, opacity 0.05s ease-out');
        
        const items = $('.timeline-item');
        const itemCount = items.length;
        
        // Store original positions for each item
        const expandedStep = getExpandedStep();

        console.log('expandedStep', expandedStep);
        
        
        const originalPositions = [];
        items.each(function(index) {
            const measuredTopRaw = $(this).position().top;
            const fallbackTop = index * expandedStep;
            const measuredTop = (typeof measuredTopRaw === 'number' && !isNaN(measuredTopRaw) && measuredTopRaw > 0) ? measuredTopRaw : fallbackTop;
            originalPositions[index] = {
                top: fallbackTop
            };
        });
        
        // Set all items to absolute positioning but in expanded positions
        items.addClass('stacking').each(function(index) {
            $(this).css({
                'transform': `translateY(${originalPositions[index].top}px)`,
                'z-index': itemCount - index,
                'opacity': 1
            });
        });

        // Compute dynamic heights for the timeline wrapper once
        const lastIndex = Math.max(0, itemCount - 1);
        const expandedHeight = ((originalPositions[lastIndex] && originalPositions[lastIndex].top) || 0) + $(items[lastIndex]).outerHeight() + 40;
        const stackedHeight = $(items[0]).outerHeight() + 40;
        // Capture wrapper top BEFORE changing its height
        const baseTimelineTop = $timeline.offset().top;

        // Reserve minimum timeline height so content below doesn't jump in
        // while the stacking animation/scroll region is active.
        $timeline.css('min-height', expandedHeight + 'px');
        
        let lastScrollY = 0;
        let ticking = false;
        const debugTimeline = false; // set true to see debug logs in console
        
        function updateTimeline() {
            const scrollTop = $(window).scrollTop();
            const timelineTop = baseTimelineTop;
            const timelineHeight = expandedHeight;
            const windowHeight = $(window).height();
            
            // Get responsive sticky thresholds
            const stickyThresholds = getStickyThresholds($timeline);
            
            // Define stacking zone using responsive thresholds
            const stackingStart = timelineTop - windowHeight * stickyThresholds.start;
            const stackingEnd = timelineTop + timelineHeight - windowHeight * stickyThresholds.end;
            const stackingDistance = stackingEnd - stackingStart;
            
            if (scrollTop < stackingStart) {
                // Before stacking - expanded layout
                items.removeClass('active').each(function(index) {
                    $(this).css({
                        'transform': `translateY(${originalPositions[index].top}px)`,
                        'z-index': itemCount - index,
                        'opacity': 1
                    });

                    // Restore original (display) year when not active
                    const $yearEl = $(this).find('.year-number');
                    const displayYear = $yearEl.attr('data-display-year') || $yearEl.text();
                    $yearEl.text(displayYear);
                });
            } else if (scrollTop >= stackingStart && scrollTop <= stackingEnd) {
                const progress = (scrollTop - stackingStart) / stackingDistance;
                const totalProgress = Math.min(Math.max(progress, 0), 1);

                // how far (in px) the whole stack should move up
                const maxDelta = originalPositions[lastIndex].top; // e.g. 1250
                const globalDelta = totalProgress * maxDelta;

                items.removeClass('active').each(function(index) {
                    const $item = $(this);
                    const originalTop = originalPositions[index].top;

                    // ✅ all items reduce by the same amount
                    const currentTop = Math.max(0, originalTop - globalDelta);

                    $item.css({
                        transform: `translateY(${currentTop}px)`,
                        zIndex: itemCount - index,
                        opacity: 1
                    });

                    const $yearEl = $item.find('.year-number');

                    // active when it reaches the top
                    if (currentTop <= 1) {
                        $item.addClass('active');
                        const completeYear = $yearEl.attr('data-complete-year');
                        if (completeYear) $yearEl.text(completeYear);
                    } else {
                        $yearEl.text($yearEl.attr('data-display-year') || $yearEl.text());
                    }
                });
            } else {
                // After stacking - all items stacked
                items.removeClass('active').each(function(index) {
                    $(this).css({
                        'transform': `translateY(0px)`,
                        'z-index': index + 10, // Reversed: last item gets highest z-index
                        'opacity': Math.max(0.7, 1 - (itemCount - 1 - index) * 0.15)
                    });
                });
                
                // Highlight the last item (newest year) in final stack
                items.last().addClass('active');
                // Override CSS z-index for active item with !important
                items.last()[0].style.setProperty('z-index', `${itemCount + 10}`, 'important');
                
                // set complete year for the active last item and restore others
                items.each(function(i) {
                    const $y = $(this).find('.year-number');
                    if (i === itemCount - 1) { // last item
                        const complete = $y.attr('data-complete-year');
                        if (complete) $y.text(complete);
                    } else {
                        const disp = $y.attr('data-display-year') || $y.text();
                        $y.text(disp);
                    }
                });
                // wrapper height left to CSS (no forced height)
            }
            
            ticking = false;
        }
        
        // Smooth scroll handling
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateTimeline);
                ticking = true;
            }
        }
        
        $(window).on('scroll', function() {
            requestTick();
        });
        
        // Initial call
        updateTimeline();
        
        // Initial trigger
        $(window).trigger('scroll');
        
        // Add hover effects for images
        $('.timeline-content').on('mouseenter', function() {
            $(this).find('.content-image img').addClass('image-zoomed');
        }).on('mouseleave', function() {
            $(this).find('.content-image img').removeClass('image-zoomed');
        });
        
        // Lazy loading for images (optional enhancement)
        if ('loading' in HTMLImageElement.prototype) {
            $('.content-image img').attr('loading', 'lazy');
        }
    }
    
})(jQuery);
