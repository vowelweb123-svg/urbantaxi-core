<?php
/**
 * Elementor Integration
 * 
 * Adds UrbanTaxi Smart Animationsort Animations controls to Elementor widgets
 *
 * @package UrbanTaxi_Smart_Animations
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Elementor Integration Class
 */
class UrbanTaxi_Smart_Animations_Elementor {
        
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            return;
        }
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add animation controls before Custom CSS section starts
        add_action('elementor/element/before_section_start', array($this, 'register_animation_section'), 10, 3);
        
        // Render animation classes on frontend for all elements
        add_action('elementor/frontend/widget/before_render', array($this, 'render_animation_classes'));
        add_action('elementor/frontend/section/before_render', array($this, 'render_animation_classes'));
        add_action('elementor/frontend/container/before_render', array($this, 'render_animation_classes'));
        add_action('elementor/frontend/column/before_render', array($this, 'render_animation_classes'));
        
        // Add custom CSS for Elementor editor
        add_action('wp_enqueue_scripts', [$this, 'editor_styles']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'editor_styles']);
        
        // Register custom animations in Elementor
        add_filter('elementor/controls/animations/additional_animations', array($this, 'register_custom_animations'));

        add_action('elementor/editor/after_enqueue_scripts', function () {
            wp_enqueue_script('urbantaxi-smart-animations-editor-custom-animations-js', URBANTAXI_SMART_ANIMATIONS_URL . 'assets/js/animations.js', array('jquery', 'elementor-editor'), URBANTAXI_SMART_ANIMATIONS_VERSION, true);
        });

    }

    /**
     * Register animation section before Custom CSS section
     */
    public function register_animation_section($element, $section_id, $args) {
        if ($section_id === 'section_custom_css_pro') {
            $this->add_animation_controls($element, $args);
        }
    }

    /**
     * Get Animate.css animations
     */
    public function cp_get_animate_css_options() {
        $groups = include URBANTAXI_SMART_ANIMATIONS_PATH . 'includes/animations/animate-css.php';
        $options = [];

        foreach ($groups as $group_label => $animations) {
            //  group header
            $options['group_' . sanitize_title($group_label)] = '— ' . $group_label . ' —';

            foreach ($animations as $class => $label) {
                $options[$class] = '   ' . $label;
            }
        }

        return $options;
    }

    /**
     * Get vivify animations
     */
    public function cp_get_vivify_options() {
        $groups = include URBANTAXI_SMART_ANIMATIONS_PATH . 'includes/animations/vivify-css.php';
        $options = [];

        foreach ($groups as $group_label => $animations) {
            //  group header
            $options['group_' . sanitize_title($group_label)] = '— ' . $group_label . ' —';

            foreach ($animations as $class => $label) {
                $options[$class] = '   ' . $label;
            }
        }

        return $options;
    }
    
    /**
     * Add animation controls to Elementor widgets
     */
    public function add_animation_controls($element, $args) {
        $element->start_controls_section(
            'urbantaxi_smart_animations_section',
            array(
                'label' => '<i class="eicon-animation"></i> ' . esc_html__('UrbanTaxi Smart Animations', 'urbantaxi-smart-animations'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            )
        );

        // Disable Animation
        $element->add_control(
            'urbantaxi_smart_animations_disable_animation',
            array(
                'label' => esc_html__('Disable Animation', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'urbantaxi-smart-animations'),
                'label_off' => esc_html__('No', 'urbantaxi-smart-animations'),
                'return_value' => 'yes',
                'default' => '',
                'prefix_class' => 'cp-disable-animation-',
                'frontend_available' => true,
            )
        );

        // Main Animation Controls Heading
        $element->add_control(
            'cp_main_animation_heading',
            [
                'label' => __('Main Element Animation', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        // 1️⃣ Animation Library for Main Element
        $element->add_control(
            'cp_animation_library',
            [
                'label'     => __('Animation Library', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'frontend_available' => true,
                'options'   => [
                    '' => __('None', 'urbantaxi-smart-animations'),
                    'animate' => 'Animate.css',
                    'vivify' => 'Vivify',
                ],
            ]
        );

        // Animate.css dropdown for Main Element
        $element->add_control(
            'cp_animate_css_animation',
            [
                'label'     => __('Animate.css Animation', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->cp_get_animate_css_options(),
                'prefix_class' => 'cp-animate__',
                'frontend_available' => true,
                'condition' => [
                    'cp_animation_library' => 'animate',
                ],
            ]
        );

        // Vivify dropdown for Main Element
        $element->add_control(
            'cp_vivify_animation',
            [
                'label'     => __('Vivify Animation', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->cp_get_vivify_options(),
                'prefix_class' => 'cp-vivify__',
                'frontend_available' => true,
                'condition' => [
                    'cp_animation_library' => 'vivify',
                ],
            ]
        );

        $element->add_control(
            'cp_main_animation_delay',
            [
                'label' => __('Animation Delay (ms)', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 50,
                'frontend_available' => true,
            ]
        );

        // Multiple Selector Animations Heading
        $element->add_control(
            'cp_selector_animations_heading',
            [
                'label' => __('Multiple Selector Animations', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        // Repeater for multiple selector animations
        $repeater = new Repeater();

        $repeater->add_control(
            'cp_animation_selector_target_selector',
            [
                'label'       => __('Target Selector', 'urbantaxi-smart-animations'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => '.child, h2, .box img',
                'frontend_available' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'cp_animation_selector_animation_library',
            [
                'label'     => __('Animation Library', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'animate',
                'options'   => [
                    'animate' => 'Animate.css',
                    'vivify' => 'Vivify',
                ],
                'frontend_available' => true,
            ]
        );

        $repeater->add_control(
            'cp_animate_css_selector_animate_animation',
            [
                'label'     => __('Animate Animation', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->cp_get_animate_css_options(),
                'condition' => [
                    'cp_animation_selector_animation_library' => 'animate',
                ],
                'frontend_available' => true,
            ]
        );

        $repeater->add_control(
            'cp_vivify_selector_vivify_animation',
            [
                'label'     => __('Vivify Animation', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->cp_get_vivify_options(),
                'condition' => [
                    'cp_animation_selector_animation_library' => 'vivify',
                ],
                'frontend_available' => true,
            ]
        );


        $repeater->add_control(
            'cp_animation_selector_delay',
            [
                'label' => __('Delay (ms)', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 50,
                'frontend_available' => true,
            ]
        );

        // Add the repeater control
        $element->add_control(
            'cp_selector_animations',
            [
                'label' => __('Add Selector Animations', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'title_field' => '{{{ cp_animation_selector_target_selector }}}',
                'frontend_available' => true,
            ]
        );

        /* Editor Notice */
        $element->add_control(
            'cp_custom_animation_frontend_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => '<div>
                    <strong>Note:</strong> Selector animations will be visible only on the frontend.
                </div>',
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'separator' => 'before',
            ]
        );



        /* =====================================================
        HOVER SELECTOR ANIMATIONS
        ===================================================== */

        $element->add_control(
            'cp_hover_selector_heading',
            [
                'label' => __('Hover Selector Animations', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $hover_repeater = new Repeater();

        $hover_repeater->add_control(
            'cp_hover_main_selector',
            [
                'label' => __('Main Element Selector', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => '.box',
                'default' => '.box',
                'frontend_available' => true,
            ]
        );

        $hover_repeater->add_control(
            'cp_hover_target_selector',
            [
                'label' => __('Target Element Selector', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'h3',
                'default' => 'h3',
                'frontend_available' => true,
            ]
        );

        /* Animation Library */
        $hover_repeater->add_control(
            'cp_hover_animation_library',
            [
                'label'     => __('Animation Library', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'animate',
                'options'   => [
                    'animate' => 'Animate.css',
                    'vivify'  => 'Vivify',
                ],
                'frontend_available' => true,
            ]
        );

        /* Animate.css */
        $hover_repeater->add_control(
            'cp_hover_animate_css_animation',
            [
                'label'     => __('Animate Animation', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->cp_get_animate_css_options(),
                'condition' => [
                    'cp_hover_animation_library' => 'animate',
                ],
                'frontend_available' => true,
            ]
        );

        /* Vivify */
        $hover_repeater->add_control(
            'cp_hover_vivify_animation',
            [
                'label'     => __('Vivify Animation', 'urbantaxi-smart-animations'),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->cp_get_vivify_options(),
                'condition' => [
                    'cp_hover_animation_library' => 'vivify',
                ],
                'frontend_available' => true,
            ]
        );

        $hover_repeater->add_control(
            'cp_hover_color',
            [
                'label' => __('Hover Color', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::COLOR,
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'cp_hover_selector_animations',
            [
                'label' => __('Add Hover Selector Animations', 'urbantaxi-smart-animations'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $hover_repeater->get_controls(),
                'default' => [],
                'title_field' => '{{{ cp_hover_main_selector }}} → {{{ cp_hover_target_selector }}}',
                'frontend_available' => true,
            ]
        );




        $element->end_controls_section();
    }
    
    /**
     * Render animation classes on frontend
     */
    public function render_animation_classes($element) {

        // $settings = $element->get_settings();
        $settings = $element->get_settings_for_display();

        // Early exit: our controls are not registered on this element
        if ( ! array_key_exists( 'cp_animation_library', $settings ) ) {
            return;
        }

        // Check if animation is disabled
        if ( isset( $settings['urbantaxi_smart_animations_disable_animation'] ) && $settings['urbantaxi_smart_animations_disable_animation'] === 'yes' ) {
            $element->add_render_attribute('_wrapper', 'class', 'cp-disable-animation-yes');
            return;
        }

        // Main Element Animation - Animate.css
        if (!empty($settings['cp_animation_library']) && $settings['cp_animation_library'] === 'animate' && !empty($settings['cp_animate_css_animation'])) {
            $element->add_render_attribute(
                '_wrapper',
                'class',
                ['animate__animated', $settings['cp_animate_css_animation']]
            );
        }

        // Main Element Animation - Vivify (if you want to add classes for vivify)
        if (!empty($settings['cp_animation_library']) && $settings['cp_animation_library'] === 'vivify' && !empty($settings['cp_vivify_animation'])) {
            // Add vivify animation class if needed
            // $element->add_render_attribute('_wrapper', 'class', $settings['cp_vivify_animation']);
        }


        // ================= MAIN ELEMENT ANIMATION (DEFERRED) =================
        if ( !empty($settings['cp_animation_library']) &&  !empty($settings['cp_animate_css_animation']) && $settings['cp_animation_library'] === 'animate') 
        {
            $element->add_render_attribute(
                '_wrapper',
                'data-cp-main-animation',
                wp_json_encode([
                    'library'   => 'animate',
                    'animation' => 'animate__animated ' . $settings['cp_animate_css_animation'],
                    'delay'     => !empty($settings['cp_main_animation_delay'])? (int) $settings['cp_main_animation_delay'] : 0,
                ])
            );
        }

        if ( !empty($settings['cp_animation_library']) && !empty($settings['cp_vivify_animation']) && $settings['cp_animation_library'] === 'vivify' )
        {
            $element->add_render_attribute(
                '_wrapper',
                'data-cp-main-animation',
                wp_json_encode([
                    'library'   => 'vivify',
                    'animation' => 'cp-vivify__' . $settings['cp_vivify_animation'],
                    'delay'     => !empty($settings['cp_main_animation_delay']) ? (int) $settings['cp_main_animation_delay'] : 0,
                ])
            );
        }

        // Multiple Selector Animations
        if (!empty($settings['cp_selector_animations'])) {
            $selector_animations = [];
            
            foreach ($settings['cp_selector_animations'] as $animation) {
                if (!empty($animation['cp_animation_selector_target_selector'])) {
                    $animation_data = [
                        'selector' => trim($animation['cp_animation_selector_target_selector']),
                        'library' => $animation['cp_animation_selector_animation_library'] ?? 'animate',
                        'animation' => '',
                        'delay'    => !empty($animation['cp_animation_selector_delay']) ? (int) $animation['cp_animation_selector_delay'] : 0,
                    ];

                    if (($animation['cp_animation_selector_animation_library'] ?? 'animate') === 'animate' && !empty($animation['cp_animate_css_selector_animate_animation'])) {
                        $animation_data['animation'] = 'animate__animated ' . $animation['cp_animate_css_selector_animate_animation'];
                    } elseif (($animation['cp_animation_selector_animation_library'] ?? 'animate') === 'vivify' && !empty($animation['cp_vivify_selector_vivify_animation'])) {
                        $animation_data['animation'] = 'cp-vivify__' . $animation['cp_vivify_selector_vivify_animation'];
                    }

                    if (!empty($animation_data['animation'])) {
                        $selector_animations[] = $animation_data;
                    }
                }
            }

            if (!empty($selector_animations)) {
                $element->add_render_attribute(
                    '_wrapper',
                    'data-cp-selector-animations',
                    wp_json_encode($selector_animations)
                );
            }
        }


        // ================= HOVER SELECTOR ANIMATIONS =================
        if (!empty($settings['cp_hover_selector_animations'])) {

            $hover_data = [];

            foreach ($settings['cp_hover_selector_animations'] as $hover) {

                if (!empty($hover['cp_hover_main_selector']) && !empty($hover['cp_hover_target_selector'])) {

                    $animation_class = '';

                    if (
                        $hover['cp_hover_animation_library'] === 'animate' &&
                        !empty($hover['cp_hover_animate_css_animation'])
                    ) {
                        $animation_class = 'animate__animated ' . $hover['cp_hover_animate_css_animation'];
                    }

                    if (
                        $hover['cp_hover_animation_library'] === 'vivify' &&
                        !empty($hover['cp_hover_vivify_animation'])
                    ) {
                        $animation_class = 'cp-vivify__' . $hover['cp_hover_vivify_animation'];
                    }

                    if ($animation_class) {
                        $hover_data[] = [
                            'main_selector'   => trim($hover['cp_hover_main_selector']),
                            'target_selector' => trim($hover['cp_hover_target_selector']),
                            'animation'       => $animation_class,
                            'color'           => !empty($hover['cp_hover_color'])
                                ? $hover['cp_hover_color']
                                : '',
                        ];
                    }
                }
            }

            if (!empty($hover_data)) {
                $element->add_render_attribute(
                    '_wrapper',
                    'data-cp-hover-animations',
                    wp_json_encode($hover_data)
                );
            }
        }
    }
    
    public function editor_styles() {
        $animate_groups = include URBANTAXI_SMART_ANIMATIONS_PATH . 'includes/animations/animate-css.php';
        $css = '[class*="cp-animate__"] { animation-duration: 1s; animation-fill-mode: both; }' . "\n";

        foreach ($animate_groups as $group => $animations) {
            foreach ($animations as $class => $label) {
                // Prefix for consistency
                $css_class = str_replace('animate__', 'cp-animate__animate__', $class);
                $animation_name = str_replace('animate__', '', $class);
                $css .= ".{$css_class} { animation-name: {$animation_name}; }\n";
            }
        }

        $vivify_groups = include URBANTAXI_SMART_ANIMATIONS_PATH . 'includes/animations/vivify-css.php';

        foreach ($vivify_groups as $group => $animations) {
            foreach ($animations as $class => $label) {
                $css .= "
                .cp-vivify__{$class} {
                    animation: {$class} 1s both;
                }
                ";
            }
        }

        wp_add_inline_style('elementor-frontend', $css);
    }
    
    /**
     * Register custom animations in Elementor's animation list
     */
    public function register_custom_animations($animations) {
        $custom_animations = array(
            'UrbanTaxi Smart Animations' => array(
                'urbantaxi-smart-animations' => esc_html__('USA Fade In', 'urbantaxi-smart-animations'),
                'urbantaxi-smart-animations-up' => esc_html__('USA Fade In Up', 'urbantaxi-smart-animations'),
                'urbantaxi-smart-animations-down' => esc_html__('USA Fade In Down', 'urbantaxi-smart-animations'),
                'urbantaxi-smart-animations-left' => esc_html__('USA Fade In Left', 'urbantaxi-smart-animations'),
                'urbantaxi-smart-animations-right' => esc_html__('USA Fade In Right', 'urbantaxi-smart-animations'),
                'urbantaxi-smart-animations-scale-up' => esc_html__('USA Scale Up', 'urbantaxi-smart-animations'),
            ),
        );
        
        return array_merge($animations, $custom_animations);
   }
}

UrbanTaxi_Smart_Animations_Elementor::get_instance();