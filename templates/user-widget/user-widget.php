<?php
	/*
	$vars = {
	    "title": "Widget",
	    "user_select": "user_id",
	    "user_id": "1",
	    "view": "user-widget",
	    "show_title": ""
	}
	*/
	$user = pw_get_user( $vars['user_id'], 'all' );
?>

<img src="<?php echo $user['avatar']['medium']['url'] ?>">

<h1>
	<?php echo $user['display_name']?>
</h1>

<?php echo pw_contact_methods_user_menu( $user['ID'] ); ?>

<?php
	if( _get($user,'usermeta.twitter') != false )
		echo pw_twitter_follow_button( array(
			'username' 		=> _get($user,'usermeta.twitter'),
			'show_count'	=>	true,
			'size'			=>	'small'
			) ); ?>

<?php echo _get( $user, 'usermeta.description' )?>