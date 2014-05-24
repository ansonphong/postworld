'use strict';


postworld.factory('pw',
	['$resource','$q','$log','$window',
	function ($resource, $q, $log, $window) {   

	// DECLARATIONS
	return {
		pluginUrl: function(value){
			if( !_.isUndefined(value) )
				value = "/postworld/" + value;
			else
				value = "/postworld/";

			return $window.pwSiteGlobals.wordpress.plugins_url + value;
		},
		loadScript: function( url, callback ){
            // Adding the script tag to the head as suggested before
            var head = document.getElementsByTagName('head')[0];
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = url;

            // Then bind the event to the callback function.
            // There are several events for cross browser compatibility.
            script.onreadystatechange = callback;
            script.onload = callback;

            // Fire the loading
            head.appendChild(script);
        },
	};

}]);


/*                      _                                  
		_   _ _ __   __| | ___ _ __ ___  ___ ___  _ __ ___ 
	   | | | | '_ \ / _` |/ _ \ '__/ __|/ __/ _ \| '__/ _ \
	   | |_| | | | | (_| |  __/ |  \__ \ (_| (_) | | |  __/
  ____(_)__,_|_| |_|\__,_|\___|_|  |___/\___\___/|_|  \___|
 |_____|                                                   
 
 //////////////////////////////////////////////////////////*/

postworld.factory('_',
	['$resource','$q','$log','$window',
	function ($resource, $q, $log, $window) {   
	// DECLARATIONS

	return {
		exists: function(value){
			if ( typeof value === 'undefined' )
				return false;
			else
				return true;
		},
		extract_parentheses: function(string){
			var pattern = /\((.+?)\)/g,
				match,
				matches = [];
			while (match = pattern.exec(string)) {
				matches.push(match[1]);
			}
			return matches;
		},
		isNumber: function(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		},
		isInArray: function(value, array) {
			if (array)
				return array.indexOf(value) > -1 ? true : false;
			else
				return false;
		},
		isEmpty: function(value){
			if ( typeof value === 'undefined' || value == '' )
				return true;
			else
				return false; //value[0].value ? true : false;  
		},
		isEmptyObj: function(obj){
				for(var prop in obj) {
					if(obj.hasOwnProperty(prop))
						return false;
				}
				return true;
		},
		mergeRecursiveObj: function(obj1, obj2) {
		  for (var p in obj2) {
			try {
			  // Property in destination object set; update its value.
			  if ( obj2[p].constructor==Object ) {
				obj1[p] = MergeRecursive(obj1[p], obj2[p]);
			  } else {
				obj1[p] = obj2[p];
			  }
			} catch(e) {
			  // Property in destination object not set; create it and set its value.
			  obj1[p] = obj2[p];
			}
		  }
		  return obj1;
		},
		stringToBoolean : function(string){
			switch(string.toLowerCase()){
				case "true": case "yes": case "1": return true;
				case "false": case "no": case "0": case null: return false;
				default: return Boolean(string);
			}
		},
		objExists : function(obj, prop){
			var parts = prop.split('.');
			for(var i = 0, l = parts.length; i < l; i++) {
				var part = parts[i];
				if(obj !== null && typeof obj === "object" && part in obj) {
					obj = obj[part];
				}
				else {
					return false;
				}
			}
			return true;
			
		},
		getObj: function( obj, subKey ){
            // Returns a sub-object
            // SYNTAX : subKey = 'object.subkey.subsubkey'

            if( _.isUndefined( obj ) )
                return false;
            
            ///// MINE OBJECT /////
            var parts = subKey.split('.');
            for(var i = 0, l = parts.length; i < l; i++) {
                var part = parts[i];
                if(obj !== null && typeof obj === "object" && part in obj) {
                    obj = obj[part];
                }
                else {
                    return false;
                }
            }

            // Return findWhere
            return obj;
            
        },
		setObj : function( obj, key, value  ){
			// Sets the value of an object,
			// even if it or it's parent(s) doesn't exist.
			/*  PARAMETERS:
				obj     =   [object]
				key     =   [string] ie. ( "key.subkey.subsubkey" )
				value   =   [string/array/object]
			*/

			///// KEY PARTS /////
			// FROM : "key.subkey.sub.subkey"
			// TO   : [ "key", "subkey", "subkey" ]
			var key_parts = key.split('.');
			key_parts = key_parts.reverse();

			// Count how many parts
			var key_parts_count = key_parts.length;

			// Prepare to catch finished key parts
			var key_parts_done = [];

			// Iterate through each key part
			var seed = [];
			var i = 0;

			angular.forEach( key_parts, function( key_part ){
				i++;

				// First Key
				if( i == 1 ){
					// Create seed with first key
					seed[i] = {};
					seed[i][key_part] = value;
				// Other Keys
				} else{
					// Nest previous seed in current key
					seed[i] = {};
					seed[i][key_part] = seed[(i-1)];
				}
				// Last Key
				if( i == key_parts_count ){
					// Return final seed result
					seed = seed[i];
				}

			});

			$log.debug( "SEED : ", seed);
			$log.debug( "OBJ : ", obj);

			// Merge $seed array with input $array
			obj = deepmerge( obj, seed );

			return obj;

		}
	};

}]);



////// DEPRECIATED /////

/* _                  _   
  | |   _    _____  _| |_ 
 / __) (_)  / _ \ \/ / __|
 \__ \  _  |  __/>  <| |_ 
 (   / (_)  \___/_/\_\\__|
  |_|                     
////////// JAVASCRIPT EXTENTION SERVICE //////////*/ 

postworld.service('ext', ['$log', function ($log) {
	// SIMPLE JS FUNCTION HELPERS
	// Extends the function vocabulary of JS

	return {
		exists: function(value){
			if ( typeof value === 'undefined' )
				return false;
			else
				return true;
		},
		extract_parentheses: function(string){
			var pattern = /\((.+?)\)/g,
				match,
				matches = [];
			while (match = pattern.exec(string)) {
				matches.push(match[1]);
			}
			return matches;
		},
		isNumber: function(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		},
		isInArray: function(value, array) {
			if (array)
				return array.indexOf(value) > -1 ? true : false;
			else
				return false;
		},
		isEmpty: function(value){
			if ( typeof value === 'undefined' || value == '' )
				return true;
			else
				return false; //value[0].value ? true : false;  
		},
		isEmptyObj: function(obj){
				for(var prop in obj) {
					if(obj.hasOwnProperty(prop))
						return false;
				}
				return true;
		},
		mergeRecursiveObj: function(obj1, obj2) {
		  for (var p in obj2) {
			try {
			  // Property in destination object set; update its value.
			  if ( obj2[p].constructor==Object ) {
				obj1[p] = MergeRecursive(obj1[p], obj2[p]);
			  } else {
				obj1[p] = obj2[p];
			  }
			} catch(e) {
			  // Property in destination object not set; create it and set its value.
			  obj1[p] = obj2[p];
			}
		  }
		  return obj1;
		},
		stringToBoolean : function(string){
			switch(string.toLowerCase()){
				case "true": case "yes": case "1": return true;
				case "false": case "no": case "0": case null: return false;
				default: return Boolean(string);
			}
		},
		objExists : function(obj, prop){
			var parts = prop.split('.');
			for(var i = 0, l = parts.length; i < l; i++) {
				
				var part = parts[i];
				if(obj !== null && typeof obj === "object" && part in obj) {
					obj = obj[part];
				}
				else {
					return false;
				}
				
			}
			return true;
			
		},

	}
}]);


/*
   _        ____           _      ___        _   _                 
  | |   _  |  _ \ ___  ___| |_   / _ \ _ __ | |_(_) ___  _ __  ___ 
 / __) (_) | |_) / _ \/ __| __| | | | | '_ \| __| |/ _ \| '_ \/ __|
 \__ \  _  |  __/ (_) \__ \ |_  | |_| | |_) | |_| | (_) | | | \__ \
 (   / (_) |_|   \___/|___/\__|  \___/| .__/ \__|_|\___/|_| |_|___/
  |_|                                 |_|                          

////////// ------------ EDIT POST OPTIONS SERVICE ------------ //////////*/  
postworld.service('pwPostOptions', ['$window','$log', 'pwData',
							function ($window, $log, $pwData) {

	return{
		getTaxTerms: function($scope, tax_obj){ // , tax_obj

			if ( typeof tax_obj === 'undefined' )
				var tax_obj = "tax_terms";

			var args = $window.pwSiteGlobals.post_options.taxonomy_outline;
			$pwData.taxonomies_outline_mixed( args ).then(
				// Success
				function(response) {    
					$scope[tax_obj] = response.data;
				},
				// Failure
				function(response) {
					//alert('Error loading terms.');
				}
			);
		},

		pwGetPostTypeOptions: function( mode ){
			// MODE OPTIONS
			// read / edit / edit_others / publish / create / edit_published / edit_private
			// IF READ MODE : Return all public post types
			if( mode == 'read' ){
				return $window.pwGlobals.post_types;
			}

			// IF EDIT/OTHER MODE : Compare post types against their capabilities
			// Cycle through provided post_types
			// Which post_types does the user have access to 'mode' operation?
			var userPostTypeOptions = {};
			if( $window.pwGlobals.current_user != 0 ){
				angular.forEach( $window.pwGlobals.post_types , function( name, slug ){
					var cap_type = mode + "_"+ slug + "s";
					if( $window.pwGlobals.current_user.allcaps[cap_type] == true ){
						userPostTypeOptions[slug] = name;
					}
				});
			}
			return userPostTypeOptions;
		},

		pwGetPostStatusOptions: function( post_type ){
			if ((!$window.pwGlobals.current_user) || (!$window.pwGlobals.current_user))
				return;

			// GET ROLE
			var current_user_role = $window.pwGlobals.current_user.roles[0];
			// DEFINE : POST STATUS OPTIONS
			var post_status_options = $window.pwSiteGlobals.post_options.post_status;
			// DEFINE : POST STATUS OPTIONS PER ROLE BY POST TYPE
			var role_post_type_status_access = $window.pwSiteGlobals.post_options.role_post_type_status_access;
			// BUILD OPTIONS MENU OBJECT
			var post_status_menu = {};
			var user_role_options = role_post_type_status_access[current_user_role][post_type];
			if( typeof user_role_options !== 'undefined' ){
				angular.forEach(  user_role_options, function( post_status_slug ){
					post_status_menu[post_status_slug] = post_status_options[post_status_slug];
				});
				return post_status_menu;
			} else{
				// DEFAULT
				return post_status_options;
			}
		},
		
		pwGetPostClassOptions: function(){
			return $window.pwSiteGlobals.post_options.post_class;
		},

		pwGetLinkFormatOptions: function(){
			return $window.pwSiteGlobals.post_options.link_format;
		},

		pwGetLinkFormatMeta: function(){
			return $window.pwSiteGlobals.post_options.link_format_meta;
		},

		pwGetPostYearOptions: function(){
			return $window.pwSiteGlobals.post_options.year;
		},

		pwGetPostMonthOptions: function(){
			var months_obj = $window.pwSiteGlobals.post_options.month;
			var months_array = [];
			// Convert from "1:January" Associative Object format to { number:1, name:"January" } 
			angular.forEach( months_obj, function( value, key ){
				var month = {
					'number' : parseInt(key),
					'name' : value
				};
				months_array.push( month );
			});
			return months_array;
		},

		pwGetTaxInputModel: function(){
			// TAXONOMY OBJECT MODEL
			// Makes empty array in the taxInput object for each taxonomy inputs
			var taxonomies = $window.pwSiteGlobals.post_options.taxonomies;
			var taxInput = {};
			angular.forEach( taxonomies, function( value ){
				taxInput[value] = [];
			});
			return taxInput;
		},

		pw_site_options: function( $scope ){
			
			var args = "";
			$pwData.pw_site_options( args ).then(
				// Success
				function(response) {    
					//alert( JSON.stringify(response.data.months) );
					return response.data;
					//alert(JSON.stringify(response.data));
				},
				// Failure
				function(response) {
					//alert('Error loading terms.');
				}
			);

		},

	}
}]);


/* _        ____       _         _                         
  | |   _  |  _ \ ___ | | ___   / \   ___ ___ ___  ___ ___ 
 / __) (_) | |_) / _ \| |/ _ \ / _ \ / __/ __/ _ \/ __/ __|
 \__ \  _  |  _ < (_) | |  __// ___ \ (_| (_|  __/\__ \__ \
 (   / (_) |_| \_\___/|_|\___/_/   \_\___\___\___||___/___/
  |_|                                                      

/*///////// ------- SERVICE : PW USERS ------- /////////*/  
postworld.service('pwRoleAccess', ['$log', '$window', 'ext', function ($log, $window, $ext) {
	return{
		setRoleAccess : function($scope){
			$scope.current_user = $window.pwGlobals.current_user;

			( $scope.current_user != 0 ) ?
				$scope.current_user_role = $window.pwGlobals.current_user.roles[0] :
				$scope.current_user_role = 'guest' ;

			$scope.roles = {};
			$scope.role_map = $window.pwSiteGlobals.role.map;

			// ESTABLISH ROLE ACCESS
			// Is the user an editor?
			( $ext.isInArray( $scope.current_user_role, $scope.role_map.editor ) ) ?
				$scope.roles.editor = true : $scope.roles.editor = false;

			// Is the user an author?
			( $ext.isInArray( $scope.current_user_role, $scope.role_map.author ) ) ?
				$scope.roles.author = true : $scope.roles.author = false;

			// Is the user an contributor?
			( $ext.isInArray( $scope.current_user_role, $scope.role_map.contributor ) ) ?
				$scope.roles.contributor = true : $scope.roles.contributor = false ;

		},
	}
}]);





/*
   _        _____    _ _ _     ____           _     _____ _ _ _                
  | |   _  | ____|__| (_) |_  |  _ \ ___  ___| |_  |  ___(_) | |_ ___ _ __ ___ 
 / __) (_) |  _| / _` | | __| | |_) / _ \/ __| __| | |_  | | | __/ _ \ '__/ __|
 \__ \  _  | |__| (_| | | |_  |  __/ (_) \__ \ |_  |  _| | | | ||  __/ |  \__ \
 (   / (_) |_____\__,_|_|\__| |_|   \___/|___/\__| |_|   |_|_|\__\___|_|  |___/
  |_|                                                                          
////////// ------------ EDIT POST FILTERS SERVICE ------------ //////////*/  
postworld.service('pwEditPostFilters',
	['$log', 'ext', '$window', 'pwPostOptions',
	function ($log, ext, $window, $pwPostOptions ) {

	return {
		parseKnownJsonFields: function( post ){

			if ( !_.isUndefined( post['post_meta'] ) ){

				// Deserialize Known JSON Fields
				var knownJsonFields = [
					'geocode',
					'location_obj',
					'related_post'
					];
				angular.forEach( post.post_meta , function(value, key){
					if( ext.isInArray( key, knownJsonFields ) )
						post.post_meta[key] = angular.fromJson(value);
				});

			}
			return post;

		},
		sortTaxTermsInput: function(post, tax_terms, sub_object){
			// SORT TAXONOMIES
			// FOR EACH SELECTED TAXONOMY TERM SET
			// The order of the taxonomy terms & sub-terms control is sensitive to order in the terms array
			// tax_input[taxonomy][0/1] : [0] is the primary term, and [1] is a sub/child-term of that term in the database
			// So In case the taxonomy terms were not returned in the right order
			// Confirm that the order is ['parent', 'child'], otherwise re-arrange based on the supplied tax_terms hierarchy
			// So that the taxonomy[0] is the primary term and taxonomy[1] is the sub/child-term
			// ie. If it's returned : { topic : ["healing","body"] }
			//     and "healing" term is in fact a child of "body" term,
			//     then re-arrange to { topic : ["body","healing"] }
			
			// Return the post if missing input
			if( _.isUndefined(post) ||
				_.isUndefined(tax_terms) )
				return post;

			angular.forEach( post[sub_object], function( selected_terms, taxonomy ){
				if ( selected_terms.length > 1 ){
					// FOR EACH TAXONOMY TERM OPTION
					// Go through each top level term for taxonomy in tax_terms
					// If it equals the first value of terms, leave it as is
					// If it isn't found, then swap order
					var reorder = true;
					angular.forEach( tax_terms[taxonomy], function( term_option ){
						// Compare each term option to the selected terms
						// If they're the same, do not reorder
						if ( term_option.slug == selected_terms[0] ){
							// If the term is the first term
							reorder = false;
						}
					});

					if ( reorder == true ){
						post[sub_object][taxonomy].reverse();
					}
					
				}
			});
			return post;
		},

		///// EVALUATE AND SET link_format DEPENDING ON LINK_URL /////
		evalPostFormat: function( link_url, link_format_meta ){

			// If Custom Link Format Meta isn't supplied, get the default
			if ( _.isUndefined( link_format_meta ) )
				link_format_meta = $pwPostOptions.pwGetLinkFormatMeta();

			// Set the default format
			var default_format = $window.pwSiteGlobals.post_options.link_format_defaults.none;
			var set = "";

			// If link_url has a value
			if ( !ext.isEmpty( link_url ) && !ext.isEmpty( link_format_meta ) ){
				///// FOR EACH POST FORMAT : Go through each post format
				angular.forEach( link_format_meta, function( link_format ){
					///// FOR EACH DOMAIN : Go through each domain
					angular.forEach( link_format.domains, function( domain ){
					// If domain exists in the link_url, set that format
						if ( ext.isInArray( domain, link_url ) ){
							set = link_format.slug;
						}
					});
				});
				// If no matches, set default
				if ( set == "" )
					return $window.pwSiteGlobals.post_options.link_format_defaults.link;
				else
					return set;
			}
			// Otherwise, set default
			else {
				return default_format;
			}
		},
		selected_tax_terms: function(tax_terms, tax_input){
			///// SELECTED TAXONOMY TERMS /////
			// • Extracts an object with singular sub-term data
			//   so that they can be referred to to define subtopics

			// Return false if missing input
			if( _.isUndefined(tax_terms) ||
				_.isUndefined(tax_input) )
				return false;

			// EACH TAXONOMY : Cycle through each taxonomy
			var selected_tax_terms = {};
			angular.forEach( tax_terms, function( terms, taxonomy ){
				// Setup Object
				if ( ext.isEmpty( tax_input[taxonomy] ) )
					tax_input[taxonomy] = [];
				// SET TERM : Cycle through each term
				// Set the selected taxonomy terms object
				angular.forEach( terms, function( term ){
					// If the term is selected, add it to the selected object
					if ( term.slug == tax_input[taxonomy][0] ){
						selected_tax_terms[taxonomy] = term;
					}
				});
			});// END FOREACH
			return selected_tax_terms;
		},
		clear_sub_terms: function( tax_terms, tax_input, selected_tax_terms ){
			// • Manages the values of tax_input by
			//   clearing irrelivant sub-term selections

			// EACH TAXONOMY : Cycle through each taxonomy
			angular.forEach( tax_terms, function( terms, taxonomy ){
				///// CLEAR SUBTERM /////
				// If there is a sub-term defined and it has children
				// Check to see if that child term exists in the main term
				// The set term object of the current taxonomy
				var term_set = selected_tax_terms[taxonomy];

				// Does the currently selected term of this taxonomy have children
				if ( typeof term_set !== 'undefined' ){
					if ( typeof term_set.terms !== 'undefined' )
						var term_has_children = true;
					else
						var term_has_children = false; 
				}
				else
					var term_has_children = false;
				// Is the child term set for this taxonomy in tax_input?
				var child_term_is_set = !ext.isEmpty( tax_input[taxonomy][1] );
				if ( term_has_children ){
					// Default
					var is_subterm = false;
					// Cycle through current sub-terms, and see if it exists
					angular.forEach( term_set.terms, function( child_term ){
						if ( child_term.slug == tax_input[taxonomy][1] )
							is_subterm = true;
					});
					// If it doesn't exist as a sub-term, clear it
					if ( is_subterm == false )
						tax_input[taxonomy].splice(1,1);
				}
				// Otherwise clear it
				else if ( child_term_is_set )
					tax_input[taxonomy].splice(1,1);
			});// END FOREACH
			return tax_input;
		},
		stripslashes: function( str ){
			return (str + '').replace(/\\(.?)/g, function (s, n1) {
			switch (n1) {
			case '\\':
			  return '\\';
			case '0':
			  return '\u0000';
			case '':
			  return '';
			default:
			  return n1;
			}
		  });
		},
	};
}]);




/*              ____        _         ____                  _               
  _ ____      _|  _ \  __ _| |_ ___  / ___|  ___ _ ____   _(_) ___ ___  ___ 
 | '_ \ \ /\ / / | | |/ _` | __/ _ \ \___ \ / _ \ '__\ \ / / |/ __/ _ \/ __|
 | |_) \ V  V /| |_| | (_| | ||  __/  ___) |  __/ |   \ V /| | (_|  __/\__ \
 | .__/ \_/\_/ |____/ \__,_|\__\___| |____/ \___|_|    \_/ |_|\___\___||___/
 |_|                                                                        
//////////// ------------ POSTWORLD DATE SERVICES ------------ ////////////*/  


postworld.service('pwDate', [ '$log', 'ext', '$window', function ($log, ext, $window) {
	return {

		setDateRange: function( set, dateObj, offset ){
			// set = 'min' / 'max'
			// dateObj = a JS Date Object
			// offset = how much to offset in milliseconds from given time

			// USAGE
			// setDateRange();                              // Resets both to null
			// setDateRange( 'min' );                       // Sets min to current time
			// setDateRange( 'max' );                       // Sets max to null
			// setDateRange( 'min', dateObj );              // Offsets the dateObj to the minDate
			// setDateRange( 'max', dateObj, '100000' );    // Offsets the dateObj by 100000 milliseconds and sets the maxDate
			// setDateRange( 'max', 'now', 'oneDay' );    // Offsets the dateObj by 100000 milliseconds and sets the maxDate

			///// DEFAULT SETTINGS ////
			if( _.isUndefined(set) ){
				// Defaults
				//$scope.minDate = null;
				//$scope.maxDate = null;
				return false;
			}

			///// DATE OBJ OPTIONS /////
			if( dateObj == 'now' )
				dateObj = new Date();

			///// OFFSET OPTIONS /////
			if( offset == 'oneDay' )
				offset = parseInt(86400000);
			
			///// MINIMUM SETTINGS /////
			if( set == 'min'  ){
				// If dateObj is undefined, set as current time
				if( _.isUndefined( dateObj ) ){
					//$scope.minDate = new Date();
					var minDate = new Date();
					return minDate;
				}

				// If Date Object is defined but not offset
				// Set the setting to the given time
				if( _.isUndefined( offset ) ){
					//$scope.minDate = new Date(dateObj);
					var minDate = new Date(dateObj);
					return minDate;
				}

				// If a offset number is given
				// Subtract that many milliseconds
				if( _.isNumber(offset) ){
					var localDateObj = new Date(dateObj);
					var parsedDateObj = Date.parse(localDateObj);
					var newTime = parsedDateObj - offset;
					var setTime = new Date(newTime);
					//$scope.minDate = setTime;
					return setTime;
				}
				return false;
				
			}


			///// MAXIMUM SETTINGS /////
			if( set == 'max'  ){
				// If dateObj is undefined, set as null
				if( _.isUndefined( dateObj ) ){
					//$scope.maxDate = null;
					return false;
				}

				// If Date Object is defined but not offset
				// Set the setting to the given time
				if( _.isUndefined( offset ) ){
					//$scope.maxDate = new Date(dateObj);
					var maxDate = new Date(dateObj);
					return maxDate;
				}

				// If a offset number is given
				// Add that many milliseconds
				offset = parseInt(offset);
				if( _.isNumber(offset) ){
					var localDateObj = new Date(dateObj);
					var parsedDateObj = Date.parse(localDateObj);
					var newTime = parsedDateObj + offset;
					var setTime = new Date(newTime);
					//$scope.maxDate = setTime;
					return setTime;
				}
				return false;
				
			}
			

			//if(   _.isObject($scope.post.post_meta.related_post) )
			//  alert('object');

		},



	};
}]);
