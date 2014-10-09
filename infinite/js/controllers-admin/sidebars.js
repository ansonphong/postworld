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

infinite.controller('iAdminSidebarsCtrl',
	[ '$scope', '$log', '$window', '$parse', 'iData', 'pwData', '_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $iData, $pwData, $_, $pwPostOptions ) {
	

	$scope.newSidebar = function(){
		var newSidebar = {
			'name': 		'New Sidebar',
			'id': 			"sidebar_" + $_.makeHash( 8 ),
			'description': 	'Description goes here.',
			'class': 		'widget',
			'before_widget':'<div class="sidebar-widget">',
			'after_widget': '</div>',
			'before_title': '<h3 class="sidebar-title">',
			'after_title':  '</h3>'
		};

		$scope.iSidebars.push( newSidebar );
		$scope.selectItem( newSidebar );
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

	
}]);
