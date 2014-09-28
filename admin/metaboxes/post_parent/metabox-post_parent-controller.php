<?php
/*____           _     ____                      _   
 |  _ \ ___  ___| |_  |  _ \ __ _ _ __ ___ _ __ | |_ 
 | |_) / _ \/ __| __| | |_) / _` | '__/ _ \ '_ \| __|
 |  __/ (_) \__ \ |_  |  __/ (_| | | |  __/ | | | |_ 
 |_|   \___/|___/\__| |_|   \__,_|_|  \___|_| |_|\__|
                                                     
////////////////////////////////////////////////////*/

global $post;
?>

<!--///// METABOX WRAPPER /////-->
<div id="pwPostParentMetabox" class="postworld pw-metabox">
	<div ng-controller="pwPostParentMetaboxCtrl">
		<?php
			// Include the UI template
			$metabox_template = pw_get_template ( 'admin', 'metabox-post_parent', 'php', 'dir' );
			include $metabox_template;
			
			// Action Hook
			do_action('pw_post_parent_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_event_post" ng-value="post | json" style="width:100%;">
		
		<!-- DEV : Test Output -->
		
		<hr><pre>POST : {{ post | json }}</pre>
		<hr><pre>QUERY : {{ query | json }}</pre>
		
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// APP /////
	var pwPostParentMetabox = angular.module( 'pwPostParentMetabox', ['postworld'] );
	
	///// CONTROLLER /////
	pwPostParentMetabox.controller('pwPostParentMetaboxCtrl',
		['$scope', 'pwData', '_', '$log',
			function( $scope, $pwData, $_, $log ) {
			$scope.post = <?php echo json_encode($pw_parent_post); ?>;
			$scope.query = <?php echo json_encode( $query ); ?>;

			$scope.getPosts = function( val ) {
				var query = $scope.query;
				query.s = val;

				return $pwData.pw_query( query ).then(
					function( response ){
						$log.debug( "QUERY RESPONSE : ", response.data.posts );
						return response.data.posts;
					},
					function(){}
				);
			};

			$scope.addPostParent = function( item ){
				$log.debug( "SELECT POST PARENT : ", item );
			}

	}]);
	
</script>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_post_parent_metabox_scripts');
?>

<script>
	///// BOOTSTRAP APP /////
	angular.bootstrap(document.getElementById("pwPostParentMetabox"),['pwPostParentMetabox']);
</script>




