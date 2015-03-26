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

	///// RANK SCORE /////
	$scope.rankScoreReadout = {};

	$scope.rankScoreTypes = [
		{
			label:  		'Update Post Rank Scores Cache',
			functionName: 	'pw_cache_all_rank_scores',
		},
		{
			label:  		'Repair Post Points Cache',
			functionName: 	'pw_cache_all_post_points',
		},
		{
			label:  		'Repair User Points Cache',
			functionName: 	'pw_cache_all_user_points',
		},
		{
			label:  		'Repair Comment Points Cache',
			functionName: 	'pw_cache_all_comment_points',
		},
	];

	$scope.updateRankScoreType = function( functionName ){

		var busyKey = 'rankscore_' + functionName;
		$scope.busy[busyKey] = true;

		$pwData.wp_ajax( functionName, {} ).then(
			function( response ){
				$scope.busy[busyKey] = false;
				$log.debug( 'updateRankScoreType : RESPONSE : ', response );
				$scope.rankScoreReadout[functionName] = response.data;
			},
			function( response ){
				$scope.busy[busyKey] = false;
				$log.debug( 'updateRankScoreType : RESPONSE : ERROR : ', response );
			}
		);

	}

	///// CLEANUP METADATA /////

	$scope.cleanupMetaReadout = {};

	$scope.cleanupTables = [
		{
			name: 'Postmeta',
			type: 'postmeta',
		},
		{
			name: 'Postworld Postmeta',
			type: 'postworld_postmeta',
		},
		{
			name: 'Usermeta',
			type: 'usermeta',
		},
	];

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
