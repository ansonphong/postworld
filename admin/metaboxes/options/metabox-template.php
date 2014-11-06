<?php
/*_ __  __      _        _               
 (_)  \/  | ___| |_ __ _| |__   _____  __
 | | |\/| |/ _ \ __/ _` | '_ \ / _ \ \/ /
 | | |  | |  __/ || (_| | |_) | (_) >  < 
 |_|_|  |_|\___|\__\__,_|_.__/ \___/_/\_\
/////////////////////////////////////////*/
global $post;
$pwMeta = pw_get_postmeta( array( 'post_id' => $post->ID, 'meta_key' => PW_POSTMETA_KEY ) );
?>

<!--///// METABOX TEMPLATES /////-->
<div id="postworldMetabox" class="infinite postworld">
	<div ng-controller="pwMetaboxCtrl">
		<?php
			// Print the Templates
			do_action('pw_admin_options_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="<?php echo PW_POSTMETA_KEY; ?>" ng-value="pwMeta | json" style="width:100%;">
		<!-- DEV : Test Output
		<hr><pre>{{ pwMeta | json }}</pre> -->
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// APP /////
	var postworldMetabox = angular.module( 'postworldMetabox', ['infinite','postworld'] );
	///// CONTROLLER /////
	postworldMetabox.controller('pwMetaboxCtrl',
		['$scope',
			function( $scope ) {
			$scope.pwMeta = <?php echo json_encode($pwMeta); ?>;
	}]);
</script>
<?php
	// Print the Javascript(s)
	do_action('pw_admin_options_metabox_scripts');
?>
<script>
	///// BOOTSTRAP APP /////
	angular.bootstrap(document.getElementById("postworldMetabox"),['postworldMetabox']);
</script>
