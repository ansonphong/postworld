/*
  _____         _                         _____ _ _ _            
 |_   _|____  _| |_ __ _ _ __ ___  __ _  |  ___(_) | |_ ___ _ __ 
   | |/ _ \ \/ / __/ _` | '__/ _ \/ _` | | |_  | | | __/ _ \ '__|
   | |  __/>  <| || (_| | | |  __/ (_| | |  _| | | | ||  __/ |   
   |_|\___/_/\_\\__\__,_|_|  \___|\__,_| |_|   |_|_|\__\___|_|   
																 
////////// NG-TEXTAREA-FILTER DIRECTIVE //////////// */
// Adds extended functionality to textareas
// Takes attributes : data-maxlength, data-readmore

// NEEDS REFCTOR!
/*
postworld.directive('pwTextarea', function() {
		return function($scope, element, attributes) {
			var model = attributes.ngModel;
			var readmore = attributes.readmore;
				$scope.$watch( model,
					function (){
						var modelObjArray = model.split(".");

						// TODO : Refactor to use scope/model directive input 
						var textareaContents = $scope[ modelObjArray[0] ][ modelObjArray[1] ];

						///// Filter Text Contents /////
						// Max Characters
						var maxChars = ( !_.isUndefined( attributes.textareaMaxlength ) ) ?
							attributes.textareaMaxlength : 40 ;

						// Readmore Quote
						var readMore = ( !_.isUndefined( attributes.textareaReadmore ) ) ?
							attributes.textareaReadmore : "" ;

						// If it's over the maxLength, trim it
						if ( !_.isUndefined( textareaContents ) ){
							if ( textareaContents.length > maxChars && textareaContents.length > (maxChars-readMore.length) ){
								textareaContents = textareaContents.slice(0, (maxChars-readMore.length)) + readMore;
							}

							// Insert new textareaContents;
							$scope[ modelObjArray[0] ][ modelObjArray[1] ] = textareaContents;
						}

					}, 1 );
		};
	});
*/





postworld.directive( 'pwTextarea', [ function($scope){
	return {
		restrict: 'A',
		controller: 'pwTextareaCtrl',
		scope:{
			maxlength: '@textareaMaxlength',
			readMore:  '@textareaReadmore',
			contents:  '=ngModel',
		},
		link: function( $scope, element, attrs ){
		}
	};
}]);

postworld.controller('pwTextareaCtrl',
	[ "$scope", "$window", "$_", "$log", 
	function($scope, $window,  $_, $log ) {

		///// Filter Text Contents /////
		// Max Length
		$scope.maxlength = ( _.isUndefined( $scope.maxlength ) ) ?
			// Default length
			40 :
			// Force value to integer
			parseInt( $scope.maxlength );

		// Max Characters
		if( _.isUndefined( $scope.readMore ) )
			$scope.readMore = "";

		// Trim the text if it's too long
		$scope.$watch( 'contents', function(){
			if( !_.isUndefined( $scope.contents ) )
				if( $scope.contents.length > $scope.maxlength )
					$scope.contents = $scope.contents.slice(0, ( $scope.maxlength - $scope.readMore.length)) + $scope.readMore;
		});

}]);

