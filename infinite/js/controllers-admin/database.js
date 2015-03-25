/*____        _        _                    
 |  _ \  __ _| |_ __ _| |__   __ _ ___  ___ 
 | | | |/ _` | __/ _` | '_ \ / _` / __|/ _ \
 | |_| | (_| | || (_| | |_) | (_| \__ \  __/
 |____/ \__,_|\__\__,_|_.__/ \__,_|___/\___|
                                            
////////////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminDatabase', [ function(){
		return { 
				controller: 'pwAdminDatabaseCtrl',
				link:function( $scope, element, attrs ){
					// Add Module Class
					element.addClass('pw-admin-database');
				}
		};
}]);

postworldAdmin.controller( 'pwAdminDatabaseCtrl',
	[ '$scope', '$log', '$window', 'pwData', '_', 
	function ( $scope, $log, $window, $pwData, $_ ) {
	
	$scope.busy = {};

	////////// CLEANUP META //////////

	$scope.cleanupMetaReadout = {
		postmeta: false,
		postworld_postmeta: false,
		usermeta: false,
		commentmeta: false,
		taxonomymeta: false,
	};


	$scope.cleanupMeta = function( type ){

		var busyKey = 'cleanup_' + type;
		$scope.busy[busyKey] = true;

		$pwData.wp_ajax('pw_cleanup_meta', { type: type } ).then(
			function( response ){
				$scope.busy[busyKey] = false;
				$log.debug( 'cleanupMeta : RESPONSE : ', response );
				$scope.cleanupMetaReadout[type] = response.data;
			},

			function( response ){}

		);

	}


	////////// CACHE //////////

	$scope.deleteCacheType = function( cacheType ){
		$scope.busy.cacheTypeReadout = true;
		$pwData.wp_ajax('pw_delete_cache_type', { cache_type: cacheType } ).then(
			function( response ){
				$scope.busy.cacheTypeReadout = false;
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.refreshCacheReadout = function(){
		$scope.busy.cacheTypeReadout = true;
		$pwData.wp_ajax('pw_get_cache_types_readout', {} ).then(
			function( response ){
				$scope.busy.cacheTypeReadout = false;
				$log.debug( 'refreshCacheReadout : RESPONSE : ', response );
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.truncateCache = function(){
		$scope.busy.cacheTypeReadout = true;
		$pwData.wp_ajax('pw_truncate_cache', {} ).then(
			function( response ){
				$scope.busy.cacheTypeReadout = false;
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.cacheTypeReadoutIsEmpty = function(){
		return _.isEmpty( $scope.cacheTypeReadout );
	}

	
}]);
