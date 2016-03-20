<?php
/**
 * Settings
 *
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add settings to the gateway page
 *
 * @access      public
 * @since       1.0.0
 * @param       array $extensions_settings The existing EDD settings array
 * @return      array The modified EDD settings array
 */
function edd_gadw_add_extensions_settings( $extensions_settings ) {

    $default_gadw_settings = array(
        'edd_gadw_settings' => array(
            'id' => 'edd_gadw_conversion',
            'name' => '<strong>' . __('Conversion Tracking', 'edd-google-adwords') . '</strong>',
            'desc' => __('Configure the Google AdWords conversion tracking', 'edd-google-adwords' ),
            'type' => 'header'
        ),
        'edd_gadw_conversion_status' => array(
            'id'      => 'edd_gadw_conversion_status',
            'name' => __( 'Status', 'edd-google-adwords' ),
            'desc' => __( 'Check in order to insert the tracking code', 'edd-google-adwords' ),
            'type' => 'checkbox',
            'std' => '1'
        ),
        'edd_gadw_conversion_id' => array(
            'id'      => 'edd_gadw_conversion_id',
            'name' => __( 'Conversion ID', 'edd-google-adwords' ),
            'type' => 'text',
            'desc' => _( 'e.g. 1023980795', 'edd-google-adwords' ),
            'std' => ''
        ),
        'edd_gadw_conversion_label' => array(
            'id'      => 'edd_gadw_conversion_label',
            'name' => __( 'Conversion Label', 'edd-google-adwords' ),
            'type' => 'text',
            'desc' => _( 'e.g. cl3DNMLf5GQQ373A4wN', 'edd-google-adwords' ),
            'std' => ''
        ),

    );

    $default_gadw_settings = apply_filters( 'edd_gadw_settings', $default_gadw_settings );
    $extensions_settings['edd-gadw'] = $default_gadw_settings;

    return $extensions_settings;
}

add_filter('edd_settings_extensions', 'edd_gadw_add_extensions_settings');

/*
 * Add settings section
 */
function edd_gadw_add_extensions_settings_section( $extensions_sections ) {

    $extensions_sections['edd-gadw'] = __( 'Google AdWords', 'edd-google-adwords' );

    return $extensions_sections;
}
add_filter('edd_settings_sections_extensions', 'edd_gadw_add_extensions_settings_section');