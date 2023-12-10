<?php
/**
 * Plugin Name: Nordwebb WooCommerce Price Multiplier
 * Plugin URI: https://yourwebsite.com
 * Description: A WooCommerce plugin to set a universal price multiplier and manage prices effectively.
 * Version: 1.0
 * Author: Nordwebb  
 * Author URI: https://nordwebb.com
 * License: GPL2
 * WC requires at least: 3.0.0
 * WC tested up to: 5.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register the submenu under WooCommerce menu
 */
function nwpm_add_submenu_page() {
    add_submenu_page(
        'woocommerce', // The slug name for the parent menu
        'Price Multiplier', // The text to be displayed in the title tags
        'Price Multiplier', // The text to be used for the menu
        'manage_options', // The capability required for this menu to be displayed
        'nwpm-settings', // The slug name to refer to this menu
        'nwpm_settings_page' // The function to output the content for this page
    );
}
add_action('admin_menu', 'nwpm_add_submenu_page');

/**
 * Callback for displaying the plugin settings page
 */
function nwpm_settings_page() {
    // Include the settings page template
    include_once 'admin/admin-page.php';
}

// Activation and Deactivation hooks
register_activation_hook( __FILE__, 'nwpm_activate' );
register_deactivation_hook( __FILE__, 'nwpm_deactivate' );

/**
 * Function to execute on plugin activation
 */
function nwpm_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'nwpm_original_prices';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL to create your new table
    $sql = "CREATE TABLE $table_name (
        meta_id bigint(20) unsigned NOT NULL auto_increment,
        post_id bigint(20) unsigned NOT NULL default '0',
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext,
        PRIMARY KEY  (meta_id),
        KEY post_id (post_id),
        KEY meta_key (meta_key)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    // Query to get all the prices from postmeta
    $price_query = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_regular_price' OR meta_key = '_sale_price';";

    $prices = $wpdb->get_results( $price_query );

    // Insert the prices into the new table
    foreach ( $prices as $price ) {
        $wpdb->insert(
            $table_name,
            array(
                'post_id' => $price->post_id,
                'meta_key' => $price->meta_key,
                'meta_value' => $price->meta_value
            )
        );
    }
}

/**
 * Function to execute on plugin deactivation
 */
function nwpm_deactivate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'nwpm_original_prices';

    // SQL to drop the table
    $sql = "DROP TABLE IF EXISTS $table_name;";
    
    $wpdb->query($sql);
}

