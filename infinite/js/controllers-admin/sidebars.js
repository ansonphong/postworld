/*_    _       _           _         ____  _     _      _                    
 (_)  / \   __| |_ __ ___ (_)_ __   / ___|(_) __| | ___| |__   __ _ _ __ ___ 
 | | / _ \ / _` | '_ ` _ \| | '_ \  \___ \| |/ _` |/ _ \ '_ \ / _` | '__/ __|
 | |/ ___ \ (_| | | | | | | | | | |  ___) | | (_| |  __/ |_) | (_| | |  \__ \
 |_/_/   \_\__,_|_| |_| |_|_|_| |_| |____/|_|\__,_|\___|_.__/ \__,_|_|  |___/
                                                                             
/////////////////////////////////////////////////////////////////////////////*/

infinite.directive( 'iAdminSidebars', [ function(){
    return { 
        controller: 'iAdminSidebarsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('i-admin-style');
        }
    };
}]);

infinite.controller('iAdminSidebarsCtrl', function ( $scope, $window, $parse, iData ) {

	$scope.default_sidebar = {
			'name': 		'New Sidebar',
			'id': 			'newsidebar-1',
			'description': 	'Description goes here.',
			'class': 		'widget',
			'before_widget':'<div class="sidebar-widget">',
			'after_widget': '</div>',
			'before_title': '<h3 class="sidebar-title">',
			'after_title':  '</h3>'
		};

	$scope.newSidebar = function(){
		$scope.sidebars.push( $scope.default_sidebar );
	}

	$scope.removeSidebar = function(sidebar){
		var updatedSidebars = [];
		angular.forEach( $scope.sidebars, function( value ){
			if( sidebar != value ){
				updatedSidebars.push(value);
			}
		});
		$scope.sidebars = updatedSidebars;
	}

	
});
