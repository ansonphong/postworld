<?php
//////////////////// CRON TASKS ////////////////////
///// SETUP HOURLY HOOK /////
add_filter( 'cron_schedules', 'cron_add_hourly' ); 
function cron_add_hourly( $schedules ) {
	// Adds once weekly to the existing schedules.
	$schedules['weekly'] = array(
		'interval' => 3600,
		'display' => __( 'Once Weekly' )
	);
	return $schedules;
}

///// PREFIX SCHEDULE /////
add_action( 'wp', 'prefix_setup_schedule' );
/**
 * On an early action hook,check if the hook is scheduled - if not, schedule it.
 */
function prefix_setup_schedule() {
	if ( ! wp_next_scheduled( 'prefix_hourly_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'prefix_hourly_event');
	}
}

///// DO ACTIONS /////
add_action( 'prefix_hourly_event', 'prefix_do_this_hourly' );

/**
 * On the scheduled action hook, run a function.
 */
function prefix_do_this_hourly() {
	cache_all_rank_scores();
}

wp_cron();

?>