<?php
/**
 * Plugin Name:     WPeMatico Extras
 * Plugin URI:      @todo
 * Description:     WPeMatico Add-on starter point Boilerplate plugin 
 * Version:         1.0.2
 * Author:          etruel
 * Author URI:      http://www.netmdp.com
 * Text Domain:     boilerplate
 *
 * @package         etruel\Boilerplate
 * @author          Esteban Truelsegaard
 * @copyright       Copyright (c) 2016
 *
 *
 * - Find all instances of @todo in the plugin and update the relevant
 *   areas as necessary.
 *
 * - All functions that are not class methods MUST be prefixed with the
 *   plugin name, replacing spaces with underscores. NOT PREFIXING YOUR
 *   FUNCTIONS CAN CAUSE PLUGIN CONFLICTS!
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Plugin version
if( ! defined('WPEMTICO_EXTRAS_VER') ) {
	define('WPEMTICO_EXTRAS_VER', '1.0' );
}

if( !class_exists( 'WPeMatico_Extras' ) ) {


	
    /**
     * Main Boilerplate class
     *
     * @since       1.0.0
     */
    class WPeMatico_Extras {

        /**
         * @var         Boilerplate $instance The one true Boilerplate
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true Boilerplate
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new self();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
       public static function setup_constants() {
			// Plugin root file
			if(!defined('WPEMATICO_EXTRAS_ROOT_FILE')) {
				define('WPEMATICO_EXTRAS_ROOT_FILE', __FILE__ );
			}
            // Plugin path
			if(!defined('WPEMATICO_EXTRAS_DIR')) {
				define('WPEMATICO_EXTRAS_DIR', plugin_dir_path( __FILE__ ) );
			}
            // Plugin URL
			if(!defined('WPEMATICO_EXTRAS_URL')) {
				define('WPEMATICO_EXTRAS_URL', plugin_dir_url( __FILE__ ) );
			}
			if(!defined('WPEMATICO_EXTRAS_STORE_URL')) {
				define('WPEMATICO_EXTRAS_STORE_URL', 'https://etruel.com'); 
			} 
			if(!defined('WPEMATICO_EXTRAS_ITEM_NAME')) {
				define('WPEMATICO_EXTRAS_ITEM_NAME', 'WPeMatico Boilerplate'); 
			} 
        }


        /**
         * Include necessary files
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
         public static function includes() {
            // Include scripts
		    require_once WPEMATICO_EXTRAS_DIR . 'includes/functions.php'; 
        }


        /**
         * Run action and filter hooks
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         *
         */
         public static function hooks() {
            // Register settings
            add_action('wpematico_cronjob', array(__CLASS__, 'settings_cronjob') );
            add_filter('wpematico_before_get_content', 'wpematico_extras_aux_curl', 10, 3);
        }
        
        public static function settings_cronjob() {
            ?>
            
            <span class="coderr b"><i> php -q <?php echo WPEMATICO_EXTRAS_DIR . "includes/wpe-cron.php"; ?></i></span><br />
            
            <?php
        }
        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
         public static function load_textdomain() {
            // Set filter for language directory
            $lang_dir = WPEMATICO_EXTRAS_DIR . '/languages/';
            $lang_dir = apply_filters( 'wpematico_extras_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'wpematico_extras' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'wpematico_extras', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/wpematico_extras/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/wpematico_extras/ folder
                load_textdomain( 'wpematico_extras', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/wpematico_extras/languages/ folder
                load_textdomain( 'wpematico_extras', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'wpematico_extras', false, $lang_dir );
            }
        }
        
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true Boilerplate
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Boilerplate The one true Boilerplate
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function WPeMatico_Extras_load() {
    if( !class_exists( 'WPeMatico' ) ) {
        
    } else {
         return WPeMatico_Extras::instance();
    }
   
    
}
add_action( 'plugins_loaded', 'WPeMatico_Extras_load', 999);

