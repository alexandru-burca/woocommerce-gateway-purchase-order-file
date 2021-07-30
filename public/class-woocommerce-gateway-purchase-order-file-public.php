<?php
class Woocommerce_Gateway_Purchase_Order_File_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    public function enqueue_scripts(){

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom.js', array( 'jquery' ), filemtime(plugin_dir_path( __FILE__ ) . 'js/custom.js'), true );

        wp_localize_script( $this->plugin_name, 'woo_order_po', array('ajax_object' => admin_url( 'admin-ajax.php' )) );
        wp_enqueue_script( $this->plugin_name.'-jquery-cookie', 'https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js', array('jquery'), '1.4.1', true );

    }

    /**
     * Purchase order HTML output.
     * @access public
     * @since 1.0.0
     * @param $order
     * @return void
     */
    public function display_purchase_order_file ( $order ) {
        $payment_method = version_compare( WC_VERSION, '3.0', '<' ) ? $order->payment_method : $order->get_payment_method();
        $order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

        if ( 'woocommerce_gateway_purchase_order' === $payment_method ) {
            $po_file = get_post_meta( $order_id, '_po_file', true );
            if ( '' != $po_file ) {
                if ( 'woocommerce_order_details_after_order_table' == current_filter() ) {
                    echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';
                    echo '<li class="woocommerce-order-overview__purchase-order purchase-order">' . __( 'Purchase Order File:', 'woocommerce-gateway-purchase-order' ) . '<a href="' . $po_file . '" target="_blank"><strong>Open</strong></a></li>';
                    echo '</ul>';
                } else {
                    echo '<p class="form-field form-field-wide"><strong>' . __( 'Purchase Order File:', 'woocommerce-gateway-purchase-order' ) . '</strong><a href="' . $po_file . '" target="_blank"><strong>Open</strong></a></p>' . "\n";
                }
            }
        }
    }

    /**
     * Display the Purchase Order File on a Print Invoices/Packing lists output.
     *
     * @param string $type
     * @param string $action
     * @param object $document
     * @param object $order
     * @return void
     */
    public function add_po_file_to_pip( $type, $action, $document, $order ) {
        if ( 'invoice' != $type ) {
            return;
        }

        $payment_method = version_compare( WC_VERSION, '3.0', '<' ) ? $order->payment_method : $order->get_payment_method();
        $order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

        if ( 'woocommerce_gateway_purchase_order' === $payment_method ) {
            $po_file = get_post_meta( $order_id, '_po_file', true );
            printf( '<div class="purchase-order-number">' . __( '<strong>Purchase order file:</strong> %s', 'woocommerce-gateway-purchase-order' ) . '</div>', '<strong>', '</strong>',  $po_file );
            /* translators: Placeholder: %1$s - opening <strong> tag, %2$s - coupons count (used in order), %3$s - closing </strong> tag - %4$s - coupons list */
            printf( '<div class="purchase-order-file">' . __( '%1$sPurchase order filer:%2$s %3$s', 'woocommerce-gateway-purchase-order' ) . '</div>', '<strong>', '</strong>', $po_file );
        }
    }

    public function woo_order_file_upload(){
        if (in_array($_FILES['file']['type'], array('application/pdf'))) {
            $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
            setcookie('po_current_order_url', $upload['url'], time() + 60, COOKIEPATH, COOKIE_DOMAIN );
            echo json_encode(array('url' => $upload['url']));
        }
        wp_die();
    }

    public function update_order_file_meta($order_id){
        if(isset($_COOKIE['po_current_order_url']) && $_COOKIE['po_current_order_url'] != '') {
            $data = $_COOKIE['po_current_order_url'];
            $order = wc_get_order($order_id);
            $order->update_meta_data( '_po_file', $data);
            $order->save();
            //remove cookie
            unset($_COOKIE['po_current_order_url']);
            setcookie('po_current_order_url', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        }
    }

}