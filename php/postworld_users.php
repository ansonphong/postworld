<?php

/////----- INSERT NEW USER -----/////
function pw_insert_user( $userdata ){
	global $pwSiteGlobals;

	if( isset( $pwSiteGlobals['role']['levels']['default'] ) )
		$userdata['role'] = $pwSiteGlobals['role']['levels']['default'];	
	else
		$userdata['role'] = 'subscriber';

	$user_id = wp_insert_user( $userdata );

	// If it's successful, we have the new user ID
	if( is_int($user_id) ){

		// Send Activation Email
		pw_activation_email(array("ID" => $user_id));

		// Set the security mode to allow for system operations
		global $pw;
		$pw["security"]["mode"] = "system";

		// Set the context in a special usermeta
		if( isset( $userdata['context'] ) ){
			$usermeta = array(
				"user_id"	=>	$user_id,
				"sub_key"	=>	"signup.context",
				"value" 	=>	$userdata['context'],
				//"meta_key" 	=>	// default: 'pw_meta'
				);
			pw_set_wp_usermeta( $usermeta );
		}

		// Return with Data
		return get_userdata($user_id);

	}
	else
		return $user_id;
}


/////----- SEND ACTIVATION LINK -----/////
function pw_activation_email( $userdata ){

	if( isset($userdata['email']) ){
		$user_obj = get_user_by( 'email', $userdata['email'] );
		$user_id = $user_obj->ID; // TEST?
	}

	elseif( isset( $userdata['ID'] ) ) {
		$user_id = $userdata['ID'];
	}

	// See if user already has an activation key
	$hash = get_user_meta( $user_id, 'activation_key', true );

	// If no key exists
	if ( !$hash ){
		$hash = md5( rand() );
		add_user_meta( $user_id, 'activation_key', $hash );
	}

	$user_info = get_userdata($user_id);
	$to = $user_info->user_email;           
	$subject = 'Signup Verification';

	$message .= 'Thanks for signing up for '.get_bloginfo('name').'!';
	$message .= "\n\n";
	$message .= 'Username: '.$user_info->user_login;
	$message .= "\n";
	$message .= 'Email: '.$user_info->user_email;
	$message .= "\n\n";
	$message .= "Click this link to activate your account:";
	$message .= "\n";
	$message .= home_url('/').'activate/?activation_key='.$hash;
	
	$headers = "From: ". get_bloginfo('admin_email') . "\r\n";
	//$headers .= "Content-type: text/html \r\n";

	return wp_mail($to, $subject, $message, $headers); 
}



/////----- ACTIVATE USER -----/////
function pw_activate_user( $auth_key ){
	// Query for users based on the meta data
	$user_query = new WP_User_Query(
		array(
			'meta_key'		=>	'activation_key',
			'meta_value'	=>	$auth_key
		)
	);

	// Set the Activated Role
	if( isset( $pwSiteGlobals['role']['levels']['activated'] ) )
		$role = $pwSiteGlobals['role']['levels']['activated'];	
	else
		$role = 'contributor';

	// Get the results from the query, returning the first user
	$users = $user_query->get_results();
	$user = $users[0];
	if( isset($user) ){
		$args = array(
			"ID" => $user->ID,
			"role" => $role,
			);
		wp_update_user($args);
		return $user;
	}
	else
		return array("error" => "Wrong activation code.");
}



function pw_activate_autologin( $activation_key, $redirect = "" ){

	///// GET THE USER /////
	// Get the user object with the specified activation key
	$user_query = new WP_User_Query(
		array(
			'meta_key'		=>	'activation_key',
			'meta_value'	=>	$activation_key
		)
	);
	$users = $user_query->get_results();
	$user = ( isset( $users[0] ) ) ?
		$users[0] : array();
    //echo json_encode( $users );

	///// SECRITY LAYER /////
	// Return false if more than one user returned
	if( count( $users ) > 1 )
		return false;
	// Return false if double check fails
	if( get_user_meta( $user->ID, 'activation_key', true ) != $activation_key )
		return false;


	///// LOG THE USER IN /////
    // If a user is found with this activation key
    if( !empty( $user ) ){

    	///// CONTEXT /////
    	// Get context from usermeta array
    	global $pw;
    	$pw['security']['mode'] = "system";

    	$usermeta = array(
    		"user_id"	=>	$user->ID,
			"sub_key"	=>	'signup.context',
			//"meta_key" 	=>	[string] 	(optional)
    		);
    	$signup_context = pw_get_wp_usermeta( $usermeta );

    	// Get the site config for various contexts
    	if( isset( $signup_context ) ){
    		global $pwSiteGlobals;
	    	$redirect_config = pw_get_obj( $pwSiteGlobals, 'signup.context.'.$signup_context.'.redirect' );
	    	// Get Default
	    	if( !isset( $redirect_config ) )
	    		$redirect_config = pw_get_obj( $pwSiteGlobals, 'signup.context.default.redirect' );
    	}
    	
    	// If the context and redirect are configured
    	if( isset($redirect_config) )
    		$redirect = $redirect_config;

    	// Delete the activation key !!!
    	delete_user_meta( $user->ID, 'activation_key' );

    	// Login User
		wp_clear_auth_cookie();
	    wp_set_current_user ( $user->ID );
	    wp_set_auth_cookie  ( $user->ID );

	    // Redirect
	    if( !empty( $redirect ) )
		    wp_redirect( $redirect );

    }
    else {
    	return false;
    }

    return true;

}

function pw_activate_autologin_exec(){
	$activation_key = $_GET['activation_key'];
	pw_activate_autologin( $activation_key );
}
// Automatically run autologin function if activation_key is provided in the URL parameters
if( isset( $_GET['activation_key'] ) )
	add_action( 'template_redirect', 'pw_activate_autologin_exec', 10, 3 );


/////----- RESET PASSWORD LINK -----/////
function pw_reset_password_email( $userdata ){
	if( isset($userdata['email']) ){
		$user_obj = get_user_by( 'email', $userdata['email'] );
		$user_id = $user_obj->ID;
	}
	elseif( isset( $userdata['ID'] ) ) {
		$user_id = $userdata['ID'];
	}

	// See if user already has an activation key
	$hash = get_user_meta( $user_id, 'reset_password_key', true );
	// If no key exists
	if ( !$hash ){
		$hash = md5( rand() );
		add_user_meta( $user_id, 'reset_password_key', $hash );
	}
	// If a key exists, update it with a new one
	else {
		$hash = md5( rand() );
		update_user_meta( $user_id, 'reset_password_key', $hash );
	}

	$user_info = get_userdata($user_id);
	$to = $user_info->user_email;           
	$subject = 'Reset Password'; 
	//$message = 'Hello,';
	//$message .= "\n\n";
	$message .= "Have you recently requested to reset your password on ".get_bloginfo('name')."?";
	$message .= "\n";
	$message .= "If not, you can ignore this email.";
	$message .= "\n\n";
	$message .= 'Username: '.$user_info->user_login;
	$message .= "\n";
	$message .= 'Email: '.$user_info->user_email;
	$message .= "\n\n";
	$message .= "Click this link to reset your password:";
	$message .= "\n";
	$message .= home_url('/').'reset-password/?auth_key='.$hash;
	$headers = 'From: '. get_bloginfo('admin_email') . "\r\n";           
	return wp_mail($to, $subject, $message, $headers); 
}


// ON ACTUAL PASSWORD RESET, REQUIRE THE ACTUAL KEY PRESENT AND VERIFY IT
/////----- RESET PASSWORD LINK -----/////
function pw_reset_password_submit( $userdata ){
	/*
		$userdata = array( "password" => *string*, "auth_key" => *string* );
	*/

	// Query for users based on the meta data
	$user_query = new WP_User_Query(
		array(
			'meta_key'		=>	'reset_password_key',
			'meta_value'	=>	$userdata['auth_key'],
		)
	);
	// Get the results from the query, returning the first user
	$users = $user_query->get_results();
	$user = $users[0];
	if( isset($user) ){
		$args = array(
			"ID" => $user->ID,
			"user_pass" => $userdata['user_pass']
			);
		$user_id = wp_update_user( $args );

		if( is_int($user_id) && $user_id == $user->ID ){
			// Remove the used key
			delete_user_meta( $user_id, 'reset_password_key' );
		}

		return $user;
	}
	else
		return array("error" => "Wrong or no authorization code.");

}






function pw_set_avatar( $image_object, $user_id ){
	//Get Current User
	$current_user_id = get_current_user_id();

	// TODO : Check if is user OR can XX capabaility
		// return array('error'=>'no access');

	//return $image_object['action'];

	if( $user_id == null )
		return false;

	// Delete avatar meta data
	if( $image_object['action'] == 'delete' ){
		// Delete User Meta
		delete_user_meta( $user_id, 'pw_avatar' );
		return true;
	}

	// Upload image from remote URL
	if( isset( $image_object['url'] ) && !isset( $image_object['id'] ) ){
		$attachment_id = url_to_media_library( $image_object['url'] );
		$image_object['id'] = $attachment_id;
	}

	// If Image has an 'ID' field
	if( isset( $image_object['id'] ) && is_numeric($image_object['id']) ){
		$attachment_id = $image_object['id'];

		$previous_value = get_user_meta( $user_id,'pw_avatar', true);

		// Is there a previous value?
		if( is_numeric( $previous_value ) ){
			// Update Meta Field
			$success = update_user_meta( $user_id, 'pw_avatar', $attachment_id );
		}
		else{
			// Add Meta Field
			$success = add_user_meta( $user_id, 'pw_avatar', $attachment_id, true );
		}

	} else
		return array('error'=>'No add avatar.');

	if( $success == true )
		return pw_get_avatar( array( "user_id" => $user_id ) );
	else{
		if( is_numeric( $previous_value ) )
			return $user_id;
	}

}


/////----- GET POSTWORLD AVATAR -----/////
function pw_get_avatar( $obj ){
	/*
		$args = { user_id:"1", [ size: 256 ], [ width:256, height:256 ] }
	*/

	extract($obj);

	global $pwSiteGlobals;
	$default_avatar = $pwSiteGlobals['avatar']['default'] ;

	if ( !isset($user_id) ){
		return $default_avatar;
	}
	
	$attachment_id = get_user_meta( $user_id, 'pw_avatar', true );

	if ( !empty($attachment_id) ){
		$attachment_meta = wp_get_attachment_metadata( $attachment_id );
		$attachment_image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
		$attachment_meta["file_url"] = $attachment_image_src[0];
		$attachment_meta["id"] = $attachment_id;

		// If no size is set, return with image meta object
		if ( !isset($size) ){
			return $attachment_meta;
		}
		// Size is set
		else{	
			$size = (int) $size;

			// If requested avatar is larger than the original image
			if( $size > $attachment_meta["width"] || $size > $attachment_meta["height"] ){
				$size = min( $attachment_meta["width"], $attachment_meta["height"] );
			}

			$width = $size;
			$height = $size;
			return aq_resize( $attachment_image_src[0], $width, $height, true );
		}
	}
	else{
		global $template_paths;
		return $default_avatar;
	}

}




function pw_user_login( $user_id, $redirect = '/' ) {

	// Login User
	wp_clear_auth_cookie();
    wp_set_current_user ( $user_id );
    wp_set_auth_cookie  ( $user_id );

    // Redirect
    wp_safe_redirect( $redirect );

}


// Automatically login a user with an auth key

// http://localhost/login/?autologin=909238409283kj23hk324


//// DELETE ////
function login_user_test( $activation_key, $redirect ){
    pw_user_login( 1, '/' );
}
//add_action( 'after_setup_theme', 'login_user_test', 10, 3 );





?>