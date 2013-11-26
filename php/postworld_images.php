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
	
	$base_file_name = basename($image_url);
	$filename = $post_id."-".$base_file_name;
	if(wp_mkdir_p($upload_dir['path']))
	    $file = $upload_dir['path'] . '/' . $filename;
	else
	    $file = $upload_dir['basedir'] . '/' . $filename;
	//file_put_contents($file, $image_data);

	$image_data = grab_image($image_url,$file);//file_get_contents($image_url);
	// Check if post exists
	
	if($image_data!== FALSE){
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

?>