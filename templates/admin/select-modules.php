<script>
	postworldAdmin.controller( 'pwModulesCtrl',
		[ '$scope', '$_', '$log', '$pw', function( $scope, $_, $log, $pw ){

		$scope.pwModules = <?php echo json_encode( pw_enabled_modules() ); ?>;
		$scope['options'] = $pw.optionsMeta;
		$scope.availableModules = <?php echo json_encode( pw_available_modules() ); ?>;
		$scope.supportedModules = <?php echo json_encode( pw_supported_modules() ); ?>;
		$scope.requiredModules = <?php echo json_encode( pw_required_modules() ); ?>;
		
		$scope.modulesInit = function(){
			$_.arrayFromObjectWatch( $scope, 'pwModules', 'selectedModules' );
		}

		// Every time the selected modules changes
		$scope.$watch( 'selectedModules', function(val){
			// Force required modules to be enabled
			angular.forEach( $scope.requiredModules, function( moduleName ){
				$scope.selectedModules[ moduleName ] = true;
			});
		});

		$scope.isRequired = function( value ){
			return $_.isInArray( value, $scope.requiredModules );
		}

	}]);
</script>

<div class="well" ng-controller="pwModulesCtrl" ng-init="modulesInit()">

	<div class="save-right">
		<?php pw_save_option_button( PW_OPTIONS_MODULES, 'pwModules'); ?>
	</div>
	<h2>
		<i class="pwi-th-large"></i>
		Modules
	</h2>
	<small>Here you can enable the modules you would like to use, and disable the ones you do not need.</small>
	<hr class="thin">

	<div class="well" ng-repeat="module in availableModules">
		<label for="select-{{module.slug}}">
			<div>
				<input
					id="select-{{module.slug}}"
					type="checkbox"
					ng-model="selectedModules[ module.slug ]"
					ng-disabled="isRequired( module.slug )">
				<i class="{{ module.icon }}"></i>
				{{ module.name }}
				<small
					ng-show="isRequired( module.slug )">
					(required)
				</small>
			</div>
		</label>
	</div>

	<!--
	<pre>selectedModules : {{ selectedModules | json }}</pre>
	<pre>pwModules : {{ pwModules | json }}</pre>
	-->

</div>