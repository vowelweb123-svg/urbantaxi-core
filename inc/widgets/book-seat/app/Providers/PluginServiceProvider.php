<?php
namespace UrbanTaxi\BookSeatWidget\Providers;
use UrbanTaxi\BookSeatWidget\Http\Controllers\BookSeatWidget;
if (!defined('ABSPATH')) {
    exit;
}


class PluginServiceProvider
{
    public static function boot()
    {
        self::registerCPT();
        self::registerAssets();
        self::registerElementorWidget();
    }

    protected static function registerCPT()
    {
        // add_action('init', [TestimonialPostType::class, 'register']);
    }

    protected static function registerAssets()
    {
        add_action('init', function () {
            wp_register_style(
                'urbantaxi-book-seat-widget',
                URBANTAXI_BOOK_SEAT_WIDGET_URL . 'public/css/book-seat-widget.css',
                [],
                URBANTAXI_BOOK_SEAT_WIDGET_VERSION
            );

            wp_register_script(
                'urbantaxi-book-seat-widget',
                URBANTAXI_BOOK_SEAT_WIDGET_URL . 'public/js/book-seat-widget.js',
                [],
                URBANTAXI_BOOK_SEAT_WIDGET_VERSION,
                true
            );
        });

        add_action('wp_enqueue_scripts', function () {
            if (!wp_style_is('urbantaxi-book-seat-widget', 'enqueued')) {
                wp_enqueue_style('urbantaxi-book-seat-widget');
            }
            if (!wp_script_is('urbantaxi-book-seat-widget', 'enqueued')) {
                wp_enqueue_script('urbantaxi-book-seat-widget');
            }
        });

        add_action('elementor/editor/after_enqueue_scripts', function () {
            if (!wp_style_is('urbantaxi-book-seat-widget', 'enqueued')) {
                wp_enqueue_style('urbantaxi-book-seat-widget');
            }
            if (!wp_script_is('urbantaxi-book-seat-widget', 'enqueued')) {
                wp_enqueue_script('urbantaxi-book-seat-widget');
            }
        });

        add_action('elementor/frontend/after_enqueue_scripts', function () {
            if (!wp_style_is('urbantaxi-book-seat-widget', 'enqueued')) {
                wp_enqueue_style('urbantaxi-book-seat-widget');
            }
            if (!wp_script_is('urbantaxi-book-seat-widget', 'enqueued')) {
                wp_enqueue_script('urbantaxi-book-seat-widget');
            }
        });


        add_action('wp_enqueue_scripts', function () {

            if(!wp_style_is('swiper-css', 'enqueued')){
                // Swiper CSS
                wp_enqueue_style(
                    'swiper-css',
                    URBANTAXI_BOOK_SEAT_WIDGET_URL . 'public/css/swiper-bundle.min.css',
                    array(),
                    URBANTAXI_BOOK_SEAT_WIDGET_VERSION
                );
            }

            // Swiper JS
            if(!wp_script_is('swiper-js', 'enqueued')){
                wp_enqueue_script(
                    'swiper-js',
                    URBANTAXI_BOOK_SEAT_WIDGET_URL . 'public/js/swiper-bundle.min.js',
                    array(),
                    URBANTAXI_BOOK_SEAT_WIDGET_VERSION,
                    true
                );
            }


        });

    }


    protected static function registerElementorWidget()
    {
        add_action('elementor/widgets/register', function ($widgets_manager) {

            // Elementor safety check
            if (!did_action('elementor/loaded')) {
                return;
            }

            $widgets_manager->register(new BookSeatWidget());
        });
    }
}

