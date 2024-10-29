<?php
/*
Plugin Name: AZ Custom Tabs for WooCommerce
Description: Add global and product-specific custom tabs in WooCommerce.
Version: 1.0
Author: Abbas Z. Dhebar
Author URI: https://in.linkedin.com/in/abbas-dhebar-113a91152
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Logo: assets/logo.png
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if WooCommerce is activated
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action('admin_notices', 'azwctabs_custom_tabs_woocommerce_error_notice');

    function azwctabs_custom_tabs_woocommerce_error_notice() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php esc_html_e('AZ WooCommerce Custom Tabs requires WooCommerce to be installed and activated.', 'az-custom-tabs-for-woocommerce'); ?></p>
        </div>
        <?php
    }

    // Prevent the plugin from running
    return;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/global-tabs.php';
require_once plugin_dir_path(__FILE__) . 'includes/product-tabs.php';

// Activation and Deactivation hooks
function azwctabs_custom_tabs_activate() {
    // Actions on activation (e.g., creating options)
}
register_activation_hook(__FILE__, 'azwctabs_custom_tabs_activate');

function azwctabs_custom_tabs_deactivate() {
    // Actions on deactivation (e.g., cleaning up)
}
register_deactivation_hook(__FILE__, 'azwctabs_custom_tabs_deactivate');



function azwctabs_plugin_admin_scripts($hook) {

    // Enqueue styles
    wp_enqueue_style('azwctabs-style', plugin_dir_url(__FILE__) . 'assets/main.css',array(),'1.0.0');

    // Enqueue scripts
    wp_enqueue_script('azwctabs-script', plugin_dir_url(__FILE__) . 'assets/main.js', array(), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'azwctabs_plugin_admin_scripts');

