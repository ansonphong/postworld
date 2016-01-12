<?php
/*_____              _                 _     
 |  ___|_ _  ___ ___| |__   ___   ___ | | __ 
 | |_ / _` |/ __/ _ \ '_ \ / _ \ / _ \| |/ / 
 |  _| (_| | (_|  __/ |_) | (_) | (_) |   <  
 |_|  \__,_|\___\___|_.__/ \___/ \___/|_|\_\ 

////////////// FACEBOOK SDK //////////////*/

///// INCLUDE FACEBOOK SDK /////
 
/**
 * @todo Conditionally include FB SDK, based on higher level filter
 * so other things can enable / disable it.
 */

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

	// If no URL provided, get the PW global URL
	$url = _get( $vars, 'url' );
	if( empty( $url ) )
		$vars['url'] = $pw['view']['url'];
	
	// Get the saved options array
	$fbc = pw_grab_option( PW_OPTIONS_COMMENTS, 'facebook' );

	// If not enabled, return empty
	if( $fbc['enable'] == false )
		return '';

	// Use output buffering to capture HTML as a string
	ob_start(); ?>

		<div
			class="fb-comments"
			data-href="<?php echo $vars['url'] ?>"
			data-numposts="<?php echo $fbc['numposts'] ?>"
			data-colorscheme="<?php echo $fbc['colorscheme'] ?>"
			data-order-by="<?php echo $fbc['order_by'] ?>"
			width="100%">
		</div>

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}




?>