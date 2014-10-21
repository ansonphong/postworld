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
<div id="pwPostParentMetabox" class="postworld pw-metabox metabox-post-parent">
	<div ng-controller="pwPostParentMetaboxCtrl">
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

<!--///// METABOX SCRIPTS /////-->
<script>
	///// APP /////
	var pwPostParentMetabox = angular.module( 'pwPostParentMetabox', ['postworld'] );
	
	///// CONTROLLER /////
	pwPostParentMetabox.controller('pwPostParentMetaboxCtrl',
		['$scope', 'pwData', '_', '$log',
			function( $scope, $pwData, $_, $log ) {

			// This is the post object which is saved
			$scope.ppPost = <?php echo json_encode( $pw_post ); ?>;
			// The variables by which parent posts autocomplete are queried
			$scope.query = <?php echo json_encode( $query ); ?>;
			// Labels for the UI
			$scope.labels = <?php echo json_encode( $labels ); ?>;
			// The post which is selected as the post parent
			$scope.parent_post = <?php echo json_encode( $pw_parent_post ); ?>;

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
				$log.debug( "PW METABOX : POST PARENT : addPostParent( $item ) : ", item );
				// Set the ID as the post parent
				$scope.ppPost['post_parent'] = item.ID;
				// Populate the parent post object
				$scope.parent_post = item;
			}

			$scope.removePostParent = function(){
				// Clear the post_parent field from the post
				$scope.ppPost['post_parent'] = 0;
				// Clear the post_parent object
				$scope.parent_post = false;
				//alert('remove');
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
