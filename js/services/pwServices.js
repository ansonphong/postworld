'use strict';

/*
   _                  _   
  | |   _    _____  _| |_ 
 / __) (_)  / _ \ \/ / __|
 \__ \  _  |  __/>  <| |_ 
 (   / (_)  \___/_/\_\\__|
  |_|                     
////////// ------------ JAVASCRIPT EXTENTION SERVICE ------------ //////////*/ 
postworld.service('ext', ['$log', function ($log) {
    // SIMPLE JS FUNCTION HELPERS
    // Extends the function vocabulary of JS

    return{
        varExists: function(value){
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
            angular.forEach( $window.pwGlobals.post_types , function( name, slug ){
                var cap_type = mode + "_"+ slug + "s";
                if( $window.pwGlobals.current_user.allcaps[cap_type] == true ){
                    userPostTypeOptions[slug] = name;
                }
            });
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
            return $window.pwSiteGlobals.post_options.post_format;
        },

        pwGetPostFormatMeta: function(){
            return $window.pwSiteGlobals.post_options.post_format_meta;
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




/*
   _        _____    _ _ _     ____           _     _____ _ _ _                
  | |   _  | ____|__| (_) |_  |  _ \ ___  ___| |_  |  ___(_) | |_ ___ _ __ ___ 
 / __) (_) |  _| / _` | | __| | |_) / _ \/ __| __| | |_  | | | __/ _ \ '__/ __|
 \__ \  _  | |__| (_| | | |_  |  __/ (_) \__ \ |_  |  _| | | | ||  __/ |  \__ \
 (   / (_) |_____\__,_|_|\__| |_|   \___/|___/\__| |_|   |_|_|\__\___|_|  |___/
  |_|                                                                          
////////// ------------ EDIT POST FILTERS SERVICE ------------ //////////*/  
postworld.service('pwEditPostFilters', ['$log', 'ext', function ($log, ext) {
        return {
            sortTaxTermsInput: function(post_data, tax_terms, sub_object){
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
                
                angular.forEach( post_data[sub_object], function( selected_terms, taxonomy ){
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
                            post_data[sub_object][taxonomy].reverse();
                        }
                    }
                });
                return post_data;
            },
            ///// EVALUATE AND SET link_format DEPENDING ON LINK_URL /////
            evalPostFormat: function( link_url, link_format_meta ){
                var default_format = "standard";
                var set = "";
                //alert(link_format_meta);
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
                        return "link";
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

