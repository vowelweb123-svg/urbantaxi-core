<?php
namespace UrbanTaxi_Service_Cards_Widget;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * UrbanTaxi Service Cards Widget
 */
class UrbanTaxi_Service_Cards_Widget extends Widget_Base
{

    /**
     * Get widget name
     */
    public function get_name()
    {
        return 'urbantaxi_service_cards_widget';
    }

    /**
     * Get widget title
     */
    public function get_title()
    {
        return __('UrbanTaxi Service Cards Widget', 'urbantaxi-service-cards-widget');
    }

    /**
     * Get widget icon
     */
    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    /**
     * Get widget categories
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends()
    {
        return ['font-awesome-js', 'urbantaxi-service-cards-widget-script'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends()
    {
        return ['font-awesome-css', 'urbantaxi-service-cards-widget-style'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => __('Post Type', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'post',
                'options' => array_merge(
                    ['post' => __('Post', 'urbantaxi-service-cards-widget'), 'page' => __('Page', 'urbantaxi-service-cards-widget')],
                    get_post_types(['public' => true, '_builtin' => false], 'names', 'and')
                ),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'exclude_posts',
            [
                'label' => __('Exclude Posts', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __('Enter post IDs separated by commas to exclude them from display.', 'urbantaxi-service-cards-widget'),
                'placeholder' => __('e.g. 1,2,3', 'urbantaxi-service-cards-widget'),
            ]
        );

        $this->add_control(
            'title_word_limit',
            [
                'label' => __('Title Word Limit', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 50,
                'description' => __('Set the maximum number of words for the title. 0 for no limit.', 'urbantaxi-service-cards-widget'),
            ]
        );

        $this->add_control(
            'show_featured_image',
            [
                'label' => __('Show Featured Image', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => __('Toggle to show or hide the featured image.', 'urbantaxi-service-cards-widget'),
            ]
        );

        $this->add_control(
            'excerpt_word_limit',
            [
                'label' => __('Excerpt Word Limit', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 20,
                'min' => 1,
                'max' => 100,
                'description' => __('Set the maximum number of words for the excerpt.', 'urbantaxi-service-cards-widget'),
            ]
        );

        $this->add_control(
            'custom_meta_key',
            [
                'label' => __('Custom Meta Key', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'metaimage',
                'description' => __('Meta key to render in the custom meta block.', 'urbantaxi-service-cards-widget'),
            ]
        );



        $this->add_control(
            'columns',
            [
                'label' => __('Columns (Desktop)', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => __('1', 'urbantaxi-service-cards-widget'),
                    '2' => __('2', 'urbantaxi-service-cards-widget'),
                    '3' => __('3', 'urbantaxi-service-cards-widget'),
                    '4' => __('4', 'urbantaxi-service-cards-widget'),
                    '5' => __('5', 'urbantaxi-service-cards-widget'),
                    '6' => __('6', 'urbantaxi-service-cards-widget'),
                ],
            ]
        );

        $this->add_control(
            'columns_tablet',
            [
                'label' => __('Columns (Tablet)', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '1' => __('1', 'urbantaxi-service-cards-widget'),
                    '2' => __('2', 'urbantaxi-service-cards-widget'),
                    '3' => __('3', 'urbantaxi-service-cards-widget'),
                    '4' => __('4', 'urbantaxi-service-cards-widget'),
                ],
            ]
        );

        $this->add_control(
            'columns_mobile',
            [
                'label' => __('Columns (Mobile)', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => __('1', 'urbantaxi-service-cards-widget'),
                    '2' => __('2', 'urbantaxi-service-cards-widget'),
                    '3' => __('3', 'urbantaxi-service-cards-widget'),
                    '4' => __('4', 'urbantaxi-service-cards-widget'),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'pagination_section',
            [
                'label' => __('Pagination', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __('Show Pagination', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __('Pagination Type', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'numbers',
                'options' => [
                    'numbers' => __('Numbers', 'urbantaxi-service-cards-widget'),
                    'arrows' => __('Arrows', 'urbantaxi-service-cards-widget'),
                    'both' => __('Numbers + Arrows', 'urbantaxi-service-cards-widget'),
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_numbers_show',
            [
                'label' => __('Show Page Numbers', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_pagination' => 'yes',
                    'pagination_type' => 'arrow_text',
                ],
            ]
        );

        $this->add_control(
            'pagination_ellipsis',
            [
                'label' => __('Show Ellipsis', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_pagination' => 'yes',
                    'pagination_type' => 'arrow_text',
                ],
            ]
        );

        $this->add_control(
            'pagination_visible_numbers',
            [
                'label' => __('Visible Page Numbers', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5,
                'min' => 1,
                'max' => 10,
                'description' => __('Number of pages to display after the current page. Example: If set to 2 and you are on page 1, it will show: 01 02 03 ... Last', 'urbantaxi-service-cards-widget'),
                'condition' => [
                    'show_pagination' => 'yes',
                    'pagination_type' => 'arrow_text',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'grid_gap',
            [
                'label' => __('Grid Gap', 'urbantaxi-service-cards-widget'),
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
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-query-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'typography_section',
            [
                'label' => __('Typography', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Title Typography', 'urbantaxi-service-cards-widget'),
                'selector' => '{{WRAPPER}} .post-query-item h3',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'label' => __('Meta Typography', 'urbantaxi-service-cards-widget'),
                'selector' => '{{WRAPPER}} .post-meta',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'label' => __('Excerpt Typography', 'urbantaxi-service-cards-widget'),
                'selector' => '{{WRAPPER}} .post-excerpt',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'text_alignment_section',
            [
                'label' => __('Text Alignment', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'title_text_align',
            [
                'label' => __('Title Text Align', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .post-query-grid .post-query-item h3 ' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_text_align',
            [
                'label' => __('Meta Text Align', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .post-meta' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'excerpt_text_align',
            [
                'label' => __('Excerpt Text Align', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-service-cards-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .post-excerpt' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'post_item_style_section',
            [
                'label' => __('Post Item Style', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'post_item_padding',
            [
                'label' => __('Padding', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '20',
                    'left' => '20',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'post_item_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .post-item-background-set' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_query_item_bg_color',
            [
                'label' => __('Item Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .post-query-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_item_border_type',
            [
                'label' => __('Border Type', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'urbantaxi-service-cards-widget'),
                    'solid' => __('Solid', 'urbantaxi-service-cards-widget'),
                    'double' => __('Double', 'urbantaxi-service-cards-widget'),
                    'dotted' => __('Dotted', 'urbantaxi-service-cards-widget'),
                    'dashed' => __('Dashed', 'urbantaxi-service-cards-widget'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-query-item' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_item_border_width',
            [
                'label' => __('Border Width', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-query-item' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'post_item_border_color',
            [
                'label' => __('Border Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-query-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_item_hover_border_color',
            [
                'label' => __('Hover Border Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-query-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_query_item_border_radius',
            [
                'label' => __('Item Border Radius', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-query-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        

        $this->add_control(
            'post_item_text_color',
            [
                'label' => __('Text Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .post-query-item, {{WRAPPER}} .post-query-item h3 a, {{WRAPPER}} .post-query-item .post-excerpt, {{WRAPPER}} .post-query-item .post-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_item_hover_text_color',
            [
                'label' => __('Hover Text Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .post-query-item:hover, {{WRAPPER}} .post-query-item:hover h3 a, {{WRAPPER}} .post-query-item:hover .post-excerpt, {{WRAPPER}} .post-query-item:hover .post-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'post_item_box_shadow',
                'label' => __('Box Shadow', 'urbantaxi-service-cards-widget'),
                'selector' => '{{WRAPPER}} .post-query-item',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'post_count_box_style_section',
            [
                'label' => __('Post Count Box Style', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'post_count_box_typography',
                'label' => __('Typography', 'urbantaxi-service-cards-widget'),
                'selector' => '{{WRAPPER}} .post-count-box',
            ]
        );

        $this->add_control(
            'post_count_box_font_size',
            [
                'label' => __('Font Size', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-count-box' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'post_count_box_color',
            [
                'label' => __('Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-count-box' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_count_box_hover_color',
            [
                'label' => __('Hover Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-query-item:hover .post-count-box' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'custom_meta_style_section',
            [
                'label' => __('Custom Meta Style', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'custom_meta_bg_color',
            [
                'label' => __('Meta Icon Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f0f0f0',
                'selectors' => [
                    '{{WRAPPER}} .post-custom-meta' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_meta_hover_bg_color',
            [
                'label' => __('Meta Icon Hover Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .post-query-item:hover .post-custom-meta' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_meta_border_type',
            [
                'label' => __('Meta Icon Border Type', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'solid',
                'options' => [
                    'none' => __('None', 'urbantaxi-service-cards-widget'),
                    'solid' => __('Solid', 'urbantaxi-service-cards-widget'),
                    'double' => __('Double', 'urbantaxi-service-cards-widget'),
                    'dotted' => __('Dotted', 'urbantaxi-service-cards-widget'),
                    'dashed' => __('Dashed', 'urbantaxi-service-cards-widget'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-custom-meta' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_meta_border_width',
            [
                'label' => __('Meta Icon Border Width', 'urbantaxi-service-cards-widget'),
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
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-custom-meta' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'custom_meta_border_color',
            [
                'label' => __('Meta Icon Border Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF26',
                'selectors' => [
                    '{{WRAPPER}} .post-custom-meta' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_meta_hover_border_color',
            [
                'label' => __('Meta Icon Hover Border Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .post-query-item:hover .post-custom-meta' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        

        $this->add_control(
            'custom_meta_padding',
            [
                'label' => __('Meta Icon Padding', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '10',
                    'bottom' => '10',
                    'left' => '10',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-custom-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'custom_meta_border_radius',
            [
                'label' => __('Meta Icon Border Radius', 'urbantaxi-service-cards-widget'),
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
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-custom-meta' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'read_more_section',
            [
                'label' => __('Read More Button', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_read_more',
            [
                'label' => __('Show Read More Button', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );



        $this->add_control(
            'read_more_icon',
            [
                'label' => __('Icon', 'urbantaxi-service-cards-widget'),
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

        $this->end_controls_section();

        $this->start_controls_section(
            'read_more_style_section',
            [
                'label' => __('Read More Button Style', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );



        $this->add_control(
            'read_more_icon_size',
            [
                'label' => __('Icon Size', 'urbantaxi-service-cards-widget'),
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
                    '{{WRAPPER}} .post-read-more-btn i' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .post-read-more-btn ' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );
        $this->add_control(
            'read_more_icon_color',
            [
                'label' => __('Icon Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .post-read-more-btn i' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .post-query-item .post-read-more-btn svg' => 'fill: {{VALUE}} !important; color: {{VALUE}} !important;',
                    '{{WRAPPER}} .post-query-item .post-read-more-btn svg path' => 'fill: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_control(
            'read_more_icon_bg_color',
            [
                'label' => __('Icon Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(255,255,255,0.2)',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'background-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .post-read-more-btn ' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_control(
            'read_more_icon_border_color',
            [
                'label' => __('Icon Border Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'border-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .post-read-more-btn ' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_control(
            'read_more_icon_border_style',
            [
                'label' => __('Icon Border Style', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'urbantaxi-service-cards-widget'),
                    'solid' => __('Solid', 'urbantaxi-service-cards-widget'),
                    'double' => __('Double', 'urbantaxi-service-cards-widget'),
                    'dotted' => __('Dotted', 'urbantaxi-service-cards-widget'),
                    'dashed' => __('Dashed', 'urbantaxi-service-cards-widget'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'border-style: {{VALUE}} !important;',
                    '{{WRAPPER}} .post-read-more-btn ' => 'border-style: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_control(
            'read_more_icon_border_width',
            [
                'label' => __('Icon Border Width', 'urbantaxi-service-cards-widget'),
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
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-read-more-btn i' => 'border-width: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .post-read-more-btn ' => 'border-width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );
        $this->add_control(
            'read_more_icon_border_radius',
            [
                'label' => __('Icon Border Radius', 'urbantaxi-service-cards-widget'),
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
                    '{{WRAPPER}} .post-read-more-btn i' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .post-read-more-btn ' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'pagination_style_section',
            [
                'label' => __('Pagination Style', 'urbantaxi-service-cards-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => __('Text Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .page-number, {{WRAPPER}} .pagination-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .page-number, {{WRAPPER}} .pagination-arrow' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label' => __('Active Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .page-number.active, {{WRAPPER}} .pagination-arrow.active' => 'background-color: {{VALUE}}; color: #fff;',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_text_color',
            [
                'label' => __('Active Text Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .page-number.active, {{WRAPPER}} .pagination-arrow.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_color',
            [
                'label' => __('Hover Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => '#005a87',
                'selectors' => [
                    '{{WRAPPER}} .page-number:hover:not(.active), {{WRAPPER}} .pagination-arrow:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Add hover background color for pagination items
        $this->add_control(
            'pagination_hover_bg_color',
            [
                'label' => __('Hover Background Color', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .page-number:hover:not(.active), {{WRAPPER}} .pagination-arrow:hover:not(.active)' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        // Border control for pagination items
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border',
                'label' => __('Border', 'urbantaxi-service-cards-widget'),
                'selector' => '{{WRAPPER}} .page-number, {{WRAPPER}} .pagination-arrow',
            ]
        );

        $this->add_control(
            'pagination_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-service-cards-widget'),
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
                    '{{WRAPPER}} .page-number, {{WRAPPER}} .pagination-arrow' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_gap',
            [
                'label' => __('Gap', 'urbantaxi-service-cards-widget'),
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
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .post-query-pagination' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_page_number_width',
            [
                'label' => __('Page Number Width', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 5,
                        'max' => 25,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 36,
                ],
                'selectors' => [
                    '{{WRAPPER}} .page-number' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_page_number_height',
            [
                'label' => __('Page Number Height', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 5,
                        'max' => 25,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 36,
                ],
                'selectors' => [
                    '{{WRAPPER}} .page-number' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_arrow_width',
            [
                'label' => __('Arrow Width', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 5,
                        'max' => 25,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 36,
                ],
                'selectors' => [
                    '{{WRAPPER}} .pagination-arrow' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_arrow_height',
            [
                'label' => __('Arrow Height', 'urbantaxi-service-cards-widget'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 5,
                        'max' => 25,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 36,
                ],
                'selectors' => [
                    '{{WRAPPER}} .pagination-arrow' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }


    /**
     * Limit words in a string
     */
    private function limit_words($text, $limit)
    {
        if ($limit == 0) {
            return $text;
        }
        $words = explode(' ', $text);
        if (count($words) > $limit) {
            return implode(' ', array_slice($words, 0, $limit)) . '...';
        }
        return $text;
    }

    /**
     * Convert array/scalar value to string for safe output
     */
    private function to_string($value, $default = '')
    {
        if (is_array($value)) {
            // For arrays with 'size' key (common in Elementor)
            if (isset($value['size'])) {
                return strval($value['size']);
            }
            // For arrays with 'value' key
            if (isset($value['value'])) {
                return strval($value['value']);
            }
            // Return first value if array
            if (!empty($value)) {
                return strval(reset($value));
            }
            return $default;
        }
        return strval($value);
    }

    /**
     * Convert array/scalar value to integer for safe output
     */
    private function to_int($value, $default = 0)
    {
        if (is_array($value)) {
            if (isset($value['size'])) {
                return intval($value['size']);
            }
            if (isset($value['value'])) {
                return intval($value['value']);
            }
            if (!empty($value)) {
                return intval(reset($value));
            }
            return $default;
        }
        return intval($value);
    }

    /**
     * Render widget output on the frontend
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $post_type = isset($settings['post_type']) ? $settings['post_type'] : 'post';
        $posts_per_page = $this->to_int(isset($settings['posts_per_page']) ? $settings['posts_per_page'] : 6, 6);
        $exclude_posts = isset($settings['exclude_posts']) ? $settings['exclude_posts'] : '';
        $title_word_limit = $this->to_int(isset($settings['title_word_limit']) ? $settings['title_word_limit'] : 0, 0);
        $excerpt_word_limit = $this->to_int(isset($settings['excerpt_word_limit']) ? $settings['excerpt_word_limit'] : 20, 20);
        $custom_meta_key = $this->to_string(isset($settings['custom_meta_key']) ? $settings['custom_meta_key'] : '', '');
        if ($custom_meta_key === '' && isset($settings['post_data_order']) && is_array($settings['post_data_order'])) {
            // Backward compatibility: pick first custom_meta key from old repeater data.
            foreach ($settings['post_data_order'] as $item) {
                if (isset($item['element']) && $item['element'] === 'custom_meta' && !empty($item['custom_meta_key'])) {
                    $custom_meta_key = $this->to_string($item['custom_meta_key'], '');
                    break;
                }
            }
        }
        $show_featured_image = $this->to_string(isset($settings['show_featured_image']) ? $settings['show_featured_image'] : 'yes', 'yes');
        // Ensure column values are strings (not arrays)
        $columns = $this->to_string(isset($settings['columns']) ? $settings['columns'] : '3', '3');
        $columns_tablet = $this->to_string(isset($settings['columns_tablet']) ? $settings['columns_tablet'] : '2', '2');
        $columns_mobile = $this->to_string(isset($settings['columns_mobile']) ? $settings['columns_mobile'] : '1', '1');
        $show_pagination = $this->to_string(isset($settings['show_pagination']) ? $settings['show_pagination'] : 'yes', 'yes');
        $pagination_type = $this->to_string(isset($settings['pagination_type']) ? $settings['pagination_type'] : 'numbers', 'numbers');

        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $posts_per_page,
            'paged' => 1,
        );

        // Add exclude posts if specified
        if (!empty($exclude_posts)) {
            $exclude_ids = array_map('intval', array_map('trim', explode(',', $exclude_posts)));
            $args['post__not_in'] = $exclude_ids;
        }

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $widget_classes = 'post-query-widget';
            $container_classes = 'post-query-grid';
            $container_style = 'style="grid-template-columns: repeat(' . esc_attr($columns) . ', 1fr);"';

            // Prepare safe values for data attributes
            $pagination_prev_text = $this->to_string(isset($settings['pagination_prev_text']) ? $settings['pagination_prev_text'] : 'Previous', 'Previous');
            $pagination_next_text = $this->to_string(isset($settings['pagination_next_text']) ? $settings['pagination_next_text'] : 'Next', 'Next');
            $pagination_numbers_show = $this->to_string(isset($settings['pagination_numbers_show']) ? $settings['pagination_numbers_show'] : 'yes', 'yes');
            $pagination_ellipsis = $this->to_string(isset($settings['pagination_ellipsis']) ? $settings['pagination_ellipsis'] : 'yes', 'yes');
            $pagination_visible_numbers = $this->to_string(isset($settings['pagination_visible_numbers']) ? $settings['pagination_visible_numbers'] : '5', '5');
            $show_read_more = $this->to_string(isset($settings['show_read_more']) ? $settings['show_read_more'] : 'yes', 'yes');
            $read_more_text = $this->to_string(isset($settings['read_more_text']) ? $settings['read_more_text'] : 'Read More', 'Read More');
            $read_more_align = $this->to_string(isset($settings['read_more_align']) ? $settings['read_more_align'] : 'left', 'left');
            $read_more_icon_size = $this->to_string(isset($settings['read_more_icon_size']) ? $settings['read_more_icon_size'] : '14', '14');
            $read_more_icon_color = $this->to_string(isset($settings['read_more_icon_color']) ? $settings['read_more_icon_color'] : '#ffffff', '#ffffff');
            $read_more_icon_bg_color = $this->to_string(isset($settings['read_more_icon_bg_color']) ? $settings['read_more_icon_bg_color'] : 'rgba(255,255,255,0.2)', 'rgba(255,255,255,0.2)');
            $read_more_icon_border_radius = $this->to_string(isset($settings['read_more_icon_border_radius']) ? $settings['read_more_icon_border_radius'] : '50', '50');

            $read_more_icon_json = '';
            if (isset($settings['read_more_icon']) && is_array($settings['read_more_icon'])) {
                $read_more_icon_json = json_encode($settings['read_more_icon']);
            }

            $read_more_icon_padding_json = '{"top":"4","right":"4","bottom":"4","left":"4","unit":"px"}';
            if (isset($settings['read_more_icon_padding']) && is_array($settings['read_more_icon_padding'])) {
                $read_more_icon_padding_json = json_encode($settings['read_more_icon_padding']);
            }

            echo '<div class="' . esc_attr($widget_classes) . '" data-post-type="' . esc_attr($post_type) . '" data-posts-per-page="' . esc_attr($posts_per_page) . '" data-exclude-posts="' . esc_attr($exclude_posts) . '" data-custom-meta-key="' . esc_attr($custom_meta_key) . '" data-title-word-limit="' . esc_attr($title_word_limit) . '" data-show-featured-image="' . esc_attr($show_featured_image) . '" data-excerpt-word-limit="' . esc_attr($excerpt_word_limit) . '" data-columns="' . esc_attr($columns) . '" data-columns-tablet="' . esc_attr($columns_tablet) . '" data-columns-mobile="' . esc_attr($columns_mobile) . '" data-show-pagination="' . esc_attr($show_pagination) . '" data-pagination-type="' . esc_attr($pagination_type) . '" data-pagination-prev-text="' . esc_attr($pagination_prev_text) . '" data-pagination-next-text="' . esc_attr($pagination_next_text) . '" data-pagination-numbers-show="' . esc_attr($pagination_numbers_show) . '" data-pagination-ellipsis="' . esc_attr($pagination_ellipsis) . '" data-pagination-visible-numbers="' . esc_attr($pagination_visible_numbers) . '" data-show-read-more="' . esc_attr($show_read_more) . '" data-read-more-text="' . esc_attr($read_more_text) . '" data-read-more-align="' . esc_attr($read_more_align) . '" data-read-more-icon="' . esc_attr($read_more_icon_json) . '" data-read-more-icon-size="' . esc_attr($read_more_icon_size) . '" data-read-more-icon-padding="' . esc_attr($read_more_icon_padding_json) . '" data-read-more-icon-border-radius="' . esc_attr($read_more_icon_border_radius) . '">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<div class="' . esc_attr($container_classes) . '" ' . $container_style . '>';

            // initialize post counter for zero-padded number
            $i = 1;

            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <div class="post-query-item">

                    <?php
                    $show_featured_image = $this->to_string(isset($settings['show_featured_image']) ? $settings['show_featured_image'] : 'yes', 'yes');
                    if ($show_featured_image === 'yes' && has_post_thumbnail()): ?>
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
                            esc_html($this->limit_words(get_the_title(), $title_word_limit)) .
                            '</h3>';

                        echo '<div class="post-excerpt position-relative">';
                        echo esc_html($this->limit_words(get_the_excerpt(), $excerpt_word_limit));
                        echo '</div>';


                        echo '</div>';
                        ?>

                        <?php
                        /* =========================
                        READ MORE BUTTON
                        ========================== */
                        echo '<div class="post-read-more-box d-flex" style="justify-content:space-between;">';
                            echo '<div class="post-count-box">';
                            echo esc_html($number_with_zero);
                            echo '</div>';
                            if (isset($settings['show_read_more']) && $settings['show_read_more'] === 'yes') {

                                $icon_html = '';

                                if (!empty($settings['read_more_icon'])) {

                                    ob_start();

                                    \Elementor\Icons_Manager::render_icon(
                                        $settings['read_more_icon'],
                                        [
                                            'class' => 'post-read-more-icon',
                                            'aria-hidden' => 'true',
                                        ]
                                    );

                                    $icon_html = ob_get_clean();
                                }

                                echo '<a href="' . esc_url(get_permalink()) . '" class="post-read-more-btn" alt="' . esc_attr(get_the_title()) . '">';
                                if ($icon_html) {
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo $icon_html;
                                }
                                echo '</a>';
                            }
                        echo '</div>';
                        ?>

                    </div>

                    <div class="post-item-background-set">
                        <!-- post background -->
                    </div>

                </div>
                <?php
                $i++;
            }

            echo '</div>';

            if (isset($show_pagination) && $show_pagination === 'yes' && $query->max_num_pages > 1) {
                echo '<div class="post-query-pagination pt-5">';

                // Previous arrow with text
                if ($pagination_type === 'arrow_text') {
                    $prev_text = $this->to_string(isset($settings['pagination_prev_text']) ? $settings['pagination_prev_text'] : __('Previous', 'urbantaxi-service-cards-widget'), __('Previous', 'urbantaxi-service-cards-widget'));
                    echo '<a href="#" class="pagination-arrow prev-arrow" data-page="' . esc_attr('prev') . '"><i class="fa-solid fa-angle-left"></i> ' . esc_html($prev_text) . '</a>';
                } elseif ($pagination_type === 'arrows' || $pagination_type === 'both') {
                    echo '<a href="#" class="pagination-arrow prev-arrow" data-page="' . esc_attr('prev') . '"><i class="fa-solid fa-angle-left"></i></a>';
                }

                // Page numbers
                if ($pagination_type === 'numbers' || $pagination_type === 'both') {
                    for ($i = 1; $i <= $query->max_num_pages; $i++) {
                        $active_class = ($i == 1) ? 'active' : '';
                        // Add leading zero if single digit
                        $display_num = $i < 10 ? '0' . $i : $i;
                        echo '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="' . esc_attr($i) . '">' . esc_html($display_num) . '</a>';
                    }
                } elseif ($pagination_type === 'arrow_text' && isset($settings['pagination_numbers_show']) && $settings['pagination_numbers_show'] === 'yes') {
                    $visible_numbers = $this->to_int(isset($settings['pagination_visible_numbers']) ? $settings['pagination_visible_numbers'] : 5, 5);
                    $show_ellipsis = isset($settings['pagination_ellipsis']) && $settings['pagination_ellipsis'] === 'yes';
                    $total_pages = $query->max_num_pages;
                    $current_page = 1; // Initial page is always 1

                    $half = floor($visible_numbers / 2);
                    $start = max(2, $current_page - $half);
                    $end = min($total_pages - 1, $current_page + $half);

                    // Ensure we show exactly visible_numbers if possible
                    if ($end - $start + 1 < $visible_numbers) {
                        if ($start == 2) {
                            $end = min($total_pages - 1, $start + $visible_numbers - 1);
                        } else {
                            $start = max(2, $end - $visible_numbers + 1);
                        }
                    }

                    // Page 1 (always show)
                    $active_class = ($current_page == 1) ? 'active' : '';
                    echo '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="1">01</a>';

                    // Ellipsis after page 1 if there's a gap
                    if ($show_ellipsis && $start > 2) {
                        echo '<span class="pagination-ellipsis">...</span>';
                    }

                    // Show pages from start to end
                    for ($i = $start; $i <= $end; $i++) {
                        $active_class = ($current_page == $i) ? 'active' : '';
                        $display_num = $i < 10 ? '0' . $i : $i;
                        echo '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="' . esc_attr($i) . '">' . esc_html($display_num) . '</a>';
                    }

                    // Ellipsis before last page if there's a gap
                    if ($show_ellipsis && $end < $total_pages - 1) {
                        echo '<span class="pagination-ellipsis">...</span>';
                    }

                    // Last page (always show, if more than 1 page)
                    if ($total_pages > 1) {
                        $active_class = ($current_page == $total_pages) ? 'active' : '';
                        $display_num = $total_pages < 10 ? '0' . $total_pages : $total_pages;
                        echo '<a href="#" class="page-number ' . esc_attr($active_class) . '" data-page="' . esc_attr($total_pages) . '">' . esc_html($display_num) . '</a>';
                    }
                }

                if ($pagination_type === 'arrow_text') {
                    $next_text = $this->to_string(isset($settings['pagination_next_text']) ? $settings['pagination_next_text'] : __('Next', 'urbantaxi-service-cards-widget'), __('Next', 'urbantaxi-service-cards-widget'));
                    echo '<a href="#" class="pagination-arrow next-arrow" data-page="' . esc_attr('next') . '">' . esc_html($next_text) . ' <i class="fa-solid fa-angle-right"></i></a>';
                } elseif ($pagination_type === 'arrows' || $pagination_type === 'both') {
                    echo '<a href="#" class="pagination-arrow next-arrow" data-page="' . esc_attr('next') . '"><i class="fa-solid fa-angle-right"></i></a>';
                }

                echo '</div>';
            }

            echo '</div>';
        }

        wp_reset_postdata();
    }
}
