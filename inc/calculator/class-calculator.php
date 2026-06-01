<?php
/**
 * Main Calculator class
 */

class Cost_Calculator {

	private $id;
	private $calculator;

	public function __construct( $id ) {
		$this->id = intval( $id );
		$this->load();
	}

	/**
	 * Load calculator from database
	 */
	private function load() {
		global $wpdb;
		
		$this->calculator = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}cost_calculators WHERE id = %d",
				$this->id
			)
		);
	}

	/**
	 * Get calculator ID
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get calculator data
	 */
	public function get_data() {
		return $this->calculator;
	}

	/**
	 * Get calculator fields
	 */
	public function get_fields() {
		if ( ! $this->calculator ) {
			return array();
		}
		return json_decode( $this->calculator->fields, true ) ?: array();
	}

	/**
	 * Get calculator settings
	 */
	public function get_settings() {
		if ( ! $this->calculator ) {
			return array();
		}
		return json_decode( $this->calculator->settings, true ) ?: array();
	}

	/**
	 * Save calculator
	 */
	public function save( $title, $description, $fields, $settings = array() ) {
		global $wpdb;

		$normalized_fields = $this->normalize_fields( $fields );

		$data = array(
			'title'       => sanitize_text_field( $title ),
			'description' => wp_kses_post( $description ),
			'fields'      => wp_json_encode( $normalized_fields ),
			'settings'    => wp_json_encode( $settings ),
			'updated_at'  => current_time( 'mysql' ),
		);

		if ( $this->id ) {
			return $wpdb->update(
				$wpdb->prefix . 'cost_calculators',
				$data,
				array( 'id' => $this->id ),
				array( '%s', '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
		} else {
			$data['created_at'] = current_time( 'mysql' );
			$result = $wpdb->insert(
				$wpdb->prefix . 'cost_calculators',
				$data,
				array( '%s', '%s', '%s', '%s', '%s', '%s' )
			);
			if ( $result ) {
				$this->id = $wpdb->insert_id;
			}
			return $result;
		}
	}

	/**
	 * Calculate values based on formula
	 */
	public function calculate( $values ) {
		$fields = $this->get_fields();
		$result = array(
			'values'  => array(),
			'summary' => array(),
			'total'   => 0,
		);
		$context = array();
		$formula_fields = array();
		$summary_field_types = array( 'number', 'quantity', 'dropdown', 'radio', 'checkbox', 'slider', 'toggle' );

		foreach ( $fields as $field ) {
			$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
			$field_type = isset( $field['type'] ) ? $field['type'] : '';
			$field_label = isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : $field_key;
			$show_frontend = isset( $field['show_frontend'] ) ? (bool) $field['show_frontend'] : ( 'formula' !== $field_type );

			if ( '' === $field_key ) {
				continue;
			}

			$default_value = isset( $field['default_value'] ) ? $field['default_value'] : 0;
			$field_value = array_key_exists( $field_key, $values ) ? $values[ $field_key ] : $default_value;

			// Store field values
			$result['values'][ $field_key ] = $field_value;
			$context[ $field_key ] = floatval( $field_value );

			if ( $field_type === 'formula' ) {
				$formula_fields[] = $field;
			} elseif ( $show_frontend && in_array( $field_type, $summary_field_types, true ) && '' !== $field_label ) {
				$result['summary'][ $field_label ] = is_numeric( $field_value ) ? floatval( $field_value ) : 0;
			}
		}

		/*
		 * Resolve formula fields in order and allow formulas to depend on
		 * previously calculated formula keys.
		 */
		$max_iterations = max( 1, count( $formula_fields ) );
		for ( $i = 0; $i < $max_iterations; $i++ ) {
			$updated = false;

			foreach ( $formula_fields as $field ) {
				$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
				if ( '' === $field_key ) {
					continue;
				}

				$formula = isset( $field['formula'] ) ? (string) $field['formula'] : '';
				$calculated_value = $this->evaluate_formula( $formula, $context );

				if ( ! isset( $context[ $field_key ] ) || floatval( $context[ $field_key ] ) !== floatval( $calculated_value ) ) {
					$context[ $field_key ] = $calculated_value;
					$updated = true;
				}
			}

			if ( ! $updated ) {
				break;
			}
		}

		foreach ( $formula_fields as $field ) {
			$field_key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';
			$field_label = isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : $field_key;
			$calculated_value = isset( $context[ $field_key ] ) ? floatval( $context[ $field_key ] ) : 0;
			$show_frontend = isset( $field['show_frontend'] ) ? (bool) $field['show_frontend'] : false;
			$summary_only  = isset( $field['summary_only'] ) ? (bool) $field['summary_only'] : false;

			if ( ( $show_frontend || $summary_only ) && '' !== $field_label ) {
				$result['summary'][ $field_label ] = $calculated_value;
			}

			if ( ! $summary_only ) {
				$result['total'] += $calculated_value;
			}
		}

		return $result;
	}

	/**
	 * Evaluate mathematical formula
	 */
	private function evaluate_formula( $formula, $values ) {
		// Replace field keys with their values
		foreach ( $values as $key => $value ) {
			// Only replace numeric values
			if ( is_numeric( $value ) ) {
				$formula = str_replace( '{' . sanitize_key( $key ) . '}', floatval( $value ), $formula );
			}
		}

		// Remove any remaining unreplaced fields
		$formula = preg_replace( '/\{[^}]+\}/', '0', $formula );

		// Evaluate the formula using a safe math evaluation
		return $this->safe_eval_math( $formula );
	}

	/**
	 * Safe mathematical evaluation
	 */
	private function safe_eval_math( $expression ) {
		// Remove whitespace
		$expression = str_replace( ' ', '', $expression );

		// Only allow numbers, operators, and parentheses
		if ( ! preg_match( '/^[\d\.\+\-\*\/\(\)]+$/', $expression ) ) {
			return 0;
		}

		$tokens = $this->tokenize_expression( $expression );
		if ( empty( $tokens ) ) {
			return 0;
		}

		$rpn = $this->to_rpn( $tokens );
		if ( empty( $rpn ) ) {
			return 0;
		}

		return $this->eval_rpn( $rpn );
	}

	/**
	 * Tokenize math expression into numbers/operators.
	 */
	private function tokenize_expression( $expression ) {
		preg_match_all( '/\d*\.?\d+|[+\-*\/()]|./', $expression, $matches );
		$tokens = $matches[0];

		foreach ( $tokens as $token ) {
			if ( ! preg_match( '/^\d*\.?\d+$|^[+\-*\/()]$/', $token ) ) {
				return array();
			}
		}

		return $tokens;
	}

	/**
	 * Convert tokens to Reverse Polish Notation.
	 */
	private function to_rpn( $tokens ) {
		$output = array();
		$stack = array();
		$precedence = array(
			'+' => 1,
			'-' => 1,
			'*' => 2,
			'/' => 2,
		);

		foreach ( $tokens as $token ) {
			if ( preg_match( '/^\d*\.?\d+$/', $token ) ) {
				$output[] = $token;
				continue;
			}

			if ( '(' === $token ) {
				$stack[] = $token;
				continue;
			}

			if ( ')' === $token ) {
				while ( ! empty( $stack ) && '(' !== end( $stack ) ) {
					$output[] = array_pop( $stack );
				}

				if ( empty( $stack ) ) {
					return array();
				}

				array_pop( $stack );
				continue;
			}

			while ( ! empty( $stack ) && '(' !== end( $stack ) ) {
				$top = end( $stack );
				if ( $precedence[ $top ] >= $precedence[ $token ] ) {
					$output[] = array_pop( $stack );
				} else {
					break;
				}
			}

			$stack[] = $token;
		}

		while ( ! empty( $stack ) ) {
			$token = array_pop( $stack );
			if ( '(' === $token || ')' === $token ) {
				return array();
			}
			$output[] = $token;
		}

		return $output;
	}

	/**
	 * Evaluate Reverse Polish Notation stack.
	 */
	private function eval_rpn( $tokens ) {
		$stack = array();

		foreach ( $tokens as $token ) {
			if ( preg_match( '/^\d*\.?\d+$/', $token ) ) {
				$stack[] = floatval( $token );
				continue;
			}

			if ( count( $stack ) < 2 ) {
				return 0;
			}

			$right = array_pop( $stack );
			$left = array_pop( $stack );

			switch ( $token ) {
				case '+':
					$stack[] = $left + $right;
					break;
				case '-':
					$stack[] = $left - $right;
					break;
				case '*':
					$stack[] = $left * $right;
					break;
				case '/':
					$stack[] = ( 0.0 === (float) $right ) ? 0 : ( $left / $right );
					break;
				default:
					return 0;
			}
		}

		return ( 1 === count( $stack ) && is_numeric( $stack[0] ) ) ? floatval( $stack[0] ) : 0;
	}

	/**
	 * Normalize and sanitize field definitions before persistence.
	 */
	private function normalize_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return array();
		}

		$allowed_types = array( 'text', 'number', 'quantity', 'dropdown', 'radio', 'checkbox', 'slider', 'toggle', 'formula', 'html', 'divider' );
		$normalized = array();

		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$type = isset( $field['type'] ) ? sanitize_key( $field['type'] ) : '';
			$key = isset( $field['key'] ) ? sanitize_key( $field['key'] ) : '';

			if ( ! in_array( $type, $allowed_types, true ) ) {
				continue;
			}

			if ( 'divider' !== $type && 'html' !== $type && '' === $key ) {
				continue;
			}

			$item = array(
				'type'  => $type,
				'label' => isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '',
				'key'   => $key,
				'show_frontend' => isset( $field['show_frontend'] ) ? (bool) $field['show_frontend'] : ( 'formula' !== $type ),
				'default_value' => isset( $field['default_value'] ) ? (string) floatval( $field['default_value'] ) : '0',
			);

			if ( isset( $field['min'] ) ) {
				$item['min'] = (string) floatval( $field['min'] );
			}

			if ( isset( $field['max'] ) ) {
				$item['max'] = (string) floatval( $field['max'] );
			}

			if ( isset( $field['step'] ) ) {
				$item['step'] = (string) floatval( $field['step'] );
			}

			if ( isset( $field['placeholder'] ) ) {
				$item['placeholder'] = sanitize_text_field( $field['placeholder'] );
			}

			if ( 'formula' === $type ) {
				$item['formula']      = isset( $field['formula'] ) ? preg_replace( '/[^0-9\+\-\*\/\(\)\.\{\}_a-zA-Z]/', '', (string) $field['formula'] ) : '';
				$item['summary_only'] = isset( $field['summary_only'] ) ? (bool) $field['summary_only'] : false;
			}

			if ( 'html' === $type ) {
				$item['html'] = isset( $field['html'] ) ? wp_kses_post( $field['html'] ) : '';
			}

			if ( 'toggle' === $type ) {
				$item['on_value'] = isset( $field['on_value'] ) ? (string) floatval( $field['on_value'] ) : '1';
				$item['off_value'] = isset( $field['off_value'] ) ? (string) floatval( $field['off_value'] ) : '0';
			}

			if ( in_array( $type, array( 'dropdown', 'radio', 'checkbox' ), true ) ) {
				$item['options'] = array();
				$options = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();

				foreach ( $options as $option ) {
					if ( ! is_array( $option ) ) {
						continue;
					}

					$option_label = isset( $option['label'] ) ? sanitize_text_field( $option['label'] ) : '';
					$option_value = isset( $option['value'] ) ? sanitize_text_field( $option['value'] ) : '';

					if ( '' === $option_label || '' === $option_value ) {
						continue;
					}

					$item['options'][] = array(
						'label' => $option_label,
						'value' => $option_value,
					);
				}
			}

			$normalized[] = $item;
		}

		return $normalized;

	}

	/**
	 * Get all calculators
	 */
	public static function get_all() {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}cost_calculators ORDER BY created_at DESC"
		);
	}

	/**
	 * Delete calculator
	 */
	public function delete() {
		global $wpdb;

		return $wpdb->delete(
			$wpdb->prefix . 'cost_calculators',
			array( 'id' => $this->id ),
			array( '%d' )
		);
	}

	/**
	 * Duplicate calculator
	 */
	public function duplicate() {
		global $wpdb;

		$new_title = $this->calculator->title . ' (Copy)';
		
		$result = $wpdb->insert(
			$wpdb->prefix . 'cost_calculators',
			array(
				'title'       => $new_title,
				'description' => $this->calculator->description,
				'fields'      => $this->calculator->fields,
				'settings'    => $this->calculator->settings,
				'created_at'  => current_time( 'mysql' ),
				'updated_at'  => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return $result ? $wpdb->insert_id : false;
	}
}
