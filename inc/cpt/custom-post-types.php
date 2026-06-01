<?php
/**
 * UrbanTaxi Custom Post Types
 * Registers all custom post types, taxonomies and meta boxes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// Register Custom Taxonomy
add_action( 'init', function () {

    register_taxonomy( 'mptbm_category', [ 'mptbm_rent' ], [
        'labels' => [
            'singular_name' => __( 'Category', 'urbantaxi' ),
        ],
        'public' => true,
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_ui' => true,
        'show_in_rest' => true,
    ]);

}, 0 );

add_action( 'init', 'cpcp_create_post_services' );
function cpcp_create_post_services() {
    register_post_type( 'service',array(
        'labels' => array(
            'name' => __( 'Service','urbantaxi-custom-post-type' ),
            'singular_name' => __( 'Service','urbantaxi-custom-post-type' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-video-alt',
        'public' => true,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'page-attributes',
            'comments'
        )
    ));
}

// location
add_action('init', function () {

	if (!taxonomy_exists('locations')) {

		register_taxonomy('locations', ['mptbm_rent'], [
			'label' => 'Locations',
			'public' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite' => ['slug' => 'locations'],
		]);

	}

	global $wp_taxonomies;

	if (isset($wp_taxonomies['locations'])) {

		$wp_taxonomies['locations']->meta_box_cb = function ($post, $box) {

			        $taxonomy = $box['args']['taxonomy'];

			        $terms = get_terms([
			        	'taxonomy' => $taxonomy,
			        	'hide_empty' => false,
			        ]);

			        $selected = wp_get_post_terms($post->ID, $taxonomy, ['fields' => 'ids']);
			        $selected = !empty($selected) ? $selected[0] : '';

			        echo '<select name="custom_location" style="width:100%;">';
			        echo '<option value="">Select Location</option>';

			        foreach ($terms as $term) {
				        printf(
				        	'<option value="%d" %s>%s</option>',
				        	$term->term_id,
				        	selected($selected, $term->term_id, false),
				        	esc_html($term->name)
				        );
			        }

			        echo '</select>';
		        }
		        	;

	        }

        }, 99);

add_action('save_post', function ($post_id) {

	if (get_post_type($post_id) !== 'mptbm_rent')
		return;
	if (isset($_POST['custom_location'])) {

		$term_id = intval($_POST['custom_location']);

		if ($term_id > 0) {
			wp_set_object_terms($post_id, [$term_id], 'locations', false);
		}
		else {
			wp_set_object_terms($post_id, [], 'locations', false);
		}
	}

}, 20);


// ---------------- service---------------
function service_custom_meta_service() {

    add_meta_box( 'bn_meta', __( 'service Meta', 'urbantaxi-custom-post-type' ), 'service_meta_callback_service', 'service', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
    add_action('admin_menu', 'service_custom_meta_service');
}

function service_meta_callback_service( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $service_meta_icon = get_post_meta( $post->ID, 'service_meta_icon', true );
    $service_meta_button_text = get_post_meta( $post->ID, 'service_meta_button_text', true );
    $service_meta_button_url = get_post_meta( $post->ID, 'service_meta_button_url', true ); 
    $service_meta_button_icon_class = get_post_meta( $post->ID, 'service_meta_button_icon_class', true ); ?>
    
    <div id="recentwork_stuff">
        <table id="list-table">
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr class="service-image">
                    <th class="left">
                        <?php esc_html_e( 'Meta Icon Image', 'urbantaxi-custom-post-type' )?></th>
                    <td>
                        <input type="text" name="service_meta_icon" value="<?php echo esc_attr( $service_meta_icon ); ?>" readonly>
                        <input type="button" class="button button-primary" value="Upload Image" id="service_meta_icon"><br>
                    </td>
                </tr>   
                
                <tr class="service-button-text">
                    <th class="left">
                        <?php esc_html_e( 'Meta Button Text', 'urbantaxi-custom-post-type' )?>
                    </th>
                    <td class="left">
                        <input type="text" name="service_meta_button_text" id="service_meta_button_text" value="<?php echo esc_html($service_meta_button_text); ?>" />
                    </td>
                </tr>  


                <tr class="service-icon-class">
                    <th class="left">
                        <?php esc_html_e( 'Meta Button Icon Class', 'urbantaxi-custom-post-type' )?></th>
                    <td class="left">
                        <input type="text" name="service_meta_button_icon_class" id="service_meta_button_icon_class" value="<?php echo esc_attr( $service_meta_button_icon_class ); ?>" placeholder="e.g. fa fa-arrow-right" />
                        <p class="description"><?php esc_html_e( 'Enter Font Awesome icon classes for the button.', 'urbantaxi-custom-post-type' ); ?></p>
                    </td>
                </tr>

                <tr class="service-button-url">
                    <th class="left">
                        <?php esc_html_e( 'Meta Button URL', 'urbantaxi-custom-post-type' )?>
                    </th>
                    <td class="left">
                        <input type="text" name="service_meta_button_url" id="service_meta_button_url" value="<?php echo esc_html($service_meta_button_url); ?>" />
                    </td>
                </tr>  

            </tbody>
        </table>
    </div>
<?php }

function service_meta_save_service( $post_id ) {

    if (!isset($_POST['bn_nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bn_nonce'] ) ), basename(__FILE__))) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if( isset( $_POST[ 'service_meta_icon' ] ) ) {
        update_post_meta( $post_id, 'service_meta_icon', esc_html( sanitize_text_field( wp_unslash( $_POST[ 'service_meta_icon' ] ) ) ) );
    } 
    if( isset( $_POST[ 'service_meta_button_text' ] ) ) {
        update_post_meta( $post_id, 'service_meta_button_text', esc_html( sanitize_text_field( wp_unslash( $_POST[ 'service_meta_button_text' ] ) ) ) );
    } 
    if( isset( $_POST[ 'service_meta_button_icon_class' ] ) ) {
        update_post_meta( $post_id, 'service_meta_button_icon_class', sanitize_text_field( wp_unslash( $_POST[ 'service_meta_button_icon_class' ] ) ) );
    }
    if( isset( $_POST[ 'service_meta_button_url' ] ) ) {
        update_post_meta( $post_id, 'service_meta_button_url', esc_html( sanitize_text_field( wp_unslash( $_POST[ 'service_meta_button_url' ] ) ) ) );
    } 
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

}
add_action( 'save_post', 'service_meta_save_service' );


add_action( 'init', 'cpcp_create_post_drivers' );
function cpcp_create_post_drivers() {
    register_post_type( 'driver',array(
        'labels' => array(
            'name' => __( 'Driver','urbantaxi-custom-post-type' ),
            'singular_name' => __( 'Driver','urbantaxi-custom-post-type' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-video-alt',
        'public' => true,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'page-attributes',
            'comments'
        )
    ));
}
// ---------------- driver---------------
function driver_custom_meta_driver() {

    add_meta_box( 'bn_meta', __( 'driver Meta', 'urbantaxi-custom-post-type' ), 'driver_meta_callback_driver', 'driver', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
    add_action('admin_menu', 'driver_custom_meta_driver');
}

function driver_meta_callback_driver( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $driver_designation_text = get_post_meta( $post->ID, 'driver_designation_text', true ); ?>
    
    <div id="recentwork_stuff">
        <table id="list-table">
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr id="project-desg">
                    <td class="left">
                        <?php esc_html_e( 'Driver Designation Text', 'urbantaxi-custom-post-type' )?>
                    </td>
                    <td class="left">
                        <input type="text" name="driver_designation_text" id="driver_designation_text" value="<?php echo esc_html($driver_designation_text); ?>" />
                    </td>
                </tr>            
            </tbody>
        </table>
    </div>
<?php }

function driver_meta_save_driver( $post_id ) {

    if (!isset($_POST['bn_nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bn_nonce'] ) ), basename(__FILE__))) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if( isset( $_POST[ 'driver_designation_text' ] ) ) {
        update_post_meta( $post_id, 'driver_designation_text', esc_html( sanitize_text_field( wp_unslash( $_POST[ 'driver_designation_text' ] ) ) ) );
    } 
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

}
add_action( 'save_post', 'driver_meta_save_driver' );


// ---------------- blog ---------------
function blog_custom_meta_service() {
	add_meta_box( 'urbantaxi-custom-post-type-posttype-post-meta', __( 'Enter Details', 'urbantaxi-custom-post-type' ), 'custom_post_meta_callback', 'post', 'normal', 'high' );
}

// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'blog_custom_meta_service');
}
/* Adds a meta box for custom post */
function custom_post_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'post_meta_nonce' );
    //$bn_stored_meta = get_post_meta( $post->ID );
    $post_address = get_post_meta( $post->ID, 'post_address', true );
    //facebook details

    $post_instagram_icon = get_post_meta( get_the_ID(), 'post_instagram_icon', true );
    $post_twitter_icon = get_post_meta( get_the_ID(), 'post_twitter_icon', true );
    $post_facebook_icon = get_post_meta( get_the_ID(), 'post_facebook_icon', true );
    $post_linkedin_icon = get_post_meta( get_the_ID(), 'post_linkedin_icon', true );

    $post_instagram = get_post_meta( get_the_ID(), 'post_instagram', true );
    $post_twitter = get_post_meta( get_the_ID(), 'post_twitter', true );
    $post_facebook = get_post_meta( get_the_ID(), 'post_facebook', true );
    $post_linkedin = get_post_meta( get_the_ID(), 'post_linkedin', true );
	?>
	<div id="testimonials_custom_stuff">
		<table id="list">
			<tbody id="the-list" data-wp-lists="list:meta">

                <tr id="post-desg">
                    <td class="left">
                        <?php esc_html_e( 'Instagram URL', 'urbantaxi-custom-post-type' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="post_instagram" id="post_instagram" value="<?php echo esc_html($post_instagram); ?>" />
                    </td>
                </tr>
                <tr id="post-desg">
                    <td class="left">
                        <?php esc_html_e( 'Twitter URL', 'urbantaxi-custom-post-type' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="post_twitter" id="post_twitter" value="<?php echo esc_html($post_twitter); ?>" />
                    </td>
                </tr>
                <tr id="post-desg">
                    <td class="left">
                        <?php esc_html_e( 'Facebook URL', 'urbantaxi-custom-post-type' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="post_facebook" id="post_facebook" value="<?php echo esc_html($post_facebook); ?>" />
                    </td>
                </tr>
                <tr id="post-desg">
                    <td class="left">
                        <?php esc_html_e( 'LinkedIn URL', 'urbantaxi-custom-post-type' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="post_linkedin" id="post_linkedin" value="<?php echo esc_html($post_linkedin); ?>" />
                    </td>
                </tr>
			</tbody>
		</table>
	</div>
	<?php
}

/* Saves the custom meta input */
function custom_post_posttype_bn_meta_save( $post_id ) {

    if (
        ! isset( $_POST['post_meta_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['post_meta_nonce'] ) ), basename( __FILE__ ) )
    ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Address
    if ( isset( $_POST['post_address'] ) ) {
        update_post_meta(
            $post_id,
            'post_address',
            sanitize_text_field( wp_unslash( $_POST['post_address'] ) )
        );
    }

    // Social URLs (use esc_url_raw instead of esc_html)
    if ( isset( $_POST['post_instagram'] ) ) {
        update_post_meta(
            $post_id,
            'post_instagram',
            esc_url_raw( wp_unslash( $_POST['post_instagram'] ) )
        );
    }

    if ( isset( $_POST['post_twitter'] ) ) {
        update_post_meta(
            $post_id,
            'post_twitter',
            esc_url_raw( wp_unslash( $_POST['post_twitter'] ) )
        );
    }

    if ( isset( $_POST['post_facebook'] ) ) {
        update_post_meta(
            $post_id,
            'post_facebook',
            esc_url_raw( wp_unslash( $_POST['post_facebook'] ) )
        );
    }

    if ( isset( $_POST['post_linkedin'] ) ) {
        update_post_meta(
            $post_id,
            'post_linkedin',
            esc_url_raw( wp_unslash( $_POST['post_linkedin'] ) )
        );
    }

    // Icons (likely URLs too)
    if ( isset( $_POST['post_instagram_icon'] ) ) {
        update_post_meta(
            $post_id,
            'post_instagram_icon',
            esc_url_raw( wp_unslash( $_POST['post_instagram_icon'] ) )
        );
    }

    if ( isset( $_POST['post_twitter_icon'] ) ) {
        update_post_meta(
            $post_id,
            'post_twitter_icon',
            esc_url_raw( wp_unslash( $_POST['post_twitter_icon'] ) )
        );
    }

    if ( isset( $_POST['post_facebook_icon'] ) ) {
        update_post_meta(
            $post_id,
            'post_facebook_icon',
            esc_url_raw( wp_unslash( $_POST['post_facebook_icon'] ) )
        );
    }

    if ( isset( $_POST['post_linkedin_icon'] ) ) {
        update_post_meta(
            $post_id,
            'post_linkedin_icon',
            esc_url_raw( wp_unslash( $_POST['post_linkedin_icon'] ) )
        );
    }
}

add_action( 'save_post', 'custom_post_posttype_bn_meta_save' );
