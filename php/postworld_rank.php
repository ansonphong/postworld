<?php

function calculate_rank_score ( $post_id ) {
	/*
	• Calculates Rank Score based on rank equation
	• Cache the result in wp_postworld_meta in the rank_score column
	• Returns the Rank Score 
	return : integer (Rank Score)
	*/

	//////////// SETUP ////////////
	global $TIME_UNITS;
	extract($TIME_UNITS);

	//////////// SET DEFAULTS ////////////
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


	//////////// GET POST VALUES ////////////
	// TIME
	$post_time =	get_post_time('U', true, $post_id);
	$current_time =	time();
	$TIME = $current_time - $post_time;

	// POINTS 
	$POINTS = get_post_points($post_id);

	// COMMENTS
	$comments_count = wp_count_comments( $post_id );
	$COMMENTS = $comments_count->approved;


	//////////// SET MIN & MAX VALUES ////////////

	////// TIME START //////
	// Start time after X period of time
	$TIME_START = 3*$ONE_HOUR;
	if ( $TIME < $TIME_START )
		$TIME = $TIME_START;

	////// TIME END //////
	// End time after X period of time
	$TIME_END = $ONE_YEAR;
	if ( $TIME > $TIME_END )
		$TIME = $TIME_END;
		
	////// FREE RANK //////
	// Calculate Free Rank curve
	// If post has X down votes, disqualify from Free Rank
	if ( $TIME < $FREE_RANK_PERIOD && $POINTS > -2 ){
		$FREE_RANK_CURVE = pow( (1-($TIME/$FREE_RANK_PERIOD)), 0.9);
		$FREE_RANK = $FREE_RANK_CURVE * $FREE_RANK_POINTS ;
	}
	else
		$FREE_RANK = 0;


	//////////// CURRENTS ////////////
	///// FRESH CURRENT /////
	// ENABLE FRESH CURRENT
	if ( $TIME > $FRESH_PERIOD )
		$FRESH_CURRENT = 1; 

	///// ARCHIVE CURRENT /////
	// ENABLE ARCHIVE CURRENT
	if ( $TIME < $ARCHIVE_PERIOD )
		$ARCHIVE_CURRENT = 1; 

	//////////// COMMENTS ////////////
	// KILL COMMENTS IF NO POINTS
	if ($POINTS < 0)
		$COMMENTS = 0;

	////////// RANK SCORE EQUATION //////////
	$RANK_SCORE_RANGE = 1000;
	$TIME = pow($TIME, $TIME_CURVE);

	$RANK_SCORE = ((( $POINTS*$POINTS_WEIGHT + $COMMENTS*$COMMENTS_WEIGHT ) / ($TIME * $TIME_WEIGHT)  ) * $RANK_SCORE_RANGE) * $FRESH_CURRENT * $ARCHIVE_CURRENT + $FREE_RANK;

	////////// RETURN //////////
	// IF RANK SCORE IS NEGATIVE
	if ($RANK_SCORE < 0) $RANK_SCORE = 0;

	// IF RANK SCORE IS OVER 1000
	if ($RANK_SCORE > $RANK_SCORE_RANGE) $RANK_SCORE = $RANK_SCORE_RANGE;

	return round($RANK_SCORE);

}

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



function cache_rank_score ( $post_id ){
	/*• Calculate rank_score with calculate_rank_score() method
	• Cache the result in wp_postworld_meta in the rank_score column
	return :  integer (Rank Score)*/ 
	global $wpdb;
	$wpdb -> show_errors();
	$post_rank_score = calculate_rank_score($post_id);
	$query ="update $wpdb->pw_prefix"."post_meta set rank_score=".$post_rank_score." where post_id=".$post_id;
	$result = $wpdb->query($query);
}
?>