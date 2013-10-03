
///// INITIALIZE /////
var feed_init = {};
var feed_data = {};

///// THE POSTWORLD MODULE /////
var postworld = angular.module('postworld', []);

///// POSTWORLD META CONTROLLER /////
postworld.controller('postworld', function($scope) {
	$scope.templates = templates;
});

////////// HELPERS ////////
window.isInArray =  function(value, array) {
  return array.indexOf(value) > -1 ? true : false;
}

////////// TEST FEED DIRECTIVE //////////
// Takes the element with test-feed="feed_id" on it and turns it into a feed
postworld.directive( 'testFeed', ['$compile', function($compile, $scope){

	return { 
		restrict: 'A',
		scope : function(){
			// Scope functions here
		},
		link : function ($scope, elem, attrs){

			///// SETUP /////
			// Get the ID of the feed from the given attribute
			feed_id = attrs.testFeed;
			$scope.feed_id = feed_id;

			// Get the JSON Object associated with the feed_id
			feed_element = angular.element(elem);

			///// TRANSFER SETTINGS /////
			// Transfer ['feed_init'] Object settings into ['feed_data'] Object
			global_feed_object = window['feed_data'][feed_id];
			init_feed_object = window['feed_init'][feed_id];

			angular.forEach(init_feed_object, function(value, key){
				global_feed_object[key] = init_feed_object[key];
			});

			///// ADD CLASSES /////
			// Add feed classes
			feed_element.addClass('feed live_feed');

			// Add class of the current view
			feed_element.addClass( global_feed_object['view']['current'] );
			
			// Add ID attribute classes
			feed_element.attr('ID',feed_id);

			///// ADD CONTROL PANEL /////
			if ( global_feed_object.panel ) {
				panel_id = global_feed_object.panel;
				panel_src = "templates.panels." + panel_id ;
				panel_html = "<div class=\"panel\" ng-include src=\" " + panel_src + "\"></div>";
			}
			else{
				panel = "";
			}

			///// ADD FEED /////
			// Define Variables
			feed_controller = "postworld_feed";
			repeat_pattern = "post in feed_data.posts | orderBy:feed_data.order_by";
			template_source = "get_template(post.post_type)";
			
			// Build DOM Structure
			feed_html = "<div ng-controller=\" " + feed_controller + " \">" + panel_html + "<div class=\"post\" ng-repeat=\" " + repeat_pattern + " \"><div ng-include src=\" " + template_source + " \"></div></div></div>";
			feed_element = angular.element( feed_html );
			elem.append( feed_element );
			
			// Compile DOM into Angular Scope
			$compile( feed_element )( $scope );

		}
	}
}]);


////////// PROTOTYPE EDIT FIELD DIRECTIVE //////////
// Takes the element with test-feed="feed_id" on it and turns it into a feed
postworld.directive( 'editField', ['$compile', function($compile, $scope){

	return { 
		restrict: 'A',
		scope : function(){
			// Scope functions here
		},
		link : function (scope, elem, attrs){

			////////// PARSE INPUT FIELDS //////////

			///// TEXT INPUTS //////
			if ( attrs.input.indexOf("input-") > -1){
				input_text_fields = ['text','password','hidden','url'];
				input_extension = attrs.input.replace("input-", ""); // strip "input-"
				if (input_text_fields.indexOf( input_extension ) > -1){
					input_html = "<input type='" + input_extension + "' name='" + attrs.editField + "' id='" + attrs.editField + "' value='"+ attrs.value +"'>";
					input_element = angular.element( input_html );
					elem.append( input_element );
					//$compile( input_element )( scope );
				}
			}

			///// DROPDOWN SELECT //////
			if ( attrs.input.indexOf("select") > -1){

				// Check for "-multiple" extension
				input_extension = attrs.input.replace("select-", ""); // strip "input-"
				if (input_extension == 'multiple'){
					multiple = ' multiple ';
					// Split the value attribute into an Array
					oValue = attrs.value.split(',');
				}
				else{
					multiple = '';
					oValue = attrs.value;
				}
				
				// Get the size of the select area
				if ( attrs.size )
					size = " size='"+attrs.size+"' ";
				else
					size = '';

				// Parse the HTML
				select_head = "<select id='"+attrs.editField+"' name='"+attrs.editField+"' " + multiple + " " + size + ">";
				select_foot = "</select>";
				select_items = "";

				// Loop through each item in the editField object
				angular.forEach( window[attrs.editField], function(value, key){
					// Check to see if current key in oValue
					if( key == oValue || isInArray(key, oValue) )
						selected = 'selected';
					else
						selected = '';

					// Add option
					select_items += "<option value='"+key+"' "+selected+">"+value+"</option>";
				});
				
				input_element = angular.element( select_head + select_items + select_foot );
				elem.append( input_element );
				//$compile( input_element )( scope );
				
			}


		}
	}
}]);


////////// POSTWORLD FEED CONTROLLER //////////
postworld.controller('postworld_feed', function($scope) {

    // Pull feed_data[feed_id] Object for current feed into $scope
    $scope.feed_data = feed_data[ $scope.feed_id ];

    ///// SET VIEW TEMPLATE /////
    $scope.get_template = function (post_type) {
        return $scope.templates.posts[post_type][$scope.feed_data.view.current];
    }

});


////////// UTILITIES //////////

// Disable Template Cache for Development
postworld.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});





