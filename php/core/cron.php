<?php
function pw_add_schedules($schedules) {
	// WordPress already comes with some schedules
	// WP Schedules : hourly, twicedaily, daily
	// Add a few more schedules into the mix

	$schedules['one_second'] = array(
		'interval' => 1,
		'display' => __('Every Second','postworld')
	);
	$schedules['one_minute'] = array(
		'interval' => 60,
		'display' => __('Every Minute','postworld')
	);
	$schedules['five_minutes'] = array(
		'interval' => 300,
		'display' => __('Every 5 Minutes','postworld')
	);
	$schedules['ten_minutes'] = array(
		'interval' => 600,
		'display' => __('Every 10 Minutes','postworld')
	);
	$schedules['fifteen_minutes'] = array(
		'interval' => 900,
		'display' => __('Every 15 Minutes','postworld')
	);
	$schedules['thirty_minutes'] = array(
		'interval' => 1800,
		'display' => __('Every 30 Minutes','postworld')
	);
	$schedules['fourtyfive_minutes'] = array(
		'interval' => 2700,
		'display' => __('Every 45 Minutes','postworld')
	);

	$schedules['weekly'] = array(
		'interval' => 604800,
		'display' => __('Every Week','postworld')
	);

	$schedules['monthly'] = array(
		'interval' => 2419200,
		'display' => __('Every Week','postworld')
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
	$wpdb->insert(
		$wpdb->postworld_prefix . 'cron_logs',
		$cron_log
		);

	return $cron_log;

}
	
?>