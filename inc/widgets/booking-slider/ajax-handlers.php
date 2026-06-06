<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Booking-slider helper functions and AJAX handlers.
// Loaded unconditionally so they are available on admin-ajax.php requests.


/**
 * Get shop page URL. Works with all permalink structures.
 * If no URL provided, fallback to WooCommerce shop page.
 *
 * @param string $url Provided URL.
 * @return string Shop page URL or provided URL.
 */
function utcb_get_shop_url($url = '')
{
    if (!empty($url)) {
        return $url;
    }

    // Prefer the custom shop-2-2 page when no explicit URL is provided.
    $preferred_url = home_url('/shop-2-2/');
    $preferred_page_id = url_to_postid($preferred_url);
    if (!empty($preferred_page_id)) {
        $preferred_page_url = get_permalink($preferred_page_id);
        if (!empty($preferred_page_url)) {
            return $preferred_page_url;
        }
    }

    // Try to get WooCommerce shop page
    if (function_exists('wc_get_page_id')) {
        $shop_page_id = wc_get_page_id('shop-2-2');
        if ($shop_page_id) {
            $shop_url = get_permalink($shop_page_id);
            if ($shop_url) {
                return $shop_url;
            }
        }
    }

    // Fallback to home URL if shop page not found
    return home_url('/shop/');
}

/**
 * Normalize Elementor icon control value (array or JSON string from AJAX).
 *
 * @param mixed $icon Raw setting.
 * @return array Icon data for Icons_Manager or empty array.
 */
function utcb_normalize_elementor_icon_setting($icon)
{
    if (empty($icon)) {
        return [];
    }
    if (is_string($icon)) {
        $decoded = json_decode($icon, true);
        $icon = is_array($decoded) ? $decoded : [];
    }
    if (!is_array($icon) || empty($icon['value'])) {
        return [];
    }
    return $icon;
}

/**
 * Enqueue Font Awesome when the icon library requires it (AJAX fragments do not inherit editor bundles).
 *
 * @param array $icon Normalized icon data.
 */
function utcb_maybe_enqueue_font_awesome_for_icon(array $icon)
{
    $lib = isset($icon['library']) ? $icon['library'] : '';
    if (in_array($lib, ['fa-solid', 'fa-regular', 'fa-brands'], true)) {
        wp_enqueue_style('font-awesome');
    }
}

/**
 * Render Elementor icon to HTML string; do not pass through wp_kses_post (strips SVG / breaks icons).
 *
 * @param array $icon Normalized icon data.
 * @param array $attr Attributes for Icons_Manager::render_icon.
 * @return string
 */
function utcb_render_elementor_icon_html(array $icon, array $attr = [])
{
    if (empty($icon)) {
        return '';
    }
    utcb_maybe_enqueue_font_awesome_for_icon($icon);
    ob_start();
    \Elementor\Icons_Manager::render_icon($icon, $attr);
    return ob_get_clean();
}


// new handler 

function utcb_get_post_data($post_id)
{
    $rent_id = get_post_meta($post_id, 'link_mptbm_id', true);

    $image_url = '';
    if ($rent_id) {
        $image_url = get_the_post_thumbnail_url($rent_id, 'large');
    }
    if (!$image_url && has_post_thumbnail($post_id)) {
        $image_url = get_the_post_thumbnail_url($post_id, 'large');
    }

    $term_ids = [];
    if ($rent_id) {
        $terms = get_the_terms($rent_id, 'mptbm_category');
        if ($terms && !is_wp_error($terms)) {
            $term_ids = wp_list_pluck($terms, 'term_id');
        }
    }

    $extra_info = $rent_id ? get_post_meta($rent_id, 'mptbm_extra_info', true) : '';

    $loc_terms = $rent_id ? get_the_terms($rent_id, 'locations') : [];
    $location = (!empty($loc_terms) && !is_wp_error($loc_terms)) ? $loc_terms[0]->name : '';

    return [
        'post_id' => $post_id,
        'rent_id' => $rent_id,
        'image_url' => $image_url,
        'category_string' => implode(',', array_map('intval', $term_ids)),
        'extra_info' => $extra_info,
        'location' => $location,
    ];
}
// end



add_action('wp_ajax_utcb_get_post_info', 'urban_taxi_cab_get_post_info');
add_action('wp_ajax_nopriv_utcb_get_post_info', 'urban_taxi_cab_get_post_info');

function urban_taxi_cab_get_post_info()
{
    check_ajax_referer('utcb_nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
        wp_die();
    }

    $rent_id = get_post_meta($post_id, 'link_mptbm_id', true);
    $settings_json = isset($_POST['settings']) ? wp_unslash($_POST['settings']) : '{}';
    $settings = json_decode($settings_json, true) ?: [];

    ob_start();
    urban_taxi_cab_booking_slider_render_post_card($post_id, $rent_id, [], $settings);
    $info_html = ob_get_clean();

    wp_send_json_success(['info_html' => $info_html]);
    wp_die();
}

/**
 * Render post card HTML for overlay (AJAX).
 */
function urban_taxi_cab_booking_slider_render_post_card($post_id, $rent_id, $term_ids, $settings)
{
    $settings = wp_parse_args($settings, [
        'show_read_more' => 'yes',
        'read_more_text' => 'Read More',
        'read_more_align' => 'left',
        'read_more_icon' => [],
        'read_more_url' => [],
        'content_meta_icon' => [],
    ]);

    $read_more_icon = utcb_normalize_elementor_icon_setting($settings['read_more_icon']);
    $icon_html = utcb_render_elementor_icon_html(
        $read_more_icon,
        ['aria-hidden' => 'true', 'class' => 'post-read-more-icon']
    );

    $content_meta_icon = utcb_normalize_elementor_icon_setting($settings['content_meta_icon']);
    $content_meta_icon_prefix = '';
    if (!empty($content_meta_icon)) {
        $meta_raw = utcb_render_elementor_icon_html(
            $content_meta_icon,
            [
                'class' => 'utcb-meta-icon',
                'aria-hidden' => 'true',
            ]
        );
        if ($meta_raw !== '') {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor icon markup (SVG/i).
            $content_meta_icon_prefix = '<span class="me-2 utcb-meta-icon-wrap">' . $meta_raw . '</span> ';
        }
    }

    $currency_symbol = get_woocommerce_currency_symbol();
    ?>
    <div class="utcb-post-item utcb-post-card">
        <div class="utcb-post-content-block">
            <?php if ($rent_id):
                $km_price = get_post_meta($rent_id, 'mptbm_km_price', true);
                $extra_info_desc = get_post_meta($rent_id, 'mptbm_extra_info', true);

                $seating_capacity = '';
                $additional_charge_km = '';
                $additional_passenger = '';

                $features = get_post_meta($rent_id, 'mptbm_features', true);

                if (!empty($features) && is_array($features)) {
                    foreach ($features as $feature) {
                        if (!isset($feature['label'])) {
                            continue;
                        }
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
                <?php /*if ($extra_info_desc): ?>
                                         <p class="extra-info"><?php echo esc_html($extra_info_desc); ?></p>
                                     <?php endif; */ ?>
                <div class="utcb-taxi-meta">
                    <?php if ($additional_passenger): ?>
                        <div class="cabs-booking-contents">
                            <div>
                                <?php
                                if ($content_meta_icon_prefix) {
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo $content_meta_icon_prefix;
                                }
                                ?>
                                <strong>Base Fare</strong>
                            </div>
                            <div class="counts"><?php echo esc_html($currency_symbol); ?><?php echo esc_html($additional_passenger); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($seating_capacity): ?>
                        <div class="cabs-booking-contents">
                            <div>
                                <?php
                                if ($content_meta_icon_prefix) {
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo $content_meta_icon_prefix;
                                }
                                ?>
                                <strong>Passenger Seats</strong>
                            </div>
                            <div class="counts"><?php echo esc_html($seating_capacity); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($additional_charge_km): ?>
                        <div class="cabs-booking-contents">
                            <div>
                                <?php
                                if ($content_meta_icon_prefix) {
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo $content_meta_icon_prefix;
                                }
                                ?>
                                <strong>Additional Charge / KM</strong>
                            </div>
                            <div class="counts"><?php echo esc_html($currency_symbol); ?><?php echo esc_html($additional_charge_km); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="cabs-booking-price-button">
                <?php if ($settings['show_read_more'] === 'yes'): ?>
                    <div class="post-read-more-wrapper"
                        style="text-align: <?php echo esc_attr($settings['read_more_align']); ?>;">
                        <?php
                        $button_url_ajax = '#';

                        if (!empty($settings['read_more_url']['url'])) {
                            $button_url_ajax = $settings['read_more_url']['url'];
                        } else {
                            $booking_page = get_page_by_path('booking-form');
                            if (!$booking_page) {
                                $booking_page = get_page_by_title('Booking Form');
                            }
                            if ($booking_page) {
                                $button_url_ajax = get_permalink($booking_page->ID);
                            }
                        }
                        ?>
                        <a href="<?php echo esc_url($button_url_ajax); ?>" class="post-read-more-btn" target="_blank"
                            rel="noopener">                            
                            <span class="car-booking-btn-text">
                                <?php echo esc_html($settings['read_more_text']); ?>
                            </span>
                            <?php if ($icon_html): ?>
                                <?php
                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                echo $icon_html; ?>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="utcb-post-media-block d-none">
            <?php
            $image_url = '';
            if ($rent_id) {
               $image_url = get_the_post_thumbnail_url($rent_id, 'large');
            }
            if (!$image_url && has_post_thumbnail($post_id)) {
                $image_url = get_the_post_thumbnail_url($post_id, 'large');
            }
            ?>
            <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" class="utcb-post-thumbnail"
                    alt="<?php echo esc_attr(get_the_title()); ?>">
            <?php endif; ?>
        </div>
    </div>
    <?php
}


// new handler for max tittles 

add_action('wp_ajax_utcb_get_posts_by_category', 'utcb_get_posts_by_category');
add_action('wp_ajax_nopriv_utcb_get_posts_by_category', 'utcb_get_posts_by_category');

function utcb_get_posts_by_category()
{
    check_ajax_referer('utcb_nonce', 'nonce');

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    $max_titles = isset($_POST['max_titles']) ? intval($_POST['max_titles']) : 10;

    $settings_json = isset($_POST['settings']) ? wp_unslash($_POST['settings']) : '{}';
    $settings = json_decode($settings_json, true) ?: [];

    $meta_query = [
        [
            'key' => 'link_mptbm_id',
            'compare' => 'EXISTS',
        ],
    ];

    if ($category !== 'all') {

        $rent_posts = get_posts([
            'post_type' => 'mptbm_rent',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'tax_query' => [
                [
                    'taxonomy' => 'mptbm_category',
                    'field' => 'term_id',
                    'terms' => intval($category),
                ],
            ],
        ]);

        // If no rent posts → return empty
        if (empty($rent_posts)) {
            wp_send_json_success(['html' => '']);
            return;
        }

        // Step 2: Filter products by those rent IDs
        $meta_query[] = [
            'key' => 'link_mptbm_id',
            'value' => $rent_posts,
            'compare' => 'IN',
        ];
    }

    $args = [
        'post_type' => 'product',
        'posts_per_page' => $max_titles,
        'post_status' => 'publish',
        'meta_query' => $meta_query,
    ];

    $query = new WP_Query($args);

    $first_post_id = 0;
    $first_info_html = '';

    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $post_id = get_the_ID();
            $data = utcb_get_post_data($post_id);

            // new add

            if ($first_post_id === 0) {
                $first_post_id = $post_id;

                ob_start();
                urban_taxi_cab_booking_slider_render_post_card(
                    $post_id,
                    $data['rent_id'],
                    [],
                    $settings
                );
                $first_info_html = ob_get_clean();
            }
            // end

            // for single page
            $rent_id = get_post_meta($post_id, 'link_mptbm_id', true);
            $rent_post = $rent_id ? get_post($rent_id) : null;

            $tittle_url = $settings['tittle_url']['url'] ?? '';
            $final_url = !empty($tittle_url) ? $tittle_url : ($rent_post ? get_permalink($rent_post->ID) : '#');
            //end
            ?>
            <div class="utcb-title-item d-flex" data-post-id="<?php echo esc_attr($post_id); ?>"
                data-image-src="<?php echo esc_url($data['image_url']); ?>"
                data-category="<?php echo esc_attr($data['category_string']); ?>">

                <div class="utcb-title-icon-wrapper">
                    <?php if ('yes' === ($settings['enable_sidebar_title_icon'] ?? 'yes')): ?>
                        <?php
                        $title_icon = utcb_normalize_elementor_icon_setting($settings['sidebar_title_icon'] ?? []);
                        $title_icon_html = utcb_render_elementor_icon_html(
                            $title_icon,
                            ['class' => 'utcb-sidebar-title-icon', 'aria-hidden' => 'true']
                        );
                        if (!empty($title_icon_html)) {
                            echo '<span class="utcb-sidebar-title-icon-wrap">' . $title_icon_html . '</span>'; // phpcs:ignore
                        }
                        ?>
                    <?php endif; ?>
                </div>
                <div class="utcb-title-contents-wrapper">
                    <h3 class="utcb-title-heading">
                        <a href="<?php echo esc_url($final_url); ?>" target="_blank" rel="noopener">
                            <?php echo esc_html(get_the_title()); ?>
                        </a>
                    </h3>
                    <?php if ($data['extra_info']): ?>
                        <div class="utcb-title-desc"><?php echo esc_html($data['extra_info']); ?></div>
                    <?php endif; ?>

                    <?php if ($data['location']):
                        $loc_icon = utcb_normalize_elementor_icon_setting($settings['sidebar_location_icon'] ?? []);
                        $loc_icon_html = utcb_render_elementor_icon_html(
                            $loc_icon,
                            ['class' => 'utcb-title-location-icon me-1', 'aria-hidden' => 'true']
                        );
                        ?>
                        <div class="utcb-title-location">
                            <?php echo $loc_icon_html; // phpcs:ignore ?>

                            <?php
                            $location_url = $settings['location_url']['url'] ?? '';
                            if (empty($location_url)) {
                                $term = get_term_by('name', $data['location'], 'locations');

                                if ($term && !is_wp_error($term)) {
                                    $location_url = get_term_link($term);
                                }
                            }

                            echo '<a href="' . esc_url($location_url) . '" target="_blank" rel="noopener" class="utcb-location-link">';
                            echo esc_html($data['location']);
                            echo '</a>';
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <?php

        }

        $show_val = isset($settings['show_view_all_location']) && $settings['show_view_all_location'] !== '' ? $settings['show_view_all_location'] : 'yes';
        if ('yes' === $show_val) {
            $val_text = !empty($settings['view_all_location_text']) ? $settings['view_all_location_text'] : __('View All Location', 'urban-taxi-cab-booking-slider');
            $val_url = utcb_get_shop_url(!empty($settings['view_all_location_url']['url']) ? $settings['view_all_location_url']['url'] : '');
            $val_external = !empty($settings['view_all_location_url']['is_external']);
            $val_icon = utcb_normalize_elementor_icon_setting($settings['view_all_location_icon'] ?? []);
            $val_icon_html = utcb_render_elementor_icon_html(
                $val_icon,
                ['class' => 'utcb-view-all-location-icon', 'aria-hidden' => 'true']
            );
            $val_position = !empty($settings['view_all_location_icon_position']) ? $settings['view_all_location_icon_position'] : 'before';
            ?>
            <div class="utcb-view-all-location-row">
                <a href="<?php echo esc_url($val_url); ?>" class="utcb-view-all-location-inner"
                    <?php echo $val_external ? 'target="_blank" rel="noopener"' : ''; ?>>
                    <?php if ('before' === $val_position && $val_icon_html): ?>
                        <?php echo $val_icon_html; // phpcs:ignore ?>
                    <?php endif; ?>
                    <span class="utcb-view-all-location-text"><?php echo esc_html($val_text); ?></span>
                    <?php if ('after' === $val_position && $val_icon_html): ?>
                        <?php echo $val_icon_html; // phpcs:ignore ?>
                    <?php endif; ?>
                </a>
            </div>
            <?php
        }
    }

    wp_reset_postdata();

    wp_send_json_success([
        'html' => ob_get_clean(),
        'first_info' => $first_info_html
    ]);
}
