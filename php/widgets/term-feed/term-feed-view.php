<?php
/*_____                     _____             _ 
 |_   _|__ _ __ _ __ ___   |  ___|__  ___  __| |
   | |/ _ \ '__| '_ ` _ \  | |_ / _ \/ _ \/ _` |
   | |  __/ |  | | | | | | |  _|  __/  __/ (_| |
   |_|\___|_|  |_| |_| |_| |_|  \___|\___|\__,_|
                                                
/////////////// TERM FEED - VIEW ///////////////*/
$print_term_feed = array(
		'template'	=>	$template_id,
		'terms' => array(
			'taxonomies'    =>  array( $taxonomy ),
			),
		);
echo pw_print_term_feed( $print_term_feed );

?>