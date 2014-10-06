<?php
/*_ __  __      _        _               
 (_)  \/  | ___| |_ __ _| |__   _____  __
 | | |\/| |/ _ \ __/ _` | '_ \ / _ \ \/ /
 | | |  | |  __/ || (_| | |_) | (_) >  < 
 |_|_|  |_|\___|\__\__,_|_.__/ \___/_/\_\
/////////////////////////////////////////*/?>

<!--///// METABOX TEMPLATES /////-->
<div ng-app="infiniteMetabox" id="infiniteMetabox" class="infinite postworld">
	<div ng-controller="iMetaboxCtrl">
		<?php
			// Print the Templates
			do_action('i_admin_options_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="i_meta" ng-value="iMeta | json" style="width:100%;">
		<!-- DEV : Test Output
		<hr><pre>{{ iMeta | json }}</pre>
		-->
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// APP /////
	var infiniteMetabox = angular.module( 'infiniteMetabox', ['infinite','postworld'] );
	///// CONTROLLER /////
	infiniteMetabox.controller('iMetaboxCtrl',
		['$scope',
			function( $scope ) {
			$scope.iMeta = <?php echo json_encode($iMeta); ?>;
	}]);
</script>
<?php
	// Print the Javascript(s)
	do_action('i_admin_options_metabox_scripts');
?>
<script>
	///// BOOTSTRAP APP /////
	angular.bootstrap(document.getElementById("infiniteMetabox"),['infiniteMetabox']);
</script>
