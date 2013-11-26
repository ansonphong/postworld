<?php

////////// SHARE BUG //////////
// This is a 'bug' which listens for the share fields
// And if they exist and data checks out, add a share to the DB


add_action( 'wp_loaded', 'share_bug', 10 );
function share_bug(){

	if( isset( $_GET['u'] ) )
		$user_id = $_GET['u'];
	if( isset( $_GET['u'] ) )
		$post_id = $_GET['u'];

	// If both user and post are supplied ie.
	// http://localhost:8888/?u=1&p=169953  /  175474 / 178530

	if ( isset( $user_id ) && isset( $post_id ) ){
		if ( username_exists_by_id($user_id) && post_exists_by_id($post_id) ){
			set_share ( $user_id, $post_id );
			// Redirect browser
			$permalink = get_permalink( $post_id );
			wp_redirect( $permalink );
		}
	
	}
}


function set_share ( $user_id, $post_id ){
	/*
	 Description

	Sets a record of a share in Shares table
	Context : The URL leading to the share looks like :
	http://realitysandwich.com/?p=24&u=48
	p : The post ID
	u : The user ID
	Process
	
	1-Setup
		Check if user ID exists
		Check if post ID exists
		Get the ID of the post author from Posts table
		Get the user's IP address with get_client_ip()
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
	
	if(does_user_exist($user_id)&& ($post_author = does_post_exist_return_post_author($post_id))!==FALSE){
		$last_time = date('Y-m-d H:i:s');
		//echo 'pos_auhor:'.$post_author;
		$user_ip = get_client_ip();
		$current_share = get_share($user_id,$post_id);
		if($current_share){
			$shares=0;
			$ips_list =(array)json_decode( $current_share->recent_ips);
			if(!in_array($user_ip, $ips_list)){
				if(count($ips_list)>=100){
					$ips_list = array($user_ip);
				}else{
					$ips_list[]=$user_ip;
				}
				$shares=1;
			}
			update_share($user_id, $post_id,$ips_list,$shares,$last_time);
			
			
			
		}
		else{//share not found
			$ips_list = array($user_ip);
			add_share($user_id,$post_id,$post_author,$ips_list,$last_time);
			//return TRUE;
		}
		return TRUE;
	}else{
		return FALSE;
	}
	
}
function update_share($user_id, $post_id, $ips_list,$added_shares,$last_time=null){
	global $wpdb;
	$wpdb->show_errors();
	
	$query = "update $wpdb->pw_prefix"."shares set recent_ips='".json_encode($ips_list)."',shares=shares+".$added_shares;
	if($last_time){$query.=" ,last_time='".$last_time."'";}
	$query.=" where user_id=".$user_id." and post_id=".$post_id;
	$wpdb->query($query); 
}
function add_share($user_id,$post_id,$post_author,$ips_list,$last_time){
	global $wpdb;
	$wpdb->show_errors();
	
	$query = "insert into $wpdb->pw_prefix"."shares values(".$user_id.",".$post_id.",".$post_author.",'".json_encode($ips_list)."',1,'".$last_time."')";
	$wpdb->query($query);
	
}
function get_share($user_id,$post_id){
	global $wpdb;	
	$wpdb ->show_errors();
	$query = "select * from $wpdb->pw_prefix"."shares where post_id=$post_id and user_id=$user_id";
	$row = $wpdb->get_row($query);
	
	return $row;
}



function does_user_exist($user_id){
	
    global $wpdb;
	$wpdb->show_errors();
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users WHERE ID = $user_id");
    if($count == 1){ return TRUE; }else{ return FALSE; }


}
function does_post_exist_return_post_author($post_id){
	global $wpdb;
	$wpdb->show_errors();
	$get_post = get_post($post_id, ARRAY_A);
	if($get_post){
	  return $get_post['post_author'];
	} else {
	  return FALSE;
	}
}
function user_share_report_outgoing ( $user_id ){
	/*
	 Description

	Generate a report of all the shares relating to the current user by posts that the given user has shared
	Process
	
	Lookup all posts shared by user ID in User Shares table, column user_id
	return : Array
	
	array(
	    array(
	        'post_id' => 8723,
	        'shares' => 385,
	        'last_time' => {{integer}}
	        ),
	    array(
	        'post_id' => 3463,
	        'shares' => 234,
	        'last_time' => {{integer}}
	        ),
	    ...
	
	    )
	 */
	
	global $wpdb;
	$wpdb->show_errors();
	
	$query = "select * from $wpdb->pw_prefix"."shares where user_id=".$user_id;
	$results = $wpdb->get_results($query);
	$output = array();
	if($results){
		foreach ($results as $row ) {
			$share_data = array('post_id'=>$row->post_id, 'shares'=>$row->shares, 'last_time'=>$row->last_time);
			$output[]= $share_data;
		}
	}
	return $output;
	
}

////////// USER SHARE REPORT META //////////
function user_share_report_meta ($user_share_report){
	if (!empty($user_share_report)){
		$user_share_meta_report = array();
		foreach( $user_share_report as $shared_post ){
			$user_share_meta_single = $shared_post;
			$user_share_meta_single['post'] = pw_get_post( $shared_post['post_id'], 'preview' );
			// If it's an incoming share report
			// Add user meta for each sharer
			if( isset($shared_post["user_shares"]) ){
				// Generate new user_sshares object, with 'user' meta data
				$user_shares = array();
				foreach( $shared_post["user_shares"] as $user ){ 
					$user_share_single = $user;
					$user_fields = array("display_name", "user_nicename", "user_profile_url");
					$user_share_single["user"] = pw_get_userdata( $user["user_id"], $user_fields );
					array_push( $user_shares, $user_share_single );
				}
				// Over-write original user_shares object
				$user_share_meta_single["user_shares"] = $user_shares;
			}
			array_push( $user_share_meta_report, $user_share_meta_single );
		}
	}
	return $user_share_meta_report;
}



////////// POST SHARE REPORT META //////////
// [{"user_id":"1","shares":"1","last_time":"2013-11-17 12:17:27"}]
function post_share_report_meta ($post_share_report){

	if (!empty($post_share_report)){
		
		// SETUP OBJECT
		$post_share_report_meta = array();

		// CYCLE THROUGH SHARES
		foreach( $post_share_report as $share_user ){

			// RETURN USER DATA
			$user_fields = array("display_name", "user_nicename", "user_profile_url");
			$share_user["user"] = pw_get_userdata( $share_user["user_id"], $user_fields );

			// PUSH TO NEW OBJECT
			array_push( $post_share_report_meta, $share_user );

		}

	}

	return $post_share_report_meta;

}



function user_share_report_incoming ( $user_id ){
	/*
	Description

	Generate a report of all the shares relating to the current user by shares to the given user's posts
	Process
	
	Lookup all shared posts owned by the user ID from User Shares table, column author_id
	return : Array
	
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
	
	global $wpdb;
	$wpdb->show_errors();
	$output=array();
	$query = "select post_id, sum(shares) as total_shares from $wpdb->pw_prefix"."shares where author_id=".$user_id." group by post_id";
	$results = $wpdb->get_results($query);
	
	//print_r($results);
	$output = array();
	if($results){
		foreach ($results as $row ) {
			
			$query="select * from $wpdb->pw_prefix"."shares where post_id=".$row->post_id;
			$posts_shares_by_id=$wpdb->get_results($query); 
			//print_r($posts_shares_by_id);
			$generalData = array('post_id'=>$row->post_id,'total_shares'=>$row->total_shares);
			$generalData['user_shares']=array();	
			foreach ($posts_shares_by_id as $post_share) {
				$post_share_data = array('user_id'=>$post_share->user_id,
										'shares'=>$post_share->shares,
										'last_time'=>$post_share->last_time
										);
				$generalData['user_shares'][]=$post_share_data;
			}
			$output[]=$generalData;
		}
		
	}
	return $output;
}

function post_share_report ( $post_id ){
	/*
	 
	Generate a report of all the shares relating to the current post
	Process
	
	Collect data from Shares table on the given post
	return : Array
	
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
	
	global $wpdb;
	$wpdb->show_errors();
	
	$query = "select * from $wpdb->pw_prefix"."shares where post_id=".$post_id;
	$results = $wpdb->get_results($query);
	$output = array();
	if($results){
		foreach ($results as $row ) {
			$share_data = array('user_id'=>$row->user_id, 'shares'=>$row->shares, 'last_time'=>$row->last_time);
			$output[]= $share_data;
		}
	}
	return $output;
	
}

?>