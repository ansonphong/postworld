<?php
	$user = pw_get_user( $vars['user_id'], 'all' );
?>

<div class="pw-user-widget author-feature">

	<div class="user-avatar">
		<a href="<?php echo $user['posts_url'] ?>">
			<img
				src="<?php echo $user['avatar']['medium']['url'] ?>"
				class="avatar-image">

			<h2>
				<?php echo $user['display_name']?>
			</h2>
		</a>
	</div>

	<div class="share-social">
		<?php echo pw_contact_methods_user_menu( $user['ID'] ); ?>
	</div>

	<div class="user-description">
		<?php echo _get( $user, 'usermeta.description' )?>
	</div>
	
	<?php if( _get($user,'usermeta.twitter') != false ): ?>
		<?php echo pw_twitter_follow_button( array(
			'username' 		=> _get($user,'usermeta.twitter'),
			'show_count'	=>	false,
			'size'			=>	'small'
			) ); ?>
	<?php endif; ?>

</div>
