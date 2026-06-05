<?php
namespace UrbanTaxi\OurBlogWidget\Http\Controllers;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;


if (!defined('ABSPATH')) {
    exit;
}

class OurBlogWidget extends Widget_Base
{
    public function get_name()
    {
        return 'urbantaxi_our_blog';
    }

    public function get_title()
    {
        return __('UrbanTaxi Our Blog', 'urbantaxi-our-blog-widget');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['general'];
    }

    private function get_post_types()
    {
        $post_types = get_post_types(
            [
                'public' => true,
            ],
            'objects'
        );

        $options = [];

        foreach ($post_types as $post_type) {

            // Optional: exclude media & elementor templates
            if (in_array($post_type->name, ['attachment', 'elementor_library'])) {
                continue;
            }

            $options[$post_type->name] = $post_type->label;
        }

        return $options;
    }

    

    /* -----------------------------
     * Elementor Controls
     * ----------------------------- */
    protected function register_controls()
    {
        $this->start_controls_section(
            'section_query',
            [
                'label' => __('Query', 'urbantaxi-our-blog-widget'),
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => __('Post Type', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'post',
                'options' => $this->get_post_types(),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
            ]
        );

        $this->end_controls_section();


        //Layout Options
        $this->start_controls_section(
            'section_layout',
            [
                'label' => __('Layout Options', 'urbantaxi-our-blog-widget'),
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => __('Show Excerpt', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label' => __('Show Author', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => __('Show Date', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_comments',
            [
                'label' => __('Show Comments', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'custom_author_image',
            [
                'label' => __('Override Author Image', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::MEDIA,
            ]
        );


        $this->add_control(
            'date_icon',
            [
                'label' => __('Date Icon', 'urbantaxi-our-blog-widget'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-calendar-alt',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'comments_icon',
            [
                'label' => __('Comments Icon', 'urbantaxi-our-blog-widget'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-comment',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->end_controls_section();

       // Layout Mode Section
        $this->start_controls_section(
            'section_layout_mode',
            [
                'label' => __('Layout Mode', 'urbantaxi-our-blog-widget'),
            ]
        );

        $this->add_control(
            'enable_grid',
            [
                'label' => __('Enable Grid Mode', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();


        // Grid Settings Section
        $this->start_controls_section(
            'section_grid',
            [
                'label' => __('Grid Settings', 'urbantaxi-our-blog-widget'),
                'condition' => [
                    'enable_grid' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'grid_columns',
            [
                'label' => __('Columns', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 12,
            ]
        );


        $this->add_control('heading_grid_responsive', [
            'label' => __('Responsive Columns', 'urbantaxi-our-blog-widget'),
            'type'  => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('grid_col_320', [
            'label' => __('320 - 575', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
        ]);

        $this->add_control('grid_col_576', [
            'label' => __('576 - 767', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
        ]);

        $this->add_control('grid_col_768', [
            'label' => __('768 - 991', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 2,
        ]);

        $this->add_control('grid_col_992', [
            'label' => __('992 - 1024', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 2,
        ]);

        $this->add_control('grid_col_1025', [
            'label' => __('1025 - 1199', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
        ]);

        $this->add_control('grid_col_1200', [
            'label' => __('1200 - 1920', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
        ]);


        $this->add_control(
            'grid_rows',
            [
                'label' => __('Rows', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 2,
                'min' => 1,
                'max' => 12,
            ]
        );

        
        $this->add_control(
            'grid_items_per_page',
            [
                'label' => __('Items Per Page', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
            ]
        );


        // Grid Settings Section
        $this->add_control(
            'grid_enable_custom_pagination',  // Changed from 'enable_custom_pagination'
            [
                'label' => __('Custom Pagination', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'grid_custom_pagination_type',    // Changed from 'custom_pagination_type'
            [
                'label' => __('Pagination Type', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'number',
                'options' => [
                    'number'        => __('Number', 'urbantaxi-our-blog-widget'),
                    'dot'           => __('Dot', 'urbantaxi-our-blog-widget'),
                    'text'          => __('Text', 'urbantaxi-our-blog-widget'),
                    'text_number'   => __('Text + Number', 'urbantaxi-our-blog-widget'),
                    'text_dot'      => __('Text + Dot', 'urbantaxi-our-blog-widget'),
                    'number_arrow'  => __('Number + Arrow', 'urbantaxi-our-blog-widget'),
                ],
                'condition' => [
                    'grid_enable_custom_pagination' => 'yes',  // Updated condition
                ],
            ]
        );

        $this->add_control(
            'grid_prev_text',                  // Changed from 'prev_text'
            [
                'label' => __('Previous Text', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Prev',
                'condition' => [
                    'grid_enable_custom_pagination' => 'yes',  // Updated condition
                ],
            ]
        );

        $this->add_control(
            'grid_next_text',                  // Changed from 'next_text'
            [
                'label' => __('Next Text', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Next',
                'condition' => [
                    'grid_enable_custom_pagination' => 'yes',  // Updated condition
                ],
            ]
        );


        $this->end_controls_section();


        // Add Slider Controls
        $this->start_controls_section(
            'section_slider',
            [
                'label' => __('Slider Settings', 'urbantaxi-our-blog-widget'),
                'condition' => [
                    'enable_grid!' => 'yes', // 👈 important
                ],
            ]
        );

        $this->add_control(
            'slides_per_view',
            [
                'label' => __('Slides Per View', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
            ]
        );

        $this->add_control('heading_responsive', [
            'label' => __('Responsive Slides', 'urbantaxi-our-blog-widget'),
            'type'  => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('slides_320', [
            'label' => __('320 - 575', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
        ]);

        $this->add_control('slides_576', [
            'label' => __('576 - 767', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 1,
        ]);

        $this->add_control('slides_768', [
            'label' => __('768 - 991', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 2,
        ]);

        $this->add_control('slides_992', [
            'label' => __('992 - 1024', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 2,
        ]);

        $this->add_control('slides_1025', [
            'label' => __('1025 - 1199', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
        ]);

        $this->add_control('slides_1200', [
            'label' => __('1200 - 1920', 'urbantaxi-our-blog-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3,
        ]);


        $this->add_control(
            'space_between',
            [
                'label' => __('Space Between', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::NUMBER,
                'default' => 30,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __('Loop', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => __('Navigation Arrows', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __('Pagination Dots', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );


        $this->add_control(
            'enable_custom_pagination',
            [
                'label' => __('Custom Pagination', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'custom_pagination_type',
            [
                'label' => __('Pagination Type', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SELECT,
                'default' => 'number',
                'options' => [
                    'number'        => __('Number', 'urbantaxi-our-blog-widget'),
                    'dot'           => __('Dot', 'urbantaxi-our-blog-widget'),
                    'text'          => __('Text', 'urbantaxi-our-blog-widget'),
                    'text_number'   => __('Text + Number', 'urbantaxi-our-blog-widget'),
                    'text_dot'      => __('Text + Dot', 'urbantaxi-our-blog-widget'),
                    'number_arrow'  => __('Number + Arrow', 'urbantaxi-our-blog-widget'),
                ],
                'condition' => [
                    'enable_custom_pagination' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'prev_text',
            [
                'label' => __('Previous Text', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'enable_custom_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'next_text',
            [
                'label' => __('Next Text', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'enable_custom_pagination' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();


        /* ==========================================================
        CARD STYLE
        ========================================================== */
        $this->start_controls_section(
            'section_card_style',
            [
                'label' => __('Card', 'urbantaxi-our-blog-widget'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('card_tabs');

        /* ==========================
        NORMAL TAB
        ========================== */
        $this->start_controls_tab(
            'card_normal',
            ['label' => __('Normal', 'urbantaxi-our-blog-widget')]
        );

        /* Background */
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'card_bg',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .urbantaxi-blog-card',
            ]
        );

        /* Text Color */
        $this->add_control(
            'card_text_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card' => 'color: {{VALUE}};',
                ],
            ]
        );

        /* Border */
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-card',
            ]
        );

        /* Border Radius */
        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        /* Box Shadow */
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-card',
            ]
        );

        /* Padding */
        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Padding', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        /* Margin */
        $this->add_responsive_control(
            'card_margin',
            [
                'label' => __('Margin', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card' =>
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();


        /* ==========================
        HOVER TAB
        ========================== */
        $this->start_controls_tab(
            'card_hover',
            ['label' => __('Hover', 'urbantaxi-our-blog-widget')]
        );

        /* Hover Background */
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'card_hover_bg',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .urbantaxi-blog-card:hover, {{WRAPPER}} .urbantaxi-blog-card:focus-within',
            ]
        );

        /* Hover Text Color */
        $this->add_control(
            'card_hover_text_color',
            [
                'label' => __('Hover Text Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .urbantaxi-blog-card:focus-within' => 'color: {{VALUE}};',
                ],
            ]
        );

        /* Hover Border */
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_hover_border',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-card:hover',
            ]
        );

        /* Hover Shadow */
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_hover_shadow',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-card:hover',
            ]
        );

        /* Hover Transform Scale */
        $this->add_control(
            'card_hover_scale',
            [
                'label' => __('Hover Scale', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1.2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover' =>
                        'transform: scale({{SIZE}});',
                ],
            ]
        );

        /* Hover Translate Y */
        $this->add_control(
            'card_hover_translate',
            [
                'label' => __('Hover Translate Y', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -50,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover' =>
                        'transform: translateY({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        /* ==========================
        TRANSITION
        ========================== */
        $this->add_control(
            'card_transition_duration',
            [
                'label' => __('Transition Duration (ms)', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::NUMBER,
                'default' => 300,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card' =>
                        'transition: all {{VALUE}}ms ease;',
                ],
            ]
        );

        /* Overflow Option */
        $this->add_control(
            'card_overflow',
            [
                'label' => __('Overflow', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::SELECT,
                'default' => 'hidden',
                'options' => [
                    'hidden'  => 'Hidden',
                    'visible' => 'Visible',
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card' =>
                        'overflow: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();



        /* ==========================================================
        TITLE STYLE
        ========================================================== */
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Title', 'urbantaxi-our-blog-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('title_tabs');

        /* NORMAL */
        $this->start_controls_tab('title_normal', ['label' => __('Normal', 'urbantaxi-our-blog-widget')]);

        $this->add_control(
            'title_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bg',
            [
                'label' => __('Background', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'title_border',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-title',
            ]
        );

        $this->add_responsive_control(
            'title_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-title' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-title',
            ]
        );

        $this->end_controls_tab();

        /* HOVER */
        $this->start_controls_tab('title_hover', ['label' => __('Hover', 'urbantaxi-our-blog-widget')]);

        $this->add_control(
            'title_hover_color',
            [
                'label' => __('Hover Text Color', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover .urbantaxi-blog-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_bg',
            [
                'label' => __('Hover Background', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover .urbantaxi-blog-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-title',
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => __('Padding', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-title' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margin', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-title' =>
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'title_align',
            [
                'label' => __('Alignment', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-title' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
            ]
        );


        $this->end_controls_section();


        /* ==========================================================
        EXCERPT STYLE
        ========================================================== */
        $this->start_controls_section(
            'section_excerpt_style',
            [
                'label' => __('Excerpt', 'urbantaxi-our-blog-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'excerpt_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'excerpt_card_hover_color',
            [
                'label' => __('On Card Hover Color', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover .urbantaxi-blog-excerpt, {{WRAPPER}} .urbantaxi-blog-card:focus-within .urbantaxi-blog-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-excerpt',
            ]
        );

        $this->add_responsive_control(
            'excerpt_padding',
            [
                'label' => __('Padding', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-excerpt' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'excerpt_align',
            [
                'label' => __('Alignment', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-excerpt' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
            ]
        );


        $this->end_controls_section();


        /* ==========================================================
        CATEGORY STYLE
        ========================================================== */
        $this->start_controls_section(
            'section_category_style',
            [
                'label' => __('Category', 'urbantaxi-our-blog-widget'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'category_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_bg',
            [
                'label' => __('Background Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_hover_color',
            [
                'label' => __('Hover Text Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover .urbantaxi-blog-categories a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .urbantaxi-blog-card:focus-within .urbantaxi-blog-categories a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_hover_bg',
            [
                'label' => __('Hover Background Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-card:hover .urbantaxi-blog-categories' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .urbantaxi-blog-card:focus-within .urbantaxi-blog-categories' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-categories a',
            ]
        );

        $this->add_responsive_control(
            'category_font_size',
            [
                'label' => __('Font Size', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_line_height',
            [
                'label' => __('Line Height', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 120,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories a' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label' => __('Padding', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_margin',
            [
                'label' => __('Margin', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories' =>
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'category_border',
                'selector' => '{{WRAPPER}} .urbantaxi-blog-categories',
            ]
        );

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_align',
            [
                'label' => __('Alignment', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-blog-categories' => 'text-align: {{VALUE}};',
                ],
                'default' => 'left',
            ]
        );

        $this->end_controls_section();


        /* ==========================================================
        META ITEMS (AUTHOR / DATE / COMMENTS)
        ========================================================== */

        $meta_items = [
            'author'   => 'urbantaxi-meta-author',
            'date'     => 'urbantaxi-meta-date',
            'comments' => 'urbantaxi-meta-comments',
        ];

        foreach ($meta_items as $key => $class) {

            $this->start_controls_section(
                'section_' . $key . '_style',
                [
                    'label' => ucfirst($key),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->start_controls_tabs($key . '_tabs');

            /*
            ==================================================
            NORMAL
            ==================================================
            */
            $this->start_controls_tab(
                $key . '_normal_tab',
                ['label' => __('Normal', 'urbantaxi-our-blog-widget')]
            );

            $this->add_control(
                $key . '_color',
                [
                        'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        // container
                        '{{WRAPPER}} .' . $class => 'color: {{VALUE}};',
                        // ensure links inside the meta use the chosen color and override theme defaults
                        '{{WRAPPER}} .' . $class . ' a' => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} .' . $class . ' a:visited' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_control(
                $key . '_bg',
                [
                        'label' => __('Background Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .' . $class => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => $key . '_typography',
                    'selector' => '{{WRAPPER}} .' . $class . ' a',
                ]
            );

            $this->add_responsive_control(
                $key . '_padding',
                [
                        'label' => __('Padding', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .' . $class =>
                            'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();


            /*
            ==================================================
            HOVER (Individual Hover)
            ==================================================
            */
            $this->start_controls_tab(
                $key . '_hover_tab',
                ['label' => __('Hover', 'urbantaxi-our-blog-widget')]
            );

            $this->add_control(
                $key . '_hover_color',
                [
                        'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .' . $class . ':hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .' . $class . ' a:hover' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_control(
                $key . '_hover_bg',
                [
                        'label' => __('Background Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .' . $class . ':hover' =>
                            'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => $key . '_hover_typography',
                    'selector' => '{{WRAPPER}} .' . $class . ':hover',
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => $key . '_hover_shadow',
                    'selector' => '{{WRAPPER}} .' . $class . ':hover',
                ]
            );

            $this->end_controls_tab();


            /*
            ==================================================
            ON CARD HOVER
            ==================================================
            */
            $this->start_controls_tab(
                $key . '_card_hover_tab',
                ['label' => __('On Card Hover', 'urbantaxi-our-blog-widget')]
            );

            $this->add_control(
                $key . '_card_hover_color',
                [
                        'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .urbantaxi-blog-card:hover .' . $class => 'color: {{VALUE}};',
                        '{{WRAPPER}} .urbantaxi-blog-card:hover .' . $class . ' a' => 'color: {{VALUE}} !important;',
                        '{{WRAPPER}} .urbantaxi-blog-card:hover .' . $class . ' a:hover' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_control(
                $key . '_card_hover_bg',
                [
                        'label' => __('Background Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .urbantaxi-blog-card:hover .' . $class =>
                            'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => $key . '_card_hover_typography',
                    'selector' =>
                        '{{WRAPPER}} .urbantaxi-blog-card:hover .' . $class,
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => $key . '_card_hover_shadow',
                    'selector' =>
                        '{{WRAPPER}} .urbantaxi-blog-card:hover .' . $class,
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->end_controls_section();
        }


        /* ==========================================================
        META ICON STYLE (DATE + COMMENTS)
        ========================================================== */

        $icon_items = [
            'date'     => 'urbantaxi-meta-date',
            'comments' => 'urbantaxi-meta-comments',
        ];

        foreach ($icon_items as $key => $class) {

            $this->start_controls_section(
                'section_' . $key . '_icon_style',
                [
                    'label' => ucfirst($key) . ' Icon',
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->start_controls_tabs($key . '_icon_tabs');

            /*
            =============================
            NORMAL
            =============================
            */
            $this->start_controls_tab(
                $key . '_icon_normal',
                ['label' => __('Normal', 'urbantaxi-our-blog-widget')]
            );

            $this->add_control(
                $key . '_icon_color',
                [
                        'label' => __('Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .' . $class . ' .urbantaxi-meta-icon i, 
                        {{WRAPPER}} .' . $class . ' .urbantaxi-meta-icon svg' =>
                            'color: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                $key . '_icon_size',
                [
                        'label' => __('Size', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => ['min' => 8, 'max' => 60],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .' . $class . ' .urbantaxi-meta-icon i' =>
                            'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .' . $class . ' .urbantaxi-meta-icon svg' =>
                            'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                $key . '_icon_spacing',
                [
                        'label' => __('Spacing', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => ['min' => 0, 'max' => 30],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .' . $class . ' .urbantaxi-meta-icon' =>
                            'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();


            /*
            =============================
            HOVER (ICON ITSELF)
            =============================
            */
            $this->start_controls_tab(
                $key . '_icon_hover',
                ['label' => __('Hover', 'urbantaxi-our-blog-widget')]
            );

            $this->add_control(
                $key . '_icon_hover_color',
                [
                        'label' => __('Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .' . $class . ':hover .urbantaxi-meta-icon i,
                        {{WRAPPER}} .' . $class . ':hover .urbantaxi-meta-icon svg' =>
                            'color: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();


            /*
            =============================
            ON CARD HOVER
            =============================
            */
            $this->start_controls_tab(
                $key . '_icon_card_hover',
                ['label' => __('On Card Hover', 'urbantaxi-our-blog-widget')]
            );

            $this->add_control(
                $key . '_icon_card_hover_color',
                [
                        'label' => __('Color', 'urbantaxi-our-blog-widget'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .urbantaxi-blog-card:hover .' . $class . ' .urbantaxi-meta-icon i,
                        {{WRAPPER}} .urbantaxi-blog-card:hover .' . $class . ' .urbantaxi-meta-icon svg' =>
                            'color: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->end_controls_section();
        }
        /* ==========================================================
        Custom Paginatio STYLE (text + dot + number)
        ========================================================== */
        $this->start_controls_section(
            'section_custom_pagination_style',
            [
                'label' => __('Custom Pagination', 'urbantaxi-our-blog-widget'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'enable_custom_pagination' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'pagination_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'pagination_active_color',
            [
                'label' => __('Active Color', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span.active, {{WRAPPER}} .urbantaxi-custom-pagination button.active' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'pagination_alignment',
            [
                'label' => __('Alignment', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'urbantaxi-our-blog-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_gap',
            [
                'label' => __('Gap', 'urbantaxi-our-blog-widget'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button',
            ]
        );

        $this->add_responsive_control(
            'custom_pagination_width',
            [
                'label' => __('Width', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 20, 'max' => 300],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' =>
                        'width: {{SIZE}}{{UNIT}}; display:flex; align-items:center; justify-content:center;',
                ],
            ]
        );

        $this->add_responsive_control(
            'custom_pagination_height',
            [
                'label' => __('Height', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 20, 'max' => 300],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' =>
                        'height: {{SIZE}}{{UNIT}}; display:flex; align-items:center; justify-content:center;',
                ],
            ]
        );

        $this->add_responsive_control(
            'custom_pagination_padding',
            [
                'label' => __('Padding', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );       

        $this->add_responsive_control(
            'custom_pagination_margin',
            [
                'label' => __('Margin', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' =>
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'custom_pagination_border',
                'selector' => '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button',
            ]
        );

        $this->add_responsive_control(
            'custom_pagination_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('custom_pagination_style_tabs');

        $this->start_controls_tab(
            'custom_pagination_normal',
            ['label' => __('Normal', 'urbantaxi-our-blog-widget')]
        );

        $this->add_control(
            'custom_pagination_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_pagination_bg',
            [
                'label' => __('Background', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'custom_pagination_hover',
            ['label' => __('Hover', 'urbantaxi-our-blog-widget')]
        );

        $this->add_control(
            'custom_pagination_hover_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span:hover, {{WRAPPER}} .urbantaxi-custom-pagination button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_pagination_hover_bg',
            [
                'label' => __('Background', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span:hover, {{WRAPPER}} .urbantaxi-custom-pagination button:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'custom_pagination_active',
            ['label' => __('Active', 'urbantaxi-our-blog-widget')]
        );

        $this->add_control(
            'custom_pagination_active_color',
            [
                'label' => __('Text Color', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span.active, {{WRAPPER}} .urbantaxi-custom-pagination button.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'custom_pagination_active_bg',
            [
                'label' => __('Background', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span.active, {{WRAPPER}} .urbantaxi-custom-pagination button.active' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'custom_pagination_transition',
            [
                'label' => __('Transition Duration (ms)', 'urbantaxi-our-blog-widget'),
                'type'  => Controls_Manager::NUMBER,
                'default' => 300,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-custom-pagination span, {{WRAPPER}} .urbantaxi-custom-pagination button' =>
                        'transition: all {{VALUE}}ms ease;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /* -----------------------------
     * Frontend Render
     * ----------------------------- */

    protected function render(){

        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        $posts_per_page = $settings['posts_per_page'];
        if ($settings['enable_grid'] === 'yes') {
            $posts_per_page = $settings['grid_items_per_page'] ?? ($settings['grid_columns'] * $settings['grid_rows'] * 2);
        }

        $query = new \WP_Query([
            'post_type' => $settings['post_type'],
            'posts_per_page' => $posts_per_page,
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        ]);

        if (!$query->have_posts()) return;

        if ( $settings['enable_grid'] === 'yes' ) {

            $columns = $settings['grid_columns'];
            $rows = $settings['grid_rows'];
            $per_page = $columns * $rows;

            include URBANTAXI_OUR_BLOG_WIDGET_PATH . 'resources/views/our-blog-widget-grid.php';
        }else{
            include URBANTAXI_OUR_BLOG_WIDGET_PATH . 'resources/views/our-blog-widget.php';
        }

        wp_reset_postdata();
    }
}

