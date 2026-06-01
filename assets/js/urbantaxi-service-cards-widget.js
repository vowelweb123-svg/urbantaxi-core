(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.post-query-widget').each(function () {
            var $widget = $(this);
            var $grid = $widget.find('.post-query-grid');
            var $pagination = $widget.find('.post-query-pagination');
            var postType = $widget.data('post-type');
            var postsPerPage = $widget.data('posts-per-page');
            var excludePosts = $widget.data('exclude-posts');
            var customMetaKey = $widget.data('custom-meta-key') || '';
            
            var titleWordLimit = $widget.data('title-word-limit');
            var showFeaturedImage = $widget.data('show-featured-image');
            if (showFeaturedImage === undefined || showFeaturedImage === null) {
                showFeaturedImage = 'yes';
            }
            var excerptWordLimit = $widget.data('excerpt-word-limit');
            var columns = $widget.data('columns');
            var columnsTablet = $widget.data('columns-tablet');
            var columnsMobile = $widget.data('columns-mobile');
            var showPagination = $widget.data('show-pagination');
            var paginationType = $widget.data('pagination-type');
            var paginationPrevText = $widget.data('pagination-prev-text');
            var paginationNextText = $widget.data('pagination-next-text');
            var paginationNumbersShow = $widget.data('pagination-numbers-show');
            var paginationEllipsis = $widget.data('pagination-ellipsis');
            var paginationVisibleNumbers = $widget.data('pagination-visible-numbers');
            var showReadMore = $widget.data('show-read-more');
            
            // Use attr() for JSON objects to get raw string
            var readMoreIconRaw = $widget.attr('data-read-more-icon');
            var readMoreIcon = readMoreIconRaw ? JSON.parse(readMoreIconRaw) : null;
            
            var currentPage = 1;
            var maxPages = $pagination.find('.page-number').length;

            // Check if we're in Elementor editor
            var isEditMode = typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode();
            function setGridColumns() {
                var windowWidth = $(window).width();
                var cols;
                if (windowWidth <= 768) {
                    cols = columnsMobile;
                } else if (windowWidth <= 1024) {
                    cols = columnsTablet;
                } else {
                    cols = columns;
                }
                $grid.css('grid-template-columns', 'repeat(' + cols + ', 1fr)');
            }

            if (isEditMode) {
                $grid.css('grid-template-columns', 'repeat(' + columns + ', 1fr)');
            } else {
                setGridColumns();
                $(window).on('resize', function () {
                    setGridColumns();
                });
            }

            function updatePagination(page) {
                currentPage = page;

                $pagination.find('.page-number').removeClass('active');
                $pagination.find('.page-number[data-page="' + page + '"]').addClass('active');
                $pagination.find('.prev-arrow').toggleClass('disabled', page <= 1);
                $pagination.find('.next-arrow').toggleClass('disabled', page >= maxPages);
            }

            function loadPage(page) {
                if (page < 1 || page > maxPages) {
                    return;
                }

                $grid.addClass('loading');

                $.ajax({
                    url: urbantaxiServiceCardsWidgetAjax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'load_more_posts',
                        page: page,
                        posts_per_page: postsPerPage,
                        post_type: postType,
                        exclude_posts: excludePosts,
                        custom_meta_key: customMetaKey,
                        title_word_limit: titleWordLimit,
                        show_featured_image: showFeaturedImage,
                        excerpt_word_limit: excerptWordLimit,
                        show_pagination: $widget.data('show-pagination'),
                        pagination_type: paginationType,
                        pagination_prev_text: paginationPrevText,
                        pagination_next_text: paginationNextText,
                        pagination_numbers_show: paginationNumbersShow,
                        pagination_ellipsis: paginationEllipsis,
                        pagination_visible_numbers: paginationVisibleNumbers,
                        show_read_more: showReadMore,
                        read_more_icon: readMoreIcon,
                        nonce: urbantaxiServiceCardsWidgetAjax.nonce
                    },
                    success: function (response) {
                        if (response.success) {
                            $grid.html(response.data.posts);
                            
                            if (response.data.pagination) {
                                $pagination.html(response.data.pagination);
                            }

                            maxPages = response.data.max_pages;
                            
                            updatePagination(page);
                        } else {
                            console.error('Ajax error:', response.data);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Ajax request failed:', error);
                    },
                    complete: function () {
                        $grid.removeClass('loading');
                    }
                });
            }

            $pagination.on('click', '.page-number', function (e) {
                e.preventDefault();
                var $this = $(this);
                var page = parseInt($this.data('page'));

                if ($this.hasClass('active') || $this.hasClass('disabled')) {
                    return;
                }

                loadPage(page);
            });

            $pagination.on('click', '.pagination-arrow', function (e) {
                e.preventDefault();
                var $this = $(this);
                var direction = $this.data('page');
                var newPage = currentPage;

                if ($this.hasClass('disabled')) {
                    return;
                }

                if (direction === 'prev') {
                    newPage = currentPage - 1;
                } else if (direction === 'next') {
                    newPage = currentPage + 1;
                }

                loadPage(newPage);
            });
        });
    });

})(jQuery);
