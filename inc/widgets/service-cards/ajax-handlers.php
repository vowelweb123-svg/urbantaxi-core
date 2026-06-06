<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'urbantaxi_service_cards_limit_words' ) ) {
    function urbantaxi_service_cards_limit_words( $text, $limit ) {
        if ( $limit == 0 ) { return $text; }
        $words = explode( ' ', $text );
        if ( count( $words ) > $limit ) {
            return implode( ' ', array_slice( $words, 0, $limit ) ) . '...';
        }
        return $text;
    }
}

add_action( 'wp_ajax_load_more_posts', 'urbantaxi_service_cards_load_more_posts' );
add_action( 'wp_ajax_nopriv_load_more_posts', 'urbantaxi_service_cards_load_more_posts' );

function urbantaxi_service_cards_load_more_posts() {
    {
        check_ajax_referer('urbantaxi_service_cards_widget_nonce', 'nonce');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 6;
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
        $exclude_posts = isset($_POST['exclude_posts']) ? sanitize_text_field($_POST['exclude_posts']) : '';
        $custom_meta_key = isset($_POST['custom_meta_key']) ? sanitize_text_field($_POST['custom_meta_key']) : '';

        $title_word_limit = isset($_POST['title_word_limit']) ? intval($_POST['title_word_limit']) : 0;
        $show_featured_image = isset($_POST['show_featured_image']) ? sanitize_text_field($_POST['show_featured_image']) : 'yes';
        $excerpt_word_limit = isset($_POST['excerpt_word_limit']) ? intval($_POST['excerpt_word_limit']) : 20;
        $show_pagination = isset($_POST['show_pagination']) ? sanitize_text_field($_POST['show_pagination']) : 'yes';
        $show_read_more = isset($_POST['show_read_more']) ? sanitize_text_field($_POST['show_read_more']) : 'yes';

        $read_more_icon = null;
        if (isset($_POST['read_more_icon'])) {
            if (is_array($_POST['read_more_icon'])) {
                $read_more_icon = $_POST['read_more_icon'];
            } else {
                $read_more_icon = json_decode(stripslashes($_POST['read_more_icon']), true);
            }
        }

        $icon_html = '';

        if (!empty($read_more_icon)) {

            ob_start();

            \Elementor\Icons_Manager::render_icon(
                $read_more_icon,
                [
                    'class' => 'post-read-more-icon',
                    'aria-hidden' => 'true',
                ]
            );

            $icon_html = ob_get_clean();
        }

        $pagination_type = isset($_POST['pagination_type']) ? sanitize_text_field($_POST['pagination_type']) : 'numbers';
        $pagination_prev_text = isset($_POST['pagination_prev_text']) ? sanitize_text_field($_POST['pagination_prev_text']) : esc_html__('Previous', 'urbantaxi-service-cards-widget'); // phpcs:ignore WordPress.Security.EscapeOutput -- AJAX safe
        $pagination_next_text = isset($_POST['pagination_next_text']) ? sanitize_text_field($_POST['pagination_next_text']) : esc_html__('Next', 'urbantaxi-service-cards-widget'); // phpcs:ignore WordPress.Security.EscapeOutput -- AJAX safe
        $pagination_numbers_show = isset($_POST['pagination_numbers_show']) ? sanitize_text_field($_POST['pagination_numbers_show']) : 'yes';
        $pagination_ellipsis = isset($_POST['pagination_ellipsis']) ? sanitize_text_field($_POST['pagination_ellipsis']) : 'yes';
        $pagination_visible_numbers = isset($_POST['pagination_visible_numbers']) ? intval($_POST['pagination_visible_numbers']) : 5;

        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
        );
        if (!empty($exclude_posts)) {
            $exclude_ids = array_map('intval', array_map('trim', explode(',', $exclude_posts)));
            $args['post__not_in'] = $exclude_ids;
        }

        $query = new \WP_Query($args);

        ob_start();
        if ($query->have_posts()) {
            $starting_number = ($page - 1) * $posts_per_page + 1;
            $i = $starting_number;
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <div class="post-query-item">
                    <?php if ($show_featured_image === 'yes' && has_post_thumbnail()): ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="post-content">
                        <?php

                        $number_with_zero = sprintf('%02d', $i);

                        echo '<div class="post-custom-meta-image-box">';

                        if (!empty($custom_meta_key)) {
                            $meta_value = get_post_meta(get_the_ID(), $custom_meta_key, true);
                            if (!empty($meta_value)) {
                            echo '<div class="post-custom-meta">';

                            if (is_numeric($meta_value)) {
                                echo wp_get_attachment_image($meta_value, 'medium');
                            } elseif (filter_var($meta_value, FILTER_VALIDATE_URL)) {
                                echo '<img src="' . esc_url($meta_value) . '" alt="" />';
                            } else {
                                echo esc_html($meta_value);
                            }

                            echo '</div>';
                            }
                        }

                        echo '<h3>' .
                            esc_html(urbantaxi_service_cards_limit_words(get_the_title(), $title_word_limit)) .
                            '</h3>';

                        echo '<div class="post-excerpt position-relative">';
                        echo esc_html(urbantaxi_service_cards_limit_words(get_the_excerpt(), $excerpt_word_limit));
                        echo '</div>';

                        echo '</div>';

                        /* =========================
                           READ MORE BUTTON
                        ========================= */
                        echo '<div class="post-read-more-box d-flex" style="justify-content:space-between;">';
                            echo '<div class="post-count-box">';
                            echo esc_html($number_with_zero);
                            echo '</div>';
 
                            
                            if ($show_read_more === 'yes') {
                                echo '<a href="' . esc_url(get_permalink()) . '" class="post-read-more-btn" aria-label="' . esc_attr(sprintf(__('Read more about %s', 'urbantaxi-service-cards-widget'), get_the_title())) . '">';
                                if ($icon_html) {
                                    echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor Icons safe HTML
                                }
                                echo '</a>';
                            }
                        echo '</div>';

                        ?>
                    </div>
                </div>
                <?php
                $i++;
            }
        }
        $posts_html = ob_get_clean();

        $pagination_html = '';
        if ($show_pagination === 'yes' && $query->max_num_pages > 1) {
            $pagination_html .= '<div class="post-query-pagination pt-5">';

            if ($pagination_type === 'arrow_text') {
                $pagination_html .= '<a href="#" class="pagination-arrow prev-arrow" data-page="prev"><i class="fa-solid fa-angle-left"></i> ' . esc_html($pagination_prev_text) . '</a>';
            } elseif ($pagination_type === 'arrows' || $pagination_type === 'both') {
                $pagination_html .= '<a href="#" class="pagination-arrow prev-arrow" data-page="prev"><i class="fa-solid fa-angle-left"></i></a>';
            }

            if ($pagination_type === 'numbers' || $pagination_type === 'both') {
                for ($i = 1; $i <= $query->max_num_pages; $i++) {
                    $active_class = ($i == 1) ? 'active' : '';
                    $display_num = $i < 10 ? '0' . $i : $i;
                    $pagination_html .= '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="' . esc_attr($i) . '">' . esc_html($display_num) . '</a>';
                }
            } elseif ($pagination_type === 'arrow_text' && $pagination_numbers_show === 'yes') {
                $visible_numbers = $pagination_visible_numbers;
                $show_ellipsis = $pagination_ellipsis === 'yes';
                $total_pages = $query->max_num_pages;
                $current_page = $page;

                $half = floor($visible_numbers / 2);
                $start = max(2, $current_page - $half);
                $end = min($total_pages - 1, $current_page + $half);

                if ($end - $start + 1 < $visible_numbers) {
                    if ($start == 2) {
                        $end = min($total_pages - 1, $start + $visible_numbers - 1);
                    } else {
                        $start = max(2, $end - $visible_numbers + 1);
                    }
                }

                // Page 1 (always show)
                $active_class = ($current_page == 1) ? 'active' : '';
                $pagination_html .= '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="1">01</a>';

                // Ellipsis after page 1 if there's a gap
                if ($show_ellipsis && $start > 2) {
                    $pagination_html .= '<span class="pagination-ellipsis">...</span>';
                }

                // Show pages from start to end
                for ($i = $start; $i <= $end; $i++) {
                    $active_class = ($current_page == $i) ? 'active' : '';
                    $display_num = $i < 10 ? '0' . $i : $i;
                    $pagination_html .= '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="' . esc_attr($i) . '">' . esc_html($display_num) . '</a>';
                }

                // Ellipsis before last page if there's a gap
                if ($show_ellipsis && $end < $total_pages - 1) {
                    $pagination_html .= '<span class="pagination-ellipsis">...</span>';
                }

                // Last page (always show, if more than 1 page)
                if ($total_pages > 1) {
                    $active_class = ($current_page == $total_pages) ? 'active' : '';
                    $display_num = $total_pages < 10 ? '0' . $total_pages : $total_pages;
                    $pagination_html .= '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="' . esc_attr($total_pages) . '">' . esc_html($display_num) . '</a>';
                }
            }

            if ($pagination_type === 'arrow_text') {
                $pagination_html .= '<a href="#" class="pagination-arrow next-arrow" data-page="next">' . esc_html($pagination_next_text) . ' <i class="fa-solid fa-angle-right"></i></a>';
            } elseif ($pagination_type === 'arrows' || $pagination_type === 'both') {
                $pagination_html .= '<a href="#" class="pagination-arrow next-arrow" data-page="next"><i class="fa-solid fa-angle-right"></i></a>';
            }

            $pagination_html .= '</div>';
        }

        wp_reset_postdata();

        wp_send_json_success(array(
            'posts' => $posts_html,
            'pagination' => $pagination_html,
            'max_pages' => $query->max_num_pages
        ));
    }
}
