/*                _                    _   ___                            
  _ ____      __ | |    ___   __ _  __| | |_ _|_ __ ___   __ _  __ _  ___ 
 | '_ \ \ /\ / / | |   / _ \ / _` |/ _` |  | || '_ ` _ \ / _` |/ _` |/ _ \
 | |_) \ V  V /  | |__| (_) | (_| | (_| |  | || | | | | | (_| | (_| |  __/
 | .__/ \_/\_/   |_____\___/ \__,_|\__,_| |___|_| |_| |_|\__,_|\__, |\___|
 |_|                                                           |___/      
 ///////////////////////// LOAD IMAGE DIRECTIVE ////////////////////////*/

 postworld.directive( 'pwImage', [ function($scope){
	return {
		restrict: 'AE',
		controller: 'pwImageCtrl',
		scope: {
			//userQuery:'@userQuery', // INOP
			imageId:'@imageId',
			imageModel:'=imageModel',
		},
		link: function( $scope, element, attrs ){
			$scope.imageId = parseInt($scope.imageId);

			// OBSERVE Attribute
			attrs.$observe('imageId', function(value) {
				$scope.getImage($scope.imageId);
			});
			
		}
	};
}]);


postworld.controller( 'pwImageCtrl',
	[ '$scope', '$window', '$timeout', 'pwData', '$log',
	function( $scope, $window, $timeout, $pwData, $log ) {

	$scope.getImage = function( imageId ){
		
		// If the value is empty
		if( _.isEmpty(imageId) ){
			$scope.imageModel = {};
			return false;
		}

		var args = {
			'image_id': imageId,
			//'return_fields': ['ID','image(all)'],
			//'return': 'image( large, 300, 300, true )' // id / all / ID of registeded image size / parameters of image - passed to pw_get_post 
		};

		$pwData.get_image( args ).then(
			// Success
			function(response) {    
				$scope.imageModel = response.data;
			},
			// Failure
			function(response) {
				//$scope.movements = [{post_title:"Movements not loaded.", ID:"0"}];
			}
		);

	};


}]);