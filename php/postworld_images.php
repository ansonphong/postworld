<?php

///// INCLUDES /////
// AQ RESIZER : Module for resizing images 
$aq_resizer_include = $template_paths['POSTWORLD_PATH'].'lib/wordpress/aq_resizer.php';
include_once $aq_resizer_include;


///// REQUIRE IMAGE /////
function pw_require_image( $posts = array() ){
	// Returns only the posts with images

	$new_posts = array();

	// Iterate through each post
	foreach( $posts as $post ){
		if(
			isset( $post['image'] ) &&
			!empty( $post['image']['sizes'] ) &&
			is_array( $post['image']['sizes'] )
			){
			$has_image = true;
			$missing_images = 0;
			// Check through each image size, if it's null
			foreach( $post['image']['sizes'] as $image_size ){
				if( $image_size['url'] == null ){
					$missing_images ++;
				}
			}
			// If the number of null image sizes is equal to the total number of image sizes
			if( $missing_images == count( $post['image']['sizes'] ) )
				// It's missing an image
				$has_image = false;
			// If it has an image, add it to the posts
			if( $has_image )
				array_push( $new_posts, $post );
		}

	}
	// Return the posts with images
	return $new_posts;
}


///// GENERATE IMAGE TAGS /////

function pw_image_tag_filters( $vars ){

	extract( $vars );

	///// FILTERS /////
	// Tag filters to process
	// Available variables are : $width, $height, $ratio
	$tag_filters = array();

	global $pwSiteGlobals;
	$custom_tags = ( isset( $pwSiteGlobals['images']['tags'] ) ) ?
		$pwSiteGlobals['images']['tags'] :
		array();

	$default_tags = array(
		// SQUARE
		array(
			"tag"		=>	"square",
			"condition" => "$ratio > .8 && $ratio < 1.2",
			),

		// TALL
		array(
			"tag"		=>	"tall",
			"condition" => "$ratio <= .8",
			),
		array(
			"tag"		=>	"x-tall",
			"condition" => "$ratio <= .5",
			),
		array(
			"tag"		=>	"xx-tall",
			"condition" => "$ratio <= .33",
			),

		// WIDE
		array(
			"tag"		=>	"wide",
			"condition" => "$ratio >= 1.2",
			),
		array(
			"tag"		=>	"x-wide",
			"condition" => "$ratio >= 2",
			),
		array(
			"tag"		=>	"xx-wide",
			"condition" => "$ratio >= 3",
			),

		// DEFINITION
		array(
			"tag"		=>	"HD",
			"condition" => "$width >= 1024 && $height >= 1024",
			),
		array(
			"tag"		=>	"XHD",
			"condition" => "$width >= 2048 && $height >= 2048",
			),
		);

	// Merge Filters
	// TODO : Iterate through each custom tag, and over-ride defaults with conditions
	$tag_filters = array_merge( $default_tags, $custom_tags );

	return $default_tags;

}

function pw_generate_image_tags( $vars = array() ){
	/*
		$vars = array(
			'width'		=> [integer]
			'height'	=> [integer]
			'ratio'		=> [number/decimal]
		)
	*/
	if( empty( $vars ) )
		return false;

	if( empty($vars['width']) || empty($vars['height']) )
		return false;

	extract( $vars );

	// Set Defaults
	if( !isset( $ratio ) )
		$ratio = $width / $height;

	// Get Tag Filters
	$tag_filters = pw_image_tag_filters( array( "width" => $width, "height" =>  $height, "ratio" => $ratio ) );

	// Setup tags object
	$tags = array();

	///// PROCESS FILTERS /////
	// Iterate through each filter
	foreach( $tag_filters as $tag_filter ){
		$condition = ( string ) $tag_filter['condition'];
		$condition = "return (" . $condition . ");";

		$boolean = (bool) eval( $condition );

		if( $boolean == true )
			$tags[] = $tag_filter['tag'];

	}
	return $tags;

}

///// GET IMAGE /////
function pw_get_image( $vars ){
	extract($vars);

	$image = array();
	$image = wp_get_attachment_metadata( $image_id );
	$image['url'] = wp_get_attachment_url($image_id);

	foreach( $image['sizes'] as $key => $value ){
		$image_size_meta = wp_get_attachment_image_src( $image_id, $key );
		$image['sizes'][$key]['url'] = $image_size_meta[0];
	}

	return $image;
}


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


function pw_get_featured_image_obj( $post_id, $size = 'full' ){
	// Get attachment ID from post ID
	$attachment_id = get_post_thumbnail_id( $post_id );
	// Get the image object
	$image_obj = pw_get_image_obj( $attachment_id, $size );
	// Return image object
	return $image_obj;
}

///// GET OBJECT OF AN IMAGE ATTACHMENT ATTRIBUTES /////
function pw_get_image_obj( $attachment_id, $size = 'full' ){
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

function grab_image($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	
    $raw=curl_exec_follow($ch);;  	
    if(curl_errno($ch))
	{
	    echo 'Curl error: ' . curl_error($ch);
		curl_close ($ch);
		return false;
	}    
    else {
	   	$file_size = file_put_contents($saveto,$raw);
		curl_close ($ch);
		return $file_size;
    }
	return false;
}

function curl_exec_follow($ch, &$maxredirect = null) {
  
  // we emulate a browser here since some websites detect
  // us as a bot and don't let us do our job
  $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5)".
                " Gecko/20041107 Firefox/1.0";
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent );

  $mr = $maxredirect === null ? 5 : intval($maxredirect);

  if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  } else {
    
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    if ($mr > 0)
    {
      $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
      $newurl = $original_url;
      
      $rch = curl_copy_handle($ch);
      
      curl_setopt($rch, CURLOPT_HEADER, true);
      curl_setopt($rch, CURLOPT_NOBODY, true);
      curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
      do
      {
        curl_setopt($rch, CURLOPT_URL, $newurl);
        $header = curl_exec($rch);
        if (curl_errno($rch)) {
          $code = 0;
        } else {
          $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
          if ($code == 301 || $code == 302) {
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $newurl = trim(array_pop($matches));
            
            // if no scheme is present then the new url is a
            // relative path and thus needs some extra care
            if(!preg_match("/^https?:/i", $newurl)){
              $newurl = $original_url . $newurl;
            }   
          } else {
            $code = 0;
          }
        }
      } while ($code && --$mr);
      
      curl_close($rch);
      
      if (!$mr)
      {
        if ($maxredirect === null)
        trigger_error('Too many redirects.', E_USER_WARNING);
        else
        $maxredirect = 0;
        
        return false;
      }
      curl_setopt($ch, CURLOPT_URL, $newurl);
    }
  }
  return curl_exec($ch);
}

///// UPLOADS REMOTE URL TO WP IMAGE LIBRARY /////
function url_to_media_library( $image_url, $post_id = 0){

	// Check if it's a URL string
	if ( strpos($image_url,'//') == false )
	    return array( 'error' => 'Not a URL.' );

	// STRIP QUERY VARS : Remove everything after '?' in the image URL
	$image_url_split = explode('?', $image_url);
	$image_url = $image_url_split[0];

	// SETUP
	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents($image_url); // grab_image($image_url,$file);

	$base_file_name = basename($image_url);
	$filename = $post_id."-".$base_file_name;
	if(wp_mkdir_p($upload_dir['path']))
	    $file = $upload_dir['path'] . '/' . $filename;
	else
	    $file = $upload_dir['basedir'] . '/' . $filename;
	file_put_contents($file, $image_data);

	// Check if post exists
	if($image_data !== FALSE){
		$postdata = get_post( $post_id, "ARRAY_A" );
	
		// Define the title
		if( $post_id != 0 && isset($postdata['post_title'])  ){
			$file_title = $postdata['post_title'];
		} else{
			// Strip off the file extension
			$file_title =preg_replace("/\\.[^.\\s]{3,4}$/", "", $base_file_name);
		}
	
		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title' => $file_title,
		    'post_content' => '',
		    'post_status' => 'inherit'
		);
		if( $post_id != 0 )
			$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		else
			$attach_id = wp_insert_attachment( $attachment, $file);
		
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );
	
		return $attach_id;
	}
	else echo  "<br />IMAGE NOT MIGRATED<br />";
	return 0;
}





/**
 * Retrieve galleries from the passed post's content.
 * @param int|WP_Post $post Optional. Post ID or object.
 * @param bool        $html Whether to return HTML or data in the array.
 * @return array A list of arrays, each containing gallery data and srcs parsed
 *		         from the expanded shortcode.
 */
/*
function pw_get_post_galleries_atts( $post ) {
	// Based on `get_post_galleries`
	if ( ! $post = get_post( $post ) )
		return array();

	if ( ! has_shortcode( $post->post_content, 'gallery' ) )
		return array();

	$galleries = array();

	// Find all gallery shortcodes in the post
	// And return an array of associative arrays with the shortcode attributes
	if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $shortcode ) {
			if ( 'gallery' === $shortcode[2] ) {
				$data = shortcode_parse_atts( $shortcode[3] );
				$data['src'] = array_values( array_unique( $srcs ) );
				$galleries[] = $data;
			}
		}
	}

	return $galleries;
}
*/


function pw_get_post_galleries_atts( $post ){
	get_post_galleries( $post, false );

}


function pw_get_post_galleries_attachment_ids( $post ){
	// Returns all the attachment IDs for all gallery images in the post's galleries

	$galleries = get_post_galleries( $post, false );

	if( !empty( $galleries ) ){
		$attachment_ids = array();		
		
		foreach( $galleries as $gallery ){
			$gallery_ids = explode( ',', $gallery['ids']);
			$attachment_ids = array_merge( $attachment_ids, $gallery_ids );
		}

		$attachment_ids = array_unique( $attachment_ids );

	} else{
		$attachment_ids = array();
	}

	return $attachment_ids;

}


function pw_get_posts_galleries_attachment_ids( $post_ids = array() ){
	// Does for multiple posts

	if( empty($post_ids) )
		return false;

	$attachment_ids = array();
	foreach( $post_ids as $post_id ){
		$gallery_attachment_ids = pw_get_post_galleries_attachment_ids( $post_id );
		$attachment_ids = array_merge( $attachment_ids, $gallery_attachment_ids );
	}

	$attachment_ids = array_unique( $attachment_ids );

	return $attachment_ids;

}



?>