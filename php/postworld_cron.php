<?php

function pw_add_intervals($schedules) {
	// add a 'per minute' interval
	$schedules['minute'] = array(
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
	return $schedules;
}
add_filter( 'cron_schedules', 'pw_add_intervals'); 

?>