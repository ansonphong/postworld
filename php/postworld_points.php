<?php
	global $pw_table_names;
	class set_Points_Output {
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
	
	
	function get_post_points($post_id) {
		/*
		• Get the total number of points of the given post from the points column in 'wp_postworld_meta' table
		return : integer
		*/
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT * FROM " . $pw_table_names['post_meta'] . " Where post_id=" . $post_id;
		//echo ($query);
		$postPoints = $wpdb -> get_row($query);
		if ($postPoints != null)
			return $postPoints -> post_points;
		else
			return 0;
	}
	
	function set_post_points($post_id, $user_id, $add_points) {
		/*
		 • $add_points is an integer
		 • Write row in wp_postworld_points
		 • Passing 0 deletes row
		 • Check that user role has permission to write that many points (wp_options) <<<<
		 • Check is the user has already voted points on that post
		 • Also update cached points in wp_postworld_meta directly
		 • Add Unix timestamp to time column in wp_postworld_points
		 return : Object
		 points_added : {{integer}} (points which were successfully added)
		 points_total : {{integer}} (from wp_postworld_meta)
		 */
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
		$points_total = 0;
	
		//get row to check if found or not, also because when is going to be deleted to return reduced points
		$query = "SELECT * FROM ".$pw_table_names['post_points']." Where post_id=" . $post_id . " and user_id=" . $user_id;
		$postPointsRow = $wpdb -> get_row($query);
	
		//check if it is required to delete the row and update cashed points (triggers)
		if ($add_points == 0) {
			if ($postPointsRow != null) {
				$query = "delete from ".$pw_table_names['post_points']." where post_id=" . $post_id . " and user_id=" . $user_id;
				$wpdb -> query($wpdb -> prepare($query));
				$add_points = -($postPointsRow -> points);
				$points_total = get_points($post_id);
			} else {
				//nothing was done.
				$points_total = get_points($post_id);
				$add_points = 0;
			}
	
		} else {
			//check if row already present, and user has the authority to add much points,else create and add unix timestamp
			if ($postPointsRow != null) {
				//echo ('NOTT NULL');
				// TODO: check if user can add more points from wp-options ?????????????
				$userCanAddPoints = can_user_add_more_points($user_id,$postPointsRow->points,$add_points);
				if ($userCanAddPoints) {
					$query = "Update ".$pw_table_names['post_points']." set post_points=post_points + ". $add_points . " Where post_id=" . $post_id . " and user_id=" . $user_id;
					//echo($query);
					$wpdb -> query($wpdb -> prepare($query));
					$points_total = get_points($post_id);
				} else {// set added points equal zero and call get points only
					$add_points = 0;
					// user can't add points and no points were added
					$points_total = get_points($post_id);
				}
	
			} else {// row not found, insert
				//echo ('NULL');
				$query = "insert into ".$pw_table_names['post_points']." values(" . $post_id . "," . $user_id . "," . $add_points . ",null)";
				//time stamp added automatically
				//echo($query);
				$wpdb -> query($wpdb -> prepare($query));
				$points_total = get_points($post_id);
			}
	
		}
		//prepare output format
	
		/*echo("total Pooints:".$points_total);
		 echo("added points:" .$add_points);*/
	
		$output = new set_Points_Output();
		$output -> points_added = $add_points;
		$output -> points_total = $points_total;
		return $output;
	}
	
	function calculate_post_points($post_id) {
		/*
		• Adds up the points from the specified post, stored in ".$pw_table_names['post_points']."
		• Stores the result in the points column in wp_postworld_meta
		return : integer (number of points)
		*/
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
	
		//first sum points
		$query = "select SUM(points) from ".$pw_table_names['post_points']." where post_id=" . $post_id;
		$points_total = $wpdb -> get_var($query);
		echo("\npoints cal" . $points_total);
	
		//update wp_postworld_meta
		$query = "update ".$pw_table_names['post_meta']." set post_points=" . $points_total . " where post_id=" . $post_id;
		$result =$wpdb -> query($wpdb -> prepare($query));
		//TODO
		if ($result === 0){
			//insertt new row for this post in post_meta
			
		}
		return $points_total;
	
	}
	
	function has_voted_on_post($post_id, $user_id) {
		/*
		 • Check wp_postworld_points to see if the user has voted on the post
		 • Return the number of points
		 return : integer
		 */
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT * FROM ".$pw_table_names['post_points']." Where post_id=" . $post_id . " and user_id=" . $user_id;
		$postPointsRow = $wpdb -> get_row($query);
	
		if ($postPointsRow != null)
			return $postPointsRow -> post_points;
		else
			return 0;
	
	}
	//TODO : remove the view
	function get_user_points_voted_to_posts($user_id) {
		/*
		 • Get array of all posts by given user
		 • Get points of each post from wp_postworld_meta
		 • Add all the points up
		 return : integer (number of points)
		 */
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
	
		//create view to combine both then select from the view
		//SELECT * FROM wp_postworld_a1.get_user_points_view;
		$query = "SELECT SUM(post_points) as total_points FROM ".$pw_table_names['post_meta']." Where author_id=" . $user_id;
		$total_points = $wpdb -> get_var($query);
		if ($total_points != null) {
			//echo("total_points:" . $total_points);
			return $total_points;
		} else
			return 0;
	
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
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT SUM(post_points) as total_points, Count(*) as total_posts FROM ".$pw_table_names['post_points']." Where user_id=" . $user_id;
		$total_points = $wpdb -> get_results($query);
	
		foreach ($total_points as $row) {
			echo $row -> total_points . ",";
			echo $row -> total_posts . ",";
			echo($row -> total_points / $row -> total_posts);
	
			$output = new get_user_votes_report_Output();
			$output -> total_posts = $row -> total_posts;
			$output -> total_points = $row -> total_points;
			$output -> average_points = ($row -> total_points / $row -> total_posts);
			return $output;
		}
	
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
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT * FROM ".$pw_table_names['post_points']." Where user_id=" . $user_id;
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
	
	function get_user_vote_power ( $user_id ){
		/*
		 	• Checks to see user's WP roles with get_user_role()
			• Checks how many points the user's role can cast, from wp_postworld_user_roles table, under vote_points column
			return : integer (the number of points the user can cast)
		 */
		global $pw_table_names;
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
		
		$query = "select vote_points from ".$pw_table_names['user_roles']." where user_role='".$current_user_role."'";
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
	
	function cache_post_points() {
		/*
		 • Cycles through each post in each post_type with points enabled
		 • Calculates each post's current points with calculate_points()
		 • Stores points it in wp_postworld_meta 'points' column
		 • return : cron_logs Object (add to table wp_postworld_cron_logs)
		 */
		 
		//Post_type = page/post, 
		
		global $pw_table_names;
		global $wpdb;
		$wpdb -> show_errors();
		
		//get wp_options Enable Points ( postworld_points ) field and get post types enabled for points - http://codex.wordpress.org/Function_Reference/get_option
		
		// select all post ids of posts that their post types are enabled for points
		
		//update postworld_meta
		
		//prepare cron_log object -- for each type create a cron_log option
		
		
	
		/*$query = "Update wp_postworld_meta set points = (select SUM(points) from wp_postworld_points where wp_postworld_meta.id = wp_postworld_points.id ) where wp_postworld_meta.id = wp_postworld_points.id";
		$wpdb -> query($wpdb -> prepare($query));*/
		
		 
		 
	}

	


?>