<?php

function pw_time_units(){
	///// TIME PERIODS /////
	$ONE_MINUTE	=	60;				// seconds in a minute
	$ONE_HOUR	=	60*$ONE_MINUTE;	// seconds in an hour
	$ONE_DAY	=	24*$ONE_HOUR;	// seconds in one day
	$ONE_WEEK	= 	7*$ONE_DAY;		// seconds in one week
	$ONE_YEAR	= 	365*$ONE_DAY;	// seconds in one year
	$ONE_MONTH	= 	$ONE_YEAR/12;	// seconds in one month

	return compact("ONE_MINUTE", "ONE_HOUR", "ONE_DAY", "ONE_WEEK", "ONE_MONTH", "ONE_YEAR");
}

function get_postworld_uri(){
	global $pwSiteGlobals;
	$pw_url = pw_get_obj(  $pwSiteGlobals, 'paths.postworld.url' );
	// If the Postworld URL is defined in the config
	if( $pw_url )
		return $pw_url;	
	// Otherwise return the default
	else
		return plugins_url().'/postworld/';
}


?>