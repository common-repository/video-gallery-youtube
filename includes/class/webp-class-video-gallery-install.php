<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Webp_Gallery_Video_Install {

	/**
	 * Check Gallery Video version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_version() {
		if(get_option( 'webp_gallery_video_version' ) !== Webp_Gallery_Video()->version ){
			self::install();
			update_option( 'webp_gallery_video_version',Webp_Gallery_Video()->version );
		}
	}
    /**
     * Install  Gallery Image.
     */
    public static function install() {
        if ( ! defined( 'GALLERY_VIDEO_INSTALLING' ) ) {
            define( 'GALLERY_VIDEO_INSTALLING', true );
        }
        self::create_tables();

        self::install_options();

        do_action( 'gallery_video_installed' );
    }

    public static function install_options() {

        if( !get_option( 'gallery_video_lightbox_type' ) ) {
                update_option( 'gallery_video_lightbox_type', 'new_type' );
        }

        $lightbox_new_options = array(
            'gallery_video_lightbox_lightboxView'                               => 'view1',
            'gallery_video_lightbox_speed_new'                                  => '600',
            'gallery_video_lightbox_overlayClose_new'                           => 'true',
            'gallery_video_lightbox_loop_new'                                   => 'true',
        );

        if(!get_option( 'gallery_video_lightbox_lightboxView' )) {
            foreach ($lightbox_new_options as $name => $value) {
                add_option( $name, $value);
            }
        }
        global $wpdb;
        if ( ! get_option( 'gallery_video_disable_right_click' ) ) {
            update_option( 'gallery_video_disable_right_click', 'off' );
        }
        $imagesAllFieldsInArray = $wpdb->get_results( "DESCRIBE " . $wpdb->prefix . "webpace_videogallery_videos", ARRAY_A );
        $forUpdate              = 0;
        foreach ( $imagesAllFieldsInArray as $portfoliosField ) {
            if ( $portfoliosField['Field'] == 'thumb_url' ) {
                $forUpdate = 1;
            }
        }
        if ( $forUpdate != 1 ) {
            $wpdb->query( "ALTER TABLE " . $wpdb->prefix . "webpace_videogallery_videos ADD thumb_url text DEFAULT NULL" );
        }
        $imagesAllFieldsInArray2 = $wpdb->get_results( "DESCRIBE " . $wpdb->prefix . "webpace_videogallery_galleries", ARRAY_A );
        $fornewUpdate            = 0;
        foreach ( $imagesAllFieldsInArray2 as $portfoliosField2 ) {
            if ( $portfoliosField2['Field'] == 'display_type' ) {
                $fornewUpdate = 1;
            }
        }
        if ( $fornewUpdate != 1 ) {
            $wpdb->query( "ALTER TABLE " . $wpdb->prefix . "webpace_videogallery_galleries ADD display_type integer DEFAULT '2' " );
            $wpdb->query( "ALTER TABLE " . $wpdb->prefix . "webpace_videogallery_galleries ADD content_per_page integer DEFAULT '5' " );
        }
        $table_name = $wpdb->prefix . 'webpace_videogallery_params';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
            $query                      = "SELECT name,value FROM " . $table_name;
            $video_gallery_table_params = $wpdb->get_results( $query );
        }
        $table_name_galleries = $wpdb->prefix . "webpace_videogallery_galleries";
        $table_name_videos = $wpdb->prefix . "webpace_videogallery_videos";
        if(function_exists('webp_issetTableColumn')) {
            if ( ! webp_issetTableColumn( $table_name_galleries, 'autoslide' ) ) {
                $wpdb->query( "ALTER TABLE " . $table_name_galleries . " ADD autoslide varchar(3) DEFAULT 'on'");
            }
            if ( ! webp_issetTableColumn( $table_name_videos, 'show_controls' ) ) {
                $wpdb->query( "ALTER TABLE " . $table_name_videos . " 
                ADD COLUMN show_controls varchar(3) DEFAULT 'on',
                ADD COLUMN show_info varchar(3) DEFAULT 'on' " );
            }
        }
    }


	private static function create_tables() {
		global $wpdb;

		$sql_webpace_videogallery_videos = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "webpace_videogallery_videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `videogallery_id` varchar(200) DEFAULT NULL,
  `description` text,
  `image_url` text,
  `sl_url` varchar(128) DEFAULT NULL,
  `sl_type` text NOT NULL,
  `link_target` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) unsigned DEFAULT NULL,
  `published_in_sl_width` tinyint(4) unsigned DEFAULT NULL,
  `thumb_url` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
)   DEFAULT CHARSET=utf8 AUTO_INCREMENT=5";

		$sql_webpace_videogallery_galleries = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "webpace_videogallery_galleries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sl_height` int(11) unsigned DEFAULT NULL,
  `sl_width` int(11) unsigned DEFAULT NULL,
  `pause_on_hover` text,
  `videogallery_list_effects_s` text,
  `description` text,
  `param` text,
  `sl_position` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` text,
   `webpace_sl_effects` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
)   DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ";

		$table_name = $wpdb->prefix . "webpace_videogallery_videos";
		$sql_2      = "
INSERT INTO 

`" . $table_name . "` (`id`, `name`, `videogallery_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES
(1, 'The Best Of Extreme Sport', '1', '<p>People are Awesome 2016; Best Of Web. A Compilation of Different Extreme sports.</p>', 'https://www.youtube.com/embed/PF0L3gvSVcg', 'https://webpace.net/', 'video', 'on', 0, 1, NULL),
(2, 'GARAGE RACE', '1', '<p>Video of GARAGE RACE 2016 is finally here - something between a movie and reportage covering all day including the crashes, smiles and races. Enjoy!</p>', 'https://player.vimeo.com/video/163167824', 'https://webpace.net/', 'video', 'on', 1, 1, NULL),
(3, 'That was Paris', '1', '<h6>That was Paris </h6><p>I’ve recently moved back to Paris and have been able to rediscover this city. Paris is often represented by monuments, but videos didn’t seem to show the atmosphere, the people. </p><p>As a city, the pace is varied which I tried to highlight with timelapse and slow motion. It’s a series of moments captured to show what Paris is to me. Its a moment in time.</p>', 'http://player.vimeo.com/video/67841329', 'https://webpace.net/', 'video', 'on', 2, 1, NULL),
(4, 'Introducing New York City', '1', '<p>Start exploring New York City with Lonely Planet’s video guide to getting around, when to go and the top things to do while you are there.</p>', 'https://www.youtube.com/embed/AUuHyXguUsg', 'https://webpace.net/', 'video', 'on', 3, 1, NULL),
(5, 'The Panda Rabbit!', '1', '<p>The Panda Rabbit is a hybrid animal who isolates himself from the world because he is different and unloved. He finds solace in his TV but is soon sucked into the television world and finds it much less enticing than he thought. The Panda Rabbit battles his way through the television world and eventually makes it back to reality. He is then greeted by a stranger that can change his life...</p>', 'http://player.vimeo.com/video/29994384', 'https://webpace.net/', 'video', 'on', 4, 1, NULL),
(6, 'SURFING 1000 FRAMES PER SECOND', '1', '<p>All images where shot using The Phantom Flex, Phantom Miro M-320S and the new Phantom 4K Flex with Arri Ultra prime lenses and Chris Bryan Films custom underwater housings.</p>', 'http://player.vimeo.com/video/108799588', 'https://webpace.net/', 'video', 'on', 5, 1, NULL),
(7, 'Girls Are Awesome', '1', '<p>Girls Are Awesome (Parkour Edition).</p>', 'https://www.youtube.com/embed/JZfR9buMHFY', 'https://webpace.net/', 'video', 'on', 6, 1, NULL),
(8, '360 Slow Motion', '1', '<h6>Flying through space and time using the new high speed camera</h6>', 'https://www.youtube.com/embed/bVx-PFkDf_E', 'https://webpace.net/', 'video', 'on', 7, 1, NULL)";

		$table_name = $wpdb->prefix . "webpace_videogallery_galleries";
		$sql_3      = "

INSERT INTO `$table_name` (`id`, `name`, `sl_height`, `sl_width`, `pause_on_hover`, `videogallery_list_effects_s`, `description`, `param`, `sl_position`, `ordering`, `published`, `webpace_sl_effects`) VALUES
(1, 'My First Video Gallery', 375, 600, 'on', 'random', '4000', '1000', 'center', 1, '300', '5')";


		$wpdb->query( $sql_webpace_videogallery_videos );
		$wpdb->query( $sql_webpace_videogallery_galleries );


		if ( ! $wpdb->get_var( "select count(*) from " . $wpdb->prefix . "webpace_videogallery_videos" ) ) {
			$wpdb->query( $sql_2 );
		}
		if ( ! $wpdb->get_var( "select count(*) from " . $wpdb->prefix . "webpace_videogallery_galleries" ) ) {
			$wpdb->query( $sql_3 );
		}
		
	}

	/**
	 * Update DataBase
	 */

}