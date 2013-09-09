<?php

///// TIME PERIODS /////
$ONE_MINUTE	=	60;				// seconds in a minute
$ONE_HOUR	=	60*$ONE_MINUTE;	// seconds in an hour
$ONE_DAY	=	24*$ONE_HOUR;	// seconds in one day
$ONE_WEEK	= 	7*$ONE_DAY;		// seconds in one week
$ONE_MONTH	= 	30*$ONE_DAY;	// seconds in one month
$ONE_YEAR	= 	365*$ONE_DAY;	// seconds in one year

global $TIME_UNITS;
$TIME_UNITS	=	compact("ONE_MINUTE", "ONE_HOUR", "ONE_DAY", "ONE_WEEK", "ONE_MONTH", "ONE_YEAR");

?>