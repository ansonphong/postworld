/*////////////// ------- SERVICE ------- //////////////*/  

postworld.service('pwModal', [ '$rootScope', '$log', '$location', '$modal', 'pwData',
	function ( $rootScope, $log, $location, $modal, pwData ) {
	return{

		openModal : function( meta ){
			
			///// DEFAULTS /////
			if( _.isUndefined(post) )
				var post = {};
			if( _.isUndefined(meta) )
				var meta = {};

			// Default Template ID
			var mode = ( _.isUndefined( meta.mode ) ) ?
					'default' : meta.mode;

			// Default Template ID
			var panelId = ( _.isUndefined( meta.panelId ) ) ?
					'modal-default' : meta.panelId;

			// Default Controller
			var controller = ( _.isUndefined( meta.controller ) ) ?
					'osModalInstanceCtrl' : meta.controller;

			// Default Window Class
			var windowClass = ( _.isUndefined( meta.windowClass ) ) ?
				'os-modal-default' : meta.windowClass;


			////////// SWITCH MODE //////////
			// mode : Can be used to pass the preset mode
			// or if string not found, this substitutes as the panel id
			switch( meta.mode ){
				///// QUICK EDIT /////
				// TODO - IMPLIMENT THIS <<<< Currently it's using the lineage "pwQuickEdit" method
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
					// TODO : Add support to detect post types / format and check for availability of the modal template					
					templateName = "modal-view-post";
					controller = "pwModalInstanceCtrl";
					windowClass = "modal-view-post"; 
				break;
				///// MEDIA /////
				case "media":
					// TODO : Add support to detect post types / format and check for availability of the modal template					
					templateName = "modal-media";
					controller = "pwModalInstanceCtrl";
					windowClass = "modal-media"; 
				break;
				///// DEFAULT /////
				default:
					templateName = meta.templateName;
					controller = meta.controller;
					windowClass = meta.windowClass;
			}

			// Default Defaults
			if( _.isUndefined( meta.mode ) )
				var mode = 'quick-edit';

			$log.debug( "Launch Modal // MODAL TEMPLATE : " + templateName + " // META : ", meta );

			var modalInstance = $modal.open({
			  templateUrl: pwData.pw_get_template( { subdir: 'modals', view: templateName } ),
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
				$location.path('/');
				//$rootScope.$apply();

			});
		},

	}
}]);



////////// MODAL INSTANCE CONTROL //////////

postworld.controller('pwModalInstanceCtrl',
	[ '$scope', '$rootScope',  '$modalInstance', 'mode', 'pwData', '$timeout', // 'pwQuickEdit',
	function($scope, $rootScope, $modalInstance, mode, $pwData, $timeout) { // , $pwQuickEdit

	// Set Default Mode
	$scope.mode = ( !_.isUndefined( mode ) ) ?
		mode : "view";

	// Import the passed post object into the Modal Scope
	if( !_.isUndefined( meta.post ) )
		$scope.post = meta.post;
		//alert( JSON.stringify( $scope.post ) );

	// TIMEOUT
	// Allow editPost Controller to Initialize
	if( !_.isUndefined( $scope.post.ID ) &&
		mode != 'new' ){

		// Broadcast to Load in the Post Data
		$timeout(function() {
		  $scope.$broadcast('loadPostData', $scope.post.ID );
		}, 3);
	}
	
	// MODAL CLOSE
	$scope.close = function () {
		$modalInstance.dismiss('close');
	};

	// TRASH POST
	$scope.trashPost = function(){
		//$pwQuickEdit.trashPost($scope.post.ID, $scope);
	}; 

	// WATCH FOR TRASHED
	// Close Modal
	// Set Parent post_status = trash
	// Watch on the value of post_status
	$scope.$watch( "post.post_status",
		function (){
			if( $scope.post.post_status == 'trash'  )
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
	['$scope', '$rootScope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
	'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', 'ext', '$window', 'pwRoleAccess', 'pwQuickEdit', 'pwModal',
	function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
		$pwData, $log, $route, $routeParams, $location, $http, $ext, $window, $pwRoleAccess, $pwQuickEdit, $pwModal ) {

	$scope.openModal = function( meta ){
		$pwModal.openModal( meta );
	}

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
		openQuickEdit : function( post, mode ){
			
			// Default Defaults
			if( _.isUndefined( mode ) )
				var mode = 'quick-edit';

			$log.debug( "Launch Quick Edit : MODE : " + mode, post );

			var modalInstance = $modal.open({
			  templateUrl: pwData.pw_get_template( { subdir: 'panels', view: 'modal-edit-post' } ),
			  controller: 'quickEditInstanceCtrl',
			  windowClass: 'quick_edit',
			  resolve: {
				post: function(){
					return post;
				},
				mode: function(){
					return mode;
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


