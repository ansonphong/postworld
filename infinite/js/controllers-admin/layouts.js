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
	[ '$scope', '$log', '$window', '$parse', 'iData', '_',
	function ( $scope, $log, $window, $parse, $iData, $_ ) {

	// Initialize Status
	$scope.status = "done";

	// Initialize iLayouts
	if( _.isUndefined( $scope.iLayouts ) ||
		!_.isObject( $scope.iLayouts ) || 
		_.isEmpty( $scope.iLayouts ) )
		$scope.iLayouts = {};



	// Initialize Settings Object
	angular.forEach( $scope.iLayoutOptions.contexts, function( value ){

		$log.debug( value );

		// Initialize Layout Context Names
		if( _.isUndefined( $scope.iLayouts[value.name] ) )
			$scope.iLayouts[value.name] = {};
	
		// Initialize Header
		if( _.isUndefined( $scope.iLayouts[value.name].header ) )
			$scope.iLayouts[value.name].header = "";

		// Initialize Sidebars
		if( _.isUndefined( $scope.iLayouts[value.name].sidebars ) )
			$scope.iLayouts[value.name].sidebars = {};

		// Initialize Template
		if( _.isUndefined( $scope.iLayouts[value.name].template ) ){
			$scope.iLayouts[value.name].template = 'default';
			// Set the default default template
			if( value.name == 'default' )
				$scope.iLayouts[value.name].template = 'full-width';
		}


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

	////////// FUNCTIONS //////////

	$scope.selectedLayout = function( contextId ){
		// If the layout options cache is undefined
		if( _.isUndefined( $scope.layoutOptions ) ){
			// Get the available layout templates
			var layoutOptions = $_.getObj($scope, 'iLayoutOptions.templates.options');
			// Get the 'default' layout templates
			var defaultOption = $_.getObj($scope, 'iLayoutOptions.templates.default')[0];
			// If we got layout options
			if( layoutOptions ){
				// Remove two-way data binding
				options = angular.fromJson( angular.toJson( layoutOptions ) );
				// If default template is an object
				if( _.isObject( defaultOption ) )
					// Push it to available options
					options.push( defaultOption );
			} else
				// Set default array
				options = [];
			// Save options in a cache for performance
			$scope.layoutOptions = options;
		}
		// Use underscore to return the selected option object based on slug key
		return _.findWhere( $scope.layoutOptions, { slug: contextId } );
	}


	////////// SHOW / HIDE LOGIC //////////
	// Logic for showing / hiding modules
	$scope.showModule = function( module, contextName, meta ){

		var template = $_.getObj( $scope.iLayouts, contextName + '.template' );
		if( !template )
			template = '';

		///// SHOW LOGIC /////
		switch( module ){
			/// HEADER & FOOTER ///
			case 'headerFooter':
				if( template == 'default' || template == '' )
					return false;
				else
					return true;
				break;

			/// SIDEBARS ///
			case 'sidebars':
				// If any sidebars are registered
				if( $scope.iSidebars.length > 0 ){
					if( template == 'default' ||
						template == 'full-width' ||
						template == '' )
						return false;
					else
						return true;
				}
				break;

			/// SIDEBAR LOCATIONS ///
			case 'sidebar-location':
				// Left Sidebar
				if( meta == "left" ){
					if(	template.indexOf("left") != -1 )
						return true;
					else
						return false;
				}
				// Right Sidebar
				if( meta == "right" ){
					if(	template.indexOf("right") != -1 )
						return true;
					else
						return false;
				}
				break;

		}

	};


	


}]);

