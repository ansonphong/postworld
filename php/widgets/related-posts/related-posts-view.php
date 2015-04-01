<?/*
  ____      _       _           _   ____           _       
 |  _ \ ___| | __ _| |_ ___  __| | |  _ \ ___  ___| |_ ___ 
 | |_) / _ \ |/ _` | __/ _ \/ _` | | |_) / _ \/ __| __/ __|
 |  _ <  __/ | (_| | ||  __/ (_| | |  __/ (_) \__ \ |_\__ \
 |_| \_\___|_|\__,_|\__\___|\__,_| |_|   \___/|___/\__|___/
                                                           
////////////////////// RELATED POSTS //////////////////////*/?>
<?php
	global $post;
	if( _get( $OPTIONS, 'user_select' ) == 'current_author' )
		$OPTIONS['user_id'] = $post->post_author;

	$template = pw_get_user_widget_template( $OPTIONS['view'] );
	echo pw_ob_include( $template, $OPTIONS );
?>