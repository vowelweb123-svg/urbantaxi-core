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

// -------------------------------------------------------------------------
// Locations taxonomy — meta box, query, and save (secure, named functions)
// -------------------------------------------------------------------------

add_action( 'init', 'urbantaxi_locations_meta_box_setup', 99 );
function urbantaxi_locations_meta_box_setup() {
    if ( ! taxonomy_exists( 'locations' ) ) {
        register_taxonomy( 'locations', [ 'mptbm_rent' ], [
            'label'        => __( 'Locations', 'urbantaxi' ),
            'public'       => true,
            'show_ui'      => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => [ 'slug' => 'locations' ],
        ] );
    }
    global $wp_taxonomies;
    if ( isset( $wp_taxonomies['locations'] ) ) {
        $wp_taxonomies['locations']->meta_box_cb = 'urbantaxi_locations_meta_box_cb';
    }
}

function urbantaxi_locations_meta_box_cb( $post, $box ) {
    $taxonomy = $box['args']['taxonomy'];
    $terms    = get_terms( [
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ] );
    $selected = wp_get_post_terms( $post->ID, $taxonomy, [ 'fields' => 'ids' ] );
    $selected = ! empty( $selected ) ? $selected[0] : '';

    echo '<select name="custom_location" style="width:100%;">';
    echo '<option value="">' . esc_html__( 'Select Location', 'urbantaxi' ) . '</option>';
    foreach ( $terms as $term ) {
        printf(
            '<option value="%d" %s>%s</option>',
            $term->term_id,
            selected( $selected, $term->term_id, false ),
            esc_html( $term->name )
        );
    }
    echo '</select>';
    wp_nonce_field( 'urbantaxi_custom_location_action', 'urbantaxi_custom_location_nonce' );
}

add_action( 'pre_get_posts', 'urbantaxi_locations_pre_get_posts', 20 );
function urbantaxi_locations_pre_get_posts( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( $query->is_tax( 'locations' ) ) {
        $query->set( 'post_type', [ 'mptbm_rent' ] );
        $posts_per_page = absint( get_option( 'posts_per_page', 10 ) );
        $query->set( 'posts_per_page', $posts_per_page > 0 ? $posts_per_page : 10 );
    }
}

add_action( 'save_post', 'urbantaxi_save_custom_location', 20 );
function urbantaxi_save_custom_location( $post_id ) {
    if ( get_post_type( $post_id ) !== 'mptbm_rent' ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if (
        ! isset( $_POST['urbantaxi_custom_location_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['urbantaxi_custom_location_nonce'] ) ),
            'urbantaxi_custom_location_action'
        )
    ) {
        return;
    }
    if ( isset( $_POST['custom_location'] ) ) {
        $term_id = intval( wp_unslash( $_POST['custom_location'] ) );
        if ( $term_id > 0 ) {
            wp_set_object_terms( $post_id, [ $term_id ], 'locations', false );
        } else {
            wp_set_object_terms( $post_id, [], 'locations', false );
        }
    }
}


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

// -------------------------------------------------------------------------
// Location taxonomy image fields (add/edit/save + media uploader script)
// -------------------------------------------------------------------------

add_action( 'locations_add_form_fields', 'urbantaxi_add_location_image_field' );
function urbantaxi_add_location_image_field() { ?>
	<div class="form-field term-group">
		<label for="location-image"><?php esc_html_e( 'Image', 'urbantaxi' ); ?></label>
		<?php wp_nonce_field( 'urbantaxi_location_image_action', 'urbantaxi_location_image_nonce' ); ?>
		<input type="hidden" id="location-image" name="location-image" value="">
		<div id="image-preview"></div>
		<button type="button" class="button button-secondary" id="upload-image-btn">
			<?php esc_html_e( 'Upload Image', 'urbantaxi' ); ?>
		</button>
	</div>
	<?php
}

add_action( 'locations_edit_form_fields', 'urbantaxi_edit_location_image_field' );
function urbantaxi_edit_location_image_field( $term ) {
	$image_id = get_term_meta( $term->term_id, 'location-image', true );
	?>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label for="location-image"><?php esc_html_e( 'Image', 'urbantaxi' ); ?></label></th>
		<td>
			<?php wp_nonce_field( 'urbantaxi_location_image_action', 'urbantaxi_location_image_nonce' ); ?>
			<input type="hidden" id="location-image" name="location-image" value="<?php echo esc_attr( $image_id ); ?>">
			<div id="image-preview">
				<?php if ( $image_id ) {
					echo wp_get_attachment_image( $image_id, 'thumbnail' );
				} ?>
			</div>
			<button type="button" class="button button-secondary" id="upload-image-btn">
				<?php esc_html_e( 'Upload Image', 'urbantaxi' ); ?>
			</button>
		</td>
	</tr>
	<?php
}

add_action( 'created_locations', 'urbantaxi_save_location_image' );
add_action( 'edited_locations', 'urbantaxi_save_location_image' );
function urbantaxi_save_location_image( $term_id ) {
	if (
		! isset( $_POST['urbantaxi_location_image_nonce'] ) ||
		! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['urbantaxi_location_image_nonce'] ) ),
			'urbantaxi_location_image_action'
		)
	) {
		return;
	}
	if ( isset( $_POST['location-image'] ) ) {
		update_term_meta(
			$term_id,
			'location-image',
			sanitize_text_field( wp_unslash( $_POST['location-image'] ) )
		);
	}
}

add_action( 'admin_footer', 'urbantaxi_location_admin_scripts' );
function urbantaxi_location_admin_scripts( $hook ) {
	wp_enqueue_media();
	?>
	<script>
	jQuery(document).ready(function($){
		var mediaUploader;
		$('#upload-image-btn').click(function(e) {
			e.preventDefault();
			if (mediaUploader) {
				mediaUploader.open();
				return;
			}
			mediaUploader = wp.media({
				title: 'Select Image',
				button: { text: 'Use this image' },
				multiple: false
			});
			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				$('#location-image').val(attachment.id);
				$('#image-preview').html('<img src="'+attachment.url+'" style="max-width:100px;">');
			});
			mediaUploader.open();
		});
	});
	</script>
	<?php
}

// -------------------------------------------------------------------------
// Single transportation URL routing
// -------------------------------------------------------------------------

add_action( 'parse_request', 'urbantaxi_parse_request_transportation', 0 );
function urbantaxi_parse_request_transportation( $wp ) {
	if ( ! empty( $wp->request ) && strpos( $wp->request, 'transportation/' ) === 0 ) {
		$slug                        = basename( $wp->request );
		$wp->query_vars['post_type'] = 'transportation';
		$wp->query_vars['name']      = $slug;
		status_header( 200 );
	}
}

add_action( 'pre_get_posts', 'urbantaxi_pre_get_posts_transportation', 0 );
function urbantaxi_pre_get_posts_transportation( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( $query->get( 'post_type' ) === 'transportation' ) {
			$query->is_404 = false;
		}
	}
}

add_filter( 'template_include', 'urbantaxi_template_include_transportation', 999 );
function urbantaxi_template_include_transportation( $template ) {
	if ( get_query_var( 'post_type' ) === 'transportation' ) {
		return get_stylesheet_directory() . '/single-transportation.php';
	}
	return $template;
}

// -------------------------------------------------------------------------
// Booking form scroll behaviour (.mptbm_transport_select)
// -------------------------------------------------------------------------

add_action( 'wp_footer', 'urbantaxi_custom_transport_select_script' );
function urbantaxi_custom_transport_select_script() { ?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		function findScrollableParent(element) {
			var $element = $(element);
			var $parent = $element.parent();
			while ($parent.length) {
				var overflow = $parent.css('overflow-y');
				if (overflow === 'auto' || overflow === 'scroll') {
					if ($parent[0].scrollHeight > $parent[0].clientHeight) {
						return $parent;
					}
				}
				$parent = $parent.parent();
			}
			var $modalContent = $element.closest('.urbantaxi-modal-content');
			if ($modalContent.length && $modalContent[0].scrollHeight > $modalContent[0].clientHeight) {
				return $modalContent;
			}
			var $mainSection = $element.closest('.mainSection');
			if ($mainSection.length && $mainSection[0].scrollHeight > $mainSection[0].clientHeight) {
				return $mainSection;
			}
			var $mpStickyArea = $element.closest('.mp_sticky_depend_area');
			if ($mpStickyArea.length && $mpStickyArea[0].scrollHeight > $mpStickyArea[0].clientHeight) {
				return $mpStickyArea;
			}
			return null;
		}
		$(document).on('click', '.mptbm_transport_select', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var $button = $(this);
			var $scrollableParent = findScrollableParent($button);
			if ($scrollableParent) {
				$scrollableParent.animate({ scrollTop: $scrollableParent[0].scrollHeight }, 500);
			} else {
				var $modalContent = $button.closest('.urbantaxi-modal-content');
				if ($modalContent.length) {
					$modalContent.animate({ scrollTop: $modalContent[0].scrollHeight }, 500);
				}
			}
			return false;
		});
	});
	</script>
	<?php
}

// -------------------------------------------------------------------------
// Elementor support for driver CPT + transport feature label helper
// -------------------------------------------------------------------------

add_action( 'init', 'urbantaxi_add_elementor_support_for_driver' );
function urbantaxi_add_elementor_support_for_driver() {
	add_post_type_support( 'driver', 'elementor' );
}

function urbantaxi_modify_transport_features( $features ) {
	if ( ! empty( $features ) && is_array( $features ) ) {
		foreach ( $features as $key => $feature ) {
			$label = $feature['label'] ?? '';
			$text  = $feature['text'] ?? '';
			$features[ $key ]['text'] = $label . ': ' . $text;
		}
	}
	return $features;
}

// -------------------------------------------------------------------------
// Sync product thumbnails from linked mptbm_rent records (admin only)
// -------------------------------------------------------------------------

add_action( 'init', 'urbantaxi_sync_product_thumbnails' );
function urbantaxi_sync_product_thumbnails() {
	if ( ! is_admin() ) {
		return;
	}
	$products = get_posts( [
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'meta_key'       => 'link_mptbm_id',
		'post_status'    => 'any',
	] );
	if ( empty( $products ) ) {
		return;
	}
	$grouped = [];
	foreach ( $products as $product ) {
		$rent_id = get_post_meta( $product->ID, 'link_mptbm_id', true );
		if ( ! $rent_id ) {
			continue;
		}
		$thumbnail_id         = get_post_thumbnail_id( $product->ID );
		$grouped[ $rent_id ][] = [
			'product_id'   => $product->ID,
			'thumbnail_id' => $thumbnail_id,
		];
	}
	foreach ( $grouped as $rent_id => $items ) {
		$main_thumbnail_id = 0;
		foreach ( $items as $item ) {
			if ( ! empty( $item['thumbnail_id'] ) ) {
				$main_thumbnail_id = $item['thumbnail_id'];
				break;
			}
		}
		if ( ! $main_thumbnail_id ) {
			continue;
		}
		foreach ( $items as $item ) {
			if ( empty( $item['thumbnail_id'] ) ) {
				update_post_meta( $item['product_id'], '_thumbnail_id', $main_thumbnail_id );
			}
		}
	}
}
