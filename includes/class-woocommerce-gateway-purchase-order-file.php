<?php

class Woocommerce_Gateway_Purchase_Order_File{
    protected $plugin_name;

    protected $version;

    protected $loader;

    public function __construct() {

        $this->version = '1.0.0';

        $this->plugin_name = 'woocommerce_gateway_purchase_order_file';

        $this->load_dependencies();
        $this->define_public_hooks();

    }

    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-gateway-purchase-order-file-loader.php';


        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-gateway-purchase-order-file-public.php';


        $this->loader = new Woocommerce_Gateway_Purchase_Order_File_Loader();

    }


    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Woocommerce_Gateway_Purchase_Order_File_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action( 'woocommerce_admin_order_data_after_order_details', $plugin_public, 'display_purchase_order_file' );
        $this->loader->add_action( 'woocommerce_email_after_order_table', $plugin_public, 'display_purchase_order_file' );
        $this->loader->add_action( 'woocommerce_order_details_after_order_table', $plugin_public, 'display_purchase_order_file' );

        // Print Invoices/Packing Lists Integration
        $this->loader->add_action( 'wc_pip_after_body', $plugin_public, 'add_po_file_to_pip', 10, 4 );

        $this->loader->add_action( 'wp_ajax_woo_order_file_upload', $plugin_public, 'woo_order_file_upload' );
        $this->loader->add_action( 'wp_ajax_nopriv_woo_order_file_upload', $plugin_public, 'woo_order_file_upload' );

        $this->loader->add_action('woocommerce_checkout_order_processed', $plugin_public, 'update_order_file_meta', 90, 1);

    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }

    function run(){
        $this->loader->run();
    }
}