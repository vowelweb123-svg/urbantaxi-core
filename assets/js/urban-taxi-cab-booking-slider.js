(function ($) {
    'use strict';
    function handleTitleClick(titleItem) {
        const widget = titleItem.closest('.urban-taxi-cab-booking-widget');
        const imageContainer = widget.querySelector('.utcb-active-image-container');
        const activeImage = imageContainer.querySelector('.utcb-active-image');
        const overlayInfo = imageContainer.querySelector('.utcb-overlay-info');
        overlayInfo.innerHTML = '<div class="loading">Loading info...</div>';

        const imageSrc = titleItem.dataset.imageSrc;
        const postId = titleItem.dataset.postId;

        widget.querySelectorAll('.utcb-title-item').forEach(i => i.classList.remove('active'));
        titleItem.classList.add('active');

        if (imageSrc && activeImage) {
            activeImage.src = imageSrc;
        }

        fetchPostInfo(widget, postId, overlayInfo);
    }

    function fetchPostInfo(widget, postId, overlayInfo) {
        if (!postId || !overlayInfo) return;

        overlayInfo.innerHTML = '<div class="loading">Loading info...</div>';

        const formData = new FormData();
        formData.append('action', 'utcb_get_post_info');
        formData.append('nonce', utcbAjax.nonce);
        formData.append('post_id', postId);
        formData.append('settings', widget.dataset.settings);

        // cancel previous request for THIS widget only
        if (widget._currentRequest) {
            widget._currentRequest.abort();
        }

        const controller = new AbortController();
        widget._currentRequest = controller;

        fetch(utcbAjax.ajax_url, {
            method: 'POST',
            body: formData,
            signal: controller.signal
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    overlayInfo.innerHTML = data.data.info_html;
                } else {
                    overlayInfo.innerHTML = '<div class="error">Failed to load data</div>';
                }
            })
            .catch((err) => {
                if (err.name !== 'AbortError') {
                    overlayInfo.innerHTML = '<div class="error">Something went wrong</div>';
                }
            })
            .finally(() => {
                if (widget._currentRequest === controller) {
                    widget._currentRequest = null;
                }
            });
    }
    // end

    const bootstrapElementorHook = () => {
        if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }

        window.elementorFrontend.hooks.addAction('frontend/element_ready/urban-taxi-cab-booking-slider-widget.default', ($scope) => {
            const scopeElement = $scope?.jquery ? $scope[0] : $scope;
            initSliderInstances(scopeElement || $scope);
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.urban-taxi-cab-booking-widget').forEach((widget) => {

            const allBtn = widget.querySelector('.utcb-filter-btn[data-category="all"]');

            if (allBtn) {
                allBtn.click();
            } else {
                const activeTitle = widget.querySelector('.utcb-title-item.active');
                if (activeTitle) {
                    activeTitle.click();
                }
            }

            utcbRunImageAnimation(widget);
        });
    });

    if (window.elementorFrontend && window.elementorFrontend.hooks) {
        bootstrapElementorHook();
    } else {
        window.addEventListener('elementor/frontend/init', bootstrapElementorHook);
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.utcb-title-item')) {
            handleTitleClick(e.target.closest('.utcb-title-item'));
            return;
        }

        if (e.target.classList.contains('utcb-filter-btn')) {

            const button = e.target;
            const widget = button.closest('.urban-taxi-cab-booking-widget');
            const titleList = widget.querySelector('.utcb-title-list');
            const overlayInfo = widget.querySelector('.utcb-overlay-info');
            const activeImage = widget.querySelector('.utcb-active-image');

            const category = button.dataset.category;
            const maxTitles = widget.dataset.maxTitles;

            // active button UI
            widget.querySelectorAll('.utcb-filter-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // loading state
            titleList.innerHTML = '<div class="loading">Loading...</div>';

            const formData = new FormData();
            formData.append('action', 'utcb_get_posts_by_category');
            formData.append('nonce', utcbAjax.nonce);
            formData.append('category', category);
            formData.append('max_titles', maxTitles);

            // FIXED (no stringify/parse)
            formData.append('settings', widget.dataset.settings);

            fetch(utcbAjax.ajax_url, {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {

                        // update titles
                        titleList.innerHTML = data.data.html;
                        utcbRunImageAnimation(widget);

                        // update overlay
                        if (overlayInfo && data.data.first_info) {
                            overlayInfo.innerHTML = data.data.first_info;
                        }
                        const firstItem = titleList.querySelector('.utcb-title-item');

                        if (!firstItem) {
                            if (overlayInfo) overlayInfo.innerHTML = '<div class="empty">No data found</div>';
                            if (activeImage) activeImage.src = '';
                            return;
                        }

                        if (firstItem) {
                            firstItem.classList.add('active');

                            const imageSrc = firstItem.dataset.imageSrc;

                            if (imageSrc && activeImage) {
                                activeImage.src = imageSrc;
                            }
                        }
                    }
                })
                .catch(() => {
                    titleList.innerHTML = '<div class="error">Failed to load posts</div>';
                });

            return;
        }
    });

    function utcbRunImageAnimation(container) {
        const titleItems = container.querySelectorAll('.utcb-title-item');

        titleItems.forEach((titleItem, index) => {
            setTimeout(() => {
                titleItem.classList.add('utcb-animate');
            }, index * 120);
        });

        const activeImage = container.querySelector('.utcb-active-image');
        if (activeImage) {
            activeImage.classList.add('utcb-animate');
        }
    }

    function initSliderInstances(scope) {
        const root = scope && scope.querySelector ? scope : document;

        root.querySelectorAll('.urban-taxi-cab-booking-widget').forEach((widget) => {

            const allBtn = widget.querySelector('.utcb-filter-btn[data-category="all"]');
            if (allBtn) {
                allBtn.click(); // always apply limit
            }

            utcbRunImageAnimation(widget);
        });
    }


})(window.jQuery);
