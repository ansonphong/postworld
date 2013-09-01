<?php

function get_points ( $post_id ){
	/*
	• Get the total number of points of the given post from the points column in 'wp_postworld_meta' table
	return : integer
	*/
}

function set_points ( $post_id, $user_id, $add_points ){
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
}



function calculate_points ( $post_id ){
	/*
	• Adds up the points from the specified post, stored in wp_postworld_points
	• Stores the result in the points column in wp_postworld_meta
	return : integer (number of points)
	*/
}


function has_voted ( $post_id, $user_id ){
	/*
	• Check wp_postworld_points to see if the user has voted on the post
	• Return the number of points
	return : integer
	*/
}


function get_user_points ( $user_id ){
	/*
	• Get array of all posts by given user
	• Get points of each post from wp_postworld_meta
	• Add all the points up
	return : integer (number of points)
	*/
}



function get_user_votes ( $user_id ){
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
}


function get_user_votes_by_post ( $user_id ){ //Can't have duplicate names for functions
	/*
	• Get all posts which user has voted on from wp_postworld_points
	return : Object
	     #for_each
	     post_id : {{integer}}
	     votes : {{integer}}
	     time : {{timestamp}}
	*/
}


function cache_points () {
	/*
	• Cycles through each post in each post_type with points enabled
	• Calculates each post's current rank with calculate_points()
	• Stores points it in wp_postworld_meta 'points' column
	*/
}

?>