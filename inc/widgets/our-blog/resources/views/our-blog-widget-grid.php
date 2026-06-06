<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="urbantaxi-blog-slider-wrapper urbantaxi-grid-wrapper"
    id="urbantaxi-blog-<?php echo esc_attr($widget_id); ?>"
    data-enable-grid="yes"
    data-columns="<?php echo esc_attr($columns); ?>"
    data-rows="<?php echo esc_attr($rows); ?>"
    data-per-page="<?php echo esc_attr($per_page); ?>"
    data-enable-pagination="<?php echo esc_attr($settings['grid_enable_custom_pagination']); ?>"
    data-pagination-type="<?php echo esc_attr($settings['grid_custom_pagination_type']); ?>"
    data-prev-text="<?php echo esc_attr($settings['grid_prev_text']); ?>"
    data-next-text="<?php echo esc_attr($settings['grid_next_text']); ?>"
    data-col-320="<?php echo esc_attr($settings['grid_col_320']); ?>"
    data-col-576="<?php echo esc_attr($settings['grid_col_576']); ?>"
    data-col-768="<?php echo esc_attr($settings['grid_col_768']); ?>"
    data-col-992="<?php echo esc_attr($settings['grid_col_992']); ?>"
    data-col-1025="<?php echo esc_attr($settings['grid_col_1025']); ?>"
    data-col-1200="<?php echo esc_attr($settings['grid_col_1200']); ?>">

    <div class="urbantaxi-grid-container">

        <?php 
        $urbantaxi_our_blog_widget_index = 0;
        while ($query->have_posts()) : 
            $query->the_post();
            $urbantaxi_our_blog_widget_author_id = get_the_author_meta('ID');
            $urbantaxi_our_blog_widget_categories = get_the_category();

            $urbantaxi_our_blog_widget_author_image = !empty($settings['custom_author_image']['url'])
                ? $settings['custom_author_image']['url']
                : get_avatar_url($urbantaxi_our_blog_widget_author_id);
        ?>

            <div class="urbantaxi-grid-item">

                <div class="urbantaxi-blog-card">

                    <div class="urbantaxi-blog-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>

                    <?php if (!empty($urbantaxi_our_blog_widget_categories)) : ?>
                        <div class="urbantaxi-blog-categories">
                            <?php
                            $urbantaxi_our_blog_widget_random_index = array_rand($urbantaxi_our_blog_widget_categories);
                            $urbantaxi_our_blog_widget_random_category = $urbantaxi_our_blog_widget_categories[$urbantaxi_our_blog_widget_random_index];
                            echo esc_html($urbantaxi_our_blog_widget_random_category->name);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="urbantaxi-blog-content pt-3">
                        <h3 class="urbantaxi-blog-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <?php if ($settings['show_excerpt'] === 'yes') : ?>
                            <div class="urbantaxi-blog-excerpt">
                                <?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="urbantaxi-blog-meta d-flex" style="justify-content:space-between;">

                            <?php if ($settings['show_author'] === 'yes') : ?>
                                <div class="urbantaxi-meta-author" style="width:fit-content;border-radius:50px;padding:5px 15px 5px 5px;">
                                    <span>
                                        <img src="<?php echo esc_url($urbantaxi_our_blog_widget_author_image); ?>" class="urbantaxi-blog-author-img" alt="">
                                        <a class="urbantaxi-meta-author-link"
                                            href="<?php echo esc_url(get_author_posts_url($urbantaxi_our_blog_widget_author_id)); ?>">
                                            <?php echo esc_html(get_the_author_meta('display_name', $urbantaxi_our_blog_widget_author_id)); ?>
                                        </a>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if ($settings['show_date'] === 'yes') : ?>
                                <span class="urbantaxi-meta-date align-self-center">
                                    <span class="urbantaxi-meta-icon">
                                        <?php \Elementor\Icons_Manager::render_icon($settings['date_icon'], ['aria-hidden' => 'true']); ?>
                                    </span>
                                    <span>
                                        <a href="<?php echo esc_url(get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d'))); ?>">
                                            <?php echo esc_html(get_the_date('d/m/Y')); ?>
                                        </a>
                                    </span>
                                </span>
                            <?php endif; ?>

                            <?php if ($settings['show_comments'] === 'yes') : ?>
                                <span class="urbantaxi-meta-comments align-self-center">
                                    <span class="urbantaxi-meta-icon">
                                        <?php \Elementor\Icons_Manager::render_icon($settings['comments_icon'], ['aria-hidden' => 'true']); ?>
                                    </span>
                                    <span>
                                        <?php comments_number('0 Comment','1 Comment','% Comments'); ?>
                                    </span>
                                </span>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </div>

        <?php endwhile; ?>

    </div>

    <?php if ($settings['grid_enable_custom_pagination'] === 'yes') : ?>
        <div class="urbantaxi-custom-pagination mt-5"></div>
    <?php endif; ?>

</div>
