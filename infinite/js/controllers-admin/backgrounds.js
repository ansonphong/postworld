/*/////////////////////////////////////////////////////////////////////////////*/

postworld.directive( 'pwAdminBackgrounds', [ function(){
    return { 
        controller: 'pwAdminBackgroundsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-backgrounds');
        }
    };
}]);

postworld.controller('pwAdminBackgroundsCtrl',
	[ '$scope', '$log', '$window', '$parse', 'iData', 'pwData', '_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $iData, $pwData, $_, $pwPostOptions ) {
	

	$scope.newBackground = function(){
		var newBackground = {
			'name': 		'My Background',
			'id': 			"background_" + $_.makeHash( 8 ),
			'description': 	'',

			'primary' : {
				'id': null,
				'css':{
					'background-size': '100%',
					'background-position': null,
					'background-repeat':null,
					'background-color': null,
				},
			},

			'secondary' : {
				'id': null,
				'css':{
					'background-size': '100%',
					'background-position': null,
					'background-repeat':null,
					'background-color': null,
					'opacity': 1,
				},
			},

		};

		$scope.pwBackgrounds.push( newBackground );
		$scope.selectItem( newBackground );
	}

	$scope.removeBackground = function(background){
		var updatedBackgrounds = [];
		angular.forEach( $scope.pwBackgrounds, function( value ){
			if( background != value ){
				updatedBackgrounds.push(value);
			}
		});
		$scope.pwBackgrounds = updatedBackgrounds;
	}

	
}]);
