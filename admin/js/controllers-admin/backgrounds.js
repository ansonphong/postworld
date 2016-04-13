/*____             _                                   _     
 | __ )  __ _  ___| | ____ _ _ __ ___  _   _ _ __   __| |___ 
 |  _ \ / _` |/ __| |/ / _` | '__/ _ \| | | | '_ \ / _` / __|
 | |_) | (_| | (__|   < (_| | | | (_) | |_| | | | | (_| \__ \
 |____/ \__,_|\___|_|\_\__, |_|  \___/ \__,_|_| |_|\__,_|___/
                       |___/                                 
/////////////////////////////////////////////////////////////*/

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
	function ( $scope, $log, $window, $parse, $pwData, $_, $pwPostOptions, $pw ) {
	
	$scope.view = 'settings';

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


	$scope.optionsMeta = $pw.optionsMeta;


	
});
