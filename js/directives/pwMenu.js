/*                    __  __                      
  _ ____      __  _  |  \/  | ___ _ __  _   _ ___ 
 | '_ \ \ /\ / / (_) | |\/| |/ _ \ '_ \| | | / __|
 | |_) \ V  V /   _  | |  | |  __/ | | | |_| \__ \
 | .__/ \_/\_/   (_) |_|  |_|\___|_| |_|\__,_|___/
 |_|                                              
 ///////////////////////// MENU LOADING DIRECTIVE ////////////////////////*/

// Gets the site menus and populates into the local scope
postworld.directive( 'pwMenus', function( $window, $timeout, $pwData, $log, $_, $pw ){
	return {
		restrict: 'AE',
		scope:{
			pwMenus:'=',	// Where to populated the result
		},
		link: function( $scope, element, attrs ){
			$scope.getMenus = function(){
				// If the menus are preloaded
				var menus = $_.get( $pw, 'admin.menus' );
				if( menus != false ){
					$scope.pwMenus = menus;
					return;
				}
				// If the data isn't preloaded, get it by AJAX
				$pwData.get_menus({}).then(
					function(response) {    
						$scope.pwMenus = response.data;
					},
					function(response) {}
				);
			};
			$scope.getMenus();
		}
	};
});
