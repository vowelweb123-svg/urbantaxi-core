<?php
/**
 * UrbanTaxi Core - Asset Registration
 * Centralized registration of all widget assets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function urbantaxi_core_register_assets() {
    $plugin_url = URBANTAXI_CORE_PLUGIN_URL;
    $version = URBANTAXI_CORE_VERSION;

    // ===== SHARED LIBRARIES =====
    wp_register_script(
        'urbantaxi-swiper',
        $plugin_url . 'assets/lib/swiper-bundle.min.js',
        [],
        '8.4.5',
        true
    );

    wp_register_style(
        'urbantaxi-font-awesome',
        $plugin_url . 'assets/lib/all.min.css',
        [],
        '6.0.0'
    );

    wp_register_script(
        'urbantaxi-slick',
        $plugin_url . 'assets/lib/slick.min.js',
        ['jquery'],
        '1.8.0',
        true
    );

    wp_register_style(
        'urbantaxi-slick',
        $plugin_url . 'assets/lib/slick.css',
        [],
        '1.8.0'
    );

    // ===== HEADING STYLE =====
    wp_register_script(
        'urbantaxi-heading-style-script',
        $plugin_url . 'assets/js/heading-style-preview.js',
        ['jquery'],
        $version,
        true
    );

    // ===== BOOKING SLIDER =====
    wp_register_script(
        'urbantaxi-booking-slider-script',
        $plugin_url . 'assets/js/urban-taxi-cab-booking-slider.js',
        ['jquery', 'urbantaxi-swiper'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-booking-slider-style',
        $plugin_url . 'assets/css/urban-taxi-cab-booking-slider.css',
        [],
        $version
    );

    // ===== CAB FILTER =====
    wp_register_script(
        'urbantaxi-cab-filter-script',
        $plugin_url . 'assets/js/urban-taxi-cab-filter-widget.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-cab-filter-style',
        $plugin_url . 'assets/css/urban-taxi-cab-filter-widget.css',
        [],
        $version
    );

    // ===== CLIENT TESTIMONIAL =====
    wp_register_script(
        'urbantaxi-client-testimonial-script',
        $plugin_url . 'assets/js/urban-taxi-client-testimonial.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-client-testimonial-style',
        $plugin_url . 'assets/css/urban-taxi-client-testimonial.css',
        [],
        $version
    );

    // ===== BOOK SEAT =====
    wp_register_script(
        'urbantaxi-book-seat-script',
        $plugin_url . 'assets/js/book-seat-widget.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-book-seat-style',
        $plugin_url . 'assets/css/book-seat-widget.css',
        [],
        $version
    );

    // ===== OUR BLOG =====
    wp_register_script(
        'urbantaxi-our-blog-script',
        $plugin_url . 'assets/js/our-blog-widget.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_script(
        'urbantaxi-our-blog-grid-script',
        $plugin_url . 'assets/js/our-blog-widget-grid.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-our-blog-style',
        $plugin_url . 'assets/css/our-blog-widget.css',
        [],
        $version
    );

    // ===== SERVICE CARDS =====
    wp_register_script(
        'urbantaxi-service-cards-script',
        $plugin_url . 'assets/js/urbantaxi-service-cards-widget.js',
        ['jquery', 'urbantaxi-slick'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-service-cards-style',
        $plugin_url . 'assets/css/urbantaxi-service-cards-widget.css',
        ['urbantaxi-font-awesome', 'urbantaxi-slick'],
        $version
    );

    // ===== SMART ANIMATIONS =====
    wp_register_script(
        'urbantaxi-smart-animations-script',
        $plugin_url . 'assets/js/animations.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-smart-animations-style',
        $plugin_url . 'assets/css/animations.css',
        [],
        $version
    );
    wp_register_style(
        'urbantaxi-animate-css',
        $plugin_url . 'assets/css/animate.min.css',
        [],
        $version
    );
    wp_register_style(
        'urbantaxi-vivify-css',
        $plugin_url . 'assets/css/vivify.min.css',
        [],
        $version
    );

    // ===== STICKY MISSION =====
    wp_register_script(
        'urbantaxi-sticky-mission-script',
        $plugin_url . 'assets/js/script.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-sticky-mission-style',
        $plugin_url . 'assets/css/sticky-mission.css',
        [],
        $version
    );

    // ===== TAXONOMY =====
    wp_register_style(
        'urbantaxi-taxonomy-style',
        // taxonomy widget has no CSS in this plugin
        // $plugin_url . 'assets/css/taxonomy/style.css'
        [],
        $version
    );

    // ===== TEAM =====
    wp_register_script(
        'urbantaxi-team-script',
        $plugin_url . 'assets/js/team-carousel.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-team-style',
        $plugin_url . 'assets/css/team-carousel.css',
        [],
        $version
    );

    // ===== TIMELINE =====
    wp_register_script(
        'urbantaxi-timeline-script',
        $plugin_url . 'assets/js/timeline.js',
        ['jquery'],
        $version,
        true
    );
    wp_register_style(
        'urbantaxi-timeline-style',
        $plugin_url . 'assets/css/timeline.css',
        [],
        $version
    );
}

add_action( 'wp_enqueue_scripts', 'urbantaxi_core_register_assets' );
