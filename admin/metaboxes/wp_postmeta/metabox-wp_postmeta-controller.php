<?php
/*_        ______    ____           _                  _        
 \ \      / /  _ \  |  _ \ ___  ___| |_ _ __ ___   ___| |_ __ _ 
  \ \ /\ / /| |_) | | |_) / _ \/ __| __| '_ ` _ \ / _ \ __/ _` |
   \ V  V / |  __/  |  __/ (_) \__ \ |_| | | | | |  __/ || (_| |
    \_/\_/  |_|     |_|   \___/|___/\__|_| |_| |_|\___|\__\__,_|
                                                                
///////////////////////////////////////////////////////////////*/

global $post;
?>

<!--///// METABOX WRAPPER /////-->
<div id="pwWpPostmetaMetabox" class="postworld pw-metabox metabox-wp-postmeta">
	<div ng-controller="pwWpPostmetaMetaboxCtrl">
		<?php include $metabox_template; ?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_post_wp_postmeta" ng-value="wpPostmetaPost | json" style="width:100%;">
		<!-- DEV : Test Output -->
		<!--
		<hr><pre>POST : {{ post | json }}</pre>
		<hr><pre>FIELDS : {{ fields | json }}</pre>
		-->
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// CONTROLLER /////
	postworldAdmin.controller('pwWpPostmetaMetaboxCtrl',
		['$scope', 'pwData', '_', '$log',
			function( $scope, $pwData, $_, $log ) {
			// This is the post object which is saved
			$scope.wpPostmetaPost = <?php echo json_encode( $pw_postmeta_post ); ?>;
			// The input fields to add
			$scope.fields = <?php echo json_encode( $fields ); ?>;
	}]);
	
</script>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_wp_postmeta_metabox_scripts');
?>