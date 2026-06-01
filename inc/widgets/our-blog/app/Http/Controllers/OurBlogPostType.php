<?php
namespace UrbanTaxi\OurBlogWidget\Http\Controllers;

if (!defined('ABSPATH')) {
    exit;
}

class OurBlogPostType
{
    public static function register()
    {
        register_post_type('ut_our_blog_testimonial', [
            'labels' => [
                'name'          => __('Our Blog Testimonials', 'urbantaxi-our-blog-widget'),
                'singular_name' => __('Our Blog Testimonial', 'urbantaxi-our-blog-widget'),
            ],
            'public'        => true,
            'menu_icon'     => 'dashicons-testimonial',
            'supports'      => ['title', 'thumbnail'],
            'show_in_rest'  => true,
        ]);
    }
}

