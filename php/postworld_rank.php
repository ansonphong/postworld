<?php
function get_rank_score( $post_id, $method ){
	/*  • Gets the Rank Score of the given post, using calculate_rank_score()
		• Retrieves from the rank_score column in wp_postworld_meta
		return : integer (Rank Score)
	 */
	global $wpdb;
	$wpdb->show_errors();
	
	$query ="select rank_score from $wpdb->pw_prefix"."post_meta where post_id=".$post_id;
	echo($query);
	$rank_score = $wpdb->get_var($query);
	if($rank_score == null)
		$rank_score = 0;
	
	return $rank_score;
}


function calculate_rank_score ( $post_id ) {
	/*
	• Calculates Rank Score based on rank equation
	• Cache the result in wp_postworld_meta in the rank_score column
	• Returns the Rank Score 
	return : integer (Rank Score)
	*/

	if( defined( 'PW_CALCULATE_RANK_SCORE_FUNCTION' ) ){
		$rank_score = call_user_func( PW_CALCULATE_RANK_SCORE_FUNCTION, $post_id );
		$rank_score = (int) $rank_score;
		//$rank_score = round($rank_score);
		return $rank_score;
	}
	else
		return false;

}


function cache_rank_score ( $post_id ){
	/*• Calculate rank_score with calculate_rank_score() method
	• Cache the result in wp_postworld_meta in the rank_score column
	return :  integer (Rank Score)*/ 
	global $wpdb;
	$wpdb -> show_errors();
	$post_rank_score = calculate_rank_score($post_id);
	
	add_record_to_post_meta($post_id);
	$query ="update $wpdb->pw_prefix"."post_meta set rank_score=".$post_rank_score." where post_id=".$post_id;
	$result = $wpdb->query($query);

	return $post_rank_score;
}
?>