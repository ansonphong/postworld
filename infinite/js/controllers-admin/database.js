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
	[ '$scope', '$log', '$window', 'pwData', '_', '$timeout', 
	function ( $scope, $log, $window, $pwData, $_, $timeout ) {
	
	///// BUSY /////
	// When a task is in process, make it 'busy'
	// Then we can track the activity of an action

	$scope.busy = {};
	$scope.isBusy = function( key ){
		return $_.get( $scope.busy, key );
	}
	$scope.setBusy = function( key, bool ){
		$scope.busy[ key ] = bool;
	}

	$scope.getPercent = function( current, total ){
		var d = current / total;
		var p = d * 100;
		return parseInt( p );
	}

	$scope.getPercentWidth = function( current, total ){
		var p = $scope.getPercent( current, total );
		return {
			width: p + "%"
		};
	}


	///// PROGRESS API /////
	$scope.updateBusy = {};
	$scope.updateProgress = function( functionName ){
		
		if( $_.get( $scope.updateBusy, functionName ) == true )
			return false;

		if( $_.get( $scope.endingProgress, functionName ) == true )
			return false;

		$scope.updateBusy[functionName] = true;

		$pwData.getProgress( functionName ).then(
			function( response ){
				$scope.updateBusy[functionName] = false;
				$scope.progress[ functionName ] = response.data;
			},
			function(response){}
		);
	}

	$scope.progressLoop = function( functionName ){

		// If the process isn't busy, return here
		if( !$scope.isBusy( functionName ) )
			return false;

		// Run Update Progress
		$scope.updateProgress( functionName );

		// Run again in 5 seconds
		$timeout(
			function(){
				$scope.progressLoop( functionName );
			}, 2000
		);
	}

	///// WATCH : PROGRESS /////
	$scope.$watch( 'progress', function( val ){

		// On controller bootup, check the progress object
		// And if there are any functions currently active
		// Make the current busy state reflect that
		angular.forEach( $scope.xCacheTypes, function( xCacheType ){
			var active = $_.get( $scope.progress, xCacheType.functionName + '.active' );
			if( active ){
				$scope.setBusy( xCacheType.functionName, true );
				$scope.progressLoop( xCacheType.functionName );
			} else{
				$scope.setBusy( xCacheType.functionName, false );
			}
		});

	}, 1 );

	$timeout(function(){},0);

	$scope.endingProgress = {};
	$scope.endProgress = function( functionName ){

		if( $_.get( $scope.endingProgress, functionName ) == true )
			return false;

		$scope.endingProgress[functionName] = true;

		$pwData.endProgress( functionName ).then(
			function( response ){
				$scope.endingProgress[functionName] = false;
				$scope.updateBusy[functionName] = false;
				$scope.progress[functionName] = response.data;
				$scope.updateProgress( functionName );
			},
			function(response){}
		);
	}

	///// RANK SCORE /////
	$scope.rankScoreReadout = {};

	$scope.xCacheTypes = [
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

		$scope.setBusy( functionName, true );

		$scope.progressLoop( functionName );

		$pwData.wp_ajax( functionName, {} ).then(
			function( response ){
				$scope.setBusy( functionName, false );
				$log.debug( 'updateRankScoreType : RESPONSE : ', response );
				$scope.rankScoreReadout[functionName] = response.data;

				// TODO : Return here with the final progress data

			},
			function( response ){
				$scope.setBusy( functionName, false );
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
		$scope.setBusy( busyKey, true );

		$pwData.wp_ajax('pw_cleanup_meta', { type: type } ).then(
			function( response ){
				$scope.setBusy( busyKey, false );
				$log.debug( 'cleanupMeta : RESPONSE : ', response );
				$scope.cleanupMetaReadout[type] = response.data;
			},
			function( response ){}
		);

	}


	////////// CACHE //////////

	$scope.deleteCacheType = function( cacheType ){
		$scope.setBusy( 'cacheTypeReadout', true );
		$pwData.wp_ajax('pw_delete_cache_type', { cache_type: cacheType } ).then(
			function( response ){
				$scope.setBusy( 'cacheTypeReadout', false );
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.refreshCacheReadout = function(){
		$scope.setBusy( 'cacheTypeReadout', true );
		$pwData.wp_ajax('pw_get_cache_types_readout', {} ).then(
			function( response ){
				$scope.setBusy( 'cacheTypeReadout', false );
				$log.debug( 'refreshCacheReadout : RESPONSE : ', response );
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.truncateCache = function(){
		$scope.setBusy( 'cacheTypeReadout', true );
		$pwData.wp_ajax('pw_truncate_cache', {} ).then(
			function( response ){
				$scope.setBusy( 'cacheTypeReadout', false );
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.cacheTypeReadoutIsEmpty = function(){
		return _.isEmpty( $scope.cacheTypeReadout );
	}

	
}]);
