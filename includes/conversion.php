<?php
/**
 * Conversion Tracking
 *
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Get conversion tracking code and mark payment
 */
function edd_gadw_get_conversion_tracking_code( $payment, $content_filter = false ) {

    // Tracking activated?
    $conversion_status = edd_get_option('edd_gadw_conversion_status', 0);

    if ( $conversion_status != '1' )
        return false;

    // ID and label set?
    $conversion_id = edd_get_option('edd_gadw_conversion_id', false);
    $conversion_label = edd_get_option('edd_gadw_conversion_label', false);

    if ( ! $conversion_id || ! $conversion_label )
        return false;

    // Conversion already sent?
    if ( get_post_meta( $payment->ID, '_gadw_conversions', true ) && ! $content_filter ) {
        return false;
    }


    // Build EDD purchase conversion data
    $conversion_value = edd_get_payment_amount( $payment->ID );
    $conversion_currency = edd_get_payment_currency_code( $payment->ID );

    $conversion_value = ( strpos( $conversion_value, '.' ) !== false) ? $conversion_value : $conversion_value . '.00';

    // Mark conversion for this payment as sent
    update_post_meta( $payment->ID, '_gadw_conversions', true );

    //edd_insert_payment_note( $payment->ID, 'Google AdWords conversion tracking sent.' );

    // BEGIN Code
    ob_start();
    ?>

    <img height="1" width="1" style="border-style:none;" alt=""
         src="//www.googleadservices.com/pagead/conversion/<?php echo $conversion_id; ?>/?value=<?php echo $conversion_value; ?>&amp;currency_code=<?php echo $conversion_currency; ?>&amp;label=<?php echo $conversion_label; ?>&amp;guid=ON&amp;script=0"/>
    <?php

    // END Code
    $code = ob_get_clean();

    return $code;

    // TODO: Script method currently disabled to the the fact that wordpress manipulates the CDATA comments!!
    return;

    ?>
    <!-- Google AdWords Conversion Code -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = <?php echo $conversion_id; ?>;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_label = <?php echo json_encode( $conversion_label ); ?>;
        var google_conversion_value = <?php echo json_encode( $conversion_value ); ?>;
        var google_conversion_currency = <?php echo json_encode( $conversion_currency ); ?>;
        var google_remarketing_only = false;
        /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt=""
                 src="//www.googleadservices.com/pagead/conversion/<?php echo $conversion_id; ?>/?value=<?php echo $conversion_value; ?>&amp;currency_code=EUR&amp;label=<?php echo $conversion_label; ?>&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
    <?php
}

/*
 * Default payment receipt
 */
function edd_gadw_add_conversion_tracking_code_to_payment_receipt( $payment, $edd_receipt_args ) {

    if ( $edd_receipt_args['payment_id'] ) {

        // Get code and mark payment
        $conversion_code = edd_gadw_get_conversion_tracking_code( $payment );

        // Output
        if ( $conversion_code )
            echo $conversion_code;
    }
}
add_action( 'edd_payment_receipt_after_table', 'edd_gadw_add_conversion_tracking_code_to_payment_receipt', 10, 2);

/*
 * Support for EDD Bank Transfer Gateway
 */
function edd_gadw_add_conversion_tracking_code_to_edd_btg_confirmation_page( $content, $payment ) {

    // Get code and mark payment
    $conversion_code = edd_gadw_get_conversion_tracking_code( $payment, true );

    //var_dump($conversion_code);

    // Output
    if ( $conversion_code )
        $content .= $conversion_code;

    return $content;
}
add_filter('edd_btg_confirmation_page_content', 'edd_gadw_add_conversion_tracking_code_to_edd_btg_confirmation_page', 90, 2);