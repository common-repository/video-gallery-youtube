<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="free_version_banner" <?php if (isset($_COOKIE['portfolioGalleryBannerShow']) && $_COOKIE['portfolioGalleryBannerShow'] == "no") echo 'style="display:none"'; ?> >
    <a class="close_free_banner">+</a>
    <a class="get_full_version" href="http://webpace.net/wordpress-video-gallery/" target="_blank">Upgrade To The Full Version</a>

    <div style="clear: both;"></div>
    <div class="description_text"><p>Want to have more customizable options? "Upgrade to the Full Version" and get more out of this plugin. We are grateful for each of our customers.</p></div>
    <div style="clear: both;"></div>
</div>