<?php
/*____           _     ____                      _   
 |  _ \ ___  ___| |_  |  _ \ __ _ _ __ ___ _ __ | |_ 
 | |_) / _ \/ __| __| | |_) / _` | '__/ _ \ '_ \| __|
 |  __/ (_) \__ \ |_  |  __/ (_| | | |  __/ | | | |_ 
 |_|   \___/|___/\__| |_|   \__,_|_|  \___|_| |_|\__|
                                                     
////////////////////////////////////////////////////*/

global $post;

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwPostParentMetaboxCtrl',
	'vars' => array(
		// The post object which is saved
		'ppPost' => $pw_post,
		// The variables by which parent posts autocomplete are queried
		'query' => $query,
		// Labels for the UI
		'labels' => $labels,
		// The post which is selected as the post parent
		'parent_post' => $pw_parent_post

		),
	));

?>

<!--///// METABOX WRAPPER /////-->
<div
	id="pwPostParentMetabox"
	class="postworld pw-metabox metabox-post-parent"
	ng-cloak>
	<div pw-admin-post-parent ng-controller="pwPostParentMetaboxCtrl">
		<?php
			// Include the UI template
			$metabox_template = pw_get_template ( 'admin', 'metabox-post_parent', 'php', 'dir' );
			include $metabox_template;
			
			// Action Hook
			do_action('pw_post_parent_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_post_parent_post" ng-value="ppPost | json" style="width:100%;">
		
		<!-- DEV : Test Output -->
		<!--
		<hr><pre>POST : {{ post | json }}</pre>
		<hr><pre>PARENT POST ID : {{ parent_post.ID | json }}</pre>
		<hr><pre>QUERY : {{ query | json }}</pre>
		-->
	</div>	
</div>


<?php
	// Action hook to print the Javascript(s)
	do_action('pw_post_parent_metabox_scripts');
?>