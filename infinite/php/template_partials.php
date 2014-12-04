<?php

function pw_builtin_template_partials( $template_partials ){
	// Define all the template partials 

	// GALLERY OPTIONS
	$template_partials = pw_set_obj( $template_partials,
		'editPost.pwMeta.gallery',		// partials model path
		'i_gallery_options'				// function name
		);

	// COLUMNS
	$template_partials = pw_set_obj( $template_partials,
		'editPost.pwMeta.post_content.columns',	// partials model path
		'i_content_columns_option'				// function name
		);

	// IMAGE DOWNLOAD
	$template_partials = pw_set_obj( $template_partials,
		'editPost.pwMeta.image.download',		// partials model path
		'i_download_image_option'				// function name
		);


	// SOCIAL SHARE LINKS
	$template_partials = pw_set_obj( $template_partials,
		'viewPost.social.shareLinks',			// partials model path
		'pw_social_share'						// function name
		);

	// SOCIAL POST WIDGETS
	$template_partials = pw_set_obj( $template_partials,
		'viewPost.social.widgets',				// partials model path
		'pw_social_widgets'						// function name
		);

	return $template_partials;
}

add_filter( 'pw_template_partials', 'pw_builtin_template_partials' )

?>