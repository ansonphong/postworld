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
			
			return $current_cron_log_object;
	}
    
  
?>