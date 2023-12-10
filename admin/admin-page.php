<?php
$last_multiplier = get_option('nwpm_last_multiplier', '1'); // Default to 1 if not set


// Check if WordPress is fully loaded
if (function_exists('add_action')) {
    // Check if the user has the required capability
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle the form data
        // You'll need to include db-handler.php here to process the form data
        include_once plugin_dir_path( __FILE__ ) . '../includes/db-handler.php';

        // Call a function from db-handler.php based on the action
        if (isset($_POST['set_standard'])) {
            nwpm_set_standard_prices();
        } elseif (isset($_POST['apply_multiplier'])) {
            $multiplier = floatval($_POST['multiplier']);
            nwpm_apply_multiplier($multiplier);
        } elseif (isset($_POST['reset_multiplier'])) {
            nwpm_reset_to_standard_prices();
        }
    }
}
?>

<div class="wrap">
    <h1>WooCommerce Price Multiplier</h1>
    <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Price Multiplier:</th>
                <td><input type="number" step="any" name="multiplier" value="<?php echo esc_attr($last_multiplier); ?>" required />
</td>
            </tr>
        </table>
        <input type="submit" name="set_standard" class="button button-primary" value="Set Standard Prices"/>
        <input type="submit" name="apply_multiplier" class="button button-secondary" value="Apply Multiplier"/>
        <input type="submit" name="reset_multiplier" class="button button-secondary" value="Reset to Standard Prices"/>
    </form>
</div>

