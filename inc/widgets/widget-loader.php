<?php
/**
 * UrbanTaxi Core - Widget Loader
 * Registers all Elementor widgets and bootstraps all per-widget hooks/assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load classic WordPress widgets bundled in core plugin.
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/taxonomy/urbantaxi-taxonomy-widget.php';

// =============================================================================
// CONSTANTS — map each original plugin's path/URL constants into the core plugin
// =============================================================================

// booking-slider
if ( ! defined( 'URBAN_TAXI_CAB_BOOKING_SLIDER_VERSION' ) )    define( 'URBAN_TAXI_CAB_BOOKING_SLIDER_VERSION',    '1.0.0' );
if ( ! defined( 'URBAN_TAXI_CAB_BOOKING_SLIDER_PLUGIN_DIR' ) ) define( 'URBAN_TAXI_CAB_BOOKING_SLIDER_PLUGIN_DIR', URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/booking-slider/' );
if ( ! defined( 'URBAN_TAXI_CAB_BOOKING_SLIDER_PLUGIN_URL' ) ) define( 'URBAN_TAXI_CAB_BOOKING_SLIDER_PLUGIN_URL', URBANTAXI_CORE_PLUGIN_URL . 'inc/widgets/booking-slider/' );

// cab-filter
if ( ! defined( 'URBAN_TAXI_CAB_FILTER_WIDGET_VERSION' ) ) define( 'URBAN_TAXI_CAB_FILTER_WIDGET_VERSION', '1.0.0' );
if ( ! defined( 'URBAN_TAXI_CAB_FILTER_WIDGET_DIR' ) )     define( 'URBAN_TAXI_CAB_FILTER_WIDGET_DIR',     URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/cab-filter/' );
if ( ! defined( 'URBAN_TAXI_CAB_FILTER_WIDGET_URL' ) )     define( 'URBAN_TAXI_CAB_FILTER_WIDGET_URL',     URBANTAXI_CORE_PLUGIN_URL . 'inc/widgets/cab-filter/' );

// client-testimonial
if ( ! defined( 'UTCT_VERSION' ) ) define( 'UTCT_VERSION', '1.0.0' );
if ( ! defined( 'UTCT_PATH' ) )    define( 'UTCT_PATH',    URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/client-testimonial/' );
if ( ! defined( 'UTCT_URL' ) )     define( 'UTCT_URL',     URBANTAXI_CORE_PLUGIN_URL . 'inc/widgets/client-testimonial/' );
if ( ! defined( 'TSW_VERSION' ) )  define( 'TSW_VERSION',  UTCT_VERSION );
if ( ! defined( 'TSW_PATH' ) )     define( 'TSW_PATH',     UTCT_PATH );
if ( ! defined( 'TSW_URL' ) )      define( 'TSW_URL',      UTCT_URL );

// service-cards
if ( ! defined( 'URBANTAXI_SERVICE_CARDS_WIDGET_VERSION' ) )    define( 'URBANTAXI_SERVICE_CARDS_WIDGET_VERSION',    '1.0.0' );
if ( ! defined( 'URBANTAXI_SERVICE_CARDS_WIDGET_PLUGIN_DIR' ) ) define( 'URBANTAXI_SERVICE_CARDS_WIDGET_PLUGIN_DIR', URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/service-cards/' );
if ( ! defined( 'URBANTAXI_SERVICE_CARDS_WIDGET_PLUGIN_URL' ) ) define( 'URBANTAXI_SERVICE_CARDS_WIDGET_PLUGIN_URL', URBANTAXI_CORE_PLUGIN_URL . 'inc/widgets/service-cards/' );

// book-seat
if ( ! defined( 'URBANTAXI_BOOK_SEAT_WIDGET_PATH' ) )        define( 'URBANTAXI_BOOK_SEAT_WIDGET_PATH',        URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/book-seat/' );
if ( ! defined( 'URBANTAXI_BOOK_SEAT_WIDGET_URL' ) )         define( 'URBANTAXI_BOOK_SEAT_WIDGET_URL',         URBANTAXI_CORE_PLUGIN_URL . 'inc/widgets/book-seat/' );
if ( ! defined( 'URBANTAXI_BOOK_SEAT_WIDGET_VERSION' ) )     define( 'URBANTAXI_BOOK_SEAT_WIDGET_VERSION',     '1.0.0' );
if ( ! defined( 'URBANTAXI_BOOK_SEAT_WIDGET_TEXT_DOMAIN' ) ) define( 'URBANTAXI_BOOK_SEAT_WIDGET_TEXT_DOMAIN', 'urbantaxi-book-seat-widget' );

// our-blog
if ( ! defined( 'URBANTAXI_OUR_BLOG_WIDGET_PATH' ) )    define( 'URBANTAXI_OUR_BLOG_WIDGET_PATH',    URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/our-blog/' );
if ( ! defined( 'URBANTAXI_OUR_BLOG_WIDGET_URL' ) )     define( 'URBANTAXI_OUR_BLOG_WIDGET_URL',     URBANTAXI_CORE_PLUGIN_URL . 'inc/widgets/our-blog/' );
if ( ! defined( 'URBANTAXI_OUR_BLOG_WIDGET_VERSION' ) ) define( 'URBANTAXI_OUR_BLOG_WIDGET_VERSION', '1.0.0' );

// heading-style
if ( ! defined( 'HEADING_STYLE_WIDGET_VERSION' ) )    define( 'HEADING_STYLE_WIDGET_VERSION',    '1.0.0' );
if ( ! defined( 'HEADING_STYLE_WIDGET_PLUGIN_DIR' ) ) define( 'HEADING_STYLE_WIDGET_PLUGIN_DIR', URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/heading-style/' );
if ( ! defined( 'HEADING_STYLE_WIDGET_PLUGIN_URL' ) ) define( 'HEADING_STYLE_WIDGET_PLUGIN_URL', URBANTAXI_CORE_PLUGIN_URL . 'inc/widgets/heading-style/' );

// smart-animations
if ( ! defined( 'URBANTAXI_SMART_ANIMATIONS_VERSION' ) ) define( 'URBANTAXI_SMART_ANIMATIONS_VERSION', '1.0.0' );
if ( ! defined( 'URBANTAXI_SMART_ANIMATIONS_PATH' ) )    define( 'URBANTAXI_SMART_ANIMATIONS_PATH',    URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/smart-animations/' );
if ( ! defined( 'URBANTAXI_SMART_ANIMATIONS_URL' ) )     define( 'URBANTAXI_SMART_ANIMATIONS_URL',     URBANTAXI_CORE_PLUGIN_URL );

// sticky-mission
if ( ! defined( 'URBANTAXI_STICKY_VERSION' ) )    define( 'URBANTAXI_STICKY_VERSION',    '1.0.0' );
if ( ! defined( 'URBANTAXI_STICKY_PLUGIN_DIR' ) ) define( 'URBANTAXI_STICKY_PLUGIN_DIR', URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/sticky-mission/' );
if ( ! defined( 'URBANTAXI_STICKY_PLUGIN_URL' ) ) define( 'URBANTAXI_STICKY_PLUGIN_URL', URBANTAXI_CORE_PLUGIN_URL );

// timeline
if ( ! defined( 'URBANTAXI_TIMELINE_VERSION' ) )    define( 'URBANTAXI_TIMELINE_VERSION',    '1.0.0' );
if ( ! defined( 'URBANTAXI_TIMELINE_PLUGIN_DIR' ) ) define( 'URBANTAXI_TIMELINE_PLUGIN_DIR', URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/timeline/' );
if ( ! defined( 'URBANTAXI_TIMELINE_PLUGIN_URL' ) ) define( 'URBANTAXI_TIMELINE_PLUGIN_URL', URBANTAXI_CORE_PLUGIN_URL );

// hero-slider
if ( ! defined( 'URBANTAXI_HERO_SLIDER_VERSION' ) )    define( 'URBANTAXI_HERO_SLIDER_VERSION',    '1.0.0' );
if ( ! defined( 'URBANTAXI_HERO_SLIDER_PATH' ) )      define( 'URBANTAXI_HERO_SLIDER_PATH',      URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/hero-slider/' );
if ( ! defined( 'URBANTAXI_HERO_SLIDER_URL' ) )       define( 'URBANTAXI_HERO_SLIDER_URL',       URBANTAXI_CORE_PLUGIN_URL );

// =============================================================================
// BOOK-SEAT — PSR-4 autoloader + PluginServiceProvider (handles assets & widget)
// =============================================================================
$_bsw_autoload = URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/book-seat/vendor/autoload.php';
if ( file_exists( $_bsw_autoload ) ) {
    require_once $_bsw_autoload;
    if ( class_exists( 'UrbanTaxi\\BookSeatWidget\\Providers\\PluginServiceProvider' ) ) {
        \UrbanTaxi\BookSeatWidget\Providers\PluginServiceProvider::boot();
    }
}

// =============================================================================
// OUR-BLOG — PSR-4 autoloader + PluginServiceProvider (handles assets & widget)
// =============================================================================
$_obw_autoload = URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/our-blog/vendor/autoload.php';
if ( file_exists( $_obw_autoload ) ) {
    require_once $_obw_autoload;
    if ( class_exists( 'UrbanTaxi\\OurBlogWidget\\Providers\\PluginServiceProvider' ) ) {
        \UrbanTaxi\OurBlogWidget\Providers\PluginServiceProvider::boot();
    }
}

// =============================================================================
// BOOKING-SLIDER — enqueue scripts & styles
// =============================================================================
add_action( 'elementor/frontend/after_enqueue_scripts', 'urbantaxi_core_booking_slider_enqueue' );
add_action( 'elementor/editor/after_enqueue_scripts',   'urbantaxi_core_booking_slider_enqueue' );
function urbantaxi_core_booking_slider_enqueue() {
    wp_enqueue_script(
        'urban-taxi-cab-booking-slider-js',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/js/urban-taxi-cab-booking-slider.js',
        array( 'jquery' ),
        URBAN_TAXI_CAB_BOOKING_SLIDER_VERSION,
        true
    );
    wp_localize_script( 'urban-taxi-cab-booking-slider-js', 'utcbAjax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'utcb_nonce' ),
    ) );
}
add_action( 'elementor/frontend/after_enqueue_styles', 'urbantaxi_core_booking_slider_styles' );
add_action( 'elementor/editor/after_enqueue_styles',   'urbantaxi_core_booking_slider_styles' );
function urbantaxi_core_booking_slider_styles() {
    wp_enqueue_style(
        'urban-taxi-cab-booking-slider-css',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/urban-taxi-cab-booking-slider.css',
        array(),
        URBAN_TAXI_CAB_BOOKING_SLIDER_VERSION
    );
}

// =============================================================================
// CAB-FILTER — enqueue scripts & styles + AJAX handler
// =============================================================================
add_action( 'elementor/frontend/after_enqueue_scripts', 'urbantaxi_core_cab_filter_enqueue' );
add_action( 'elementor/editor/after_enqueue_scripts',   'urbantaxi_core_cab_filter_enqueue' );
function urbantaxi_core_cab_filter_enqueue() {
    wp_enqueue_script(
        'urban-taxi-cab-filter-widget-js',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/js/urban-taxi-cab-filter-widget.js',
        array( 'jquery' ),
        URBAN_TAXI_CAB_FILTER_WIDGET_VERSION,
        true
    );
    wp_localize_script( 'urban-taxi-cab-filter-widget-js', 'utcfw_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ) );
}
add_action( 'elementor/frontend/after_enqueue_styles', 'urbantaxi_core_cab_filter_styles' );
add_action( 'elementor/editor/after_enqueue_styles',   'urbantaxi_core_cab_filter_styles' );
function urbantaxi_core_cab_filter_styles() {
    wp_enqueue_style(
        'urban-taxi-cab-filter-widget-css',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/urban-taxi-cab-filter-widget.css',
        array(),
        URBAN_TAXI_CAB_FILTER_WIDGET_VERSION
    );
}
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/cab-filter/ajax-handlers.php';
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/booking-slider/ajax-handlers.php';

// =============================================================================
// CLIENT-TESTIMONIAL — register styles & scripts
// =============================================================================
add_action( 'elementor/frontend/after_enqueue_styles',    'urbantaxi_core_testimonial_styles' );
add_action( 'elementor/editor/after_enqueue_styles',      'urbantaxi_core_testimonial_styles' );
function urbantaxi_core_testimonial_styles() {
    wp_register_style( 'urban-taxi-client-testimonial-style', URBANTAXI_CORE_PLUGIN_URL . 'assets/css/urban-taxi-client-testimonial.css', array(), UTCT_VERSION );
    wp_register_style( 'tsw-style', URBANTAXI_CORE_PLUGIN_URL . 'assets/css/urban-taxi-client-testimonial.css', array(), UTCT_VERSION );
    wp_enqueue_style( 'urban-taxi-client-testimonial-style' );
}
add_action( 'elementor/frontend/after_register_scripts', 'urbantaxi_core_testimonial_scripts' );
add_action( 'elementor/editor/after_enqueue_scripts',    'urbantaxi_core_testimonial_scripts' );
function urbantaxi_core_testimonial_scripts() {
    wp_register_script( 'urban-taxi-client-testimonial-script', URBANTAXI_CORE_PLUGIN_URL . 'assets/js/urban-taxi-client-testimonial.js', array(), UTCT_VERSION, true );
    wp_register_script( 'tsw-script', URBANTAXI_CORE_PLUGIN_URL . 'assets/js/urban-taxi-client-testimonial.js', array(), UTCT_VERSION, true );
}

// =============================================================================
// SERVICE-CARDS — enqueue assets + AJAX handler
// =============================================================================
add_action( 'wp_enqueue_scripts', 'urbantaxi_core_service_cards_frontend_assets' );
function urbantaxi_core_service_cards_frontend_assets() {
    wp_register_style( 'font-awesome-css', URBANTAXI_CORE_PLUGIN_URL . 'assets/lib/all.min.css', array(), URBANTAXI_SERVICE_CARDS_WIDGET_VERSION );
    wp_register_script( 'font-awesome-js', URBANTAXI_CORE_PLUGIN_URL . 'assets/lib/all.min.js', array( 'jquery' ), URBANTAXI_SERVICE_CARDS_WIDGET_VERSION, true );
    wp_register_style( 'urbantaxi-service-cards-widget-style', URBANTAXI_CORE_PLUGIN_URL . 'assets/css/urbantaxi-service-cards-widget.css', array(), URBANTAXI_SERVICE_CARDS_WIDGET_VERSION );
    wp_register_script( 'urbantaxi-service-cards-widget-script', URBANTAXI_CORE_PLUGIN_URL . 'assets/js/urbantaxi-service-cards-widget.js', array( 'jquery' ), URBANTAXI_SERVICE_CARDS_WIDGET_VERSION, true );
    wp_localize_script( 'urbantaxi-service-cards-widget-script', 'urbantaxiServiceCardsWidgetAjax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'urbantaxi_service_cards_widget_nonce' ),
    ) );
    wp_enqueue_style( 'urbantaxi-service-cards-widget-style' );
    wp_enqueue_script( 'urbantaxi-service-cards-widget-script' );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'urbantaxi_core_service_cards_editor_assets' );
function urbantaxi_core_service_cards_editor_assets() {
    wp_enqueue_style( 'urbantaxi-service-cards-widget-style', URBANTAXI_CORE_PLUGIN_URL . 'assets/css/urbantaxi-service-cards-widget.css', array(), URBANTAXI_SERVICE_CARDS_WIDGET_VERSION );
}
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/service-cards/ajax-handlers.php';

// =============================================================================
// HEADING-STYLE — inline CSS + editor preview script
// =============================================================================
add_action( 'elementor/frontend/after_enqueue_styles', function() {
    wp_add_inline_style( 'elementor-frontend', '.heading-text-gradient { -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; background-clip: text !important; }' );
} );
add_action( 'elementor/editor/after_enqueue_scripts', function() {
    wp_register_script( 'heading-style-preview', URBANTAXI_CORE_PLUGIN_URL . 'assets/js/heading-style-preview.js', array( 'jquery', 'elementor-frontend' ), HEADING_STYLE_WIDGET_VERSION, true );
} );

// =============================================================================
// STICKY-MISSION — register style & script
// =============================================================================
add_action( 'wp_enqueue_scripts',                          'urbantaxi_core_sticky_assets' );
add_action( 'elementor/frontend/after_register_scripts',   'urbantaxi_core_sticky_assets' );
add_action( 'elementor/editor/before_enqueue_scripts',     'urbantaxi_core_sticky_assets' );
function urbantaxi_core_sticky_assets() {
    wp_register_style(
        'urbantaxi-sticky-style',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/sticky-mission.css',
        array(),
        URBANTAXI_STICKY_VERSION
    );
    wp_register_script(
        'urbantaxi-sticky-script',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/js/script.js',
        array( 'jquery' ),
        URBANTAXI_STICKY_VERSION,
        true
    );
}

// =============================================================================
// TEAM-CAROUSEL — register style & script
// =============================================================================
add_action( 'elementor/frontend/after_register_scripts', 'urbantaxi_core_team_assets' );
add_action( 'elementor/frontend/after_register_styles',  'urbantaxi_core_team_assets' );
function urbantaxi_core_team_assets() {
    wp_enqueue_script( 'swiper' );
    wp_register_script(
        'team-carousel-script',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/js/team-carousel.js',
        array( 'jquery', 'swiper' ),
        '1.0',
        true
    );
    wp_register_style(
        'team-carousel-style',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/team-carousel.css',
        array(),
        '1.0'
    );
}

// =============================================================================
// TIMELINE — enqueue style & script
// =============================================================================
add_action( 'wp_enqueue_scripts', 'urbantaxi_core_timeline_assets' );
function urbantaxi_core_timeline_assets() {
    wp_enqueue_style(
        'urbantaxi-timeline-style',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/timeline.css',
        array(),
        URBANTAXI_TIMELINE_VERSION
    );
    wp_enqueue_script(
        'urbantaxi-timeline-script',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/js/timeline.js',
        array( 'jquery' ),
        URBANTAXI_TIMELINE_VERSION,
        true
    );
}

// =============================================================================
// HERO-SLIDER — enqueue styles & register scripts
// =============================================================================
add_action( 'elementor/frontend/after_enqueue_styles',   'urbantaxi_core_hero_slider_styles' );
function urbantaxi_core_hero_slider_styles() {
    wp_enqueue_style(
        'swiper',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/lib/swiper-bundle.min.css',
        array(),
        URBANTAXI_HERO_SLIDER_VERSION
    );
    wp_enqueue_style(
        'urbantaxi-hero-slider',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/hero-slider.css',
        array( 'swiper' ),
        URBANTAXI_HERO_SLIDER_VERSION
    );
}
add_action( 'elementor/frontend/after_register_scripts', 'urbantaxi_core_hero_slider_scripts' );
function urbantaxi_core_hero_slider_scripts() {
    wp_register_script(
        'swiper',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/lib/swiper-bundle.min.js',
        array(),
        URBANTAXI_HERO_SLIDER_VERSION,
        true
    );
    wp_register_script(
        'urbantaxi-hero-slider',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/js/hero-slider.js',
        array( 'swiper' ),
        URBANTAXI_HERO_SLIDER_VERSION,
        true
    );
}

// =============================================================================
// ELEMENTOR WIDGET REGISTRATION
// =============================================================================

function urbantaxi_core_register_widgets( $widgets_manager ) {
    if ( ! did_action( 'elementor/loaded' ) ) {
        return;
    }

    $widgets = array(
        'heading-style' => array(
            'class' => 'Heading_Style_Widget',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/heading-style/class-heading-style-widget.php',
        ),
        'booking-slider' => array(
            'class' => 'Urban_Taxi_Cab_Booking_Slider_Widget',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/booking-slider/class-urban-taxi-cab-booking-slider-widget.php',
        ),
        'cab-filter' => array(
            'class' => 'Urban_Taxi_Cab_Filter_Widget',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/cab-filter/class-urban-taxi-cab-filter-widget.php',
        ),
        'client-testimonial' => array(
            'class' => 'Urban_Taxi_Client_Testimonial_Widget',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/client-testimonial/urban-taxi-client-testimonial.php',
        ),
        // book-seat & our-blog are registered by their PluginServiceProvider::boot() above
        'service-cards' => array(
            'class' => 'UrbanTaxi_Service_Cards_Widget\\UrbanTaxi_Service_Cards_Widget',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/service-cards/class-urbantaxi-service-cards-widget.php',
        ),
        'sticky-mission' => array(
            'class' => 'UrbanTaxi_Elementor_Widget',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/sticky-mission/elementor-widget.php',
        ),
        'team' => array(
            'class' => 'Urbantaxi_Team_Carousel',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/team/team-carousel.php',
        ),
        'timeline' => array(
            'class' => 'UrbanTaxi_Timeline_Elementor_Widget',
            'file'  => URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/timeline/elementor-timeline-widget.php',
        ),
        'hero-slider' => array(
            'class' => 'UrbanTaxi\Widgets\Hero_Slider_Widget',
            'file'  => URBANTAXI_HERO_SLIDER_PATH . 'includes/widgets/class-hero-slider-widget.php',
        ),
    );

    foreach ( $widgets as $slug => $widget_config ) {
        $class_name  = $widget_config['class'];
        $widget_file = $widget_config['file'];

        if ( ! file_exists( $widget_file ) ) {
            error_log( "UrbanTaxi Core: Widget file not found - {$widget_file}" );
            continue;
        }

        require_once $widget_file;

        if ( ! class_exists( $class_name ) ) {
            error_log( "UrbanTaxi Core: Widget class not found - {$class_name}" );
            continue;
        }

        try {
            $widgets_manager->register( new $class_name() );
        } catch ( Exception $e ) {
            error_log( "UrbanTaxi Core: Error registering widget {$class_name} - " . $e->getMessage() );
        }
    }

    // Initialize smart-animations integration (singleton, not an Elementor widget)
    $smart_animations_file = URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/smart-animations/elementor-integration.php';
    if ( file_exists( $smart_animations_file ) ) {
        require_once $smart_animations_file;
    }
}

add_action( 'elementor/widgets/register', 'urbantaxi_core_register_widgets', 99 );

function urbantaxi_core_register_widget_categories( $elements_manager ) {
    $elements_manager->add_category(
        'urbantaxi-widgets',
        array(
            'title' => esc_attr__( 'UrbanTaxi Widgets', 'urbantaxi-core' ),
            'icon'  => 'fa fa-taxi',
        )
    );
}

add_action( 'elementor/elements/categories_registered', 'urbantaxi_core_register_widget_categories' );


