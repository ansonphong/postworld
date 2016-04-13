<?php
/*_        ______    ____           _                  _        
 \ \      / /  _ \  |  _ \ ___  ___| |_ _ __ ___   ___| |_ __ _ 
  \ \ /\ / /| |_) | | |_) / _ \/ __| __| '_ ` _ \ / _ \ __/ _` |
   \ V  V / |  __/  |  __/ (_) \__ \ |_| | | | | |  __/ || (_| |
    \_/\_/  |_|     |_|   \___/|___/\__|_| |_| |_|\___|\__\__,_|
                                                                
///////////////////////////////////////////////////////////////*/

global $post;

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwWpPostmetaMetaboxCtrl',
	'vars' => array(
		'fields' => $fields,
		),
	));

?>

<!--///// METABOX WRAPPER /////-->
<div
	id="pwWpPostmetaMetabox"
	class="postworld pw-metabox metabox-wp-postmeta"
	ng-cloak>
	<div ng-controller="pwWpPostmetaMetaboxCtrl">
		<?php include $metabox_template; ?>
		<!-- HIDDEN FIELD -->
		<!--<input type="hidden" name="pw_post_wp_postmeta" ng-value="wpPostmetaPost | json" style="width:100%;">-->
		
		<!-- DEV : Test Output -->
		<?php if( pw_dev_mode() ): ?>
			<div class="well">
				<h3><i class="icon pwi-code"></i> Development Mode</h3>
				<div class="well">
					<b>$scope.fields</b>
					<pre><code>{{ fields | json }} </code></pre>
				</div>
			</div>
		<?php endif ?>

	</div>	
</div>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_wp_postmeta_metabox_scripts');
?>