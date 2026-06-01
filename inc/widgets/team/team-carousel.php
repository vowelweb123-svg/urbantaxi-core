<?php
if (!defined('ABSPATH')) exit;

class Urbantaxi_Team_Carousel extends \Elementor\Widget_Base {

    public function get_name() {
        return 'team_carousel';
    }

    public function get_title() {
        return __('Team Carousel', 'urbantaxi');
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_script_depends() {
        return ['team-carousel-script'];
    }

    public function get_style_depends() {
        return ['team-carousel-style'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'urbantaxi'),
            ]
        );

        // Dynamic Post Types
        $post_types = get_post_types(['public' => true], 'objects');
        $options = [];

        foreach ($post_types as $post_type) {
            if (in_array($post_type->name, ['attachment', 'elementor_library'])) {
                continue;
            }
            $options[$post_type->name] = $post_type->label;
        }

        // Post Type
        $this->add_control(
            'post_type',
            [
                'label' => __('Select Post Type', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $options,
                'default' => 'post',
            ]
        );

        // Posts Per Page
        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => -1,
            ]
        );

        // Loop
        $this->add_control(
            'loop',
            [
                'label' => __('Loop', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // Slides per view for different breakpoints
        $this->add_control(
            'slides_per_view_desktop',
            [
                'label' => __('Slides Per View - Desktop (1367px+)', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 4,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->add_control(
            'slides_per_view_laptop',
            [
                'label' => __('Slides Per View - Laptop (1200px-1366px)', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->add_control(
            'slides_per_view_tablet_landscape',
            [
                'label' => __('Slides Per View - Tablet Landscape (1025px-1200px)', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->add_control(
            'slides_per_view_tablet',
            [
                'label' => __('Slides Per View - Tablet (881px-1024px)', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 2,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->add_control(
            'slides_per_view_mobile_landscape',
            [
                'label' => __('Slides Per View - Mobile Landscape (768px-880px)', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 2,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->add_control(
            'slides_per_view_mobile',
            [
                'label' => __('Slides Per View - Mobile (320px-767px)', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->end_controls_section(); //ONLY ONE close

        // Social Icons Section
        $this->start_controls_section(
            'social_icons_section',
            [
                'label' => __('Social Icons', 'urbantaxi'),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'social_icon',
            [
                'label' => __('Icon', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fab fa-twitter',
                    'library' => 'fa-brands',
                ],
            ]
        );

        $repeater->add_control(
            'social_url',
            [
                'label' => __('URL', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://example.com', 'urbantaxi'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'social_icons_list',
            [
                'label' => __('Social Icons', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'social_icon' => ['value' => 'fab fa-twitter', 'library' => 'fa-brands'],
                        'social_url' => ['url' => '#'],
                    ],
                    [
                        'social_icon' => ['value' => 'fab fa-instagram', 'library' => 'fa-brands'],
                        'social_url' => ['url' => '#'],
                    ],
                    [
                        'social_icon' => ['value' => 'fab fa-linkedin', 'library' => 'fa-brands'],
                        'social_url' => ['url' => '#'],
                    ],
                    [
                        'social_icon' => ['value' => 'fab fa-youtube', 'library' => 'fa-brands'],
                        'social_url' => ['url' => '#'],
                    ],
                ],
                'title_field' => '{{{ social_icon.value }}}',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'box_style',
            [
                'label' => __('Box', 'urbantaxi'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'box_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'box_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'box_padding',
            [
                'label' => __('Padding', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .team-item',
            ]
        );

        $this->end_controls_section();

        /*
        ========================
        TITLE STYLE
        ========================
        */
        $this->start_controls_section(
            'title_style',
            [
                'label' => __('Title', 'urbantaxi'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Title Color
        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-title a, {{WRAPPER}} .team-title a:link, {{WRAPPER}} .team-title a:visited, {{WRAPPER}} .team-title a:hover, {{WRAPPER}} .team-title a:active' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        // Title Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .team-title a',
            ]
        );

        $this->end_controls_section();

        /*
        ========================
        DESIGNATION STYLE
        ========================
        */
        $this->start_controls_section(
            'designation_style',
            [
                'label' => __('Designation', 'urbantaxi'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Text Color
        $this->add_control(
            'designation_color',
            [
                'label' => __('Text Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-designation' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Background Color
        $this->add_control(
            'designation_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-designation' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'designation_typography',
                'selector' => '{{WRAPPER}} .team-designation',
            ]
        );

        // Padding
        $this->add_responsive_control(
            'designation_padding',
            [
                'label' => __('Padding', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-designation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Border Radius
        $this->add_responsive_control(
            'designation_radius',
            [
                'label' => __('Border Radius', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .team-designation' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /*
        ========================
        SOCIAL ICONS STYLE
        ========================
        */
        $this->start_controls_section(
            'social_style',
            [
                'label' => __('Social Icons', 'urbantaxi'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Icon Color
        $this->add_control(
            'social_color',
            [
                'label' => __('Icon Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-social a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Background Color
        $this->add_control(
            'social_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-social a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Hover Icon Color
        $this->add_control(
            'social_hover_color',
            [
                'label' => __('Hover Icon Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-social a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Hover Background
        $this->add_control(
            'social_hover_bg',
            [
                'label' => __('Hover Background', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .team-social a:hover::after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Icon Size (important)
        $this->add_responsive_control(
            'social_size',
            [
                'label' => __('Icon Size', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .team-social a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Spacing
        $this->add_responsive_control(
            'social_spacing',
            [
                'label' => __('Spacing', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .team-social a' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        /*
        ========================
        DOTS STYLE
        ========================
        */
        $this->start_controls_section(
            'dots_style',
            [
                'label' => __('Dots', 'urbantaxi'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Normal dot color
        $this->add_control(
            'dot_color',
            [
                'label' => __('Dot Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Active dot color
        $this->add_control(
            'dot_active_color',
            [
                'label' => __('Active Dot Color', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Active dot width
        $this->add_responsive_control(
            'dot_active_width',
            [
                'label' => __('Active Dot Width', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Dot Width
        $this->add_responsive_control(
            'dot_width',
            [
                'label' => __('Dot Width', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 5, 'max' => 30],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Dot Height
        $this->add_responsive_control(
            'dot_height',
            [
                'label' => __('Dot Height', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 5, 'max' => 30],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_spacing',
            [
                'label' => __('Dots Spacing', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 0, 'max' => 30],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_margin_top',
            [
                'label' => __('Dots Top Gap', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 0, 'max' => 100],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label' => __('Show Dots', 'urbantaxi'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'urbantaxi'),
                'label_off' => __('Hide', 'urbantaxi'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $query = new \WP_Query([
            'post_type' => $settings['post_type'],
            'posts_per_page' => $settings['posts_per_page'],
        ]);

        if ($query->have_posts()) {

            echo '<div class="team-carousel swiper"
                data-loop="' . esc_attr($settings['loop']) . '"
                data-desktop="' . esc_attr($settings['slides_per_view_desktop']) . '"
                data-laptop="' . esc_attr($settings['slides_per_view_laptop']) . '"
                data-tablet-landscape="' . esc_attr($settings['slides_per_view_tablet_landscape']) . '"
                data-tablet="' . esc_attr($settings['slides_per_view_tablet']) . '"
                data-mobile-landscape="' . esc_attr($settings['slides_per_view_mobile_landscape']) . '"
                data-mobile="' . esc_attr($settings['slides_per_view_mobile']) . '"
                data-dots="' . esc_attr($settings['show_dots'] === 'yes' ? 'yes' : 'no') . '"
            >';

            echo '<div class="swiper-wrapper">';

            while ($query->have_posts()) {
                $query->the_post();

                echo '<div class="swiper-slide">';
                    echo '<div class="team-item">';
                        echo '<div class="team-content-item text-center">';
                            echo '<p class="team-designation">'. esc_html(get_post_meta(get_the_ID(),'driver_designation_text',true)) .'</p>';
                            echo '<div class="team-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . get_the_title() . '</a></div>';
                        echo '</div>';
                        echo get_the_post_thumbnail(get_the_ID(), 'medium');

                        echo '<div class="team-social">';
                        if (!empty($settings['social_icons_list'])) {
                            foreach ($settings['social_icons_list'] as $social_item) {
                                $icon_html = '';
                                if (!empty($social_item['social_icon'])) {
                                    ob_start();
                                    \Elementor\Icons_Manager::render_icon($social_item['social_icon'], ['aria-hidden' => 'true']);
                                    $icon_html = ob_get_clean();
                                }
                                $url = !empty($social_item['social_url']['url']) ? esc_url($social_item['social_url']['url']) : '#';
                                $target = !empty($social_item['social_url']['is_external']) ? 'target="_blank"' : '';
                                $rel = !empty($social_item['social_url']['nofollow']) ? 'rel="nofollow"' : '';
                                echo '<a href="' . $url . '" ' . $target . ' ' . $rel . '>' . $icon_html . '</a>';
                            }
                        }
                        echo '</div>';

                    echo '</div>';
                echo '</div>';
            }

            echo '</div>';
            echo '<div class="swiper-pagination"></div>';
            echo '</div>';

            wp_reset_postdata();

        } else {
            echo 'No posts found';
        }
    }
}