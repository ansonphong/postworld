<?php


function calculate_rank_score ( $post_id, $points, $comments, $time ) {
	/*
	• Calculates Rank Score based on rank equation
	• Cache the result in wp_postworld_meta in the rank_score column
	• Returns the Rank Score 
	return : integer (Rank Score)
	*/

	////// SET DEFAULTS //////
	global $pw_defaults;
	$equasion = $pw_defaults['rank']['equations']['default'];

	$POINTS_WEIGHT =		$equasion['points_weight'];;
	$COMMENTS_WEIGHT =		$equasion['comments_weight'];;
	$TIME_WEIGHT =			$equasion['time_weight'];
	$TIME_CURVE =			$equasion['time_compression'];;

	$FRESH_PERIOD =			$equasion['fresh_period'];
	$FRESH_MULTIPLIER =		$equasion['fresh_multiplier'];

	$ARCHIVE_PERIOD =		$equasion['archive_period'];
	$ARCHIVE_MULTIPLIER =	$equasion['archive_multiplier'];

	$FREE_RANK_SCORE =		$equasion['free_rank_score'];
	$FREE_RANK_PERIOD =		$equasion['free_rank_period'];


	///// GET POST VALUES /////

	// TIME
	$post_time =	get_post_time('U', true, $post_id);
	$current_time =	time();
	$TIME = $current_time - $post_time;

	// POINTS 
	$POINTS = get_points($post_id);

	// COMMENTS
	$comments_count = wp_count_comments( $post_id );
	$COMMENTS = $comments_count->approved;


	$rank_score = $POINTS;
	return $rank_score;

}


?>