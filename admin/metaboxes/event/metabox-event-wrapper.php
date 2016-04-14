<?php
/*_____                 _     __  __      _        
 | ____|_   _____ _ __ | |_  |  \/  | ___| |_ __ _ 
 |  _| \ \ / / _ \ '_ \| __| | |\/| |/ _ \ __/ _` |
 | |___ \ V /  __/ | | | |_  | |  | |  __/ || (_| |
 |_____| \_/ \___|_| |_|\__| |_|  |_|\___|\__\__,_|

//////////////////////////////////////////////////*/
global $post;
global $pw_event_post;

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwEventMetaboxCtrl',
	'vars' => array(
		'post' => $pw_event_post,
		'eventKey' => $event_postmeta_key,
		),
	));

?>

<!--///// METABOX WRAPPER /////-->
<div
	id="pwEventMetabox"
	class="postworld pw-metabox"
	ng-cloak>
	<div pw-admin-metabox-event ng-controller="pwEventMetaboxCtrl">
		<?php
			// Include the UI template
			$metabox_template = pw_get_template ( 'admin', 'metabox-event', 'php', 'dir' );
			include $metabox_template;
			
			// Action Hook
			do_action('pw_event_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_event_post" ng-value="post | json" style="width:100%;">
		
		<?php if( pw_dev_mode() ): ?>
			<div class="well">
				<h3>$scope.post</h3>
			
				<!-- DEV : Test Output -->
				<pre><code>{{ post | json }}</code></pre>
			
			</div>
		<?php endif; ?>

	</div>	
</div>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_event_metabox_scripts');
?>


