(function () {

    function applyDelay(el, delay) {
        if (!delay || isNaN(delay)) return;
        el.style.animationDelay = delay + 'ms';
    }


    function stripAnimationClasses(scope) {

        const animationClassRegex = /^(animate__|cp-animate__|cp-vivify__|vivify__)/;

        scope.querySelectorAll('*').forEach(function (el) {

            const toRemove = [];

            el.classList.forEach(function (cls) {
                if (animationClassRegex.test(cls)) {
                    toRemove.push(cls);
                }
            });

            if (toRemove.length) {
                el.__cpRemovedAnimations = toRemove;
                toRemove.forEach(cls => el.classList.remove(cls));
            }
        });
    }


    function runAnimations(wrapper) {

        if (wrapper.classList.contains('cp-disable-animation-yes')) return;
        if (wrapper.__cpAnimated) return; // prevent repeat

        /* ================= MAIN ELEMENT ANIMATION ================= */
        if (wrapper.hasAttribute('data-cp-main-animation')) {

            let mainAnimation;
            try {
                mainAnimation = JSON.parse(  wrapper.getAttribute('data-cp-main-animation')  );
            } catch (e) {}

            if (mainAnimation && mainAnimation.animation) {

                // console.log(mainAnimation);
                if (mainAnimation.delay) {
                    applyDelay(wrapper, mainAnimation.delay);
                }

                mainAnimation.animation.split(' ').forEach(function (cls) {
                    if (cls) wrapper.classList.add(cls.trim());
                });
            }
        }

        /* ================= FRONTEND ================= */
        if (wrapper.hasAttribute('data-cp-selector-animations')) {

            let selectorAnimations;
            try {
                selectorAnimations = JSON.parse(
                    wrapper.getAttribute('data-cp-selector-animations')
                );
            } catch (e) {
                console.error('Error parsing selector animations:', e);
                return;
            }

            if (Array.isArray(selectorAnimations)) {
                selectorAnimations.forEach(function (animation) {
                    if (!animation.selector || !animation.animation) return;

                    wrapper.querySelectorAll(animation.selector.trim()).forEach(function (el) {

                        if (animation.delay) {
                            applyDelay(el, animation.delay);
                        }
                        
                        animation.animation.split(' ').forEach(function (cls) {
                            if (cls) el.classList.add(cls.trim());
                        });
                    });
                });
            }
        }

        /* ================= ELEMENTOR EDITOR ================= */
        if (wrapper.hasAttribute('data-settings')) {

            let settings;
            try {
                settings = JSON.parse(wrapper.getAttribute('data-settings'));
            } catch (e) {
                return;
            }

            if (settings.urbantaxi_smart_animations_disable_animation === 'yes') return;

            if (settings.cp_animation_source === 'custom') {

                const selector = settings.cp_animation_selector_target_selector;
                if (!selector) return;

                let classToAdd = '';

                if (
                    settings.cp_animation_selector_animation_library === 'animate' &&
                    settings.cp_animate_css_selector_animate_animation
                ) {
                    classToAdd = 'animate__animated ' +
                        settings.cp_animate_css_selector_animate_animation;
                }

                if (
                    settings.cp_animation_selector_animation_library === 'vivify' &&
                    settings.cp_vivify_selector_vivify_animation
                ) {
                    classToAdd = 'cp-vivify__' +
                        settings.cp_vivify_selector_vivify_animation;
                }

                if (!classToAdd) return;

                wrapper.querySelectorAll(selector.trim()).forEach(function (el) {

                    if (settings.cp_animation_selector_delay) {
                        applyDelay(el, settings.cp_animation_selector_delay);
                    }

                    
                    classToAdd.split(' ').forEach(function (cls) {
                        if (cls) el.classList.add(cls.trim());
                    });
                });
            }
        }


        /* ================= HOVER SELECTOR ANIMATIONS ================= */
        if (wrapper.hasAttribute('data-cp-hover-animations')) {

            let hoverAnimations;

            try {
                hoverAnimations = JSON.parse(
                    wrapper.getAttribute('data-cp-hover-animations')
                );
            } catch (e) {
                console.error('Error parsing hover animations:', e);
                return;
            }

            if (Array.isArray(hoverAnimations)) {
                hoverAnimations.forEach(function (hover) {
                    
                    if (!hover.main_selector || !hover.target_selector) return;
                    
                    wrapper.querySelectorAll(hover.main_selector.trim()).forEach(function (mainEl) {
                        
                        mainEl.addEventListener('mouseenter', function () {
                            
                            wrapper.querySelectorAll(hover.target_selector.trim()).forEach(function (targetEl) {
                                
                                /* Apply Animation */
                                if (hover.animation) {
                                    hover.animation.split(' ').forEach(function (cls) {
                                        if (cls) targetEl.classList.add(cls.trim());
                                    });
                                }

                                /* Apply Color */
                                if (hover.color) {
                                    targetEl.__cpOriginalColor = targetEl.style.color;
                                    targetEl.style.color = hover.color;
                                    targetEl.style.transition = 'color 0.3s ease';
                                }
                            });
                        });

                        mainEl.addEventListener('mouseleave', function () {

                            wrapper.querySelectorAll(hover.target_selector.trim()).forEach(function (targetEl) {

                                /* Remove Animation */
                                if (hover.animation) {
                                    hover.animation.split(' ').forEach(function (cls) {
                                        if (cls) targetEl.classList.remove(cls.trim());
                                    });
                                }

                                /* Restore Color */
                                if (hover.color) {
                                    targetEl.style.color = targetEl.__cpOriginalColor || '';
                                }
                            });
                        });

                    });
                });
            }
        }

        wrapper.__cpAnimated = true;
    }

    function observeElements(scope) {

        const wrappers = scope.querySelectorAll(
            '[data-cp-selector-animations],[data-cp-hover-animations], [data-settings]'
        );

        if (!wrappers.length) return;

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                runAnimations(entry.target);
                observer.unobserve(entry.target);
            });
        }, {
            threshold: 0,
            rootMargin: '200px 0px'
        });

        wrappers.forEach(function (wrapper) {
            observer.observe(wrapper);
        });
    }

    /* ================= FRONTEND ================= */
    document.addEventListener('DOMContentLoaded', function () {
        stripAnimationClasses(document);
        observeElements(document);
    });

    /* ================= ELEMENTOR EDITOR ================= */
    jQuery(window).on('elementor/frontend/init', function () {
        if (window.elementorFrontend) {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/global',
                function ($scope) {
                    const settings = $scope.data('settings');
                    observeElements($scope[0]);
                }
            );
        }
    });

    window.urbanTaxiSmartAnimationsApplyAnimations = function () {
        observeElements(document);
    };

})();
