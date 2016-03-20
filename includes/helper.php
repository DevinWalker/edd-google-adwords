<?php
/**
 * Helper
 *
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function edd_gadw_debug( $args ) {
    echo '<pre>';
    print_r($args);
    echo '</pre>';
}