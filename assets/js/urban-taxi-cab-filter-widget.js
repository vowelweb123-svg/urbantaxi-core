(function ($) {

    jQuery(window).on('elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction('frontend/element_ready/urban_taxi_cab_filter_widget.default', function ($scope) {

            let currentCategory = 'all';
            let currentPage = 1;

            function loadPosts(category = 'all', page = 1) {

                currentCategory = category;
                currentPage = page;

                let wrapper = $scope.find('.utcfw-wrapper');
                let results = $scope.find('.utcfw-results');

                let postsPerPage = wrapper.data('posts');
                let showPagination = wrapper.data('pagination');

                results.html('<p>Loading...</p>');

                let readMore = wrapper.data('readmore');
                let readMoreText = wrapper.data('readmore-text');
                let readMoreUrl = wrapper.data('readmore-url');

                let readMoreIcon = wrapper.data('readmore-icon');
                let metaIcon = wrapper.data('meta-icon');
                let tittleUrl = wrapper.data('tittle-url');  // ← ADD THIS LINE

                $.ajax({
                    url: utcfw_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'utcfw_filter_posts',
                        category: category,
                        paged: page,
                        posts_per_page: postsPerPage,
                        show_pagination: showPagination,
                        show_read_more: readMore,
                        read_more_text: readMoreText,
                        read_more_url: readMoreUrl,
                        tittle_url: tittleUrl,
                        read_more_icon: JSON.stringify(readMoreIcon),
                        meta_icon: JSON.stringify(metaIcon)
                    },
                    success: function (response) {
                        results.html(response);
                    }
                });


            }

            // Initial Load
            loadPosts();

            // Filter click
            $scope.on('click', '.utcfw-filter li', function () {

                $scope.find('.utcfw-filter li').removeClass('active');
                $(this).addClass('active');

                let category = $(this).data('filter');

                loadPosts(category, 1);
            });

            // Pagination click
            $scope.on('click', '.utcfw-page', function () {

                if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
                    return;
                }

                let page = $(this).data('page');
                if (!page) return;

                loadPosts(currentCategory, page);
            });

        });

    });

})(jQuery);