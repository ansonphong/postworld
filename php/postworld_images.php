<?php

///// INCLUDES /////
// AQ RESIZER : Module for resizing images 
$aq_resizer_include = $template_paths['POSTWORLD_PATH'].'lib/wordpress/aq_resizer.php';
include_once $aq_resizer_include;

///// GET FIRST IMAGE : FULL SIZE : URL, WIDTH, HEIGHT & ID /////
function first_image_obj( $post_id ) {
	$args = array(
		'numberposts' => 1,
		'order' => 'ASC',
		'post_mime_type' => 'image',
		'post_parent' => $post_id,
		'post_status' => null,
		'post_type' => 'attachment',
	);
	$attachments = get_children( $args );
	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$first_image_obj['url'] = $image_attributes[0];
			$first_image_obj['width'] = $image_attributes[1];
			$first_image_obj['height'] = $image_attributes[2];
			$first_image_obj['ID'] = $attachment->ID;
			return $first_image_obj;
		}
	}
	else {
		return false;
		}
}

///// GET OBJECT OF AN IMAGE ATTACHMENT ATTRIBUTES /////
function image_obj( $attachment_id, $size = 'full' ){
	$image_attributes = wp_get_attachment_image_src( $attachment_id, $size );
	$first_image_obj['url'] = $image_attributes[0];
	$first_image_obj['width'] = $image_attributes[1];
	$first_image_obj['height'] = $image_attributes[2];
	$first_image_obj['ID'] = $attachment_id; //$attachment->ID;
	return $first_image_obj;
}


///// GET OBJECT OF ALL REGISTERED IMAGE ATTRIBUTES /////
function registered_images_obj(){
	global $_wp_additional_image_sizes;
		$sizes = array();
		foreach( get_intermediate_image_sizes() as $s ){
			// Get standard image sizes
			$sizes[ $s ] = array();
			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
				$sizes[ $s ]['width'] = get_option( $s . '_size_w' );
				$sizes[ $s ]['height'] = get_option( $s . '_size_h' );
				$sizes[ $s ]['crop'] = get_option( $s . '_crop' );
			}else{
				// Get additional image sizes
				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
					$sizes[ $s ] = array(
						'width'		=>	$_wp_additional_image_sizes[ $s ]['width'],
						'height'	=>	$_wp_additional_image_sizes[ $s ]['height'],
						'crop'		=>	$_wp_additional_image_sizes[ $s ]['crop'],
						);
			}
		}
		return $sizes;
	}


///// UPLOADS REMOTE URL TO WP IMAGE LIBRARY /////
function url_to_media_library( $image_url, $post_id =null){

	// Check if it's a URL string
	if ( strpos($image_url,'://') == false ) {
	    return array( 'error' => 'Not a URL.' );
	}

	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents($image_url);
	$filename = basename($image_url);
	if(wp_mkdir_p($upload_dir['path']))
	    $file = $upload_dir['path'] . '/' . $filename;
	else
	    $file = $upload_dir['basedir'] . '/' . $filename;
	file_put_contents($file, $image_data);

	// Strip off the file extension
	$file_title =preg_replace("/\\.[^.\\s]{3,4}$/", "", $filename);

	$wp_filetype = wp_check_filetype($filename, null );
	$attachment = array(
	    'post_mime_type' => $wp_filetype['type'],
	    'post_title' => sanitize_file_name($file_title),
	    'post_content' => '',
	    'post_status' => 'inherit'
	);
	if(!is_null($post_id))
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
	else
		$attach_id = wp_insert_attachment( $attachment, $file);
	
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;
}

?>