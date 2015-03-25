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

	return $schedules;
}
add_filter( 'cron_schedules', 'pw_add_schedules'); 


class pw_cron_logs_Object {
	public $function_type;// {{feed/post_type}}
	public $process_id ;// {{feed id / post_type slug}}
	public $time_start;// {{timestamp}}
	public $time_end;// {{timestamp}}
	public $timer;// {{milliseconds}}
	public $posts;// {{number of posts}}
	//public $timer_average;// {{milliseconds}}
	public $query_args ;// {{ query_vars Object }}
}

class pw_query_vars_Object  {
	public $post_type;
	public $class;
	public $format;
}
	
?>