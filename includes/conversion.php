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

function edd_gadw_add_conversion_tracking_code( $content ) {

    // Success page?
    $success_page = edd_get_option( 'success_page', 0 );

    if( $success_page == 0 || get_the_ID() != $success_page )
        return $content;

    // Tracking activated?
    $conversion_status = edd_get_option( 'edd_gadw_conversion_status', 0 );

    if ( $conversion_status != '1' )
        return $content;

    // ID and label set?
    $conversion_id = edd_get_option( 'edd_gadw_conversion_id', false );
    $conversion_label = edd_get_option( 'edd_gadw_conversion_label', false );

    if ( ! $conversion_id || ! $conversion_label )
        return $content;

    // It's a finished purchase?
    $purchase_session = edd_get_purchase_session();

    if ( empty ( $purchase_session['purchase_key'] ) )
        return $content;

    $payment_id = edd_get_purchase_id_by_key( $purchase_session['purchase_key'] );

    // Conversion already sent?
    $payment_conversion = get_post_meta( $payment_id, '_gadw_conversions', null );

    if ( ! empty ( $payment_conversion ) )
        return $content;

    // Collect data
    $payment = new EDD_Payment( $payment_id );

    if ( ! isset ( $payment->total ) || $payment->total == '0' || $payment->total == '0.00' || ! isset ( $payment->currency ) )
        return $content;

    // Build EDD purchase conversion data
    $conversion_value = $payment->total;
    $conversion_currency = $payment->currency;

    // Save conversions data to payment
    $conversions = array();

    $conversions[] = array(
        'datetime' => time(),
        'id' => $conversion_id,
        'label' => $conversion_label,
        'value' => $conversion_value,
        'currency' => $conversion_currency
    );

    update_post_meta( $payment_id, '_gadw_conversions', $conversions );

    ob_start();
    ?>
    <!-- Google AdWords Conversion Code -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = <?php echo $conversion_id; ?>;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_label = "<?php echo $conversion_label; ?>";
        var google_conversion_value = <?php echo $conversion_value; ?>;
        var google_conversion_currency = "<?php echo $conversion_currency; ?>";
        var google_remarketing_only = false;
        /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/<?php echo $conversion_id; ?>/?value=<?php echo $conversion_value; ?>&amp;currency_code=EUR&amp;label=<?php echo $conversion_label; ?>&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
    <?php

    $conversion_code = ob_get_clean();

    if ( ! empty ( $conversion_code ) )
        $content .= $conversion_code;

    return $content;
}
add_action( 'the_content', 'edd_gadw_add_conversion_tracking_code' );