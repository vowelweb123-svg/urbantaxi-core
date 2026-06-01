<?php
/**
 * Admin pages handler
 */


class Cost_Calculator_Admin {

   public function __construct() {
	   add_action( 'admin_menu', array( $this, 'register_menu' ) );
	   add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
   }

   public function enqueue_admin_styles() {
	   $screen = get_current_screen();
	   if ( isset( $screen->id ) && ( $screen->id === 'toplevel_page_cost-calculator' || $screen->id === 'cost-calculator_page_cost-calculator-new' ) ) {
		   wp_enqueue_style('cc-admin-modern', plugins_url('assets/css/admin-modern.css', dirname(__FILE__, 4)), [], null);
	   }
   }

	/**
	 * Register admin menu
	 */
	public function register_menu() {
		$menu_slug = 'cost-calculator';

		add_menu_page(
			__( 'Cost Calculator', 'cost-calculator-core' ),
			__( 'Cost Calculator', 'cost-calculator-core' ),
			'manage_options',
			$menu_slug,
			array( $this, 'display_calculators_page' ),
			'dashicons-calculator',
			25
		);

		add_submenu_page(
			$menu_slug,
			__( 'All Calculators', 'cost-calculator-core' ),
			__( 'All Calculators', 'cost-calculator-core' ),
			'manage_options',
			$menu_slug,
			array( $this, 'display_calculators_page' )
		);

		add_submenu_page(
			$menu_slug,
			__( 'Add New Calculator', 'cost-calculator-core' ),
			__( 'Add New', 'cost-calculator-core' ),
			'manage_options',
			'cost-calculator-new',
			array( $this, 'display_editor_page' )
		);
	}

	/**
	 * Display list of calculators
	 */
	public function display_calculators_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to access this page.', 'cost-calculator-core' ) );
		}

		// Handle delete action
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
		if ( 'delete' === $action && isset( $_GET['id'] ) ) {
			$calculator_id = intval( wp_unslash( $_GET['id'] ) );
			$nonce_key = 'delete_calculator_' . $calculator_id;
			
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), $nonce_key ) ) {
				$calculator = new Cost_Calculator( $calculator_id );
				$calculator->delete();
				wp_safe_redirect( admin_url( 'admin.php?page=cost-calculator&message=deleted' ) );
				exit;
			}
		}
		
		$list_table = new Cost_Calculator_List();
		$list_table->prepare_items();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Cost Calculators', 'cost-calculator-core' ); ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=cost-calculator-new' ) ); ?>" class="page-title-action">
					<?php esc_html_e( 'Add New', 'cost-calculator-core' ); ?>
				</a>
			</h1>

			<?php if ( isset( $_GET['message'] ) && $_GET['message'] === 'deleted' ) : ?>
				<div class="updated notice"><p><?php esc_html_e( 'Calculator deleted successfully.', 'cost-calculator-core' ); ?></p></div>
			<?php endif; ?>

			<?php $list_table->display(); ?>
		</div>
		<?php
	}

	/**
	 * Display calculator editor
	 */
	public function display_editor_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to access this page.', 'cost-calculator-core' ) );
		}

		$calculator_id = isset( $_GET['id'] ) ? intval( wp_unslash( $_GET['id'] ) ) : 0;
		$calculator = null;
		$title = '';
		$description = '';
		$fields = array();

		if ( $calculator_id ) {
			$calculator = new Cost_Calculator( $calculator_id );
			$data = $calculator->get_data();
			if ( $data ) {
				$title = $data->title;
				$description = $data->description;
				$fields = $calculator->get_fields();
			}
		}

		// Handle form submission
		$has_valid_nonce = isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'calculator_editor' );
			if ( isset( $_POST['save_calculator'] ) && $has_valid_nonce ) {
				$title = isset( $_POST['calculator_title'] ) ? sanitize_text_field( wp_unslash( $_POST['calculator_title'] ) ) : '';
				$description = isset( $_POST['calculator_description'] ) ? wp_kses_post( wp_unslash( $_POST['calculator_description'] ) ) : '';
				$fields_json = isset( $_POST['calculator_fields'] ) ? wp_unslash( $_POST['calculator_fields'] ) : '[]';
				$fields = json_decode( $fields_json, true );
				$fields = is_array( $fields ) ? $fields : array();

				$settings = array();
				$settings['summary_label'] = isset( $_POST['summary_label'] ) ? sanitize_text_field( wp_unslash( $_POST['summary_label'] ) ) : 'Total Summary';
				$settings['text_color'] = isset( $_POST['text_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['text_color'] ) ) : '#111827';

				if ( ! $title ) {
					echo '<div class="notice notice-error"><p>' . esc_html__( 'Title is required.', 'cost-calculator-core' ) . '</p></div>';
				} else {
					if ( ! $calculator ) {
						$calculator = new Cost_Calculator( 0 );
					}
					$calculator->save( $title, $description, $fields, $settings );
					$calculator_id = $calculator->get_id();

					echo '<div class="notice notice-success"><p>' . esc_html__( 'Calculator saved successfully.', 'cost-calculator-core' ) . '</p></div>';
				}
			}

		   // Enqueue modern admin CSS
		   add_action('admin_enqueue_scripts', function() {
			   wp_enqueue_style('cc-admin-modern', plugins_url('assets/css/admin-modern.css', dirname(__FILE__, 4)), [], null);
		   });
		   ?>

		   <div class="cc-admin-wrap">
			   <h1 style="font-size:2.2rem;letter-spacing:-1px;margin-bottom:24px;"><?php esc_html_e( $calculator_id ? 'Edit Calculator' : 'Add New Calculator', 'cost-calculator-core' ); ?></h1>

			   <form method="POST" id="calculator-form" style="margin-bottom:0;">
				   <?php wp_nonce_field( 'calculator_editor', 'nonce' ); ?>

				   <div style="display:flex;gap:32px;flex-wrap:wrap;align-items:flex-start;">
					   <div style="flex:1 1 340px;min-width:320px;">
						   <h2><?php esc_html_e( 'Calculator Settings', 'cost-calculator-core' ); ?></h2>
						   <table class="form-table">
							   <tr>
								   <th scope="row"><label for="summary_label"><?php esc_html_e( 'Summary Title', 'cost-calculator-core' ); ?></label></th>
								   <td>
									   <input type="text" id="summary_label" name="summary_label" value="<?php echo esc_attr( isset( $calculator ) && $calculator ? ( $calculator->get_settings()['summary_label'] ?? 'Total Summary' ) : 'Total Summary' ); ?>" class="regular-text" />
									   <p class="description"><?php esc_html_e( 'Change the summary panel heading.', 'cost-calculator-core' ); ?></p>
								   </td>
							   </tr>
							   <tr>
								   <th scope="row"><label for="text_color"><?php esc_html_e( 'Text Color', 'cost-calculator-core' ); ?></label></th>
								   <td>
									   <input type="color" id="text_color" name="text_color" value="<?php echo esc_attr( isset( $calculator ) && $calculator ? ( $calculator->get_settings()['text_color'] ?? '#111827' ) : '#111827' ); ?>" />
									   <p class="description"><?php esc_html_e( 'Set the color for all field and summary text.', 'cost-calculator-core' ); ?></p>
								   </td>
							   </tr>
						   </table>

						   <table class="form-table">
							   <tr>
								   <th scope="row"><label for="calculator_title"><?php esc_html_e( 'Title', 'cost-calculator-core' ); ?></label></th>
								   <td>
									   <input type="text" id="calculator_title" name="calculator_title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" required />
								   </td>
							   </tr>
							   <tr>
								   <th scope="row"><label for="calculator_description"><?php esc_html_e( 'Description', 'cost-calculator-core' ); ?></label></th>
								   <td>
									   <?php wp_editor( $description, 'calculator_description', array( 'media_buttons' => false, 'textarea_rows' => 5 ) ); ?>
								   </td>
							   </tr>
						   </table>
					   </div>
					   <div style="flex:1 1 340px;min-width:320px;">
						   <h2><?php esc_html_e( 'Form Fields', 'cost-calculator-core' ); ?></h2>
						   <div id="fields-builder">
							   <div id="fields-list"></div>
							   <button type="button" class="button button-secondary" id="add-field-btn"><?php esc_html_e( 'Add Field', 'cost-calculator-core' ); ?></button>
						   </div>
					   </div>
				   </div>

				   <input type="hidden" id="calculator_fields" name="calculator_fields" />

				   <div style="margin-top:32px;">
					   <?php submit_button( __( 'Save Calculator', 'cost-calculator-core' ), 'primary', 'save_calculator' ); ?>
				   </div>
			   </form>
		   </div>

		<div id="field-modal" style="display:none;">
			<div class="field-modal-content">
				<h3><?php esc_html_e( 'Add Field', 'cost-calculator-core' ); ?></h3>
				<?php $this->render_field_form(); ?>
			</div>
		</div>

		<script>
			var calculatorFields = <?php echo wp_json_encode( $fields ); ?>;
		</script>
		<?php
	}

	/**
	 * Render field form template
	 */
	private function render_field_form() {
		?>
		<form id="field-form" class="field-form">
			<p>
				<label for="field_type"><?php esc_html_e( 'Field Type', 'cost-calculator-core' ); ?></label>
				<select id="field_type" name="field_type" required>
					<option value=""> <?php esc_html_e( 'Select Type', 'cost-calculator-core' ); ?> </option>
					<option value="text"><?php esc_html_e( 'Text Input', 'cost-calculator-core' ); ?></option>
					<option value="number"><?php esc_html_e( 'Number Input', 'cost-calculator-core' ); ?></option>
					<option value="quantity"><?php esc_html_e( 'Quantity', 'cost-calculator-core' ); ?></option>
					<option value="dropdown"><?php esc_html_e( 'Dropdown', 'cost-calculator-core' ); ?></option>
					<option value="radio"><?php esc_html_e( 'Radio Button', 'cost-calculator-core' ); ?></option>
					<option value="checkbox"><?php esc_html_e( 'Checkbox', 'cost-calculator-core' ); ?></option>
					<option value="slider"><?php esc_html_e( 'Slider', 'cost-calculator-core' ); ?></option>
					<option value="toggle"><?php esc_html_e( 'Toggle Switch', 'cost-calculator-core' ); ?></option>
					<option value="formula"><?php esc_html_e( 'Formula', 'cost-calculator-core' ); ?></option>
					<option value="html"><?php esc_html_e( 'HTML Content', 'cost-calculator-core' ); ?></option>
					<option value="divider"><?php esc_html_e( 'Divider', 'cost-calculator-core' ); ?></option>
				</select>
			</p>

			<p>
				<label for="field_label"><?php esc_html_e( 'Label', 'cost-calculator-core' ); ?></label>
				<input type="text" id="field_label" name="field_label" />
			</p>

			<p>
				<label for="field_key"><?php esc_html_e( 'Field Key (unique identifier)', 'cost-calculator-core' ); ?></label>
				<input type="text" id="field_key" name="field_key" />
			</p>

			<div id="field-options" style="display:none;">
				<p>
					<label><?php esc_html_e( 'Options', 'cost-calculator-core' ); ?></label>
					<small><?php esc_html_e( 'For calculations, set numeric values (example: Standard = 10, Premium = 20).', 'cost-calculator-core' ); ?></small>
					<div id="options-list"></div>
					<button type="button" class="button button-secondary" id="add-option"><?php esc_html_e( 'Add Option', 'cost-calculator-core' ); ?></button>
				</p>
			</div>

			<div id="field-formula" style="display:none;">
				<p>
					<label for="field_formula"><?php esc_html_e( 'Formula', 'cost-calculator-core' ); ?></label>
					<textarea id="field_formula" name="field_formula" rows="3" placeholder="{field_key1} * 2 + {field_key2}"></textarea>
					<small><?php esc_html_e( 'Use {field_key} to reference other fields', 'cost-calculator-core' ); ?></small>
					<div id="formula-key-hints" class="formula-key-hints"></div>
				</p>
				<p>
					<label for="field_summary_only">
						<input type="checkbox" id="field_summary_only" name="field_summary_only" />
						<?php esc_html_e( 'Show in summary only (does not add to total)', 'cost-calculator-core' ); ?>
					</label>
					<small><?php esc_html_e( 'Use this for displaying commission or breakdowns without affecting the final total.', 'cost-calculator-core' ); ?></small>
				</p>
			</div>

			<div id="field-html" style="display:none;">
				<p>
					<label for="field_html"><?php esc_html_e( 'HTML Content', 'cost-calculator-core' ); ?></label>
					<textarea id="field_html" name="field_html" rows="5"></textarea>
				</p>
			</div>

			<div id="field-min-max" style="display:none;">
				<p>
					<label for="field_min"><?php esc_html_e( 'Minimum', 'cost-calculator-core' ); ?></label>
					<input type="number" id="field_min" name="field_min" value="0" />
				</p>
				<p>
					<label for="field_max"><?php esc_html_e( 'Maximum', 'cost-calculator-core' ); ?></label>
					<input type="number" id="field_max" name="field_max" value="100" />
				</p>
			</div>

			<p>
				<label for="field_default_value"><?php esc_html_e( 'Default Value (used when no frontend input)', 'cost-calculator-core' ); ?></label>
				<input type="number" id="field_default_value" name="field_default_value" step="any" value="0" />
				<small><?php esc_html_e( 'Useful for hidden constants like commission.', 'cost-calculator-core' ); ?></small>
			</p>

			<p>
				<label for="field_show_frontend">
					<input type="checkbox" id="field_show_frontend" name="field_show_frontend" checked />
					<?php esc_html_e( 'Show on frontend', 'cost-calculator-core' ); ?>
				</label>
			</p>

			<button type="button" class="button button-primary" id="save-field-btn"><?php esc_html_e( 'Save Field', 'cost-calculator-core' ); ?></button>
		</form>
		<?php
	}
}
