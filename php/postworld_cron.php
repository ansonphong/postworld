<?php
function pw_add_schedules($schedules) {
	// WordPress already comes with some schedules
	// WP Schedules : hourly, twicedaily, daily
	// Add a few more schedules into the mix

	$schedules['one_minute'] = array(
		'interval' => 60,
		'display' => __('Every Minute')
	);
	$schedules['five_minutes'] = array(
		'interval' => 300,
		'display' => __('Every 5 Minutes')
	);
	$schedules['ten_minutes'] = array(
		'interval' => 600,
		'display' => __('Every 10 Minutes')
	);
	$schedules['fifteen_minutes'] = array(
		'interval' => 900,
		'display' => __('Every 15 Minutes')
	);
	$schedules['thirty_minutes'] = array(
		'interval' => 1800,
		'display' => __('Every 30 Minutes')
	);
	$schedules['fourtyfive_minutes'] = array(
		'interval' => 2700,
		'display' => __('Every 45 Minutes')
	);

	$schedules['weekly'] = array(
		'interval' => 604800,
		'display' => __('Every Week')
	);

	$schedules['monthly'] = array(
		'interval' => 2419200,
		'display' => __('Every Week')
	);

	return $schedules;
}
add_filter( 'cron_schedules', 'pw_add_schedules'); 


function pw_insert_cron_log($cron_log){
	// Inserts an entry into postworld_cron_logs tables

	global $wpdb;
	
	$timer = (strtotime( $cron_log['time_end'] )-strtotime( $cron_log['time_start']));

	$default_cron_log = array(
		'function_type'	=>	null,
		'process_id'	=>	null,
		'time_start'	=>	null,
		'time_end'		=>	null,
		'timer'			=>	$timer,
		'posts'			=>	null,
		'query_args'	=>	null,
		);

	$cron_log = array_replace_recursive($default_cron_log, $cron_log);

	///// INSERT /////
	return $wpdb->insert(
		$wpdb->pw_prefix . 'cache',
		$cron_log
		);

}
	
?>