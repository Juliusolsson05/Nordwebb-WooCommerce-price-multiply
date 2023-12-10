<?php
global $wpdb;

// Function to set standard prices
function nwpm_set_standard_prices() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nwpm_original_prices';

    // Check if the standard prices are already set
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    if ($count > 0) {
        // Update existing entries
        // Your logic to update the table
    } else {
        // Insert the current prices as standard
        $wpdb->query("INSERT INTO $table_name (post_id, meta_key, meta_value)
                      SELECT post_id, meta_key, meta_value
                      FROM {$wpdb->postmeta}
                      WHERE meta_key IN ('_regular_price', '_sale_price')");
    }
}

// Function to apply the multiplier
function nwpm_apply_multiplier($multiplier) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nwpm_original_prices';
    $postmeta_table = $wpdb->prefix . 'postmeta';

  // Store the multiplier value in WordPress options
    update_option('nwpm_last_multiplier', $multiplier);

    // Multiply prices and update postmeta
    $wpdb->query("UPDATE $postmeta_table pm
                  JOIN $table_name nwpm ON pm.post_id = nwpm.post_id AND pm.meta_key = nwpm.meta_key
                  SET pm.meta_value = nwpm.meta_value * $multiplier
                  WHERE nwpm.meta_key IN ('_regular_price', '_sale_price')");
}

// Function to reset prices
function nwpm_reset_to_standard_prices() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nwpm_original_prices';
    $postmeta_table = $wpdb->prefix . 'postmeta';
    // Reset the stored multiplier value
    update_option('nwpm_last_multiplier', '1');
    // Reset prices to original values
    $wpdb->query("UPDATE $postmeta_table pm
                  JOIN $table_name nwpm ON pm.post_id = nwpm.post_id AND pm.meta_key = nwpm.meta_key
                  SET pm.meta_value = nwpm.meta_value
                  WHERE nwpm.meta_key IN ('_regular_price', '_sale_price')");
}

