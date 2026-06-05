<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_ajax_utcfw_filter_posts', 'utcfw_filter_posts' );
add_action( 'wp_ajax_nopriv_utcfw_filter_posts', 'utcfw_filter_posts' );
function utcfw_filter_posts()
{
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 6;
    $show_pagination = isset($_POST['show_pagination']) && $_POST['show_pagination'] === 'yes' ? 'yes' : 'no';

    $show_read_more = isset($_POST['show_read_more']) ? $_POST['show_read_more'] : 'yes';
    $read_more_text = isset($_POST['read_more_text']) ? sanitize_text_field($_POST['read_more_text']) : 'Book Now';
    $read_more_url = isset($_POST['read_more_url']) ? esc_url($_POST['read_more_url']) : '';
    $tittle_url = isset($_POST['tittle_url']) ? esc_url($_POST['tittle_url']) : '';

    $read_more_icon = isset($_POST['read_more_icon']) ? json_decode(stripslashes($_POST['read_more_icon']), true) : [];
    $meta_icon = isset($_POST['meta_icon']) ? json_decode(stripslashes($_POST['meta_icon']), true) : [];

    $currency_symbol = function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '';

    $args = [
        'post_type' => 'mptbm_rent',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
    ];

    if ($category !== 'all') {
        $args['tax_query'] = [
            [
                'taxonomy' => 'mptbm_category',
                'field' => 'slug',
                'terms' => $category,
            ]
        ];
    }

    $query = new WP_Query($args);

    // for icon 

    $read_more_icon = function_exists('utcb_normalize_elementor_icon_setting')
        ? utcb_normalize_elementor_icon_setting($read_more_icon)
        : [];

    $icon_html = function_exists('utcb_render_elementor_icon_html')
        ? utcb_render_elementor_icon_html(
            $read_more_icon,
            ['aria-hidden' => 'true', 'class' => 'post-read-more-icon']
        )
        : '';

    $meta_icon = function_exists('utcb_normalize_elementor_icon_setting')
        ? utcb_normalize_elementor_icon_setting($meta_icon)
        : [];

    $content_meta_icon_prefix = '';

    if (!empty($meta_icon) && function_exists('utcb_render_elementor_icon_html')) {

        $meta_raw = utcb_render_elementor_icon_html(
            $meta_icon,
            [
                'class' => 'utcb-meta-icon',
                'aria-hidden' => 'true',
            ]
        );

        if ($meta_raw !== '') {
            $content_meta_icon_prefix = '<span class="me-2 utcb-meta-icon-wrap">' . $meta_raw . '</span> ';
        }
    }
    // end

    ob_start();

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();
            $current_rent_id = get_the_ID();
            $rent_id = get_post_meta($current_rent_id, 'link_mptbm_id', true);
            $rent_post = $rent_id ? get_post($rent_id) : null;
            $final_url = !empty($tittle_url) ? $tittle_url : ($rent_post ? get_permalink($rent_post->ID) : get_permalink($current_rent_id));
            ?>

            <div class="utcfw-card">

                <div class="utcfw-image">
                    <?php if (has_post_thumbnail())
                        the_post_thumbnail('medium'); ?>
                </div>

                <div class="utcfw-content">
                    <h4>
                        <a href="<?php echo esc_url($final_url); ?>" target="_blank" rel="noopener">
                            <?php the_title(); ?>
                        </a>
                    </h4>

                    <?php
                    $rent_id = get_the_ID();

                     $initial_price = get_post_meta($rent_id, 'mptbm_initial_price', true);
                     $km_price = get_post_meta($rent_id, 'mptbm_km_price', true);

                    $seating_capacity = '';
                    $additional_charge_km = '';
                    $additional_passenger = '';

                    $features = get_post_meta($rent_id, 'mptbm_features', true);

                    if (!empty($features) && is_array($features)) {
                        foreach ($features as $feature) {
                            if (!isset($feature['label']))
                                continue;

                            $label = trim($feature['label']);

                            if ($label === 'Seating Capacity') {
                                $seating_capacity = $feature['text'];
                            }
                            if ($label === 'Additional Charge/Km') {
                                $additional_charge_km = $feature['text'];
                            }
                            if ($label === 'Additional Passenger') {
                                $additional_passenger = $feature['text'];
                            }
                        }
                    }
                    ?>

                    <div class="utcfw-meta">

                        <?php if ($seating_capacity): ?>
                            <div style="align-self:center; justify-content: space-between;">
                                <span class="d-flex">
                                    <?php echo $content_meta_icon_prefix; ?>
                                    <strong>Seating:</strong>
                                </span>
                                <?php echo esc_html($seating_capacity); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($additional_charge_km): ?>
                            <div style="align-self:center; justify-content: space-between;">
                                <span class="d-flex">
                                    <?php echo $content_meta_icon_prefix; ?>
                                    <strong>Charge/Km:</strong>
                                </span>
                                <?php echo $currency_symbol . esc_html($additional_charge_km); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($initial_price): ?>
                            <div style="align-self:center; justify-content: space-between;">
                                <span class="d-flex">
                                    <?php echo $content_meta_icon_prefix; ?>
                                    <strong>Base Fare:</strong>
                                </span>
                                <?php echo $currency_symbol . esc_html($initial_price); ?>
                            </div>
                        <?php endif; ?>


                    </div>

                    <div class="utcfw-meta-button-box">
                        <?php if ($km_price): ?>
                            <p class="utcfw-price align-self-center">
                                <?php echo $currency_symbol . esc_html($km_price); ?> <span class="utcfw-price-km">/km</span>
                            </p>
                        <?php endif; ?>

                        <?php if ($show_read_more === 'yes'): ?>
                            <?php
                            $button_url = '#';

                            if (!empty($read_more_url)) {
                                $button_url = $read_more_url;
                            } else {
                                $booking_page = get_page_by_path('booking-form');
                                if (!$booking_page) {
                                    $booking_page = get_page_by_title('Booking Form');
                                }

                                if ($booking_page) {
                                    $button_url = get_permalink($booking_page->ID);
                                }
                            }
                            ?>

                            <a href="<?php echo esc_url($button_url); ?>" class="utcfw-readmore" target="_blank" rel="noopener">

                                <span class="utcfw-btn-text">
                                    <?php echo esc_html($read_more_text); ?>
                                </span>
                            </a>
                        <?php endif; ?>


                    </div>

                </div>

            </div>

        <?php endwhile;

        // Pagination
        $total_pages = $query->max_num_pages;

        if ($total_pages > 1 && $show_pagination === 'yes'):

            echo '<div class="utcfw-pagination">';

            // PREV BUTTON
            if ($paged > 1) {
                echo '<span class="utcfw-page utcfw-prev" data-page="' . ($paged - 1) . '">
                <i class="fa fa-angle-left"></i>
              </span>';
            } else {
                echo '<span class="utcfw-page utcfw-prev disabled">
                <i class="fa fa-angle-left"></i>
              </span>';
            }

            $range = 1;

            $start = max(1, $paged - $range);
            $end = min($total_pages, $paged + $range);

            // ALWAYS SHOW FIRST PAGE
            if ($start > 1) {

                echo '<span class="utcfw-page" data-page="1">01</span>';

                if ($start > 2) {
                    echo '<span class="utcfw-dots">...</span>';
                }
            }

            // MAIN RANGE LOOP
            for ($i = $start; $i <= $end; $i++) {

                $active = ($i == $paged) ? 'active' : '';

                echo '<span class="utcfw-page ' . $active . '" data-page="' . $i . '">'
                    . sprintf('%02d', $i) .
                    '</span>';
            }

            // ALWAYS SHOW LAST PAGE
            if ($end < $total_pages) {

                if ($end < $total_pages - 1) {
                    echo '<span class="utcfw-dots">...</span>';
                }

                echo '<span class="utcfw-page" data-page="' . $total_pages . '">'
                    . sprintf('%02d', $total_pages) .
                    '</span>';
            }

            // NEXT BUTTON
            if ($paged < $total_pages) {
                echo '<span class="utcfw-page utcfw-next" data-page="' . ($paged + 1) . '">
                <i class="fa fa-angle-right"></i>
              </span>';
            } else {
                echo '<span class="utcfw-page utcfw-next disabled">
                <i class="fa fa-angle-right"></i>
              </span>';
            }

            echo '</div>';

        endif;

    else:
        echo '<p>No vehicles found.</p>';
    endif;

    wp_reset_postdata();

    echo ob_get_clean();
    wp_die();
}