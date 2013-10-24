<?php
	function cache_all_points (){
		/*• Runs cache_user_points() and cache_post_points()
		return : cron_logs Object (add to table wp_postworld_cron_logs)*/
		$post_points_cron_log = cache_all_post_points();
		$user_points_cron_log = cache_all_user_points();
		
		
		return array($post_points_cron_log,$user_points_cron_log);
	}
	//TODO
	function cache_all_user_points(){
		/*• Cycles through all users with cache_user_points() method
		return : cron_logs Object (add to table wp_postworld_cron_logs)*/
		global $wpdb;
		$wpdb -> show_errors();
		
		//get all user ids
		
		$query ="select ID from wp_users";
		$blogusers=$wpdb->get_results($query);
		$blog_users_count = count($blogusers);
		$time_start = date("Y-m-d H:i:s");
		for($i=0;$i<$blog_users_count;$i++){
			
			cache_user_posts_points($blogusers[$i]->ID);
		}
		
		//loop for all users: get calculate_user_points and user_post_points?
		$time_end = date("Y-m-d H:i:s");
	//	$current_cron_log_object = create_cron_log_object($time_start, $time_end, $blog_users_count, 'user_points','');
		$current_cron_log_object = create_cron_log_object($time_start, $time_end, null, 'cache_all_user_points',null,null);
		echo json_encode($current_cron_log_object);
	}
	
	function cache_all_post_points() {
		/*
		 • Cycles through each post in each post_type with points enabled
		 • Calculates each post's current points with calculate_points()
		 • Stores points it in wp_postworld_meta 'points' column
		 • return : cron_logs Object (add to table wp_postworld_cron_logs)
		 */
		//Post_type = page/post, 
					 
		global $wpdb;
	
		$wpdb -> show_errors();
		
		//get wp_options Enable Points ( postworld_points ) field and get post types enabled for points - http://codex.wordpress.org/Function_Reference/get_option
		//TODO : Use wp_options_api http://codex.wordpress.org/Options_API
		
		global $pw_defaults;
		$points_options = $pw_defaults['points']; // array of post types
		
		//select all post ids of posts that their post types are enabled for points
		//$post_types_string = implode(', ',$points_options['post_types']);
		
		
		$post_types = $points_options['post_types'];
		//echo(json_encode($post_types).'<br>');
		$number_of_post_types = count($post_types);
		$cron_logs;
		//echo json_encode($cron_logs);
		$cron_logs['points']= array();
		//echo json_encode($cron_logs);
		//echo($number_of_post_types);
		for($i=0;$i<$number_of_post_types;$i++){
				$query = "select * from wp_posts where post_type ='".$post_types[$i]."'";
			//	echo("<br>".$query."<br>");
				$posts = $wpdb -> get_results($query);
				$time_start = date("Y-m-d H:i:s");
				foreach ($posts as $row) {
					
					//check if already there is a record for this post , yes then calculate points
					//else create a row with zero points
					cache_post_points($row->ID);
					// {{feed/post_type}}
					
					//$current_cron_log_object->query_vars[] ="";// {{ query_vars Object: use pw_get_posts  }}
				}
				
				$time_end = date("Y-m-d H:i:s");
				//$current_cron_log_object = create_cron_log_object($time_start, $time_end, count($posts), 'points', $post_types[$i],'');
				$current_cron_log_object = create_cron_log_object($time_start, $time_end, count($posts), 'cache_all_post_points',$post_types[$i],null);
				
				$cron_logs['points'][] = $current_cron_log_object;
				//echo json_encode($cron_logs);
		}
	
		echo json_encode(($cron_logs));
		//echo($cron_logs);
		 
	}
	
	
	
	function cache_all_comment_points(){
		/*• Cycles through all columns
		• Calculates and caches each comment's current points with cache_comment_points() method
		return : cron_logs Object (add to table wp_postworld_cron_logs)*/
		
		global $wpdb;
		$wpdb -> show_errors();
		$query ="select comment_ID from wp_comments";
		$blog_comments=$wpdb->get_results($query);
		$blog_comments_count = count($blog_comments);
		
		$time_start = date("Y-m-d H:i:s");
		for($i=0;$i<$blog_comments_count;$i++){
			cache_comment_points($blog_comments[$i]->comment_ID);
		}
		$time_end =  date("Y-m-d H:i:s");
		//$current_cron_log_object = create_cron_log_object($time_start, $time_end, $blog_comments_count, 'comments', '');	
		$current_cron_log_object = create_cron_log_object($time_start, $time_end, null, 'cache_all_comment_points',null,null);
		echo json_encode($current_cron_log_object);
		
		
	}
	//TODO
	function cache_all_rank_scores (){
		/*• Cycles through each post in each post_type scheduled for Rank Score caching
		• Calculates and caches each post's current rank with cache_rank_score() method
		return : cron_logs Object (add to table wp_postworld_cron_logs)*/
	
		global $wpdb;
		$wpdb -> show_errors();
		
		global $pw_defaults;
		$rank_options = $pw_defaults['rank']; // array of post types
		$post_types = $rank_options['post_types'];
		$number_of_post_types = count($post_types);
		$cron_logs;
		$cron_logs['rank']= array();
		
		for($i=0;$i<$number_of_post_types;$i++){
			$query = "select * from wp_posts where post_type ='".$post_types[$i]."'";
			$posts = $wpdb -> get_results($query);
			$time_start = date("Y-m-d H:i:s");
			foreach ($posts as $row) {
				cache_rank_score($row->ID);
			}
			$time_end= date("Y-m-d H:i:s");	
			$current_cron_log_object = create_cron_log_object($time_start, $time_end, count($posts), 'cache_all_rank_scores', $post_types[$i],'');
			$cron_logs['rank'][] = $current_cron_log_object;
			
	}

	echo json_encode(($cron_logs));
	 
	
	}
	
	/*later*///TODO
	function cache_all_feeds (){
		/*• Run pw_cache_feed() method for each feed registered for feed caching in WP Options
		return : cron_logs Object (store in table wp_postworld_cron_logs)*/
		global $pw_defaults;
		$feeds_options = $pw_defaults['feeds']['cache_feeds'];
		$cron_logs=array();
		$number_of_feeds = count($feeds_options);
		//print_r($number_of_feeds);
		for ($i=0; $i <$number_of_feeds ; $i++) {
			$time_start = date("Y-m-d H:i:s"); 
			$cache_output = pw_cache_feed($feeds_options[$i]);
			$time_end = date("Y-m-d H:i:s");
			$current_cron_log_object = create_cron_log_object($time_start, $time_end,$cache_output['number_of_posts'] , 'cache_all_feeds', $feeds_options[$i],$cache_output['feed_query']);
			$cron_logs[]=$current_cron_log_object;
		}
		return $cron_logs;	
		
	}
	
	function clear_cron_logs ( $timestamp ){
		/*  • Count number of rows in wp_postworld_cron_logs (rows_before)
			• Deletes all rows which are before the specified timestamp (rows_removed)
			• Count number of rows after clearing (rows_after)
			return : Object
			rows_before: {{integer}}
			rows_removed: {{integer}}
			rows_after: {{integer}}
			
			 $timestamp format : '2013-09-25 14:39:55'
		*/
		
		
		global $wpdb;
		$wpdb -> show_errors();
		
		$query = "select COUNT(*) FROM $wpdb->pw_prefix"."cron_logs";
		echo $query."<br>";
		$total_logs = $wpdb-> get_var($query);
		echo $total_logs."<br>";
		if($total_logs == 0){
			return array('rows_before'=> 0,'rows_removed'=> 0,'rows_after'=>0); 
		}
		else{
		
			$query ="DELETE FROM $wpdb->pw_prefix"."cron_logs WHERE time_end < '".$timestamp."'";
			echo $query."<br>";
			$deleted_rows = $wpdb->query($query);
			echo print_r($deleted_rows);
			if($deleted_rows === FALSE)
				$deleted_rows=0;
		
			return array('rows_before'=> $total_logs,'rows_removed'=> $deleted_rows,'rows_after'=>($total_logs - $deleted_rows));
		}
	}


	function cache_shares ( $cache_all = FALSE){ 
		/*
		
		 *  Description
		Caches user and post share reports from the Shares table
		
		 * Paramaters
		
			-$cache_all : boolean
			-Default : false
		
		 * Process
		
			-If $cache_all = false, just update the recently changed share reports
				-Check Cron Logs table for the most recent start time of the last cache_shares() operation
				-POSTS :
					Get an array of all post_IDs from Shares table which have been updated since the most recent run of cache_shares() by checking the last time column
					Run cache_post_shares($post_id) for all recently updated shares
				-AUTHORS :
					Get an array of all post_author_IDs from Shares table which have been updated since the last cache.
					Run cache_user_post_shares($user_id) for all recently updated user's shares
				-USERS :
					Get an array of all user_IDs from Shares table which have been updated since the last cache. Run cache_user_shares($user_id) for all recently updated user's shares
		
		 	-If $cache_all = true
				-Cycle through every post and run cache_post_shares($post_id)
				-Cycle through every author and run cache_user_post_shares($user_id)
				-Cycle through every user and run cache_user_shares($user_id)
		return : cron_logs Object (store in table wp_postworld_cron_logs)
		 */	
		
		
		
				
		 $cron_logs=array();
		 if(!$cache_all){
		 	
			$recent_log = get_most_recent_cache_shares_log();
			 
			 if(!is_null($recent_log)){
			 	$time_start = date("Y-m-d H:i:s");
				$post_ids = get_recent_shares_post_ids($recent_log->last_time);
				foreach ($post_ids as $post_id) {
					cache_user_post_shares($post_id->$post_id);
				}
				
				$user_ids = get_recent_shares_user_ids($recent_log->last_time);
				foreach ($user_ids as $user_id) {
					cache_user_shares($user_id->$user_id,'outgoing');
				}
				
				$author_ids = get_recent_shares_author_ids($recent_log->last_time);
				foreach ($author_ids as $author_id) {
					 cache_user_shares($author_id->$author_id,'incoming');
				}
				
				
				$time_end = date("Y-m-d H:i:s");
				$current_cron_log_object = create_cron_log_object($time_start, $time_end, null, 'cache_shares',null,null);
				return $current_cron_log_object;
			 }else{
			 	
			 }
			
			
		 }else{
	 		//-If $cache_all = true
	 		$time_start = date("Y-m-d H:i:s");
	 		/*-Cycle through every post and run cache_post_shares($post_id) */
	 		$post_ids = get_all_post_ids_as_array();
			foreach ($post_ids as $post_id) {
				cache_post_shares($post_id->ID);
			}
			/*-Cycle through every author and run cache_user_post_shares($user_id)*/
			/*-Cycle through every user and run cache_user_shares($user_id)*/
			$user_ids = get_all_user_ids_as_array();
			foreach ($user_ids as $user_id) {
				cache_user_shares($user_id->ID,'both');
			}
				 
			$time_end = date("Y-m-d H:i:s");
			$current_cron_log_object = create_cron_log_object($time_start, $time_end, null, 'cache_shares',null,null);
			return $current_cron_log_object;
				
		 }
		 
	
	}

/*	
	//TODO : to be specified
	function cache_user_post_shares($user_id){
		cache_user_shares($user_id, 'incoming');
	}*/

	function get_most_recent_cache_shares_log(){
		global $wpdb;
		$wpdb->show_errors();
		$query="SELECT * FROM wp_postworld_a1. wp_postworld_cron_logs  WHERE time_start = (SELECT MAX(time_start) FROM $wpdb->pw_prefix"."cron_logs where function_type = 'cache_shares')";
		$row = $wpdb->get_row($query);	
	}
	
	function get_recent_shares_post_ids($last_time){
		 global $wpdb;	
		 $wpdb->show_errors();
		 $query = "select DISTINCT  post_id from  $wpdb->pw_prefix"."shares where last_time>='$last_time'";
		 $post_ids = $wpdb->query($query);
		 return $post_ids;
	}	
	
	function get_recent_shares_author_ids($last_time){
		 global $wpdb;	
		 $wpdb->show_errors();
		 $query = "select DISTINCT  user_id from  $wpdb->pw_prefix"."shares where last_time>='$last_time'";
		 $user_ids = $wpdb->query($query);
		 return $user_ids;
	}	
	
	function get_recent_shares_user_ids($last_time){
		 global $wpdb;	
		 $wpdb->show_errors();
		 $query = "select DISTINCT author_id from  $wpdb->pw_prefix"."shares where last_time>='$last_time'";
		 $author_ids = $wpdb->query($query);
		 return $author_ids;
	}	


	function get_all_post_ids_as_array(){
		 global $wpdb;
		 $wpdb->show_errors();
		 
		 $query = "select ID from wp_posts";
		 $post_ids_array = $wpdb->get_results($query);
		 
		 return ($post_ids_array);
	}
	function get_all_user_ids_as_array(){
		
		global $wpdb;
		$wpdb->show_errors();

		$query = "select ID from wp_users";
		$user_ids_array = $wpdb->get_results($query);

		return $user_ids_array;
		
	}
	
	//////////////// POST SHARES /////////////////////
	function calculate_post_shares($post_id){
		/*Calculates the total number of shares to the given post
		Process
		-Lookup the given post_id in the Shares table
		-Add up ( SUM ) the total number in shares column attributed to the post
		-return : integer (number of shares)*/
		
		
		global $wpdb;
		$wpdb -> show_errors();
		
		$query = "select SUM(shares) FROM $wpdb->pw_prefix"."shares where post_id=".$post_id;
		$total_shares = $wpdb->get_var($query);
		if($total_shares)
			return $total_shares;
		else return 0;
	}
	
	function cache_post_shares( $post_id ){
	
		/*Caches the total number of shares to the given post
		Process
		-Run calculate_post_shares($post_id)
		-Write the result to the post_shares column in the Post Meta table
		-return : integer (number of shares)*/
		$total_shares = calculate_post_shares($post_id);
		
		add_recored_to_post_meta($post_id);
		
		global $wpdb;
		$wpdb -> show_errors();
		
		
		
		$query = "update $wpdb->pw_prefix"."post_meta set post_shares=".$total_shares." where post_id=".$post_id;
		$wpdb->query($query);
		return $total_shares;
	}
	
	
	

	
	
	////////////////// USER SHARES /////////////////////////
	function calculate_user_shares( $user_id, $mode='both' ){
		/*
		Calculates the total number of shares relating to a given user
		
		
		 * Parameters
		-$post_id : integer
		-$mode : string (optional)
		
		
		 * Options :
		-both (default) : Return both incoming and outgoing
		-incoming : Return shares attributed to the user's posts
		-outgoing : Return shares that the user has initiated
		
		 * Process
		-Lookup the given user_id in the Shares table
		-Modes :
		 -For incoming : Match to author_id column in Shares table
		 -For outgoing : Match to user_id column in Shares table
		-Add up (SUM) the total number of the shares column attributed to the user, according to $mode
		
		 * return : Array (number of shares)
		
		array(
		    'incoming' => {{integer}},
		    'outgoing' => {{integer}}
		    )
		*/
		$output = array();
		global $wpdb;
		$wpdb -> show_errors();
		if($mode =='incoming' || $mode=='both'){
			$user_share_report = user_share_report($user_id);
			//print_r($user_share_report);
			$incoming = 0;
			for ($i=0; $i <count($user_share_report) ; $i++) { 
				$incoming=$incoming + $user_share_report[$i]['shares'];
			}
			$output['incoming'] = $incoming;
		}
		
		if($mode == 'outgoing' || $mode =='both'){
			$user_posts_share_report = user_posts_share_report($user_id);
			//print_r($user_posts_share_report);
			$outgoing = 0;
			for ($i=0; $i <count($user_posts_share_report) ; $i++) { 
				$outgoing=$outgoing + $user_posts_share_report[$i]['total_shares'];
			}
			$output['outgoing'] = $outgoing;
		}
		return $output;
	}
	
	function cache_user_shares( $user_id, $mode ){
		/*
		Caches the total number of shares relating to a given user
		Process
		
		Run calculate_user_shares()
		Update the post_shares column in the user Meta table
		return : integer (number of shares)
		 */
		 
		 
		 
		$user_shares = calculate_user_shares($user_id,$mode);
		//print_r($user_shares);
		global $wpdb;
		$wpdb -> show_errors();
		
		$total_user_shares=0;
		if(isset($user_shares['incoming'])) $total_user_shares = $user_shares['incoming'];
		if(isset($user_shares['outgoing'])) $total_user_shares = $user_shares['outgoing'];
		
		//check if cached before and replace json values
		$old_shares = get_user_shares($user_id);
		//print_r($old_shares);
		//print_r($user_shares);
		
		if(!is_null($old_shares))
		{
			
			$old_shares = (array)json_decode($old_shares);
			if($mode =='incoming' || $mode='both')
				if(isset($user_shares['incoming'])) $old_shares['incoming'] = $user_shares['incoming'];
			if($mode =='outgoing' || $mode='both')
				if(isset($user_shares['outgoing'])) $old_shares['outgoing'] = $user_shares['outgoing'];
			
		}else{
			add_record_to_user_meta($user_id);	
			$old_shares = $user_shares;		
		}
		//$total_user_shares = ($user_shares['incoming']+$user_shares['outgoing']);
		$query = "update $wpdb->pw_prefix"."user_meta set share_points=".$total_user_shares.",share_points_meta='".json_encode($old_shares)."' where user_id=".$user_id;
		//print_r($query);
		$wpdb->query($query);
		
		return $total_user_shares;
		 
	}
	
	
	function get_user_shares($user_id){
			
		global $wpdb;
		$wpdb -> show_errors();
			
		$query = "select share_points_meta from $wpdb->pw_prefix"."user_meta where user_id=".$user_id;
		return $wpdb->get_var($query);
		
	}
    ////////////////  HELPER FUNCTIONS  //////////////////////
	function add_new_cron_logs($cron_logs_array){
		$cron_logs_count = count($cron_logs_array);
		
		for ($i=0; $i <$cron_logs_count ; $i++) {
			$query = "insert into " ;
			
		}
		
	}
	
	function create_cron_log_object($time_start,$time_end,$number_of_posts=null,$function_type,$process_id=null,$query_args=null){
			$current_cron_log_object = new cron_logs_Object();	
			$current_cron_log_object->function_type = $function_type;
			$current_cron_log_object->time_start =$time_start;// {{timestamp}}
			$current_cron_log_object->posts=$number_of_posts;// {{number of posts}}
			$current_cron_log_object->process_id=$process_id;// {{feed id / post_type slug}}
			$current_cron_log_object->query_args = $query_args;
			$current_cron_log_object->time_end=$time_end;// {{timestamp}}
			$current_cron_log_object->timer=(strtotime( $current_cron_log_object->time_end )-strtotime( $current_cron_log_object->time_start))*1000 ;// {{milliseconds}}
			//$current_cron_log_object->timer_average = $current_cron_log_object->timer / $current_cron_log_object->posts;// {{milliseconds}}	
			insert_cron_log_to_db($current_cron_log_object);
			return $current_cron_log_object;
	}
	
	function insert_cron_log_to_db($cron_log_object){
		global $wpdb;
		$wpdb -> show_errors();
		
		if(is_null($cron_log_object->posts) )$cron_log_object->posts = 'null';
		$query = "INSERT INTO `wp_postworld_a1`.`wp_postworld_cron_logs`
					(
					`function_type`,
					`process_id`,
					`time_start`,
					`time_end`,
					`timer`,
					`posts`,
					`query_args`)
					VALUES
					(
					'".$cron_log_object->function_type."',
					'".$cron_log_object->process_id."',
					'".$cron_log_object->time_start."',
					'".$cron_log_object->time_end."',
					'".$cron_log_object->timer."',
					".$cron_log_object->posts.",
					'".$cron_log_object->query_args."')";
					
		$wpdb->query($query);
	}
	
	
    
  
?>