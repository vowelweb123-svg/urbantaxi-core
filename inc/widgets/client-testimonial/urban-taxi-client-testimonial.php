<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;

class Urban_Taxi_Client_Testimonial_Widget extends Widget_Base {

    public function get_name()       { return 'tsw_testimonial_slider'; }
    public function get_title()      { return esc_html__( 'Urban Taxi Client Testimonial', 'urban-taxi-client-testimonial' ); }
    public function get_icon()       { return 'eicon-testimonial-carousel'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'testimonial', 'slider', 'carousel', 'review', 'quote', 'stacked' ]; }

    public function get_style_depends()  { return [ 'urban-taxi-client-testimonial-style', 'tsw-style' ]; }
    public function get_script_depends() { return [ 'urban-taxi-client-testimonial-script', 'tsw-script' ]; }

    protected function register_controls() {

        /* ── CONTENT: Testimonials Repeater ── */
        $this->start_controls_section( 'section_testimonials', [
            'label' => esc_html__( 'Testimonials', 'urban-taxi-client-testimonial' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new Repeater();

        $repeater->add_control( 'client_image', [
            'label'   => esc_html__( 'Client Photo', 'urban-taxi-client-testimonial' ),
            'type'    => Controls_Manager::MEDIA,
            'default' => [ 'url' => Utils::get_placeholder_image_src() ],
        ]);

        $repeater->add_control( 'client_name', [
            'label'       => esc_html__( 'Client Name', 'urban-taxi-client-testimonial' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'John Doe',
            'label_block' => true,
        ]);

        $repeater->add_control( 'client_title', [
            'label'       => esc_html__( 'Title / Role', 'urban-taxi-client-testimonial' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Business Traveler',
            'label_block' => true,
        ]);

        $repeater->add_control( 'testimonial_text', [
            'label'       => esc_html__( 'Testimonial', 'urban-taxi-client-testimonial' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => 'Absolutely amazing experience! The car was immaculate, the driver was professional, and the service was top-notch. Will definitely again!',
            'label_block' => true,
            'rows'        => 5,
        ]);

        $repeater->add_control( 'star_rating', [
            'label'   => esc_html__( 'Star Rating', 'urban-taxi-client-testimonial' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '5',
            'options' => [ '1'=>'1 Star','2'=>'2 Stars','3'=>'3 Stars','4'=>'4 Stars','5'=>'5 Stars' ],
        ]);

        $this->add_control( 'testimonials', [
            'label'       => esc_html__( 'Testimonials', 'urban-taxi-client-testimonial' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'client_name'=>'David Anderson',    'client_title'=>'Travel Blogger',       'testimonial_text'=>'Absolutely amazing experience! The car was immaculate, the driver was professional, and the service was top-notch. Will definitely again!', 'star_rating'=>'5' ],
                [ 'client_name'=>'Sophia Martinezson','client_title'=>'Business Traveler',    'testimonial_text'=>'Absolutely amazing experience! The car was immaculate, the driver was professional, and the service was top-notch. Will definitely again!', 'star_rating'=>'5' ],
                [ 'client_name'=>'James Mitchell',    'client_title'=>'Entrepreneur',         'testimonial_text'=>'Amazing experience! The car was immaculate, the driver was professional, and the service was top-notch. Will definitely again!',             'star_rating'=>'5' ],
                [ 'client_name'=>'Emily Johnson',     'client_title'=>'Marketing Director',   'testimonial_text'=>'Exceptional service from start to finish. The vehicle was pristine and the driver was courteous and professional throughout the entire journey.', 'star_rating'=>'5' ],
                [ 'client_name'=>'Robert Chen',       'client_title'=>'Corporate Executive',  'testimonial_text'=>'I have used many car services and this is by far the best. Punctual, professional, and the vehicles are always in perfect condition.',      'star_rating'=>'5' ],
            ],
            'title_field' => '{{{ client_name }}}',
        ]);

        $this->end_controls_section();

        /* ── CONTENT: Slider Settings ── */
        $this->start_controls_section( 'section_slider_settings', [
            'label' => esc_html__( 'Slider Settings', 'urban-taxi-client-testimonial' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'autoplay', [
            'label'        => esc_html__( 'Autoplay', 'urban-taxi-client-testimonial' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control( 'autoplay_speed', [
            'label'     => esc_html__( 'Autoplay Delay (ms)', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 4000,
            'min'       => 1000,
            'max'       => 12000,
            'step'      => 500,
            'condition' => [ 'autoplay' => 'yes' ],
        ]);

        $this->add_control( 'transition_speed', [
            'label'   => esc_html__( 'Transition Speed (ms)', 'urban-taxi-client-testimonial' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 650,
            'min'     => 200,
            'max'     => 1500,
            'step'    => 50,
        ]);

        $this->add_control( 'show_navigation', [
            'label'        => esc_html__( 'Show Navigation Arrows', 'urban-taxi-client-testimonial' ),
            'type'         => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_responsive_control( 'slides_per_view', [
            'label'   => esc_html__( 'Slides Per View', 'urban-taxi-client-testimonial' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '5',
            'tablet_default' => '5',
            'mobile_default' => '5',
            'options' => [
                '1' => esc_html__( '1', 'urban-taxi-client-testimonial' ),
                '3' => esc_html__( '3', 'urban-taxi-client-testimonial' ),
                '5' => esc_html__( '5', 'urban-taxi-client-testimonial' ),
            ],
            'description' => esc_html__( 'Set visible slides for desktop, tablet, and mobile.', 'urban-taxi-client-testimonial' ),
        ]);

        $this->end_controls_section();

        /* ── STYLE: Cards ── */
        $this->start_controls_section( 'style_cards', [
            'label' => esc_html__( 'Cards', 'urban-taxi-client-testimonial' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'card_bg', [
            'label'     => esc_html__( 'Card Background', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1c1c1c',
            'selectors' => [ '{{WRAPPER}} .tsw-card' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'card_active_bg', [
            'label'     => esc_html__( 'Active Card Background', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#242424',
            'selectors' => [ '{{WRAPPER}} .tsw-slide[data-pos="0"] .tsw-card' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'card_border_color', [
            'label'     => esc_html__( 'Border Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2e2e2e',
            'selectors' => [ '{{WRAPPER}} .tsw-card' => 'border-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'card_active_border_color', [
            'label'     => esc_html__( 'Active Border Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5c518',
            'selectors' => [ '{{WRAPPER}} .tsw-slide[data-pos="0"] .tsw-card' => 'border-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'card_border_radius', [
            'label'      => esc_html__( 'Border Radius', 'urban-taxi-client-testimonial' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'default'    => [ 'top'=>'18','right'=>'18','bottom'=>'18','left'=>'18','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .tsw-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->add_responsive_control( 'card_padding', [
            'label'      => esc_html__( 'Card Padding', 'urban-taxi-client-testimonial' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'default'    => [ 'top'=>'38','right'=>'34','bottom'=>'30','left'=>'34','unit'=>'px' ],
            'tablet_default' => [ 'top'=>'30','right'=>'26','bottom'=>'24','left'=>'26','unit'=>'px' ],
            'mobile_default' => [ 'top'=>'24','right'=>'18','bottom'=>'20','left'=>'18','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .tsw-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();

        /* ── STYLE: Card Content ── */
        $this->start_controls_section( 'style_card_content', [
            'label' => esc_html__( 'Card Content', 'urban-taxi-client-testimonial' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'text_color', [
            'label'     => esc_html__( 'Testimonial Text', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#c8c8c8',
            'selectors' => [ '{{WRAPPER}} .tsw-text' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'text_typography',
            'selector' => '{{WRAPPER}} .tsw-text',
        ]);

        $this->add_control( 'name_color', [
            'label'     => esc_html__( 'Client Name Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5c518',
            'selectors' => [ '{{WRAPPER}} .tsw-client-name' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'name_typography',
            'selector' => '{{WRAPPER}} .tsw-client-name',
        ]);

        $this->add_control( 'title_color', [
            'label'     => esc_html__( 'Title / Role Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#7a7a7a',
            'selectors' => [ '{{WRAPPER}} .tsw-client-title' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .tsw-client-title',
        ]);

        $this->add_control( 'star_color', [
            'label'     => esc_html__( 'Star Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5c518',
            'selectors' => [ '{{WRAPPER}} .tsw-star.filled' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'quote_color', [
            'label'     => esc_html__( 'Quote Icon Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5c518',
            'selectors' => [ '{{WRAPPER}} .tsw-quote-icon' => 'color: {{VALUE}};' ],
        ]);
        

        $this->end_controls_section();

        /* ── STYLE: Navigation ── */
        $this->start_controls_section( 'style_nav', [
            'label'     => esc_html__( 'Navigation Arrows', 'urban-taxi-client-testimonial' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'show_navigation' => 'yes' ],
        ]);

        $this->add_control( 'nav_bg', [
            'label'     => esc_html__( 'Background', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1e1e1e',
            'selectors' => [ '{{WRAPPER}} .tsw-nav-btn' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'nav_color', [
            'label'     => esc_html__( 'Icon Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .tsw-nav-btn' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'nav_border_color', [
            'label'     => esc_html__( 'Border Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2e2e2e',
            'selectors' => [ '{{WRAPPER}} .tsw-nav-btn' => 'border-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'nav_border_radius', [
            'label'      => esc_html__( 'Border Radius', 'urban-taxi-client-testimonial' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [
                'px' => [ 'min' => 0, 'max' => 100 ],
                '%'  => [ 'min' => 0, 'max' => 50 ],
            ],
            'default'    => [
                'unit' => '%',
                'size' => 50,
            ],
            'selectors'  => [ '{{WRAPPER}} .tsw-nav-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ]);

        $this->add_control( 'nav_font_size', [
            'label'      => esc_html__( 'Font Size', 'urban-taxi-client-testimonial' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem' ],
            'range'      => [
                'px' => [ 'min' => 8, 'max' => 48 ],
                'em' => [ 'min' => 0.5, 'max' => 3, 'step' => 0.1 ],
                'rem' => [ 'min' => 0.5, 'max' => 3, 'step' => 0.1 ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 18,
            ],
            'selectors'  => [ '{{WRAPPER}} .tsw-nav-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
        ]);

        $this->add_control( 'nav_hover_bg', [
            'label'     => esc_html__( 'Hover Background', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5c518',
            'selectors' => [ '{{WRAPPER}} .tsw-nav-btn:hover' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'nav_hover_color', [
            'label'     => esc_html__( 'Hover Icon Color', 'urban-taxi-client-testimonial' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#0a0a0a',
            'selectors' => [ '{{WRAPPER}} .tsw-nav-btn:hover' => 'color: {{VALUE}};' ],
        ]);
        

        $this->end_controls_section();
    }

    /* ============================================================
       RENDER
    ============================================================ */
    protected function render() {
        $settings     = $this->get_settings_for_display();
        $testimonials = $settings['testimonials'];
        if ( empty( $testimonials ) ) return;

        $widget_id  = $this->get_id();
        $show_nav   = $settings['show_navigation'] === 'yes';

        $desktop_spv = (int) ( $settings['slides_per_view'] ?? 5 );
        $tablet_spv  = (int) ( $settings['slides_per_view_tablet'] ?? $desktop_spv );
        $mobile_spv  = (int) ( $settings['slides_per_view_mobile'] ?? $tablet_spv );

        $cfg = json_encode([
            'autoplay'      => $settings['autoplay'] === 'yes',
            'autoplaySpeed' => (int) ( $settings['autoplay_speed'] ?? 4000 ),
            'transition'    => (int) ( $settings['transition_speed'] ?? 650 ),
            'slidesPerView' => [
                'desktop' => $desktop_spv,
                'tablet'  => $tablet_spv,
                'mobile'  => $mobile_spv,
            ],
        ]);
        ?>
        <div class="tsw-testimonial-section" id="tsw-<?php echo esc_attr( $widget_id ); ?>">

            <!-- Slider -->
            <div class="tsw-slider-wrapper">

                <?php if ( $show_nav ) : ?>
                <button class="tsw-nav-btn tsw-prev" aria-label="<?php esc_attr_e( 'Previous', 'urban-taxi-client-testimonial' ); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <?php endif; ?>

                <!-- Stage: all cards are children here, positioned by JS via data-pos -->
                <div class="tsw-stage" data-tsw-settings='<?php echo esc_attr( $cfg ); ?>'>
                    <?php foreach ( $testimonials as $index => $item ) :
                        $rating  = (int) ( $item['star_rating'] ?? 5 );
                        $img_url = ! empty( $item['client_image']['url'] ) ? $item['client_image']['url'] : Utils::get_placeholder_image_src();
                        // Initial position: centre slide is index 0 (JS will recompute on init)
                        $init_pos = $index; // just a placeholder; JS overrides immediately
                    ?>
                    <div class="tsw-slide" data-pos="hidden" data-index="<?php echo esc_attr( $index ); ?>"
                         role="group" aria-label="Testimonial <?php echo esc_attr( $index + 1 ); ?> of <?php echo esc_attr( count( $testimonials ) ); ?>">
                        <div class="tsw-card">
                            <!-- Avatar -->
                            <div class="tsw-avatar-wrap">
                                <img class="tsw-avatar"
                                     src="<?php echo esc_url( $img_url ); ?>"
                                     alt="<?php echo esc_attr( $item['client_name'] ); ?>"
                                     loading="lazy" width="88" height="88"/>
                                <div class="tsw-avatar-ring" aria-hidden="true"></div>
                            </div>

                            <!-- Stars -->
                            <div class="tsw-stars" aria-label="Rating: <?php echo esc_attr( $rating ); ?> out of 5">
                                <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                                <svg class="tsw-star <?php echo $s <= $rating ? 'filled' : 'empty'; ?>"
                                     viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <?php endfor; ?>
                            </div>

                            <!-- Text -->
                            <p class="tsw-text"><?php echo esc_html( $item['testimonial_text'] ); ?></p>

                            <!-- Client -->
                            <div class="tsw-client-info">
                                <span class="tsw-client-name"><?php echo esc_html( $item['client_name'] ); ?></span>
                                <span class="tsw-client-title"><?php echo esc_html( $item['client_title'] ); ?></span>
                            </div>

                            <!-- Quote -->
                            <div class="tsw-quote-icon" aria-hidden="true">
                                <svg viewBox="0 0 40 30" fill="currentColor" width="34" height="26">
                                    <path d="M0 30V18.571C0 8.095 6.667 2.381 20 0l2.857 4.286C16.508 5.397 12.698 8.254 11.429 12.857H18V30H0zm22 0V18.571C22 8.095 28.667 2.381 42 0l2.857 4.286C38.508 5.397 34.698 8.254 33.429 12.857H40V30H22z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div><!-- /.tsw-stage -->

                <?php if ( $show_nav ) : ?>
                <button class="tsw-nav-btn tsw-next" aria-label="<?php esc_attr_e( 'Next', 'urban-taxi-client-testimonial' ); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><path d="M9 18l6-6-6-6"/></svg>
                </button>
                <?php endif; ?>

            </div><!-- /.tsw-slider-wrapper -->
        </div>
        <?php
    }

    /* ============================================================
       EDITOR LIVE PREVIEW
    ============================================================ */
    protected function content_template() {
        ?>
        <#
        var items = settings.testimonials;
        if ( !items || !items.length ) return;
        #>
        <div class="tsw-testimonial-section">
            <div class="tsw-editor-preview">
                <# _.each( items, function( item, i ) {
                    if ( i > 2 ) return;
                    var rating  = parseInt( item.star_rating ) || 5;
                    var imgUrl  = item.client_image && item.client_image.url ? item.client_image.url : '';
                    var posClass = ( i === 1 ) ? 'tsw-active' : '';
                #>
                <div class="tsw-slide {{ posClass }}">
                    <div class="tsw-card">
                        <# if ( imgUrl ) { #>
                        <div class="tsw-avatar-wrap">
                            <img class="tsw-avatar" src="{{{ imgUrl }}}" alt="{{{ item.client_name }}}"/>
                            <div class="tsw-avatar-ring"></div>
                        </div>
                        <# } #>
                        <div class="tsw-stars">
                            <# for( var s = 1; s <= 5; s++ ) { #>
                            <svg class="tsw-star <# if(s<=rating){#>filled<#}else{#>empty<#}#>" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <# } #>
                        </div>
                        <p class="tsw-text">{{{ item.testimonial_text }}}</p>
                        <div class="tsw-client-info">
                            <span class="tsw-client-name">{{{ item.client_name }}}</span>
                            <span class="tsw-client-title">{{{ item.client_title }}}</span>
                        </div>
                        <div class="tsw-quote-icon">
                            <svg viewBox="0 0 40 30" fill="currentColor" width="32" height="24">
                                <path d="M0 30V18.571C0 8.095 6.667 2.381 20 0l2.857 4.286C16.508 5.397 12.698 8.254 11.429 12.857H18V30H0zm22 0V18.571C22 8.095 28.667 2.381 42 0l2.857 4.286C38.508 5.397 34.698 8.254 33.429 12.857H40V30H22z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <# }); #>
            </div>
        </div>
        <?php
    }
}

if ( ! class_exists( 'TSW_Testimonial_Slider_Widget', false ) ) {
    class_alias( 'Urban_Taxi_Client_Testimonial_Widget', 'TSW_Testimonial_Slider_Widget' );
}
