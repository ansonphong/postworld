'use strict';

postworld.config(function($locationProvider){
	// $locationProvider.html5Mode(true).hashPrefix('!');
});

/**
 *
 * @ngdoc directive
 * @name postworld.directive:pwFeed

 * @param {string} feedId A predefined feed ID
 *
 * @todo Rename to pwFeed. (Test in various implimentations.)
 * @todo Have ability for non-scrolling feed.
 *
 * @description
 * Generates a feed from predefined feed paramters within the specified element.
 *
 * @example
<script>
pw.feeds['newsFeed'] = {
	preload: 10,
	load_increment: 10,
	offset: 0,
	order_by: '-post_date',
	view: {
		current: 'list'
	},
	query:{
		post_type:['post'],
		post_status:'publish',
		posts_per_page: 100
	}
};
</script>
<div live-feed="newsFeed"></div> 
 *
 */
postworld.directive('pwFeed', function($log) {
	return {
		restrict: 'A',
		replace: true,
		controller: 'pwFeedController',
       	template: '<div ng-include="templateUrl" class="feed"></div>',
		scope : {
			feedId: '@pwFeed',
		},
		link : function( $scope, element, attrs ){
			$log.debug( 'pwFeed [directive] : INIT : ', $scope.feedId ); //$scope.templateUrl
		},
	};
});

///// DEPRECIATED /////
postworld.directive('liveFeed', function($log) {
	return {
		restrict: 'A',
		replace: true,
		controller: 'pwFeedController',
       	template: '<div ng-include="templateUrl" class="feed"></div>',
		scope : {
			feedId: '@liveFeed',
		},
		link : function( $scope, element, attrs ){
			$log.debug( 'liveFeed [directive] : INIT : ', $scope.feedId ); //$scope.templateUrl
		},
	};
});

postworld.controller('pwFeedController',
	[ '$scope', '$rootScope', '$location', '$log', '$attrs', '$timeout', 'pwData', '$route', '_', '$window', '$pw',
	function( $scope, $rootScope, $location, $log, $attrs, $timeout, $pwData, $route, $_, $window, $pw ) {
	
	// Initialize
	$scope.busy = false; 		// Avoids running simultaneous service calls to get posts. True: Service is Running to get Posts, False: Service is Idle    	
	var firstRun = true; 		// True until pwLiveFeed runs once. False for al subsequent pwScrollFeed
	$scope.scrollMessage = "";

	// IF NO FEED ID
	if( _.isUndefined( $scope.feedId ) || _.isEmpty($scope.feedId) ){
		$log.debug( 'pwFeed : ERROR : No Feed ID provided' );
		return false;
	}

	// INSERT FEED
	// Inserts feed into the Postworld Data service
	if( $_.objExists( $window.pw, 'feeds.' + $scope.feedId ) ){
		$pwData.insertFeed( $scope.feedId, $window.pw.feeds[$scope.feedId] );
		
		// Wait for all the controller functions to register
		$timeout( function(){
			// Initialize the feed
			$scope.pwLiveFeed();
		}, 0 );
		
	}
	else
		$log.debug( 'pwFeed : ERROR : No valid feed provided in $window.pw.feeds.' + $scope.feedId );

	$scope.feed = function(){
		return $pwData.feeds[$scope.feedId];
	}

	$scope.posts = function(){
		return $pwData.feeds[$scope.feedId].posts;
	}

	//$log.debug( 'pwFeed : BOOT : feedId : ' + $scope.feedId, $scope.feed() );

   	$scope.updateTemplateUrl = function(){

   		// Check if there's an over-ride feed template
   		var templateId = $_.get( $pwData.feeds[$scope.feedId], 'feed_template' );

   		// If no fallback template
   		if( templateId == false )
   			// Generate template ID from the current feed view
			var templateId = 'feed-' + $pwData.getFeedView( $scope.feedId );
   		
   		// Define fallback template ID
		var templateIdFallback = 'feed-list';

		// Get the template path
		var template = $pwData.pw_get_template( { subdir: 'feeds', view: templateId } )

		// If the template path doesn't exist
		if( template == false )
			// Get fallback
			template = $pwData.pw_get_template( { subdir: 'feeds', view: templateIdFallback } )

		// Set into the scope, this is picked up by the ng-include
		$scope.templateUrl = template; //$pwData.pw_get_template( { subdir: 'feeds', view: template } );	   	

   	}
   	// TODO : Run this on view change
   	// Currently only the post templates are changing on view change
   	// This must also change
   	$scope.updateTemplateUrl();

	$scope.setDefault = function( exp, defaultVal ){
		var value = $scope.$eval( exp );
		if( _.isUndefined( value ) )
			return defaultVal;
		else
			return value;
	}
	
	$scope.fillFeedData = function( response ) {
		// This function executes on the first run of a pwLiveFeed or pwLoadFeed
		// After hearing back from the server AJAX call
		// With the first payload of posts
		// For feeds preloaded with post data on load, this function is not called

		// Create Feed Object if it doesn't exist
		if( _.isUndefined( $pwData.feeds[$scope.feedId] ) )
			$pwData.feeds[$scope.feedId] = {};

		/// INSERT FEED DATA RESPONSE ///
		$pwData.feeds[$scope.feedId].feed_outline = response.data.feed_outline;
		$pwData.feeds[$scope.feedId].posts = response.data.posts;
		$pwData.feeds[$scope.feedId].loaded = response.data.loaded;

		$scope.updateStatus();
					
	};
	
	$scope.getNext = function() {
		$scope.message = "";   

		$log.debug( 'pwFeed : getNext() : ' + $scope.feedId, $pwData['feeds'][$scope.feedId] );		
		
		// If already getting results, do not run again.
		if ($scope.busy) {
			$log.debug( 'pwFeed : getNext() : BUSY', $scope.feedId );	
			$log.debug('pwFeedController.getNext: We\'re Busy, wait!');
			return;
		}

		// if running for the first time
		if( firstRun ) {
			firstRun = false;
			$scope.pwLiveFeed();
		}
		else {
			// Get next batch of posts
			$scope.scrollFeed();				
		}
	};
	
	// Searching from Filter Feed Directives will trigger this function
	// Which in turn restarts the Feed Loading Process
	$scope.reloadFeed = function() {

		// TODO : Can we break an existing Ajax Call?
		// We cannot do that, but we can use an identifier for the request
		// and ignore previous requests to the current id.
		// This scenario might not happen since we're not allowing more than
		// one feed request at a time, this might be a limitation,
		// but it makes the data consistent.

		$log.debug( "pwFeed : reloadFeed()", $scope.feedId );

		$pw.queryToLocation( $scope.feed().query );						
		
		$pwData.feeds[$scope.feedId].posts = false;
		firstRun = true;			
		this.getNext();
	};

	//$timeout( function(){}, 0 );
	$scope.$on( 'feed.reload', function( e, feedId ){
		$log.debug( "pwFeed.$on : feed.reload : ", feedId );
		if( feedId == $scope.feedId )
			$scope.reloadFeed();
	});

	$scope.pwLiveFeed = function() {
		// TODO : Set Nonce Authentically
		$pwData.setNonce(78);
		$log.debug( "pwFeed : pwLiveFeed() : INIT : ID : " + $scope.feedId, $pwData.feeds[$scope.feedId] );

		///// GET FEED FROM PRELOADED DATA /////
		// If posts have already been pre-loaded
		if( _.isArray( $pwData.feeds[$scope.feedId].posts ) &&
			!_.isEmpty( $pwData.feeds[$scope.feedId].posts ) ){
			$log.debug( "pwFeed : INIT : PRELOADED : " + $scope.feedId, $scope.feed().posts );
			// Inject blocks into the feed
			$scope.injectBlocks();
			// Add feed meta data
			$scope.addFeedMeta();
			// Update the status
			$scope.updateStatus();
			// Toggle off busy
			$scope.busy = false;
			// Return here to avoid AJAX call
			return;
		}

		///// GET FEED BY AJAX /////
		// Toggle on busy
		$scope.busy = true;
		// Clone Args Value as 'feed'
		var feed = JSON.parse( JSON.stringify( $pwData.feeds[$scope.feedId] ) );

		// Get Query String Parameters, if any are provided
		var qsArgs = $pw.locationToQuery();
		var qsArgsValue = JSON.parse( JSON.stringify( qsArgs ) );

		// Initiate the AJAX Call
		$log.debug( 'pwFeed : getLiveFeed : ' + $scope.feedId, feed );
		$pwData.getLiveFeed( feed, qsArgsValue ).then(
			// Success
			function(response) {

				$log.debug( "LIVE FEED (response) : ID : " + response.data.feed_id, response.data );

				// Prevent Flicker when Template Loading
				$timeout( function(){
					$scope.busy = false;
				}, 100 );

				// Handle Error					
				if ( response.status === undefined ) {
					$log.error('LIVE FEED : ID : ' + feed.feed_id + ' : response format is not recognized');
					return;
				}
				if ( response.status == 200 ) {
					// Check if data exists
					if ( _.isObject( response.data ) ) {
						// Insert Response in Feed Data					
						$scope.fillFeedData( response );
						$scope.injectBlocks();
						$scope.addFeedMeta();
					} else {
						$log.debug('pwFeedController.getLiveFeed No Data Received');						
					}
					return response.data;
				} else {
					// handle error
					$log.debug( 'error', response.status, response.message );
					// TODO should we set busy to false when error is returned?
				}
				// return response.posts;
			},
			// Failure
			function(response) {
				$scope.busy = false;
				$log.error('pwFeedController.getLiveFeed Failure',response);
				// TODO Show User Friendly Message
			}
		);
		// change url params after getting finalFeedQuery						
		// $scope.queryToLocation($pwData.feeds[$scope.feedId].finalFeedQuery);						
	  };

	$scope.addFeedMeta = function( vars ){
		// TODO : PERFORMANCE : Add Mechanism for scrollFeed, so it stores the value of the last index,
		// so it doesn't have to re-iterate over the whole array
		// vars = { mode: 'scrollFeed', postsLoaded: postsLoaded, newItems: newItems.length }

		// Set the mode of the Meta Data
		if( !$_.objExists( vars, 'mode' ) ){
			vars = {};
			vars.mode = "newFeed";
		}

		// Localize the posts
		var posts = $pwData.feeds[$scope.feedId].posts;
		
		var index = 0;
		var loadOrder = 0;

		var newPosts = [];

		// Iterate through each post
		angular.forEach( posts, function( post ){
			// Add the Feed ID
			post = $_.set( post, 'feed.id', $scope.feedId );
			// Add new variables to post object
			if( vars.mode == "newFeed" ){
				post = $_.set( post, 'feed.index', index );
				post = $_.set( post, 'feed.loadOrder', index );
			}
			else if( vars.mode == "scrollFeed" && index >= vars.postsLoaded ){
				post = $_.set( post, 'feed.index', index );
			}
			newPosts.push( post );
			index ++;
		});
		// Re-set the centralized posts object
		$pwData.feeds[$scope.feedId].posts = newPosts;

	};

	$scope.scrollFeed = function() {
		
		// Check if all Loaded, then return and do nothing
		if ($pwData.feeds[$scope.feedId].status == 'all_loaded') {
			$log.debug('pwFeedController.scrollFeed : ALL LOADED', $scope.feedId);				
			$scope.busy = false;
			return;
		};

		// TODO do we need to set the loading status? or just use the busy flag?
		$pwData.feeds[$scope.feedId].status = 'loading';
		
		if( $scope.busy )
			return;

		// TODO set Nonce from UI
		$pwData.setNonce(78);


		///// PREPARE GET POSTS /////

		var feed = $pwData.feeds[$scope.feedId];

		// Slice Outline Array
		var idBegin = feed.loaded.length;

		// Use Preload value if no posts loaded, otherwise use load_increment
		var idEnd = ( feed.loaded.length == 0 ) ?
			idBegin + feed.preload :
			idBegin + feed.load_increment;
		
		//$log.debug( 'pwData.feeds : ', feeds );
		$log.debug( 'pwFeedController.scrollFeed // idBegin : ' + idBegin + ' / idEnd : ' + idEnd );

		// Set the post IDs
		var postIDs = feed.feed_outline.slice( idBegin, idEnd );

		// Set the fields
		var fields = $_.getObj( feed, 'query.fields' );
		if( fields == false )
			fields = 'preview';

		// Set the options
		var options = $_.getObj( feed, 'options' );
		if( options == false )
			options = {};

		// Set the parameters
		var params = {
			post_ids : postIDs,
			fields : fields,
			options : options,
		};

		////////////////////////////
		$log.debug( "pw_get_posts : " , params );

		$scope.busy = true;
		$pwData.pw_get_posts( params ).then(
			// Success
			function(response) {
				if (response.status === undefined) {
					$log.debug('Feed response format is not recognized.');
					return;
				}
				if( response.status == 200) {

					var newItems = response.data;
					
					// Used to sequence the order of post fade-in transitions 
					var loadOrder = 0;
					var injectBlock = false;

					for ( var i = 0; i < newItems.length ; i++ ) {

						/// LOAD ORDER ///
						// Tells which order in the chunk of posts loaded each post is
						// To allow for sequencing transitions, such as offset fade-ins on scroll
						newItems[i] = $_.setObj( newItems[i], 'feed.loadOrder', loadOrder );
						loadOrder ++;

						$log.debug( "pwFeed : INJECTED POST : loadOrder : " + newItems[i].feed.loadOrder + " : " + newItems[i].post_title );

						// If Posts doesn't exist as an array
						if( _.isUndefined( $pwData.feeds[$scope.feedId].posts ) )
							$pwData.feeds[$scope.feedId].posts = [];
						
						// Push to central posts array
						$pwData.feeds[$scope.feedId].posts.push( newItems[i] );

						/// INJECT BLOCKS ///					
						var blockVars = { singleRun: true, post:{ feed:{ loadOrder: loadOrder } } };
						// Used to know if a block has been injected
						// Will return true if a block was added
						injectBlock = $scope.injectBlock( blockVars );
						if( injectBlock == true ){
							$log.debug( "pwFeed : INJECTED BLOCK : loadOrder : ", blockVars.post.feed.loadOrder );
							loadOrder ++;
						}

					}

					// Inject Blocks
					//$scope.injectBlocks();

					/// UPDATE : LOADED ///
					// Get the new 'loaded' array directly from which post IDs were requested
					// Since in some cases of returning gallery posts, they will differ from the actual post data
					var loaded = $pwData.feeds[$scope.feedId].loaded;
					loaded = loaded.concat( postIDs );
					$pwData.feeds[$scope.feedId].loaded = loaded;

					// Add Feed Meta for only the new posts
					var postsLoaded = parseInt( $pwData.feeds[$scope.feedId].posts.length - 1 );
					$scope.addFeedMeta( { mode: 'scrollFeed', postsLoaded: postsLoaded, newItems: newItems.length } );

					// Update feed status
					$scope.updateStatus();
					$scope.busy = false;
									
				} else {
					$log.debug('pwFeed : ERROR : ',response.status,response.message);
					$scope.busy = true;

				}
			},
			// Failure
			function(response) {
				$log.error('pwFeed : pwFeedController.pwScrollFeed Failure',response);
				$scope.busy = true;
				// TODO Show User Friendly Error Message
			}
		);
	};

	$scope.initBlocksWidgets = function( blocks ){
		// blocks = [ object ] // the blocks object
		// If widgets are defined in blocks, localize the widgets item

		// Get the Sidebar ID
		var sidebarId = $_.get( blocks, 'widgets.sidebar' );
		// If sidebar ID doesn't exist as a string, return here
		if( !_.isString( sidebarId ) )
			return blocks;

		// Get the Widgets
		var widgets = $_.get( $pwData.widgets, sidebarId );

		// If widgets doesn't exist as an array, or is empty, return here
		if( !_.isArray( widgets ) || _.isEmpty( widgets ) )
			return false;

		// If there are less widgets than the max blocks value
		if( blocks.max > widgets.length )
			// Reduce the max to the number of widgets
			blocks.max = widgets.length;

		// Create scope object of widgets
		$scope.widgets = widgets;
		
		// Return
		return blocks;

	}

	$scope.initBlocks = true;
	$scope.injectBlocks = function() {
		// Run at the initiation of a feed
		// Injects blocks into the feed array
		//$log.debug( "FEED : ", $pwData.feeds[$scope.feedId] );

		///// INIT BLOCK INJECTION /////
		// If this is the first time injecting, establish the blocks object
		if( $scope.initBlocks ){
			$scope.initBlocks = false;

			// Get the blocks object
			var blocks = $_.get( $pwData.feeds, $scope.feedId +'.blocks' );

			// If blocks is defined as an object
			if( _.isObject( blocks ) ){

				// Define the default blocks settings
				var defaultBlocks = {
					offset: 3,				// How many posts in the feed before first block
					increment: 10,			// Place blocks every X number of posts
					max: 50,				// Maximum number of blocks to inject
					template: 'ad-block',	// Name of panel template id
					sidebar: false,			// Use a sidebar for blocks
					query: false,			// Use a query for the blocks
					classes: false,
				};

				// Replace the default settings with the settings from the feed
				blocks = array_replace_recursive( defaultBlocks, blocks );

				// Current number of blocks in the feed
				blocks._count = 0;

				// Establish where the next block will be added
				blocks._nextIndex = blocks.offset;

				// Initialize widgets
				blocks = $scope.initBlocksWidgets( blocks );

				// Set the object into the scope
				$scope.blocks = blocks;

			}
			// If no blocks object is defined, return here
			else
				return;

		}

		///// IF : NO BLOCKS DEFINED /////
		else if( !_.isObject( $scope.blocks ) )
			// Return here
			return;

		// Get the number of posts loaded
		//var postCount = $pwData.feeds[$scope.feedId].loaded.length;

		$scope.injectBlock();

	};


	
	$scope.injectBlock = function( vars ){

		// If no blocks configured, return here
		if( _.isEmpty( $scope.blocks ) )
			return;

		/// SET DEFAULTS ///
		if( _.isUndefined( vars ) )
			vars = {};
		if( _.isUndefined( vars.singleRun ) )
			vars.singleRun = false;
		if( _.isUndefined( vars.post ) )
			vars.post = {};

		// If the next block is within range of the current feed length
		if( $scope.blocks._nextIndex <= $pwData.feeds[$scope.feedId].posts.length &&
			// And the maximum number of blocks has not been reached
			$scope.blocks._count < $scope.blocks.max ){

			// Increase block count
			$scope.blocks._count ++;

			// Define the block item as a post to inject into the feed 
			var post = {
				ID: 0,
				post_type	: '_pw_block',
				template	: $scope.blocks.template,
				block: {
					'index'			: $scope.blocks._count - 1,
					'feed_index'	: $scope.blocks._nextIndex,
					'classes'		: $scope.blocks['classes'],
				},
			};

			// If a post is provided, add the post data
			if( !_.isEmpty( vars.post ) )
				post = array_replace_recursive( post, vars.post );

			// Inject the block into the feed at the pre-calculated next index
			$pwData.feeds[$scope.feedId].posts.splice( $scope.blocks._nextIndex, 0, post );
			
			// Set when the next block will be added
			$scope.blocks._nextIndex += $scope.blocks.increment + 1; // + 1 to include the new block added					
	
			// LOOP : Run this function again
			if( !vars.singleRun )
				$scope.injectBlock();
			else
				return true;
		}

		return false;

	}

	$scope.updateStatus = function(){

		if( !_.isArray( $pwData.feeds[$scope.feedId].feed_outline ) ){
			$pwData.feeds[$scope.feedId].status = 'loaded';	
			return;
		}

		// Count Length of loaded, update scroll message
		if( $pwData.feeds[$scope.feedId].loaded.length >= $pwData.feeds[$scope.feedId].feed_outline.length )
			$pwData.feeds[$scope.feedId].status = 'all_loaded';																									
		else
			$pwData.feeds[$scope.feedId].status = 'loaded';											
		
	}

	//$log.debug( "LOCATION.SEARCH() >>> ", $location.search() );

}]);



