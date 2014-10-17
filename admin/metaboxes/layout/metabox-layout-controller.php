<?php
/*_                            _   
 | |    __ _ _   _  ___  _   _| |_ 
 | |   / _` | | | |/ _ \| | | | __|
 | |__| (_| | |_| | (_) | |_| | |_ 
 |_____\__,_|\__, |\___/ \__,_|\__|
             |___/                 
///////////////////////////////////*/
global $post;
?>

<!--///// METABOX WRAPPER /////-->
<div id="pwLayoutMetabox" class="postworld pw-metabox metabox-layout">
	<div ng-controller="pwLayoutMetaboxCtrl">
		<?php
			// Include the UI template
			$metabox_template = pw_get_template ( 'admin', 'metabox-layout', 'php', 'dir' );
			include $metabox_template;
			
			// Action Hook
			do_action('pw_layout_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_layout_post" ng-value="post | json" style="width:100%;">
		
		<!-- DEV : Test Output -->
		<!--
		<hr><pre>POST : {{ post | json }}</pre>
		<hr><pre>PARENT POST ID : {{ parent_post.ID | json }}</pre>
		<hr><pre>QUERY : {{ query | json }}</pre>
		-->
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// APP /////
	var pwLayoutMetabox = angular.module( 'pwLayoutMetabox', ['postworld'] );
	
	///// CONTROLLER /////
	pwLayoutMetabox.controller('pwLayoutMetaboxCtrl',
		['$scope', 'pwData', '_', '$log',
			function( $scope, $pwData, $_, $log ) {

			// This is the post object which is saved
			$scope.post = <?php echo json_encode( $pw_post ); ?>;
			// The variables by which parent posts autocomplete are queried
			$scope.query = <?php echo json_encode( $query ); ?>;

	}]);
	
</script>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_layout_metabox_scripts');
?>

<script>
	///// BOOTSTRAP APP /////
	angular.bootstrap(document.getElementById("pwLayoutMetabox"),['pwLayoutMetabox']);
</script>
