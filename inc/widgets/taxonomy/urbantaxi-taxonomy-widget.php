<?php
/**
 * UrbanTaxi taxonomy and recent posts sidebar widgets.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('URBANTAXI_TAXONOMY_WIDGET_VERSION', '1.0.0');
define('URBANTAXI_TAXONOMY_WIDGET_PATH', plugin_dir_path(__FILE__));
define('URBANTAXI_TAXONOMY_WIDGET_URL', plugin_dir_url(__FILE__));

/**
 * Register and load the widgets
 */
function urbantaxi_load_taxonomy_widget() {
    register_widget('UrbanTaxi_Taxonomy_Widget');
    register_widget('UrbanTaxi_Recent_Posts_Widget');
}
add_action('widgets_init', 'urbantaxi_load_taxonomy_widget');

/**
 * Enqueue frontend styles
 */
function urbantaxi_taxonomy_widget_enqueue_styles() {
    wp_enqueue_style(
        'urbantaxi-taxonomy-widget',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/taxonomy-widget.css',
        array(),
        URBANTAXI_TAXONOMY_WIDGET_VERSION
    );
}
add_action('wp_enqueue_scripts', 'urbantaxi_taxonomy_widget_enqueue_styles');

/**
 * UrbanTaxi Taxonomy Widget Class
 */
class UrbanTaxi_Taxonomy_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'urbantaxi_taxonomy_widget',
            __('UrbanTaxi - Taxonomy Categories', 'urbantaxi-taxonomy-widget'),
            array(
                'description' => __('Display taxonomy terms with custom styling', 'urbantaxi-taxonomy-widget'),
                'classname' => 'urbantaxi-taxonomy-widget',
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Taxi Category', 'urbantaxi-taxonomy-widget');
        $taxonomy = !empty($instance['taxonomy']) ? $instance['taxonomy'] : 'category';
        $highlighted_term = !empty($instance['highlighted_term']) ? $instance['highlighted_term'] : '';
        $show_count = !empty($instance['show_count']) ? true : false;
        $hide_empty = !empty($instance['hide_empty']) ? true : false;

        echo wp_kses_post($args['before_widget']);

        // Get taxonomy terms
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => $hide_empty,
            'orderby' => 'name',
            'order' => 'ASC',
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            ?>
            <div class="urbantaxi-taxonomy-widget-container">
                <h4 class="urbantaxi-widget-title"><?php echo esc_html($title); ?></h4>
                <div class="urbantaxi-taxonomy-list">
                    <?php foreach ($terms as $term) : 
                        $term_link = get_term_link($term);
                        $is_highlighted = ($highlighted_term == $term->term_id);
                        $item_class = $is_highlighted ? 'urbantaxi-taxonomy-item highlighted' : 'urbantaxi-taxonomy-item';
                    ?>
                        <a href="<?php echo esc_url($term_link); ?>" class="<?php echo esc_attr($item_class); ?>">
                            <span class="urbantaxi-term-name">
                                <?php echo esc_html($term->name); ?>
                                <?php if ($show_count) : ?>
                                    <span class="urbantaxi-term-count">(<?php echo absint($term->count); ?>)</span>
                                <?php endif; ?>
                            </span>
                            <span class="urbantaxi-arrow">
                                <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_875_2796)">
                                    <path d="M19.1355 9.76805L0 9.79208V8.23507L19.3251 8.21059L11.7616 1.14795L12.79 0L22.32 8.89874L12.5934 18L11.565 16.8516L19.1355 9.76805Z" fill="#222121"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_875_2796">
                                    <rect width="22.32" height="18" fill="white"/>
                                    </clipPath>
                                    </defs>
                                </svg>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }

        echo wp_kses_post($args['after_widget']);
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Taxi Category', 'urbantaxi-taxonomy-widget');
        $taxonomy = !empty($instance['taxonomy']) ? $instance['taxonomy'] : 'category';
        $highlighted_term = !empty($instance['highlighted_term']) ? $instance['highlighted_term'] : '';
        $show_count = !empty($instance['show_count']) ? true : false;
        $hide_empty = !empty($instance['hide_empty']) ? true : false;

        // Get all public taxonomies
        $taxonomies = get_taxonomies(array('public' => true), 'objects');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'urbantaxi-taxonomy-widget'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>">
                <?php esc_html_e('Select Taxonomy:', 'urbantaxi-taxonomy-widget'); ?>
            </label>
            <select class="widefat urbantaxi-taxonomy-select" 
                    id="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('taxonomy')); ?>"
                    data-widget-id="<?php echo esc_attr($this->id); ?>">
                <?php foreach ($taxonomies as $tax) : ?>
                    <option value="<?php echo esc_attr($tax->name); ?>" 
                            <?php selected($taxonomy, $tax->name); ?>>
                        <?php echo esc_html($tax->label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('highlighted_term')); ?>">
                <?php esc_html_e('Highlighted Term:', 'urbantaxi-taxonomy-widget'); ?>
            </label>
            <select class="widefat urbantaxi-term-select" 
                    id="<?php echo esc_attr($this->get_field_id('highlighted_term')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('highlighted_term')); ?>">
                <option value=""><?php esc_html_e('None', 'urbantaxi-taxonomy-widget'); ?></option>
                <?php
                $terms = get_terms(array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false,
                ));
                if (!empty($terms) && !is_wp_error($terms)) :
                    foreach ($terms as $term) : ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>" 
                                <?php selected($highlighted_term, $term->term_id); ?>>
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endforeach;
                endif;
                ?>
            </select>
        </p>

        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked($show_count); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>">
                <?php esc_html_e('Show post count', 'urbantaxi-taxonomy-widget'); ?>
            </label>
        </p>

        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked($hide_empty); ?> 
                   id="<?php echo esc_attr($this->get_field_id('hide_empty')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('hide_empty')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('hide_empty')); ?>">
                <?php esc_html_e('Hide empty terms', 'urbantaxi-taxonomy-widget'); ?>
            </label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['taxonomy'] = (!empty($new_instance['taxonomy'])) ? sanitize_text_field($new_instance['taxonomy']) : 'category';
        $instance['highlighted_term'] = (!empty($new_instance['highlighted_term'])) ? sanitize_text_field($new_instance['highlighted_term']) : '';
        $instance['show_count'] = (!empty($new_instance['show_count'])) ? 1 : 0;
        $instance['hide_empty'] = (!empty($new_instance['hide_empty'])) ? 1 : 0;
        
        return $instance;
    }
}

/**
 * UrbanTaxi Recent Posts Widget Class
 */
class UrbanTaxi_Recent_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'urbantaxi_recent_posts_widget',
            __('UrbanTaxi - Recent Posts', 'urbantaxi-taxonomy-widget'),
            array(
                'description' => __('Display recent posts with thumbnails, date, and comment count', 'urbantaxi-taxonomy-widget'),
                'classname' => 'urbantaxi-recent-posts-widget',
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Post', 'urbantaxi-taxonomy-widget');
        $number_posts = !empty($instance['number_posts']) ? absint($instance['number_posts']) : 5;
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';
        $show_thumbnail = isset($instance['show_thumbnail']) ? (bool) $instance['show_thumbnail'] : true;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $show_comments = isset($instance['show_comments']) ? (bool) $instance['show_comments'] : true;

        echo wp_kses_post($args['before_widget']);

        // Query recent posts
        $recent_posts = new WP_Query(array(
            'post_type' => $post_type,
            'posts_per_page' => $number_posts,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
        ));

        if ($recent_posts->have_posts()) :
            ?>
            <div class="urbantaxi-recent-posts-container">
                <h4 class="urbantaxi-recent-posts-title"><?php echo esc_html($title); ?></h4>
                <div class="urbantaxi-recent-posts-list">
                    <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                        <div class="urbantaxi-recent-post-item">
                            <?php if ($show_thumbnail && has_post_thumbnail()) : ?>
                                <div class="urbantaxi-post-thumbnail">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <?php $alt = get_the_title(); // fallback alt

                                        if (has_post_thumbnail()) {

                                            $thumb_id = get_post_thumbnail_id();

                                            $alt_text = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);

                                            if (!$alt_text) {
                                                $alt_text = get_the_title();
                                            }

                                            echo get_the_post_thumbnail(
                                                get_the_ID(),
                                                'thumbnail',
                                                array(
                                                    'class' => 'urbantaxi-thumb-image',
                                                    'alt'   => esc_attr($alt_text)
                                                )
                                            );
                                        } ?>

                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="urbantaxi-post-content align-self-center">
                                <h3 class="urbantaxi-post-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                </h3>
                                
                                <div class="urbantaxi-post-meta">
                                    <?php if ($show_date) : ?>
                                        <span class="urbantaxi-post-date">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_875_2902)">
                                                <path d="M9.20898 2.57308C8.9869 2.57308 8.80273 2.38891 8.80273 2.16683V1.0835C8.80273 0.861413 8.9869 0.677246 9.20898 0.677246C9.43107 0.677246 9.61523 0.861413 9.61523 1.0835V2.16683C9.61523 2.38891 9.43107 2.57308 9.20898 2.57308Z" fill="#FEBB1B"/>
                                                <path d="M3.79102 2.57308C3.56893 2.57308 3.38477 2.38891 3.38477 2.16683V1.0835C3.38477 0.861413 3.56893 0.677246 3.79102 0.677246C4.0131 0.677246 4.19727 0.861413 4.19727 1.0835V2.16683C4.19727 2.38891 4.0131 2.57308 3.79102 2.57308Z" fill="#FEBB1B"/>
                                                <path d="M12.3236 4.46859V3.7915C12.3236 2.67025 11.4136 1.76025 10.2923 1.76025H2.70898C1.58773 1.76025 0.677734 2.67025 0.677734 3.7915V4.46859H12.3236Z" fill="#FEBB1B"/>
                                                <path d="M0.677734 5.28125V10.2917C0.677734 11.4129 1.58773 12.3229 2.70898 12.3229H10.2923C11.4136 12.3229 12.3236 11.4129 12.3236 10.2917V5.28125H0.677734ZM3.79232 10.2917C3.4944 10.2917 3.25065 10.0479 3.25065 9.75C3.25065 9.45208 3.4944 9.20833 3.79232 9.20833C4.09023 9.20833 4.33398 9.45208 4.33398 9.75C4.33398 10.0479 4.09023 10.2917 3.79232 10.2917ZM3.79232 7.58333C3.4944 7.58333 3.25065 7.33958 3.25065 7.04167C3.25065 6.74375 3.4944 6.5 3.79232 6.5C4.09023 6.5 4.33398 6.74375 4.33398 7.04167C4.33398 7.33958 4.09023 7.58333 3.79232 7.58333ZM6.50065 10.2917C6.20273 10.2917 5.95898 10.0479 5.95898 9.75C5.95898 9.45208 6.20273 9.20833 6.50065 9.20833C6.79857 9.20833 7.04232 9.45208 7.04232 9.75C7.04232 10.0479 6.79857 10.2917 6.50065 10.2917ZM6.50065 7.58333C6.20273 7.58333 5.95898 7.33958 5.95898 7.04167C5.95898 6.74375 6.20273 6.5 6.50065 6.5C6.79857 6.5 7.04232 6.74375 7.04232 7.04167C7.04232 7.33958 6.79857 7.58333 6.50065 7.58333ZM9.20898 10.2917C8.91107 10.2917 8.66732 10.0479 8.66732 9.75C8.66732 9.45208 8.91107 9.20833 9.20898 9.20833C9.5069 9.20833 9.75065 9.45208 9.75065 9.75C9.75065 10.0479 9.5069 10.2917 9.20898 10.2917ZM9.20898 7.58333C8.91107 7.58333 8.66732 7.33958 8.66732 7.04167C8.66732 6.74375 8.91107 6.5 9.20898 6.5C9.5069 6.5 9.75065 6.74375 9.75065 7.04167C9.75065 7.33958 9.5069 7.58333 9.20898 7.58333Z" fill="#FEBB1B"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0_875_2902">
                                                <rect width="13" height="13" fill="white"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                            <?php echo esc_html(get_the_date('d/m/Y')); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($show_comments) : ?>
                                        <span class="urbantaxi-post-comments">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 6.16322C0 2.92136 2.59082 0 6.16628 0C9.66173 0 12.3079 2.8659 12.3079 6.14472C12.3079 9.94743 9.20634 12.3079 6.15397 12.3079C5.14472 12.3079 4.0247 12.0368 3.12622 11.5067C2.81236 11.3157 2.54774 11.1739 2.20927 11.2848L0.966173 11.6546C0.652321 11.7533 0.369238 11.5067 0.461548 11.1739L0.873864 9.79334C0.941557 9.60229 0.929249 9.3989 0.830786 9.23866C0.301544 8.26487 0 7.19863 0 6.16322ZM3.30529 6.95864C2.87451 6.95864 2.51758 6.60117 2.51758 6.16975C2.51758 5.73216 2.86835 5.38086 3.30529 5.38086C3.74222 5.38086 4.09299 5.73216 4.09299 6.16975C4.09299 6.60117 3.74222 6.95248 3.30529 6.95864ZM5.35547 6.16367C5.35547 6.60126 5.70624 6.95256 6.14318 6.95872C6.58011 6.95872 6.93088 6.60126 6.93088 6.16983C6.93088 5.73224 6.58011 5.38094 6.14318 5.38094C5.7124 5.37477 5.35547 5.73224 5.35547 6.16367ZM8.19336 6.16975C8.19336 6.60117 8.54414 6.95864 8.98107 6.95864C9.418 6.95864 9.76878 6.60117 9.76878 6.16975C9.76878 5.73216 9.418 5.38086 8.98107 5.38086C8.54414 5.38086 8.19336 5.73216 8.19336 6.16975Z" fill="#FEBB1B"/>
                                            </svg>
                                            <?php 
                                                $comments_count = get_comments_number();
                                                printf(
                                                    /* translators: %s: number of comments */
                                                    esc_html( _n( '%s Comment', '%s Comments', $comments_count, 'urbantaxi-taxonomy-widget' ) ),
                                                    esc_html( number_format_i18n( $comments_count ) )
                                                );                           
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php
            wp_reset_postdata();
        endif;

        echo wp_kses_post($args['after_widget']);
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Post', 'urbantaxi-taxonomy-widget');
        $number_posts = !empty($instance['number_posts']) ? absint($instance['number_posts']) : 5;
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : 'post';
        $show_thumbnail = isset($instance['show_thumbnail']) ? (bool) $instance['show_thumbnail'] : true;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $show_comments = isset($instance['show_comments']) ? (bool) $instance['show_comments'] : true;

        // Get all public post types
        $post_types = get_post_types(array('public' => true), 'objects');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'urbantaxi-taxonomy-widget'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number_posts')); ?>">
                <?php esc_html_e('Number of posts:', 'urbantaxi-taxonomy-widget'); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr($this->get_field_id('number_posts')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('number_posts')); ?>" 
                   type="number" 
                   step="1" 
                   min="1" 
                   max="20"
                   value="<?php echo esc_attr($number_posts); ?>" 
                   size="3">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>">
                <?php esc_html_e('Post Type:', 'urbantaxi-taxonomy-widget'); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('post_type')); ?>">
                <?php foreach ($post_types as $pt) : ?>
                    <option value="<?php echo esc_attr($pt->name); ?>" 
                            <?php selected($post_type, $pt->name); ?>>
                        <?php echo esc_html($pt->label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked($show_thumbnail); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_thumbnail')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_thumbnail')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_thumbnail')); ?>">
                <?php esc_html_e('Show thumbnail', 'urbantaxi-taxonomy-widget'); ?>
            </label>
        </p>

        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked($show_date); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>">
                <?php esc_html_e('Show date', 'urbantaxi-taxonomy-widget'); ?>
            </label>
        </p>

        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked($show_comments); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_comments')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_comments')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_comments')); ?>">
                <?php esc_html_e('Show comments count', 'urbantaxi-taxonomy-widget'); ?>
            </label>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number_posts'] = (!empty($new_instance['number_posts'])) ? absint($new_instance['number_posts']) : 5;
        $instance['post_type'] = (!empty($new_instance['post_type'])) ? sanitize_text_field($new_instance['post_type']) : 'post';
        $instance['show_thumbnail'] = (!empty($new_instance['show_thumbnail'])) ? 1 : 0;
        $instance['show_date'] = (!empty($new_instance['show_date'])) ? 1 : 0;
        $instance['show_comments'] = (!empty($new_instance['show_comments'])) ? 1 : 0;
        
        return $instance;
    }
}
