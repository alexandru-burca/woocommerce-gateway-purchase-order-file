<?php
/**
* Plugin Name: WooCommerce Purchase Order Payment Gateway File Addon
* Description: Attach file to purchase order
* Version: 1.0.1
* Author: Alex Burca
* Author URI: https://github.com/alexandru-burca
* Requires at least: 4.1.0
* Tested up to: 5.5
*
* Text Domain: woocommerce-gateway-purchase-order-file
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if WooCommerce and WooCommerce Purchase Order Gateway are active
 **/
if (
    in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
    &&
    in_array( 'woocommerce-gateway-purchase-order/woocommerce-gateway-purchase-order.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
) {
    /**
     * site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-gateway-purchase-order-file.php';

    /**
     * Begins execution of the plugin.
     *
     * @since    1.0.0
     */
    function run_Woocommerce_Gateway_Purchase_Order_File() {

        $plugin = new Woocommerce_Gateway_Purchase_Order_File();
        $plugin->run();

    }
    run_Woocommerce_Gateway_Purchase_Order_File();
}else{
    add_action('admin_notices', function () {
        echo sprintf(
            '<div class="notice notice-warning is-dismissible"><p>PO File - <strong>Woocommerce</strong> or <strong>Purchase Order Payment Gateway</strong> not active. Please install and activate both plugins.</p></div>',
        );
    });
}

