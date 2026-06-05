<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Heading_Style_Widget extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'heading-style-widget';
    }

    public function get_title()
    {
        return __('Heading Style Widget', 'heading-style-widget');
    }

    public function get_icon()
    {
        return 'eicon-heading';
    }

    public function get_categories()
    {
        return ['basic'];
    }

    public function get_script_depends()
    {
        return ['heading-style-preview'];
    }



    /**
     * Register widget controls.
     */
    protected function register_controls()
    {

        // Content Tab
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'heading-style-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'heading_text',
            [
                'label' => __('Heading Text', 'heading-style-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Your Heading Here', 'heading-style-widget'),
                'placeholder' => __('Enter your heading', 'heading-style-widget'),
            ]
        );

        $this->add_control(
            'highlight_words',
            [
                'label' => __('Words to Highlight', 'heading-style-widget'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'word',
                        'label' => __('Word', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => '',
                        'placeholder' => __('Enter word to highlight', 'heading-style-widget'),
                    ],
                    [
                        'name' => 'text_type',
                        'label' => __('Text Type', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'solid',
                        'options' => [
                            'solid' => __('Solid', 'heading-style-widget'),
                            'gradient' => __('Gradient', 'heading-style-widget'),
                        ],
                    ],
                    [
                        'name' => 'text_color',
                        'label' => __('Text Color', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '',
                        'condition' => [
                            'text_type' => 'solid',
                        ],
                    ],
                    [
                        'name' => 'text_gradient_color_1',
                        'label' => __('Text Gradient Color 1', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '',
                        'condition' => [
                            'text_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'text_gradient_color_2',
                        'label' => __('Text Gradient Color 2', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '',
                        'condition' => [
                            'text_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'gradient_angle',
                        'label' => __('Gradient Angle (degrees)', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 0,
                        'max' => 360,
                        'step' => 1,
                        'default' => 0,
                        'condition' => [
                            'text_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'gradient_stop_1',
                        'label' => __('Gradient Stop 1 (%)', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'default' => 0,
                        'condition' => [
                            'text_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'gradient_stop_2',
                        'label' => __('Gradient Stop 2 (%)', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'default' => 100,
                        'condition' => [
                            'text_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'background_type',
                        'label' => __('Background Type', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::SELECT,
                        'default' => 'none',
                        'options' => [
                            'none' => __('None', 'heading-style-widget'),
                            'color' => __('Color', 'heading-style-widget'),
                            'gradient' => __('Gradient', 'heading-style-widget'),
                        ],
                    ],
                    [
                        'name' => 'background_color',
                        'label' => __('Background Color', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '',
                        'condition' => [
                            'background_type' => 'color',
                        ],
                    ],
                    [
                        'name' => 'background_gradient_color_1',
                        'label' => __('Gradient Color 1', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '',
                        'condition' => [
                            'background_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'background_gradient_color_2',
                        'label' => __('Gradient Color 2', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '',
                        'condition' => [
                            'background_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'background_gradient_angle',
                        'label' => __('Gradient Angle (degrees)', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 0,
                        'max' => 360,
                        'step' => 1,
                        'default' => 0,
                        'condition' => [
                            'background_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'background_gradient_stop_1',
                        'label' => __('Gradient Stop 1 (%)', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'default' => 0,
                        'condition' => [
                            'background_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'background_gradient_stop_2',
                        'label' => __('Gradient Stop 2 (%)', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'default' => 100,
                        'condition' => [
                            'background_type' => 'gradient',
                        ],
                    ],
                    [
                        'name' => 'padding',
                        'label' => __('Padding', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 50,
                                'step' => 1,
                            ],
                            'em' => [
                                'min' => 0,
                                'max' => 5,
                                'step' => 0.1,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                                'step' => 1,
                            ],
                        ],
                        'default' => [
                            'top' => 2,
                            'right' => 2,
                            'bottom' => 2,
                            'left' => 2,
                            'unit' => 'px',
                            'isLinked' => true,
                        ],
                        'condition' => [
                            'background_type!' => 'none',
                        ],
                    ],
                    [
                        'name' => 'border_radius',
                        'label' => __('Border Radius (px)', 'heading-style-widget'),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => ['px'],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 50,
                                'step' => 1,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => 2,
                        ],
                        'condition' => [
                            'background_type!' => 'none',
                        ],
                    ],
                ],
                'title_field' => '{{{ word }}}',
            ]
        );

        $this->add_control(
            'heading_tag',
            [
                'label' => __('Heading Tag', 'heading-style-widget'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'heading-style-widget'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __('Typography', 'heading-style-widget'),
                'selector' => '{{WRAPPER}} .heading-style-widget-heading',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'heading-style-widget'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .heading-style-widget-heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => __('Background', 'heading-style-widget'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .heading-style-widget-heading',
            ]
        );

        $this->add_responsive_control(
            'style_text_align',
            [
                'label' => __('Text Alignment', 'heading-style-widget'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'heading-style-widget'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'heading-style-widget'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'heading-style-widget'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .heading-style-widget-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $heading_tag = !empty($settings['heading_tag']) ? $settings['heading_tag'] : 'h2';
        $heading_text = isset($settings['heading_text']) ? $settings['heading_text'] : '';
        $highlight_words = isset($settings['highlight_words']) ? $settings['highlight_words'] : [];

        // Per-instance unique class to avoid conflicts when multiple widgets present
        $instance_id = method_exists($this, 'get_id') ? $this->get_id() : uniqid('hsw_');
        $wrapper_class = 'heading-style-widget-' . esc_attr($instance_id);

        // Prepare processed text safely (escape during replacement to preserve HTML if needed)
        $processed_text = $heading_text;

        if (!empty($highlight_words) && is_array($highlight_words)) {
            foreach ($highlight_words as $index => $highlight) {
                $word = isset($highlight['word']) ? trim((string) $highlight['word']) : '';
                if ($word === '') {
                    continue;
                }

                $inner_styles = [];
                $inner_classes = [];
                $outer_styles = [];

                if (isset($highlight['text_type']) && $highlight['text_type'] === 'solid' && !empty($highlight['text_color'])) {
                    $inner_styles[] = 'color: ' . esc_attr($highlight['text_color']);
                } elseif (isset($highlight['text_type']) && $highlight['text_type'] === 'gradient' && !empty($highlight['text_gradient_color_1']) && !empty($highlight['text_gradient_color_2'])) {
                    $inner_classes[] = 'heading-text-gradient';
                    $angle = isset($highlight['gradient_angle']) ? $highlight['gradient_angle'] : 0;
                    $stop1 = isset($highlight['gradient_stop_1']) ? $highlight['gradient_stop_1'] : 0;
                    $stop2 = isset($highlight['gradient_stop_2']) ? $highlight['gradient_stop_2'] : 100;
                    $inner_styles[] = 'background: linear-gradient(' . esc_attr($angle) . 'deg, ' . esc_attr($highlight['text_gradient_color_1']) . ' ' . esc_attr($stop1) . '%, ' . esc_attr($highlight['text_gradient_color_2']) . ' ' . esc_attr($stop2) . '%)';
                }

                if (isset($highlight['background_type']) && $highlight['background_type'] === 'color' && !empty($highlight['background_color'])) {
                    $outer_styles[] = 'background-color: ' . esc_attr($highlight['background_color']);
                    $padding_top = isset($highlight['padding']['top']) ? $highlight['padding']['top'] : 2;
                    $padding_right = isset($highlight['padding']['right']) ? $highlight['padding']['right'] : 2;
                    $padding_bottom = isset($highlight['padding']['bottom']) ? $highlight['padding']['bottom'] : 2;
                    $padding_left = isset($highlight['padding']['left']) ? $highlight['padding']['left'] : 2;
                    $padding_unit = isset($highlight['padding']['unit']) ? $highlight['padding']['unit'] : 'px';
                    $outer_styles[] = 'padding: ' . esc_attr($padding_top . $padding_unit) . ' ' . esc_attr($padding_right . $padding_unit) . ' ' . esc_attr($padding_bottom . $padding_unit) . ' ' . esc_attr($padding_left . $padding_unit);
                    $outer_styles[] = 'display: inline-block';
                    $border_radius = isset($highlight['border_radius']['size']) ? $highlight['border_radius']['size'] : 2;
                    $outer_styles[] = 'border-radius: ' . esc_attr($border_radius) . 'px';
                } elseif (isset($highlight['background_type']) && $highlight['background_type'] === 'gradient' && !empty($highlight['background_gradient_color_1']) && !empty($highlight['background_gradient_color_2'])) {
                    $angle = isset($highlight['background_gradient_angle']) ? $highlight['background_gradient_angle'] : 0;
                    $stop1 = isset($highlight['background_gradient_stop_1']) ? $highlight['background_gradient_stop_1'] : 0;
                    $stop2 = isset($highlight['background_gradient_stop_2']) ? $highlight['background_gradient_stop_2'] : 100;
                    $outer_styles[] = 'background: linear-gradient(' . esc_attr($angle) . 'deg, ' . esc_attr($highlight['background_gradient_color_1']) . ' ' . esc_attr($stop1) . '%, ' . esc_attr($highlight['background_gradient_color_2']) . ' ' . esc_attr($stop2) . '%)';
                    $padding_top = isset($highlight['padding']['top']) ? $highlight['padding']['top'] : 2;
                    $padding_right = isset($highlight['padding']['right']) ? $highlight['padding']['right'] : 2;
                    $padding_bottom = isset($highlight['padding']['bottom']) ? $highlight['padding']['bottom'] : 2;
                    $padding_left = isset($highlight['padding']['left']) ? $highlight['padding']['left'] : 2;
                    $padding_unit = isset($highlight['padding']['unit']) ? $highlight['padding']['unit'] : 'px';
                    $outer_styles[] = 'padding: ' . esc_attr($padding_top . $padding_unit) . ' ' . esc_attr($padding_right . $padding_unit) . ' ' . esc_attr($padding_bottom . $padding_unit) . ' ' . esc_attr($padding_left . $padding_unit);
                    $outer_styles[] = 'display: inline-block';
                    $border_radius = isset($highlight['border_radius']['size']) ? $highlight['border_radius']['size'] : 2;
                    $outer_styles[] = 'border-radius: ' . esc_attr($border_radius) . 'px';
                }

                $inner_class_attr = !empty($inner_classes) ? ' class="' . implode(' ', array_map('esc_attr', $inner_classes)) . '"' : '';
                $inner_style_attr = !empty($inner_styles) ? ' style="' . implode('; ', array_map('esc_attr', $inner_styles)) . '"' : '';
                $outer_style_attr = !empty($outer_styles) ? ' style="' . implode('; ', array_map('esc_attr', $outer_styles)) . '"' : '';

                $escaped_word = preg_quote($word, '/');
                $pattern = '/(?<!\p{L})(' . $escaped_word . ')(?!\p{L})/iu';
                $replacement = '<span' . $outer_style_attr . '><span' . $inner_class_attr . $inner_style_attr . '>$1</span></span>';

                try {
                    $processed_text = preg_replace($pattern, $replacement, $processed_text);
                } catch (\Throwable $e) {
                    $fallback_pattern = '/\b(' . $escaped_word . ')\b/i';
                    $processed_text = preg_replace($fallback_pattern, $replacement, $processed_text);
                }
            }
        }

        // Sanitize the final output
        $processed_text = wp_kses($processed_text, [
            'span' => [
                'class' => true,
                'style' => true,
            ],
        ]);

        echo '<div class="heading-style-widget-wrapper ' . esc_attr($wrapper_class) . '">';
        printf('<%1$s class="heading-style-widget-heading">%2$s</%1$s>', esc_attr($heading_tag), wp_kses($processed_text, ['span' => ['class' => true, 'style' => true]]));
        echo '</div>';
    }
}
