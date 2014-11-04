<?
	$pwBackgrounds = pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUNDS ) );
	$pw_backgrounds_structure = apply_filters( 'pwOptions-backgrounds-structure', array() );
?>
<div ng-app="infinite" class="postworld styles wrap">
	<script type="text/javascript">
		//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
		infinite.controller('pwBackgroundsDataCtrl', [ '$scope', '$window', function( $scope, $window ){
			$scope.lang = "en";
			// Print Data
			$scope.language = <?php global $i_style_language; echo json_encode( $i_style_language ); ?>;
			$scope.pwBackgrounds = <?php echo json_encode( $iStyles ); ?>;
			$scope.pwBackgroundsStructure = <?php echo json_encode( $pw_backgrounds_structure ); ?>;
		}]);
	</script>
	<div
		i-admin-style
		ng-controller="pwBackgroundsDataCtrl"
		ng-cloak>

		<h1>
			<i class="icon-paint-format"></i>
			Backgrounds
		</h1>

		<hr class="thick">


		<!--<pre>{{ iStyleStructure | json }}</pre>-->

		<!-- ////////// VARIABLES ////////// -->

		

		
		<!-- ////////// END VARIABLES ////////// -->

		<hr class="thick">
		
		<!-- SAVE BUTTON -->
		<div class="save-right"><?php i_save_option_button( PW_OPTIONS_BACKGROUNDS, 'pwBackgrounds'); ?></div>


		<button ng-click="resetDefaults()" class="button">Reset to Defaults</button>
		
		<hr class="thick">
		<pre>iStyles : {{ iStyles | json }}</pre>

	</div>

</div>