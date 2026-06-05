<?php
/**
 * Urban Taxi Cab Booking Slider Widget for Elementor
 *
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if Elementor is loaded
if (!did_action('elementor/loaded')) {
    return;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

// ---------------------------------------------------------------------------
// Helper functions (originally defined in the standalone plugin's main file).
// Guarded with function_exists() so they coexist if the old plugin is active.
// ---------------------------------------------------------------------------

if (!function_exists('utcb_get_shop_url')) {
    function utcb_get_shop_url($url = '')
    {

        // If a URL is explicitly provided, use it.
        if (!empty($url)) {
            return $url;
        }

        // Prefer the "Taxis" page (slug: all-cars).
        $taxis_page = get_page_by_path('all-cars');

        if ($taxis_page) {
            $taxis_page_url = get_permalink($taxis_page->ID);

            if (!empty($taxis_page_url)) {
                return $taxis_page_url;
            }
        }

        // Fallback to WooCommerce shop page if available.
        if (function_exists('wc_get_page_id')) {
            $shop_page_id = wc_get_page_id('shop');

            if ($shop_page_id && $shop_page_id > 0) {
                $shop_url = get_permalink($shop_page_id);

                if (!empty($shop_url)) {
                    return $shop_url;
                }
            }
        }

        // Final fallback.
        return home_url('/all-cars/');
    }
}

if (!function_exists('utcb_normalize_elementor_icon_setting')) {
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
}

if (!function_exists('utcb_maybe_enqueue_font_awesome_for_icon')) {
    function utcb_maybe_enqueue_font_awesome_for_icon(array $icon)
    {
        $lib = isset($icon['library']) ? $icon['library'] : '';
        if (in_array($lib, ['fa-solid', 'fa-regular', 'fa-brands'], true)) {
            wp_enqueue_style('font-awesome');
        }
    }
}

if (!function_exists('utcb_render_elementor_icon_html')) {
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
}

if (!function_exists('utcb_get_post_data')) {
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
}

if (!function_exists('urban_taxi_cab_booking_slider_render_post_card')) {
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
                ['class' => 'utcb-meta-icon', 'aria-hidden' => 'true']
            );
            if ($meta_raw !== '') {
                $content_meta_icon_prefix = '<span class="me-2 utcb-meta-icon-wrap">' . $meta_raw . '</span> '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }

        $currency_symbol = function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '';
        ?>
        <div class="utcb-post-item utcb-post-card">
            <div class="utcb-post-content-block">
                <?php if ($rent_id):
                    $km_price = get_post_meta($rent_id, 'mptbm_km_price', true);
                    $extra_info_desc = get_post_meta($rent_id, 'mptbm_extra_info', true);
                    $initial_price = get_post_meta($rent_id, 'mptbm_initial_price', true);
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
                    <div class="utcb-taxi-meta">
                       <?php if ($initial_price): ?>
                            <div class="cabs-booking-contents">
                                <div>
                                    <?php if ($content_meta_icon_prefix) {
                                        echo $content_meta_icon_prefix;
                                    } // phpcs:ignore ?>
                                    <strong>Base Fare</strong>
                                </div>
                                <div class="counts">
                                    <?php echo esc_html($currency_symbol); ?>                <?php echo esc_html($initial_price); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($seating_capacity): ?>
                            <div class="cabs-booking-contents">
                                <div>
                                    <?php if ($content_meta_icon_prefix) {
                                        echo $content_meta_icon_prefix;
                                    } // phpcs:ignore ?>
                                    <strong>Passenger Seats</strong>
                                </div>
                                <div class="counts"><?php echo esc_html($seating_capacity); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($additional_charge_km): ?>
                            <div class="cabs-booking-contents">
                                <div>
                                    <?php if ($content_meta_icon_prefix) {
                                        echo $content_meta_icon_prefix;
                                    } // phpcs:ignore ?>
                                    <strong>Additional Charge / KM</strong>
                                </div>
                                <div class="counts">
                                    <?php echo esc_html($currency_symbol); ?>                <?php echo esc_html($additional_charge_km); ?></div>
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
                                <span class="car-booking-btn-text"><?php echo esc_html($settings['read_more_text']); ?></span>
                                <?php if ($icon_html) {
                                    echo $icon_html;
                                } // phpcs:ignore ?>
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
}

if (!function_exists('urban_taxi_cab_get_post_info')) {
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
    add_action('wp_ajax_utcb_get_post_info', 'urban_taxi_cab_get_post_info');
    add_action('wp_ajax_nopriv_utcb_get_post_info', 'urban_taxi_cab_get_post_info');
}

if (!function_exists('utcb_get_posts_by_category')) {
    function utcb_get_posts_by_category()
    {
        check_ajax_referer('utcb_nonce', 'nonce');

        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
        $max_titles = isset($_POST['max_titles']) ? intval($_POST['max_titles']) : 10;

        $settings_json = isset($_POST['settings']) ? wp_unslash($_POST['settings']) : '{}';
        $settings = json_decode($settings_json, true) ?: [];

        $meta_query = [['key' => 'link_mptbm_id', 'compare' => 'EXISTS']];

        if ($category !== 'all') {
            $rent_posts = get_posts([
                'post_type' => 'mptbm_rent',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'tax_query' => [['taxonomy' => 'mptbm_category', 'field' => 'term_id', 'terms' => intval($category)]],
            ]);
            if (empty($rent_posts)) {
                wp_send_json_success(['html' => '']);
                return;
            }
            $meta_query[] = ['key' => 'link_mptbm_id', 'value' => $rent_posts, 'compare' => 'IN'];
        }

        $query = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => $max_titles,
            'post_status' => 'publish',
            'meta_query' => $meta_query,
        ]);

        $first_post_id = 0;
        $first_info_html = '';

        ob_start();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $data = utcb_get_post_data($post_id);

                if ($first_post_id === 0) {
                    $first_post_id = $post_id;
                    ob_start();
                    urban_taxi_cab_booking_slider_render_post_card($post_id, $data['rent_id'], [], $settings);
                    $first_info_html = ob_get_clean();
                }

                $rent_id = get_post_meta($post_id, 'link_mptbm_id', true);
                $rent_post = $rent_id ? get_post($rent_id) : null;
                $tittle_url = $settings['tittle_url']['url'] ?? '';
                $final_url = !empty($tittle_url) ? $tittle_url : ($rent_post ? get_permalink($rent_post->ID) : '#');
                ?>
                <div class="utcb-title-item d-flex" data-post-id="<?php echo esc_attr($post_id); ?>"
                    data-image-src="<?php echo esc_url($data['image_url']); ?>"
                    data-category="<?php echo esc_attr($data['category_string']); ?>">
                    <div class="utcb-title-icon-wrapper">
                        <?php if ('yes' === ($settings['enable_sidebar_title_icon'] ?? 'yes')):
                            $title_icon = utcb_normalize_elementor_icon_setting($settings['sidebar_title_icon'] ?? []);
                            $title_icon_html = utcb_render_elementor_icon_html($title_icon, ['class' => 'utcb-sidebar-title-icon', 'aria-hidden' => 'true']);
                            if (!empty($title_icon_html)) {
                                echo '<span class="utcb-sidebar-title-icon-wrap">' . $title_icon_html . '</span>'; // phpcs:ignore
                            }
                        endif; ?>
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
                            $loc_icon_html = utcb_render_elementor_icon_html($loc_icon, ['class' => 'utcb-title-location-icon me-1', 'aria-hidden' => 'true']);
                            $location_url = $settings['location_url']['url'] ?? '';
                            if (empty($location_url)) {
                                $term = get_term_by('name', $data['location'], 'locations');
                                if ($term && !is_wp_error($term)) {
                                    $location_url = get_term_link($term);
                                }
                            }
                            ?>
                            <div class="utcb-title-location">
                                <?php echo $loc_icon_html; // phpcs:ignore ?>
                                <a href="<?php echo esc_url($location_url); ?>" target="_blank" rel="noopener" class="utcb-location-link">
                                    <?php echo esc_html($data['location']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html, 'first_post_id' => $first_post_id, 'first_info_html' => $first_info_html]);
        wp_die();
    }
    add_action('wp_ajax_utcb_get_posts_by_category', 'utcb_get_posts_by_category');
    add_action('wp_ajax_nopriv_utcb_get_posts_by_category', 'utcb_get_posts_by_category');
}

// ---------------------------------------------------------------------------

/**
 * Urban Taxi Cab Booking Slider Widget
 */
class Urban_Taxi_Cab_Booking_Slider_Widget extends Widget_Base
{

    /**
     * Get widget name
     */
    public function get_name()
    {
        return 'urban-taxi-cab-booking-slider-widget';
    }

    /**
     * Get widget title
     */
    public function get_title()
    {
        return __('Urban Taxi Cab Booking Slider', 'urban-taxi-cab-booking-slider');
    }

    /**
     * Get widget icon
     */
    public function get_icon()
    {
        return 'eicon-posts-carousel';
    }

    /**
     * Get widget categories
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'urban-taxi-cab-booking-slider'),
            ]
        );

        $this->add_control(
            'max_titles',
            [
                'label' => __('Max Posts to Load On Sidebar', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'height_desktop',
            [
                'label' => __('Height for Desktop (1201px - 1920px)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 200, 'max' => 800],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
            ]
        );

        $this->add_control(
            'height_laptop',
            [
                'label' => __('Height for Laptop (1025px - 1200px)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 200, 'max' => 800],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 450,
                ],
            ]
        );

        $this->add_control(
            'height_tablet_portrait',
            [
                'label' => __('Height for Tablet Portrait (992px - 1024px)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 200, 'max' => 800],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
            ]
        );

        $this->add_control(
            'height_tablet',
            [
                'label' => __('Height for Tablet (768px - 991px)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 200, 'max' => 800],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 350,
                ],
            ]
        );

        $this->add_control(
            'height_mobile_portrait',
            [
                'label' => __('Height for Mobile Portrait (576px - 767px)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 200, 'max' => 800],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
            ]
        );

        $this->add_control(
            'height_mobile',
            [
                'label' => __('Height for Mobile (up to 575px)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 200, 'max' => 800],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 250,
                ],
            ]
        );

        $this->add_control(
            'enable_category_filter',
            [
                'label' => __('Enable Category Filter Tabs', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'category_filter_bg_color',
            [
                'label' => __('Tab Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f0f0f0',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'category_filter_color',
            [
                'label' => __('Tab Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_filter_active_bg_color',
            [
                'label' => __('Active Tab Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn.active' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_filter_active_color',
            [
                'label' => __('Active Tab Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn.active' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_filter_active_border_color',
            [
                'label' => __('Active Tab Border Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn.active' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_filter_border_radius',
            [
                'label' => __('Tab Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 25,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_filter_border_color',
            [
                'label' => __('Tab Border Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_filter_bg_hover_color',
            [
                'label' => __('Tab Background Hover Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_filter_hover_color',
            [
                'label' => __('Tab Hover Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_container_bg_color',
            [
                'label' => __('Filter Container Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .utcb-category-filter' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_container_border_radius',
            [
                'label' => __('Filter Container Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-category-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_container_border_color',
            [
                'label' => __('Filter Container Border Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .utcb-category-filter' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'category_filter_typography',
                'label' => __('Tab Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-filter-btn',
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'category_filter_active_typography',
                'label' => __('Active Tab Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-filter-btn.active',
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_filter_padding',
            [
                'label' => __('Tab Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '20',
                    'bottom' => '10',
                    'left' => '20',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'tablet_default' => [
                    'top' => '8',
                    'right' => '16',
                    'bottom' => '8',
                    'left' => '16',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'mobile_default' => [
                    'top' => '8',
                    'right' => '14',
                    'bottom' => '8',
                    'left' => '14',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_filter_gap',
            [
                'label' => __('Gap Between Tabs and Slider', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 25,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-category-filter' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_category_filter' => 'yes',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'read_more_section',
            [
                'label' => __('Read More Button', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_read_more',
            [
                'label' => __('Show Read More Button', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => __('Button Text', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Read More', 'urban-taxi-cab-booking-slider'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_align',
            [
                'label' => __('Button Alignment', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_icon',
            [
                'label' => __('Icon', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-arrow-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_url',
            [
                'label' => __('Button URL', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'urban-taxi-cab-booking-slider'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'url_section',
            [
                'label' => __('Url', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'location_url',
            [
                'label' => __('Location URL', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-location-link.com', 'urban-taxi-cab-booking-slider'),
                'default' => [
                    'url' => '',
                    'is_external' => '',
                ],
            ]
        );

        $this->add_control(
            'tittle_url',
            [
                'label' => __('Tittle URL', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-location-link.com', 'urban-taxi-cab-booking-slider'),
                'default' => [
                    'url' => '',
                    'is_external' => '',
                ],
            ]
        );



        $this->end_controls_section();


        // ── View All Location ─────────────────────────────────────────────
        $this->start_controls_section(
            'view_all_location_section',
            [
                'label' => __('View All Location', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_view_all_location',
            [
                'label' => __('Show View All Location', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'view_all_location_text',
            [
                'label' => __('Link Text', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => __('View All Location', 'urban-taxi-cab-booking-slider'),
                'condition' => ['show_view_all_location' => 'yes'],
            ]
        );

        $this->add_control(
            'view_all_location_url',
            [
                'label' => __('Link URL', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
                'placeholder' => __('https://your-link.com', 'urban-taxi-cab-booking-slider'),
                'default' => ['url' => '', 'is_external' => ''],
                'condition' => ['show_view_all_location' => 'yes'],
            ]
        );

        $this->add_control(
            'view_all_location_icon',
            [
                'label' => __('Icon', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-map-marker-alt',
                    'library' => 'fa-solid',
                ],
                'condition' => ['show_view_all_location' => 'yes'],
            ]
        );

        $this->add_control(
            'view_all_location_icon_position',
            [
                'label' => __('Icon Position', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'before' => __('Before Text', 'urban-taxi-cab-booking-slider'),
                    'after' => __('After Text', 'urban-taxi-cab-booking-slider'),
                ],
                'default' => 'before',
                'condition' => ['show_view_all_location' => 'yes'],
            ]
        );

        $this->end_controls_section();
        // ── End View All Location ─────────────────────────────────────────


        $this->start_controls_section(
            'read_more_style_section',
            [
                'label' => __('Read More Button Style', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_bg_color',
            [
                'label' => __('Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_text_color',
            [
                'label' => __('Text Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_hover_bg_color',
            [
                'label' => __('Hover Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#005a87',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_hover_text_color',
            [
                'label' => __('Hover Text Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_padding',
            [
                'label' => __('Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '20',
                    'bottom' => '10',
                    'left' => '20',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_border_radius',
            [
                'label' => __('Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'read_more_typography',
                'label' => __('Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .post-read-more-btn, {{WRAPPER}} .post-read-more-btn .car-booking-btn-text',
            ]
        );

        $this->add_control(
            'read_more_icon_size',
            [
                'label' => __('Icon Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .post-read-more-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_icon_color',
            [
                'label' => __('Icon Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .post-read-more-btn svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .post-read-more-btn svg path' => 'fill: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'read_more_icon_bg_color',
            [
                'label' => __('Icon Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(255,255,255,0.2)',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .post-read-more-btn svg' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_icon_padding',
            [
                'label' => __('Icon Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => '4',
                    'right' => '4',
                    'bottom' => '4',
                    'left' => '4',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .post-read-more-btn svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn .elementor-button-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn .elementor-button-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn .elementor-button-icon svg path' => 'fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-post-card:hover .post-read-more-btn svg path' => 'fill: {{VALUE}} !important;',
                ],
            ]
        );

        // $this->add_control(
        //     'read_more_icon_hover_bg_color',
        //     [
        //         'label' => __('Icon Hover Background Color', 'urban-taxi-cab-booking-slider'),
        //         'type' => Controls_Manager::COLOR,
        //         'default' => 'rgba(255,255,255,0.3)',
        //         'selectors' => [
        //             '{{WRAPPER}} .utcb-post-content-block:hover .post-read-more-btn:after' => 'background: {{VALUE}};',
        //         ],
        //     ]
        // );

        $this->add_control(
            'read_more_icon_border_radius',
            [
                'label' => __('Icon Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .post-read-more-btn svg' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();



        $this->start_controls_section(
            'sidebar_title_icon_style_section',
            [
                'label' => __('Sidebar Title Icon', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'enable_sidebar_title_icon',
            [
                'label' => __('Enable Sidebar Title Icon', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'sidebar_title_icon',
            [
                'label' => __('Sidebar Title Icon', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-car',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_size',
            [
                'label' => __('Icon Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 40,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 18,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-sidebar-title-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_color',
            [
                'label' => __('Icon Color (Inactive)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item:not(.active) .utcb-sidebar-title-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .utcb-title-item:not(.active) .utcb-sidebar-title-icon svg path' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_active_color',
            [
                'label' => __('Icon Color (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item.active .utcb-sidebar-title-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .utcb-title-item.active .utcb-sidebar-title-icon svg path' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item:hover:not(.active) .utcb-sidebar-title-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .utcb-title-item:hover:not(.active) .utcb-sidebar-title-icon svg path' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_bg_color',
            [
                'label' => __('Icon Background Color (Inactive)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item:not(.active) .utcb-sidebar-title-icon-wrap' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_bg_active_color',
            [
                'label' => __('Icon Background Color (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item.active .utcb-sidebar-title-icon-wrap' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_border_width',
            [
                'label' => __('Border Width', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-sidebar-title-icon-wrap' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_border_color',
            [
                'label' => __('Border Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#dddddd',
                'selectors' => [
                    '{{WRAPPER}} .utcb-sidebar-title-icon-wrap' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sidebar_title_icon_border_radius',
            [
                'label' => __('Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 100],
                    '%' => ['min' => 0, 'max' => 50],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-sidebar-title-icon-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'sidebar_title_icon_margin',
            [
                'label' => __('Icon Margin', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => '0',
                    'right' => '8',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-sidebar-title-icon-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'sidebar_title_icon_padding',
            [
                'label' => __('Icon Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-sidebar-title-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_sidebar_title_icon' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();



        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Title Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-title-list .utcb-title-heading a',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color (Inactive)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item:not(.active) .utcb-title-heading a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_active_color',
            [
                'label' => __('Title Color (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item.active .utcb-title-heading a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => __('Title Hover Color', 'urban-taxi-cab-booking-slider'),
                'description' => __('Applies when the item is not active.', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item:hover:not(.active) .utcb-title-heading a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_text_align',
            [
                'label' => __('Title Text Align', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-heading' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_item_bg',
            [
                'label' => __('Sidebar Item Background (Inactive)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f8fafc',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item:not(.active)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_item_bg_active',
            [
                'label' => __('Sidebar Item Background (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item.active' => 'background-color: {{VALUE}}; background-image: none;',
                ],
            ]
        );


        $this->add_control(
            'title_item_active_border_color',
            [
                'label' => __('Sidebar Item Border Color (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item.active' => 'border-color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'title_item_border_radius',
            [
                'label' => __('Sidebar Item Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_item_box_padding',
            [
                'label' => __('Sidebar Item Box Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '16',
                    'right' => '16',
                    'bottom' => '16',
                    'left' => '16',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'tablet_default' => [
                    'top' => '14',
                    'right' => '14',
                    'bottom' => '14',
                    'left' => '14',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'mobile_default' => [
                    'top' => '12',
                    'right' => '12',
                    'bottom' => '12',
                    'left' => '12',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-list .utcb-title-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_panel_bg',
            [
                'label' => __('Image Info Panel Background', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.85)',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'overlay_panel_padding',
            [
                'label' => __('Image Info Panel Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'overlay_panel_border_radius',
            [
                'label' => __('Image Info Panel Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '10',
                    'right' => '10',
                    'bottom' => '10',
                    'left' => '10',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_panel_border_style',
            [
                'label' => __('Image Info Panel Border Type', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'solid',
                'options' => [
                    'none' => __('None', 'urban-taxi-cab-booking-slider'),
                    'solid' => __('Solid', 'urban-taxi-cab-booking-slider'),
                    'double' => __('Double', 'urban-taxi-cab-booking-slider'),
                    'dotted' => __('Dotted', 'urban-taxi-cab-booking-slider'),
                    'dashed' => __('Dashed', 'urban-taxi-cab-booking-slider'),
                    'groove' => __('Groove', 'urban-taxi-cab-booking-slider'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'overlay_panel_border_width',
            [
                'label' => __('Image Info Panel Border Width', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => '1',
                    'right' => '1',
                    'bottom' => '1',
                    'left' => '1',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_panel_border_color',
            [
                'label' => __('Image Info Panel Border Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(255, 255, 255, 0.1)',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => __('Content Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-overlay-info .utcb-post-content-block, {{WRAPPER}} .utcb-overlay-info .utcb-post-content-block *',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => __('Content Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-post-content-block, {{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta, {{WRAPPER}} .utcb-overlay-info .cabs-booking-contents, {{WRAPPER}} .utcb-overlay-info .extra-info, {{WRAPPER}} .utcb-overlay-info .cabs-booking-contents strong, {{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta .counts' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_text_align',
            [
                'label' => __('Content Text Align', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urban-taxi-cab-booking-slider'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'prefix_class' => 'utcb-content%s-align-',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_label_color',
            [
                'label' => __('Label Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_extra_info_color',
            [
                'label' => __('Extra Info Color(Description)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .extra-info, {{WRAPPER}} .utcb-overlay-info .cabs-booking-contents strong, {{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta .counts' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cabs_booking_contents_typography',
                'label' => __('Taxi Meta Row Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-overlay-info .cabs-booking-contents, {{WRAPPER}} .utcb-overlay-info .cabs-booking-contents strong, {{WRAPPER}} .utcb-overlay-info .cabs-booking-contents .counts',
            ]
        );

        $this->add_control(
            'cabs_booking_contents_color',
            [
                'label' => __('Taxi Meta Row Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .cabs-booking-contents, {{WRAPPER}} .utcb-overlay-info .cabs-booking-contents strong, {{WRAPPER}} .utcb-overlay-info .cabs-booking-contents .counts' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cabs_booking_counts_typography',
                'label' => __('Taxi Meta Value Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-overlay-info .cabs-booking-contents .counts',
            ]
        );

        $this->add_control(
            'cabs_booking_counts_color',
            [
                'label' => __('Taxi Meta Value Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .cabs-booking-contents .counts' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_meta_icon',
            [
                'label' => __('Content Icon', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-info-circle',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'content_meta_icon_color',
            [
                'label' => __('Content Icon Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap .utcb-meta-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap svg path' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta .utcb-meta-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta .utcb-meta-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta .utcb-meta-icon svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_meta_icon_size',
            [
                'label' => __('Content Icon Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 30,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap .utcb-meta-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-meta-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta .utcb-meta-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .utcb-overlay-info .utcb-taxi-meta .utcb-meta-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'sidebar_location_icon',
            [
                'label' => __('Location Icon', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-map-marker-alt',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'sidebar_location_icon_color',
            [
                'label' => __('Location Icon Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-location-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .utcb-title-location-icon svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sidebar_location_icon_size',
            [
                'label' => __('Location Icon Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-location-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );



        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sidebar_desc_typography',
                'label' => __('Description Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-title-desc',
            ]
        );

        $this->add_control(
            'sidebar_desc_color',
            [
                'label' => __('Description Color (Inactive)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item:not(.active) .utcb-title-desc' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sidebar_desc_active_color',
            [
                'label' => __('Description Color (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ddd',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item.active .utcb-title-desc' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sidebar_desc_font_size',
            [
                'label' => __('Description Font Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-desc' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sidebar_location_typography',
                'label' => __('Location Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-title-location',
            ]
        );

        $this->add_control(
            'sidebar_location_color',
            [
                'label' => __('Location Color (Inactive)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item:not(.active) .utcb-title-location' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sidebar_location_active_color',
            [
                'label' => __('Location Color (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#99d6ff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item.active .utcb-title-location' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sidebar_location_font_size',
            [
                'label' => __('Location Font Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 18,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 13,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-location' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'location_link_color',
            [
                'label' => __('Location Link Color (Inactive)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item:not(.active) .utcb-location-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'location_link_active_color',
            [
                'label' => __('Location Link Color (Active)', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#99d6ff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item.active .utcb-location-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'location_link_hover_color',
            [
                'label' => __('Location Link Hover Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#005a87',
                'selectors' => [
                    '{{WRAPPER}} .utcb-title-item .utcb-location-link:hover' => 'color: {{VALUE}}; text-decoration: underline;',
                ],
            ]
        );

        $this->add_control(
            'km_price_color',
            [
                'label' => __('Price per KM Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-km-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'km_price_typography',
                'label' => __('Price per KM Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-overlay-info .utcb-km-price',
            ]
        );

        $this->add_control(
            'km_price_size',
            [
                'label' => __('Price per KM Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 3,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 3,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-km-price .utcb-km-label' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'km_price_label_color',
            [
                'label' => __('Price /km Label Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .utcb-overlay-info .utcb-km-price .utcb-km-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        // ── Right Side Image Box Style ────────────────────────────────────
        $this->start_controls_section(
            'image_box_style_section',
            [
                'label' => __('Right Side Image Box', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_box_bg_color',
            [
                'label' => __('Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .utcb-image-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_box_padding',
            [
                'label' => __('Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-image-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_box_border_radius',
            [
                'label' => __('Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-image-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_control(
            'image_box_border_style',
            [
                'label' => __('Border Type', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'solid',
                'options' => [
                    'none' => __('None', 'urban-taxi-cab-booking-slider'),
                    'solid' => __('Solid', 'urban-taxi-cab-booking-slider'),
                    'double' => __('Double', 'urban-taxi-cab-booking-slider'),
                    'dotted' => __('Dotted', 'urban-taxi-cab-booking-slider'),
                    'dashed' => __('Dashed', 'urban-taxi-cab-booking-slider'),
                    'groove' => __('Groove', 'urban-taxi-cab-booking-slider'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-image-wrapper' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_box_border_width',
            [
                'label' => __('Border Width', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-image-wrapper' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_box_border_color',
            [
                'label' => __('Border Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .utcb-image-wrapper' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();





        // ── End Right Side Image Box Style ────────────────────────────────


        // ── View All Location Style ───────────────────────────────────────
        $this->start_controls_section(
            'view_all_location_style_section',
            [
                'label' => __('View All Location Style', 'urban-taxi-cab-booking-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => ['show_view_all_location' => 'yes'],
            ]
        );

        $this->add_control(
            'view_all_location_text_color',
            [
                'label' => __('Text Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-view-all-location-row a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_hover_color',
            [
                'label' => __('Hover Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcb-view-all-location-row:hover a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_bg_color',
            [
                'label' => __('Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_hover_bg_color',
            [
                'label' => __('Hover Background Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_icon_color',
            [
                'label' => __('Icon Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .utcb-view-all-location-icon svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_icon_size',
            [
                'label' => __('Icon Size', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 10, 'max' => 30]],
                'default' => ['unit' => 'px', 'size' => 14],
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'view_all_location_typography',
                'label' => __('Typography', 'urban-taxi-cab-booking-slider'),
                'selector' => '{{WRAPPER}} .utcb-view-all-location-row a',
            ]
        );

        $this->add_responsive_control(
            'view_all_location_padding',
            [
                'label' => __('Padding', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => '12',
                    'right' => '20',
                    'bottom' => '12',
                    'left' => '20',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_border_color',
            [
                'label' => __('Border Color', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '#5E5E5E',
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_border_width',
            [
                'label' => __('Border Width', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 5]],
                'default' => ['unit' => 'px', 'size' => 1],
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_border_radius',
            [
                'label' => __('Border Radius', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => ['px' => ['min' => 0, 'max' => 50]],
                'default' => ['unit' => 'px', 'size' => 0],
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-row' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'view_all_location_gap',
            [
                'label' => __('Gap Between Icon & Text', 'urban-taxi-cab-booking-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 20]],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .utcb-view-all-location-inner' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        // ── End View All Location Style ───────────────────────────────────
    }

    /**
     * Render widget output on the frontend
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // new add
        $max_titles = !empty($settings['max_titles']) ? intval($settings['max_titles']) : 10;


        $args = [
            'post_type' => 'product',
            'posts_per_page' => $max_titles,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'link_mptbm_id',
                    'compare' => 'EXISTS',
                ],
            ],
        ];

        $categories = get_terms([
            'taxonomy' => 'mptbm_category',
            'hide_empty' => false,
        ]);

        $posts_query = new WP_Query($args);

        if (!$posts_query->have_posts()) {
            echo '<p>' . esc_html__('No posts found.', 'urban-taxi-cab-booking-slider') . '</p>';
            return;
        }

        // Generate unique ID
        $widget_id = $this->get_id();
        $wrapper_id = 'urban-taxi-cab-booking-widget-' . $widget_id;

        $wrapper_inline_styles = '';
        ?>
        <div class="urban-taxi-cab-booking-widget utcb-new-layout" id="<?php echo esc_attr($wrapper_id); ?>" <?php echo !empty($wrapper_inline_styles) ? 'style="' . esc_attr($wrapper_inline_styles) . '"' : ''; ?>
            data-widget-id="<?php echo esc_attr($widget_id); ?>" data-max-titles="<?php echo esc_attr($max_titles); ?>"
            data-settings="<?php echo esc_attr(wp_json_encode([
                'show_read_more' => $settings['show_read_more'] ?? 'yes',
                'read_more_text' => $settings['read_more_text'] ?? 'Read More',
                'read_more_align' => $settings['read_more_align'] ?? 'left',
                // 'read_more_icon' => !empty($settings['read_more_icon']) ? wp_json_encode($settings['read_more_icon']) : '',
                'read_more_icon' => $settings['read_more_icon'] ?? [],
                'read_more_url' => $settings['read_more_url'] ?? [],
                'content_meta_icon' => $settings['content_meta_icon'] ?? [],
                'sidebar_location_icon' => $settings['sidebar_location_icon'] ?? [],
                'location_url' => $settings['location_url'] ?? [],
                'tittle_url' => $settings['tittle_url'] ?? [],
                'enable_sidebar_title_icon' => $settings['enable_sidebar_title_icon'] ?? 'yes',
                'sidebar_title_icon' => $settings['sidebar_title_icon'] ?? [],
                'show_view_all_location' => $settings['show_view_all_location'] ?? 'yes',
                'view_all_location_text' => $settings['view_all_location_text'] ?? 'View All Location',
                'view_all_location_url' => $settings['view_all_location_url'] ?? [],
                'view_all_location_icon' => $settings['view_all_location_icon'] ?? [],
                'view_all_location_icon_position' => $settings['view_all_location_icon_position'] ?? 'before',
            ])); ?>">

            <?php if ('yes' === $settings['enable_category_filter'] && !empty($categories) && !is_wp_error($categories)): ?>
                <div class="utcb-category-filter">
                    <button class="utcb-filter-btn active" data-category="all">
                        <?php esc_html_e('All Taxis', 'urban-taxi-cab-booking-slider'); ?>
                    </button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="utcb-filter-btn" data-category="<?php echo esc_attr($cat->term_id); ?>">
                            <?php echo esc_html($cat->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Titles Scroll List -->
            <div class="utcb-layout-container">
                <?php
                $first_post_id = !empty($posts_query->posts) ? $posts_query->posts[0]->ID : 0;
                ?>
                <div class="utcb-title-list" data-widget-id="<?php echo esc_attr($widget_id); ?>">
                    <?php
                    while ($posts_query->have_posts()):

                        $posts_query->the_post();
                        $post_id = get_the_ID();
                        $data = utcb_get_post_data($post_id);

                        // for single page
                        $rent_id = get_post_meta($post_id, 'link_mptbm_id', true);
                        $rent_post = $rent_id ? get_post($rent_id) : null;

                        $tittle_url = $settings['tittle_url']['url'] ?? '';
                        $final_url = !empty($tittle_url) ? $tittle_url : ($rent_post ? get_permalink($rent_post->ID) : '#');
                        //end
                        ?>
                        <div class="utcb-title-item <?php echo $post_id == $first_post_id ? 'active' : ''; ?>"
                            data-post-id="<?php echo esc_attr($post_id); ?>"
                            data-image-src="<?php echo esc_attr($data['image_url']); ?>"
                            data-category="<?php echo esc_attr($data['category_string']); ?>">
                            <div class="utcb-title-icon-wrapper">
                                <?php if ('yes' === $settings['enable_sidebar_title_icon']): ?>
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
                                <h3 class="utcb-title-heading">
                                    <a href="<?php echo esc_url($final_url); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                </h3>
                            </div>
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
                    <?php endwhile; ?>

                    <?php
                    $show_val = isset($settings['show_view_all_location']) && $settings['show_view_all_location'] !== '' ? $settings['show_view_all_location'] : 'yes';
                    if ('yes' === $show_val):
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
                            <a href="<?php echo esc_url($val_url); ?>" class="utcb-view-all-location-inner" <?php echo $val_external ? 'target="_blank" rel="noopener"' : ''; ?>>
                                <?php if ($val_position === 'before' && $val_icon_html): ?>
                                    <?php echo $val_icon_html; // phpcs:ignore ?>
                                <?php endif; ?>
                                <span class="utcb-view-all-location-text"><?php echo esc_html($val_text); ?></span>
                                <?php if ($val_position === 'after' && $val_icon_html): ?>
                                    <?php echo $val_icon_html; // phpcs:ignore ?>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Active Post Image -->
                <?php
                $first_post = $posts_query->posts[0] ?? null;
                $first_post_id = $first_post ? $first_post->ID : 0;
                $first_rent_id = $first_post_id ? get_post_meta($first_post_id, 'link_mptbm_id', true) : '';

                $first_image = '';
                if ($first_rent_id)
                    // $first_image = get_post_meta($first_rent_id, 'feature_image', true);
                    $first_image = get_the_post_thumbnail_url($rent_id, 'large');
                if (!$first_image && has_post_thumbnail())
                    $first_image = get_the_post_thumbnail_url($first_post_id, 'large');
                ?>
                <div class="utcb-active-image-container">
                    <?php if ($first_image): ?>
                        <div class="utcb-image-wrapper">
                            <img src="<?php echo esc_url($first_image); ?>"
                                alt="<?php echo esc_attr(get_the_title($first_post_id)); ?>" class="utcb-active-image">
                            <div class="utcb-overlay-info">
                                <div class="loading">Loading info...</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="utcb-placeholder-image">
                            <div class="utcb-overlay-info">No Image</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <style>
                @media (min-width: 1201px) {
                    #<?php echo esc_attr($wrapper_id); ?> .utcb-active-image {
                        height:
                            <?php echo esc_attr($settings['height_desktop']['size'] ?? 500); ?>
                            px !important;
                    }
                }

                @media (min-width: 1025px) and (max-width: 1200px) {
                    #<?php echo esc_attr($wrapper_id); ?> .utcb-active-image {
                        height:
                            <?php echo esc_attr($settings['height_laptop']['size'] ?? 450); ?>
                            px !important;
                    }
                }

                @media (min-width: 992px) and (max-width: 1024px) and (orientation: portrait) {
                    #<?php echo esc_attr($wrapper_id); ?> .utcb-active-image {
                        height:
                            <?php echo esc_attr($settings['height_tablet_portrait']['size'] ?? 400); ?>
                            px !important;
                    }
                }

                @media (min-width: 768px) and (max-width: 991px) {
                    #<?php echo esc_attr($wrapper_id); ?> .utcb-active-image {
                        height:
                            <?php echo esc_attr($settings['height_tablet']['size'] ?? 350); ?>
                            px !important;
                    }
                }

                @media (min-width: 576px) and (max-width: 767px) and (orientation: portrait) {
                    #<?php echo esc_attr($wrapper_id); ?> .utcb-active-image {
                        height:
                            <?php echo esc_attr($settings['height_mobile_portrait']['size'] ?? 300); ?>
                            px !important;
                    }
                }

                @media (max-width: 575px) {
                    #<?php echo esc_attr($wrapper_id); ?> .utcb-active-image {
                        height:
                            <?php echo esc_attr($settings['height_mobile']['size'] ?? 250); ?>
                            px !important;
                    }
                }
            </style>

        </div>
        <?php
        wp_reset_postdata();

    }

}

