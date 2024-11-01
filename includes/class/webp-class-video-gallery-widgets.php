<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Class Webp_Gallery_Video_Widgets
 */
class Webp_Gallery_Video_Widgets{

	/**
	 * Register WebPace  Gallery Video Widget
	 */
	public static function init(){
		register_widget( 'Webp_Gallery_Video_Widget' );
	}
}
