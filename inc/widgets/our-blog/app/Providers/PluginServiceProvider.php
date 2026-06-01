<?php
namespace UrbanTaxi\OurBlogWidget\Providers;

use UrbanTaxi\OurBlogWidget\Http\Controllers\OurBlogPostType;
use UrbanTaxi\OurBlogWidget\Http\Controllers\OurBlogWidget;

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
        // add_action('init', [OurBlogPostType::class, 'register']);
    }


    protected static function registerAssets()
    {
        // Register Swiper and widget scripts/styles
        add_action('wp_enqueue_scripts', function () {
            // Swiper (local assets inside the plugin)
            if (!wp_style_is('swiper-css', 'registered')) {
                wp_register_style(
                    'swiper-css',
                    URBANTAXI_OUR_BLOG_WIDGET_URL . 'public/css/swiper-bundle.min.css',
                    [],
                    URBANTAXI_OUR_BLOG_WIDGET_VERSION
                );
            }

            if (!wp_script_is('swiper-js', 'registered')) {
                wp_register_script(
                    'swiper-js',
                    URBANTAXI_OUR_BLOG_WIDGET_URL . 'public/js/swiper-bundle.min.js',
                    ['jquery'],
                    URBANTAXI_OUR_BLOG_WIDGET_VERSION,
                    true
                );
            }

            // Widget assets
            if (!wp_style_is('urbantaxi-our-blog-widget', 'registered')) {
                wp_register_style(
                    'urbantaxi-our-blog-widget',
                    URBANTAXI_OUR_BLOG_WIDGET_URL . 'public/css/our-blog-widget.css',
                    ['swiper-css'],
                    URBANTAXI_OUR_BLOG_WIDGET_VERSION
                );
            }

            if (!wp_script_is('urbantaxi-our-blog-widget', 'registered')) {
                wp_register_script(
                    'urbantaxi-our-blog-widget',
                    URBANTAXI_OUR_BLOG_WIDGET_URL . 'public/js/our-blog-widget.js',
                    ['jquery', 'swiper-js', 'elementor-frontend'],
                    URBANTAXI_OUR_BLOG_WIDGET_VERSION,
                    true
                );
            }

            if (!wp_script_is('urbantaxi-our-blog-widget-grid', 'registered')) {
                wp_register_script(
                    'urbantaxi-our-blog-widget-grid',
                    URBANTAXI_OUR_BLOG_WIDGET_URL . 'public/js/our-blog-widget-grid.js',
                    ['jquery', 'swiper-js', 'elementor-frontend'],
                    URBANTAXI_OUR_BLOG_WIDGET_VERSION,
                    true
                );
            }

            // Ensure assets are enqueued on the frontend if not already
            if (!wp_style_is('urbantaxi-our-blog-widget', 'enqueued')) {
                wp_enqueue_style('urbantaxi-our-blog-widget');
            }

            if (!wp_script_is('urbantaxi-our-blog-widget', 'enqueued')) {
                wp_enqueue_script('urbantaxi-our-blog-widget');
            }

            if (!wp_script_is('urbantaxi-our-blog-widget-grid', 'enqueued')) {
                wp_enqueue_script('urbantaxi-our-blog-widget-grid');
            }
        });

        // Enqueue scripts/styles for Elementor editor
        add_action('elementor/editor/after_enqueue_scripts', function () {
            if (!wp_style_is('urbantaxi-our-blog-widget', 'enqueued')) {
                wp_enqueue_style('urbantaxi-our-blog-widget');
            }

            if (!wp_script_is('urbantaxi-our-blog-widget', 'enqueued')) {
                wp_enqueue_script('urbantaxi-our-blog-widget');
            }

            if (!wp_script_is('urbantaxi-our-blog-widget-grid', 'enqueued')) {
                wp_enqueue_script('urbantaxi-our-blog-widget-grid');
            }
        });

        // Enqueue scripts/styles for Elementor frontend
        add_action('elementor/frontend/after_enqueue_scripts', function () {
            if (!wp_style_is('urbantaxi-our-blog-widget', 'enqueued')) {
                wp_enqueue_style('urbantaxi-our-blog-widget');
            }

            if (!wp_script_is('urbantaxi-our-blog-widget', 'enqueued')) {
                wp_enqueue_script('urbantaxi-our-blog-widget');
            }

            if (!wp_script_is('urbantaxi-our-blog-widget-grid', 'enqueued')) {
                wp_enqueue_script('urbantaxi-our-blog-widget-grid');
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

            $widgets_manager->register(new OurBlogWidget());
        });
    }
}

