<?php
/**
 * Plugin Name:     Easy Digital Downloads - Google AdWords
 * Plugin URI:      https://wordpress.org/plugins/edd-google-adwords/
 * Description:     Adding Google AdWords support for Easy Digital Downloads
 * Version:         1.1.1
 * Author:          flowdee
 * Author URI:      http://flowdee.de
 * Text Domain:     edd-google-adwords
 * Domain Path:     /languages
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 3, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @author          flowdee <coder@flowdee.de>
 * @copyright       Copyright (c) flowdee
 * @license         http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'EDD_GOOGLE_ADWORDS' ) ) {

    /**
     * Main EDD_GOOGLE_ADWORDS class
     *
     * @since       1.0.0
     */
    class EDD_GOOGLE_ADWORDS {

        /**
         * @var         EDD_GOOGLE_ADWORDS $instance The one true EDD_GOOGLE_ADWORDS
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_GOOGLE_ADWORDS
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_GOOGLE_ADWORDS();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {

            // Plugin version
            define( 'EDD_GADW_NAME', 'Easy Digital Downloads - Google AdWords' );

            // Plugin version
            define( 'EDD_GADW_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_GADW_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_GADW_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {

            // Get out if EDD is not active
            if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
                return;
            }

            // Include files and scripts
            require_once EDD_GADW_DIR . 'includes/helper.php';

            if ( is_admin() ) {
                require_once EDD_GADW_DIR . 'includes/admin/settings.php';
            }

            require_once EDD_GADW_DIR . 'includes/conversion.php';
        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {

            // Set filter for plugin's languages directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'edd_google_adwords_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale   = apply_filters( 'plugin_locale',  get_locale(), 'edd-google-adwords' );
            $mofile   = sprintf( '%1$s-%2$s.mo', 'edd-google-adwords', $locale );

            // Setup paths to current locale file
            $mofile_local  = $lang_dir . $mofile;
            $mofile_global = WP_LANG_DIR . '/edd-google-adwords/' . $mofile;

            if ( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-google-adwords/ folder
                load_textdomain( 'edd-google-adwords', $mofile_global );
            } elseif ( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-google-adwords/languages/ folder
                load_textdomain( 'edd-google-adwords', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-google-adwords', false, $lang_dir );
            }
        }
        
        /*
         * Activation function fires when the plugin is activated.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public static function activation() {
            // nothing
        }

        /*
         * Uninstall function fires when the plugin is being uninstalled.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public static function uninstall() {
            // nothing
        }
    }

    /**
     * The main function responsible for returning the one true EDD_GOOGLE_ADWORDS
     * instance to functions everywhere
     *
     * @since       1.0.0
     * @return      \EDD_GOOGLE_ADWORDS The one true EDD_GOOGLE_ADWORDS
     */
    function EDD_GOOGLE_ADWORDS_load() {

        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            return EDD_GOOGLE_ADWORDS::instance();
        }
    }

    /**
     * The activation & uninstall hooks are called outside of the singleton because WordPress doesn't
     * register the call from within the class hence, needs to be called outside and the
     * function also needs to be static.
     */
    register_activation_hook( __FILE__, array( 'EDD_GOOGLE_ADWORDS', 'activation' ) );
    register_uninstall_hook( __FILE__, array( 'EDD_GOOGLE_ADWORDS', 'uninstall') );

    add_action( 'plugins_loaded', 'EDD_GOOGLE_ADWORDS_load' );

} // End if class_exists check

/*
 * Plugin action links
 */
function edd_gadw_plugin_action_links( $links, $file ) {

    // Get out if EDD is not active
    if( ! function_exists( 'EDD' ) ) {
        return $links;
    }

    $settings_link = '<a href="' . admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions&section=edd-gadw' ) . '">' . esc_html__( 'Configure', 'edd-google-adwords' ) . '</a>';
    if ( $file == 'edd-google-adwords/edd-google-adwords.php' )
        array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links', 'edd_gadw_plugin_action_links', 10, 2 );