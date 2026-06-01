<?php
/**
 * Hero Slider Widget for Elementor.
 *
 * @package UrbanTaxi\Widgets
 */

namespace UrbanTaxi\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Hero_Slider_Widget
 *
 * Full-screen hero slider with per-slide: background image, mini title,
 * main title, description and two CTA buttons.
 */
class Hero_Slider_Widget extends Widget_Base {

	// -------------------------------------------------------------------------
	// Identity
	// -------------------------------------------------------------------------

	public function get_name(): string {
		return 'urbantaxi_hero_slider';
	}

	public function get_title(): string {
		return esc_html__( 'Hero Slider', 'urbantaxi-hero-slider' );
	}

	public function get_icon(): string {
		return 'eicon-slider-full-screen';
	}

	public function get_categories(): array {
		return [ 'general' ];
	}

	public function get_keywords(): array {
		return [ 'hero', 'slider', 'swiper', 'taxi', 'urbantaxi' ];
	}

	/**
	 * Enqueue widget-specific scripts on the frontend.
	 */
	public function get_script_depends(): array {
		return [ 'urbantaxi-hero-slider' ];
	}

	/**
	 * Enqueue widget-specific styles on the frontend.
	 */
	public function get_style_depends(): array {
		return [ 'urbantaxi-hero-slider' ];
	}

	// -------------------------------------------------------------------------
	// Controls
	// -------------------------------------------------------------------------

	protected function register_controls(): void {
		$this->section_slides();
		$this->section_slider_options();
		$this->section_sidebar_shortcode();
		$this->section_style_content();
		$this->section_style_sidebar_static_content();
		$this->section_style_pagination();
		$this->section_style_arrows();
		$this->section_style_buttons();
	}

	/**
	 * Add a responsive text alignment control.
	 *
	 * @param string $control_id Control ID.
	 * @param string $label      Control label.
	 * @param string $selector   CSS selector.
	 * @param bool   $use_margin Whether margin rules should be added.
	 */
	private function add_responsive_text_alignment_control( string $control_id, string $label, string $selector, bool $use_margin = false ): void {
		$selectors_dictionary = [
			'left'   => 'text-align: left;',
			'center' => 'text-align: center;',
			'right'  => 'text-align: right;',
		];

		if ( $use_margin ) {
			$selectors_dictionary = [
				'left'   => 'text-align: left; margin-left: 0; margin-right: auto;',
				'center' => 'text-align: center; margin-left: auto; margin-right: auto;',
				'right'  => 'text-align: right; margin-left: auto; margin-right: 0;',
			];
		}

		$this->add_responsive_control(
			$control_id,
			[
				'label'                => $label,
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'urbantaxi-hero-slider' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'urbantaxi-hero-slider' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'urbantaxi-hero-slider' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => 'left',
				'toggle'               => true,
				'selectors_dictionary' => $selectors_dictionary,
				'selectors'            => [
					$selector => '{{VALUE}}',
				],
			]
		);
	}

	/**
	 * Add a responsive button group alignment control.
	 *
	 * @param string $control_id Control ID.
	 * @param string $label      Control label.
	 * @param string $selector   CSS selector.
	 */
	private function add_responsive_button_alignment_control( string $control_id, string $label, string $selector ): void {
		$this->add_responsive_control(
			$control_id,
			[
				'label'                => $label,
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'urbantaxi-hero-slider' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'urbantaxi-hero-slider' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'urbantaxi-hero-slider' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => 'left',
				'toggle'               => true,
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start;',
					'center' => 'justify-content: center;',
					'right'  => 'justify-content: flex-end;',
				],
				'selectors'            => [
					$selector => '{{VALUE}}',
				],
			]
		);
	}

	/**
	 * Content tab — Slides repeater.
	 */
	private function section_slides(): void {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'urbantaxi-hero-slider' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		// --- Background image ---
		$repeater->add_control(
			'slide_image',
			[
				'label'   => esc_html__( 'Background Image', 'urbantaxi-hero-slider' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => Utils::get_placeholder_image_src() ],
			]
		);

		// --- Mini title ---
		$repeater->add_control(
			'mini_title',
			[
				'label'       => esc_html__( 'Mini Title', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Premium Taxi & Car Rental', 'urbantaxi-hero-slider' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		// --- Main title ---
		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Comfort. Luxury. Arrive In Style.', 'urbantaxi-hero-slider' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		// --- Title highlighted word (yellow) ---
		$repeater->add_control(
			'title_highlight',
			[
				'label'       => esc_html__( 'Highlighted Word(s) in Title', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Style.', 'urbantaxi-hero-slider' ),
				'description' => esc_html__( 'This exact text inside the title will be wrapped in a highlight span.', 'urbantaxi-hero-slider' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		// --- Description ---
		$repeater->add_control(
			'description',
			[
				'label'      => esc_html__( 'Description', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::TEXTAREA,
				'default'    => esc_html__( 'Experience Premium Rides With Professional Chauffeurs And Luxury Vehicles. Your Comfort Is Our Priority.', 'urbantaxi-hero-slider' ),
				'rows'       => 4,
				'dynamic'    => [ 'active' => true ],
			]
		);

		// --- Button 1 ---
		$repeater->add_control(
			'btn1_text',
			[
				'label'       => esc_html__( 'Button 1 Text', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Book Your Ride', 'urbantaxi-hero-slider' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'btn1_icon',
			[
				'label'   => esc_html__( 'Button 1 Icon', 'urbantaxi-hero-slider' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'btn1_url',
			[
				'label'         => esc_html__( 'Button 1 URL', 'urbantaxi-hero-slider' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'urbantaxi-hero-slider' ),
				'show_external' => true,
				'default'       => [ 'url' => '#' ],
				'dynamic'       => [ 'active' => true ],
			]
		);

		// --- Button 2 ---
		$repeater->add_control(
			'btn2_text',
			[
				'label'       => esc_html__( 'Button 2 Text', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Watch Video', 'urbantaxi-hero-slider' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'btn2_icon',
			[
				'label'   => esc_html__( 'Button 2 Icon', 'urbantaxi-hero-slider' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'btn2_url',
			[
				'label'         => esc_html__( 'Button 2 URL', 'urbantaxi-hero-slider' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'urbantaxi-hero-slider' ),
				'show_external' => true,
				'default'       => [ 'url' => '#' ],
				'dynamic'       => [ 'active' => true ],
			]
		);

		// --- Add repeater to section ---
		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Slides', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'mini_title'      => esc_html__( 'Premium Taxi & Car Rental', 'urbantaxi-hero-slider' ),
						'title'           => esc_html__( 'Comfort. Luxury. Arrive In Style.', 'urbantaxi-hero-slider' ),
						'title_highlight' => esc_html__( 'Style.', 'urbantaxi-hero-slider' ),
						'description'     => esc_html__( 'Experience Premium Rides With Professional Chauffeurs And Luxury Vehicles. Your Comfort Is Our Priority.', 'urbantaxi-hero-slider' ),
						'btn1_text'       => esc_html__( 'Book Your Ride', 'urbantaxi-hero-slider' ),
						'btn1_icon'       => [
							'value'   => 'fas fa-arrow-right',
							'library' => 'fa-solid',
						],
						'btn2_text'       => esc_html__( 'Watch Video', 'urbantaxi-hero-slider' ),
						'btn2_icon'       => [
							'value'   => 'fas fa-play',
							'library' => 'fa-solid',
						],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content tab — Slider options.
	 */
	private function section_slider_options(): void {
		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => esc_html__( 'Slider Options', 'urbantaxi-hero-slider' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slider_height',
			[
				'label'      => esc_html__( 'Slider Height', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 300, 'max' => 1200, 'step' => 10 ],
					'vh' => [ 'min' => 30, 'max' => 100 ],
				],
				'default'    => [ 'unit' => 'vh', 'size' => 100 ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'urbantaxi-hero-slider' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'urbantaxi-hero-slider' ),
				'label_off'    => esc_html__( 'No', 'urbantaxi-hero-slider' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed (ms)', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [ 'autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'loop',
			[
				'label'        => esc_html__( 'Loop', 'urbantaxi-hero-slider' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'urbantaxi-hero-slider' ),
				'label_off'    => esc_html__( 'No', 'urbantaxi-hero-slider' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => esc_html__( 'Show Arrows', 'urbantaxi-hero-slider' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'urbantaxi-hero-slider' ),
				'label_off'    => esc_html__( 'No', 'urbantaxi-hero-slider' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label'        => esc_html__( 'Show Dots', 'urbantaxi-hero-slider' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'urbantaxi-hero-slider' ),
				'label_off'    => esc_html__( 'No', 'urbantaxi-hero-slider' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content tab — Sidebar shortcode.
	 */
	private function section_sidebar_shortcode(): void {
		$this->start_controls_section(
			'section_sidebar_shortcode',
			[
				'label' => esc_html__( 'Sidebar / Shortcode', 'urbantaxi-hero-slider' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_sidebar',
			[
				'label'        => esc_html__( 'Enable Sidebar', 'urbantaxi-hero-slider' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'urbantaxi-hero-slider' ),
				'label_off'    => esc_html__( 'No', 'urbantaxi-hero-slider' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'sidebar_icon',
			[
				'label'      => esc_html__( 'Icon', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::MEDIA,
				'condition'  => [ 'enable_sidebar' => 'yes' ],
				'dynamic'    => [ 'active' => true ],
			]
		);

		$this->add_control(
			'sidebar_title',
			[
				'label'       => esc_html__( 'Title', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [ 'enable_sidebar' => 'yes' ],
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'sidebar_mini_title',
			[
				'label'       => esc_html__( 'Mini Title', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [ 'enable_sidebar' => 'yes' ],
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'sidebar_shortcode',
			[
				'label'       => esc_html__( 'Shortcode', 'urbantaxi-hero-slider' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter shortcode here, e.g., [contact-form-7 id="123"]', 'urbantaxi-hero-slider' ),
				'description' => esc_html__( 'Enter any WordPress shortcode. It will be executed and displayed on the right side of the slider.', 'urbantaxi-hero-slider' ),
				'condition'   => [ 'enable_sidebar' => 'yes' ],
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'sidebar_padding',
			[
				'label'      => esc_html__( 'Sidebar Padding', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 40,
					'right'    => 40,
					'bottom'   => 40,
					'left'     => 40,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider-sidebar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 'enable_sidebar' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab — Content typography & colours.
	 */
	private function section_style_content(): void {
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Content', 'urbantaxi-hero-slider' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Overlay colour
		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__( 'Overlay Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.55)',
				'selectors' => [
					'{{WRAPPER}} .ut-slide__overlay' => 'background: {{VALUE}};',
				],
			]
		);

		// Content alignment
		$this->add_control(
			'content_heading',
			[
				'label'     => esc_html__( 'Content', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_text_alignment_control(
			'content_alignment',
			esc_html__( 'Text Alignment', 'urbantaxi-hero-slider' ),
			'{{WRAPPER}} .ut-slide__content'
		);

		// Mini title
		$this->add_control(
			'mini_title_heading',
			[
				'label'     => esc_html__( 'Mini Title', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_text_alignment_control(
			'mini_title_alignment',
			esc_html__( 'Alignment', 'urbantaxi-hero-slider' ),
			'{{WRAPPER}} .ut-slide__mini-title'
		);

		$this->add_control(
			'mini_title_color',
			[
				'label'     => esc_html__( 'Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c518',
				'selectors' => [
					'{{WRAPPER}} .ut-slide__mini-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'mini_title_typography',
				'selector' => '{{WRAPPER}} .ut-slide__mini-title',
			]
		);

		// Main title
		$this->add_control(
			'title_heading',
			[
				'label'     => esc_html__( 'Title', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_text_alignment_control(
			'title_alignment',
			esc_html__( 'Alignment', 'urbantaxi-hero-slider' ),
			'{{WRAPPER}} .ut-slide__title'
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-slide__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_highlight_color',
			[
				'label'     => esc_html__( 'Highlight Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c518',
				'selectors' => [
					'{{WRAPPER}} .ut-slide__title .ut-highlight' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ut-slide__title',
			]
		);

		// Description
		$this->add_control(
			'desc_heading',
			[
				'label'     => esc_html__( 'Description', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_text_alignment_control(
			'desc_alignment',
			esc_html__( 'Alignment', 'urbantaxi-hero-slider' ),
			'{{WRAPPER}} .ut-slide__description',
			true
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e0e0e0',
				'selectors' => [
					'{{WRAPPER}} .ut-slide__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .ut-slide__description',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab — Sidebar static content.
	 */
	private function section_style_sidebar_static_content(): void {
		$this->start_controls_section(
			'section_style_sidebar_static_content',
			[
				'label'     => esc_html__( 'Sidebar Static Content', 'urbantaxi-hero-slider' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'enable_sidebar' => 'yes' ],
			]
		);

		$this->add_control(
			'sidebar_static_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'urbantaxi-hero-slider' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'sidebar_static_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 120, 'step' => 1 ] ],
				'default'    => [ 'size' => 44, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-sidebar-static-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sidebar_static_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-sidebar-static-icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ut-sidebar-static-icon' => 'fill: currentColor; stroke: currentColor;',
				],
			]
		);

		$this->add_control(
			'sidebar_static_icon_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .ut-sidebar-static-icon-wrap' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'sidebar_static_icon_border',
				'selector' => '{{WRAPPER}} .ut-sidebar-static-icon-wrap',
			]
		);

		$this->add_responsive_control(
			'sidebar_static_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-sidebar-static-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sidebar_static_icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-sidebar-static-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sidebar_static_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-sidebar-static-icon-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sidebar_static_icon_box_shadow',
				'selector' => '{{WRAPPER}} .ut-sidebar-static-icon-wrap',
			]
		);

		$this->add_control(
			'sidebar_static_title_heading',
			[
				'label'     => esc_html__( 'Title', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sidebar_static_title_color',
			[
				'label'     => esc_html__( 'Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-sidebar-static-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sidebar_static_title_typography',
				'selector' => '{{WRAPPER}} .ut-sidebar-static-title',
			]
		);

		$this->add_control(
			'sidebar_static_mini_title_heading',
			[
				'label'     => esc_html__( 'Mini Title', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sidebar_static_mini_title_color',
			[
				'label'     => esc_html__( 'Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cfcfcf',
				'selectors' => [
					'{{WRAPPER}} .ut-sidebar-static-mini-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sidebar_static_mini_title_typography',
				'selector' => '{{WRAPPER}} .ut-sidebar-static-mini-title',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab — Pagination/Dots.
	 */
	private function section_style_pagination(): void {
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__( 'Pagination (Dots)', 'urbantaxi-hero-slider' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// --- Inactive dots ---
		$this->add_control(
			'dot_inactive_heading',
			[
				'label' => esc_html__( 'Inactive Dot', 'urbantaxi-hero-slider' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'dot_inactive_color',
			[
				'label'     => esc_html__( 'Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.5)',
				'selectors' => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_inactive_width',
			[
				'label'      => esc_html__( 'Width', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 40, 'step' => 1 ] ],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_inactive_height',
			[
				'label'      => esc_html__( 'Height', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 40, 'step' => 1 ] ],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_inactive_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => '%',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// --- Active dots ---
		$this->add_control(
			'dot_active_heading',
			[
				'label'     => esc_html__( 'Active Dot', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'dot_active_color',
			[
				'label'     => esc_html__( 'Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c518',
				'selectors' => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_active_width',
			[
				'label'      => esc_html__( 'Width', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 40, 'step' => 1 ] ],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_active_height',
			[
				'label'      => esc_html__( 'Height', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 4, 'max' => 40, 'step' => 1 ] ],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_active_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => '%',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// --- Pagination container ---
		$this->add_control(
			'pagination_spacing_heading',
			[
				'label'     => esc_html__( 'Pagination Gap', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'pagination_gap',
			[
				'label'      => esc_html__( 'Gap Between Dots', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 30, 'step' => 1 ] ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_bottom_offset',
			[
				'label'      => esc_html__( 'Bottom Offset', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ] ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__pagination' => 'bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab — Navigation Arrows.
	 */
	private function section_style_arrows(): void {
		$this->start_controls_section(
			'section_style_arrows',
			[
				'label' => esc_html__( 'Navigation Arrows', 'urbantaxi-hero-slider' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// --- Arrow color ---
		$this->add_control(
			'arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-hero-slider__arrow' => 'color: {{VALUE}};',
				],
			]
		);

		// --- Arrow background color ---
		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0)',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.5)',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60, 'step' => 1 ] ],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-hero-slider__arrow' => '--swiper-navigation-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// --- Arrow width ---
		$this->add_responsive_control(
			'arrow_width',
			[
				'label'      => esc_html__( 'Width', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 20, 'max' => 80, 'step' => 1 ] ],
				'default'    => [ 'size' => 48, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// --- Arrow height ---
		$this->add_responsive_control(
			'arrow_height',
			[
				'label'      => esc_html__( 'Height', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 20, 'max' => 80, 'step' => 1 ] ],
				'default'    => [ 'size' => 48, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// --- Arrow padding ---
		$this->add_control(
			'arrow_padding',
			[
				'label'      => esc_html__( 'Padding', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// --- Arrow border radius ---
		$this->add_control(
			'arrow_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => '%',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_heading',
			[
				'label'     => esc_html__( 'Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label'     => esc_html__( 'Arrow Color (Hover)', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow:hover, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color (Hover)', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c518',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow:hover, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color (Hover)', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c518',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev.ut-hero-slider__arrow:hover, {{WRAPPER}} .swiper-button-next.ut-hero-slider__arrow:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab — Buttons.
	 */
	private function section_style_buttons(): void {
		$this->start_controls_section(
			'section_style_buttons',
			[
				'label' => esc_html__( 'Buttons', 'urbantaxi-hero-slider' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_button_alignment_control(
			'buttons_alignment',
			esc_html__( 'Alignment', 'urbantaxi-hero-slider' ),
			'{{WRAPPER}} .ut-slide__buttons'
		);

		// --- Button 1 ---
		$this->add_control(
			'btn1_heading',
			[
				'label' => esc_html__( 'Button 1 (Primary)', 'urbantaxi-hero-slider' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'btn1_bg',
			[
				'label'     => esc_html__( 'Background', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c518',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--primary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn1_color',
			[
				'label'     => esc_html__( 'Text Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--primary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn1_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c518',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--primary' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn1_padding',
			[
				'label'      => esc_html__( 'Padding', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 14,
					'right'    => 30,
					'bottom'   => 14,
					'left'     => 30,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--primary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn1_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 4,
					'right'    => 4,
					'bottom'   => 4,
					'left'     => 4,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--primary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn1_hover_heading',
			[
				'label'     => esc_html__( 'Button 1 Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn1_bg_hover',
			[
				'label'     => esc_html__( 'Background Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4a800',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--primary:hover, {{WRAPPER}} .ut-btn--primary:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn1_color_hover',
			[
				'label'     => esc_html__( 'Text Color Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--primary:hover, {{WRAPPER}} .ut-btn--primary:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn1_border_hover',
			[
				'label'     => esc_html__( 'Border Color Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4a800',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--primary:hover, {{WRAPPER}} .ut-btn--primary:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn1_typography',
				'selector' => '{{WRAPPER}} .ut-btn--primary',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'size' => 15,
							'unit' => 'px',
						],
					],
					'font_weight' => [
						'default' => '700',
					],
				],
			]
		);

		$this->add_control(
			'btn1_icon_heading',
			[
				'label'     => esc_html__( 'Button 1 Icon', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn1_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 60, 'step' => 1 ] ],
				'default'    => [ 'size' => 14, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--primary .ut-btn__icon i, {{WRAPPER}} .ut-btn--primary .ut-btn__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn1_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Spacing', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 30, 'step' => 1 ] ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--primary .ut-btn__icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn1_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Color Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ut-btn--primary:hover .ut-btn__icon i, {{WRAPPER}} .ut-btn--primary:focus .ut-btn__icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ut-btn--primary:hover .ut-btn__icon svg path, {{WRAPPER}} .ut-btn--primary:focus .ut-btn__icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		// --- Button 2 ---
		$this->add_control(
			'btn2_heading',
			[
				'label'     => esc_html__( 'Button 2 (Secondary)', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn2_bg',
			[
				'label'     => esc_html__( 'Background', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--secondary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn2_color',
			[
				'label'     => esc_html__( 'Text Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--secondary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn2_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--secondary' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn2_padding',
			[
				'label'      => esc_html__( 'Padding', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 14,
					'right'    => 30,
					'bottom'   => 14,
					'left'     => 30,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--secondary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn2_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 4,
					'right'    => 4,
					'bottom'   => 4,
					'left'     => 4,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--secondary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn2_hover_heading',
			[
				'label'     => esc_html__( 'Button 2 Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'btn2_bg_hover',
			[
				'label'     => esc_html__( 'Background Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.12)',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--secondary:hover, {{WRAPPER}} .ut-btn--secondary:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn2_color_hover',
			[
				'label'     => esc_html__( 'Text Color Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--secondary:hover, {{WRAPPER}} .ut-btn--secondary:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn2_border_hover',
			[
				'label'     => esc_html__( 'Border Color Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ut-btn--secondary:hover, {{WRAPPER}} .ut-btn--secondary:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn2_typography',
				'selector' => '{{WRAPPER}} .ut-btn--secondary',
				'fields_options' => [
					'font_size' => [
						'default' => [
							'size' => 16,
							'unit' => 'px',
						],
					],
					'font_weight' => [
						'default' => '700',
					],
				],
			]
		);

		$this->add_control(
			'btn2_icon_heading',
			[
				'label'     => esc_html__( 'Button 2 Icon', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn2_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 60, 'step' => 1 ] ],
				'default'    => [ 'size' => 14, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--secondary .ut-btn__icon i, {{WRAPPER}} .ut-btn--secondary .ut-btn__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn2_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Spacing', 'urbantaxi-hero-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 30, 'step' => 1 ] ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ut-btn--secondary .ut-btn__icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn2_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Color Hover', 'urbantaxi-hero-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ut-btn--secondary:hover .ut-btn__icon i, {{WRAPPER}} .ut-btn--secondary:focus .ut-btn__icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ut-btn--secondary:hover .ut-btn__icon svg path, {{WRAPPER}} .ut-btn--secondary:focus .ut-btn__icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	// -------------------------------------------------------------------------
	// Render
	// -------------------------------------------------------------------------

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$slides   = $settings['slides'] ?? [];

		if ( empty( $slides ) ) {
			return;
		}

		$enable_sidebar = 'yes' === ( $settings['enable_sidebar'] ?? 'no' );
		$shortcode      = ! empty( $settings['sidebar_shortcode'] ) ? $settings['sidebar_shortcode'] : '';
		$sidebar_icon   = ! empty( $settings['sidebar_icon']['url'] ) ? esc_url( $settings['sidebar_icon']['url'] ) : '';
		$sidebar_title  = ! empty( $settings['sidebar_title'] ) ? esc_html( $settings['sidebar_title'] ) : '';
		$sidebar_mini_title = ! empty( $settings['sidebar_mini_title'] ) ? esc_html( $settings['sidebar_mini_title'] ) : '';
		$has_sidebar_static_content = $sidebar_icon || $sidebar_title || $sidebar_mini_title;
		$wrapper_background_image = '';

		if ( ! empty( $slides[0]['slide_image']['url'] ) ) {
			$wrapper_background_image = esc_url( $slides[0]['slide_image']['url'] );
		}

		// Build data attributes for the JS initialiser.
		$swiper_options = [
			'loop'     => 'yes' === $settings['loop'],
			'autoplay' => 'yes' === $settings['autoplay']
				? [ 'delay' => (int) $settings['autoplay_speed'], 'disableOnInteraction' => false ]
				: false,
			'navigation' => 'yes' === $settings['show_arrows'],
			'pagination' => 'yes' === $settings['show_dots'],
		];

		$this->add_render_attribute(
			'slider_wrapper',
			[
				'class'             => 'ut-hero-slider swiper',
				'data-swiper-options' => wp_json_encode( $swiper_options ),
			]
		);

		// Container class based on sidebar visibility.
		$container_class = $enable_sidebar ? 'ut-hero-slider-wrapper ut-has-sidebar' : 'ut-hero-slider-wrapper';
		$wrapper_style   = $wrapper_background_image ? 'background-image: url(' . esc_url( $wrapper_background_image ) . ');' : '';
		?>
		<div class="<?php echo esc_attr( $container_class ); ?>"<?php echo $wrapper_style ? ' style="' . esc_attr( $wrapper_style ) . '"' : ''; ?>>
			<div class="row slider-main-outer-box">
				<div class="col-xl-5 col-lg-6 col-md-6 col-12 slider-content-box">
					<div <?php $this->print_render_attribute_string( 'slider_wrapper' ); ?>>
						<div class="swiper-wrapper">
							<?php foreach ( $slides as $slide ) : ?>
								<?php $this->render_slide( $slide ); ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-lg-6 col-md-6 col-12 slider-sidebar-form-box">
					<?php if ( $enable_sidebar && ( $has_sidebar_static_content || $shortcode ) ) : ?>
						<div class="ut-hero-slider-sidebar">
							<div class="ut-sidebar-content">
								<?php if ( $has_sidebar_static_content ) : ?>
									<div class="ut-sidebar-static-content">
										<div class="ut-sidebar-static-header">
											<?php if ( $sidebar_icon ) : ?>
												<div class="ut-sidebar-static-icon-wrap">
													<img class="ut-sidebar-static-icon" src="<?php echo $sidebar_icon; ?>" alt="" aria-hidden="true" />
												</div>
											<?php endif; ?>

											<div class="ut-sidebar-static-text">
												<?php if ( $sidebar_title ) : ?>
													<h3 class="ut-sidebar-static-title"><?php echo $sidebar_title; ?></h3>
												<?php endif; ?>

												<?php if ( $sidebar_mini_title ) : ?>
													<p class="ut-sidebar-static-mini-title"><?php echo $sidebar_mini_title; ?></p>
												<?php endif; ?>
											</div>
										</div>
									</div>
								<?php endif; ?>

								<?php if ( $shortcode ) : ?>
									<div class="ut-sidebar-shortcode-output">
										<?php echo do_shortcode( $shortcode ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( 'yes' === $settings['show_dots'] ) : ?>
					<div class="swiper-pagination ut-hero-slider__pagination"></div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_arrows'] ) : ?>
					<div class="swiper-button-prev ut-hero-slider__arrow"></div>
					<div class="swiper-button-next ut-hero-slider__arrow"></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a single slide.
	 *
	 * @param array $slide Slide settings from the repeater.
	 */
	private function render_slide( array $slide ): void {
		$image_url = ! empty( $slide['slide_image']['url'] )
			? esc_url( $slide['slide_image']['url'] )
			: '';

		$mini_title  = ! empty( $slide['mini_title'] ) ? $slide['mini_title'] : '';
		$title       = ! empty( $slide['title'] ) ? $slide['title'] : '';
		$highlight   = ! empty( $slide['title_highlight'] ) ? $slide['title_highlight'] : '';
		$description = ! empty( $slide['description'] ) ? $slide['description'] : '';

		// Wrap the highlighted portion in a span if present.
		$title_html = esc_html( $title );
		if ( $highlight ) {
			$title_html = str_replace(
				esc_html( $highlight ),
				'<span class="ut-highlight">' . esc_html( $highlight ) . '</span>',
				$title_html
			);
		}

		// Button 1
		$btn1_text = ! empty( $slide['btn1_text'] ) ? esc_html( $slide['btn1_text'] ) : '';
		$btn1_url  = ! empty( $slide['btn1_url']['url'] ) ? esc_url( $slide['btn1_url']['url'] ) : '#';
		$btn1_target = ! empty( $slide['btn1_url']['is_external'] ) ? ' target="_blank"' : '';
		$btn1_rel    = ! empty( $slide['btn1_url']['nofollow'] ) ? ' rel="nofollow"' : '';
		$btn1_icon   = ! empty( $slide['btn1_icon'] ) ? $slide['btn1_icon'] : [];

		// Button 2
		$btn2_text = ! empty( $slide['btn2_text'] ) ? esc_html( $slide['btn2_text'] ) : '';
		$btn2_url  = ! empty( $slide['btn2_url']['url'] ) ? esc_url( $slide['btn2_url']['url'] ) : '#';
		$btn2_target = ! empty( $slide['btn2_url']['is_external'] ) ? ' target="_blank"' : '';
		$btn2_rel    = ! empty( $slide['btn2_url']['nofollow'] ) ? ' rel="nofollow"' : '';
		$btn2_icon   = ! empty( $slide['btn2_icon'] ) ? $slide['btn2_icon'] : [];
		?>
		<div class="swiper-slide ut-slide" data-slide-bg="<?php echo esc_attr( $image_url ); ?>">
			<div class="ut-slide__overlay" aria-hidden="true"></div>

			<div class="container">
				<div class="ut-slide__content">
					<?php if ( $mini_title ) : ?>
						<p class="ut-slide__mini-title"><?php echo esc_html( $mini_title ); ?></p>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<h1 class="ut-slide__title"><?php echo $title_html; ?></h1>
					<?php endif; ?>

					<?php if ( $description ) : ?>
						<p class="ut-slide__description"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>

					<?php if ( $btn1_text || $btn2_text ) : ?>
						<div class="ut-slide__buttons">
							<?php if ( $btn1_text ) : ?>
								<a href="<?php echo $btn1_url; ?>"<?php echo $btn1_target . $btn1_rel; ?> class="ut-btn ut-btn--primary">
									<?php echo $btn1_text; ?>
									<?php if ( ! empty( $btn1_icon['value'] ) ) : ?>
										<span class="ut-btn__icon" aria-hidden="true">
											<?php Icons_Manager::render_icon( $btn1_icon, [ 'aria-hidden' => 'true' ] ); ?>
										</span>
									<?php endif; ?>
								</a>
							<?php endif; ?>

							<?php if ( $btn2_text ) : ?>
								<a href="<?php echo $btn2_url; ?>"<?php echo $btn2_target . $btn2_rel; ?> class="ut-btn ut-btn--secondary">
									<?php echo $btn2_text; ?>
									<?php if ( ! empty( $btn2_icon['value'] ) ) : ?>
										<span class="ut-btn__icon" aria-hidden="true">
											<?php Icons_Manager::render_icon( $btn2_icon, [ 'aria-hidden' => 'true' ] ); ?>
										</span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
