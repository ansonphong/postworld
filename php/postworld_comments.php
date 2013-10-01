<?php
	
	function get_comment_points($comment_id){
		
		/*
			Get the total number of points of the given comment from the points column in wp_postworld_comment_meta
			return : integer (number of points) 
		*/
		global $wpdb;
		$wpdb -> show_errors();
	
		$query = "SELECT comment_points FROM " . $wpdb->pw_prefix.'comment_meta' . " Where comment_id=" . $comment_id;
		//echo ($query);
		$comment_points = $wpdb -> get_var($query);
		if ($comment_points == null)
			$comment_points=0;
		
		return $comment_points;
		 
	} 
	
	function calculate_comment_points($comment_id){
		/* 
			Adds up the points from the specified comment, stored in wp_postworld_comment_points
			Stores the result in the points column in wp_postworld_comment_meta 
		 	return : integer (number of points)
		 */
		global $wpdb;
		$wpdb -> show_errors();
	
		//first sum points
		$query = "select SUM(points) from ".$wpdb->pw_prefix.'comment_points'." where comment_id=" . $comment_id;
		$points_total = $wpdb -> get_var($query);
		//echo("\npoints cal" . $points_total);
		if($points_total==null || $points_total =='') $points_total=0;
		
		return $points_total;
	} 
	
	
	
	function cache_comment_points($comment_id){
		
		/*
			Calculates given post's current points with calculate_post_points()
			Stores points it in wp_postworld_post_meta table_ in the post_points column
			return : integer (number of points)
		*/
		global $wpdb;
		$wpdb -> show_errors();
		$total_points = calculate_comment_points($comment_id);
		 //update wp_postworld_meta
		$query = "update ".$wpdb->pw_prefix.'comment_meta'." set comment_points=" . $total_points . " where comment_id=" . $comment_id;
		$result =$wpdb -> query($query);
		echo(json_encode($result));
		
		if ($result === FALSE || $result === 0){
			//echo 'false <br>';
			//insertt new row for this comment in comment_meta, no points was added
			add_record_to_comment_meta($comment_id,$total_points);
		}
		return $total_points;
		
		
	}    


	function add_record_to_comment_meta($comment_id,$total_points=0){
		/*
		 This function gets comment data inserts a record in wp_postworld_comment_meta table
		 * Parameters:
		 * -comment_id
		 * -optional : points (default value=0)
		 * optional parameters are not sent if the function adds a new row
		 */
		 //echo "comment_id=".$comment_id;
		global $wpdb;	
		$wpdb-> show_errors();
		
		 
		$query = "select * from ".$wpdb->pw_prefix."comment_meta where comment_id=".$comment_id;
		$row = $wpdb->get_row($query);
		
		if($row ==null){
			
			$comment_data = get_comment($comment_id);
			//echo("<br>".json_encode($comment_data)); 
			//print_r($comment_data);
		
			
			$comment_data =  get_comment( $comment_id, ARRAY_A );
			//echo json_encode($post_data);
			$query = "insert into ".$wpdb->pw_prefix.'comment_meta'." values("
					.$comment_id.","
					.$comment_data['comment_post_ID'].","
					.$total_points
					.")";
					
			echo $query."<br>";
			$wpdb -> query($wpdb -> prepare($query));
		}
	}
	
?>