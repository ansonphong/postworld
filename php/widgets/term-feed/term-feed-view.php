<?php
/*_____                     _____             _ 
 |_   _|__ _ __ _ __ ___   |  ___|__  ___  __| |
   | |/ _ \ '__| '_ ` _ \  | |_ / _ \/ _ \/ _` |
   | |  __/ |  | | | | | | |  _|  __/  __/ (_| |
   |_|\___|_|  |_| |_| |_| |_|  \___|\___|\__,_|
                                                
/////////////// TERM FEED - VIEW ///////////////*/
//echo json_encode($OPTIONS);

$print_term_feed = array(
		'template'	=>	$OPTIONS['template_id'],
		'terms' => array(
			'taxonomies'    =>  array( $OPTIONS['taxonomy'] ),
			'args'          =>  array(
				'number'	=>	$OPTIONS['terms_number'],
				'orderby'	=>	$OPTIONS['terms_orderby'],
				'order'		=>	$OPTIONS['terms_order'],
				),
			),
		);
echo pw_print_term_feed( $print_term_feed );

?>