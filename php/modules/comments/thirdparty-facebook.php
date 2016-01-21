<?php
/*_____              _                 _     
 |  ___|_ _  ___ ___| |__   ___   ___ | | __ 
 | |_ / _` |/ __/ _ \ '_ \ / _ \ / _ \| |/ / 
 |  _| (_| | (_|  __/ |_) | (_) | (_) |   <  
 |_|  \__,_|\___\___|_.__/ \___/ \___/|_|\_\ 

////////////// FACEBOOK SDK //////////////*/

///// INCLUDE FACEBOOK SDK /////
if( pw_module_enabled('comments') )
	add_action( 'wp_footer', 'pw_include_facebook_sdk' );
function pw_include_facebook_sdk(){
	$app_id = pw_grab_option( PW_OPTIONS_SOCIAL, 'networks.facebook_app_id' );
	$facebook_comments_enabled = pw_grab_option( PW_OPTIONS_COMMENTS, 'facebook.enable' );
	if( !empty($app_id) && $facebook_comments_enabled ): ?>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=<?php echo $app_id ?>&version=v2.0";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>

	<?php endif;
}

///// GET FACEBOOK COMMENTS EMBED CODE /////
function pw_get_comments_facebook( $vars = array() ){
	global $pw;

	// Get the saved options array
	$fbc = pw_grab_option( PW_OPTIONS_COMMENTS, 'facebook' );

	// If not enabled, return empty
	if( $fbc['enable'] == false )
		return '';

	// Setup default vars
	$defaultVars = array(
		'id'    =>  _get( $pw, 'view.post.ID' ),
		'title' =>  _get( $pw, 'view.post.post_title' ),
		'url'   =>  _get( $pw, 'view.url' ),
		);
	
	$vars = array_replace_recursive( $defaultVars, $vars );

	/**
	 * HREF SOURCE
	 */
	switch( $fbc['href_from'] ){
		case 'id':
			$fbc['href'] = get_site_url().'/?p='.$vars['id'];
			break;
		case 'url':
			$fbc['href'] = $vars['url'];
			break;
	}

	/**
	 * NORMALIZE PROTOCOL
	 */
	if( $fbc['protocol'] === 'http' )
		$fbc['href'] = str_replace( 'https://', 'http://', $fbc['href'] );
	elseif( $fbc['protocol'] === 'https' )
		$fbc['href'] = str_replace( 'http://', 'https://', $fbc['href'] );
	
	// Get Facebook comments template
	$template = pw_get_template( 'comments', 'comments-thirdparty-facebook', 'php', 'dir' );

	// Return template as string
	return pw_ob_include( $template, $fbc );

}


?>