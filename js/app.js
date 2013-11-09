/*
  ____           _                      _     _ 
 |  _ \ ___  ___| |___      _____  _ __| | __| |
 | |_) / _ \/ __| __\ \ /\ / / _ \| '__| |/ _` |
 |  __/ (_) \__ \ |_ \ V  V / (_) | |  | | (_| |
 |_|   \___/|___/\__| \_/\_/ \___/|_|  |_|\__,_|
                                                
Developed by : Innuva & Phong Media
Framework by : AngularJS
GitHub Repo  : https://github.com/phongmedia/postworld/
ASCII Art by : http://patorjk.com/software/taag/#p=display&f=Standard

"AS SEEN ON REALITY SANDWICH!"

*/

'use strict';
var feed_settings = [];


var postworld = angular.module('postworld', ['ngResource','ngRoute', 'ngSanitize', 'infinite-scroll', 'ui.bootstrap', 'monospaced.elastic','TimeAgoFilter','TruncateFilter','UserValidation' ])
.config(function ($routeProvider, $locationProvider, $provide) {   

    ////////// ROUTE PROVIDERS //////////
    $routeProvider.when('/live-feed-1/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed1Widget.html',                
        });
    $routeProvider.when('/live-feed-2/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed2Widget.html',                
        });
    $routeProvider.when('/live-feed-2-feeds/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed3Widget.html',                
        });
    $routeProvider.when('/live-feed-with-ads/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed6Widget.html',                
        });
    $routeProvider.when('/live-feed-2-feeds-auto/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed4Widget.html',                
        });
    $routeProvider.when('/live-feed-params/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed5Widget.html',                
        });
    $routeProvider.when('/load-feed-1/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadFeed1Widget.html',                
        });
    $routeProvider.when('/load-feed-2/',
        {
            template: '<h2>Coming Soon</h2>',               
        });
    $routeProvider.when('/load-feed-2-feeds/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadFeed3Widget.html',                
        });
    $routeProvider.when('/load-feed-cached-outline/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadFeed4Widget.html',                
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
                template: '<h2>Coming Soon</h2>',               
        });
    $routeProvider.when('/edit-post/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/editPost.html',                
        });

    $routeProvider.when('/load-comments/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadCommentsWidget.html',             
        });            
    $routeProvider.when('/embedly/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwEmbedlyWidget.html',             
        });            
    $routeProvider.when('/load-post/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadPostWidget.html',             
        });            
    $routeProvider.when('/o-embed/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwEmbedWidget.html',             
        });       
    $routeProvider.when('/media-modal/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/mediaModal.html',             
        });  
    $routeProvider.when('/post-link/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/postLink.html',             
        });  
    $routeProvider.when('/test-post/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwTestWidget.html',             
        });  


    $routeProvider.when('/new/:post_type',
        {
            action: "new_post",
        });

    $routeProvider.when('/edit/:post_id',
        {
            action: "edit_post",
        });

    $routeProvider.when('/home/',
        {
            action: "default",
        });


    // this will be also the default route, or when no route is selected
    $routeProvider.otherwise({redirectTo: '/home/'});

});


 
/*
  ____              
 |  _ \ _   _ _ __  
 | |_) | | | | '_ \ 
 |  _ <| |_| | | | |
 |_| \_\\__,_|_| |_|
                    
*/
postworld.run(function($rootScope, $templateCache, $log, pwData) {    
        // TODO move getting templates to app startup
        pwData.pw_get_templates(null).then(function(value) {
            // TODO should we create success/failure responses here?
            // resolve pwData.templates
            pwData.templates.resolve(value.data);
            pwData.templatesFinal = value.data;
            console.log('postworld RUN getTemplates=',pwData.templatesFinal);
          });    

    // TODO remove in production
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });

   // 
   $rootScope.current_user = window['current_user'];
   $log.info('Current user: ', $rootScope.current_user );

});
   


/*
 * Getting Organized (Michel):
 * 
 * Whole Components
 ******************
 *  * 
 * Refactoring Needed
 * ******************
 * Use App Constants
 * 
 * Issues
 * ******
 * NONCE - not active yet
 * 
 * Enhancements
 * *************
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
/*
window.isInArray =  function(value, array) {
    if (array)
        return array.indexOf(value) > -1 ? true : false;
    else
        return false;
}

window.isEmpty = function(value){
    if ( typeof value === 'undefined' || value == '' )
        return true;
    else
        return false; //value[0].value ? true : false;  
}
*/



/*
   _        ____  _ _          ___        _   _                 
  | |   _  / ___|(_) |_ ___   / _ \ _ __ | |_(_) ___  _ __  ___ 
 / __) (_) \___ \| | __/ _ \ | | | | '_ \| __| |/ _ \| '_ \/ __|
 \__ \  _   ___) | | ||  __/ | |_| | |_) | |_| | (_) | | | \__ \
 (   / (_) |____/|_|\__\___|  \___/| .__/ \__|_|\___/|_| |_|___/
  |_|                              |_|                          
////////// ------------ CUSTOM SITE CONFIGURATIONS ------------ //////////*/
// This should be broken off into another seperate file and included seperately
// In the website theme folder, as it contains custom options for Postworld Configuration

postworld.service('siteOptions', ['$log', function ($log) {
    // Do one AJAX call here which returns all the options
    return{
        taxOutlineMixed : function(){
            return {
               "topic":{
                  "max_depth":2,
                  "fields":[
                     "term_id",
                     "name",
                     "slug"
                  ],
                  "filter":""
               },
               "section":{
                  "max_depth":1,
                  "fields":[
                     "term_id",
                     "name",
                     "slug"
                  ],
                  "filter":""
               },
               "type":{
                  "max_depth":2,
                  "fields":[
                     "term_id",
                     "name",
                     "slug"
                  ],
                  "filter":"label_group"
               }
            }
        },
    }

}]);







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
postworld.service('pwPostOptions', ['$log', 'siteOptions', 'pwData',
                            function ($log, $siteOptions, $pwData) {
    // Do one AJAX call here which returns all the options
    return{
        getTaxTerms: function($scope){
            var args = $siteOptions.taxOutlineMixed();
            $pwData.taxonomies_outline_mixed( args ).then(
                // Success
                function(response) {    
                    $scope.tax_terms = response.data;
                    //alert(JSON.stringify(response.data));
                },
                // Failure
                function(response) {
                    //alert('Error loading terms.');
                }
            );
        },
        pwGetPostTypeOptions: function(){
        return {
            "feature" : "Feature",
            "blog" : "Blog",
            "link" : "Link",
            "announcement" : "Announcement",
            "event" : "Event"
            };
        },
        pwGetPostStatusOptions: function(){
            return {
                publish : "Published",
                draft : "Draft",
                pending : "Pending",
            };
        },
        pwGetPostFormatOptions: function(){
            return {
                standard : "Standard",
                link : "Link",
                video : "Video",
                audio : "Audio",
            };
        },
        pwGetPostClassOptions: function(){
            return {
                contributor:"Contributor",
                author:"Author"
            };
        },
        pwGetPostYearOptions: function(){
            return [
                2007,
                2008,
                2009,
                2010,
                2011,
                2012,
                2013,
            ];
        },
        pwGetPostMonthOptionsPrimary: function(){
            return {
                1:"January",
                2:"Fabruary",
                3:"March",
                4:"April",
                5:"May",
                6:"June",
                7:"July",
                8:"August",
                9:"September",
                10:"October",
                11:"November",
                12:"December",
            };
        },
        pwGetPostMonthOptions: function(){
            return [
                {
                    name:"January",
                    number:1
                },
                {
                    name:"February",
                    number:2
                },
                {
                    name:"March",
                    number:3
                },
                {
                    name:"April",
                    number:4
                },
                {
                    name:"May",
                    number:5
                },
                {
                    name:"June",
                    number:6
                },
                {
                    name:"July",
                    number:7
                },
                {
                    name:"August",
                    number:8
                },
                {
                    name:"September",
                    number:9
                },
                {
                    name:"October",
                    number:10
                },
                {
                    name:"November",
                    number:11
                },
                {
                    name:"December",
                    number:12
                },
            ];
        },
        pwGetPostFormatMeta: function(){
            return [
                {
                    name:"",
                    slug:"standard",
                    domains:[],
                    icon:"<i class='icon-circle-blank'></i>"
                },
                {
                    name:"Link",
                    slug:"link",
                    domains:[],
                    icon:"<i class='icon-link'></i>"
                },
                {
                    name:"Video",
                    slug:"video",
                    domains:["youtube.com","youtu.be","vimeo.com"],
                    icon:"<i class='icon-youtube-play'></i>"
                },
                {
                    name:"Audio",
                    slug:"audio",
                    domains:["soundcloud.com"],
                    icon:"<i class='icon-headphones'></i>"
                },
            ];
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
            ///// EVALUATE AND SET POST_FORMAT DEPENDING ON LINK_URL /////
            evalPostFormat: function( link_url, post_format_meta ){
                var default_format = "standard";
                var set = "";
                //alert(post_format_meta);
                // If link_url has a value
                if ( !ext.isEmpty( link_url ) && !ext.isEmpty( post_format_meta ) ){
                    ///// FOR EACH POST FORMAT : Go through each post format
                    angular.forEach( post_format_meta, function( post_format ){
                        ///// FOR EACH DOMAIN : Go through each domain
                        angular.forEach( post_format.domains, function( domain ){
                        // If domain exists in the link_url, set that format
                            if ( ext.isInArray( domain, link_url ) ){
                                set = post_format.slug;
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
        };
    }]);


/*
  ____                      _       _____ _      _     _     
 / ___|  ___  __ _ _ __ ___| |__   |  ___(_) ___| | __| |___ 
 \___ \ / _ \/ _` | '__/ __| '_ \  | |_  | |/ _ \ |/ _` / __|
  ___) |  __/ (_| | | | (__| | | | |  _| | |  __/ | (_| \__ \
 |____/ \___|\__,_|_|  \___|_| |_| |_|   |_|\___|_|\__,_|___/
                                                             
////////// ------------ SEARCH FIELDS CONTROLLER ------------ //////////*/
postworld.controller('searchFields', ['$scope', 'pwPostOptions', 'pwEditPostFilters', function($scope, $pwPostOptions, $pwEditPostFilters) {

    // POST TYPE OPTIONS
    $scope.post_type_options = $pwPostOptions.pwGetPostTypeOptions();
    // POST YEAR OPTIONS
    $scope.post_year_options = $pwPostOptions.pwGetPostYearOptions();
    // POST MONTH OPTIONS
    $scope.post_month_options = $pwPostOptions.pwGetPostMonthOptions();
    // POST STATUS OPTIONS
    $scope.post_status_options = $pwPostOptions.pwGetPostStatusOptions();
    // POST FORMAT OPTIONS
    $scope.post_format_options = $pwPostOptions.pwGetPostFormatOptions();
    // POST FORMAT META
    $scope.post_format_meta = $pwPostOptions.pwGetPostFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();

    // TAXONOMY TERMS
    // Gets live set of terms from the DB
    // as $scope.tax_terms
    $pwPostOptions.getTaxTerms($scope);

    // TAXONOMY TERM WATCH : Watch for any changes to the post_data.tax_input
    // Make a new object which contains only the selected sub-objects
    $scope.selected_tax_terms = {};
    $scope.$watch( "taxInput",
        function (){
            // Create selected terms object
            $scope.selected_tax_terms = $pwEditPostFilters.selected_tax_terms($scope.tax_terms, $scope.taxInput);
            // Clear irrelivent sub-terms
            //$scope.post_data.tax_input = $pwEditPostFilters.clear_sub_terms( $scope.tax_terms, $scope.taxInput, $scope.selected_tax_terms );
        
        }, 1 );


    /*
    $scope.$on('updateUsername', function(username) { 
        $scope.feedQuery.author_name = username;
    });
    */
    
}]);


/*
  _   _                    _         _                                  _      _       
 | | | |___  ___ _ __     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
 | | | / __|/ _ \ '__|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
 | |_| \__ \  __/ |     / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
  \___/|___/\___|_|    /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
                                                                 |_|                   
////////// ------------ USER AUTOCOMPLETE CONTROLLER ------------ //////////*/
function userAutocomplete($scope, pwData) {
    $scope.username = undefined;
    $scope.queryList = function () {
        var searchTerm = $scope.username + "*";            
        var query_args = {
            number:20,
            fields:['user_nicename', 'display_name'],
            search: searchTerm,
        };
        pwData.user_query_autocomplete( query_args ).then(
            // Success
            function(response) {
                //console.log(response);    
                $scope.authors = response.data.results;
            },
            // Failure
            function(response) {
                throw { message:'Error: ' + JSON.stringify(response)};
            }
        );
    };

    // Watch on the value of username
    $scope.$watch( "username",
        function (){
            // When it changes, emit it's value to the parent controller
            $scope.$emit('updateUsername', $scope.username);
        }, 1 );
    
    // Catch broadcast of username change
    $scope.$on('updateUsername', function(event, data) { $scope.username = data; });

}

/*
  _____                    _         _                                  _      _       
 |_   _|_ _  __ _ ___     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
   | |/ _` |/ _` / __|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
   | | (_| | (_| \__ \  / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
   |_|\__,_|\__, |___/ /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
            |___/                                                |_|                   
////////// ------------ TAGS AUTOCOMPLETE CONTROLLER ------------ //////////*/
function tagsAutocomplete($scope, $filter, pwData) {

    $scope.tags_input = [];     // Array
    $scope.tags_input_obj = []; // Object

    $scope.queryTags = function () {
        var queryTag = $scope.queryTag;

        var args = {
            search: $scope.queryTag,
            taxonomy:"post_tag"
        }

        pwData.tags_autocomplete( args ).then(
            // Success
            function(response) {
                console.log(response.data);    
                $scope.tagOptions = response.data;
            },
            // Failure
            function(response) {
                throw { message:'Error: ' + JSON.stringify(response)};
            }
        );
    };

    $scope.addTag = function(){
        // Cycle through the tagOptions Object
        angular.forEach( $scope.tagOptions, function( tag ){
            if( tag.slug == $scope.queryTag ){
                $scope.tags_input_obj.push(tag);
                $scope.queryTag = "";
            }           
        });
    }

    $scope.removeTag = function(removeSlug){
        $scope.tags_input_obj = $filter('filter')($scope.tags_input_obj, function(item) {
            return !(item.slug == removeSlug);
         });
    }

    $scope.newTag = function(){
        var newTag = {
            name: $scope.queryTag,
            slug: $scope.queryTag,
            };

        $scope.tags_input_obj.push(newTag);
        $scope.queryTag = "";
    }

    // Watch on the object with input tags
    $scope.$watch( "tags_input_obj",
        function (){
            // When it changes, modify the tags_input object
            $scope.tags_input = [];
            angular.forEach( $scope.tags_input_obj, function( tag ){
                $scope.tags_input.push(tag.slug);
            });

            // Emit it's value to the parent controller
            $scope.$emit('updateTagsInput', $scope.tags_input);
        }, 1 );
    
    // Catch broadcast of load in tags
    $scope.$on('postTagsObject', function(event, data) { $scope.tags_input_obj = data; });

}




/*
  _____    _ _ _     ____           _   
 | ____|__| (_) |_  |  _ \ ___  ___| |_ 
 |  _| / _` | | __| | |_) / _ \/ __| __|
 | |__| (_| | | |_  |  __/ (_) \__ \ |_ 
 |_____\__,_|_|\__| |_|   \___/|___/\__|

////////// ------------ EDIT POST CONTROLLER ------------ //////////*/
postworld.controller('editPost',
    ['$scope', '$rootScope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
    'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', 'siteOptions', 'ext', 
    function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
        $pwData, $log, $route, $routeParams, $location, $http, $siteOptions, $ext ) {

    //alert( JSON.stringify( $route.current.action ) );

    //$scope.mode = "edit";
    $scope.status = "loading";

    $scope.default_post_data = {
        //post_id : 24,
        post_author: 1,
        post_title : "",
        post_name : "",
        post_type : "blog",
        post_status : "draft",
        post_format : "standard",
        post_class : "contributor",
        link_url : "",
        post_date_gmt:"",
        post_permalink : "",
        tax_input : {
            topic : [],
            section : [],
            type : [],
            post_tag : [],
        },
        tags_input : "",
    };


    // WATCH : ROUTE
    $scope.$on(
        "$routeChangeSuccess",
        function( $currentRoute, $previousRoute ){
            //alert( JSON.stringify( $currentRoute ) );
            ///// ROUTE : NEW POST /////
            if ( $route.current.action == "new_post"  ){ // && typeof $scope.post_data.post_id !== 'undefined'
                // SWITCH FROM MODE : EDIT > NEW
                // If we're coming to 'new' mode from 'edit' mode
                if($scope.mode == "edit"){
                    // Clear post Data
                    $scope.clear_post_data();
                }
                // Get the post type
                var post_type = ($routeParams.post_type || "");
                // If post type is supplied
                if ( post_type != "" )
                    // Set the post type
                    $scope.post_data.post_type = post_type;
                // Set the new mode
                $scope.mode = "new";
                // Set the status
                $scope.status = "done";
            }
            ///// ROUTE : EDIT POST /////
            else if ( $route.current.action == "edit_post"  ){ // && typeof $scope.post_data.post_id !== 'undefined'
                // Load the specified post data
                $scope.load_post_data();
            }
            ///// ROUTE : SET DEFAULT /////
            else if ( $route.current.action == "default"  ){
                $location.path('/new/blog');
            }
        }
    );

    ///// QUICK EDIT : LOAD POST DATA /////
    $scope.$on('loadPostData', function(event, post_id) {
        $scope.load_post_data( post_id );
    });


    $scope.load_post_data = function( post_id ){
        $scope.mode = "edit";


        if ( typeof $routeParams.post_id !== 'undefined' )
            var post_id = $routeParams.post_id;
        else{
            var post_id = $scope.post.ID;
        }


        // GET THE POST DATA
        $pwData.pw_get_post_edit( post_id ).then(
            // Success
            function(response) {    
                $log.info('pwData.pw_get_post_edit : RESPONSE : ', response.data);

                // FILTER FOR INPUT
                var get_post_data = response.data;

                ///// LOAD TAXONOMIES /////
                // RENAME THE KEY : TAXONOMY > TAX_INPUT
                var tax_input = {};
                var tax_obj = get_post_data['taxonomy'];
                // BOIL DOWN SELECTED TERMS
                angular.forEach( tax_obj, function( terms, taxonomy ){
                    tax_input[taxonomy] = [];
                    angular.forEach( terms, function( term ){
                        tax_input[taxonomy].push(term.slug);
                    });

                    // BROADCAST TAX OBJECT TO AUTOCOMPLETE CONTROLLER
                    if( taxonomy == "post_tag")
                        $scope.$broadcast('postTagsObject', terms);

                });
                delete get_post_data['taxonomy'];
                get_post_data['tax_input'] = tax_input; 

                ///// LOAD POST CONTENT /////
                
                // SET THE POST CONTENT
                if( typeof tinyMCE !== 'undefined' )
                    tinyMCE.get('post_content').setContent( get_post_data.post_content );

                ///// LOAD AUTHOR /////

                // EXTRACT AUTHOR NAME
                if ( typeof get_post_data['author']['user_nicename'] !== 'undefined' ){
                    get_post_data['post_author_name'] = get_post_data['author']['user_nicename'];
                    delete get_post_data['author'];
                }
                
                // BROADCAST TO USERNAME AUTOCOMPLETE FIELD
                $scope.$broadcast('updateUsername', get_post_data['post_author_name']);

                // SET DATA INTO THE SCOPE
                $scope.post_data = get_post_data;

                // UPDATE STATUS
                $scope.status = "done";

            },
            // Failure
            function(response) {
                //alert('error');
                $scope.status = "error";
            }
        );  
    }


    // SAVE POST FUNCTION
    $scope.savePost = function(pwData){

        // VALIDATE THE FORM
        if ($scope.post_data.post_title != '' || typeof $scope.post_data.post_title !== 'undefined'){
            //alert(JSON.stringify($scope.post_data));

            ///// GET POST_DATA FROM TINYMCE /////
            if ( typeof tinyMCE !== 'undefined' )
                $scope.post_data.post_content = tinyMCE.get('post_content').getContent();

            ///// SANITIZE FIELDS /////
            if ( typeof $scope.post_data.link_url === 'undefined' )
                $scope.post_data.link_url = '';


            ///// DEFINE POST DATA /////
            var post_data = $scope.post_data;

            //alert( JSON.stringify( post_data ) );
            
            $log.info('pwData.pw_save_post : SUBMITTING : ', post_data);

            ///// SAVE VIA AJAX /////
            $scope.status = "saving";
            $pwData.pw_save_post( post_data ).then(
                // Success
                function(response) {    
                    //alert( "RESPONSE : " + response.data );
                    $log.info('pwData.pw_save_post : RESPONSE : ', response.data);

                    // VERIFY POST CREATION
                    // If it was created, it's an integer
                    if( response.data === parseInt(response.data) ){
                        // SAVE SUCCESSFUL
                        var post_id = response.data;
                        $scope.status = "success";
                        $timeout(function() {
                          $scope.status = "done";
                        }, 2000);

                        // If created a new post
                        if ( $scope.mode == "new" )
                            // Forward to edit page
                            $location.path('/edit/' + post_id);
                        else
                            // Otherwise, reload the data from server
                            $scope.load_post_data();

                        // For Quick Edit Mode - emit to parent successful update
                        $rootScope.$broadcast('postUpdated', post_id);

                    }
                    else{
                        // ERROR
                        //alert("Error : " + JSON.stringify(response) );
                        $scope.status = "done";
                    }
                    
                },
                // Failure
                function(response) {
                    //alert('error');
                    $scope.status = "error";
                    $timeout(function() {
                      $scope.status = "done";
                    }, 2000);

                }
            );

        } else {
            alert("Post not saved : missing fields.");
        }
        
    }

    $scope.clear_post_data = function(){
        $scope.post_data = {};
        if( typeof tinyMCE !== 'undefined' )
            tinyMCE.get('post_content').setContent( "" );
    }

    $scope.pw_get_post_object = function(){
        var post_data = $scope.default_post_data;
        // CHECK TERMS CATEGORY / SUBCATEGORY ORDER
        post_data = $pwEditPostFilters.sortTaxTermsInput( post_data, $scope.tax_terms, 'tax_input' );
        return post_data;   
    }

    // POST TYPE OPTIONS
    $scope.post_type_options = $pwPostOptions.pwGetPostTypeOptions();
    // POST STATUS OPTIONS
    $scope.post_status_options = $pwPostOptions.pwGetPostStatusOptions();
    // POST FORMAT OPTIONS
    $scope.post_format_options = $pwPostOptions.pwGetPostFormatOptions();
    // POST FORMAT META
    $scope.post_format_meta = $pwPostOptions.pwGetPostFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();

    // POST DATA OBJECT
    $scope.post_data = $scope.pw_get_post_object();
    //alert(JSON.stringify($scope.post_data));

    // UPDATE AUTHOR NAME FROM AUTOCOMPLETE
    // Interacts with userAutocomplete() controller
    // Catches the recent value of the auto-complete
    $scope.$on('updateUsername', function( event, data ) { 
        $scope.post_data.post_author_name = data;
    });

    // UPDATE POST TAGS FROM AUTOCOMPLETE MODULE
    // Interacts with tagsAutocomplete() controller
    // Catches the recent value of the tags_input and inject into tax_input
    $scope.$on('updateTagsInput', function( event, data ) { 
        $scope.post_data.tax_input.post_tag = data;
    });


    // TAXONOMY TERMS
    // Gets live set of terms from the DB
    // as $scope.tax_terms
    $pwPostOptions.getTaxTerms($scope);


    // TAXONOMY TERM WATCH : Watch for any changes to the post_data.tax_input
    // Make a new object which contains only the selected sub-objects
    $scope.selected_tax_terms = {};
    $scope.$watch( "post_data.tax_input",
        function (){
            // Create selected terms object
            $scope.selected_tax_terms = $pwEditPostFilters.selected_tax_terms($scope.tax_terms, $scope.post_data.tax_input);
            
            // Clear irrelivent sub-terms
            $scope.post_data.tax_input = $pwEditPostFilters.clear_sub_terms( $scope.tax_terms, $scope.post_data.tax_input, $scope.selected_tax_terms );
        
        }, 1 );

    // LINK_URL WATCH : Watch for changes in link_url
    // Evaluate the post_format
    $scope.$watchCollection('[post_data.link_url, post_data.post_format]',
        function ( newValue, oldValue ){
            $scope.post_data.post_format = $pwEditPostFilters.evalPostFormat( $scope.post_data.link_url, $scope.post_format_meta );
        });


    // POST TYPE WATCH : Watch the Post Type
    $scope.$watch( "post_data.post_type",
        function (){
            // ROUTE CHANGE
            if( $scope.mode == "new" )
                $location.path('/new/' + $scope.post_data.post_type);

            // BROADCAST CHANGE TO CHILD CONTROLLERS NODES
            $rootScope.$broadcast('changePostType', $scope.post_data.post_type );

        }, 1 );

    ////////// FEATURED IMAGE //////////
    // Media Upload Window
    $scope.updateFeaturedImage = function(image_object){
        //alert( JSON.stringify(image_object) );
        $scope.post_data.image = {};
        $scope.post_data.image.meta = image_object;
        $scope.post_data.thumbnail_id = image_object.id;
        if( typeof image_object !== 'undefined' ){
            $scope.hasFeaturedImage = 'true';
        }
    }

    // FEATURE IMAGE WATCH : Watch the Featured Image
    $scope.$watch( "post_data.image",
        function (){
        if( typeof $scope.post_data.thumbnail_id !== 'undefined' &&
            $scope.post_data.thumbnail_id !== "" &&
            $scope.post_data.thumbnail_id !== "delete" )
            $scope.hasFeaturedImage = 'true';
        else
            $scope.hasFeaturedImage = 'false';
        }, 1 );

    $scope.removeFeaturedImage = function(){
        $scope.post_data.image = {};
        $scope.post_data.thumbnail_id = "delete";
        $scope.hasFeaturedImage = 'false';
    }

    ///// GET POST_CONTENT FROM TINY MCE /////
    $scope.getTinyMCEContent = function(){        
        
    }

    
}]);



////////// ------------ EVENT DATA/TIME CONTROLLER ------------ //////////*/
postworld.controller('eventInput',
    ['$scope', '$rootScope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
        'pwData', '$log', 'siteOptions', 'ext', 
    function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, 
        $pwData, $log, $siteOptions, $ext ) {


    // SETUP DATE OBJECTS
    if( typeof $scope.post_data.post_meta === 'undefined' )
        $scope.post_data.post_meta = {};

    if( typeof $scope.post_data.post_meta.event_start_date_obj === 'undefined' )
        $scope.post_data.post_meta.event_start_date_obj = new Date( );
    if( typeof $scope.post_data.post_meta.event_end_date_obj === 'undefined' )
        $scope.post_data.post_meta.event_end_date_obj = new Date( );


    // WATCH : EVENT START TIME
    $scope.$watch( "post_data.post_meta.event_start_date_obj",
        function (){
            $scope.post_data.post_meta.event_start_date = $filter('date')(
                $scope.post_data.post_meta.event_start_date_obj, 'yyyy-MM-dd HH:mm' );
        }, 1 );

    // WATCH : EVENT END TIME
    $scope.$watch( "post_data.post_meta.event_end_date_obj",
        function (){
            $scope.post_data.post_meta.event_end_date = $filter('date')(
                $scope.post_data.post_meta.event_end_date_obj, 'yyyy-MM-dd HH:mm' );
        }, 1 );


    // POST TYPE WATCH : Watch the Post Type
    // Cleanup post_meta
    $scope.$on('changePostType', function(event, data) { $scope.post_data.post_meta = {}; });


    ////////// EVENT DATE PICKER : CONFIG //////////
    $scope.showWeeks = false;

    $scope.clear = function () {
        $scope.dt = null;
    };

    $scope.dateOptions = {
        'year-format': "'yy'",
        'starting-day': 1
    };

    ////////// TIME PICKER : CONFIG //////////

    $scope.minDate = new Date();
    $scope.mytime = new Date();

    $scope.hstep = 1;
    $scope.mstep = 1;

    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    // Toggle AM/PM // 24H
    $scope.ismeridian = true;
    $scope.toggleMode = function() {
        $scope.ismeridian = ! $scope.ismeridian;
    };

    // Example (bind to ng-change)
    $scope.changed = function () {
        //console.log('Time changed to: ' + $scope.EventStartTimeObject );
        //$scope.updateEventDate();
        //$scope.post_data.EventStartHour = $scope.EventStartDateObject.getUTCHours();
        //alert( $scope.EventEndDateObject.getHours() );
    };

    // Example of setting time
    $scope.update = function() {
        var d = new Date();
        d.setHours( 14 );
        d.setMinutes( 0 );
        $scope.mytime = d;
    };

    // Example of clearing time
    $scope.clear = function() {
        $scope.mytime = null;
    };


}]);




/*
     _         _   _                     _         _                                  _      _       
    / \  _   _| |_| |__   ___  _ __     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
   / _ \| | | | __| '_ \ / _ \| '__|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
  / ___ \ |_| | |_| | | | (_) | |     / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
 /_/   \_\__,_|\__|_| |_|\___/|_|    /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
                                                                               |_|                   
////////// ------------ AUTHOR AUTOCOMPLETE CONTROLLER ------------ //////////*/

///// AUTHOR AUTOCOMPLETE /////
postworld.controller('AuthorAutocomplete', ['$scope', function($scope) {
    $scope.selected = undefined;
    $scope.authors = ['Erik Davis', 'Daniel Pinchbeck', 'Ken Jordan', 'Starhawk', 'Faye', 'Alex Grey', 'Nick Meador', 'Maureen Dawn Healy', 'Jonathon Miller Weisberger', 'Adam Elenbaas', 'Dan Phiffer', 'Michael Garfield', 'Jay Michaelson', 'Nathan Walters', 'Carolyn Elliott', 'Gary Lachman', 'Kourosh Ziabari', 'Adam Sommer'];
}]);


/*
  ____           _     _     _       _    
 |  _ \ ___  ___| |_  | |   (_)_ __ | | __
 | |_) / _ \/ __| __| | |   | | '_ \| |/ /
 |  __/ (_) \__ \ |_  | |___| | | | |   < 
 |_|   \___/|___/\__| |_____|_|_| |_|_|\_\

////////// ------------ POST LINK CONTROLLER ------------ //////////*/
postworld.controller('postLink', ['$scope', '$timeout','pwPostOptions','pwEditPostFilters','embedly','ext',function($scope, $timeout, $pwPostOptions, $pwEditPostFilters, $embedly, $ext, pwData) {

    // Setup the intermediary Link URL
    $scope.link_url = '';

    // Set the default statuss
    $scope.loaded = 'false';

    // Set the default mode
    $scope.mode = "url_input";

    // POST TYPE OPTIONS
    $scope.post_type_options = $pwPostOptions.pwGetPostTypeOptions();
    // POST STATUS OPTIONS
    $scope.post_status_options = $pwPostOptions.pwGetPostStatusOptions();
    // POST FORMAT OPTIONS
    $scope.post_format_options = $pwPostOptions.pwGetPostFormatOptions();
    // POST FORMAT META
    $scope.post_format_meta = $pwPostOptions.pwGetPostFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();
    
    // TAXONOMY TERMS
    //$scope.tax_terms = $pwPostOptions.pwGetTaxTerms();

    // DEFAULT POST DATA
    $scope.post_data = {
        post_title:"Link Title",
        link_url:"",
        post_format:"standard",
        post_class:"",
        tags_input:"",
        post_status:"publish",
        tax_input : {
            topic : [],
            section : [],
            type : []
        }
    };


    // GET URL EXTRACT
    // 1. On detect paste
    // 2. On click

    $scope.extract_url = function() {

        $embedly.liveEmbedlyExtract( $scope.link_url ).then( // 
                // Success
                function(response) {
                    console.log(response);    
                    $scope.embedly_extract = response;
                },
                // Failure
                function(response) {
                    //alert('Could not find URL.');
                    throw {message:'Embedly Error'+response};
                }
            );

        //alert(JSON.stringify($scope.embedly_extract));
        //alert('extract');
    }
    $scope.reset_extract = function() {
        $scope.embedly_extract = {};
        //alert(JSON.stringify($scope.embedly_extract));
        //alert('extract');
    }
    $scope.ok = function() {
        $scope.mode = "url_input";
    }
    
    // EMBEDLY OBJECT WATCH : Watch for any changes to the embedly data
    $scope.embedly_extract = {};
    $scope.$watch( "embedly_extract",
        function (){
            // CHANGE MODE 
            // SET MODE : ( new | edit )
            if ( typeof $scope.embedly_extract.title == 'undefined' )
                $scope.mode = "url_input";
            else
                $scope.mode = "post_input";

            // Here Process the data from embedly.extract into the post_data format
            if( $scope.mode == "post_input" ){
                // Translate Embedly Object into WP Object
                $scope.embedly_extract_translated = $embedly.translateToPostData( $scope.embedly_extract );
                // Merge it with the current post_data
                $scope.post_data = $ext.mergeRecursiveObj( $scope.post_data, $scope.embedly_extract_translated ) ;
                // Extract image meta
                $scope.embedly_extract_image_meta = $embedly.embedlyExtractImageMeta( $scope.embedly_extract );
                
                // Default Selected Image
                $scope.selected_image = 0;
            }

        $scope.loaded = 'true';

        }, 1 );


    ///// SELECT IMAGES /////
    // Default Selected Image
    $scope.selected_image = 0;
    // Previous Image
    $scope.previousImage = function(){
         if( $scope.selected_image == 0 ){
            $scope.selected_image = $scope.embedly_extract_image_meta.image_count-1;
        }
        else
            $scope.selected_image --;
    };
    // Next Image
    $scope.nextImage = function(){
        if( $scope.selected_image >= $scope.embedly_extract_image_meta.image_count-1 ){
            $scope.selected_image = 0;
        }
        else
            $scope.selected_image ++;
    };

    // UPDATE SELECTED IMAGE
    $scope.$watch( "selected_image",
        function ( newValue, oldValue ){
            if ( typeof $scope.embedly_extract_image_meta != 'undefined' )
                $scope.post_data.image_url = $scope.embedly_extract_image_meta.images[newValue].url;
            else
                $scope.post_data.image_url = "";
        }, 1 );

    // TAXONOMY TERM WATCH : Watch for any changes to the post_data.tax_input
    // Make a new object which contains only the selected sub-objects
    $scope.selected_tax_terms = {};
    $scope.$watch( "post_data.tax_input",
        function (){
            // Create selected terms object
            $scope.selected_tax_terms = $pwEditPostFilters.selected_tax_terms($scope.tax_terms, $scope.post_data.tax_input);
            // Clear irrelivent sub-terms
            $scope.post_data.tax_input = $pwEditPostFilters.clear_sub_terms( $scope.tax_terms, $scope.post_data.tax_input, $scope.selected_tax_terms );
        }, 1 );

    // LINK_URL WATCH : Watch for changes in link_url
    // Evaluate the post_format
    $scope.$watchCollection('[post_data.link_url, post_data.post_format]',
        function ( newValue, oldValue ){
            $scope.post_data.post_format = $pwEditPostFilters.evalPostFormat( $scope.post_data.link_url, $scope.post_format_meta );
        });

    ///// SUBMIT /////
    function timeoutStatus(){
        $scope.mode =  "success";
        $scope.submit_status = "ready";
        };
    $scope.submit_status = "ready";
    $scope.savePost = function(){
        //alert(JSON.stringify($scope.post_data));
        $scope.submit_status = "busy";
        $timeout( timeoutStatus, 1000);

    }

    // ADD ERROR SUPPORT


}]);




/*
  ____           _        _        _   _                 
 |  _ \ ___  ___| |_     / \   ___| |_(_) ___  _ __  ___ 
 | |_) / _ \/ __| __|   / _ \ / __| __| |/ _ \| '_ \/ __|
 |  __/ (_) \__ \ |_   / ___ \ (__| |_| | (_) | | | \__ \
 |_|   \___/|___/\__| /_/   \_\___|\__|_|\___/|_| |_|___/
                                                         
////////// ------------ POST ACTIONS CONTROLLER ------------ //////////*/

var postActions = function ( $scope, pwData ) {

    $scope.$watch( "post.viewer",
        function (){
            ( $scope.post.viewer.is_favorite == true ) ? $scope.isFavorite="selected" : $scope.isFavorite="" ;
            ( $scope.post.viewer.is_view_later == true ) ? $scope.isViewLater="selected" : $scope.isViewLater="" ;
        }, 1 );

    $scope.setFavorite = function($event){
        $scope.togglePostRelationship('favorites');
        //if ($event.stopPropagation) $event.stopPropagation();
        //if ($event.preventDefault) $event.preventDefault();
        
    }

    $scope.setViewLater = function($event){
        $scope.togglePostRelationship('view_later');
        //if ($event.stopPropagation) $event.stopPropagation();
        //if ($event.preventDefault) $event.preventDefault();
        
    }

    $scope.togglePostRelationship = function( postRelationship ) {

        // Localize the viewer object
        var viewer = $scope.post.viewer;
        
        // Check toggle switch
        var setTo;
        if ( postRelationship == "favorites" ){
            ( viewer.is_favorite == true ) ? setTo = false : setTo = true;
            $scope.favoriteStatus = "busy";
        }
        if ( postRelationship == "view_later" ){
            ( viewer.is_view_later == true ) ? setTo = false : setTo = true ;
            $scope.viewLaterStatus = "busy";
        }

        // Setup parmeters
        var args = {
            "relationship" : postRelationship,
            "switch" : setTo,
            "post_id" : $scope.post.ID,
        };

        // AJAX Call 
        pwData.set_post_relationship( args ).then(
            // ON : SUCCESS
            function(response) {    
                //SET FAVORITE
                if ( postRelationship == "favorites"){
                    if ( response.data === false )
                        $scope.post.viewer.is_favorite = false;
                    else if ( response.data === true )
                        $scope.post.viewer.is_favorite = true;
                    //else
                        //alert( "Server error setting favorite." )

                    $scope.favoriteStatus = "done";
                }
                //SET VIEW LATER
                if ( postRelationship == "view_later"){
                    if ( response.data === false )
                        $scope.post.viewer.is_view_later = false;
                    else if ( response.data === true )
                        $scope.post.viewer.is_view_later = true;
                    //else
                        //alert( "Server error setting view later." )

                    $scope.viewLaterStatus = "done";
                }
            },
            // ON : FAILURE
            function(response) {
                //alert('Client error.');
            }
        );

    };

};



/*
  ____           _    __     __    _       
 |  _ \ ___  ___| |_  \ \   / /__ | |_ ___ 
 | |_) / _ \/ __| __|  \ \ / / _ \| __/ _ \
 |  __/ (_) \__ \ |_    \ V / (_) | ||  __/
 |_|   \___/|___/\__|    \_/ \___/ \__\___|
                                                                                          
////////// ------------ POST ACTIONS CONTROLLER ------------ //////////*/

var postVote = function ( $rootScope, $scope, pwData ) {
    $rootScope.vote_power = "10";

    // SWITCH CSS CLASSES BASED ON VOTE
    $scope.$watch( "post.viewer.has_voted",
        function (){
            ( $scope.post.viewer.has_voted > 0 ) ? $scope.hasVotedUp = "selected" : $scope.hasVotedUp = "" ;
            ( $scope.post.viewer.has_voted < 0 ) ? $scope.hasVotedDown = "selected" : $scope.hasVotedDown = "" ;
            if ( $scope.post.viewer.has_voted == 0 ){
                $scope.hasVotedUp = "";
                $scope.hasVotedDown = "";
            }
        }, 1 );

    // CAST VOTE ON THE POST
    $scope.votePost = function( points ){
        // If casting the same points, reset points
        if ( points == $scope.post.viewer.has_voted )
            points = 0;
        // Setup parameters
        var args = {
            post_id: $scope.post.ID,
            points: points,
        };
        $scope.voteStatus = "busy";
        // AJAX Call 
        pwData.set_post_points ( args ).then(
            // ON : SUCCESS
            function(response) {    
                //alert( JSON.stringify(response.data) );
                // RESPONSE.DATA : {"point_type":"post","user_id":1,"id":178472,"points_added":6,"points_total":"3"}
                if ( response.data.id == $scope.post.ID ){
                    // UPDATE POST POINTS
                    $scope.post.post_points = response.data.points_total;
                    // UPDATE VIEWER HAS VOTED
                    $scope.post.viewer.has_voted = ( parseInt($scope.post.viewer.has_voted) + parseInt(response.data.points_added) ) ;
                    
                } //else
                    //alert('Server error voting.');
                $scope.voteStatus = "done";
            },
            // ON : FAILURE
            function(response) {
                $scope.voteStatus = "done";
                //alert('Client error voting.');
            }
        );

    }

};





/*
  _____         _                         _____ _ _ _            
 |_   _|____  _| |_ __ _ _ __ ___  __ _  |  ___(_) | |_ ___ _ __ 
   | |/ _ \ \/ / __/ _` | '__/ _ \/ _` | | |_  | | | __/ _ \ '__|
   | |  __/>  <| || (_| | | |  __/ (_| | |  _| | | | ||  __/ |   
   |_|\___/_/\_\\__\__,_|_|  \___|\__,_| |_|   |_|_|\__\___|_|   
                                                                 
////////// NG-TEXTAREA-FILTER DIRECTIVE //////////// */
// Adds extended functionality to textareas
// Takes attributes : data-maxlength, data-readmore

postworld.directive('ngTextareaFilter', function() {
        return function($scope, element, attributes) {
            var model = attributes.ngModel;
            var readmore = attributes.readmore;
                $scope.$watch( model,
                    function (){
                        var modelObjArray = model.split(".");
                        var textarea_contents = $scope[modelObjArray[0]][modelObjArray[1]];

                        ///// Filter Text Contents /////
                        // Setup Max Characters
                        if( typeof attributes.maxlength == 'undefined' ){
                            var maxChars = 40;
                        }
                        else{
                            var maxChars = attributes.maxlength;
                        }
                        // Setup Readmore Quote
                        if( typeof readmore == 'undefined' ){
                            var readMore = "";
                        }
                        else{
                            var readMore = readmore;
                        }
                        
                        
                        // If it's over the maxLength, trim it

                        if ( typeof textarea_contents !== 'undefined' ){
                            if ( textarea_contents.length > maxChars && textarea_contents.length > (maxChars-readMore.length) ){
                                textarea_contents = textarea_contents.slice(0, (maxChars-readMore.length)) + readMore;
                            }

                            // Insert new textarea_contents;
                            $scope[modelObjArray[0]][modelObjArray[1]] = textarea_contents;
                        }

                    }, 1 );
        };
    });








/*
element.bind("keydown keypress", function(event) {
                if(event.which === 13) {
                    scope.$apply(function(){
                        //scope.$eval(attrs.ngEnter);
                        scope.$eval("submit()");
                    });
                    event.preventDefault();
                }
            });
*/




/*
  ____  _               _   _                
 |  _ \(_)_ __ ___  ___| |_(_)_   _____  ___ 
 | | | | | '__/ _ \/ __| __| \ \ / / _ \/ __|
 | |_| | | | |  __/ (__| |_| |\ V /  __/\__ \
 |____/|_|_|  \___|\___|\__|_| \_/ \___||___/
                                             
////////// ------------ DIRECTIVES ------------ //////////*/
/*
///// BLUR FOCUS DIRECTIVE /////
// Adds 'has-focus' class to form items in focus
var blurFocusDirective = function () {
    return {
        restrict: 'E',
        require: '?ngModel',
        link: function (scope, elm, attr, ctrl) {
            if (!ctrl) {
                return;
            }

            elm.on('focus', function () {
                elm.addClass('has-focus');

                scope.$apply(function () {
                    ctrl.hasFocus = true;
                });
            });

            elm.on('blur', function () {
                elm.removeClass('has-focus');
                elm.addClass('has-visited');

                scope.$apply(function () {
                    ctrl.hasFocus = false;
                    ctrl.hasVisited = true;
                });
            });

            elm.closest('form').on('submit', function () {
                elm.addClass('has-visited');

                scope.$apply(function () {
                    ctrl.hasFocus = false;
                    ctrl.hasVisited = true;
                });
            });

        }
    };

};
postworld.directive('input', blurFocusDirective);
postworld.directive('select', blurFocusDirective);
*/

///// SUBMIT ON ENTER /////
// Submit on Enter, without a real form
postworld.directive('ngEnter', function() {
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



///// KEEP DROPDOWN OPEN ON CLICK /////
postworld.directive('preventDefaultClick', function() {
        return {
            restrict: 'A',
            link: function (scope, element) {
                element.bind('click', function (event) {
                    event.stopPropagation();
                });
            }
        };
    });

///// SELECT ON CLICK /////
postworld.directive('selectOnClick', function() {
        return function (scope, element, attrs) {
            element.bind('click', function () {
                this.select();
            });
        };
    });




/*
  _____ _           _   _        _____         _                       
 | ____| | __ _ ___| |_(_) ___  |_   _|____  _| |_ __ _ _ __ ___  __ _ 
 |  _| | |/ _` / __| __| |/ __|   | |/ _ \ \/ / __/ _` | '__/ _ \/ _` |
 | |___| | (_| \__ \ |_| | (__    | |  __/>  <| || (_| | | |  __/ (_| |
 |_____|_|\__,_|___/\__|_|\___|   |_|\___/_/\_\\__\__,_|_|  \___|\__,_|

 * angular-elastic v2.1.0
 * (c) 2013 Monospaced http://monospaced.com
 * License: MIT
 */

angular.module('monospaced.elastic', [])

  .constant('msdElasticConfig', {
    append: ''
  })

  .directive('msdElastic', ['$timeout', '$window', 'msdElasticConfig', function($timeout, $window, config) {
    'use strict';

    return {
      require: 'ngModel',
      restrict: 'A, C',
      link: function(scope, element, attrs, ngModel){

        // cache a reference to the DOM element
        var ta = element[0],
            $ta = element;

        // ensure the element is a textarea, and browser is capable
        if (ta.nodeName !== 'TEXTAREA' || !$window.getComputedStyle) {
          return;
        }

        // set these properties before measuring dimensions
        $ta.css({
          'overflow': 'hidden',
          'overflow-y': 'hidden',
          'word-wrap': 'break-word'
        });

        // force text reflow
        var text = ta.value;
        ta.value = '';
        ta.value = text;

        var appendText = attrs.msdElastic || config.append,
            append = appendText === '\\n' ? '\n' : appendText,
            $win = angular.element($window),
            $mirror = angular.element('<textarea tabindex="-1" style="position: absolute; ' +
                                      'top: -999px; right: auto; bottom: auto; left: 0 ;' +
                                      'overflow: hidden; -webkit-box-sizing: content-box; ' +
                                      '-moz-box-sizing: content-box; box-sizing: content-box; ' +
                                      'min-height: 0!important; height: 0!important; padding: 0;' +
                                      'word-wrap: break-word; border: 0;"/>').data('elastic', true),
            mirror = $mirror[0],
            taStyle = getComputedStyle(ta),
            resize = taStyle.getPropertyValue('resize'),
            borderBox = taStyle.getPropertyValue('box-sizing') === 'border-box' ||
                        taStyle.getPropertyValue('-moz-box-sizing') === 'border-box' ||
                        taStyle.getPropertyValue('-webkit-box-sizing') === 'border-box',
            boxOuter = !borderBox ? {width: 0, height: 0} : {
                          width: parseInt(taStyle.getPropertyValue('border-right-width'), 10) +
                                  parseInt(taStyle.getPropertyValue('padding-right'), 10) +
                                  parseInt(taStyle.getPropertyValue('padding-left'), 10) +
                                  parseInt(taStyle.getPropertyValue('border-left-width'), 10),
                          height: parseInt(taStyle.getPropertyValue('border-top-width'), 10) +
                                 parseInt(taStyle.getPropertyValue('padding-top'), 10) +
                                 parseInt(taStyle.getPropertyValue('padding-bottom'), 10) +
                                 parseInt(taStyle.getPropertyValue('border-bottom-width'), 10)
                        },
            minHeightValue = parseInt(taStyle.getPropertyValue('min-height'), 10),
            heightValue = parseInt(taStyle.getPropertyValue('height'), 10),
            minHeight = Math.max(minHeightValue, heightValue) - boxOuter.height,
            maxHeight = parseInt(taStyle.getPropertyValue('max-height'), 10),
            mirrored,
            active,
            copyStyle = ['font-family',
                         'font-size',
                         'font-weight',
                         'font-style',
                         'letter-spacing',
                         'line-height',
                         'text-transform',
                         'word-spacing',
                         'text-indent'];

        // exit if elastic already applied (or is the mirror element)
        if ($ta.data('elastic')) {
          return;
        }

        // Opera returns max-height of -1 if not set
        maxHeight = maxHeight && maxHeight > 0 ? maxHeight : 9e4;

        // append mirror to the DOM
        if (mirror.parentNode !== document.body) {
          angular.element(document.body).append(mirror);
        }

        // set resize and apply elastic
        $ta.css({
          'resize': (resize === 'none' || resize === 'vertical') ? 'none' : 'horizontal'
        }).data('elastic', true);

        /*
         * methods
         */

        function initMirror(){
          mirrored = ta;
          // copy the essential styles from the textarea to the mirror
          taStyle = getComputedStyle(ta);
          angular.forEach(copyStyle, function(val){
            mirror.style[val] = taStyle.getPropertyValue(val);
          });
        }

        function adjust() {
          var taHeight,
              mirrorHeight,
              width,
              overflow;

          if (mirrored !== ta) {
            initMirror();
          }

          // active flag prevents actions in function from calling adjust again
          if (!active) {
            active = true;

            mirror.value = ta.value + append; // optional whitespace to improve animation
            mirror.style.overflowY = ta.style.overflowY;

            taHeight = ta.style.height === '' ? 'auto' : parseInt(ta.style.height, 10);

            // update mirror width in case the textarea width has changed
            width = parseInt(borderBox ?
                             ta.offsetWidth :
                             getComputedStyle(ta).getPropertyValue('width'), 10) - boxOuter.width;
            mirror.style.width = width + 'px';

            mirrorHeight = mirror.scrollHeight;

            if (mirrorHeight > maxHeight) {
              mirrorHeight = maxHeight;
              overflow = 'scroll';
            } else if (mirrorHeight < minHeight) {
              mirrorHeight = minHeight;
            }
            mirrorHeight += boxOuter.height;

            ta.style.overflowY = overflow || 'hidden';

            if (taHeight !== mirrorHeight) {
              ta.style.height = mirrorHeight + 'px';
            }

            // small delay to prevent an infinite loop
            $timeout(function(){
              active = false;
            }, 1);

          }
        }

        function forceAdjust(){
          active = false;
          adjust();
        }

        /*
         * initialise
         */

        // listen
        if ('onpropertychange' in ta && 'oninput' in ta) {
          // IE9
          ta['oninput'] = ta.onkeyup = adjust;
        } else {
          ta['oninput'] = adjust;
        }

        $win.bind('resize', forceAdjust);

        scope.$watch(function(){
          return ngModel.$modelValue;
        }, function(newValue){
          forceAdjust();
        });

        /*
         * destroy
         */

        scope.$on('$destroy', function(){
          $mirror.remove();
          $win.unbind('resize', forceAdjust);
        });
      }
    };

  }]);


/*
  __  __          _ _         __  __           _       _ 
 |  \/  | ___  __| (_) __ _  |  \/  | ___   __| | __ _| |
 | |\/| |/ _ \/ _` | |/ _` | | |\/| |/ _ \ / _` |/ _` | |
 | |  | |  __/ (_| | | (_| | | |  | | (_) | (_| | (_| | |
 |_|  |_|\___|\__,_|_|\__,_| |_|  |_|\___/ \__,_|\__,_|_|

////////// ------------ MEDIA MODAL ------------ //////////*/                                                         


var mediaModalCtrl = function ($scope, $modal, $log, $window, pwData) {

  $scope.openMediaModal = function (post) {
    var modalInstance = $modal.open({
      templateUrl: pwData.pw_get_template('panels','','media_modal'), // $window['site_info'].stylesheet_directory + '/postworld/templates/panels/media_modal.html', // //jsVars.pluginurl+'/postworld/templates/panels/media_modal.html',
      controller: MediaModalInstanceCtrl,
      windowClass: 'media_modal',
      resolve: {
        post: function(){
            return post;
        }
      }
    });
    modalInstance.result.then(function (selectedItem) {
        //$scope.post_title = post_title;
    }, function () {
        // WHEN CLOSE MODAL
        $log.info('Modal dismissed at: ' + new Date());
    });
  };

};


var MediaModalInstanceCtrl = function ($scope, $sce, $modalInstance, post, pwData) {
    
    // Import the passed post object into the Modal Scope
    $scope.post = post;

    /*
    $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
        // RETURN THIS VALUE TO PAGE
    };
    */
    $scope.status = "loading";

    $scope.oEmbed = '';
    var link_url = post.link_url;
    var args = { "link_url": link_url };

    // MEDIA GET
    pwData.wp_ajax('ajax_oembed_get', args ).then(
        // Success
        function(response) {    
            $scope.oEmbed = $sce.trustAsHtml( response.data );
            $scope.status = "done";
        },
        // Failure
        function(response) {
            $scope.status = "error";
        }
    );

    // MODAL CLOSE
    $scope.close = function () {
        $modalInstance.dismiss('close');
    };
};



/*
  __  __          _ _         _____           _              _ 
 |  \/  | ___  __| (_) __ _  | ____|_ __ ___ | |__   ___  __| |
 | |\/| |/ _ \/ _` | |/ _` | |  _| | '_ ` _ \| '_ \ / _ \/ _` |
 | |  | |  __/ (_| | | (_| | | |___| | | | | | |_) |  __/ (_| |
 |_|  |_|\___|\__,_|_|\__,_| |_____|_| |_| |_|_.__/ \___|\__,_|

////////// ------------ MEDIA EMBED CONTROLLER ------------ //////////*/   

var mediaEmbed = function ( $scope, $sce, pwData ) {

    $scope.oEmbed = "";
    $scope.oEmbedGet = function (link_url) {
        var args = { "link_url":link_url };
        var oEmbed = "";
        pwData.wp_ajax('ajax_oembed_get', args ).then(
            // Success
            function(response) {    
                $scope.oEmbed = $sce.trustAsHtml(response.data);
            },
            // Failure
            function(response) {
                //alert("error");
            }
        );
        
    };

    // Run oEmbedGet on Media
    if(
        $scope.post.post_format == 'video' ||
        $scope.post.post_format == 'audio'
        )
        $scope.oEmbed = $scope.oEmbedGet( $scope.post.link_url );

};



/*
              _____           _              _ 
   ___       | ____|_ __ ___ | |__   ___  __| |
  / _ \ _____|  _| | '_ ` _ \| '_ \ / _ \/ _` |
 | (_) |_____| |___| | | | | | |_) |  __/ (_| |
  \___/      |_____|_| |_| |_|_.__/ \___|\__,_|

////////// ------------ O-EMBED DIRECTIVE ------------ //////////*/  

postworld.directive( 'oEmbed-old', ['$sce','pwData', function($scope, $sce, pwData){

    return { 
        //restrict: 'A',
        //scope : function(){
        //},
        //template : '',
        link : function ($scope, element, attributes){
            
            //alert( attributes.oEmbed );
            $scope.status = "loading";
            $scope.oEmbed = "embed code for : " + attributes.oEmbed;

            var link_url = attributes.oEmbed;
            var args = { "link_url": link_url };

            // MEDIA GET
            $scope.oEmbedGet = function(){
                pwData.wp_ajax('ajax_oembed_get', args ).then(
                    // Success
                    function(response) {    
                        $scope.status = "done";
                        return $sce.trustAsHtml( response.data );
                        
                    },
                    // Failure
                    function(response) {
                        $scope.status = "error";
                    }
                );
            };

            $scope.oEmbed = $scope.oEmbedGet();

        }
    };

}]);

postworld.directive( 'oEmbed', ['$sce',function($scope, $sce){

    return { 
        //restrict: 'A',
        //scope : function(){
        //},
        //template : '',
        controller: 'pwOEmbedController',
        link : function ($scope, element, attributes){
        	// When the oEmbed Value changes, then change the html here
        	$scope.$watch('oEmbed', function(value) {
        		console.log('test',value);
        		element.html(value);
        	});          
        }
    };

}]);


postworld.controller('pwOEmbedController',
    function pwOEmbedController($scope, $attrs, $sce, pwData) {
            //alert( attributes.oEmbed );
            $scope.status = "loading";
            $scope.oEmbed = "embed code for : " + $attrs.oEmbed;

            var link_url = $attrs.oEmbed;
            var args = { "link_url": link_url };

            // MEDIA GET
            $scope.oEmbedGet = function(){
                pwData.wp_ajax('ajax_oembed_get', args ).then(
                    // Success
                    function(response) {    
                        $scope.status = "done";
                        console.log('return',response.data);
                        $scope.oEmbed = response.data; // $sce.trustAsHtml( response.data );                        
                    },
                    // Failure
                    function(response) {
                        $scope.status = "error";
                    }
                );
            };
            $scope.oEmbedGet();
});

postworld.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});



/*
  ____        _         ____  _      _             
 |  _ \  __ _| |_ ___  |  _ \(_) ___| | _____ _ __ 
 | | | |/ _` | __/ _ \ | |_) | |/ __| |/ / _ \ '__|
 | |_| | (_| | ||  __/ |  __/| | (__|   <  __/ |   
 |____/ \__,_|\__\___| |_|   |_|\___|_|\_\___|_|   

////////// ------------ DATE PICKER CONTROLLER ------------ //////////*/   
var DatepickerDemoCtrl = function ($scope, $timeout) {

  $scope.today = function() {
    $scope.dt = new Date();
  };
  $scope.today();
  $scope.showWeeks = false;

  $scope.clear = function () {
    $scope.dt = null;
  };

  $scope.dateOptions = {
    'year-format': "'yy'",
    'starting-day': 1
  };

};





/*
             _                         _            _   
 __  __  _  | |_ ___ _ __ ___  _ __   | |_ ___  ___| |_ 
 \ \/ / (_) | __/ _ \ '_ ` _ \| '_ \  | __/ _ \/ __| __|
  >  <   _  | ||  __/ | | | | | |_) | | ||  __/\__ \ |_ 
 /_/\_\ (_)  \__\___|_| |_| |_| .__/   \__\___||___/\__|
                              |_|                       
////////// ------------ TEMP TEST CONTROLLER ------------ //////////*/   
var testController = function ( $scope, pwData, siteOptions, pwPostOptions2 ) {

    /*
    $scope.getTaxOutlineMixed = function() {
        var args = siteOptions.taxOutlineMixed();
        pwData.taxonomies_outline_mixed( args ).then(
            // Success
            function(response) {    
                alert(JSON.stringify(response.data));
            },
            // Failure
            function(response) {
                alert("error");
            }
        );
    };
    */
    $scope.promise = "empty";
    $scope.getTaxOutlineMixed = function() {
        
        $scope.promise = pwPostOptions2.taxOutlineMixed();

    };


};




'use strict';

/*
   _                       _              _   _       
  | |   _    ___ _ __ ___ | |__   ___  __| | | |_   _ 
 / __) (_)  / _ \ '_ ` _ \| '_ \ / _ \/ _` | | | | | |
 \__ \  _  |  __/ | | | | | |_) |  __/ (_| |_| | |_| |
 (   / (_)  \___|_| |_| |_|_.__/ \___|\__,_(_)_|\__, |
  |_|                                           |___/ 
////////// ------------ Embedly SERVICE ------------ //////////*/  
postworld.factory('embedly', function ($resource, $q, $log) {     
        // TODO Replace this with your Production Key
                // http://api.embed.ly/1/extract?key=:key&url=:url&maxwidth=:maxwidth&maxheight=:maxheight&format=:format&callback=:callback
        var embedlyKey = "512f7d063fc1490d9bcc7504c764a6dd";
        var embedlyUrl = "http://api.embed.ly/1/:action";
        var resource = $resource(embedlyUrl, {key:embedlyKey, url:''}, 
                                    {   embedly_call: { method: 'GET', isArray: false, params: {action:'extract'} },    }
                                );
        
        return {
            // A simplified wrapper for doing easy AJAX calls to Wordpress PHP functions
            embedly_call: function(action,url, options) {
                $log.info('embedly.embedly_call', action, url, options);
                var deferred = $q.defer();
                // works only for non array returns
                resource.embedly_call({action:action, url:url, options:options},
                    function (data) {
                        deferred.resolve(data);
                    },
                    function (response) {
                        deferred.reject(response);
                    });
                return deferred.promise;        
            },          
            liveEmbedlyExtract: function( link_url, options ){
                // LIVE EMBEDLY EXTRACT
                // API : http://embed.ly/docs/extract/api
                if (!link_url) throw {message:'embedly:link_url not provided'};
                // Escape the URL
                escape(link_url);
                // if there are options, add them to the url here.
                // call the service
                return this.embedly_call('extract',link_url,options);
                // for Ajax Calls

                //return: embedly_extract_obj;
            },
            translateToPostData: function( embedly_extract ){
                if (typeof embedly_extract.images[0] !== 'undefined' )
                    var link_url_set = embedly_extract.images[0].url;
                else
                    var link_url_set = ""; // defult image_url

                return{
                    post_title: embedly_extract.title,
                    post_excerpt: embedly_extract.description,
                    link_url: embedly_extract.url,
                    image_url: link_url_set,
                };
            },
            embedlyExtractImageMeta: function( embedly_extract ){
                
                if ( embedly_extract.images.length >= 1 )
                    var image_status_set = true;
                else
                    var image_status_set = false;

                return{
                    image_status: image_status_set,
                    image_count: embedly_extract.images.length,
                    images: embedly_extract.images,
                };
            },
                       
        };

    });

    
postworld.controller('pwEmbedly', function pwEmbedly($scope, $location, $log, pwData, $attrs, embedly) {
        $scope.embedlyGet = function () {
            
            embedly.liveEmbedlyExtract( $scope.link_url).then(
                // Success
                function(response) {
                    console.log(response);    
                    $scope.embedlyResponse = response;
                },
                // Failure
                function(response) {
                    throw {message:'Embedly Error'+response};
                }
            );
                  
        };      
    }
);





/*
     _       _           _         ____                      _                     
    / \   __| |_ __ ___ (_)_ __   |  _ \ _ __ ___  _ __   __| | _____      ___ __  
   / _ \ / _` | '_ ` _ \| | '_ \  | | | | '__/ _ \| '_ \ / _` |/ _ \ \ /\ / / '_ \ 
  / ___ \ (_| | | | | | | | | | | | |_| | | | (_) | |_) | (_| | (_) \ V  V /| | | |
 /_/   \_\__,_|_| |_| |_|_|_| |_| |____/|_|  \___/| .__/ \__,_|\___/ \_/\_/ |_| |_|
                                                  |_|                              
////////// ------------ ADMIN DROPDOWN ------------ //////////*/   
var adminDropdownMenu = function ($scope, $rootScope, $location, $window, $log, pwQuickEdit) {

    $scope.menuOptions = [
        {
            name: "Quick Edit",
            icon:"icon-pencil",
            action:"quick-edit"
        },
        {
            name: "Edit",
            icon:"icon-edit",
            action:"pw-edit",
        },
        {
            name: "WP Edit",
            icon:"icon-edit-sign",
            action:"wp-edit",
        },
        {
            name: "Flag",
            icon:"icon-flag",
            action:"flag",
        },
        {
            name: "Trash",
            icon:"icon-trash",
            action:"trash",
        }
    ];

    // Actions which each role has access to
    var actionsByRole = {
        "administrator": {
            own:['quick-edit', 'pw-edit', 'wp-edit','flag','trash'],
            other:['quick-edit', 'pw-edit', 'wp-edit','flag','trash']
        },
        "editor":{
            own: ['quick-edit', 'pw-edit', 'wp-edit','flag','trash'],
            other: ['quick-edit', 'pw-edit', 'wp-edit','flag','trash'],
        },
        "author":{
            own: ['quick-edit', 'pw-edit', 'wp-edit','flag','trash'],
            other: ['flag'],
        },
        "contributor":{
            own: ['quick-edit', 'pw-edit', 'wp-edit','flag','trash'],
            other: ['flag'],
        },
        "guest":{
            own: [],
            other: [],
        },
    };

    //$
    

    // Localize current user data
    $scope.current_user = $rootScope.current_user;

    // Detect the user's possession in relation to the post
    // If the user's ID is same as the post author's ID
    if ( typeof $scope.current_user.data !== 'undefined' && typeof $scope.post.author.ID !== 'undefined' ){
        if( $scope.current_user.data.ID == $scope.post.author.ID )
            $scope.postPossession = "own";
        else
            $scope.postPossession = "other";
    } else {
        $scope.postPossession = "other";
    }

    // Detect current user's role
    if ( $scope.current_user == 0 )
        $scope.currentRole = "guest";
    else if ( typeof $scope.current_user.roles != undefined ){
        $scope.currentRole = $rootScope.current_user.roles[0];
    }

    // Setup empty menu options array
    $scope.userOptions = [];

    // TODO : CHECK POST OBJECT, IF USER ID = SAME AS POST AUTHOR

    // Build menu for user based on role
    angular.forEach( $scope.menuOptions, function( option ){
        if( actionsByRole[ $scope.currentRole ][ $scope.postPossession ].indexOf( option.action ) != "-1" )
            $scope.userOptions.push( option );
    });

    // If no options added, set empty
    if ( $scope.userOptions == [] )
        $scope.userOptions = "0";
    

    $scope.menuAction = function(action){

        if( action == "wp-edit" )
            $window.location.href = $scope.post.edit_post_link.replace("&amp;","&");

        if( action == "pw-edit" )
            $window.location.href = "/post/#/edit/"+$scope.post.ID;

        if( action == "quick-edit" ){
            pwQuickEdit.openQuickEdit($scope.post);
        }

    };

};




/*///////// ------- SERVICE : PW QUICK EDIT ------- /////////*/  
postworld.service('pwQuickEdit', ['$log', '$modal', 'pwData', function ( $log, $modal, pwData ) {
    return{
        openQuickEdit : function( post ){

            console.log( "Launch Quick Edit : ", post );  
            var modalInstance = $modal.open({
              templateUrl: pwData.pw_get_template('panels','','quick_edit'), //jsVars.pluginurl+'/postworld/templates/panels/quick_edit.html',
              controller: quickEditInstanceCtrl,
              windowClass: 'quick_edit',
              resolve: {
                post: function(){
                    return post;
                }
              }
            });
            modalInstance.result.then(function (selectedItem) {
                //$scope.post_title = post_title;
            }, function () {
                // WHEN CLOSE MODAL
                $log.info('Modal dismissed at: ' + new Date());
            });


        },
        
    }
}]);







/*
   ___        _      _      _____    _ _ _   
  / _ \ _   _(_) ___| | __ | ____|__| (_) |_ 
 | | | | | | | |/ __| |/ / |  _| / _` | | __|
 | |_| | |_| | | (__|   <  | |__| (_| | | |_ 
  \__\_\\__,_|_|\___|_|\_\ |_____\__,_|_|\__|
                                             
////////// ------------ QUICK EDIT ------------ //////////*/   

var quickEdit = function ($scope, $modal, $log) {
    
    $scope.openQuickEdit = function( post ){
        console.log( "Launch Quick Edit : ", post );  
        var modalInstance = $modal.open({
          templateUrl: jsVars.pluginurl+'/postworld/templates/panels/quick_edit.html',
          controller: quickEditInstanceCtrl,
          windowClass: 'quick_edit',
          resolve: {
            post: function(){
                return post;
            }
          }
        });
        modalInstance.result.then(function (selectedItem) {
            //$scope.post_title = post_title;
        }, function () {
            // WHEN CLOSE MODAL
            $log.info('Modal dismissed at: ' + new Date());
        });
    };

    
};


var quickEditInstanceCtrl = function ($scope, $rootScope, $sce, $modalInstance, post, pwData, $timeout) {
    
    // Import the passed post object into the Modal Scope
    $scope.post = post;


    // TIMEOUT
    // Allow editPost Controller to Initialize
    $timeout(function() {
      $scope.$broadcast('loadPostData', post.ID );
    }, 1);
    

    // MODAL CLOSE
    $scope.close = function () {
        $modalInstance.dismiss('close');
    };
};






/*
  ____           _      ____            _             _ _           
 |  _ \ ___  ___| |_   / ___|___  _ __ | |_ _ __ ___ | | | ___ _ __ 
 | |_) / _ \/ __| __| | |   / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
 |  __/ (_) \__ \ |_  | |__| (_) | | | | |_| | | (_) | | |  __/ |   
 |_|   \___/|___/\__|  \____\___/|_| |_|\__|_|  \___/|_|_|\___|_|   
                                                                    
/*////////// ------------ POST CONTROLLER ------------ //////////*/                
var postController = function ( $scope, $rootScope, $window, pwData ) {

    // GENERATE SHARE LINK
    if(
        typeof $window.site_info !== 'undefined' &&
        typeof $window.current_user !== 'undefined' &&
        typeof $scope.post !== 'undefined'
        ){
        $scope.share_link = $window.site_info.url + "/?u=" + $window.current_user.ID + "&p=" + $scope.post.ID;
    }
    
    // Set class via ng-class, of current assigned taxonomy (topic)
    // TODO : Break this into RS theme specific scripts 
    $scope.setClass = function(){
        // Set the color topic class
        if( typeof $scope.post !== 'undefined' )
            if( typeof $scope.post.taxonomy !== 'undefined' )
                if ( typeof $scope.post.taxonomy.topic !== 'undefined' ){
                    angular.forEach( $scope.post.taxonomy.topic, function( term ){
                        if( term.parent == "0" )
                            $scope.topic = term.slug;
                    });
                }
    };
    $scope.setClass();

    // Toggles class="expaned", used with ng-class="expanded" 
    $scope.expanded = "";
    $scope.toggleExpanded = function(){
        ( $scope.expanded == "" ) ? $scope.expanded = "expanded" : $scope.expanded = "" ;
    };

    // Update the contents of post after Quick Edit
    $rootScope.$on('postUpdated', function(event, post_id) {
        if ( $scope.post.ID == post_id ){
            var args = {
                post_id: post_id,
                fields: 'all'
            };
            pwData.pw_get_post(args).then(
                // Success
                function(response) {

                    if (response.status==200) {
                        //$log.info('pwPostLoadController.pw_load_post Success',response.data);                     
                        $scope.post = response.data;

                        // Update Classes
                        $scope.setClass();
                    
                    } else {
                        // handle error
                    }
                },
                // Failure
                function(response) {
                    // $log.error('pwFeedController.pw_live_feed Failure',response);
                    // TODO Show User Friendly Message
                }
            );

        }
    });

};




/*
  _   _       _                 _      _             _             
 | | | |_ __ | | ___   __ _  __| |    / \__   ____ _| |_ __ _ _ __ 
 | | | | '_ \| |/ _ \ / _` |/ _` |   / _ \ \ / / _` | __/ _` | '__|
 | |_| | |_) | | (_) | (_| | (_| |  / ___ \ V / (_| | || (_| | |   
  \___/| .__/|_|\___/ \__,_|\__,_| /_/   \_\_/ \__,_|\__\__,_|_|   
       |_|                                                         
/*/////////// ------------ UPLOAD AVATAR ------------ ///////////*/  

var avatarCtrl = function ( $scope, $rootScope, pwData, $timeout ) {

    $scope.status = "empty";

    $scope.updateAvatarImage = function( selected_image_obj ){
        // Set the image object into the model
        $scope.status = "saving";
        var args = {
            user_id: $scope.user_id,
            image_object: selected_image_obj,
        };
        pwData.pw_set_avatar( args ).then(
                // Success
                function(response) {    
                    $scope.avatar_image = response.data;
                    $scope.status = "done";
                    // Load object into scope
                    //$scope.loadAvatarObj( $scope.user_id );
                },
                // Failure
                function(response) {
                    //alert('Error loading terms.');
                }
            );
        //$scope.avatar_image = selected_image;
        $scope.status = "setting";
    };


    $scope.loadAvatarObj = function( user_id ){
        $scope.status = "loading";
        
        // Hit pwData.pw_get_avatar with args
        var args = {
            user_id: user_id
        };
        pwData.pw_get_avatar( args ).then(
                // Success
                function(response) {    
                    //alert(response.data);
                    //alert(JSON.stringify(response.data));
                    $scope.avatar_image = response.data;
                    $scope.status = "done";
                },
                // Failure
                function(response) {
                    //alert('JS loading avatar.');
                }
            );

    };
    
    // Watch on the value of user_id
    $scope.$watch( "user_id",
        function (){
            if( typeof $scope.user_id !== 'undefined'  )
                $scope.loadAvatarObj( $scope.user_id );
        });    

    $scope.deleteAvatarImage = function(){
        // Set the image object into the model
        $scope.status = "deleting";

        var selected_image_obj = {
            id: $scope.avatar_image.id,
            action: 'delete',
        };

        var args = {
            user_id: $scope.user_id,
            image_object: selected_image_obj,
        };

        pwData.pw_set_avatar( args ).then(
                // Success
                function(response) {    
                    //alert(response.data);
                    //alert(JSON.stringify(response.data));
                    if( response.data == true )
                        $scope.avatar_image = {};
    
                    $scope.status = "done";

                },
                // Failure
                function(response) {
                    //alert('Error deleting avatar.');
                }
            );
        //$scope.avatar_image = selected_image;
        $scope.status = "setting";
    };

    $scope.loadAvatarImg = function( user_id, size ){
        $scope.status = "loading";
        // Hit pwData.pw_get_avatar with args

    };
};





/*
  _   _                     ____  _                         
 | | | |___  ___ _ __   _  / ___|(_) __ _ _ __  _   _ _ __  
 | | | / __|/ _ \ '__| (_) \___ \| |/ _` | '_ \| | | | '_ \ 
 | |_| \__ \  __/ |     _   ___) | | (_| | | | | |_| | |_) |
  \___/|___/\___|_|    (_) |____/|_|\__, |_| |_|\__,_| .__/ 
                                    |___/            |_|    
/*/////////// ------------ SIGNUP ------------ ///////////*/  

var pwUserSignup = function ( $scope, $rootScope, pwData, $timeout, $log, pwUsers ) {

    // SETUP
    $scope.formData = {
        name:"",
        username:"",
        password:"",
        email:"",
    };

    $scope.fieldStatus = {
        username:'empty',
        password:'empty',
        email:'empty',
    };

    $scope.mode = "signup";
    $scope.status = "done";

    // VALIDATE : Username
    $scope.validateUsername = function( username ){
        if(
            !($scope.signupForm.username.$error.minLength) &&
            !($scope.signupForm.username.$error.maxLength) &&
            !($scope.signupForm.username.$error.pattern) &&
            $scope.signupForm.username.$dirty
            ){
            if( username == '' )
                username = '0';
            var query_args = {
                number:1,
                search_columns:['user_nicename'],
                fields:['user_nicename'],
                search: username,
            };
            $scope.fieldStatus.username = "busy";
            $scope.signupForm.username.$setValidity('available',false);
            pwData.wp_user_query( query_args ).then(
                // Success
                function(response) {
                    $log.info('QUERY : ' + username , response.data.results);
                    // If the username is already taken
                    if ( response.data.results.length > 0 ){
                        if( response.data.results[0].user_nicename === username ){
                            // Set Field Status
                            $scope.fieldStatus.username = "taken";
                            // Set Validity to FALSE
                            $scope.signupForm.username.$setValidity('available',false);
                        }
                        else{
                            $scope.fieldStatus.username = "done";
                            $scope.signupForm.username.$setValidity('available',true);
                        }
                    }
                    else {
                        $scope.fieldStatus.username = "done";
                        $scope.signupForm.username.$setValidity('available',true);
                    }
                },
                // Failure
                function(response) {
                    throw { message:'Error: ' + JSON.stringify(response)};
                }
            );
        }
        else {
            $scope.fieldStatus.username = "done";
        }
    };
    // WATCH : value of username
    if ( typeof $scope.formData.username !== 'undefined' )
        $scope.$watch( "formData.username", function (){
            // When it changes, emit it's value to the parent controller
            $scope.validateUsername( $scope.formData.username );
            }, 1 );


    // VALIDATE : Email Doesn't Exist
    $scope.validateEmail = function( email ){
        if(
            !($scope.signupForm.email.$error.required) &&
            !($scope.signupForm.email.$error.email) &&
            $scope.signupForm.email.$dirty
            ){
            $scope.signupForm.email.$setValidity('available',false);
            if( email == '' )
                email = '0';
            var query_args = {
                number:1,
                search_columns:['user_email'],
                fields:['user_email'],
                search: email,
            };
            $scope.fieldStatus.email = "busy";
            pwData.wp_user_query( query_args ).then(
                // Success
                function(response) {
                    $log.info('QUERY : ' + email , response.data.results);
                    // If the email is already taken
                    if ( response.data.results.length > 0 ){
                        if( response.data.results[0].user_email === email ){
                            // Set Field Status
                            $scope.fieldStatus.email = "taken";
                            // Set Validity to FALSE
                            $scope.signupForm.email.$setValidity('available',false);
                        }
                        else{
                            $scope.fieldStatus.email = "done";
                            $scope.signupForm.email.$setValidity('available',true);
                        }
                    }
                    else {
                        $scope.fieldStatus.email = "done";
                        $scope.signupForm.email.$setValidity('available',true);
                    }
                },
                // Failure
                function(response) {
                    throw { message:'Error: ' + JSON.stringify(response)};
                }
            );
        }
        else {
            $scope.fieldStatus.email = "done";
        }
    };
    // WATCH : value of email
    if ( typeof $scope.formData.email != 'undefined' )
        $scope.$watch( "formData.email", function (){
            // When it changes, emit it's value to the parent controller
            $scope.validateEmail( $scope.formData.email );
            }, 1 );


    // INSERT USER
    $scope.insertUser = function(){        
        $scope.status = "inserting";
        var signupForm = $scope.signupForm;
        var userdata = {
            user_login:signupForm.username.$modelValue,
            user_pass:signupForm.password.$modelValue,
            user_email:signupForm.email.$modelValue,
            display_name:signupForm.name.$modelValue
        };
        $log.info('INSERTING USER : ' , userdata);
        pwData.pw_insert_user( userdata ).then(
            // Success
            function(response) {
                $log.info('USER INSERT SUCCESSFUL : ' , response.data);
                if ( typeof response.data.ID !== 'undefined' ){
                    if ( !isNaN( response.data.ID ) ){
                        // Insert get_userdata object into scope
                        $scope.userdata = response.data;
                        $scope.status = "done";
                        $scope.mode = "activate";
                    }
                }
            },
            // Failure
            function(response) {
                throw { message:'Error: ' + JSON.stringify(response)};
            }
        );
    };

    $scope.sendActivationLink = function( user_email ){
        pwUsers.sendActivationLink($scope, user_email);
    };

}

/*///////// ------- SIGNUP FORM : RE-ENTER PASSWORD VALIDATION ------- /////////*/  
angular.module('UserValidation', []).directive('validPasswordC', function () {
    return {
        require: 'ngModel',
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function (viewValue, $scope) {
                var noMatch = viewValue != scope.signupForm.password.$viewValue;
                ctrl.$setValidity('noMatch', !noMatch)
            })
        }
    }
})





/*
  _   _                        _        _   _            _       
 | | | |___  ___ _ __   _     / \   ___| |_(_)_   ____ _| |_ ___ 
 | | | / __|/ _ \ '__| (_)   / _ \ / __| __| \ \ / / _` | __/ _ \
 | |_| \__ \  __/ |     _   / ___ \ (__| |_| |\ V / (_| | ||  __/
  \___/|___/\___|_|    (_) /_/   \_\___|\__|_| \_/ \__,_|\__\___|

/*////////////// ------------ ACTIVATE ------------ //////////////*/  

var pwUserActivate = function ( $scope, $rootScope, pwData, $timeout, $log, pwUsers ) {

    $scope.status = "done";

    $scope.formData = {
        email:"",
    };

    $scope.fieldStatus = {
        email:'empty',
    };

    $scope.sendActivationLink = function( user_email ){
        pwUsers.sendActivationLink($scope, user_email);
    };

    $scope.activateUserKey = function( auth_key ){        
        $scope.mode = "activate";
        $scope.status = "activating";
        //alert(auth_key);
        $scope.auth_key = auth_key;
        if( typeof $scope.auth_key_animate === 'undefined' )
            $scope.auth_key_animate = " ";
        pwData.pw_activate_user( auth_key ).then(
            // Success
            function(response) {
                $log.info('ACTIVATION RETURN : ', response.data);
                
                if ( typeof response.data.data !== 'undefined' ){
                    $scope.userdata = response.data;
                    $scope.animateAuthKey();
                }
                else{
                    //alert('error');
                    $scope.mode = "error";
                }
            },
            // Failure
            function(response) {
                throw { message:'Error: ' + JSON.stringify(response)};
            }
        );
    };

    $scope.animateAuthKey = function(){
        var position = ( $scope.auth_key_animate.length ) ;
        if( ($scope.auth_key_animate.length + 1 ) <= $scope.auth_key.length ){
            $scope.auth_key_animate = $scope.auth_key.slice(0, ( position + 1 )) + "|";
            $timeout(function() {
              $scope.animateAuthKey();
            }, 50);
        }
        else{
            $scope.status = "activated";
            $timeout(function() {
              $scope.mode = "welcome";
            }, 2000);

        }
    };
    $scope.resendActivationKeyScreen = function(){
        $scope.mode = "resend";
        //$scope.formData = {};
    };

    // VALIDATE : Email Exists
    $scope.validateEmailExists = function( email ){
        var formName = "resendKey";
        var callback = "validateEmailExistsCallback";
        pwUsers.validateEmailExists( $scope, email, formName, callback );
    };

    // CALLBACK : Process Query Response
    $scope.validateEmailExistsCallback = function( response ){
        // If the email is already taken
        if ( response.data.results.length > 0 ){
            // If they are not a subscriber (they are already activated)
            if( response.data.results[0].roles[0] != 'subscriber' ){
                // Set Field Status
                $scope.fieldStatus.email = "activated";
                // Set Validity to FALSE
                $scope[formName].email.$setValidity('exists',false);
            }
            else{
                $scope.fieldStatus.email = "done";
                $scope[formName].email.$setValidity('exists',true);
            }
        }
        else {
            $scope.fieldStatus.email = "unregistered";
            $scope[formName].email.$setValidity('exists',false);
        }
    };

    // WATCH : value of email
    if ( typeof $scope.formData.email != 'undefined' )
        $scope.$watch( "formData.email", function (){
            // When it changes, emit it's value to the parent controller
            $scope.validateEmailExists( $scope.formData.email );
            }, 1 );

}



/*
  ____                _     ____                                     _ 
 |  _ \ ___  ___  ___| |_  |  _ \ __ _ ___ _____      _____  _ __ __| |
 | |_) / _ \/ __|/ _ \ __| | |_) / _` / __/ __\ \ /\ / / _ \| '__/ _` |
 |  _ <  __/\__ \  __/ |_  |  __/ (_| \__ \__ \\ V  V / (_) | | | (_| |
 |_| \_\___||___/\___|\__| |_|   \__,_|___/___/ \_/\_/ \___/|_|  \__,_|
                                                                       
/*////////////// ------------ RESET PASSWORD ------------ //////////////*/  

var pwUserPasswordReset = function ( $scope, $rootScope, pwData, $timeout, $log, pwUsers ) {

    $scope.status = "done";
    $scope.formName = "resetPassword";
    $scope.formData = {
        email:"",
        password:"",
    };
    $scope.fieldStatus = {
        email:'empty',
    };

    $scope.pwPasswordResetEmailInputScreen = function(){
        $scope.mode = "emailInput";
    };

    $scope.pwPasswordResetScreen = function(auth_key){
        $scope.mode = "resetPassword";
        $scope.authKey = auth_key;
    };
    
    $scope.sendResetPasswordLink = function( email ){
        pwUsers.sendResetPasswordLink($scope, email);
    };

    // VALIDATE : Email Exists
    $scope.validateEmailExists = function( email ){
        
        var callback = "validateEmailExistsCallback";
        pwUsers.validateEmailExists( $scope, email, $scope.formName, callback );

    };

    // CALLBACK : Process Query Response
    $scope.validateEmailExistsCallback = function( response ){
        // If the email is already taken
        if ( response.data.results.length > 0 ){
            // If they are not a subscriber (they are already activated)
            $scope[$scope.formName].email.$setValidity('exists',true);
            $scope.fieldStatus.email = "done";
        }
        else {
            $scope.fieldStatus.email = "unregistered";
            $scope[$scope.formName].email.$setValidity('exists',false);
        }
    };

    // WATCH : value of email
    if ( typeof $scope.formData.email != 'undefined' )
        $scope.$watch( "formData.email", function (){
            // When it changes, emit it's value to the parent controller
            //alert('change');
            $scope.validateEmailExists( $scope.formData.email );
            }, 1 );

    $scope.submitNewPassword = function( password ){
        //alert($scope.authKey);
        $scope.status = "busy";
        var userdata = {
            user_pass: password,
            auth_key: $scope.authKey
        };

        //alert(JSON.stringify(userdata));
        $scope.signupForm.$setValidity('busy',false);

        $log.info('SENDING NEW PASSWORD : ' , userdata);
        pwData.reset_password_submit( userdata ).then(
            // Success
            function(response) {
                $log.info('NEW PASSWORD RETURN : ' , response.data);
                if ( !isNaN( response.data.ID ) ){
                    $scope.status = "done";
                    $timeout(function() {
                      $scope.mode = "login";
                    }, 1000);
                    $scope.signupForm.$setValidity('success',true);
                } else {

                    $scope.status = "error";
                    $timeout(function() {
                        $scope.status = "done";
                        $scope.signupForm.$setValidity('busy',true);
                        }, 5000);

                }
            },
            // Failure
            function(response) {
                throw { message:'Error: ' + JSON.stringify(response)};
            }
        );

        
    };

}



/*
   _                      _   _                   
  | |   _   _ ____      _| | | |___  ___ _ __ ___ 
 / __) (_) | '_ \ \ /\ / / | | / __|/ _ \ '__/ __|
 \__ \  _  | |_) \ V  V /| |_| \__ \  __/ |  \__ \
 (   / (_) | .__/ \_/\_/  \___/|___/\___|_|  |___/
  |_|      |_|                                    

/*///////// ------- SERVICE : PW USERS ------- /////////*/  
postworld.service('pwUsers', ['$log', '$timeout', 'pwData', function ($log, $timeout, pwData) {
    return{
        sendActivationLink : function($scope, user_email){
            $scope.status = "busy";
            var userdata = {
                email: user_email,
            };
            $log.info('SENDING ACTIVATION LINK : ' , userdata);
            pwData.send_activation_link( userdata ).then(
                // Success
                function(response) {
                    $log.info('ACTIVATION LINK RETURN : ' , response.data);
                    if ( response.data == true ){
                        $scope.status = "success";
                        $timeout(function() {
                          $scope.status = "done";
                        }, 10000);
                    }
                },
                // Failure
                function(response) {
                    throw { message:'Error: ' + JSON.stringify(response)};
                }
            );
        },
        sendResetPasswordLink : function($scope, user_email){
            $scope.status = "busy";
            var userdata = {
                email: user_email,
            };
            $log.info('SENDING ACTIVATION LINK : ' , userdata);
            pwData.send_reset_password_link( userdata ).then(
                // Success
                function(response) {
                    $log.info('ACTIVATION LINK RETURN : ' , response.data);
                    if ( response.data == true ){
                        $scope.status = "success";
                        $timeout(function() {
                          $scope.status = "done";
                        }, 10000);
                    }
                },
                // Failure
                function(response) {
                    throw { message:'Error: ' + JSON.stringify(response)};
                }
            );
        },
        validateEmailExists : function ( $scope, email, formName, callback ){
            if(
                !($scope[formName].email.$error.required) &&
                !($scope[formName].email.$error.email) &&
                $scope[formName].email.$dirty
                ){
                $scope[formName].email.$setValidity('exists',false);
                if( email == '' )
                    email = '0';
                var query_args = {
                    number:1,
                    search_columns:['user_email'],
                    fields:'all',
                    search: email,
                };
                $scope.fieldStatus.email = "busy";
                pwData.wp_user_query( query_args ).then(
                    // Success
                    function(response) {
                        //alert(JSON.stringify( response.data.results ));
                        $log.info('QUERY : ' + email , response.data.results);

                        // Return reponse data to the specified callback function in the original scope
                        $scope[callback]( response );
                    },
                    // Failure
                    function(response) {
                        throw { message:'Error: ' + JSON.stringify(response)};
                    }
                );
            }
            else {
                $scope.fieldStatus.email = "done";
            }

        },
    }
}]);





/*
     __     __  ____    _    _   _ ____  ____   _____  __     __     __
    / /    / / / ___|  / \  | \ | |  _ \| __ ) / _ \ \/ /    / /    / /
   / /    / /  \___ \ / _ \ |  \| | | | |  _ \| | | \  /    / /    / / 
  / /    / /    ___) / ___ \| |\  | |_| | |_) | |_| /  \   / /    / /  
 /_/    /_/    |____/_/   \_\_| \_|____/|____/ \___/_/\_\ /_/    /_/   
                                                                       
*/





