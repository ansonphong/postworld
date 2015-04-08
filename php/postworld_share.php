<?php

/**
 * Watch for incoming shares
 * This is a watch which listens for the share fields
 * And if they exist and data checks out, add a share to the DB
 * @see pw_set_share()
 */
add_action( 'init', 'pw_share_watch', 10 ); // init
function pw_share_watch(){
	if( isset( $_GET['u'] ) )
		$user_id = $_GET['u'];
	if( isset( $_GET['p'] ) )
		$post_id = $_GET['p'];

	// If both user and post are supplied ie. /?u=1&p=220220
	if ( isset( $user_id ) && isset( $post_id ) ){
		if ( username_exists_by_id($user_id) && post_exists_by_id($post_id) ){
			// Set the share record in Postworld Shares table
			pw_set_share( $user_id, $post_id );
			// Redirect browser to the post's permalink
			$permalink = get_permalink( $post_id );
			pw_log($permalink);
			wp_redirect( $permalink ); //get_site_url()
		}
	}
}

/**
 * Sets a record of a share in Shares table
 *
 * @param integer $user_id The ID of the user which to attribute the share to
 * @param integer $post_id The ID of the post shared
 * @return Array The user share report
 */
function pw_set_share ( $user_id, $post_id ){
	/*
	Description
	Context : The URL leading to the share looks like :
	http://realitysandwich.com/?p=24&u=48
	p : The post ID
	u : The user ID
	Process
	
	1-Setup
		Check if user ID exists
		Check if post ID exists
		Get the ID of the post author from Posts table
		Get the user's IP address with pw_get_client_ip()
	2-Process IP
		Check IP address against list of IPs stored in recent_ips column in Shares table
		If the IP is not in the list, add to the list and add 1+ to total_views in wp_postworld_user_shares
		If the IP is in the list, do nothing
		If the array length of IPs is over {{100}}, remove old IPs
	3-Add Share
		If the IP is unique, add one point to the share
		Update last_time with current GMT UNIX Timestamp
	
	 * return : boolean
	
	true - if added share
	false - if no share added
	 
	 */
	
	if( pw_user_id_exists($user_id) &&
		( $post_author = pw_does_post_exist_return_post_author($post_id) ) !== FALSE ){
		$last_time = date('Y-m-d H:i:s');
		//echo 'pos_auhor:'.$post_author;
		$user_ip = pw_get_client_ip();
		$current_share = pw_get_share($user_id,$post_id);

		global $pwSiteGlobals;
		$ip_history = (int) $pwSiteGlobals['shares']['tracker']['ip_history']; // Integer - number of IPs to store in post share history
		if( isset($ip_history) )
			$ip_history = (int) 100;

		if($current_share){
			$shares=0;
			$ips_list = (array)json_decode( $current_share->recent_ips);
			if(!in_array($user_ip, $ips_list)){
				if(count($ips_list) >= $ip_history){
					$ips_list = array($user_ip);
				}else{
					$ips_list[]=$user_ip;
				}
				$shares=1;
			}
			pw_update_share($user_id, $post_id,$ips_list,$shares,$last_time);
			
		}
		else{//share not found
			$ips_list = array($user_ip);
			pw_add_share($user_id,$post_id,$post_author,$ips_list,$last_time);
			//return TRUE;
		}
		return TRUE;
	}else{
		return FALSE;
	}
	
}
function pw_update_share($user_id, $post_id, $ips_list,$added_shares,$last_time=null){
	global $wpdb;
	$wpdb->show_errors();
	
	$query = "UPDATE $wpdb->pw_prefix"."shares SET recent_ips='".json_encode($ips_list)."',shares=shares+".$added_shares;
	if($last_time){$query.=" ,last_time='".$last_time."'";}
	$query.=" where user_id=".$user_id." and post_id=".$post_id;
	$wpdb->query( $query );
}

function pw_add_share($user_id,$post_id,$post_author,$ips_list,$last_time){
	// TODO : Refactor to use $wpdb->insert function

	global $wpdb;
	$wpdb->show_errors();
	$query = "INSERT INTO $wpdb->pw_prefix"."shares VALUES(".$user_id.",".$post_id.",".$post_author.",'".json_encode($ips_list)."',1,'".$last_time."')";
	$wpdb->query( $query );
	return;
}

function pw_process_share_row( $row ){
	// Convert the "YYYY:MM:DD HH:MM:SS" timestamp to UNIX timestamp
	if( isset( $row->last_time ) )
		$row->last_timestamp = (int) strtotime( $row->last_time );
	return $row;
}

function pw_get_share($user_id,$post_id){
	global $wpdb;	
	$wpdb ->show_errors();
	$query = "SELECT * FROM $wpdb->pw_prefix"."shares WHERE post_id=$post_id AND user_id=$user_id";
	$row = $wpdb->get_row( $query );
	$row = pw_process_share_row( $row );
	return $row;
}

function pw_does_post_exist_return_post_author($post_id){
	global $wpdb;
	$wpdb->show_errors();
	$get_post = get_post($post_id, ARRAY_A);

	return ( !empty( $get_post ) ) ? $get_post['post_author'] : false;

}

/**
 * Generate a report of all the shares relating to the current user
 * by posts that the given user has shared (outgoing)
 *
 * @param Array $user_id The ID of the user which to generate the report for
 * @return Array The user share report
 */
function pw_user_share_report_outgoing( $user_id){
	
	global $wpdb;
	$wpdb->show_errors();
	
	// Lookup all posts shared by user ID in User Shares table, column user_id
	$query = "SELECT * FROM $wpdb->pw_prefix"."shares WHERE user_id=".$user_id;
	$results = $wpdb->get_results( $query );
	
	$output = array();
	if($results){
		foreach ($results as $row ) {
			$share_data = (array) pw_process_share_row( $row );
			$output[]= $share_data;
		}
	}

	return $output;
	
	/*
	array(
	    array(
	        'post_id' 			=> 8723,
	        'shares' 			=> 385,
	        'last_time' 		=> {{string}},
	        'last_timestamp'	=> {{ integer }}
	        ),
	    array(
	        'post_id' 			=> 3463,
	        'shares' 			=> 234,
	        'last_time' 		=> {{string}},
	        'last_timestamp'	=> {{ integer }}
	        ),
	    ...
	
	    )
	 */
}

/**
 * Adds post/user meta data to a user share report
 *
 * @param Array $user_share_report The share report to augment
 * @param Array $meta Metadata about how to process the report
 * @return Array The user share report with the meta data added
 */
function pw_user_share_report_meta( $user_share_report, $meta = array() ){

	$defaultMeta = array(
		'user_fields' 	=> array( "display_name", "user_nicename", "user_profile_url" ),
		'post_fields'	=>	'micro'
		);

	$meta = array_replace( $defaultMeta, $meta );

	if(empty($user_share_report))
		return array();

	$user_share_meta_report = array();

	foreach( $user_share_report as $shared_post ){
		$user_share_meta_single = $shared_post;
		$user_share_meta_single['post'] = pw_get_post( $shared_post['post_id'], $meta['post_fields'] );

		// If it's an incoming share report
		// Add user meta for each sharer
		if( isset($shared_post["user_shares"]) ){
			// Generate new user_sshares object, with 'user' meta data
			$user_shares = array();
			foreach( $shared_post["user_shares"] as $user ){ 
				$user_share_single = $user;
				$user_fields = $meta['user_fields'];
				$user_share_single["user"] = pw_get_user( $user["user_id"], $user_fields );
				array_push( $user_shares, $user_share_single );
			}
			// Over-write original user_shares object
			$user_share_meta_single["user_shares"] = $user_shares;
		}

		array_push( $user_share_meta_report, $user_share_meta_single );

	}

	return $user_share_meta_report;
}


/**
 * Add user meta data to a post share report, such as username and user profile link
 *
 * @param integer $user_id Required. The user ID for which to retreive the report
 * @return Array 
 */
function pw_post_share_report_meta ($post_share_report){
	// $post_share_report = [{"user_id":"1","shares":"1","last_time":"2013-11-17 12:17:27"}]

	if (!empty($post_share_report)){
		// SETUP OBJECT
		$post_share_report_meta = array();
		// CYCLE THROUGH SHARES
		foreach( $post_share_report as $share_user ){
			// RETURN USER DATA
			$user_fields = array("display_name", "user_nicename", "user_profile_url");
			$share_user["user"] = pw_get_user( $share_user["user_id"], $user_fields );
			// PUSH TO NEW OBJECT
			array_push( $post_share_report_meta, $share_user );
		}
	}
	return $post_share_report_meta;
}


/**
 * Generate a report of all the shares relating to
 * the current user by shares to the given user's posts
 *
 * @param integer $user_id Required. The user ID for which to retreive the report
 * @return Array 
 */
function pw_user_share_report_incoming( $user_id ){
	
	global $wpdb;
	$wpdb->show_errors();
	$output=array();

	// Lookup all shared posts owned by the user ID from User Shares table, column author_id
	$query = "SELECT post_id, sum(shares) AS total_shares FROM $wpdb->pw_prefix"."shares WHERE author_id=".$user_id." GROUP BY post_id";
	$results = $wpdb->get_results( $query );
	
	$output = array();
	if($results){
		foreach ($results as $row ) {
			$query="SELECT * FROM $wpdb->pw_prefix"."shares WHERE post_id=".$row->post_id;
			$posts_shares_by_id=$wpdb->get_results($query); 
			$generalData = array('post_id'=>$row->post_id,'total_shares'=>$row->total_shares);
			$generalData['user_shares']=array();

			foreach ($posts_shares_by_id as $post_share) {
				$post_share_data = (array) pw_process_share_row( $post_share );
				$generalData['user_shares'][]=$post_share_data;
			}
			$output[]=$generalData;
		}
	}
	return $output;

	/*
	array(
	    array(
	        'post_id' => 9348,
	        'total_shares' => 1385,
	        'users_shares' => array( 
	            array(
	                'user_id' => 843,
	                'shares' => 235,
	                'last_time' => {{integer}}
	                ),
	            array(
	                'user_id' => 733,
	                'shares' => 345,
	                'last_time' => {{integer}}
	                ),
	            ...
	            )
	        ),
	    array(
	        'post_id' => 623,
	        'total_shares' => 4523,
	        'users_shares' => array( 
	            array(
	                'user_id' => 633,
	                'shares' => 785,
	                'last_time' => {{integer}}
	                ),
	            array(
	                'user_id' => 124,
	                'shares' => 573,
	                'last_time' => {{integer}}
	                ),
	            ...
	            )
	        ),
	    )
	 */
	
}


/**
 * Generate a report of all the shares relating to the specified post.
 *
 * @param string   $title    Optional. WordPress login Page title to display in the `<title>` element.
 *                           Default 'Log In'.
 * @param string   $message  Optional. Message to display in header. Default empty.
 * @param WP_Error $wp_error Optional. The error to pass. Default empty.
 * @return Array 
 */
function pw_post_share_report ( $post_id ){
	
	global $wpdb;
	$wpdb->show_errors();
	
	// Collect data from Shares table on the given post
	$query = "select * from $wpdb->pw_prefix"."shares where post_id=".$post_id;
	$results = $wpdb->get_results( $query );
	$output = array();
	if($results){
		foreach ($results as $row ) {
			$share_data = array('user_id'=>$row->user_id, 'shares'=>$row->shares, 'last_time'=>$row->last_time);
			$output[]= $share_data;
		}
	}
	return $output;

	/*
	RETURN FORMAT :
	array(
	    array(
	        'user_id' => '12',
	        'shares' => '434',
	        'last_time' => {{integer}}
	        ),
	    array(
	        'user_id' => '53',
	        'shares' => '34',
	        'last_time' => {{integer}}
	        ),
	    ...
	    )
	 */
	
}

?>