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
?>

<pre>
- Add view option
- Feed into PW live feed variables `query.related_query`, to load asyncronously
</pre>

<pre><code><?php echo json_encode( $options, JSON_PRETTY_PRINT); ?></code></pre>