<?php
/**
 * Plugin Name: UrbanTaxi Core
 * Description: Complete companion plugin for UrbanTaxi theme - 13 Elementor widgets, custom post types, and cost calculator
 * Version: 1.0.0
 * Author: UrbanTaxi
 * License: GPL v2+
 * Text Domain: urbantaxi-core
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'URBANTAXI_CORE_VERSION', '1.0.0' );
define( 'URBANTAXI_CORE_PLUGIN_FILE', __FILE__ );
define( 'URBANTAXI_CORE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'URBANTAXI_CORE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

function urbantaxi_core_is_elementor_active() {
    return did_action( 'elementor/loaded' ) !== 0;
}

function urbantaxi_core_elementor_missing_notice() {
    if ( did_action( 'elementor/loaded' ) ) {
        return;
    }

    $message = sprintf(
        esc_html__( '%1$s requires %2$s to be installed and activated.', 'urbantaxi-core' ),
        '<strong>' . esc_html__( 'UrbanTaxi Core', 'urbantaxi-core' ) . '</strong>',
        '<strong>' . esc_html__( 'Elementor', 'urbantaxi-core' ) . '</strong>'
    );

    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        wp_kses_post( $message )
    );
}

function urbantaxi_core_init() {
    if ( ! urbantaxi_core_is_elementor_active() ) {
        add_action( 'admin_notices', 'urbantaxi_core_elementor_missing_notice' );
        return;
    }

    load_plugin_textdomain(
        'urbantaxi-core',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );

    require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/base/dependencies.php';
    require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/widgets/widget-loader.php';
}

add_action( 'plugins_loaded', 'urbantaxi_core_init', 11 );

// ===== CUSTOM POST TYPES (no Elementor dependency) =====
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/cpt/custom-post-types.php';

// ===== COST CALCULATOR (no Elementor dependency) =====
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/calculator/class-installer.php';
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/calculator/class-calculator.php';
require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/calculator/class-shortcode.php';

if ( is_admin() ) {
    require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/calculator/admin/class-admin.php';
    require_once URBANTAXI_CORE_PLUGIN_DIR . 'inc/calculator/admin/class-calculator-list.php';
    new Cost_Calculator_Admin();
}

add_action( 'init', function() {
    Cost_Calculator_Shortcode::register();
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'urbantaxi-calculator-style',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/css/calculator/calculator.css',
        [],
        URBANTAXI_CORE_VERSION
    );
    wp_enqueue_script(
        'urbantaxi-calculator-script',
        URBANTAXI_CORE_PLUGIN_URL . 'assets/js/calculator/calculator.js',
        [ 'jquery' ],
        URBANTAXI_CORE_VERSION,
        true
    );
    wp_localize_script( 'urbantaxi-calculator-script', 'costCalculatorCore', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'cost-calculator-core' ),
    ] );
} );

add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( strpos( $hook, 'cost-calculator' ) !== false ) {
        wp_enqueue_style(
            'urbantaxi-calculator-admin',
            URBANTAXI_CORE_PLUGIN_URL . 'assets/css/calculator/admin.css',
            [],
            URBANTAXI_CORE_VERSION
        );
        wp_enqueue_script(
            'urbantaxi-calculator-admin-script',
            URBANTAXI_CORE_PLUGIN_URL . 'assets/js/calculator/admin.js',
            [ 'jquery' ],
            URBANTAXI_CORE_VERSION,
            true
        );
    }
} );

add_action( 'wp_ajax_calculate', 'cost_calculator_calculate' );
add_action( 'wp_ajax_nopriv_calculate', 'cost_calculator_calculate' );

function cost_calculator_core_sanitize_values( $data ) {
    if ( ! is_array( $data ) ) {
        return array();
    }
    $sanitized = array();
    foreach ( $data as $key => $value ) {
        $clean_key = sanitize_key( wp_unslash( (string) $key ) );
        if ( '' === $clean_key ) {
            continue;
        }
        if ( is_array( $value ) ) {
            $sanitized[ $clean_key ] = cost_calculator_core_sanitize_values( $value );
        } else {
            $sanitized[ $clean_key ] = sanitize_text_field( wp_unslash( (string) $value ) );
        }
    }
    return $sanitized;
}

function cost_calculator_calculate() {
    check_ajax_referer( 'cost-calculator-core', 'nonce' );

    $calculator_id = isset( $_POST['calculator_id'] ) ? intval( $_POST['calculator_id'] ) : 0;
    $values        = isset( $_POST['values'] ) ? cost_calculator_core_sanitize_values( $_POST['values'] ) : array();

    if ( ! $calculator_id ) {
        wp_send_json_error( esc_html__( 'Invalid calculator ID', 'urbantaxi-core' ) );
    }

    $calculator = new Cost_Calculator( $calculator_id );
    $result     = $calculator->calculate( $values );

    wp_send_json_success( $result );
}

function urbantaxi_core_activate() {
    if ( function_exists( 'delete_transient' ) ) {
        delete_transient( 'elementor_widgets_data' );
    }
    Cost_Calculator_Installer::activate();
}

register_activation_hook( __FILE__, 'urbantaxi_core_activate' );

function urbantaxi_core_get_version() {
    return URBANTAXI_CORE_VERSION;
}

function urbantaxi_core_get_plugin_url( $path = '' ) {
    return URBANTAXI_CORE_PLUGIN_URL . ltrim( $path, '/' );
}

function urbantaxi_core_get_plugin_dir( $path = '' ) {
    return URBANTAXI_CORE_PLUGIN_DIR . ltrim( $path, '/' );
}
