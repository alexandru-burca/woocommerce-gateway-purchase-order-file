jQuery(document).ready(function($){
    $('body').on( 'updated_checkout', function() {
        $('.payment_box.payment_method_woocommerce_gateway_purchase_order').append('<fieldset id="po_file_fieldset"><p class="form-row form-row-first"> <label for="po_number_field_file_placeholder_field">Purchase Order File (pdf)</label> <input type="file" class="input-text" value="" id="po_number_field_file_placeholder_field" name="po_number_field_file_placeholder_field" accept="application/pdf"/> </p></fieldset>');
        $("#po_number_field_file_placeholder_field").on('change', function () {
            let file_data = $("#po_number_field_file_placeholder_field").prop('files')[0];
            let form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'woo_order_file_upload');
            $('.woocommerce-checkout button[name=woocommerce_checkout_place_order]').attr("disabled", true);
            $.ajax({
                url: woo_order_po.ajax_object,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (responseBody) {
                    $('.woocommerce-checkout button[name=woocommerce_checkout_place_order]').attr("disabled", false);
                    let response = JSON.parse(responseBody);
                    console.log(response.url);
                    Cookies.set('po_current_order_url', response.url, {expires: 7});
                }
            })
        })
    });
})