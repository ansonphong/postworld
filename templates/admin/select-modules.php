<script>
	postworldAdmin.controller( 'pwModulesCtrl',
		[ '$scope', '_', 'iOptionsData', '$log', function( $scope, $_, $iOptionsData, $log ){

		$scope.pwModules = <?php echo json_encode( pw_enabled_modules() ); ?>;
		$scope['options'] = $iOptionsData['options'];
		$scope.availableModules = <?php echo json_encode( pw_available_modules() ); ?>;
		
		$scope.modulesInit = function(){
			$_.arrayFromObjectWatch( $scope, 'pwModules', 'selectedModules' );
		}

	}]);
</script>

<div class="well" ng-controller="pwModulesCtrl" ng-init="modulesInit()">

	<div class="save-right">
		<?php i_save_option_button( PW_OPTIONS_MODULES, 'pwModules'); ?>
	</div>
	<h2>
		<i class="icon-th-large"></i>
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
					ng-model="selectedModules[ module.slug ]">
				<i class="{{ module.icon }}"></i>
				{{ module.name }}
			</div>

		</label>

	</div>

	<!--
	<pre>selectedModules : {{ selectedModules | json }}</pre>
	<pre>pwModules : {{ pwModules | json }}</pre>
	-->
</div>