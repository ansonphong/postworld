/*_                            _       
 | |    __ _ _   _  ___  _   _| |_ ___ 
 | |   / _` | | | |/ _ \| | | | __/ __|
 | |__| (_| | |_| | (_) | |_| | |_\__ \
 |_____\__,_|\__, |\___/ \__,_|\__|___/
             |___/                     
///////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminLayout', [ function(){
    return { 
        controller: 'pwAdminLayoutCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-layout');
        }
    };
}]);

postworldAdmin.controller('pwAdminLayoutCtrl',
	[ '$scope', '$log', '$window', '$parse', '_',
	function ( $scope, $log, $window, $parse, $_ ) {

	// Initialize Status
	$scope.status = "done";

	// Initialize pwLayouts
	if( _.isUndefined( $scope.pwLayouts ) ||
		!_.isObject( $scope.pwLayouts ) || 
		_.isEmpty( $scope.pwLayouts ) )
		$scope.pwLayouts = {};


	// Initialize Settings Object
	angular.forEach( $scope.pwLayoutOptions.contexts, function( value ){

		// Initialize Layout Context Names
		if( _.isUndefined( $scope.pwLayouts[value.name] ) )
			$scope.pwLayouts[value.name] = {};
	
		// Initialize Header
		if( _.isUndefined( $scope.pwLayouts[value.name].header ) )
			$scope.pwLayouts[value.name].header = "";

		// Initialize Sidebars
		if( _.isUndefined( $scope.pwLayouts[value.name].sidebars ) )
			$scope.pwLayouts[value.name].sidebars = {};

		// Initialize Template
		if( _.isUndefined( $scope.pwLayouts[value.name].template ) ){
			$scope.pwLayouts[value.name].template = 'default';
			// Set the default default template
			if( value.name == 'default' )
				$scope.pwLayouts[value.name].template = 'full-width';
		}


		// Initialize Sidebar Locations
		angular.forEach( $scope.pwLayoutOptions.widget_areas, function( sidebar_location ){

			if( _.isUndefined( $scope.pwLayouts[value.name].sidebars[sidebar_location.slug] ) )
				$scope.pwLayouts[value.name].sidebars[sidebar_location.slug] = {}; 


			// Initialize Widths for Screen Sizes
			if( _.isUndefined( $scope.pwLayouts[value.name].sidebars[sidebar_location.slug].width ) )
					$scope.pwLayouts[value.name].sidebars[sidebar_location.slug].width = {}; 

			// Populate Widths for Screen Sizes
			angular.forEach( $scope.pwLayoutOptions.screen_sizes, function( screen_size ){
				if( _.isUndefined( $scope.pwLayouts[value.name].sidebars[sidebar_location.slug].width[screen_size.slug] ) )
				$scope.pwLayouts[value.name].sidebars[sidebar_location.slug].width[screen_size.slug] = screen_size.default_sidebar_width;
			});


		});
			
	});

	////////// FUNCTIONS //////////
	$scope.initLayoutOptions = function(){
		// Basically merge the default option into the layout options
		// Get the available layout templates
		var layoutOptions = $_.getObj($scope, 'pwLayoutOptions.templates.options');
		// Get the 'default' layout templates
		var defaultOption = $_.getObj($scope, 'pwLayoutOptions.templates.default')[0];
		// If we got layout options
		if( layoutOptions ){
			// Remove two-way data binding
			options = angular.fromJson( angular.toJson( layoutOptions ) );
			// If default template is an object
			if( _.isObject( defaultOption ) )
				// Add the default context
				options.push( defaultOption );
		} else
			// Set default array
			options = [];
		// Save options in a cache for performance
		$scope.layoutOptions = options;
	}
	$scope.initLayoutOptions();

	$scope.selectedLayout = function( contextId ){
		// Use underscore to return the selected option object based on slug key
		return _.findWhere( $scope.layoutOptions, { slug: contextId } );
	}


	////////// SHOW / HIDE LOGIC //////////
	// Logic for showing / hiding modules
	$scope.showModule = function( module, layout, meta ){

		// Get the template from the layout
		var template = $_.getObj( layout, 'template' );
		if( !template )
			template = 'default';
		if( _.isUndefined( 'template' ) || template == 'default' )
			return false;

		///// SHOW LOGIC /////
		switch( module ){
			/// HEADER & FOOTER ///
			case 'headerFooter':
				if( template == 'default' || _.isEmpty(template) )
					return false;
				else
					return true;
				break;

			/// SIDEBARS ///
			case 'sidebars':
				// If any sidebars are registered
				if( $scope.pwSidebars.length > 0 ){
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

