"use strict";
function galleryVideoIsotope(elem, option) {
    if (typeof elem.isotope == 'function') {
        elem.isotope(option);
    }
    else {
        elem.webpacemicro(option);
    }
}
function galleryVideolightboxInit() {
    
        jQuery('.gallery-video-content').each(function () {
            var galleryVideoId = jQuery(this).attr('data-gallery-video-id');
            jQuery(".slider-content.clone .image-block_" + galleryVideoId + " a").removeClass('group' + galleryVideoId);
            jQuery(this).find("a[href*='youtu'],a[href*='vimeo']").removeClass('cboxElement ').addClass('vg_responsive_lightbox');
            if(gallery_video_view === 'content-slider'){
                jQuery('div.slider-content.clone').find("a[href*='youtu'],a[href*='vimeo']").removeClass('vg_responsive_lightbox');
                jQuery('.right-block').find("a[href*='youtu'],a[href*='vimeo']").removeClass('vg_responsive_lightbox');
            }
            jQuery('.vg_responsive_lightbox').lightboxVideo();
        });


}
jQuery(document).ready(function () {
    galleryVideolightboxInit();
});