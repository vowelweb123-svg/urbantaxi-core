<?php
/**
 * Calculator list table
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Cost_Calculator_List extends WP_List_Table {

	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Calculator', 'cost-calculator-core' ),
				'plural'   => __( 'Calculators', 'cost-calculator-core' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Get columns
	 */
	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Title', 'cost-calculator-core' ),
			'description' => __( 'Description', 'cost-calculator-core' ),
			'shortcode'   => __( 'Shortcode', 'cost-calculator-core' ),
			'created'     => __( 'Created', 'cost-calculator-core' ),
			'actions'     => __( 'Actions', 'cost-calculator-core' ),
		);
	}

	/**
	 * Get sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'title'   => array( 'title', false ),
			'created' => array( 'created_at', false ),
		);
	}

	/**
	 * Get bulk actions
	 */
	public function get_bulk_actions() {
		return array();
	}

	/**
	 * Prepare items
	 */
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$calculators = Cost_Calculator::get_all();

		$data = array();
		foreach ( $calculators as $calculator ) {
			$data[] = array(
				'id'          => $calculator->id,
				'title'       => $calculator->title,
				'description' => substr( wp_strip_all_tags( $calculator->description ), 0, 100 ),
				'shortcode'   => '[cost_calculator id="' . $calculator->id . '"]',
				'created'     => mysql2date( get_option( 'date_format' ), $calculator->created_at ),
			);
		}

		$total_items = count( $data );
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		$this->items = $data;
	}

	/**
	 * Render title column
	 */
	public function column_title( $item ) {
		$edit_url = admin_url( "admin.php?page=cost-calculator-new&id={$item['id']}" );
		return '<a href="' . esc_url( $edit_url ) . '"><strong>' . esc_html( $item['title'] ) . '</strong></a>';
	}

	/**
	 * Render description column
	 */
	public function column_description( $item ) {
		return esc_html( $item['description'] );
	}

	/**
	 * Render shortcode column
	 */
	public function column_shortcode( $item ) {
		return '<code style="background: #f0f0f0; padding: 3px 6px; border-radius: 3px;">' . esc_html( $item['shortcode'] ) . '</code>';
	}

	/**
	 * Render created column
	 */
	public function column_created( $item ) {
		return esc_html( $item['created'] );
	}

	/**
	 * Render actions column
	 */
	public function column_actions( $item ) {
		return $this->render_actions( $item['id'] );
	}

	/**
	 * Render column (fallback)
	 */
	public function column_default( $item, $column_name ) {
		return '';
	}

	/**
	 * Render checkbox
	 */
	public function column_cb( $item ) {
		return '<input type="checkbox" name="calculator_id[]" value="' . esc_attr( $item['id'] ) . '" />';
	}

	/**
	 * Render actions
	 */
	private function render_actions( $calculator_id ) {
		$edit_url = admin_url( "admin.php?page=cost-calculator-new&id=$calculator_id" );
		$delete_url = wp_nonce_url( admin_url( "admin.php?page=cost-calculator&action=delete&id=$calculator_id" ), 'delete_calculator_' . $calculator_id );

		return '<a href="' . esc_url( $edit_url ) . '" class="button button-small button-primary">' . esc_html__( 'Edit', 'cost-calculator-core' ) . '</a> ' .
			   '<a href="' . esc_url( $delete_url ) . '" class="button button-small button-link-delete" onclick="return confirm(\'' . esc_attr__( 'Are you sure?', 'cost-calculator-core' ) . '\')">' . esc_html__( 'Delete', 'cost-calculator-core' ) . '</a>';
	}
}
