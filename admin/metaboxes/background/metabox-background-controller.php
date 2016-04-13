<?php
/*____             _                                   _ 
 | __ )  __ _  ___| | ____ _ _ __ ___  _   _ _ __   __| |
 |  _ \ / _` |/ __| |/ / _` | '__/ _ \| | | | '_ \ / _` |
 | |_) | (_| | (__|   < (_| | | | (_) | |_| | | | | (_| |
 |____/ \__,_|\___|_|\_\__, |_|  \___/ \__,_|_| |_|\__,_|
                       |___/                             
////////////////////////////////////////////////////////*/
global $post;

$fields = array('ID','post_meta('.pw_postmeta_key.')');

$background_post = pw_get_post( $post->ID, $fields );

if( _get( $background_post, 'post_meta.'.pw_postmeta_key.'.background' ) === false )
	$background_post = _set( $background_post, 'post_meta.'.pw_postmeta_key.'.background', array('_'=>0) );

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwBackgroundMetaboxCtrl',
	'vars' => array(
		'pwBackgrounds' => pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUNDS ) ),
		'pw_background_post' => $background_post,
		// @todo DELETE
		'context' => array(
			'name' => 'single',
			'label' => 'Single',
			'icon' => 'pwi-circle-medium',
			)
		),
	));

?>

<!--///// METABOX WRAPPER /////-->
<div
	id="pwBackgroundMetabox"
	class="postworld"
	ng-cloak>
	<div
		pw-admin-background
		ng-controller="pwBackgroundMetaboxCtrl"
		class="pw-metabox metabox-side metabox-background">
		<?php
			//echo pw_ob_include( pw_get_admin_template( 'metabox-background' ) );
			echo pw_background_select( array( 'context' => 'postAdmin' ) );
			//echo i_background_single_options( array( 'context'	=>	'postAdmin' ) );
			// Action Hook
			do_action('pw_background_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_background_post" ng-value="pw_background_post | json" style="width:100%;">
		
		<!-- DEV : Test Output 
		<hr><pre>POST : {{ pw_background_post | json }}</pre>
		-->
	</div>	
</div>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_background_metabox_scripts');
?>
