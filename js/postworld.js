
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
	if (array)
		return array.indexOf(value) > -1 ? true : false;
	else
		return false;
}

window.varExists = function(value){
	if ( typeof value === 'undefined' )
		return false;
	else
		return true;
}

window.isEmpty = function(value){
	if ( typeof value === 'undefined' )
		return false;
	else
		return value[0].value ? true : false;	
}

function extract_parentheses(string){
	var pattern = /\((.+?)\)/g,
	    match,
	    matches = [];
	while (match = pattern.exec(string)) {
	    matches.push(match[1]);
	}
	return matches;
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
		template : '',
		link : function (scope, elem, attrs){

			////////// PARSE INPUT FIELDS //////////

			// OBJECT : Define the object which has with default values
			if (attrs.object)
				object = attrs.object;
			else
				object = 'edit_fields'; // Default object name : window['edit_fields']

			// FIELD : Define the field which is being edited
			field = attrs.editField;

			///// TEXT INPUTS //////
			if ( isInArray('input-', attrs.input) ){
				input_text_fields = ['text','password','hidden','url'];
				input_extension = attrs.input.replace("input-", ""); // strip "input-"

				if ( isInArray(input_extension, input_text_fields) ){

					// Placeholder
					if(attrs.placeholder)
						placeholder = attrs.placeholder;
					else
						placeholder = '';
					
					// Set Original Value : oValue
					if( attrs.value )
						oValue = attrs.value;
					else if ( window[object] )
						oValue = window[object][field];
					else
						oValue = '';

					// Generate HTML
					input_html = "<input type='" + input_extension + "' name='" + attrs.editField + "' id='" + attrs.editField + "' value='"+ oValue +"' placeholder='"+placeholder+"'>";
					input_element = angular.element( input_html );
					elem.append( input_element );
					//$compile( input_element )( scope );
				}
			}

			

			///// SELECT / MULTIPLE SELECT //////
			if ( isInArray( 'select', attrs.input ) ){


				// Check for "-multiple" extension
				var input_extension = attrs.input.replace("select-", ""); // strip "input-"
				if (input_extension == 'multiple'){
					var multiple = ' multiple ';
					
					// Split the value attribute into an Array
					if ( typeof attrs.value !== 'undefined' )
						var oValue = attrs.value.split(',');

				}
				else{
					var multiple = '';
					var oValue = attrs.value;
				}
				
				// Get the size of the select area
				if ( attrs.size )
					var size = " size='"+attrs.size+"' ";
				else
					var size = '';


				// Process Taxonomy Edit Field
				if ( isInArray( 'taxonomy', attrs.editField ) ){

					//current_taxonomy = window[object].taxonomy; // Current setting of current post
					//taxonomy_options = 

					//select_options = taxonomy_select_options( object.taxonomy );
					
					// PARSE SELECT ITEMS
					// Produces a series of HTML <options> from a given object
					function parse_select_items( items, selected, depth, child_key ){
						var select_items = '';

						// ROOT LEVEL ITEMS
						angular.forEach( items, function( item ){
							if ( isInArray( item.slug, selected ) )
								selected_attribute = ' selected ';
							else
								selected_attribute = '';
							select_items += "<option value='" + item.slug + "' "+selected_attribute+" >" + item.name + "</option>";

							// CHILD ITEMS
							if ( typeof item.terms !== 'undefined' && depth == 2 ){
								angular.forEach( item[child_key], function( item ){
									select_items += "<option value='" + item.slug + "' "+selected_attribute+" > - " + item.name + "</option>";
								});
							}

						});
						return select_items;
					}

					// Extract Selected Terms from Object > Slug to Flat Array
					function extract_selected_terms_from_object( terms ){
						var selected = [];
						angular.forEach( terms, function( term ){
							selected.push(term.slug);
						});
						return selected; // ["eco","life"]
					}

					// Get the name of the requested taxonomy
					var tax_name = extract_parentheses( attrs.editField );
					var terms = window['taxonomy'][tax_name];
					var post_terms = window[object].taxonomy[tax_name];
					var selected = extract_selected_terms_from_object( post_terms );
					var select_items = parse_select_items( terms, selected, 2, 'terms' ); // window[object].taxonomy[tax_name]

				}

				// Process Standard Edit Fields
				else{

					// Get Object from object the same name as the editField value
					var select_options = window[object];

					var select_items = "";
					// Loop through each item in the editField object
					angular.forEach( select_options, function(value, key){
						// Check to see if current key in oValue
						if( key == oValue || isInArray( key, oValue) )
							selected = 'selected';
						else
							selected = '';
						// Add option
						select_items += "<option value='"+key+"' "+selected+">"+value+"</option>";
					});

				}					



				// Parse the HTML
				var select_head = "<select id='"+attrs.editField+"' name='"+attrs.editField+"' " + multiple + " " + size + ">";
				var select_foot = "</select>";

				//if ( typeof select_items === 'undefined' ){
				//}

				var input_element = angular.element( select_head + select_items + select_foot );
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


