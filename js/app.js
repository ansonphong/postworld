'use strict';
var feed_settings = [];

var pwApp = angular.module('pwApp', ['ngResource','ngRoute', 'ngSanitize', 'infinite-scroll'])
    .config(function ($routeProvider, $locationProvider) {    	    	
        $routeProvider.when('/live-feed-1/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed1Widget.html',				
            });
        $routeProvider.when('/live-feed-2/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed2Widget.html',				
            });
        $routeProvider.when('/live-feed-3/',
            {
                template: '<h2>Coming Soon</h2>',				
            });
        $routeProvider.when('/load-feed-1/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadFeed1Widget.html',				
            });
        $routeProvider.when('/load-feed-2/',
            {
                template: '<h2>Coming Soon</h2>',				
            });
        $routeProvider.when('/load-panel/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadPanelWidget.html',				
            });
        $routeProvider.when('/register-feed/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwRegisterFeedWidget.html',				
            });
        $routeProvider.when('/home/',
            {
                templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed2Widget.html',				
            });
		// this will be also the default route, or when no route is selected
        $routeProvider.otherwise({redirectTo: '/home/'});
    });

// Submit on Enter, without a real form
pwApp.directive('ngEnter', function() {
        return function(scope, element, attrs) {
            element.bind("keydown keypress", function(event) {
                if(event.which === 13) {
                    scope.$apply(function(){
                        //scope.$eval(attrs.ngEnter);
                        scope.$eval("submit()");
                    });
                    event.preventDefault();
                }
            });
        };
    });
    
    

pwApp.run(function($rootScope, $templateCache, pwData) {	
    	// TODO move getting templates to app startup
    	pwData.pw_get_templates(null).then(function(value) {
		    // TODO should we create success/failure responses here?
		    // resolve pwData.templates
		    pwData.templates.resolve(value.data);
		    pwData.templatesFinal = value.data;
		    console.log('pwApp RUN getTemplates=',pwData.templatesFinal);
		  });    	
// TODO remove in production
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});
   


/*
 * Getting Organized (Michel):
 * 
 * Whole Components
 ******************
 * Create Advanced Search Panel [complete missing boxes]
 * Do we need Directives for non-post types?
 * Create Post Types Toggles in Search Panel Dynamically http://jsfiddle.net/BtrZH/5/
 * 
 * Create Edit Fields for Radio, checkbox, TinMCE (WP has an hook for it), Buttons
 * 	Add Validations
 * 	Add Dynamic Sub Forms [ng-switch]
 * 	Add Embedding of URLs [embed.ly?]
 * 	Will be used in URL like #/post/edit/id, #/post/new, etc...
 * 	Will Switch between forms dynamically
 * 
 * TODO List
 * *********
 * Create Startup code that runs at app startup, and put getting templates into it
 *  * 
 * Refactoring Needed
 * ******************
 * Use App Constants
 * 
 * Issues
 * ******
 * Button for Feed Templates need to be toggled and populated from Feed Settings
 * NONCE - not active yet
 * Feed_settings must have a template URL for feed []
 * Remove additional fields added to args and saved with register_feed()
 * Add Parameters to URL of the Live Feed / Search parameters - add that to our menu as an example
 * 
 * Enhancements
 * *************
 * Submitting on Field Change
 * Fix Bootstrap field alignment
 * Shouldnt we get all templates in pw_get_templates, and cache them to be used across the whole session? this will save many uneeded calls as long as we're on the same SPA session?
 * 
 * Testing
 * *******
 * 
 * UI Enhancements
 * ***************
 * Add Animation
 * Add Scrollbar like Facebook
 * Make Simple Search panel show number of results.
 * 
 * Questions
 * *********
 * How will the Search and Other Pages be presented? Templates? Pages? Widgets? other?
 * 
 * 
 */


//////////////////// CONSTRUCTION ZONE < (phongmedia) ////////////////////

////////// SIMPLE HELPERS ////////
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
        return true;
    else
        return false; //value[0].value ? true : false;  
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


////////// ADVANCED HELPERS ////////

///// PARSE HIERARCHICAL SELECT ITEMS : FUNCTION /////
// Produces a series of HTML <options> from a given hierarchical object
function parse_hierarchical_select_items( items, selected, id_key, label_key, child_key, depth, indent ){
    // SET DEFAULTS
    if ( isEmpty(id_key) ) var id_key = 0;
    if ( isEmpty(label_key) ) var label_key = 'name';
    if ( isEmpty(child_key) ) var child_key = 'children';
    if ( isEmpty(depth) ) var depth = 1;
    if ( isEmpty(indent) ) var indent = ' - ';
    
    var select_items = '';

    // ROOT LEVEL ITEMS
    angular.forEach( items, function( item ){
        var id = item[id_key];
        var label = item[label_key];
        if ( isInArray( id, selected ) )
            var selected_attribute = ' selected ';
        else
            var selected_attribute = '';
        select_items += "<option value='" + id + "' "+selected_attribute+" >" + label + "</option>";

        // CHILD ITEMS
        var child = item[child_key];
        if ( typeof child !== 'undefined' && depth == 2 ){
            angular.forEach( item[child_key], function( item ){
                var id = item[id_key];
                var label = item[label_key];
                select_items += "<option value='" + id + "' "+selected_attribute+" > " + indent + label+ "</option>";
            });
        }

    });
    return select_items;
}

///// PARSE LINEAR SELECT ITEMS : FUNCTION /////
// Produces a series of HTML <options> from a given flat object
function parse_linear_select_items( items, selected ){
    var select_items = '';
    // ROOT LEVEL ITEMS
    angular.forEach( items, function( value, key ){
        var id = key;
        var label = items[key];
        if ( isInArray( id, selected ) )
            var selected_attribute = ' selected ';
        else
            var selected_attribute = '';
        select_items += "<option value='" + id + "' "+selected_attribute+" >" + label + "</option>";
    });
    return select_items;
}



////////// EDIT FIELD DIRECTIVE //////////
pwApp.directive( 'editField', ['$compile', function($compile, $scope){

    return { 
        restrict: 'A',
        scope : function(){
            // Scope functions here
        },
        //template : '',
        link : function (scope, elem, attrs){

            ////////// PARSE INPUT FIELDS //////////

            // OBJECT : Define the object which has with default values
            
            if (attrs.object)
                var object = attrs.object;
            else
                var object = 'edit_fields'; // Default object name : window['edit_fields']
            
            // FIELD : Define the field which is being edited
            var field = attrs.editField;

            ////////// TEXT INPUTS //////////
            if ( isInArray('input-', attrs.input) ){
                var input_text_fields = ['text','password','hidden','url'];
                var input_extension = attrs.input.replace("input-", ""); // strip "input-"

                if ( isInArray(input_extension, input_text_fields) ){

                    ///// PLACEHOLDER /////
                    if(attrs.placeholder)
                        var placeholder = attrs.placeholder;
                    else
                        var placeholder = '';

                    // Set Original Value : oValue
                    if( attrs.value )
                        var oValue = attrs.value;
                    else if ( window[object] )
                        var oValue = window[object][field];
                    else
                        var oValue = '';

                    // Generate HTML
                    var input_html = "<input type='" + input_extension + "' name='" + attrs.editField + "' id='" + attrs.editField + "' class='" + attrs.editField + "' value='"+ oValue +"' placeholder='"+placeholder+"'>";
                    var input_element = angular.element( input_html );
                    elem.append( input_element );
                    //$compile( input_element )( scope );
                }
            }


            ////////// TEXT AREA //////////
            if ( isInArray('textarea', attrs.input) ){

                // Placeholder
                if(attrs.placeholder)
                    var placeholder = attrs.placeholder;
                else
                    var placeholder = 'Placeholder';

                // Wrap
                if(attrs.wrap)
                    var wrap = attrs.wrap;
                else
                    var wrap = '';

                // Set Original Value : oValue
                if( attrs.value )
                    var oValue = attrs.value;
                else if ( window[object] )
                    var oValue = window[object][field];
                else
                    var oValue = '';

                // Generate HTML
                var input_html = "<textarea name='" + attrs.editField + "' id='" + attrs.editField + "' class='" + attrs.editField + "' placeholder='"+placeholder+"' " + wrap + ">"+oValue+"</textarea>";
                var input_element = angular.element( input_html );
                elem.append( input_element );
                //$compile( input_element )( scope );
                
            }

            ////////// SELECT / MULTIPLE SELECT //////////
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
                    // If DATA-VALUE attribute is defined 
                    if ( !isEmpty(attrs.value) )
                        var oValue = attrs.value;
                    // Otherwise, use the value coorosponding to the key equal to the edit-field value
                    else
                        var oValue = window[object][attrs.editField];
                }
                
                // Get the size of the select area
                if ( attrs.size )
                    var size = " size='"+attrs.size+"' ";
                else
                    var size = '';

                ///// TAXONOMY OPTIONS /////
                // Process Taxonomy Edit Field
                if ( isInArray( 'taxonomy', attrs.editField ) ){
                    // Get the name of the requested taxonomy
                    var tax_name = extract_parentheses( attrs.editField );
                    var terms = window['taxonomy'][tax_name];
                    var selected = window[object].taxonomy[tax_name];
                    var select_items = parse_hierarchical_select_items( terms, selected, 'slug', 'name', 'terms', 2, '- ' ); // window[object].taxonomy[tax_name]
                }

                ///// PROVIDED OPTIONS /////
                // Use provided options
                else if(attrs.options){
                    var options = window[attrs.options];
                    var selected = oValue;
                    var select_items = parse_linear_select_items( options, selected );
                }

                ///// DEFAULT OPTIONS /////
                // Process Standard Edit Fields
                else{
                    var select_options = window[object];
                    var selected = oValue;
                    var select_items = parse_linear_select_items( select_options[field], selected );
                }                   

                // Parse the HTML
                var select_head = "<select id='"+attrs.editField+"' name='"+attrs.editField+"' " + multiple + " " + size + " >";
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


////////// EDIT POST CONTROLLER //////////
function editPost($scope) {

    // POST DATA OBJECT
    $scope.post_data = {
        post_title : "Hello Space",
        post_name : "hello_space",
        post_type : "feature",
        post_status : "publish",
        tax_input : {
            topic : ["life"],
            section : ["psi"],
        },
        tags_input : "tag1, tag2, tag3",
    };

    // POST TYPE OPTIONS
    $scope.post_types_linear = {
        feature : "Features",
        blog : "Blog",
        link : "Links",
        announcement : "Announcements",
        tribe_events : "Events"
    };

    // TAXONOMY TERMS
    $scope.tax_terms = {
        'topic' : [
            {
            slug:"psyche",
            name:"/psyche",
            },
            {
            slug:"arts",
            name:"/Arts",
            },
            {
            slug:"life",
            name:"/life",
            },
            {
            slug:"eco",
            name:"/eco",
            },
            {
            slug:"tech",
            name:"/tech",
            },
            {
            slug:"commons",
            name:"/commons",
            },
        ],
        'section' : [
            {
            slug:"psychedelic",
            name:"Psychedelic Culture",
            },
            {
            slug:"psi",
            name:"Psi Frontiers",
            },
            {
            slug:"video",
            name:"Videos",
            },
            {
            slug:"edm",
            name:"Evolver EDM",
            },
        ],
        
    };

    // POST STATUS OPTIONS
    $scope.post_status_options = {
        publish : "Published",
        draft : "Draft",
        pending : "Pending",
    };

    // SAVE POST FUNCTION
    $scope.savePost = function(){
        alert( JSON.stringify( $scope.post_data ) );
    }

    // DEV
    $scope.post_types = [
        {
            slug:"feature",
            name:"Feature",
            access:true
        },
        {
            slug:"blog",
            name:"Blog",
            access:true
        },
        {
            slug:"link",
            name:"Link",
            access:true
        },
        {
            slug:"announcement",
            name:"Announcements",
            access:false
        },
        {
            slug:"tribe_events",
            name:"Events",
            access:false
        }
    ];

    $scope.post_type = $scope.post_types[2];



}




























