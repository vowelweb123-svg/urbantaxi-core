<?php
/**
 * Shortcode handler for displaying calculators
 */

class Cost_Calculator_Shortcode {

	/**
	 * Register shortcode
	 */
	public static function register() {
		add_shortcode( 'cost_calculator', array( __CLASS__, 'render' ) );
	}

	/**
	 * Render calculator shortcode
	 */
	public static function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'cost_calculator'
		);

		$calculator_id = intval( $atts['id'] );

		if ( ! $calculator_id ) {
			return '<p style="color: red;">' . esc_html__( 'Cost Calculator ID is required.', 'cost-calculator-core' ) . '</p>';
		}

		$calculator = new Cost_Calculator( $calculator_id );
		$data = $calculator->get_data();

		if ( ! $data ) {
			return '<p style="color: red;">' . esc_html__( 'Calculator not found.', 'cost-calculator-core' ) . '</p>';
		}

		$fields = $calculator->get_fields();
		$settings = $calculator->get_settings();
		$summary_label = isset( $settings['summary_label'] ) ? $settings['summary_label'] : __( 'Total Summary', 'cost-calculator-core' );
		$text_color = isset( $settings['text_color'] ) ? $settings['text_color'] : '#111827';

		ob_start();
		?>
			<div class="cost-calculator-wrapper" data-calculator-id="<?php echo esc_attr( $calculator_id ); ?>" style="color: <?php echo esc_attr( $text_color ); ?>;">
				<form class="cost-calculator-form" id="calc-form-<?php echo esc_attr( $calculator_id ); ?>">
					<div class="cost-calculator-fields">
						<?php foreach ( $fields as $field ) : ?>
							<?php self::render_field( $field ); ?>
						<?php endforeach; ?>
					</div>

				   <div class="cost-calculator-summary" style="color: <?php echo esc_attr( $text_color ); ?>;">
					   <h3><?php echo esc_html( $summary_label ); ?></h3>
					   <table class="summary-table">
						   <thead>
							   <tr>
								   <th><?php esc_html_e( 'Name', 'cost-calculator-core' ); ?></th>
								   <th><?php esc_html_e( 'Total', 'cost-calculator-core' ); ?></th>
							   </tr>
						   </thead>
						   <tbody id="summary-<?php echo esc_attr( $calculator_id ); ?>">
							   <tr class="summary-total-row">
								   <td><?php esc_html_e( 'Total', 'cost-calculator-core' ); ?></td>
								   <td class="total-value">$<span>0.00</span></td>
							   </tr>
						   </tbody>
					   </table>
				   </div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render individual field
	 */
	private static function render_field( $field ) {
		$field_key = isset( $field['key'] ) ? $field['key'] : '';
		$field_type = isset( $field['type'] ) ? $field['type'] : '';
		$field_label = isset( $field['label'] ) ? $field['label'] : '';
		$field_id = 'field-' . sanitize_html_class( $field_key );
		$show_frontend = isset( $field['show_frontend'] ) ? (bool) $field['show_frontend'] : ( 'formula' !== $field_type );

		if ( ! $show_frontend ) {
			return;
		}

		echo '<div class="cost-calculator-field field-' . esc_attr( $field_type ) . '">';

		if ( $field_label && $field_type !== 'html' && $field_type !== 'divider' && $field_type !== 'formula' ) {
			echo '<label for="' . esc_attr( $field_id ) . '">' . esc_html( $field_label ) . '</label>';
		}

		switch ( $field_type ) {
			case 'text':
				self::render_text_field( $field, $field_id );
				break;
			case 'number':
				self::render_number_field( $field, $field_id );
				break;
			case 'quantity':
				self::render_quantity_field( $field, $field_id );
				break;
			case 'dropdown':
				self::render_dropdown_field( $field, $field_id );
				break;
			case 'radio':
				self::render_radio_field( $field );
				break;
			case 'checkbox':
				self::render_checkbox_field( $field );
				break;
			case 'slider':
				self::render_slider_field( $field, $field_id );
				break;
			case 'toggle':
				self::render_toggle_field( $field, $field_id );
				break;
			case 'html':
				self::render_html_field( $field );
				break;
			case 'divider':
				self::render_divider_field();
				break;
			case 'formula':
				// Formula fields are not displayed to users
				break;
		}

		echo '</div>';
	}

	/**
	 * Render text field
	 */
	private static function render_text_field( $field, $field_id ) {
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}

		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		?>
		<input 
			type="text" 
			id="<?php echo esc_attr( $field_id ); ?>" 
			name="<?php echo esc_attr( $field_key ); ?>" 
			class="field-input"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
		/>
		<?php
	}

	/**
	 * Render number field
	 */
	private static function render_number_field( $field, $field_id ) {
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}

		$min = isset( $field['min'] ) ? $field['min'] : '0';
		$max = isset( $field['max'] ) ? $field['max'] : '1000';
		$step = isset( $field['step'] ) ? $field['step'] : '1';
		?>
		<input 
			type="number" 
			id="<?php echo esc_attr( $field_id ); ?>" 
			name="<?php echo esc_attr( $field_key ); ?>" 
			class="field-input field-calculate"
			min="<?php echo esc_attr( $min ); ?>"
			max="<?php echo esc_attr( $max ); ?>"
			step="<?php echo esc_attr( $step ); ?>"
			value="0"
		/>
		<small><?php echo esc_html( "Min: $min Max: $max" ); ?></small>
		<?php
	}

	/**
	 * Render quantity field
	 */
	private static function render_quantity_field( $field, $field_id ) {
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}

		$min = isset( $field['min'] ) ? $field['min'] : '0';
		$max = isset( $field['max'] ) ? $field['max'] : '100';
		?>
		<div class="quantity-wrapper">
			<button type="button" class="qty-btn qty-minus">−</button>
			<input 
				type="number" 
				id="<?php echo esc_attr( $field_id ); ?>" 
				name="<?php echo esc_attr( $field_key ); ?>" 
				class="field-input field-calculate qty-input"
				min="<?php echo esc_attr( $min ); ?>"
				max="<?php echo esc_attr( $max ); ?>"
				value="0"
			/>
			<button type="button" class="qty-btn qty-plus">+</button>
		</div>
		<?php
	}

	/**
	 * Render dropdown field
	 */
	private static function render_dropdown_field( $field, $field_id ) {
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}

		$options = isset( $field['options'] ) ? $field['options'] : array();
		?>
		<select 
			id="<?php echo esc_attr( $field_id ); ?>" 
			name="<?php echo esc_attr( $field_key ); ?>"
			class="field-input field-calculate"
		>
			<option value=""> <?php esc_html_e( 'Select Value', 'cost-calculator-core' ); ?> </option>
			<?php foreach ( $options as $option ) : ?>
				<option value="<?php echo esc_attr( $option['value'] ?? '' ); ?>">
					<?php echo esc_html( $option['label'] ?? '' ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render radio field
	 */
	private static function render_radio_field( $field ) {
		$options = isset( $field['options'] ) ? $field['options'] : array();
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}
		?>
		<div class="radio-group">
			<?php foreach ( $options as $option ) : ?>
				<label class="radio-label">
					<input 
						type="radio" 
						name="<?php echo esc_attr( $field_key ); ?>" 
						value="<?php echo esc_attr( $option['value'] ?? '' ); ?>"
						class="field-calculate"
					/>
					<?php echo esc_html( $option['label'] ?? '' ); ?>
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render checkbox field
	 */
	private static function render_checkbox_field( $field ) {
		$options = isset( $field['options'] ) ? $field['options'] : array();
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}
		?>
		<div class="checkbox-group">
			<?php foreach ( $options as $option ) : ?>
				<label class="checkbox-label">
					<input 
						type="checkbox" 
						name="<?php echo esc_attr( $field_key ); ?>[]" 
						value="<?php echo esc_attr( $option['value'] ?? '' ); ?>"
						class="field-calculate"
					/>
					<?php echo esc_html( $option['label'] ?? '' ); ?>
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render slider field
	 */
	private static function render_slider_field( $field, $field_id ) {
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}

		$min = isset( $field['min'] ) ? $field['min'] : '0';
		$max = isset( $field['max'] ) ? $field['max'] : '100';
		$step = isset( $field['step'] ) ? $field['step'] : '1';
		?>
		<input 
			type="range" 
			id="<?php echo esc_attr( $field_id ); ?>" 
			name="<?php echo esc_attr( $field_key ); ?>" 
			class="field-input field-calculate slider"
			min="<?php echo esc_attr( $min ); ?>"
			max="<?php echo esc_attr( $max ); ?>"
			step="<?php echo esc_attr( $step ); ?>"
			value="<?php echo esc_attr( $min ); ?>"
		/>
		<output class="slider-value">0</output>
		<?php
	}

	/**
	 * Render toggle field
	 */
	private static function render_toggle_field( $field, $field_id ) {
		$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
		if ( '' === $field_key ) {
			return;
		}

		$on_value = isset( $field['on_value'] ) ? $field['on_value'] : '1';
		$off_value = isset( $field['off_value'] ) ? $field['off_value'] : '0';
		?>
		<label class="toggle-label">
			<input 
				type="checkbox" 
				id="<?php echo esc_attr( $field_id ); ?>" 
				name="<?php echo esc_attr( $field_key ); ?>" 
				class="toggle-input field-calculate"
				data-on-value="<?php echo esc_attr( $on_value ); ?>"
				data-off-value="<?php echo esc_attr( $off_value ); ?>"
			/>
			<span class="toggle-switch"></span>
		</label>
		<?php
	}

	/**
	 * Render HTML field
	 */
	private static function render_html_field( $field ) {
		$html = isset( $field['html'] ) ? $field['html'] : '';
		echo wp_kses_post( $html );
	}

	/**
	 * Render divider field
	 */
	private static function render_divider_field() {
		echo '<hr class="field-divider" />';
	}
}
