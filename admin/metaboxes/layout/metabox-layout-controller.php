<?php
/*_                            _   
 | |    __ _ _   _  ___  _   _| |_ 
 | |   / _` | | | |/ _ \| | | | __|
 | |__| (_| | |_| | (_) | |_| | |_ 
 |_____\__,_|\__, |\___/ \__,_|\__|
             |___/                 
///////////////////////////////////*/
global $post;

$pw_layout_post = pw_get_post( $post->ID, array('ID','post_meta('.pw_postmeta_key.')') );

$layout_value = _get( $pw_layout_post, 'post_meta.'.pw_postmeta_key.'.layout' );
if( empty( $layout_value ) )
	$pw_layout_post = _set( $pw_layout_post, 'post_meta.'.pw_postmeta_key.'.layout', array('template'=>'default') );

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwLayoutMetaboxCtrl',
	'vars' => array(
		'pwLayoutOptions' => pw_layout_options(),
		'pwSidebars' => pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) ),
		'pwTemplates' => pw_get_templates( array( 'ext' => 'php', 'type' => 'dir' ) ),
		'pwLayouts' => pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) ),
		'pw_layout_post' => $pw_layout_post,
		// Provide context for the layouts controller
		'context' => array(
			'name' => 'single',
			'label' => 'Single',
			'icon' => 'pwi-circle-medium',
			),
		),
	));

?>


<!--///// METABOX WRAPPER /////-->
<div id="pwLayoutMetabox" class="postworld">
	<div
		pw-admin-layout
		ng-controller="pwLayoutMetaboxCtrl"
		id="poststuff"
		class="pw-metabox metabox-side metabox-layout"
		ng-cloak>
		<?php
			echo pw_layout_single_options( array( 'context'	=>	'postAdmin' ) );
			// Action Hook
			do_action('pw_layout_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_layout_post" ng-value="pw_layout_post | json" style="width:100%;">
		
		<?php if( pw_dev_mode() ): ?>
			<hr><pre>pw_layout_post.post_meta.pw_meta.layout : {{ pw_layout_post.post_meta.pw_meta.layout | json }}</pre>
		<?php endif; ?>
		
	</div>	
</div>


<?php
	// Action hook to print the Javascript(s)
	do_action('pw_layout_metabox_scripts');
?>