<?php
/*_   _               
 | | | |___  ___ _ __ 
 | | | / __|/ _ \ '__|
 | |_| \__ \  __/ |   
  \___/|___/\___|_|   
                      
///// USER - VIEW /////*/?>
<?php
	global $post;
	if( _get( $OPTIONS, 'user_select' ) == 'current_author' )
		$OPTIONS['user_id'] = $post->post_author;

	$template = pw_get_user_widget_template( $OPTIONS['view'] );
	echo pw_ob_include( $template, $OPTIONS );
?>