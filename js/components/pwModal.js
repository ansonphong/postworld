/*////////////// ------- SERVICE ------- //////////////*/  

postworld.service('pwModal', [ '$rootScope', '$log', '$location', '$modal', 'pwData', '_', '$pw',
	function ( $rootScope, $log, $location, $modal, $pwData, $_, $pw ) {
	return{

		openModal : function( meta ){
			
			///// DEFAULTS /////
			if( _.isUndefined(meta) )
				var meta = {};
			if( _.isUndefined( meta.post ) )
				meta.post = {};

			// Default Template ID
			var mode = ( _.isUndefined( meta.mode ) ) ?
				'default' : meta.mode;

			// Default Template ID
			var templateName = ( _.isUndefined( meta.templateName ) ) ?
				'modal-default' : meta.templateName;

			// Default Controller
			var controller = ( _.isUndefined( meta.controller ) ) ?
				'pwModalInstanceCtrl' : meta.controller;

			// Default Window Class
			var windowClass = ( _.isUndefined( meta.windowClass ) ) ?
				'pw-modal-default' : meta.windowClass;


			////////// SWITCH MODE //////////
			// mode : Can be used to pass the preset mode
			// or if string not found, this substitutes as the panel id
			switch( meta.mode ){
				///// QUICK EDIT /////
				case "quick-edit":
					templateName = "modal-edit-post";
					controller = "pwModalInstanceCtrl";
					windowClass = "modal-edit-post";
				break;
				///// QUICK EDIT NEW /////
				case "quick-edit-new":
					templateName = "modal-edit-post";
					controller = "pwModalInstanceCtrl";
					windowClass = "modal-edit-post";
				break;
				///// VIEW /////
				case "view":
				///// FEED /////
				case "feed":
					// TODO : Add support to detect post types / format and check for availability of the modal template					
					templateName = "modal-view-post";
					controller = "pwModalInstanceCtrl";
					windowClass = "modal-view-post"; 
				break;
				///// MEDIA /////
				case "media":
					// TODO : Add support to detect post types / format and check for availability of the modal template					
					templateName = "modal-media";
					controller = "mediaModalInstanceCtrl";
					windowClass = "modal-media"; 
				break;
				///// DEFAULT /////
				/* Defaults already set above
				default:
					templateName = meta.templateName;
					controller = meta.controller;
					windowClass = meta.windowClass;
				*/
			}

			///// GET TEMPLATE URL /////
			// If there's a slash in the template, it's from another subdir
			if( $_.isInArray( "/", templateName ) ){
				var templateNameParts = templateName.split("/");
				// Use the first part as the subdir
				var templateSubdir = templateNameParts[0];
				// Use the second part as the view
				templateName = templateNameParts[1];
			}
			// Otherwise assume it's in /modals
			else {
				var templateSubdir = 'modals';
			}
			
			var templateUrl = $pwData.pw_get_template( { subdir: templateSubdir, view: templateName } );

			///// LAUNCH THE MODAL /////
			$log.debug(
				"Launch Modal // templateName : " + templateName + 
				" // templateSubdir : " + templateSubdir + 
				" // templateUrl : " + templateUrl + 
				" // meta : ", meta );

			var modalInstance = $modal.open({
				templateUrl: templateUrl,
				controller: controller,
				windowClass: windowClass,
				resolve: {
					meta: function(){
						return meta;
					}
				}
			});

			modalInstance.result.then(function (selectedItem) {
				//$scope.post_title = post_title;
			}, function () {
				// WHEN CLOSE MODAL
				$log.debug('Modal dismissed at: ' + new Date());
				
				// Clear the URL params
				//$location.url('/');
				//$location.path('/');
				//$rootScope.$apply();

			});
			
		},

	}
}]);


////////// MODAL INSTANCE CONTROL //////////
postworld.controller('pwModalInstanceCtrl',
	[ '$scope', '$rootScope', '$document', '$modalInstance', 'meta', '$log', 'pwData', '$timeout', '_', 'pwPosts', // 'pwQuickEdit',
	function( $scope, $rootScope, $document, $modalInstance, meta, $log, $pwData, $timeout, $_, $pwPosts ) { // , $pwQuickEdit

	///// SET META /////
	$scope.meta = meta;

	///// SET MODE /////
	// Set Default Mode
	$scope.mode = ( !_.isUndefined( meta.mode ) ) ?
		meta.mode : "view";

	///// GET POST OBJECT /////
	// Import the passed post object into the Modal Scope
	if( !_.isUndefined( meta.post ) )
		$scope.post = meta.post;

	///// FEED HANDLING /////
	if( $_.objExists( meta, 'post.feed.id' ) ){
		$scope.feed = {};

		// Find and localize the feed
		$scope.feed['id'] = meta.post.feed['id'];
		//$scope.feed['data'] = $pwData.feeds[ $scope.feed['id'] ];

		// Get the original full post object from the feed
		// In the case that only a partial post object was passed
		$scope.post = $pwPosts.getFeedPost( $scope.post.feed.id, $scope.post.ID );

		// Get the current position of the feed
		$scope.feed['currentIndex'] = _.indexOf( $pwPosts.getFeed( $scope.feed.id )['posts'], $scope.post );
	}


	///// LOAD POST DATA /////
	// Allow editPost Controller to Initialize
	if( !_.isUndefined( $scope.post.ID )){
		// Broadcast to Load in the Post Data
		if( $scope.mode == "view" || $scope.mode == "quick-edit" )
			$timeout(function() {
			  $scope.$broadcast('loadPostData', $scope.post.ID );
			}, 3 );
	}
	

	///// PREVIOUS & NEXT POSTS FUNCTIONS /////
	// TODO : Add ability to go past loaded feed items, and load feed items based on feed_outline
	// TODO : ... and then resort them in the feed_outline according to their position in feed_outline

	// Watch when feed.currentIndex changes
	$scope.$watch( "feed.currentIndex", function ( value ){
		$log.debug( '$watch:feed.currentIndex : ', value );
		if( $_.objExists( $scope, 'feed.currentIndex' ) ){
			//$rootScope.$$phase
			// Set the current $scope.post object to reflect the current index
			$scope.post = $pwPosts.getFeed( $scope.feed.id )['posts'][ $scope.feed.currentIndex ];
			$log.debug( '$watch:feed.currentIndex : SWITCH POST : ', $scope.post );
		}
	}); 

	$scope.nextPost = function(){
		if( _.isUndefined( $scope.feed ) ){
			$log.debug('nextPost() : No feed.');
			return false;
		}

		// Setup Vars
		var feedLength = $pwPosts.getFeed( $scope.feed.id )['posts'].length;
		var currentIndex = $scope.feed.currentIndex;

		// If the feed is at the end, reset index to 0
		if( currentIndex >= ( feedLength - 1 ) )
			$scope.feed.currentIndex = 0;
		else
			$scope.feed.currentIndex ++;

		$log.debug('nextPost() // feed.currentIndex : ' + $scope.feed.currentIndex );
	};

	$scope.previousPost = function(){
		if( _.isUndefined( $scope.feed ) ){
			$log.debug('previousPost() : No feed.');
			return false;
		}

		// Setup Vars
		var feedLength = $pwPosts.getFeed( $scope.feed.id )['posts'].length;
		var currentIndex = $scope.feed.currentIndex;

		// If the feed is at the beginning, set it to the end
		if( currentIndex == 0 )
			$scope.feed.currentIndex = ( feedLength - 1 );
		// Otherwise just reduce the index by one
		else
			$scope.feed.currentIndex --;

		$log.debug('previousPost() // feed.currentIndex : ' + $scope.feed.currentIndex );

	};

	///// KEY PRESS /////
	// Capture Keydown
	$document.keydown(function(e){
		$scope.keyDown( e );
	})
	$scope.keyDown = function( e ){
		//$log.debug( "key press : " + e.keyCode + " : ", e );
		var keyCode = parseInt( e.keyCode );
	
		///// FEED /////
		if( !_.isUndefined( $scope.feed ) ){
			switch( keyCode ){
				// Right Key
				case 39:
					$log.debug( "keyDown: nextPost" );
					$scope.nextPost();
					break;
				// Left Key
				case 37:
					$log.debug( "keyDown: previousPost" );
					$scope.previousPost();
					break;
			}
		}
		$scope.$apply();
	}


	///// STANDARD FUNCTIONS /////
	// MODAL CLOSE
	$scope.close = function () {
		$modalInstance.dismiss('close');
	};

	// TRASH POST
	$scope.trashPost = function(){
		//$pwQuickEdit.trashPost($scope.post.ID, $scope);
	}; 

	// WATCH FOR TRASHED
	// TODO : Set Parent post_status = trash via pwData.feeds
	// Watch on the value of post_status
	$scope.$watch( "post.post_status", function (){
		if( $_.getObj( $scope, 'post.post_status' ) == 'trash'  )
			// Close Modal
			$modalInstance.dismiss('close');
	}); 

}]);

postworld.directive( 'pwModalAccess', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwModalAccessCtrl',
		link: function( $scope, element, attrs ){
			/*
			// OBSERVE Attribute
			attrs.$observe('modal', function(value) {
				alert(value);
			});
			*/
		}
	};
}]);

postworld.controller('pwModalAccessCtrl',
	[ '$scope', 'pwModal', function( $scope, $pwModal ) {

	$scope.openModal = function( meta ){
		$pwModal.openModal( meta );
	}

}]);



/*__  __          _ _         __  __           _       _ 
 |  \/  | ___  __| (_) __ _  |  \/  | ___   __| | __ _| |
 | |\/| |/ _ \/ _` | |/ _` | | |\/| |/ _ \ / _` |/ _` | |
 | |  | |  __/ (_| | | (_| | | |  | | (_) | (_| | (_| | |
 |_|  |_|\___|\__,_|_|\__,_| |_|  |_|\___/ \__,_|\__,_|_|

////////// ------------ MEDIA MODAL ------------ //////////*/                                                         


postworld.controller('mediaModalInstanceCtrl',
    [ '$scope', '$sce', '$modalInstance', 'meta', 'pwData',
    function( $scope, $sce, $modalInstance, meta, pwData ) { 


    // Import the passed post object into the Modal Scope
    $scope.post = meta.post;

    /*
    $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
        // RETURN THIS VALUE TO PAGE
    };
    */
    $scope.status = "loading";

    $scope.oEmbed = '';
    var link_url = $scope.post.link_url;
    var args = { "link_url": link_url };

    // MEDIA GET
    pwData.wp_ajax('ajax_oembed_get', args ).then(
        // Success
        function(response) {    
            $scope.oEmbed = $sce.trustAsHtml( response.data );
            $scope.status = "done";
        },
        // Failure
        function(response) {
            $scope.status = "error";
        }
    );

    // MODAL CLOSE
    $scope.close = function () {
        $modalInstance.dismiss('close');
    };


}]);



/* ___        _      _      _____    _ _ _   
  / _ \ _   _(_) ___| | __ | ____|__| (_) |_ 
 | | | | | | | |/ __| |/ / |  _| / _` | | __|
 | |_| | |_| | | (__|   <  | |__| (_| | | |_ 
  \__\_\\__,_|_|\___|_|\_\ |_____\__,_|_|\__|
											 
////////// ------------ QUICK EDIT ------------ //////////*/  

/*///////// ------- SERVICE : PW QUICK EDIT ------- /////////*/  
postworld.service('pwQuickEdit', [ '$rootScope', '$log', '$location', '$modal', 'pwData',
	function ( $rootScope, $log, $location, $modal, pwData ) {
	return{
		openQuickEdit : function( meta ){
			
			// Default Defaults
			if( _.isUndefined( meta.mode ) )
				meta.mode = 'quick-edit';

			$log.debug( "Launch Quick Edit : META : " + meta, meta.post );

			var modalInstance = $modal.open({
			  templateUrl: pwData.pw_get_template( { subdir: 'panels', view: 'modal-edit-post' } ),
			  controller: 'quickEditInstanceCtrl',
			  windowClass: 'quick_edit',
			  resolve: {
				meta: function(){
					return meta;
				}
			  }
			});
			modalInstance.result.then(function (selectedItem) {
				//$scope.post_title = post_title;
			}, function () {
				// WHEN CLOSE MODAL
				$log.debug('Modal dismissed at: ' + new Date());
				
				// Clear the URL params
				//$location.url('/');
				$location.path('/');
				//$rootScope.$apply();

			});
		},

		trashPost : function ( post_id, scope ){
			if ( window.confirm("Are you sure you want to trash : \n" + scope.post.post_title) ) {
				pwData.pw_trash_post( post_id ).then(
					// Success
					function(response) {
						if (response.status==200) {
							$log.debug('Post Trashed RETURN : ',response.data);                  
							if ( _.isNumber(response.data) ){
								var trashed_post_id = response.data;
								if( typeof scope != undefined ){
									// SUCESSFULLY TRASHED
									//var retreive_url = "/wp-admin/edit.php?post_status=trash&post_type="+scope.post.post_type;
									scope.post.post_status = 'trash';
									// Emit Trash Event : post_id
									scope.$emit('trashPost', trashed_post_id );
									// Broadcast Trash Event : post_id
									scope.$broadcast('trashPost', trashed_post_id );
								}
							}
							else{
								alert( "Error trashing post : " + response.data );
							}
						} else {
							// handle error
						}
					},
					// Failure
					function(response) {
						// Failed Delete
					}
				);
			}
		},
	}
}]);


