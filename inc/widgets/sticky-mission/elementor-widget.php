<?php
/**
 * Elementor Widget for UrbanTaxi Sticky Mission
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class UrbanTaxi_Elementor_Widget extends \Elementor\Widget_Base {
    
    /**
     * Get widget name
     */
    public function get_name() {
        return 'urbantaxi_sticky_mission';
    }
    
    /**
     * Get widget title
     */
    public function get_title() {
        return __('UrbanTaxi Sticky Mission', 'urbantaxi-sticky-mission');
    }
    
    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-gallery-grid';
    }
    
    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['urbantaxi'];
    }
    
    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['taxi', 'mission', 'sticky', 'scroll', 'urbantaxi'];
    }
    
    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        return ['urbantaxi-sticky-style'];
    }
    
    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['urbantaxi-sticky-script'];
    }
    
    /**
     * Register widget controls
     */
    protected function register_controls() {
    
        // Cards Section
        $this->start_controls_section(
            'cards_section',
            [
                'label' => __('Mission Cards', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control(
            'tab_label',
            [
                'label' => __('Tab Label', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Our Mission', 'urbantaxi-sticky-mission'),
            ]
        );

        $repeater->add_control(
            'tab_label_icon',
            [
                'label' => __('Tab Label Icon', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-taxi',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'section_heading',
            [
                'label' => __('Section Heading', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Our Mission For Taxi Services', 'urbantaxi-sticky-mission'),
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        
        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Card Title', 'urbantaxi-sticky-mission'),
            ]
        );
        
        $repeater->add_control(
            'description',
            [
                'label' => __('Description', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Card description text goes here.', 'urbantaxi-sticky-mission'),
                'rows' => 3,
            ]
        );
        
        $repeater->add_control(
            'subtitle',
            [
                'label' => __('Subtitle', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('We Strive To:', 'urbantaxi-sticky-mission'),
            ]
        );
        
        $repeater->add_control(
            'list_items',
            [
                'label' => __('List Items', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Item 1 - Description here
Item 2 - Description here
Item 3 - Description here',
                'description' => __('Enter one item per line.', 'urbantaxi-sticky-mission'),
                'rows' => 6,
            ]
        );

        $repeater->add_control(
            'list_icon',
            [
                'label' => __('List Icon', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-check-circle',
                    'library' => 'fa-solid',
                ],
                'description' => __('This icon will be used for all list items in this card.', 'urbantaxi-sticky-mission'),
            ]
        );
        
        $this->add_control(
            'cards',
            [
                'label' => __('Cards', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'tab_label' => __('Our Mission', 'urbantaxi-sticky-mission'),
                        'tab_label_icon' => [
                            'value' => 'fas fa-taxi',
                            'library' => 'fa-solid',
                        ],
                        'section_heading' => __('Our Mission For Taxi Services', 'urbantaxi-sticky-mission'),
                        'title' => __('Driving Comfort, Delivering Trust.', 'urbantaxi-sticky-mission'),
                        'description' => __('Our mission is to provide safe, punctual, and affordable transportation by combining professional drivers, well-maintained vehicles, and user-friendly booking technology.', 'urbantaxi-sticky-mission'),
                        'list_icon' => [
                            'value' => 'fas fa-check-circle',
                            'library' => 'fa-solid',
                        ],
                    ],
                    [
                        'tab_label' => __('Our Vision', 'urbantaxi-sticky-mission'),
                        'tab_label_icon' => [
                            'value' => 'fas fa-eye',
                            'library' => 'fa-solid',
                        ],
                        'section_heading' => __('Our Vision For Taxi Services', 'urbantaxi-sticky-mission'),
                        'title' => __('Innovation Meets Convenience.', 'urbantaxi-sticky-mission'),
                        'description' => __('We leverage cutting-edge technology to streamline your travel experience, making every journey seamless from booking to destination.', 'urbantaxi-sticky-mission'),
                        'list_icon' => [
                            'value' => 'fas fa-check-circle',
                            'library' => 'fa-solid',
                        ],
                    ],
                    [
                        'tab_label' => __('Our Value', 'urbantaxi-sticky-mission'),
                        'tab_label_icon' => [
                            'value' => 'fas fa-star',
                            'library' => 'fa-solid',
                        ],
                        'section_heading' => __('What Drives Us Every Day', 'urbantaxi-sticky-mission'),
                        'title' => __('Your Safety, Our Priority.', 'urbantaxi-sticky-mission'),
                        'description' => __('Safety is at the heart of everything we do. Every driver is vetted, every vehicle inspected, and every ride monitored with advanced tracking technology.', 'urbantaxi-sticky-mission'),
                        'list_icon' => [
                            'value' => 'fas fa-check-circle',
                            'library' => 'fa-solid',
                        ],
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );
        
        $this->end_controls_section();

        // Card Style Section
        $this->start_controls_section(
            'card_style_section',
            [
                'label' => __('Card Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => __('Background Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_color',
            [
                'label' => __('Border Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .urbantaxi-card',
            ]
        );

        $this->end_controls_section();

        // Tab Label Style Section
        $this->start_controls_section(
            'tab_label_style_section',
            [
                'label' => __('Tab Label Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'tab_label_color',
            [
                'label' => __('Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-mission-wrapper .urbantaxi-tab-label .urbantaxi-tab-label-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tab_label_background_color',
            [
                'label' => __('Background Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-tab-label' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tab_label_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-mission-wrapper .urbantaxi-tab-label .urbantaxi-tab-label-text',
            ]
        );

        // Tabs (labels) typography control - applies to each tab in the tabs row
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tabs_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-tabs .urbantaxi-tab',
            ]
        );

        $this->add_responsive_control(
            'tabs_text_align',
            [
                'label' => __('Text Align', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-tabs .urbantaxi-tab' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_label_icon_size',
            [
                'label' => __('Icon Size', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-tab-label-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .urbantaxi-tab-label-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Tabs Sticky Position Section
        $this->start_controls_section(
            'tabs_sticky_position_section',
            [
                'label' => __('Tabs Sticky Position', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'tabs_sticky_top',
            [
                'label' => __('Sticky Top Position', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-tabs' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'description' => __('Adjust this if you have a sticky header. Cards will automatically adjust.', 'urbantaxi-sticky-mission'),
            ]
        );

        $this->add_responsive_control(
            'cards_base_top',
            [
                'label' => __('Cards Starting Position', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 300,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'size' => 100,
                    'unit' => 'px',
                ],
                'description' => __('First card top position. Should be greater than tabs sticky position.', 'urbantaxi-sticky-mission'),
            ]
        );

        $this->add_responsive_control(
            'cards_gap',
            [
                'label' => __('Cards Gap', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'size' => 50,
                    'unit' => 'px',
                ],
                'description' => __('Gap between stacked cards.', 'urbantaxi-sticky-mission'),
            ]
        );

        $this->end_controls_section();

        // Section Title Style Section
        $this->start_controls_section(
            'section_title_style_section',
            [
                'label' => __('Section Title Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'section_title_color',
            [
                'label' => __('Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-section-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'section_title_text_align',
            [
                'label' => __('Text Align', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-section-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // $this->add_group_control(
        //     \Elementor\Group_Control_Typography::get_type(),
        //     [
        //         'name' => 'section_title_typography',
        //         'selector' => '{{WRAPPER}} .urbantaxi-section-title',
        //     ]
        // );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'section_title_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-section-title',
                'global' => [
                    'default' => '', // 🔥 This disables global font linking
                ],
            ]
        );

        $this->end_controls_section();

        // Card Image Style Section
        $this->start_controls_section(
            'card_image_style_section',
            [
                'label' => __('Card Image Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'card_image_width',
            [
                'label' => __('Width', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 800,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_image_height',
            [
                'label' => __('Height', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 600,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card-image img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_image_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Card Title Style Section
        $this->start_controls_section(
            'card_title_style_section',
            [
                'label' => __('Card Title Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_title_color',
            [
                'label' => __('Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_title_background_color',
            [
                'label' => __('Background Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_title_text_align',
            [
                'label' => __('Text Align', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                // Use tag-qualified selector to increase specificity versus theme
                // global rules so responsive alignment applies per device.
                'selectors' => [
                    '{{WRAPPER}} h3.urbantaxi-card-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'card_title_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-card-title',
            ]
        );

        $this->end_controls_section();

        // Card Description Style Section
        $this->start_controls_section(
            'card_description_style_section',
            [
                'label' => __('Card Description Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_description_color',
            [
                'label' => __('Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_description_background_color',
            [
                'label' => __('Background Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-card-description' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_description_text_align',
            [
                'label' => __('Text Align', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                // Use tag-qualified selector (p tag) to increase specificity.
                'selectors' => [
                    '{{WRAPPER}} p.urbantaxi-card-description' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'card_description_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-card-description',
            ]
        );

        $this->end_controls_section();

        // List Title Style Section
        $this->start_controls_section(
            'list_title_style_section',
            [
                'label' => __('List Title Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'list_title_color',
            [
                'label' => __('Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'list_title_background_color',
            [
                'label' => __('Background Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-subtitle' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_title_text_align',
            [
                'label' => __('Text Align', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-subtitle' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'list_title_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-subtitle',
            ]
        );

        $this->end_controls_section();

        // List Items Style Section
        $this->start_controls_section(
            'list_items_style_section',
            [
                'label' => __('List Items Style', 'urbantaxi-sticky-mission'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'list_icon_color',
            [
                'label' => __('Icon Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-list-icon svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .urbantaxi-list-icon i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_icon_size',
            [
                'label' => __('Icon Size', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-list-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .urbantaxi-list-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .urbantaxi-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'list_text_color',
            [
                'label' => __('Text Color', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-list-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_item_text_align',
            [
                'label' => __('Text Align', 'urbantaxi-sticky-mission'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-sticky-mission'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-list-item' => 'text-align: {{VALUE}}; justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'list_text_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-list-text',
            ]
        );

        $this->end_controls_section();
    }
    
    /**
     * Render widget output on the frontend
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if (empty($settings['cards'])) {
            return;
        }

        // Get responsive settings
        $cards_base_top = !empty($settings['cards_base_top']['size']) ? $settings['cards_base_top']['size'] : 100;
        $cards_gap = !empty($settings['cards_gap']['size']) ? $settings['cards_gap']['size'] : 50;
        ?>
        <div class="urbantaxi-mission-wrapper" 
             data-base-top="<?php echo esc_attr($cards_base_top); ?>" 
             data-cards-gap="<?php echo esc_attr($cards_gap); ?>">
            <div class="urbantaxi-tabs" role="tablist" aria-label="Mission tabs">
                <?php foreach ($settings['cards'] as $index => $card) : ?>
                    <span class="urbantaxi-tab" data-index="<?php echo esc_attr($index); ?>">
                        <?php if (!empty($card['tab_label_icon']['value'])) : ?>
                            <span class="urbantaxi-tab-icon" aria-hidden="true">
                                <?php \Elementor\Icons_Manager::render_icon($card['tab_label_icon'], ['aria-hidden' => 'true']); ?>
                            </span>
                        <?php endif; ?>
                        <?php echo esc_html($card['tab_label']); ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <div class="urbantaxi-sticky-container">
                <div class="container">
                    <div class="urbantaxi-cards-wrapper">
                        <?php foreach ($settings['cards'] as $index => $card) : 
                            $is_even = ($index % 2 == 0);
                            $image_position = $is_even ? 'left' : 'right';
                            $list_items = !empty($card['list_items']) ? explode("\n", $card['list_items']) : [];
                        ?>
                        <div class="urbantaxi-card urbantaxi-image-<?php echo esc_attr($image_position); ?>">

                            <div class="urbantaxi-our-mission-heading-box text-center pb-4">
                                <?php if (!empty($card['tab_label'])) : ?>
                                    <p class="urbantaxi-tab-label">
                                        <?php if (!empty($card['tab_label_icon']['value'])) : ?>
                                            <span class="urbantaxi-tab-label-icon">
                                                <?php \Elementor\Icons_Manager::render_icon($card['tab_label_icon'], ['aria-hidden' => 'true']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="urbantaxi-tab-label-text"><?php echo esc_html($card['tab_label']); ?></span>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($card['section_heading'])) : ?>
                                    <h2 class="urbantaxi-section-title"><?php echo esc_html($card['section_heading']); ?></h2>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-5 col-sm-12 urbantaxi-card-image">
                                    <?php if (!empty($card['image']['url'])) : ?>
                                        <img src="<?php echo esc_url($card['image']['url']); ?>" alt="<?php echo esc_attr($card['title']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-6 col-md-7 col-sm-12 urbantaxi-card-content ps-lg-4 align-self-center">
                                    <?php if (!empty($card['title'])) : ?>
                                        <h3 class="urbantaxi-card-title"><?php echo esc_html($card['title']); ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($card['description'])) : ?>
                                        <p class="urbantaxi-card-description"><?php echo esc_html($card['description']); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($card['subtitle'])) : ?>
                                        <h4 class="urbantaxi-subtitle"><?php echo esc_html($card['subtitle']); ?></h4>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($list_items)) : ?>
                                        <ul class="urbantaxi-list">
                                            <?php foreach ($list_items as $item) : 
                                                $item = trim($item);
                                                if (empty($item)) continue;
                                            ?>
                                                <li class="urbantaxi-list-item">
                                                    <span class="urbantaxi-list-icon">
                                                        <?php if (!empty($card['list_icon']['value'])) : ?>
                                                            <?php \Elementor\Icons_Manager::render_icon($card['list_icon'], ['aria-hidden' => 'true']); ?>
                                                        <?php else : ?>
                                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                            </svg>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span class="urbantaxi-list-text"><?php echo esc_html($item); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <# 
        var iconSvg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>';
        
        if (settings.cards.length === 0) {
            return;
        }
        #>
        <div class="urbantaxi-mission-wrapper">
            <div class="urbantaxi-tabs" role="tablist" aria-label="Mission tabs">
                <# _.each( settings.cards, function( card, index ) {
                    var tabNavIcon = elementor.helpers.renderIcon( view, card.tab_label_icon, { 'aria-hidden': true }, 'i', 'object' );
                #>
                    <span class="urbantaxi-tab" data-index="{{ index }}">
                        <# if (tabNavIcon && tabNavIcon.value) { #>
                            <span class="urbantaxi-tab-icon" aria-hidden="true">{{{ tabNavIcon.value }}}</span>
                        <# } #>
                        {{{ card.tab_label }}}
                    </span>
                <# }); #>
            </div>
            <div class="urbantaxi-sticky-container">
                <div class="urbantaxi-cards-wrapper">
                <# _.each( settings.cards, function( card, index ) { 
                    var isEven = (index % 2 === 0);
                    var imagePosition = isEven ? 'left' : 'right';
                    var listItems = card.list_items ? card.list_items.split('\n') : [];
                    var tabIcon = elementor.helpers.renderIcon( view, card.tab_label_icon, { 'aria-hidden': true }, 'i', 'object' );
                    var listIcon = elementor.helpers.renderIcon( view, card.list_icon, { 'aria-hidden': true }, 'i', 'object' );
                #>
                <div class="urbantaxi-card urbantaxi-image-{{{ imagePosition }}}">
                    
                    <div class="urbantaxi-our-mission-heading-box text-center pb-4">
                        <# if (card.tab_label) { #>
                            <p class="urbantaxi-tab-label">
                                <# if (tabIcon && tabIcon.value) { #>
                                    <span class="urbantaxi-tab-label-icon">{{{ tabIcon.value }}}</span>
                                <# } #>
                                <span class="urbantaxi-tab-label-text">{{{ card.tab_label }}}</span>
                            </p>
                        <# } #>

                        <# if (card.section_heading) { #>
                            <h2 class="urbantaxi-section-title">{{{ card.section_heading }}}</h2>
                        <# } #>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-12 urbantaxi-card-image">
                            <# if (card.image && card.image.url) { #>
                                <img src="{{ card.image.url }}" alt="{{ card.title }}">
                            <# } #>
                        </div>
                        <div class="col-md-6 col-sm-12 urbantaxi-card-content ps-md-4 ps-0 align-self-center">
                            <# if (card.title) { #>
                                <h3 class="urbantaxi-card-title">{{{ card.title }}}</h3>
                            <# } #>
                            
                            <# if (card.description) { #>
                                <p class="urbantaxi-card-description">{{{ card.description }}}</p>
                            <# } #>
                            
                            <# if (card.subtitle) { #>
                                <h4 class="urbantaxi-subtitle">{{{ card.subtitle }}}</h4>
                            <# } #>
                            
                            <# if (listItems.length > 0) { #>
                                <ul class="urbantaxi-list">
                                    <# _.each( listItems, function( item ) { 
                                        item = item.trim();
                                        if (item === '') return;
                                    #>
                                        <li class="urbantaxi-list-item">
                                            <span class="urbantaxi-list-icon">
                                                <# if (listIcon && listIcon.value) { #>
                                                    {{{ listIcon.value }}}
                                                <# } else { #>
                                                    {{{ iconSvg }}}
                                                <# } #>
                                            </span>
                                            <span class="urbantaxi-list-text">{{ item }}</span>
                                        </li>
                                    <# }); #>
                                </ul>
                            <# } #>
                        </div>
                    </div>
                </div>
                <# }); #>
                </div>
            </div>
        </div>
        <?php
    }
}
