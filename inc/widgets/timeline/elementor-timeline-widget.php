<?php
if (!defined('ABSPATH')) {
    exit;
}

class UrbanTaxi_Timeline_Elementor_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'urbantaxi_timeline';
    }
    
    public function get_title() {
        return esc_html__('Timeline', 'urbantaxi-timeline-widget');
    }
    
    public function get_icon() {
        return 'eicon-time-line';
    }
    
    public function get_categories() {
        return ['urbantaxi'];
    }
    
    public function get_keywords() {
        return ['timeline', 'history', 'milestone', 'urbantaxi'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Timeline Items', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control(
            'year',
            [
                'label' => esc_html__('Year', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '2024',
                'label_block' => true,
            ]
        );
        
        $repeater->add_control(
            'display_year',
            [
                'label' => esc_html__('Display Year', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '24',
                'description' => esc_html__('Short format shown on timeline (e.g., "24" for 2024)', 'urbantaxi-timeline-widget'),
            ]
        );
        
        $repeater->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('2024 - Milestone Title', 'urbantaxi-timeline-widget'),
                'label_block' => true,
            ]
        );
        
        $repeater->add_control(
            'image',
            [
                'label' => esc_html__('Image', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        
        $repeater->add_control(
            'points',
            [
                'label' => esc_html__('Description Points', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => "First achievement\nSecond achievement\nThird achievement",
                'description' => esc_html__('Enter one point per line', 'urbantaxi-timeline-widget'),
                'rows' => 5,
            ]
        );
        
        $this->add_control(
            'timeline_items',
            [
                'label' => esc_html__('Timeline Items', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'year' => '2018',
                        'display_year' => '2020',
                        'title' => '2018 - Foundation',
                        'points' => "Company founded with a small fleet of city taxis\nFocused on local, short-distance travel\nIntroduced phone-based bookings",
                    ],
                    [
                        'year' => '2019',
                        'display_year' => '21',
                        'title' => '2019 - Service Expansion',
                        'points' => "Added airport pickup and drop services\nExpanded fleet with sedan and hatchback options\nStarted corporate and business travel services",
                    ],
                    [
                        'year' => '2020',
                        'display_year' => '22',
                        'title' => '2020 - Technology Integration',
                        'points' => "Launched online booking system\nIntroduced real-time driver tracking\nImplemented safety and hygiene protocols",
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Container
        $this->start_controls_section(
            'style_container',
            [
                'label' => esc_html__('Container', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'background_color',
            [
                'label' => esc_html__('Background Color', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f7f4',
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-timeline-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'container_padding',
            [
                'label' => esc_html__('Padding', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-timeline-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sticky_top_offset',
            [
                'label' => esc_html__('Sticky Top Offset', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .timeline-wrapper' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();

        // Sticky Behavior Section
        $this->start_controls_section(
            'sticky_behavior_section',
            [
                'label' => esc_html__('Sticky Behavior', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_responsive_control(
            'sticky_start_threshold',
            [
                'label' => esc_html__('Stacking Start Threshold', 'urbantaxi-timeline-widget'),
                'description' => esc_html__('When stacking animation begins relative to section visibility (0.3 = 30% from top)', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0.3,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 0.4,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 0.5,
                ],
                'frontend_available' => true,
            ]
        );
        
        $this->add_responsive_control(
            'sticky_end_threshold',
            [
                'label' => esc_html__('Stacking End Threshold', 'urbantaxi-timeline-widget'),
                'description' => esc_html__('When stacking animation completes relative to section visibility (0.7 = 70% from top)', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0.7,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 0.6,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 0.5,
                ],
                'frontend_available' => true,
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Year
        $this->start_controls_section(
            'style_year',
            [
                'label' => esc_html__('Year Display', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'year_color',
            [
                'label' => esc_html__('Color', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a1a',
                'selectors' => [
                    '{{WRAPPER}} .year-number' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'year_typography',
                'selector' => '{{WRAPPER}} .year-number',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Content Card
        $this->start_controls_section(
            'style_content',
            [
                'label' => esc_html__('Content Card', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Background Color', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .timeline-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'card_border_radius',
            [
                'label' => esc_html__('Border Radius', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .timeline-content' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'selector' => '{{WRAPPER}} .timeline-content',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Title
        $this->start_controls_section(
            'style_title',
            [
                'label' => esc_html__('Title', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a1a',
                'selectors' => [
                    '{{WRAPPER}} .content-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .content-title',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Description
        $this->start_controls_section(
            'style_description',
            [
                'label' => esc_html__('Description', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666',
                'selectors' => [
                    '{{WRAPPER}} .content-points li' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .content-points li',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section - Connector
        $this->start_controls_section(
            'style_connector',
            [
                'label' => esc_html__('Connector Line', 'urbantaxi-timeline-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'connector_color',
            [
                'label' => esc_html__('Color', 'urbantaxi-timeline-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a1a',
                'selectors' => [
                    '{{WRAPPER}} .connector-line' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .connector-dot' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if (empty($settings['timeline_items'])) {
            return;
        }

        // Get sticky behavior settings with fallbacks
        $sticky_start_desktop = isset($settings['sticky_start_threshold']['size']) ? $settings['sticky_start_threshold']['size'] : 0.3;
        $sticky_start_tablet = isset($settings['sticky_start_threshold_tablet']['size']) ? $settings['sticky_start_threshold_tablet']['size'] : 0.4;
        $sticky_start_mobile = isset($settings['sticky_start_threshold_mobile']['size']) ? $settings['sticky_start_threshold_mobile']['size'] : 0.5;
        
        $sticky_end_desktop = isset($settings['sticky_end_threshold']['size']) ? $settings['sticky_end_threshold']['size'] : 0.7;
        $sticky_end_tablet = isset($settings['sticky_end_threshold_tablet']['size']) ? $settings['sticky_end_threshold_tablet']['size'] : 0.6;
        $sticky_end_mobile = isset($settings['sticky_end_threshold_mobile']['size']) ? $settings['sticky_end_threshold_mobile']['size'] : 0.5;
        ?>
        <div class="urbantaxi-timeline-container" 
            data-sticky-start-desktop="<?php echo esc_attr($sticky_start_desktop); ?>"
            data-sticky-start-tablet="<?php echo esc_attr($sticky_start_tablet); ?>"
            data-sticky-start-mobile="<?php echo esc_attr($sticky_start_mobile); ?>"
            data-sticky-end-desktop="<?php echo esc_attr($sticky_end_desktop); ?>"
            data-sticky-end-tablet="<?php echo esc_attr($sticky_end_tablet); ?>"
            data-sticky-end-mobile="<?php echo esc_attr($sticky_end_mobile); ?>">
            <div class="timeline-wrapper">
                <?php foreach ($settings['timeline_items'] as $index => $item): ?>
                    <div class="timeline-item">
                        <div class="timeline-year">
                            <span class="year-number" data-complete-year="<?php echo esc_attr($item['year']); ?>" data-display-year="<?php echo esc_attr($item['display_year']); ?>"><?php echo esc_html($item['display_year']); ?></span>
                        </div>
                        <div class="timeline-connector">
                            <div class="connector-line"></div>
                            <div class="connector-dot"></div>
                        </div>
                        <div class="timeline-content">
                            <?php if (!empty($item['image']['url'])): ?>
                                <div class="content-image">
                                    <img src="<?php echo esc_url($item['image']['url']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="content-text">
                                <h3 class="content-title"><?php echo esc_html($item['title']); ?></h3>
                                <?php if (!empty($item['points'])): ?>
                                    <ul class="content-points">
                                        <?php 
                                        $points = explode("\n", $item['points']);
                                        foreach ($points as $point): 
                                            $point = trim($point);
                                            if (!empty($point)):
                                        ?>
                                            <li><?php echo esc_html($point); ?></li>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
