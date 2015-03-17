/* ____           _          
  / ___|__ _  ___| |__   ___ 
 | |   / _` |/ __| '_ \ / _ \
 | |__| (_| | (__| | | |  __/
  \____\__,_|\___|_| |_|\___|
                             
//////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminCache', [ function(){
		return { 
				controller: 'pwAdminCacheCtrl',
				link:function( $scope, element, attrs ){
					// Add Module Class
					element.addClass('pw-admin-iconsets');
				}
		};
}]);

postworldAdmin.controller( 'pwAdminCacheCtrl',
	[ '$scope', '$log', '$window', 'pwData', '_', 
	function ( $scope, $log, $window, $pwData, $_ ) {
	
	$scope.loading = {
		cacheTypeReadout: false,
	};

	$scope.deleteCacheType = function( cacheType ){
		$scope.loading.cacheTypeReadout = true;
		$pwData.wp_ajax('pw_delete_cache_type', { cache_type: cacheType } ).then(
			function( response ){
				$scope.loading.cacheTypeReadout = false;
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.refreshCacheReadout = function(){
		$scope.loading.cacheTypeReadout = true;
		$pwData.wp_ajax('pw_get_cache_types_readout', {} ).then(
			function( response ){
				$scope.loading.cacheTypeReadout = false;
				$log.debug( 'refreshCacheReadout : RESPONSE : ', response.data );
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.truncateCache = function(){
		$scope.loading.cacheTypeReadout = true;
		$pwData.wp_ajax('pw_truncate_cache', {} ).then(
			function( response ){
				$scope.loading.cacheTypeReadout = false;
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.cacheTypeReadoutIsEmpty = function(){
		return _.isEmpty( $scope.cacheTypeReadout );
	}

	
}]);
