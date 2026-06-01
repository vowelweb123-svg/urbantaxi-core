<?php
use Elementor\Widget_Base;

if (!defined('ABSPATH'))
    exit;

class Urban_Taxi_Cab_Filter_Widget extends Widget_Base
{
    public function get_name()
    {
        return 'urban_taxi_cab_filter_widget';
    }

    public function get_title()
    {
        return 'Urban Taxi Cab Filter Widget';
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['general'];
    }


    protected function register_controls()
    {


        $this->start_controls_section(
            'utcfw_content_section',
            [
                'label' => 'Content Settings',
            ]
        );

        // POSTS PER PAGE
        $this->add_control(
            'posts_per_page',
            [
                'label' => 'Posts Per Page',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        // COLUMNS
        $this->add_control(
            'columns_desktop',
            [
                'label' => 'Desktop Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
            ]
        );

        $this->add_control(
            'columns_laptop',
            [
                'label' => 'Laptop Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
            ]
        );

        $this->add_control(
            'columns_tablet_landscape',
            [
                'label' => 'Tablet Landscape Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
            ]
        );

        $this->add_control(
            'columns_tablet',
            [
                'label' => 'Tablet Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
            ]
        );

        $this->add_control(
            'columns_mobile_landscape',
            [
                'label' => 'Mobile Landscape Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
            ]
        );

        $this->add_control(
            'columns_mobile',
            [
                'label' => 'Mobile Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
            ]
        );

        // SHOW PAGINATION
        $this->add_control(
            'show_pagination',
            [
                'label' => 'Show Pagination',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        // FILTER TITLE
        $this->add_control(
            'filter_title',
            [
                'label' => 'Filter Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Vehicle Types',
            ]
        );

        // new add 

        // READ MORE SECTION
        $this->add_control(
            'show_read_more',
            [
                'label' => 'Show Read More',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => 'Read More Text',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Book Now',
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_url',
            [
                'label' => 'Read More URL',
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
            ]
        );

        // tiitle Url
        $this->add_control(
            'tittle_url',
            [
                'label' => 'Add Tittle Url',
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
            ]
        );

        // end

        $this->end_controls_section();

        // STYLE SECTION REMOVED - MOVED TO CARD

        // FILTER SIDEBAR STYLES
        $this->start_controls_section(
            'utcfw_filter_style_section',
            [
                'label' => 'Filter Sidebar',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'filter_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .utcfw-filter li' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_bg',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_color',
            [
                'label' => 'Active/Hover Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter li.active, {{WRAPPER}} .utcfw-filter li:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_bg',
            [
                'label' => 'Active/Hover Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter li.active, {{WRAPPER}} .utcfw-filter li:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'filter_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 0, 'max' => 30],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter' => 'border-radius: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .utcfw-filter li' => 'border-radius: calc({{SIZE}}{{UNIT}} * 0.7);',
                ],
            ]
        );

        $this->add_responsive_control(
            'filter_font_size',
            [
                'label' => 'Font Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 12, 'max' => 24],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter li' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // NEW: Typography for Filter Sidebar
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_typography',
                'label' => 'Typography',
                'selector' => '{{WRAPPER}} .utcfw-filter li',
            ]
        );

        $this->end_controls_section();

        // FILTER TITLE STYLES
        $this->start_controls_section(
            'utcfw_title_style_section',
            [
                'label' => 'Filter Title',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'filter_title_color',
            [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter h3' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_font_size',
            [
                'label' => 'Font Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 16, 'max' => 36],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter h3' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'filter_title_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-filter h3' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // NEW: Typography for Filter Title
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_title_typography',
                'label' => 'Typography',
                'selector' => '{{WRAPPER}} .utcfw-filter h3',
            ]
        );

        $this->end_controls_section();


        // CARD STYLES
        $this->start_controls_section(
            'utcfw_card_style_section',
            [
                'label' => 'Card',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-card' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_bg',
            [
                'label' => 'Image Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-image' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-card' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        // MOVED FROM OLD STYLE SECTION: Card Background
        $this->add_control(
            'card_bg_moved',
            [
                'label' => 'Card Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        // NEW: Card title settings (.utcfw-content h4)
        $this->add_control(
            'card_title_color',
            [
                'label' => 'Title Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-content h4 a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'Title Typography',
                'selector' => '{{WRAPPER}} .utcfw-content h4',
            ]
        );

        // NEW: Card metacontent settings (.utcfw-meta)
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'label' => 'Meta Typography',
                'selector' => '{{WRAPPER}} .utcfw-meta',
            ]
        );

        $this->add_responsive_control(
            'meta_size',
            [
                'label' => 'Meta Content Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 10, 'max' => 24],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-meta div' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Meta Icon Color
        $this->add_control(
            'meta_icon_color',
            [
                'label' => 'Meta Icon Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-meta i, {{WRAPPER}} .utcfw-meta svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_icon_size',
            [
                'label' => 'Meta Icon Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 10, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-meta i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .utcfw-meta svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Meta Icon Selection
        $this->add_control(
            'meta_icon',
            [
                'label' => 'Meta Icon',
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        // Button Text Color
        $this->add_control(
            'button_color',
            [
                'label' => 'Button Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-readmore' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => 'Button Typography',
                'selector' => '{{WRAPPER}} .utcfw-readmore',
            ]
        );

        // Button Background
        $this->add_control(
            'button_bg',
            [
                'label' => 'Button Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-readmore' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Button Hover Text Color
        $this->add_control(
            'button_hover_color',
            [
                'label' => 'Button Hover Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-readmore:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Hover Background
        $this->add_control(
            'button_hover_bg',
            [
                'label' => 'Button Hover Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-readmore:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Border Radius (Button)
        $this->add_control(
            'button_radius',
            [
                'label' => 'Button Border Radius',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-readmore' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Padding (Button)
        $this->add_responsive_control(
            'button_padding',
            [
                'label' => 'Button Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-readmore' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // NEW: utcfw-price settings
        $this->add_control(
            'price_color',
            [
                'label' => 'Price Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_size',
            [
                'label' => 'Price Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 12, 'max' => 36],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-price' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => 'Price Typography',
                'selector' => '{{WRAPPER}} .utcfw-price',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_km_typography',
                'label' => 'Price /km Typography',
                'selector' => '{{WRAPPER}} .utcfw-price-km',
            ]
        );

        $this->end_controls_section();


        // PAGINATION STYLES
        $this->start_controls_section(
            'utcfw_pagination_style_section',
            [
                'label' => 'Pagination',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_bg',
            [
                'label' => 'Inactive Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page:not(.active)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pagination_inactive_border',
                'label' => 'Inactive Border',
                'selector' => '{{WRAPPER}} .utcfw-page:not(.active)',
            ]
        );

        $this->add_control(
            'pagination_hover_color',
            [
                'label' => 'Hover Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page:hover:not(.active):not(.disabled)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_bg',
            [
                'label' => 'Hover Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page:hover:not(.active):not(.disabled)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label' => 'Active Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_bg',
            [
                'label' => 'Active Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        

        $this->add_responsive_control(
            'pagination_size',
            [
                'label' => 'Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 24, 'max' => 60],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .utcfw-page i' => 'font-size: calc({{SIZE}}{{UNIT}} * 0.6);',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_width',
            [
                'label' => 'Width',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 24, 'max' => 120],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_height',
            [
                'label' => 'Height',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 24, 'max' => 120],
                ],
                'selectors' => [
                    '{{WRAPPER}} .utcfw-page' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $widget_id = 'utcfw-widget-' . $this->get_id();

        $terms = get_terms([
            'taxonomy' => 'mptbm_category',
            'hide_empty' => true,
        ]);
        ?>

        <div id="<?php echo esc_attr($widget_id); ?>" class="utcfw-wrapper"
            data-posts="<?php echo esc_attr($settings['posts_per_page']); ?>"
            data-pagination="<?php echo ($settings['show_pagination'] === 'yes') ? 'yes' : 'no'; ?>"
            data-readmore="<?php echo esc_attr($settings['show_read_more']); ?>"
            data-readmore-text="<?php echo esc_attr($settings['read_more_text']); ?>"
            data-readmore-url="<?php echo esc_url($settings['read_more_url']['url']); ?>"
            data-tittle-url="<?php echo esc_url($settings['tittle_url']['url']); ?>"
            data-readmore-icon='<?php echo json_encode($settings["read_more_icon"]); ?>'
            data-meta-icon='<?php echo json_encode($settings["meta_icon"]); ?>'>
            <!-- LEFT FILTER -->
            <div class="utcfw-filter">
                <h3><?php echo esc_html($settings['filter_title']); ?></h3>
                <ul>
                    <li class="active" data-filter="all">All</li>

                    <?php foreach ($terms as $term): ?>
                        <li data-filter="<?php echo esc_attr($term->slug); ?>">
                            <?php echo esc_html($term->name); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- RIGHT GRID -->
            <style>
                #<?php echo esc_attr($widget_id); ?> .utcfw-grid {
                    grid-template-columns: repeat(<?php echo esc_attr($settings['columns_desktop']); ?>, 1fr);
                }

                @media (max-width: 1440px) {
                    #<?php echo esc_attr($widget_id); ?> .utcfw-grid {
                        grid-template-columns: repeat(<?php echo esc_attr($settings['columns_laptop']); ?>, 1fr);
                    }
                }

                @media (max-width: 1199px) {
                    #<?php echo esc_attr($widget_id); ?> .utcfw-grid {
                        grid-template-columns: repeat(<?php echo esc_attr($settings['columns_tablet_landscape']); ?>, 1fr);
                    }
                }

                @media (max-width: 991px) {
                    #<?php echo esc_attr($widget_id); ?> .utcfw-grid {
                        grid-template-columns: repeat(<?php echo esc_attr($settings['columns_tablet']); ?>, 1fr);
                    }
                }

                @media (max-width: 767px) {
                    #<?php echo esc_attr($widget_id); ?> .utcfw-grid {
                        grid-template-columns: repeat(<?php echo esc_attr($settings['columns_mobile_landscape']); ?>, 1fr);
                    }
                }

                @media (max-width: 575px) {
                    #<?php echo esc_attr($widget_id); ?> .utcfw-grid {
                        grid-template-columns: repeat(<?php echo esc_attr($settings['columns_mobile']); ?>, 1fr);
                    }
                }
            </style>

            <div class="utcfw-posts">

                <div class="utcfw-results utcfw-grid">
                </div>
            </div>

        </div>

        <?php
    }
}