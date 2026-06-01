<?php
/**
 * Installer class for setting up database tables
 */

class Cost_Calculator_Installer {

	/**
	 * Activate plugin
	 */
	public static function activate() {
		self::create_tables();
		flush_rewrite_rules();
	}

	/**
	 * Deactivate plugin
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Create database tables
	 */
	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Calculators table
		$table_calculators = $wpdb->prefix . 'cost_calculators';
		$sql_calculators = "CREATE TABLE IF NOT EXISTS $table_calculators (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			description LONGTEXT,
			fields LONGTEXT NOT NULL,
			settings LONGTEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		// Calculator submissions table
		$table_submissions = $wpdb->prefix . 'cost_calculator_submissions';
		$sql_submissions = "CREATE TABLE IF NOT EXISTS $table_submissions (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			calculator_id BIGINT UNSIGNED NOT NULL,
			values LONGTEXT,
			result DECIMAL(10, 2),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY calculator_id (calculator_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_calculators );
		dbDelta( $sql_submissions );
	}

	/**
	 * Uninstall plugin - clean up database
	 */
	public static function uninstall() {
		global $wpdb;

		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cost_calculators" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cost_calculator_submissions" );
	}
}
