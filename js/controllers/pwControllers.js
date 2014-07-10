/*____           _      ____            _             _ _           
 |  _ \ ___  ___| |_   / ___|___  _ __ | |_ _ __ ___ | | | ___ _ __ 
 | |_) / _ \/ __| __| | |   / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
 |  __/ (_) \__ \ |_  | |__| (_) | | | | |_| | | (_) | | |  __/ |   
 |_|   \___/|___/\__|  \____\___/|_| |_|\__|_|  \___/|_|_|\___|_|   
																	
/*////////// ------------ POST CONTROLLER ------------ //////////*/                

'use strict';
postworld.directive( 'pwPost', [ function( $scope ){
	return {
		restrict: 'AE',
		controller: 'postController',
		link: function( $scope, element, attrs ){
			// OBSERVE Attribute
			attrs.$observe('postRequiredFields', function( value ) {
				$scope.postRequiredFields = $scope.$eval( value );
			});
		}
	};
}]);


postworld.controller('postController',
	[ "$scope", "$rootScope", "$window", "$sce", "pwData", "pwEditPostFilters", "_", "$log", "pwImages", "$pw", "pwPosts",
	function($scope, $rootScope, $window, $sce, $pwData, pwEditPostFilters, $_, $log, $pwImages, $pw, $pwPosts ) {

	// If $scope.post doesn't exist
	// Get it from $window.post
	if( _.isUndefined( $scope.post ) ){
		if( !_.isUndefined( $window.post ) )
			$scope.post = $window.post;
		else
			$scope.post = {};
	}

		
	// RUN CUSTOM POST FUNCTIONS
	// This function can be added to the $window object
	// For performing theme-specific per-post operations
	if( typeof $window.pwPostFunctions === "function" )
		$window.pwPostFunctions( $scope );

	// Trust the post_content as HTML
	// Otherwise seed it as an empty string
	$scope.$watch( 'post.post_content', function(){
		if( $_.objExists( $scope, 'post.post_content' )){
			$scope.post.post_content = ( _.isString( $scope.post.post_content ) ) ?
				$sce.trustAsHtml( $scope.post.post_content ) : "";
		}
	});

	// POST META
	if( !$_.objExists( $scope, 'post.meta' ) )
		$scope.post.meta = {};

	// IMPORT LANGUAGE
	if(
		typeof $window.pwSiteLanguage !== 'undefined' &&
		typeof $window.pwGlobals.current_user !== 'undefined' &&
		typeof $scope.post !== 'undefined'
		){
		$scope.language = $window.pwSiteLanguage;
		$scope.current_user_id = $window.pwGlobals.current_user.ID;
		// GENERATE  SHARE LINK
		$scope.share_link = $pw.paths.home_url + "/?u=" + $window.pwGlobals.current_user.ID + "&p=" + $scope.post.ID;
	}

	// Toggles class="expaned", used with ng-class="expanded" 
	$scope.expanded = "";
	var clickTip = "Click to expand";
	$scope.clickTip = clickTip;
	$scope.toggleExpanded = function(){
		( $scope.expanded == "" ) ? $scope.expanded = "expanded" : $scope.expanded = "" ;
		( $scope.clickTip != "" ) ? $scope.clickTip = "" : $scope.clickTip = clickTip ;
	};

	// Update the contents of post after Quick Edit
	$rootScope.$on('postUpdated', function(event, post_id) {
		if ( $scope.post.ID == post_id ){
			var args = {
				post_id: post_id,
				fields: 'all'
			};
			$pwData.pw_get_post(args).then(
				// Success
				function(response) {
					if (response.status==200) {
						var post = response.data;
						// Convert Post Content into Bindable HTML
						if( !_.isUndefined( post.post_content ) &&
							_.isString(post.post_content) ){
							post.post_content = $sce.trustAsHtml(post.post_content);
						}
						$scope.post = response.data;
						// Update Classes
						$scope.setClass();
					} else {
						// handle error
					}
				},
				// Failure
				function(response) {
					// $log.error('pwFeedController.pw_live_feed Failure',response);
					// TODO Show User Friendly Message
				}
			);
		}
	});

	///// TIME FUNCTIONS /////
	$scope.jsDateToTimestamp = function(jsDate){
		var dateObject = new Date(jsDate);

		return Date.parse(dateObject);

	}

	///// IMAGE FUNCTIONS /////
	$scope.backgroundImage = function( imageUrl, properties ){

		// Set the Image URL
		//var imageUrl = $scope.post.image[imageHandle].url;
		var style = { 'background-image': "url(" + imageUrl + ")" };

		// Add additional properties
		if( !_.isUndefined( properties ) ){
			angular.forEach( properties, function(value, key){
				style[key] = value;
			});
		}
		return style;
	}


	$scope.selectImageTag = function(){
		if( $_.objExists( $scope, "post.image.tags" ) == false )
			return false;
		var imageTags = $scope.post.image['tags'];
		return $pwImages.selectImageTag( imageTags );
	}

	///// SET ACTIVE CLASS /////
	$scope.setActiveClass = function( boolean ){
		//alert('test');
		return ( boolean ) ? "active" : "";
	}

	$scope.gotoUrl = function( url ){
		window.location = url;
	};

	////////// LOAD POST DATA //////////
	$scope.loadPost = function( post_id ){
		$scope.status = "loading";

		///// DETECT ID /////
		// Post ID passed directly
		if( !_.isUndefined(post_id) ){
			$log.debug('pw-post : loadPost( *post_id* ) // Post ID passed directly : ', post_id);

		// Post ID passed by Route
		} else if ( typeof $routeParams.post_id !== 'undefined' &&
			$routeParams.post_id > 0 ){
			var post_id = $routeParams.post_id;
			$log.debug('pw-post : loadPost() // Post ID from Route : ', post_id);
		}

		// Post ID passed by Post Object
		else if( !_.isUndefined($scope.post.ID) && $scope.post.ID > 0 ){
			var post_id = $scope.post.ID;
			$log.debug('pw-post : loadPost() // Post ID from Post Object : ', post_id);
		}
		
		var vars = {
			"post_id" : post_id,
			"fields" : "all"
		};
		///// GET THE POST DATA /////
		$pwData.pw_get_post( vars ).then(
			// Success
			function(response) {
				$log.debug('pwData.pw_get_post : RESPONSE : ', response.data);

				// FILTER FOR INPUT
				var get_post = response.data;

				// LOCAL CALLBACK ACTION EMIT
				// Any sibling or parent scope can listen on this action
				$scope.$emit('postLoaded', get_post);

				// SET DATA INTO THE SCOPE
				$scope.post = get_post;
				// UPDATE STATUS
				$scope.status = "done";
			},
			// Failure
			function(response) {
				//alert('error');
				$scope.status = "error";
			}
		);  
	}

	///// ACTION : FEED POST UPDATED /////
	$scope.$on( 'feedPostUpdated', function( e, vars ){
		// This is triggered when the central feed post is updated with new data
		$log.debug( "$ON : feedPostUpdated : ", vars );
		// If the post does not know it's own feed
		if( !$_.objExists( $scope, 'post.feed.id' ) )
			return false;
		// If the feed and post IDs are provided
		if( $scope.post.feed.id == vars.feedId &&
			$scope.post.ID == vars.postId ){
			// Update the local post with the updated data from the feed
			$scope.post = $pwPosts.getFeedPost( vars.feedId, vars.postId );
		}
	});

	///// WATCH : REQUIRED FIELDS DIRECTIVE /////
	$scope.$watch( 'postRequiredFields', function( value ){
		if( !value )
			return false;
		$log.debug( "DIRECTIVE : pwPost -> postRequiredFields : ", $scope.postRequiredFields );
		$pwPosts.requiredFields(
			{
				feedId: $scope.post.feed.id,
				postId: $scope.post.ID,
				fields: $scope.postRequiredFields
			});
	} );

	///// ACTION : LOAD POST DATA /////
	$scope.$on('loadPostData', function(event, post_id) {
		if( post_id == $_.getObj( $scope, 'post.ID' ) )
			$scope.loadPost( post_id );
	});

	///// DEV /////
	//$pwPosts.mergeFeedPost( $scope.post.feed.id, $scope.post.ID, {post_date:"NOW"} );
	//$log.debug( $pwPosts.getFeedPost( $scope.post.feed.id, $scope.post.ID ) );
	
	// Make API for status. $scope.meta.status = [ 'loading', 'loadingRequiredFields' ], = [ 'done' ]
	// Make core service functions to set, and check for status

	

}]);



