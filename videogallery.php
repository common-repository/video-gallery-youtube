<?php

/*
Plugin Name: Video Gallery Youtube
Plugin URI: https://webpace.net/wordpress-video-gallery/
Description: Video Gallery plugin is one of the best ways to add video content to any website. Easily add unlimited YouTube and Vimeo videos.
Version: 1.0.0
Author: webpace
Author URI: https://webpace.net
License: GNU/GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
*/


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( 'configiration.php' );

if ( ! class_exists( 'Webp_Gallery_Video' ) ) :

    final class Webp_Gallery_Video {

        /**
         * Version of plugin
         * @var float
         */
        public $version = '1.0.0';
        /**
         * @var int
         */
        private $project_id = 5;

        /**
         * @var string
         */
        private $project_plan = 'free';

        /**
         * @var string
         */
        private $slug = 'video-gallery';

        /**
         * Instance of Webp_Gallery_Video_Admin class to manage admin
         * @var Webp_Gallery_Video_Admin instance
         */
        public $admin = null;

        /**
         * Instance of Webp_Gallery_Video_Template_Loader class to manage admin
         * @var Webp_Gallery_Video_Template_Loader instance
         */
        public $template_loader = null;

        /**
         * The single instance of the class.
         *
         * @var Webp_Gallery_Video
         */
        protected static $_instance = null;

        /**
         * Main Webp_Gallery_Video Instance.
         *
         * Ensures only one instance of Webp_Gallery_Video is loaded or can be loaded.
         *
         * @static
         * @see Webp_Gallery_Video()
         * @return Webp_Gallery_Video - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gallery-video' ), '2.1' );
        }

        /**
         * Unserializing instances of this class is forbidden.
         */
        private function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gallery-video' ), '2.1' );
        }
        

        /**
         * Webp_Gallery_Video Constructor.
         */
        private function __construct() {

            $this->define_constants();
            $this->includes();
            $this->init_hooks();
            global $Webp_Gallery_Video_url,$Webp_Gallery_Video_path;
            $Webp_Gallery_Video_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
            $Webp_Gallery_Video_url = plugins_url('', __FILE__ );
            do_action( 'Webp_Gallery_Video_loaded' );
        }

        /**
         * Hook into actions and filters.
         */
        private function init_hooks() {
            register_activation_hook( __FILE__, array( 'Webp_Gallery_Video_Install', 'install' ) );
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'plugins_loaded', array($this,'load_plugin_textdomain') );
            add_action( 'widgets_init', array( 'Webp_Gallery_Video_Widgets', 'init' ) );
            
        }

        /**
         * Define Video Gallery Constants.
         */
        private function define_constants() {
            $this->define( 'GALLERY_VIDEO_PLUGIN_URL', plugin_dir_url(__FILE__));
            $this->define( 'GALLERY_VIDEO_PLUGIN_FILE', __FILE__ );
            $this->define( 'GALLERY_VIDEO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'GALLERY_VIDEO_VERSION', $this->version );
            $this->define( 'GALLERY_VIDEO_IMAGES_PATH', $this->plugin_path(). DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR );
            $this->define( 'GALLERY_VIDEO_IMAGES_URL', untrailingslashit($this->plugin_url() . '/assets/images/' ));
            $this->define( 'GALLERY_VIDEO_TEMPLATES_PATH', $this->plugin_path() . DIRECTORY_SEPARATOR . 'templates');
            $this->define( 'GALLERY_VIDEO_TEMPLATES_URL', untrailingslashit($this->plugin_url()) . '/templates/');
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * What type of request is this?
         * string $type ajax, frontend or admin.
         *
         * @return bool
         */
        private function is_request( $type ) {
            switch ( $type ) {
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined( 'DOING_AJAX' );
                case 'cron' :
                    return defined( 'DOING_CRON' );
                case 'frontend' :
                    return  ! is_admin() && ! defined( 'DOING_CRON' );
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes() {
            include_once( 'includes/webp-video-gallery-functions.php' );
            include_once( 'includes/webp-video-gallery-videos-functions.php' );
            if ( $this->is_request( 'admin' ) ) {
                include_once( 'includes/admin/gallery-video-admin-functions.php' );
            }
        }




        /**
         * Load plugin text domain
         */
        public function load_plugin_textdomain(){
            load_plugin_textdomain( 'gallery-video', false, $this->plugin_path() . '/languages/' );
        }

        /**
         * Init Image gallery when WordPress `initialises.
         */
        public function init() {
            // Before init action.
            do_action( 'before_Webp_Gallery_Video_init' );


            $this->template_loader = new Webp_Gallery_Video_Template_Loader();

            if ( $this->is_request( 'admin' ) ) {

                $this->admin = new Webp_Gallery_Video_Admin();

                new Webp_Gallery_Video_Admin_Assets();

            }

            new Webp_Gallery_Video_Frontend_Scripts();

            new Webp_Gallery_Video_Ajax();

            new Webp_Gallery_Video_Shortcode();


            add_action('webp_video_gallery_slider_view', array($this, 'slider_view_scripts'), 10, 2);

            // Init action.
            do_action( 'Webp_Gallery_Video_init' );
        }

        public function slider_view_scripts($has_vimeo, $has_youtube) {
            if($has_vimeo === true) {
                wp_enqueue_script('webp_video_gallery_vimeo', Webp_Gallery_Video()->plugin_url().'/assets/js/vimeo.lib.js');
            }

            if($has_youtube === true) {
                wp_enqueue_script('webp_video_gallery_youtube', Webp_Gallery_Video()->plugin_url().'/assets/js/youtube.lib.js');
            }

        }

        /**
         * Get Ajax URL.
         * @return string
         */
        public function ajax_url() {
            return admin_url( 'admin-ajax.php', 'relative' );
        }

        /**
         * Video Gallery Plugin Path.
         *
         * @var string
         * @return string
         */
        public function plugin_path(){
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

        /**
         * Video Gallery Plugin Url.
         * @return string
         */
        public function plugin_url(){
            return plugins_url('', __FILE__ );
        }

        /**
         * @return int
         */
        public function get_project_id()
        {
            return $this->project_id;
        }

        /**
         * @return string
         */
        public function get_project_plan()
        {
            return $this->project_plan;
        }

        public function get_slug()
        {
            return $this->slug;
        }
        /**
         * Get plugin version.
         *
         * @return string
         */
        public function get_version() {
            return $this->version;
        }

        public function template_path()
        {
            return apply_filters('gallery_video_template_path', 'gallery-video/');
        }
    }

endif;

function Webp_Gallery_Video(){
    return Webp_Gallery_Video::instance();
}

$GLOBALS['Webp_Gallery_Video'] = Webp_Gallery_Video();
