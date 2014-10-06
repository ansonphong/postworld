/*_    _       _           _         _                            _   
 (_)  / \   __| |_ __ ___ (_)_ __   | |    __ _ _   _  ___  _   _| |_ 
 | | / _ \ / _` | '_ ` _ \| | '_ \  | |   / _` | | | |/ _ \| | | | __|
 | |/ ___ \ (_| | | | | | | | | | | | |__| (_| | |_| | (_) | |_| | |_ 
 |_/_/   \_\__,_|_| |_| |_|_|_| |_| |_____\__,_|\__, |\___/ \__,_|\__|
                                                |___/                 
/////////////////////////////////////////////////////////////////////*/

infinite.directive( 'iAdminLayout', [ function(){
    return { 
        controller: 'iAdminLayoutCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('i-admin-layout');
        }
    };
}]);

infinite.controller('iAdminLayoutCtrl',
	[ '$scope', '$window', '$parse', 'iData', '_',
	function ( $scope, $window, $parse, $iData, $_ ) {

	// Initialize Status
	$scope.status = "done";

	// Initialize iLayouts
	if( _.isUndefined( $scope.iLayouts ) ||
		!_.isObject( $scope.iLayouts ) )
		$scope.iLayouts = {};

	// Initialize Settings Object
	angular.forEach( $scope.iLayoutOptions.contexts, function( value ){
		
		// Initialize Layout Context Names
		if( _.isUndefined( $scope.iLayouts[value.name] ) )
			$scope.iLayouts[value.name] = {};
	
		// Initialize Header
		if( _.isUndefined( $scope.iLayouts[value.name].header ) )
			$scope.iLayouts[value.name].header = "";

		// Initialize Sidebars
		if( _.isUndefined( $scope.iLayouts[value.name].sidebars ) )
			$scope.iLayouts[value.name].sidebars = {};

		// Initialize Sidebar Locations
		angular.forEach( $scope.iLayoutOptions.widget_areas, function( sidebar_location ){

			if( _.isUndefined( $scope.iLayouts[value.name].sidebars[sidebar_location.slug] ) )
				$scope.iLayouts[value.name].sidebars[sidebar_location.slug] = {}; 


			// Initialize Widths for Screen Sizes
			if( _.isUndefined( $scope.iLayouts[value.name].sidebars[sidebar_location.slug].width ) )
					$scope.iLayouts[value.name].sidebars[sidebar_location.slug].width = {}; 

			// Populate Widths for Screen Sizes
			angular.forEach( $scope.iLayoutOptions.screen_sizes, function( screen_size ){
				if( _.isUndefined( $scope.iLayouts[value.name].sidebars[sidebar_location.slug].width[screen_size.slug] ) )
				$scope.iLayouts[value.name].sidebars[sidebar_location.slug].width[screen_size.slug] = screen_size.default_sidebar_width;
			});


		});
			
		


	});

	////////// SHOW / HIDE LOGIC //////////
	// Logic for showing / hiding modules
	$scope.showModule = function( module, layoutName, meta ){

		var layout = $_.getObj( $scope.iLayouts, layoutName + '.layout' );
		if( !layout )
			layout = '';

		///// SHOW LOGIC /////
		switch( module ){
			/// HEADER & FOOTER ///
			case 'header':
			case 'footer':
				if( layout == 'default' || layout == '' )
					return false;
				else
					return true;
				break;

			/// SIDEBARS ///
			case 'sidebars':
				// If any sidebars are registered
				if( $scope.i_sidebars.length > 0 ){
					if( layout == 'default' ||
						layout == 'full-width' ||
						layout == '' )
						return false;
					else
						return true;
				}
				break;

			/// SIDEBAR LOCATIONS ///
			case 'sidebar-location':
				// Left Sidebar
				if( meta == "left" ){
					if(	layout.indexOf("left") != -1 )
						return true;
					else
						return false;
				}
				// Right Sidebar
				if( meta == "right" ){
					if(	layout.indexOf("right") != -1 )
						return true;
					else
						return false;
				}
				break;

		}

	};


	


}]);

