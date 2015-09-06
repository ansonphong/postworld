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


	///// WATCH : PROGRESS /////
	$scope.$watch( 'progress', function( val ){
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


	///// PROGRESS API /////
	$scope.updateBusy = {};
	$scope.updateProgress = function( functionName ){
		// If it's already updating, don't try again
		if( $_.get( $scope.updateBusy, functionName ) == true )
			return false;
		// If it's already in the process of ending, stop here
		if( $_.get( $scope.endingProgress, functionName ) == true )
			return false;

		// Update the busy status
		$scope.updateBusy[functionName] = true;

		// Get the current progress of the function name from the server
		$pwData.getProgress( functionName ).then(
			function( response ){
				var data = response.data;
				// Update the busy status
				$scope.updateBusy[functionName] = false;
				// Check if already in the process ending the progress
				if( $_.get( $scope.endingProgress, functionName ) == false ){
					// On first time running
					if( data == false )
						data = { active:true };
					// Set the progress
					$scope.progress[ functionName ] = data;
				}
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


	$timeout(function(){},0);

	$scope.endingProgress = {};
	$scope.endProgress = function( functionName ){

		// If already in the process of ending progress, stop here
		if( $_.get( $scope.endingProgress, functionName ) == true )
			return false;

		// Update ending progress status
		$scope.endingProgress[functionName] = true;

		// End the process on the server via AJAX
		$pwData.endProgress( functionName ).then(
			function( response ){
				// Update ending progress status
				$scope.endingProgress[functionName] = false;
				// Update busy status
				$scope.updateBusy[functionName] = false;
				// Update progress object
				$scope.progress[functionName] = response.data;
			},
			function(response){}
		);
	}

	///// RANK SCORE /////
	$scope.rankScoreReadout = {};

	$scope.xCacheTypes = [
		{
			title: 			'Rank Score',
			icon: 			'pwi-bars',
			label:  		'Update Post Rank Scores Cache',
			functionName: 	'pw_cache_all_rank_scores',
		},
		{
			title: 			'Post Points',
			icon: 			'pwi-pushpin',
			label:  		'Repair Post Points Cache',
			functionName: 	'pw_cache_all_post_points',
		},
		{
			title: 			'User Points',
			icon: 			'pwi-user',
			label:  		'Repair User Points Cache',
			functionName: 	'pw_cache_all_user_points',
		},
		{
			title: 			'Comment Points',
			icon: 			'pwi-bubbles-2',
			label:  		'Repair Comment Points Cache',
			functionName: 	'pw_cache_all_comment_points',
		},
	];

	$scope.initProgressFunction = function( functionName ){

		$scope.setBusy( functionName, true );

		$scope.progressLoop( functionName );

		$pwData.wpAjax( functionName, {} ).then(
			function( response ){
				$scope.setBusy( functionName, false );
				/*
				
				$log.debug( 'runProgressFunction : RESPONSE : ', response );
				$scope.rankScoreReadout[functionName] = response.data;
				*/
			},
			function( response ){
				/*
				$scope.setBusy( functionName, false );
				$log.debug( 'runProgressFunction : RESPONSE : ERROR : ', response );
				*/
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
		{
			name: 'Term Counts',
			type: 'term_counts',
		},
	];

	$scope.cleanupMeta = function( type ){

		var busyKey = 'cleanup_' + type;
		$scope.setBusy( busyKey, true );

		$pwData.wpAjax('pw_cleanup_meta', { type: type } ).then(
			function( response ){
				$scope.setBusy( busyKey, false );
				$log.debug( 'cleanupMeta : RESPONSE : ', response );
				$scope.cleanupMetaReadout[type] = response.data;
			},
			function( response ){}
		);

	}


	///// TAXONOMY OPERATIONS /////
	$scope.taxOpReadout = {};
	$scope.taxOps = [
		{
			title: 'Update Taxonomy Term Post Counts',
			description: 'Update the post counts for all registered taxonomy\'s terms.',
			type: 'update_term_count',
		},
		{
			title: 'Delete Empty Terms',
			description:'Delete all terms with a post count of 0. Update term post counts before running this.',
			type:'delete_empty_terms'
		},
		{
			title: 'Cleanup Term Taxonomy Relations',
			description:'Delete all terms from the term_taxonomy table which have no terms.',
			type:'cleanup_term_taxonomy_relations'
		},
		{
			title: 'Remove Deleted Terms from Relationships',
			description:'Deletes all term relationships from term_relationship tables whose terms no longer exist',
			type:'delete_old_term_relationships'
		}
	];

	$scope.doTaxOp = function( type ){

		var busyKey = 'taxOp_' + type;
		$scope.setBusy( busyKey, true );

		$pwData.wpAjax('pw_taxonomy_operation', { type: type, vars:{} } ).then(
			function( response ){
				$scope.setBusy( busyKey, false );
				$log.debug( 'doTaxOp : RESPONSE : ', response );
				$scope.taxOpReadout[type] = response.data;
			},
			function( response ){}
		);

	}


	////////// CACHE //////////

	$scope.deleteCacheType = function( cacheType ){
		$scope.setBusy( 'cacheTypeReadout', true );
		$pwData.wpAjax('pw_delete_cache_type', { cache_type: cacheType } ).then(
			function( response ){
				$scope.setBusy( 'cacheTypeReadout', false );
				$scope.cacheTypeReadout = response.data;
			},
			function( response ){}
		);
	}

	$scope.refreshCacheReadout = function(){
		$scope.setBusy( 'cacheTypeReadout', true );
		$pwData.wpAjax('pw_get_cache_types_readout', {} ).then(
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
		$pwData.wpAjax('pw_truncate_cache', {} ).then(
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
