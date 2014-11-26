/*/////////////////////////////////////////////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminBackgrounds', [ function(){
    return { 
        controller: 'pwAdminBackgroundsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-backgrounds');
        }
    };
}]);

postworldAdmin.controller('pwAdminBackgroundsCtrl',
	[ '$scope', '$log', '$window', '$parse', 'iData', 'pwData', '_', 'pwPostOptions', 'iOptionsData',
	function ( $scope, $log, $window, $parse, $iData, $pwData, $_, $pwPostOptions, $iOptionsData ) {
	
	$scope.view = 'contexts';

	$scope.newBackground = function(){
		var newBackground = {
			'name': 		'My Background',
			'id': 			"background_" + $_.makeHash( 8 ),
			'description': 	'',

			'primary' : {
				'image':{
					'id': null,
					'size':'full',
					'parallax':-0.66,
				},
				'style':{
					'background-size': null,
					'background-position': 'center top',
					'background-repeat':null,
					'background-color': null,
					'background-attachment': null,
					'background-color': null,
				},
			},

			'secondary' : {
				'image':{
					'id': null,
					'size':'full',
					'parallax':-0.33,
				},
				'style':{
					'background-size': 100,
					'background-position': 'center top',
					'background-repeat':'repeat',
					'background-attachment': null,
					'opacity': 50,
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


	$scope.optionsMeta = $iOptionsData.options;


	
}]);
