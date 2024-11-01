<?php
/**
 * Plugin configurations
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$GLOBALS['webp_gallery_video_aliases'] = array(
	'Webp_Gallery_Video_Install'          => 'includes/class/webp-class-video-gallery-install',
	'Webp_Gallery_Video_Template_Loader'  => 'includes/class/webp-class-video-gallery-template-loader',
	'Webp_Gallery_Video_Ajax'             => 'includes/class/webp-class-video-gallery-ajax',
	'Webp_Gallery_Video_Widgets'          => 'includes/class/webp-class-video-gallery-widgets',
	'Webp_Gallery_Video_Widget'           => 'includes/class/webp-class-video-gallery-widget',
	'Webp_Gallery_Video_Shortcode'        => 'includes/class/webp-class-video-gallery-shortcode',
	'Webp_Gallery_Video_Frontend_Scripts' => 'includes/class/webp-class-video-gallery-front-scripts',
	'Webp_Gallery_Video_Admin'            => 'includes/admin/class-gallery-video-admin',
	'Webp_Gallery_Video_Admin_Assets'     => 'includes/admin/class-gallery-video-admin-assets',
	'Webp_Gallery_Video_General_Options'  => 'includes/admin/class-gallery-video-general-options',
	'Webp_Gallery_Video_Galleries'        => 'includes/admin/class-gallery-video-galleries',
	'Webp_Gallery_Video_Lightbox_Options' => 'includes/admin/class-gallery-video-lightbox-options',
	'Webp_Gallery_Video_Featured_Plugins' => 'includes/admin/class-gallery-video-featured-plugins',
);

/**
 * @param $webpclassname
 *
 * @throws Exception
 */
function webp_gallery_video_aliases( $webpclassname ) {
	global $webp_gallery_video_aliases;

	/**
	 * We do not touch classes that are not related to us
	 */
	if ( ! strstr( $webpclassname, 'Webp_Gallery_Video_' ) ) {
		return;
	}

	if ( ! key_exists( $webpclassname, $webp_gallery_video_aliases ) ) {
		throw new Exception( 'trying to load "' . $webpclassname . '" class that is not registered in config file.' );
	}

	$path = Webp_Gallery_Video()->plugin_path() . '/' . $webp_gallery_video_aliases[ $webpclassname ] . '.php';

	if ( ! file_exists( $path ) ) {

		throw new Exception( 'the given path for class "' . $webpclassname . '" is wrong, trying to load from ' . $path );

	}

	require_once $path;

	if ( ! interface_exists( $webpclassname ) && ! class_exists( $webpclassname ) ) {

		throw new Exception( 'The class "' . $webpclassname . '" is not declared in "' . $path . '" file.' );

	}
}

spl_autoload_register( 'webp_gallery_video_aliases' );