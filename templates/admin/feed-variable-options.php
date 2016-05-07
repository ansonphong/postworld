<?php
	$post_views = pw_config( 'post_views.options.feeds' );
?>
<?php foreach( $post_views as $post_view ): ?>
	<?php
		$vars['post_view'] = $post_view;
	?>
	<div ng-if="<?php echo $vars['ng_model'] ?>.view.current == '<?php echo $post_view ?>'">
		<?php do_action( 'pw_feed_options_view_'.$post_view, $vars ); ?>
	</div>
<?php endforeach ?>
