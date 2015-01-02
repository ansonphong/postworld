<?php if( $vars['tag'] == 'siblings' ){
	global $post;
	$post_parent_id = $post->post_parent;
	$post_parent = pw_get_post( $post_parent_id, 'micro' );
	?>
	<a href="<?php echo $post_parent['post_permalink'] ?>">
	<h2>
		<?php echo $post_parent['post_title'] ?> ›
	</h2>
	</a>
<?php } ?>

<div class="pw-shortcode subpages feed <?php echo $vars['size'] ?> <?php echo $vars['class'] ?>">
	<?php
		echo pw_print_feed( $vars['feed'] );	
	?>
</div>