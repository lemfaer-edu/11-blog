function video_width(video, local, scale) {
	var $video = jQuery(video);
	var locwidth = jQuery(local).width();
	$video.height(scale * (locwidth / $video.width()) * $video.height());
	$video.width(scale * locwidth);
}
