<?php
namespace UrbanTaxi\BookSeatWidget\Http\Controllers;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit;
}

class BookSeatWidget extends Widget_Base
{
    public function get_name()
    {
        return 'urbantaxi_book_seat_modal';
    }

    public function get_title()
    {
        return __('UrbanTaxi Book Seat Modal', 'urbantaxi-book-seat-widget');
    }

    public function get_icon()
    {
        return 'eicon-button';
    }

    public function get_categories()
    {
        return ['general'];
    }

    /* -----------------------------
     * Elementor Controls
     * ----------------------------- */
    protected function register_controls()
    {

        /* =====================================================
        BUTTON CONTENT
        ====================================================== */

        $this->start_controls_section(
            'button_content',
            [
                'label' => __('Button Content', 'urbantaxi-book-seat-widget'),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Book Your Seat Now',
            ]
        );

        $this->end_controls_section();


        /* =====================================================
        BUTTON STYLE
        ====================================================== */

        $this->start_controls_section(
            'button_style',
            [
                'label' => __('Button Style', 'urbantaxi-book-seat-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('btn_color_tabs');

        // --- Normal Tab ---
        $this->start_controls_tab(
            'btn_color_tab_normal',
            ['label' => __('Normal', 'urbantaxi-book-seat-widget')]
        );

        // Text Color
        $this->add_control(
            'btn_text_color',
            [
                'label' => __('Text Color', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Background
        $this->add_control(
            'btn_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // --- Hover Tab ---
        $this->start_controls_tab(
            'btn_color_tab_hover',
            ['label' => __('Hover', 'urbantaxi-book-seat-widget')]
        );

        // Hover Text Color
        $this->add_control(
            'btn_hover_text_color',
            [
                'label' => __('Text Color', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Hover Background Color
        $this->add_control(
            'btn_hover_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'btn_typography',
                'selector' => '{{WRAPPER}} .urbantaxi-book-btn',
            ]
        );

        // Width
        $this->add_responsive_control(
            'btn_width',
            [
                'label' => __('Width', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Height
        $this->add_responsive_control(
            'btn_height',
            [
                'label' => __('Height', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Alignment
        $this->add_responsive_control(
            'btn_alignment',
            [
                'label' => __('Alignment', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => 'Left',
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => 'Center',
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => 'Right',
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // Padding
        $this->add_responsive_control(
            'btn_padding',
            [
                'label' => __('Padding', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Margin
        $this->add_responsive_control(
            'btn_margin',
            [
                'label' => __('Margin', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-book-btn' =>
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();



        /* =====================================================
        MODAL STYLE
        ====================================================== */

        $this->start_controls_section(
            'modal_style',
            [
                'label' => __('Modal Style', 'urbantaxi-book-seat-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Text Color
        $this->add_control(
            'modal_text_color',
            [
                'label' => __('Text Color', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Background Color
        $this->add_control(
            'modal_bg_color',
            [
                'label' => __('Background Color', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'modal_blur',
            [
                'label' => __('Background Blur (px)', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-urbantaxi_book_seat_modal .urbantaxi-modal-content' =>
                        'backdrop-filter: blur({{SIZE}}{{UNIT}}); 
                 -webkit-backdrop-filter: blur({{SIZE}}{{UNIT}});',
                ],
            ]
        );


        // Width
        $this->add_responsive_control(
            'modal_width',
            [
                'label' => __('Width', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-urbantaxi_book_seat_modal .urbantaxi-modal-content' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Height
        $this->add_responsive_control(
            'modal_height',
            [
                'label' => __('Height', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-urbantaxi_book_seat_modal .urbantaxi-modal-content' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Position Type
        $this->add_control(
            'modal_position_type',
            [
                'label' => __('Position Type', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'fixed',
                'options' => [
                    'relative' => 'Relative',
                    'absolute' => 'Absolute',
                    'fixed' => 'Fixed',
                    'sticky' => 'Sticky',
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' => 'position: {{VALUE}};',
                ],
            ]
        );

        // Top / Bottom / Left / Right
        foreach (['top', 'bottom', 'left', 'right'] as $pos) {
            $this->add_responsive_control(
                'modal_' . $pos,
                [
                    'label' => ucfirst($pos),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => ['px', '%', 'vh', 'vw'],
                    'selectors' => [
                        '{{WRAPPER}} .urbantaxi-modal-content' =>
                            $pos . ': {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
        }

        // Padding
        $this->add_responsive_control(
            'modal_padding',
            [
                'label' => __('Padding', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Margin
        $this->add_responsive_control(
            'modal_margin',
            [
                'label' => __('Margin', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' =>
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );



        $this->add_control(
            'modal_border_type',
            [
                'label' => __('Border Type', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => __('None', 'urbantaxi-book-seat-widget'),
                    'solid' => __('Solid', 'urbantaxi-book-seat-widget'),
                    'double' => __('Double', 'urbantaxi-book-seat-widget'),
                    'dotted' => __('Dotted', 'urbantaxi-book-seat-widget'),
                    'dashed' => __('Dashed', 'urbantaxi-book-seat-widget'),
                    'groove' => __('Groove', 'urbantaxi-book-seat-widget'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' => 'border-style: {{VALUE}};',
                ],
            ]
        );



        $this->add_responsive_control(
            'modal_border_width',
            [
                'label' => __('Border Width', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' =>
                        'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'modal_border_type!' => '',
                ],
            ]
        );

        $this->add_control(
            'modal_border_color',
            [
                'label' => __('Border Color', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'modal_border_type!' => '',
                ],
            ]
        );


        $this->add_responsive_control(
            'modal_border_radius',
            [
                'label' => __('Border Radius', 'urbantaxi-book-seat-widget'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .urbantaxi-modal-content' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'modal_box_shadow',
                'selector' => '{{WRAPPER}} .urbantaxi-modal-content',
            ]
        );

        $this->end_controls_section();
    }

    /* -----------------------------
     * Frontend Render
     * ----------------------------- */

    protected function render()
    {
        wp_enqueue_style('urbantaxi-book-seat-widget');
        wp_enqueue_script('urbantaxi-book-seat-widget');

        $settings = $this->get_settings_for_display();
        $modal_position = $settings['modal_position'] ?? 'center';

        include URBANTAXI_BOOK_SEAT_WIDGET_PATH . 'resources/views/book-seat-widget.php';
    }
}

