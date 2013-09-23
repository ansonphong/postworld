<?php
	
	
	class set_post_points_Output {
		public $points_total = 0;
		public $points_added = 0;
	}
	
	class get_user_votes_report_Output {
		public $total_posts = 0;
		public $total_points = 0;
		public $average_points = 0.0;
	}
	
	class get_user_votes_on_posts_Output{
		 public $post_id =0;
		 public $votes =0;
		 public $time =null;
	}
	
	
	class cron_logs_Object {
		public $type;// {{feed/post_type}}
		public $query_id;// {{feed id / post_type slug}}
		public $time_start;// {{timestamp}}
		public $time_end;// {{timestamp}}
		public $timer;// {{milliseconds}}
		public $posts;// {{number of posts}}
		public $timer_average;// {{milliseconds}}
		public $query_vars;// {{ query_vars Object }}
	}
	
	class query_vars_Object  {
		public $post_type;
		public $class;
		public $format;
	
	}
	
	/////////////// POST POINTS  ///////////////////
	function get_post_points($post_id) {
		/*
		• Get the total number of points of the given post from the points column in 'wp_postworld_meta' table
		return : integer
		*/
		
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT * FROM " . $wpdb->pw_prefix.'post_meta' . " Where post_id=" . $post_id;
		//echo ($query);
		$postPoints = $wpdb -> get_row($query);
		if ($postPoints != null)
			return $postPoints -> post_points;
		else
			return 0;
	}
	
	
	function calculate_post_points($post_id) {
		/*
		• Adds up the points from the specified post, stored in ".$wpdb->pw_prefix.'post_points'."
		• Stores the result in the points column in wp_postworld_meta
		return : integer (number of points)
		*/
		
		global $wpdb;
		$wpdb -> show_errors();
	
		//first sum points
		$query = "select SUM(post_points) from ".$wpdb->pw_prefix.'post_points'." where post_id=" . $post_id;
		$points_total = $wpdb -> get_var($query);
		//echo("\npoints cal" . $points_total);
		if($points_total==null || $points_total =='') $points_total=0;
		//update wp_postworld_meta
		$query = "update ".$wpdb->pw_prefix.'post_meta'." set post_points=" . $points_total . " where post_id=" . $post_id;
		$result =$wpdb -> query($wpdb -> prepare($query));
		
		if ($result === FALSE){
			//insertt new row for this post in post_meta, no points was added
			
			/*1- get post data
			 2-  Insert new record into post_meta
			 */
			add_recored_to_post_meta($post_id,$points_total);
			
		}
		return $points_total;
	
	}


	function cache_post_points ( $post_id ){
		/*• Calculates given post's current points with calculate_post_points()	
		• Stores points it in wp_postworld_post_meta table in the post_points column
		return : integer (number of points)*/
		return calculate_post_points($post_id);
		
	
	}
	
	/////////////// USER POINTS  ///////////////////
	function get_user_posts_points ( $user_id ){
		/*
		 * • Get the number of points voted to posts authored by the given user
		 * • Get cached points of user from wp_postworld_user_meta table post_points column
			return : integer (number of points)
		 
		 * */
		global $wpdb;
		$wpdb -> show_errors();
		
		$query ="Select post_points from ".$wpdb->pw_prefix.'user_meta'." where user_id=".$user_id;
		$user_votes_points = $wpdb -> get_var($query);
		if ($user_votes_points != null) {
			//echo("total_points:" . $total_points);
			return $user_votes_points;
		} else
			return 0;
		
	}
	
	function calculate_user_posts_points( $user_id ){
		/*	• Adds up the points voted to given user's posts, stored in wp_postworld_post_points
			• Stores the result in the post_points column in wp_postworld_user_meta
		return : integer (number of points)*/
		$total_user_points = get_user_posts_points($user_id);
		
		
		global $wpdb;
		$wpdb -> show_errors();
		
		$query = "update ".$wpdb->pw_prefix.'user_meta'." set post_points=".$total_user_points." where user_id=".$user_id;
		$result = $wpdb->query($query);
		if($result === FALSE){
			//insert new Record

						
		}
		
		
		return $total_user_points;
	}
	
	function cache_user_posts_points ( $user_id ){
	/*  • Runs calculate_user_post_points() Method
		• Caches value in post_points column in wp_postworld_user_meta table
		return : integer (number of points)*/
		
		return calculate_user_posts_points($user_id);
	}
	
	///////////// COMMNET POINTS ////////////////////
	
	
	function get_user_comments_points ( $user_id ){
		/*• Get the number of points voted to comments authored by the given user
		  • Get cached points of user from wp_postworld_user_meta table comment_points column
		return : integer (number of points)*/
		
		global $wpdb;
		$wpdb -> show_errors();
		
		$query ="select SUM(points) from ".$wpdb->pw_prefix.'comment_points'." where comment_author_id=".$user_id;
		$total_points = $wpdb -> get_var($query);
		
		if($total_points==null)
			$total_points=0;
		
		return $total_points;

	}
	
	function calculate_user_comments_points ( $user_id ){
		/*• Adds up the points voted to given user's comments, stored in wp_postworld_comment_points
		  • Stores the result in the post_points column in wp_postworld_user_meta
		return : integer (number of points)*/
		
		global $wpdb;
		$wpdb -> show_errors();		
		
		$total_comment_points = get_user_comments_points($user_id);
		//????????????????????????????ASK about addition
		$query = "update ".$wpdb->pw_prefix.'user_meta'." set post_points=post_points+".$total_comment_points." where user_id=".$user_id;
		$wpdb->query($query);
		
		return $total_user_points;
	}

	function cache_user_comments_points ( $user_id ){
		/*• Runs calculate_user_comment_points() Method
		  • Caches value in comment_points column in wp_postworld_user_meta table
		return : integer (number of points)*/
		return calculate_user_comments_points($user_id);
		
		
	}
	
	
	/////////////// GENERAL POINTS  ///////////////////
	
	function set_post_points($post_id, $add_points) {
		/*
		 • $add_points is an integer
		 • Write row in wp_postworld_points
		 • Passing 0 deletes row
		 • Check that user role has permission to write that many points (wp_options) <<<<HAIDY
		 • Check is the user has already voted points on that post
		 • Also update cached points in wp_postworld_meta directly
		 • Add Unix timestamp to time column in wp_postworld_points
		 return : Object
		 points_added : {{integer}} (points which were successfully added)
		 points_total : {{integer}} (from wp_postworld_meta)
		 */
		
		$user_id = get_current_user_id();
		

		global $wpdb;
		$wpdb -> show_errors();
		$points_total = 0;
	
		//get row to check if found or not, also because when is going to be deleted to return reduced points
		$query = "SELECT * FROM ".$wpdb->pw_prefix.'post_points'." Where post_id=" . $post_id . " and user_id=" . $user_id;
		$postPointsRow = $wpdb -> get_row($query);
	
		//check if it is required to delete the row and update cashed points (triggers)
		if ($add_points == 0) {
			if ($postPointsRow != null) {
				$query = "delete from ".$wpdb->pw_prefix.'post_points'." where post_id=" . $post_id . " and user_id=" . $user_id;
				$wpdb -> query($wpdb -> prepare($query));
				$add_points = -($postPointsRow -> post_points);
				$points_total = get_post_points($post_id);
			} else {
				//nothing was done.
				$points_total = get_post_points($post_id);
				$add_points = 0;
			}
	
		} else {
			//check if row already present, and user has the authority to add much points,else create and add unix timestamp
			if ($postPointsRow != null) {
				//echo ('NOTT NULL');
				// check if user can add more points from wp-options
				$userCanAddPoints = can_user_add_more_points($user_id,$postPointsRow->post_points,$add_points);
				if ($userCanAddPoints) {
					$query = "Update ".$wpdb->pw_prefix.'post_points'." set post_points=post_points + ". $add_points . " Where post_id=" . $post_id . " and user_id=" . $user_id;
					//echo($query);
					$wpdb -> query($wpdb -> prepare($query));
					$points_total = get_post_points($post_id);
				} else {// set added points equal zero and call get points only
					$add_points = 0;
					// user can't add points and no points were added
					$points_total = get_post_points($post_id);
				}
	
			} else {// row not found, insert
				//echo ('NULL');
				$query = "insert into ".$wpdb->pw_prefix.'post_points'." values(" . $post_id . "," . $user_id . "," . $add_points . ",null)";
				//time stamp added automatically
				//echo($query);
				$wpdb -> query($wpdb -> prepare($query));
				$points_total = get_post_points($post_id);
			}
	
		}
		//prepare output format
	
		/*echo("total Pooints:".$points_total);
		 //echo("added points:" .$add_points);*/
	
		$output = new set_post_points_Output();
		$output -> points_added = $add_points;
		$output -> points_total = $points_total;
		return $output;
	}
	
	
	
	function has_voted_on_post($post_id, $user_id) {
		/*
		 • Check wp_postworld_points to see if the user has voted on the post
		 • Return the number of points
		 return : integer
		 */
		
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT * FROM ".$wpdb->pw_prefix.'post_points'." Where post_id=" . $post_id . " and user_id=" . $user_id;
		$postPointsRow = $wpdb -> get_row($query);
	
		if ($postPointsRow != null)
			return $postPointsRow -> post_points;
		else
			return 0;
	
	}
	
	/*Later*/
	function has_voted_on_comment ( $comment_id, $user_id ){ 
	/*	• Check wp_postworld_comment_points to see if the user has voted on the comment
		• Return the number of points voted
		return : integer*/
		
	}
	
	
	function get_user_points_voted_to_posts($user_id) {
		/*
		 • Get array of all posts by given user
		 • Get points of each post from wp_postworld_meta
		 • Add all the points up
		 return : integer (number of points)
		 */
	
		global $wpdb;
		$wpdb -> show_errors();
	
		
		//SELECT * FROM wp_postworld_a1.get_user_points_view;
		$query = "SELECT SUM(post_points) as total_points FROM ".$wpdb->pw_prefix.'post_meta'." Where author_id=" . $user_id;
		$total_points = $wpdb -> get_var($query);
		if ($total_points != null) {
			//echo("total_points:" . $total_points);
			return $total_points;
		} else
			return 0;
	
	}
	
	
	function get_user_votes_on_posts($user_id) {
		/*
		 • Get all posts which user has voted on from wp_postworld_points
		 return : Object
		 #for_each
		 post_id : {{integer}}
		 votes : {{integer}}
		 time : {{timestamp}}
		 */
	
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT * FROM ".$wpdb->pw_prefix.'post_points'." Where user_id=" . $user_id;
		//echo($query);
		$user_votes_per_post = $wpdb -> get_results($query);
	
		$output = array();
		foreach ($user_votes_per_post as $row) {
			$singlePost = new get_user_votes_Output();
			$singlePost->post_id = $row->id;
			$singlePost->votes = $row->post_points;
			$singlePost->time = $row->time;
			
			//echo(serialize($singlePost));
			$output[] = $singlePost;
			
			
		}
		//echo(json_encode($output));
		 return $output;
		 
	}
	
	
	function get_user_votes_report($user_id) {
		/*
		 • Returns the 'recent/active' points activity of the user
		 • Get all posts which user has recently voted on from wp_postworld_points ( total_posts )
		 • Add up all points cast (total_points)
		 • Generate average (total_points/total_posts)
		 return : Object
		 total_posts: {{integer}} (number of posts voted on)
		 total_points: {{integer}} (number of points cast by up/down votes)
		 average_points: {{decimal}} (average number of points per post)
		 */
	
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT SUM(post_points) as total_points, Count(*) as total_posts FROM ".$wpdb->pw_prefix.'post_points'." Where user_id=" . $user_id;
		$total_points = $wpdb -> get_results($query);
	
		foreach ($total_points as $row) {
		//	echo $row -> total_points . ",";
		//	echo $row -> total_posts . ",";
		//	echo($row -> total_points / $row -> total_posts);
	
			$output = new get_user_votes_report_Output();
			$output -> total_posts = $row -> total_posts;
			$output -> total_points = $row -> total_points;
			$output -> average_points = ($row -> total_points / $row -> total_posts);
			return $output;
		}
	
	}
	
	function get_user_vote_power ( $user_id ){
		/*
		 	• Checks to see user's WP roles with get_user_role()
			• Checks how many points the user's role can cast, from wp_postworld_user_roles table, under vote_points column
			return : integer (the number of points the user can cast)
		 */
	
		global $wpdb;
		$current_user_role_output = get_user_role($user_id);
		//echo(json_encode($current_user_role_output));

		if(gettype($current_user_role_output) == "array") {
			//echo('arraaaaaay');
			$current_user_role = $current_user_role_output[0];
		}
		else if (gettype($current_user_role_output) == "string"){
			//echo('string<br>');
			$current_user_role = $current_user_role_output;
			//echo("role :" .json_encode($current_user_role));
		}
		
		$query = "select vote_points from ".$wpdb->pw_prefix.'user_roles'." where user_role='".$current_user_role."'";
		$vote_points = $wpdb->get_var($query);
		
		if($vote_points !=null) return $vote_points;
		else 0;
		
	}

	function can_user_add_more_points($user_id,$current_number_of_points,$added_points){
		$user_allowed_points = get_user_vote_power($user_id);
		if($user_allowed_points >=( $current_number_of_points + $added_points))
		return true;
		else return false;
	}
	
	
	function add_recored_to_post_meta($post_id, $points=0,$rank_score=0,$favorites=0){
		/*
		 This function gets all post data and inserts a record in wp_postworld_post_meta table
		 * Parameters:
		 * -post_id
		 * -optional : points (default value=0)
		 *			   rankd_score(default value=0)
		 * 				favorites(default value=0)
		 * optional parameters are not sent if the function adds a new row
		 */
		
			
	
		global $wpdb;	
		
		
		
		$format = get_post_format();
		if ( false === $format )
		$format = 'standard';
		
		$post_data= get_post( $post_id, ARRAY_A );
		//echo json_encode($post_data);
		$query = "insert into ".$wpdb->pw_prefix.'post_meta'." values("
				.$post_id.","
				.$post_data['post_author'].","
				."'post_class'"."," //TODO
				."'".$format."',"
				."'".$post_data['guid']."',"
				.$points.","
				.$rank_score.","
				.$favorites
				.")";
				
		//echo $query."<br>";
		$wpdb -> query($wpdb -> prepare($query));
		
	}
	
	
	/////////// CACHE FUNCTIONS      ////////////////
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
				$current_cron_log_object = new cron_logs_Object();	
				
				$current_cron_log_object->time_start = date("Y-m-d H:i:s");// {{timestamp}}
				//update postworld_meta
				$current_cron_log_object->posts=count($posts);// {{number of posts}}
				$current_cron_log_object->type = 'points';
				$current_cron_log_object->query_id=$post_types[$i];// {{feed id / post_type slug}}
				$current_cron_log_object->query_vars = array();
				foreach ($posts as $row) {
					
					//check if already there is a record for this post , yes then calculate points
					//else create a row with zero points
					calculate_post_points($row->ID);
					// {{feed/post_type}}
					
					//$current_cron_log_object->query_vars[] ="";// {{ query_vars Object: use pw_get_posts  }}
				}
				
				$current_cron_log_object->time_end=date("Y-m-d H:i:s");// {{timestamp}}
				$current_cron_log_object->timer=(strtotime( $current_cron_log_object->time_end )-strtotime( $current_cron_log_object->time_start))*1000 ;// {{milliseconds}}
				$current_cron_log_object->timer_average = $current_cron_log_object->timer / $current_cron_log_object->posts;// {{milliseconds}}
				//echo(json_encode($current_cron_log_object));
				$cron_logs[$current_cron_log_object->type][] = $current_cron_log_object;
				//echo json_encode($cron_logs);
		}
	
		//echo json_encode(($cron_logs));
			
		
		
		 
	}
	

?>