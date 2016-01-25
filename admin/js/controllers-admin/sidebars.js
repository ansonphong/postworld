/*
  ____  _     _      _                    
 / ___|(_) __| | ___| |__   __ _ _ __ ___ 
 \___ \| |/ _` |/ _ \ '_ \ / _` | '__/ __|
  ___) | | (_| |  __/ |_) | (_| | |  \__ \
 |____/|_|\__,_|\___|_.__/ \__,_|_|  |___/

//////////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminSidebars', [ function(){
    return { 
        controller: 'pwAdminSidebarsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-style');
        }
    };
}]);

postworldAdmin.controller('pwAdminSidebarsCtrl',
	[ '$scope', '$log', '$window', '$parse', 'pwData', '_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $pwData, $_, $pwPostOptions ) {
	
	$scope.newSidebar = function(){
		var newSidebar = {
			'name': 		'New Sidebar',
			'id': 			"sidebar-" + $_.randomString( 8, ['numbers','lowercase'] ),
			'description': 	'Description goes here.',
			'class': 		'widget',
			'before_widget':'<div class="sidebar-widget">',
			'after_widget': '</div>',
			'before_title': '<h3 class="sidebar-title">',
			'after_title':  '</h3>'
		};

		$scope.pwSidebars.push( newSidebar );
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
