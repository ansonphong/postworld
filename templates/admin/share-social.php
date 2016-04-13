<script>
	postworldAdmin.controller( 'shareSocialCtrl', [ '$scope', '$_', '$filter', '$log', function($scope, $_, $filter, $log){
		
		$scope.setNetworkIds = function( networkIds ){
			if( $_.getObj( $scope.$parent.<?php echo $vars['model_var'];?>, "<?php echo $vars['model_key'];?>" ) == false )
				$scope.$parent.<?php echo $vars['model_var'];?> = $_.setObj( $scope.$parent.<?php echo $vars['model_var'];?>, "<?php echo $vars['model_key'];?>", networkIds );
			else
				$scope.$parent.<?php echo $vars['ng_model'];?> = networkIds;

			$log.debug( "Set networkIds:", networkIds );
		};

		$scope.getOptionsModel = function(){
			return $_.getObj( $scope, "<?php echo $vars['options_model']; ?>" );
		}

		$scope.getSelectedNetworks = function () {
		    return $filter('filter')( $_.getObj( $scope, "<?php echo $vars['options_model'] ?>" ), { selected: true });
		}

		$scope.shareSocialCtrlInit = function(){
			// Get the saved settings 
			var savedSettings = $_.getObj( $scope.$parent, "<?php echo $vars['ng_model']; ?>" );
			// If no settings are saved, set defaults
			if( savedSettings == false ){
				savedSettings = $scope.selectedNetworkIds();
			}
			// Translate the settings into the options model
			var metaValues = [];
			angular.forEach( $_.getObj( $scope, "<?php echo $vars['options_model']; ?>" ), function(meta){
				if( $_.inArray( meta['id'], savedSettings ) )
					meta['selected'] = true;
				else
					meta['selected'] = false;
				metaValues.push( meta );
			});
			$scope.<?php echo $vars['options_model']; ?> = metaValues;

		}

		$scope.selectedNetworkIds = function () {
			var selectedNetworks = $scope.getSelectedNetworks();
			var selectedNetworkIds = [];
			angular.forEach( selectedNetworks, function( network ){
				selectedNetworkIds.push( network['id'] );
			});
			return selectedNetworkIds;
		}

		// Watch for a change in the options model
		$scope.$watch(
			function(){
				return $scope.getOptionsModel();
			},
			// Update the model value
			function( value ){
				$scope.setNetworkIds( $scope.selectedNetworkIds() );
			}, 1
		);
	}]);
</script>

<div ng-controller="shareSocialCtrl" ng-init="shareSocialCtrlInit()">

	<div class="btn-group">
		<label
			ng-repeat="option in <?php echo $vars['options_model']; ?>"
			class="btn"
			ng-model="option.selected"
			ng-class="{ 'active': option.selected }"
			>
			<!-- ng-click="toggleBool( option.selected )" -->
			<i class="{{ option.icon }}"></i>
			<input
				type="checkbox"
				ng-model="option.selected"
				style="display:none;">
			{{ option.name }}
		</label>
	</div>
	<!--{{ iOptions.social.share.networks }}-->
</div>