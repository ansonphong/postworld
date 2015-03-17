/*////////////// ------- SERVICE ------- //////////////*/  

postworld.service('pwModal', [ '$rootScope', '$log', '$location', '$modal', 'pwData', '_', '$pw',
	function ( $rootScope, $log, $location, $modal, $pwData, $_, $pw ) {
	return{

		openModal : function( meta ){
			
			// Log
			$log.debug( "$pwModal.openModal : INIT : meta : ", meta );

			// The modal object which will be opened with $modal service
			var modalObj = {};

			// The metadata which will be passed into the modal
			if( _.isUndefined(meta) )
				meta = {};

			///// SET DEFAULTS /////
			var defaultMeta = {
				mode: 'view',
				modalIndex: $pw.state.modals.open,
				templateName: null,	// Used to override template
				post:{
					post_type: 'post'
				},
				keybindings: true,
			};
			meta = array_replace_recursive( defaultMeta, meta );

			// Increase the number by 1
			$pw.state.modals.open ++;

			////////// SWITCH MODE //////////
			// Used to pass the preset mode
			switch( meta.mode ){
				///// NEW /////
				case "new":
				///// EDIT /////
				case "edit":
					///// SET DEFAULT TEMPLATE NAME /////

					// Define the template name with post type
					meta.templateName = 'modal-edit modal-edit-' + $_.get( meta, 'post.post_type' );
					
					// Check if custom modal template exists
					// Will return false if not
					modalObj.templateUrl = $pwData.pw_get_template({
							subdir: 'modals',
							view: meta.templateName
						});
					
					// If no template is found
					if( modalObj.templateUrl == false )
						// Use the fallback template
						modalObj.templateUrl = $pwData.pw_get_template({
							subdir: 'modals',
							view: 	'modal-edit-post'
						});
					
					// Get default controller value from meta
					modalObj.controller = ( _.isUndefined( meta.controller ) ) ?
						'pwModalInstanceCtrl' : meta.controller;

					// Get default window value from meta
					modalObj.windowClass = ( _.isUndefined( meta.windowClass ) ) ?
						meta.templateName : meta.windowClass;

					// Disable Key Bindings
					meta.keybindings = false;

				break;

				///// MEDIA /////
				case "media":
					modalObj = {
						controller: 	"mediaModalInstanceCtrl",
						windowClass: 	"modal-media",
					};
					modalObj.templateUrl = $pwData.pw_get_template({
						subdir: 'modals',
						view: 	'modal-media'
					});
				break;

				///// VIEW /////
				case "view":
				///// FEED /////
				case "feed":
					modalObj = {
						template: 		"<div feed-item></div>",
						controller: 	"pwModalInstanceCtrl",
						windowClass: 	"modal-view-post",
					};
				break;

				///// TEMPLATE /////
				case "template":
					modalObj = {
						controller: 	"pwModalInstanceCtrl",
						windowClass: 	"modal-template",
						keybindings: 	false,
					};
					if( $_.get( meta, 'windowClass' ) )
						modalObj.windowClass = $_.get( meta, 'windowClass' );
				break;

			}

			///// GET CUSTOM OVERRIDE TEMPLATE URL /////
			// Here the `meta.templateName` can be used to override the template
			// If no inline template is defined
			if( _.isString( meta.templateName ) && _.isEmpty( modalObj.templateUrl )  ){

				// If there's a slash in the template, it's from another subdir
				if( $_.inArray( "/", meta.templateName ) ){
					var templateNameParts = meta.templateName.split("/");
					// Use the first part as the subdir
					var templateSubdir = templateNameParts[0];
					// Use the second part as the view
					var templateName = templateNameParts[1];
				}
				// Otherwise assume it's in /modals
				else {
					var templateSubdir = 'modals';
					var templateName = meta.templateName;
				}

				// Add template URL from template name 
				modalObj.templateUrl = $pwData.pw_get_template( { subdir: templateSubdir, view: templateName } );

			}
			
			///// PRIME MODAL OBJECT /////
			// Add resolve
			modalObj.resolve = {
					meta: function(){
						return meta;
					}
				};

			///// LAUNCH THE MODAL /////
			//$log.debug( 'MODAL META : ', meta );
			$log.debug( '$modal.open : modalObj : ', modalObj );

			var modalInstance = $modal.open( modalObj );

			modalInstance.result.then(function (selectedItem) {
				//$scope.post_title = post_title;
			}, function () {
				// WHEN CLOSE MODAL
				$pw.state.modals.open --;
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
	[ '$scope', '$rootScope', '$document', '$window', '$location', '$modalInstance', 'meta', '$log', 'pwData', '$timeout', '_', 'pwPosts', '$browser', '$modalStack', '$pw',  // 'pwQuickEdit',
	function( $scope, $rootScope, $document, $window, $location, $modalInstance, meta, $log, $pwData, $timeout, $_, $pwPosts, $browser, $modalStack, $pw ) { // , $pwQuickEdit

	// $modalInstance - switch modal template
	//$log.debug( '>>> $modalInstance <<<', $modalInstance );

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

	///// DETECT FEED ID /////
	/// FROM POST FEED
	// Check the post for a feed ID
	if( $_.getObj( meta, 'post.feed.id' ) ){
		$scope.modalFeed = {};
		$scope.modalFeed['id'] = meta.post.feed['id'];
		$log.debug( "FEED ID FROM : POST : ", $scope.modalFeed['id'] );
	}
	// FROM MODAL META
	// Check the modal meta for the feed ID
	else if( $_.getObj( meta, 'feed.id' )  ){
		$scope.modalFeed = {};
		$scope.modalFeed['id'] = meta.feed['id'];
		$log.debug( "FEED ID FROM : MODAL META : ", $scope.modalFeed['id'] );
	}

	///// FEED HANDLING /////
	if( $_.objExists( $scope.modalFeed, 'id' ) ){
		// Get the original full post object from the feed
		// In the case that only a partial post object was passed
		$scope.post = $pwPosts.getFeedPost( $scope.modalFeed['id'], $scope.post.ID );
		// Get the current position of the feed
		$scope.modalFeed['currentIndex'] = _.indexOf( $pwPosts.getFeed( $scope.modalFeed.id )['posts'], $scope.post );
		// Set the view to modal
		$scope.modalFeed = $_.set( $scope.modalFeed, 'view.current', 'modal' );

		$log.debug( 'FEED :', $scope.modalFeed );

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
	// TODO : Add ability to go past loaded feed items, and load and cache new feed items based on feed_outline
	// TODO : ... and then resort them in the feed_outline according to their position in feed_outline

	/*
	// Watch when feed.currentIndex changes
	$scope.$watch( "feed.currentIndex", function ( newValue, oldValue ){
		$log.debug( '$watch:feed.currentIndex : OLD VALUE : ' + oldValue + ' : NEW VALUE : ' + newValue );
		
		if( _.isUndefined( oldValue ) )
			oldValue = 0;

		if( $_.objExists( $scope, 'feed.currentIndex' ) )
			$scope.offsetFeedIndex( newValue - oldValue );
		
	}); 
	*/

	$scope.offsetFeedIndex = function( offset ){		
		// Set the current $scope.post object to reflect the current index
		// var offset = [ number ] // how many to switch, ie. 1 (next), -1 (previous)
		if( _.isUndefined( $scope.modalFeed ) ){
			$log.debug('nextPost() : No feed.');
			return false;
		}

		// Setup Vars
		var feedLength = $pwPosts.getFeed( $scope.modalFeed.id )['posts'].length;
		var currentIndex = $scope.modalFeed.currentIndex;
		var newIndex = currentIndex + offset;

		// If the feed is at the end and offset positive, loop back to the beginning
		if( newIndex > ( feedLength - 1 ) )
			newIndex = 0;

		// If the feed is at the beginning and offset negative, loop back to the end
		if( newIndex < 0 )
			newIndex = ( feedLength - 1 );

		// Automatically skip over the following post types
		var skipPostTypes = [ '_pw_block' ];

		// Get the possible new post for testing
		var newPost = $pwPosts.getFeed( $scope.modalFeed.id )['posts'][ newIndex ];

		// If the new possible post doesn't pass the test
		if( $_.inArray( $_.get( newPost, 'post_type' ), skipPostTypes ) ){
			// Recursively call this function, offsetting in the same direction
			$scope.offsetFeedIndex( offset + offset );
		}

		// If the new possible post passes
		else{
			// Set the new currentIndex
			$scope.modalFeed.currentIndex = newIndex;
			// Set the new scope post
			$scope.post = $pwPosts.getFeed( $scope.modalFeed.id )['posts'][ newIndex ];
		
			$scope.$broadcast( 'modalChangePost', {
				offset: offset,
				postId: $_.get( $scope.post, 'ID' ),
			});

			// Set the feed keybindings to this post
			setFeedKeybindings();

		}

	}

	$scope.nextPost = function(){
		$scope.offsetFeedIndex( 1 );
	};

	$scope.previousPost = function(){
		$scope.offsetFeedIndex( -1 );
	};


	///// ON : MODAL NEXT/PREVIOUS POST /////
	// Event listeners for switching post
	$scope.$on( 'modalNextPost', function( e, vars ){
		if( $_.get(vars,'feedId') == $scope.modalFeed.id )
			$scope.nextPost();
	});
	$scope.$on( 'modalPreviousPost', function( e, vars ){
		if( $_.get(vars,'feedId') == $scope.modalFeed.id )
			$scope.previousPost();
	});


	///// KEY PRESS /////
	// Capture Keydown
	
	$scope.keyDown = function( e ){
	
		//$log.debug( "$pw.state.modals.open:", $pw.state.modals.open );
		//$log.debug( "meta.modalIndex-1:", meta.modalIndex-1 );

		if( !$pw.hasKeybindings({ feedId: $scope.modalFeed.id }) )
			return false;

		// Check if the current modal is on top
		if( $pw.state.modals.open != meta.modalIndex+1 )
			return false;

		//// BIND KEYCODE TO ACTIONS ///
		var keyCode = parseInt( e.keyCode );
		if( !_.isUndefined( $scope.modalFeed ) ){
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
			
			$scope.$apply();
		}
		
	}

	var getKeyBindingsObj = function(){
		return {
			feedId: $scope.modalFeed.id,
		};
	}

	var setFeedKeybindings = function(){
		// Enable keybindings
		//$pw.state.modals.enableKeyBindings = true;		
		$pw.setKeybindings( getKeyBindingsObj() );
	}

	// Enable key bindings
	if( $_.get( meta, 'keybindings' ) ){
		
		setFeedKeybindings();

		// Bind keys to keyDown function
		$document.keydown( function( e ){
			$scope.keyDown( e );
		});
	}

	///// STANDARD FUNCTIONS /////
	// MODAL CLOSE
	$scope.close = function () {
		$modalInstance.dismiss('close');
	};

	// MODAL CLOSE
	// Will close the modal if mode is 'edit'
	$scope.closeIfEditMode = function ( mode ) {
		if( mode == 'edit' )
			$modalInstance.dismiss('close');
	};

	// MODAL FORWARD IF NEW MODE
	// Will forward to the given URL if mode is 'new'
	$scope.forwardIfNewMode = function( mode, url ) {
		if( $scope.mode == 'new' && !_.isUndefined( url ) )
			window.location.assign(url);
	};

	$scope.alert = function(message){
		alert(message);
	}

	// TRASH POST
	$scope.trashPost = function(){
		//$pwQuickEdit.trashPost($scope.post.ID, $scope);
	}; 

	// WATCH : FOR TRASHED
	// TODO : Set Parent post_status = trash via pwData.feeds
	// Watch on the value of post_status
	$scope.$watch( "post.post_status", function (){
		if( $_.getObj( $scope, 'post.post_status' ) == 'trash'  )
			// Close Modal
			$modalInstance.dismiss('close');
	}); 


	// WATCH : FOR CHANGE POST
	/*
	$scope.$watch( "post.ID", function (){
		if( $_.objExists( $scope, 'post.post_permalink' )  ){
			// Change the URL to the permalink of the post, and the title to the title
			// TODO : Currently Causing Infinite Loops

			//$locationProvider.html5Mode(true);
			//$location.path( $scope.post.post_permalink );
			//$window.history.replaceState();
			//History.replaceState( {}, $scope.post.post_title, $scope.post.post_permalink );
			
			//$window.history.replaceState( {}, $scope.post.post_title, $scope.post.post_permalink );
			// https://github.com/angular/angular.js/issues/3924

			//$browser.url( $scope.post.post_permalink );
			//$location.replace();
			//History.replaceState( {}, $scope.post.post_title, $scope.post.post_permalink );
			
			///// USE THIS ONE - WITH PHP TAG IN THE PAGE WHICH SWITCHES ROUTING TO HTML5 MODE /////
			//$location.path( $scope.post.post_permalink );
			
			//$timeout( function(){
			//	History.replaceState( {}, $scope.post.post_title, $scope.post.post_permalink );
			//}, 0 );
		}
	}); 
	*/

	$rootScope.$on('$routeChangeStart', function(event){ 
	    event.preventDefault(); 
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
    [ '$scope', '$sce', '$modalInstance', 'meta', 'pwData', '$pw',
    function( $scope, $sce, $modalInstance, meta, pwData, $pw ) { 


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


