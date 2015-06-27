<?php

///// INCLUDES /////
// AQ RESIZER : Module for resizing images 
$aq_resizer_include = POSTWORLD_PATH.'/lib/wordpress/aq_resizer.php';
include_once $aq_resizer_include;

////////// GET POST IMAGE //////////
/**
 * Get image field data.
 *
 * @param array $post Post Array
 * @param array $fields Postworld fields array, checks for 'image()' fields
 * @param number|boolean $thumbnail_id 	(Optional) Thumbnail ID Override, or if boolean 'true' then use provided $post as thumbnail_id
 * @return array Post image array $post['image']
 */
function pw_get_post_image( $post, $fields, $thumbnail_id = 0 ){

	// Extract image() fields
	$images = extract_fields( $fields, 'image' );

	// Check if there are images to process
	if( empty($images) )
		return false;
	
	$post_image = array();

	if( $thumbnail_id !== true ){
		// Localize Post ID
		$post_id = $post['ID'];
	}

	///// GET IMAGE TO USE /////
	// Setup Thumbnail Image Variables
	/**
	 * If $thumbnail_id is a boolean and true
	 * Then assume the $post is a thumbnail_id
	 */
	if( $thumbnail_id === true ){
		$thumbnail_id = $post;
	}
	elseif( !empty( $thumbnail_id ) ){
		$thumbnail_id = (int) $thumbnail_id;
	}
	elseif( $post['post_type'] == 'attachment' ){
		// Handle Attachment Post Types
		$thumbnail_id = $post_id;
	} else{
		// Handle Posts
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		//pw_log( 'thumbnail_id : ' . $thumbnail_id );
	}

	// If there is a set 'featured image' set the $thumbnail_url
	if ( $thumbnail_id ){
		$thumbnail_url = wp_get_attachment_url( $thumbnail_id ,'full');
	}
	// If there is no set 'featured image', get fallback - first image in post
	else {
		$first_image_obj = first_image_obj( $post_id );
		// If there is an image in the post
		if ($first_image_obj){
			$thumbnail_url = $first_image_obj['url'];
		}
		/*
		// If there is no image in the post, set fallbacks
		else {
			///// DEFAULT FALLBACK IMAGES /////

			// SETUP DEFAULT IMAGE FILE NAMES : ...jpg
			$link_format =  get_post_format( $post_id );
			$default_type_format_thumb_filename = 	'default-'.$post['post_type'].'-'.$link_format.'-thumb.jpg';
			$default_format_thumb_filename = 		'default-'.$link_format.'-thumb.jpg';
			$default_thumb_filename = 				'default-thumb.jpg';

			// SETUP DEFAULT IMAGE PATHS : /home/user/...
			$theme_images_dir = 				$pw_paths['THEME_PATH'].$pw_paths['IMAGES_PATH'];
			$default_type_format_thumb_path = 	$theme_images_dir . $default_type_format_thumb_filename;
			$default_format_thumb_path = 		$theme_images_dir . $default_format_thumb_filename;
			$default_thumb_path = 				$theme_images_dir . $default_thumb_filename;
			
			// SETUP DEFAULT IMAGE urlS : http://...
			$theme_images_url = 				$pw_paths['THEME_URL'].$pw_paths['IMAGES_PATH'];
			$default_type_format_thumb_url = 	$theme_images_url . $default_type_format_thumb_filename;
			$default_format_thumb_url = 		$theme_images_url . $default_format_thumb_filename;
			$default_thumb_url = 				$theme_images_url . $default_thumb_filename;
			
			// SET DEFAULT POST *TYPE + FORMAT* IMAGE PATH
			if ( file_exists( $default_type_format_thumb_path ) ) {
				$thumbnail_url = $default_type_format_thumb_url;
			}
			// SET DEFAULT POST *FORMAT* IMAGE PATH
			elseif ( file_exists( $default_format_thumb_path ) ) {
				$thumbnail_url = $default_format_thumb_url;
			}
			// SET DEFAULT POST IMAGE PATH
			elseif ( file_exists( $default_thumb_path ) ) {
				$thumbnail_url = $default_thumb_url;
			}
			// SET DEFAULT POST IMAGE PATH TO PLUGIN DEFAULT
			else{
				$thumbnail_url = $pw_paths['PLUGINS_URL'].$pw_paths['IMAGES_PATH'].$default_thumb_filename;
			}

		} // END else
		*/

	}// END else

	///// PROCESS IMAGES /////
	// Load in registered images attributes
	$registered_images_obj = registered_images_obj();
	$post_image['sizes'] = array();

	// Process each $image one at a time >> image(name,300,200,1) 
	foreach ($images as $image) {

		// Extract image attributes from parenthesis
		$image_attributes = extract_parenthesis_values($image, true);

		// Set $image_handle to name of requested image
		$image_handle = $image_attributes[0];

		///// REGISTERED IMAGE SIZES /////
		// If image attributes contains only a handle
		if ( count($image_attributes) == 1 ){

			// FULL : Get 'full' image
			if ( $image_handle == 'full' || $image_handle == 'all' ) {
				$image_obj = pw_get_image_obj($thumbnail_id, $image_handle);
				$post_image['sizes']['full']['url']	= $thumbnail_url;
				$post_image['sizes']['full']['width'] = (int)$image_obj['width'];
				$post_image['sizes']['full']['height'] = (int)$image_obj['height'];

			}

			// ALL : Get all registered images
			if( $image_handle == 'all' ) {
				$registered_images = registered_images_obj();

				foreach( $registered_images as $image_handle => $image_attributes ){
					//$image_src = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $image_handle );
					$image_src = wp_get_attachment_image_src( $thumbnail_id, $image_handle );
					$registered_images[$image_handle]["url"] = $image_src[0];
					$registered_images[$image_handle]["width"] = $image_src[1];
					$registered_images[$image_handle]["height"] = $image_src[2];
					$registered_images[$image_handle]["hard_crop"] = $image_src[3];
					$post_image['sizes'] = array_merge( $post_image['sizes'], $registered_images );
				}
			}

			// HANDLE : Get registered image
			// If it is a registered image format
			elseif( array_key_exists($image_handle, $registered_images_obj) ) {
				$image_obj = pw_get_image_obj($thumbnail_id, $image_handle);
				$post_image['sizes'][$image_handle]['url']	= $image_obj['url'];
				$post_image['sizes'][$image_handle]['width'] = (int)$image_obj['width'];
				$post_image['sizes'][$image_handle]['height'] = (int)$image_obj['height'];
			}

			// META : Get Image Meta Data
			elseif( $image_handle == 'meta' && is_numeric($thumbnail_id) ){
				$post_image['meta'] = wp_get_attachment_metadata($thumbnail_id);

				// Get the actual file URLS and inject into the object
				if( isset($post_image['meta']) && is_array($post['image']['meta']) ){
					foreach( $post_image['meta']['sizes'] as $key => $value ){
						$image_size_meta = wp_get_attachment_image_src( $thumbnail_id, $key );
						$post_image['meta']['sizes'][$key]['url'] = $image_size_meta[0];
					}
				}

			}

			elseif( $image_handle == 'tags' && is_numeric($thumbnail_id) ){

				// Get Image Meta Data
				if( isset( $post_image['meta'] ) ){
					// If it already has been queried, get fro post object
					$image_meta = $post_image['meta'];
				} else if( !isset( $image_meta ) ){
					// Otherwise get from database
					$image_meta = wp_get_attachment_metadata($thumbnail_id);
				}

				// Image Tags Object
				// Threshold Format as ['Tags'] : 'square' / 'wide' / 'tall' / 'x-wide' / 'x-tall' , etc.
				
				if( isset($image_meta) && gettype($image_meta) == 'array' )
					$image_tags = pw_generate_image_tags( array(
							"width" => $image_meta['width'],
							"height" => $image_meta['height'],
							)
						);
				else
					$image_tags = array();

				$post_image['tags'] = $image_tags;

			}

			// STATS : Get Image Stats
			elseif( $image_handle == 'stats' && is_numeric($thumbnail_id) ){
				
				// Get Image Meta Data
				if( isset( $post_image['meta'] ) ){
					// If it already has been queried, get fro post object
					$image_meta = $post_image['meta'];
				} else if( !isset( $image_meta ) ){
					// Otherwise get from database
					$image_meta = wp_get_attachment_metadata( $thumbnail_id );
				}

				// Calculate Image Ratios
				if( gettype($image_meta) == 'array' )
					$image_stats = array(
						"width" => 	$image_meta['width'],
						"height" => $image_meta['height'],
						"area"	=>	$image_meta['width'] * $image_meta['height'],
						"ratio"	=>	$image_meta['width'] / $image_meta['height']
						);
				else
					$image_stats = array();

				// TODO : Add "2:1 / 4:3 / etc" format
			
				// Set Stats in Post Object
				$post_image['stats'] = $image_stats;

			}

			// Get Image ID
			elseif( $image_handle == 'id' ){
				$post['thumbnail_id']= $thumbnail_id;
			}

		}
		elseif(
			count($image_attributes) == 2 &&
			$image_attributes[0] == 'post' &&
			is_numeric($thumbnail_id) ){

			// Get the post and deposit into post.image.post
			// With the second image attribute representing the field model handle (micro,preview,full,etc)
			$image_post = pw_get_post( $thumbnail_id, $image_attributes[1] );
			// Merge the image post (post_title,post_excerpt) into the post.image object
			$post_image = array_replace_recursive( $post_image, $image_post );

		}
		///// CUSTOM IMAGE SIZES /////
		// If image attributes contains custom height and width parameters
		else {
			// Set image attributes
			$thumb_width = $image_attributes[1];
			$thumb_height = $image_attributes[2];
			$hard_crop = $image_attributes[3];
			if ( !$hard_crop )
				$hard_crop = 1;

			// Process custom image size, return url
			$custom_image_url = aq_resize( $thumbnail_url, $thumb_width, $thumb_height, $hard_crop );
			
			// If the requested image size is bigger than what is available
			// It will return null
			if( empty($custom_image_url) ){
				$custom_image_obj = pw_get_image_obj($thumbnail_id, $image_handle);
				$custom_image_url = $custom_image_obj['url'];
				$thumb_width = (int)$image_obj['width'];
				$thumb_height = (int)$image_obj['height'];
			}

			// Set the value into the post object
			$post_image['sizes'][$image_handle]['url'] = $custom_image_url;
			
			/**
			 * @todo Get the actual image dimension if not a hard crop.
			 */
			$post_image['sizes'][$image_handle]['width'] = (int)$thumb_width;
			$post_image['sizes'][$image_handle]['height'] = (int)$thumb_height;

		}

	} // END foreeach

	return $post_image;

}


function pw_featured_image_post( $post_id = null, $fields = 'preview' ){
	global $post;

	// Default post ID
	if( empty( $post_id ) )
		$post_id = $post->ID;
	
	// Get the featured image ID
	$post_thumbnail_id = get_post_thumbnail_id( $post_id );
	
	if( empty($post_thumbnail_id) )
		return false;	

	// Reutn the postworld post object
	return pw_get_post( $post_thumbnail_id, $fields );
}


function pw_featured_image_url( $size = 'full', $echo = true ){
	$featured_image_post = pw_featured_image_post();
	$url = $featured_image_post['image']['sizes'][$size]['url'];
	if( $echo )
		echo $url;
	else
		return $url;
}


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
	$image['url'] = wp_get_attachment_url( $image_id );

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
	$image_arr = wp_get_attachment_image_src( $attachment_id, $size );
	$image_obj = array(
		'url' 		=> $image_arr[0],
		'width' 	=> $image_arr[1],
		'height' 	=> $image_arr[2],
		'ID'		=> $attachment_id,
		);
	return $image_obj;
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
function pw_url_to_media_library( $image_url, $post_id = 0){

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