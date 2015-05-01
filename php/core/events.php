<?php
/**
 * This function gets an array of registered 'event' post types
 * @return [array] A 1D array of strings which are the names of post types
 *
 * To add / register additional post types as an event, use this filter
 *
 * 		add_filter( 'pw_event_post_types', 'theme_event_post_types' );
 *		function theme_event_post_types( $post_types ){
 *			$post_types[] = 'post_type_name';
 *			return $post_types;
 *		}
 * 
 */
function pw_event_post_types(){
	return apply_filters( 'pw_event_post_types', array( 'event' ) );
}

/**
 * This is a filter function intended to be added to the 'pw_get_post_complete_filter' filter hook
 * It adds plain-language meta-data for the type of event
 * @param $post [array] A typical Postworld post array
 * @return [array] The post with an added 'event_meta' key
 *
 * Add this line to the theme to employ this filter:
 *
 * 		add_filter( 'pw_get_post_complete_filter', 'pw_event_meta_filter' );
 *
 */
function pw_event_meta_filter( $post ){

	$post_type = _get( $post, 'post_type' );
	if( !$post_type || empty($post) )
		return $post;

	// Get the event post types
	$event_post_types = pw_event_post_types();

	// If not an event post type, return post
	if( !in_array( $post_type, $event_post_types ) )
		return $post;

	///// META /////
	$meta = array(
		'multiple_days'		=>	false,
		'multiple_months'	=>	false,
		'multiple_years'	=>	false,
		'date_line'			=>	'',
		'time_line'			=>	'',
		);

	///// GET CUSTOM VARS /////
	$vars = array(
		'start_date_key' 	=> 'post_meta.pw_event.date.start_date',
		'end_date_key'		=> 'post_meta.pw_event.date.end_date'
		);
	$vars = apply_filters( 'pw_event_meta_filter_vars', $vars );

	///// START & END TIMES /////
	$start 	= 	_get( $post, $vars['start_date_key'] );
	$end 	= 	_get( $post, $vars['end_date_key'] );

	///// MULTIPLE DAY/MONTH/YEAR EVENTS BOOLEANS /////
	$start_timestamp	= strtotime( $start );
	$end_timestamp		= strtotime( $end );

	$start_day 		= date( 'd', $start_timestamp );
	$end_day 		= date( 'd', $end_timestamp );

	$start_month 	= date( 'm', $start_timestamp );
	$end_month 		= date( 'm', $end_timestamp );

	$start_year 	= date( 'Y', $start_timestamp );
	$end_year 		= date( 'Y', $end_timestamp );

	if( $start_day != $end_day || $start_month != $end_month || $start_year != $end_year )
		$meta['multiple_days'] = true;

	if( $start_month != $end_month || $start_year != $end_year )
		$meta['multiple_months'] = true;

	if( $start_year != $end_year )
		$meta['multiple_years'] = true;


	///// DATE LINE /////
	if( $meta['multiple_years'] ){
		$format = 'F j, Y';
		$meta['date_line'] = date( $format, $start_timestamp ) . ' - ' . date( $format, $end_timestamp );
	}
	elseif( $meta['multiple_months'] ){
		$meta['date_line'] =
			date( 'F', $start_timestamp ) . ' ' . date( 'j', $start_timestamp ) . ' - ' .
			date( 'F', $end_timestamp )   . ' ' . date( 'j', $end_timestamp ) . ', ' .
			date( 'Y', $end_timestamp );
	}
	elseif( $meta['multiple_days'] ){
		$meta['date_line'] =
			date( 'F', $start_timestamp ).' '.	// Month
			date( 'j', $start_timestamp ) . '-' . date( 'j', $end_timestamp ) . ', ' .	// Days
			date( 'Y', $end_timestamp );
	}
	else{
		$meta['date_line'] = date( 'l, F j, Y', $start_timestamp );
	}

	///// TIME LINE /////
	if( _get( $post, 'post_meta.pw_event.date.all_day' ) )
		$meta['time_line'] = 'All Day';
	else{
		$format = 'g:i a';
		$meta['time_line'] = date( $format, $start_timestamp ) . ' - ' . date( $format, $end_timestamp );
	}

	$post['event_meta'] = $meta;

	return $post;

}


?>