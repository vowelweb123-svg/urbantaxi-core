/**
 * Testimonial Slider Widget — Stacked Card Engine
 * Pure JS, no Swiper dependency needed for the slider logic.
 * Cards are positioned by data-pos attribute driven by CSS transforms.
 *
 * Layout logic (example with 5 slides, active index = 2):
 *   index 0 → pos -2  (far left,  behind)
 *   index 1 → pos -1  (near left, behind)
 *   index 2 → pos  0  (ACTIVE, centre, on top)
 *   index 3 → pos +1  (near right, behind)
 *   index 4 → pos +2  (far right, behind)
 *
 * Anything beyond ±2 gets pos="hidden" and sits behind the active card invisibly.
 */

(function () {
    'use strict';

    /* -------------------------------------------------------
       Constants
    ------------------------------------------------------- */
    var MAX_VISIBLE = 2; // hard visual cap supported by current CSS (-2 .. +2)
    var TABLET_BREAKPOINT = 1024;
    var MOBILE_BREAKPOINT = 767;

    /* -------------------------------------------------------
       TSWSlider — one instance per .tsw-testimonial-section
    ------------------------------------------------------- */
    function TSWSlider(section) {
        this.section      = section;
        this.stage        = section.querySelector('.tsw-stage');
        this.slides       = Array.prototype.slice.call(section.querySelectorAll('.tsw-slide'));
        this.prevBtn      = section.querySelector('.tsw-prev');
        this.nextBtn      = section.querySelector('.tsw-next');

        if (!this.stage || this.slides.length === 0) return;

        // Read config from data attribute on stage
        var raw = this.stage.getAttribute('data-tsw-settings') || '{}';
        try { this.cfg = JSON.parse(raw); } catch (e) { this.cfg = {}; }

        this.total       = this.slides.length;
        this.active      = 0;          // current active index
        this.animating   = false;
        this.autoTimer   = null;
        this.resizeTimer = null;
        this.dur         = (this.cfg.transition || 650);  // ms, matches CSS --tsw-dur
        this.maxVisible  = this._calcMaxVisible();

        this._bindEvents();
        this._render(true);            // initial render, no animation lock
        if (this.cfg.autoplay) this._startAuto();
    }

    /* -------------------------------------------------------
       Position assignment
    ------------------------------------------------------- */
    TSWSlider.prototype._posFor = function (index) {
        var n     = this.total;
        var active = this.active;
        // raw distance, can be negative
        var diff = index - active;

        // Wrap for infinite loop: find shortest path around the circle
        if (diff > n / 2)  diff -= n;
        if (diff < -n / 2) diff += n;

        if (diff === 0) return '0';
        if (diff >= -this.maxVisible && diff <= this.maxVisible) return String(diff);
        return 'hidden';
    };

    TSWSlider.prototype._toValidSlidesPerView = function (value) {
        var n = parseInt(value, 10);
        if (isNaN(n)) n = 5;
        if (n < 1) n = 1;
        if (n > 5) n = 5;
        // Stacked layout is symmetric around centre, so force odd values.
        if (n % 2 === 0) n -= 1;
        return n;
    };

    TSWSlider.prototype._currentSlidesPerView = function () {
        var cfg = this.cfg.slidesPerView || {};
        var width = window.innerWidth || document.documentElement.clientWidth || 1200;

        if (width <= MOBILE_BREAKPOINT) {
            return this._toValidSlidesPerView(cfg.mobile || cfg.tablet || cfg.desktop || 5);
        }
        if (width <= TABLET_BREAKPOINT) {
            return this._toValidSlidesPerView(cfg.tablet || cfg.desktop || 5);
        }
        return this._toValidSlidesPerView(cfg.desktop || 5);
    };

    TSWSlider.prototype._calcMaxVisible = function () {
        // 1 => 0 side cards, 3 => 1 side card, 5 => 2 side cards
        var sideVisible = Math.floor((this._currentSlidesPerView() - 1) / 2);
        if (sideVisible < 0) sideVisible = 0;
        if (sideVisible > MAX_VISIBLE) sideVisible = MAX_VISIBLE;
        return sideVisible;
    };

    TSWSlider.prototype._handleResize = function () {
        var self = this;
        if (this.resizeTimer) clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(function () {
            var nextMax = self._calcMaxVisible();
            if (nextMax !== self.maxVisible) {
                self.maxVisible = nextMax;
                self._render(true);
                self._updateAccessibility();
            }
        }, 120);
    };

    /* -------------------------------------------------------
       Render — set data-pos on every slide
    ------------------------------------------------------- */
    TSWSlider.prototype._render = function (instant) {
        var self = this;
        // If instant, briefly suppress transition for initial placement
        if (instant) {
            this.stage.style.transition = 'none';
            this.slides.forEach(function (s) { s.style.transition = 'none'; });
        }

        this.slides.forEach(function (slide, i) {
            var pos = self._posFor(i);
            slide.setAttribute('data-pos', pos);
        });

        if (instant) {
            // Re-enable transitions after a frame
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    self.slides.forEach(function (s) { s.style.transition = ''; });
                });
            });
        }
    };

    /* -------------------------------------------------------
       Go to index (with animation lock)
    ------------------------------------------------------- */
    TSWSlider.prototype._goTo = function (newIndex) {
        if (this.animating || newIndex === this.active) return;

        this.animating = true;
        this.stage.classList.add('is-animating');

        // Wrap index
        var n = this.total;
        newIndex = ((newIndex % n) + n) % n;

        this.active = newIndex;
        this._render(false);

        /* ======== ACCESSIBILITY CODE START ======== */
        this._updateAccessibility();
        /* ======== ACCESSIBILITY CODE END ======== */

        var self = this;
        setTimeout(function () {
            self.animating = false;
            self.stage.classList.remove('is-animating');
        }, this.dur + 50); // a touch of buffer
    };

    TSWSlider.prototype.next = function () { this._goTo(this.active + 1); };
    TSWSlider.prototype.prev = function () { this._goTo(this.active - 1); };

    /* -------------------------------------------------------
       Event binding
    ------------------------------------------------------- */
    TSWSlider.prototype._bindEvents = function () {
        var self = this;

        /* ======== ACCESSIBILITY CODE START ======== */
        if (this.prevBtn) {
            // Remove any tabindex to use natural button focus
            this.prevBtn.removeAttribute('tabindex');
            this.prevBtn.addEventListener('click', function () {
                self._stopAuto();
                self.prev();
                if (self.cfg.autoplay) self._startAuto();
            });
            // Allow Enter/Space on button when focused
            this.prevBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    self._stopAuto();
                    self.prev();
                    if (self.cfg.autoplay) self._startAuto();
                }
                // Arrow keys on button
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    self._stopAuto();
                    self.prev();
                    if (self.cfg.autoplay) self._startAuto();
                }
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    self._stopAuto();
                    self.next();
                    if (self.cfg.autoplay) self._startAuto();
                }
            });
        }

        if (this.nextBtn) {
            // Remove any tabindex to use natural button focus
            this.nextBtn.removeAttribute('tabindex');
            this.nextBtn.addEventListener('click', function () {
                self._stopAuto();
                self.next();
                if (self.cfg.autoplay) self._startAuto();
            });
            // Allow Enter/Space on button when focused
            this.nextBtn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    self._stopAuto();
                    self.next();
                    if (self.cfg.autoplay) self._startAuto();
                }
                // Arrow keys on button
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    self._stopAuto();
                    self.prev();
                    if (self.cfg.autoplay) self._startAuto();
                }
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    self._stopAuto();
                    self.next();
                    if (self.cfg.autoplay) self._startAuto();
                }
            });
        }
        /* ======== ACCESSIBILITY CODE END ======== */

        // Click on non-active slide → jump to it
        this.slides.forEach(function (slide, i) {
            slide.addEventListener('click', function () {
                var pos = slide.getAttribute('data-pos');
                if (pos !== '0') {
                    self._stopAuto();
                    self._goTo(i);
                    if (self.cfg.autoplay) self._startAuto();
                }
            });
            
            /* ======== ACCESSIBILITY CODE START ======== */
            // Allow arrow keys on slides when focused
            slide.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    self._stopAuto();
                    self.prev();
                    if (self.cfg.autoplay) self._startAuto();
                }
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    self._stopAuto();
                    self.next();
                    if (self.cfg.autoplay) self._startAuto();
                }
            });
            /* ======== ACCESSIBILITY CODE END ======== */
        });

        // Keyboard
        this.section.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft')  { e.preventDefault(); self._stopAuto(); self.prev(); if (self.cfg.autoplay) self._startAuto(); }
            if (e.key === 'ArrowRight') { e.preventDefault(); self._stopAuto(); self.next(); if (self.cfg.autoplay) self._startAuto(); }
            
            /* ======== ACCESSIBILITY CODE START ======== */
            // Allow Enter/Space to navigate to focused non-active slide
            if ((e.key === 'Enter' || e.key === ' ') && document.activeElement && document.activeElement.classList.contains('tsw-slide')) {
                var focusedSlide = document.activeElement;
                var focusedIndex = self.slides.indexOf(focusedSlide);
                if (focusedIndex !== -1 && focusedIndex !== self.active) {
                    e.preventDefault();
                    self._stopAuto();
                    self._goTo(focusedIndex);
                    if (self.cfg.autoplay) self._startAuto();
                }
            }
            /* ======== ACCESSIBILITY CODE END ======== */
        });
        
        /* ======== ACCESSIBILITY CODE START ======== */
        // Set section to tabindex="-1" so it doesn't interfere with tab order but can still receive keyboard events
        this.section.setAttribute('tabindex', '-1');
        // Ensure buttons are naturally focusable (no tabindex)
        if (this.prevBtn) this.prevBtn.setAttribute('aria-label', this.prevBtn.getAttribute('aria-label') || 'Previous slide');
        if (this.nextBtn) this.nextBtn.setAttribute('aria-label', this.nextBtn.getAttribute('aria-label') || 'Next slide');
        /* ======== ACCESSIBILITY CODE END ======== */

        // Touch / swipe
        var touchStartX = null;
        this.stage.addEventListener('touchstart', function (e) {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });

        this.stage.addEventListener('touchend', function (e) {
            if (touchStartX === null) return;
            var dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 40) {
                self._stopAuto();
                if (dx < 0) self.next(); else self.prev();
                if (self.cfg.autoplay) self._startAuto();
            }
            touchStartX = null;
        }, { passive: true });

        // Pause autoplay on hover
        this.section.addEventListener('mouseenter', function () { self._stopAuto(); });
        this.section.addEventListener('mouseleave', function () { if (self.cfg.autoplay) self._startAuto(); });

        window.addEventListener('resize', function () {
            self._handleResize();
        });

        /* ======== ACCESSIBILITY CODE START ======== */
        // Initialize slides with tabindex for keyboard accessibility
        this.slides.forEach(function (slide) {
            slide.setAttribute('tabindex', '-1');
        });
        // Set initial active slide to be keyboard focusable
        this._updateAccessibility();
        /* ======== ACCESSIBILITY CODE END ======== */
    };

    /* -------------------------------------------------------
       Accessibility - Update focus management on active slide change
    ------------------------------------------------------- */
    /* ======== ACCESSIBILITY CODE START ======== */
    TSWSlider.prototype._updateAccessibility = function () {
        var self = this;
        // Remove tabindex from all slides
        this.slides.forEach(function (slide) {
            slide.setAttribute('tabindex', '-1');
            slide.setAttribute('aria-selected', 'false');
        });
        // Add tabindex to active slide only, making it keyboard focusable
        if (this.slides[this.active]) {
            this.slides[this.active].setAttribute('tabindex', '0');
            this.slides[this.active].setAttribute('aria-selected', 'true');
        }
    };
    /* ======== ACCESSIBILITY CODE END ======== */

    /* -------------------------------------------------------
       Autoplay
    ------------------------------------------------------- */
    TSWSlider.prototype._startAuto = function () {
        var self  = this;
        var delay = this.cfg.autoplaySpeed || 4000;
        this._stopAuto();
        this.autoTimer = setInterval(function () { self.next(); }, delay);
    };

    TSWSlider.prototype._stopAuto = function () {
        if (this.autoTimer) { clearInterval(this.autoTimer); this.autoTimer = null; }
    };

    /* -------------------------------------------------------
       Factory: init all widgets on page
    ------------------------------------------------------- */
    function initAll() {
        var sections = document.querySelectorAll('.tsw-testimonial-section');
        sections.forEach(function (section) {
            if (section._tswInited) return;
            section._tswInited = true;
            new TSWSlider(section);
        });
    }

    /* -------------------------------------------------------
       Boot
    ------------------------------------------------------- */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Elementor frontend hook
    var tswElementorHookRegistered = false;

    function registerElementorHook() {
        if (tswElementorHookRegistered) return;
        if (typeof elementorFrontend === 'undefined') return;
        if (!elementorFrontend.hooks || typeof elementorFrontend.hooks.addAction !== 'function') return;

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/tsw_testimonial_slider.default',
            function ($scope) {
                var section = $scope[0].querySelector
                    ? $scope[0].querySelector('.tsw-testimonial-section')
                    : null;
                if (!section) section = $scope[0].classList.contains('tsw-testimonial-section') ? $scope[0] : null;
                if (section && !section._tswInited) {
                    section._tswInited = true;
                    new TSWSlider(section);
                }
            }
        );

        tswElementorHookRegistered = true;
    }

    if (typeof jQuery !== 'undefined') {
        // Try once on document ready for frontend pages.
        jQuery(registerElementorHook);
        // Retry at Elementor init time when hooks become available.
        jQuery(window).on('elementor/frontend/init', registerElementorHook);
    }

    // Expose for external use
    window.TSWSlider = TSWSlider;

})();
