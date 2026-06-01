(function ($) {

    function initUrbanTaxiGrid($scope) {
        console.log('initUrbanTaxiGrid');
        $scope = $($scope);

        let wrapper;

        if ($scope.hasClass('urbantaxi-grid-wrapper')) {
            wrapper = $scope;
        } else {
            wrapper = $scope.find('.urbantaxi-grid-wrapper');
        }

        if (!wrapper.length) return;

        wrapper.each(function () {

            const $wrapper = $(this);

            // Prevent double init in editor
            if ($wrapper.data('grid-initialized')) return;
            $wrapper.data('grid-initialized', true);

            const container  = $wrapper.find('.urbantaxi-grid-container')[0];
            const items      = $wrapper.find('.urbantaxi-grid-item');
            const pagination = $wrapper.find('.urbantaxi-custom-pagination')[0];

            if (!container || !items.length) return;

            const col320  = parseInt($wrapper.data('col-320')) || 1;
            const col576  = parseInt($wrapper.data('col-576')) || 1;
            const col768  = parseInt($wrapper.data('col-768')) || 2;
            const col992  = parseInt($wrapper.data('col-992')) || 2;
            const col1025 = parseInt($wrapper.data('col-1025')) || 3;
            const col1200 = parseInt($wrapper.data('col-1200')) || 3;

            const rows = parseInt($wrapper.data('rows')) || 2;

            const enablePagination = $wrapper.data('enable-pagination');
            const paginationType = $wrapper.data('pagination-type') || 'number';
            const prevText = $wrapper.data('prev-text') || 'Prev';
            const nextText = $wrapper.data('next-text') || 'Next';

            let currentPage = 0;

            /* =========================
               GRID FUNCTIONS (SCOPED)
            ========================== */

            function getColumns() {
                const width = window.innerWidth;

                if (width >= 1200) return col1200;
                if (width >= 1025) return col1025;
                if (width >= 992)  return col992;
                if (width >= 768)  return col768;
                if (width >= 576)  return col576;
                return col320;
            }

            function getPerPage() {
                return getColumns() * rows;
            }

            function getTotalPages() {
                return Math.ceil(items.length / getPerPage());
            }

            function updateGridLayout() {
                const columns = getColumns();
                container.style.display = 'grid';
                container.style.gap = '20px';
                container.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
            }

            /* =========================
               RENDER FUNCTIONS
            ========================== */

            function renderPage() {

                const totalItems = items.length;
                const perPage = getPerPage();

                items.each(function () {
                    this.style.display = 'none';
                });

                const start = currentPage * perPage;
                const end = Math.min(start + perPage, totalItems);

                for (let i = start; i < end; i++) {
                    items[i].style.display = 'block';
                }

                renderPagination();
            }

            function renderPagination() {

                if (!pagination) return;

                if (enablePagination !== 'yes') {
                    pagination.style.display = 'none';
                    return;
                }

                const totalPages = getTotalPages();

                pagination.style.display = 'flex';
                pagination.innerHTML = '';
                pagination.setAttribute('role', 'navigation');
                pagination.setAttribute('aria-label', 'Blog posts pagination');

                if (totalPages <= 1) return;

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

                /* ================= TEXT ONLY ================= */
                if (paginationType === 'text') {
                    const prevBtn = createPagerButton('utx-prev', 'Previous page', prevText, currentPage === 0);
                    prevBtn.onclick = function () {
                        if (currentPage > 0) {
                            currentPage--;
                            renderPage();
                        }
                    };
                    pagination.appendChild(prevBtn);

                    const nextBtn = createPagerButton('utx-next', 'Next page', nextText, currentPage === totalPages - 1);
                    nextBtn.onclick = function () {
                        if (currentPage < totalPages - 1) {
                            currentPage++;
                            renderPage();
                        }
                    };
                    pagination.appendChild(nextBtn);
                    return;
                }

                /* ================= TEXT + NUMBER / DOT ================= */
                if (paginationType === 'text_number' || paginationType === 'text_dot' || paginationType === 'number_arrow') {
                    const prevBtn = createPagerButton('utx-prev', 'Previous page', paginationType === 'number_arrow' ? null : prevText, currentPage === 0);
                    if (paginationType === 'number_arrow') {
                        prevBtn.innerHTML = '<i class="fas fa-chevron-left" aria-hidden="true"></i>';
                    }
                    prevBtn.onclick = function () {
                        if (currentPage > 0) {
                            currentPage--;
                            renderPage();
                        }
                    };
                    pagination.appendChild(prevBtn);
                }

                /* ================= NUMBER ================= */
                if (paginationType === 'number' || paginationType === 'text_number' || paginationType === 'number_arrow') {
                    const numbersToShow = 3;
                    let start = 0;
                    let end = Math.min(numbersToShow, totalPages);

                    if (currentPage >= totalPages - 2) {
                        start = Math.max(0, totalPages - numbersToShow);
                        end = totalPages;
                    } else if (currentPage > 1) {
                        start = currentPage - 1;
                        end = Math.min(currentPage + 2, totalPages);
                    }

                    if (start > 0) {
                        const firstItem = createPagerButton('utx-page-item', 'Go to page 1', '01', false);
                        firstItem.onclick = function () {
                            currentPage = 0;
                            renderPage();
                        };
                        pagination.appendChild(firstItem);

                        if (start > 1) {
                            const dots = document.createElement('span');
                            dots.className = 'utx-separator';
                            dots.innerText = '...';
                            dots.setAttribute('aria-hidden', 'true');
                            pagination.appendChild(dots);
                        }
                    }

                    for (let i = start; i < end; i++) {
                        const pageText = (i + 1) < 10 ? '0' + (i + 1) : String(i + 1);
                        const item = createPagerButton('utx-page-item', 'Go to page ' + (i + 1), pageText, false);
                        if (i === currentPage) {
                            item.classList.add('active');
                            item.setAttribute('aria-current', 'page');
                        }
                        item.onclick = function () {
                            currentPage = i;
                            renderPage();
                        };
                        pagination.appendChild(item);
                    }

                    if (end < totalPages) {
                        const dots = document.createElement('span');
                        dots.className = 'utx-separator';
                        dots.innerText = '...';
                        dots.setAttribute('aria-hidden', 'true');
                        pagination.appendChild(dots);

                        const lastText = totalPages < 10 ? '0' + totalPages : String(totalPages);
                        const lastItem = createPagerButton('utx-page-item', 'Go to page ' + totalPages, lastText, false);
                        lastItem.onclick = function () {
                            currentPage = totalPages - 1;
                            renderPage();
                        };
                        pagination.appendChild(lastItem);
                    }
                }

                /* ================= DOT ================= */
                if (paginationType === 'dot' || paginationType === 'text_dot') {
                    for (let i = 0; i < totalPages; i++) {
                        const item = createPagerButton('utx-page-item utx-dot', 'Go to page ' + (i + 1), null, false);
                        if (i === currentPage) {
                            item.classList.add('active');
                            item.setAttribute('aria-current', 'page');
                        }
                        item.onclick = function () {
                            currentPage = i;
                            renderPage();
                        };
                        pagination.appendChild(item);
                    }
                }

                /* ================= NEXT BUTTON ================= */
                if (paginationType === 'text_number' || paginationType === 'text_dot' || paginationType === 'number_arrow') {
                    const nextBtn = createPagerButton('utx-next', 'Next page', paginationType === 'number_arrow' ? null : nextText, currentPage === totalPages - 1);
                    if (paginationType === 'number_arrow') {
                        nextBtn.innerHTML = '<i class="fas fa-chevron-right" aria-hidden="true"></i>';
                    }
                    nextBtn.onclick = function () {
                        if (currentPage < totalPages - 1) {
                            currentPage++;
                            renderPage();
                        }
                    };
                    pagination.appendChild(nextBtn);
                }
            }

            /* =========================
               INIT
            ========================== */

            updateGridLayout();
            renderPage();

            window.addEventListener('resize', function () {
                updateGridLayout();
                currentPage = 0;
                renderPage();
            });

        });
    }

    /* =========================
       ELEMENTOR INIT
    ========================== */

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
                    initUrbanTaxiGrid($scope);
                }
            );
        });
    });

    /* =========================
       NORMAL FRONTEND LOAD
    ========================== */

    $(window).on('load', function () {
        $('.urbantaxi-grid-wrapper').each(function () {
            initUrbanTaxiGrid($(this));
        });
    });

})(jQuery);
