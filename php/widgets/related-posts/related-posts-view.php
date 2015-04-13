<?/*
  ____      _       _           _   ____           _       
 |  _ \ ___| | __ _| |_ ___  __| | |  _ \ ___  ___| |_ ___ 
 | |_) / _ \ |/ _` | __/ _ \/ _` | | |_) / _ \/ __| __/ __|
 |  _ <  __/ | (_| | ||  __/ (_| | |  __/ (_) \__ \ |_\__ \
 |_| \_\___|_|\__,_|\__\___|\__,_| |_|   \___/|___/\__|___/
                                                           
////////////////////// RELATED POSTS //////////////////////*/?>
<?php
	global $post;
	$vars = $options['settings'];
	$vars['post_id'] = $post->ID;
	$instance_id = 'pwRelatedPosts_' . pw_random_string(8);

	if( empty( $vars['number'] ) )
		$vars['number'] = 10;

	$related_feed = array(
		'preload' 			=> 	$vars['number'],
		'load_increment'	=>	$vars['number'],
		'view'	=>	array(
			'current'	=>	$vars['view'],
			),
		'related_posts' => $vars
		);

?>
<script>
pw.feeds['<?php echo $instance_id ?>'] = <?php echo json_encode( $related_feed, JSON_PRETTY_PRINT ) ?>;  
</script>
<div live-feed='<?php echo $instance_id ?>'></div>

<?php /*
<pre><code><?php //echo json_encode( $options, JSON_PRETTY_PRINT); ?></code></pre>
*/ ?>