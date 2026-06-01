(function ($) {

    function initBookSeat($scope) {
        const wrapper = $scope[0].querySelector(".urbantaxi-book-wrapper");
        if (!wrapper) return;

        const btn = wrapper.querySelector(".urbantaxi-book-btn");
        const modal = wrapper.querySelector(".urbantaxi-modal");
        const close = wrapper.querySelector(".urbantaxi-close");

        if (!btn || !modal || !close) return;

        // Clean old listeners by cloning
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);

        const newClose = close.cloneNode(true);
        close.parentNode.replaceChild(newClose, close);

        let focusableElements = [];
        let firstFocusable;
        let lastFocusable;

        // Helper: get all visible focusable elements inside modal
        function updateFocusableElements() {
            const selector = `
                a[href]:not([tabindex="-1"]),
                button:not([disabled]):not([tabindex="-1"]),
                textarea:not([disabled]):not([tabindex="-1"]),
                input:not([type="hidden"]):not([disabled]):not([tabindex="-1"]),
                select:not([disabled]):not([tabindex="-1"]),
                [tabindex]:not([tabindex="-1"])
            `;

            focusableElements = Array.from(modal.querySelectorAll(selector))
                .filter(el => el.offsetParent !== null); // only visible

            // Force close button as first focusable
            firstFocusable = newClose;
            lastFocusable = focusableElements.length ? focusableElements[focusableElements.length - 1] : newClose;
        }

        function openModal() {
            modal.classList.add("active");
            document.body.style.overflow = "hidden";
            updateFocusableElements();
            newClose.focus(); // start focus on close button
        }

        function closeModal() {
            modal.classList.remove("active");
            document.body.style.overflow = "";
            newBtn.focus(); // return focus to open button
        }

        newBtn.addEventListener("click", openModal);
        newClose.addEventListener("click", closeModal);

        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        document.addEventListener("keydown", (e) => {
            if (!modal.classList.contains("active")) return;

            if (e.key === "Escape") {
                e.preventDefault();
                closeModal();
                return;
            }

            if (e.key === "Tab") {
                updateFocusableElements();

                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            }
        });
    }

    // Elementor init
    function waitForElementorHooks(callback) {
        const interval = setInterval(() => {
            if (window.elementorFrontend && elementorFrontend.hooks) {
                clearInterval(interval);
                callback();
            }
        }, 50);
    }

    $(window).on('elementor/frontend/init', () => {
        waitForElementorHooks(() => {
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/urbantaxi_book_seat_modal.default',
                ($scope) => {
                    if ($scope.data('bookseat-initialized')) return;
                    $scope.data('bookseat-initialized', true);
                    initBookSeat($scope);
                }
            );
        });
    });

    // Normal frontend load fallback
    $(window).on('load', () => {
        $('.urbantaxi-book-wrapper').each(function () {
            const $scope = $(this).closest('.elementor-widget');
            if ($scope.length) {
                initBookSeat($scope);
            }
        });
    });

})(jQuery);