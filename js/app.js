/*
  ____           _                      _     _ 
 |  _ \ ___  ___| |___      _____  _ __| | __| |
 | |_) / _ \/ __| __\ \ /\ / / _ \| '__| |/ _` |
 |  __/ (_) \__ \ |_ \ V  V / (_) | |  | | (_| |
 |_|   \___/|___/\__| \_/\_/ \___/|_|  |_|\__,_|
                                                
Framework by : AngularJS
GitHub Repo  : https://github.com/phongmedia/postworld/
ASCII Art by : http://patorjk.com/software/taag/#p=display&f=Standard
*/

'use strict';
var feed_settings = [];

var postworld = angular.module('postworld', ['ngResource','ngRoute', 'ngSanitize', 'infinite-scroll', 'ui.bootstrap', 'monospaced.elastic','TimeAgoFilter','TruncateFilter','UserValidation','pwFilters', '$strap.directives' ])
.config(function ($routeProvider, $locationProvider, $provide, $logProvider) {   

    var plugin_url = jsVars.pluginurl;

    ////////// ROUTE PROVIDERS //////////
    $routeProvider.when('/live-feed-1/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed1Widget.html',                           
        });
    $routeProvider.when('/live-feed-2/',    
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed2Widget.html',
            // reloadOnSearch: false,                
        });
    $routeProvider.when('/live-feed-2-feeds/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed3Widget.html',                
        });
    $routeProvider.when('/live-feed-with-ads/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed6Widget.html',                
        });
    $routeProvider.when('/live-feed-2-feeds-auto/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed4Widget.html',                
        });
    $routeProvider.when('/live-feed-params/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLiveFeed5Widget.html',                
        });
    $routeProvider.when('/load-feed-1/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed1Widget.html',                
        });
    $routeProvider.when('/load-feed-2/',
        {
            template: '<h2>Coming Soon</h2>',               
        });
    $routeProvider.when('/load-feed-2-feeds/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed3Widget.html',                
        });
    $routeProvider.when('/load-feed-cached-outline/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed4Widget.html',                
        });
    $routeProvider.when('/load-feed-ads/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLoadFeed5Widget.html',                
        });
    $routeProvider.when('/load-panel/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLoadPanelWidget.html',                
        });
    $routeProvider.when('/register-feed/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwRegisterFeedWidget.html',             
        });
    $routeProvider.when('/home/',
        {
                template: '<h2>Coming Soon</h2>',               
        });
    $routeProvider.when('/edit-post/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/editPost.html',                
        });

    $routeProvider.when('/load-comments/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLoadCommentsWidget.html',             
        });            
    $routeProvider.when('/embedly/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwEmbedlyWidget.html',             
        });            
    $routeProvider.when('/load-post/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwLoadPostWidget.html',             
        });            
    $routeProvider.when('/o-embed/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwEmbedWidget.html',             
        });       
    $routeProvider.when('/media-modal/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/mediaModal.html',             
        });  
    $routeProvider.when('/post-link/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/postLink.html',             
        });  
    $routeProvider.when('/test-post/',
        {
            templateUrl: plugin_url+'/postworld/templates/samples/pwTestWidget.html',             
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
    // $routeProvider.otherwise({redirectTo: '/home/'});

    // SHOW / HIDE DEBUG LOGS IN CONSOLE
    // Comment out for development
    $logProvider.debugEnabled(false);

});


 
/*
  ____              
 |  _ \ _   _ _ __  
 | |_) | | | | '_ \ 
 |  _ <| |_| | | | |
 |_| \_\\__,_|_| |_|        
*/
postworld.run(function($rootScope, $window, $templateCache, $log, pwData) {    
        // TODO move getting templates to app startup
        pwData.pw_get_templates(null).then(function(value) {
            // TODO should we create success/failure responses here?
            // resolve pwData.templates
            pwData.templates.resolve(value.data);
            pwData.templatesFinal = value.data;
            //console.log('postworld RUN getTemplates=',pwData.templatesFinal);

            // BROADCAST HERE - TEMPLATES LOADED
            $rootScope.$broadcast('pwTemplatesLoaded', true);

          });    

    // TODO remove in production
    /*
    $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
    });
    */
   //$rootScope.current_user = $window.pwGlobals.current_user;
   //$log.debug('Current user: ', $rootScope.current_user );

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
               "hilight":{
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
postworld.service('pwPostOptions', ['$window','$log', 'siteOptions', 'pwData',
                            function ($window, $log, $siteOptions, $pwData) {
    // Do one AJAX call here which returns all the options
    return{
        getTaxTerms: function($scope, tax_obj){ // , tax_obj
            
            if ( typeof tax_obj === 'undefined' )
                var tax_obj = "tax_terms";

            var args = $siteOptions.taxOutlineMixed();
            $pwData.taxonomies_outline_mixed( args ).then(
                // Success
                function(response) {    
                    $scope[tax_obj] = response.data;
                    //alert(JSON.stringify(response.data));
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
        	if ((!$window.pwGlobals.current_user) || (!$window.pwGlobals.current_user)) {
        		return;
        	}        	
            // GET ROLE
            var current_user_role = $window.pwGlobals.current_user.roles[0];

            // DEFINE : POST STATUS OPTIONS
            var post_status_options = {
                "publish" : "Published",
                "draft" : "Draft",
                "pending" : "Pending"
            };

            // DEFINE : OPTIONS PER ROLE > POST TYPE
            // TODO : EXTRACT INTO SITE OPTIONS
            var post_status_role_options = {
                "administrator" : {
                    "feature" : ['publish','draft','pending'],
                    "blog" : ['publish','draft','pending'],
                    "event" : ['publish','draft','pending'],
                    "announcement" : ['publish','draft','pending'],
                    "link" : ['publish'],
                },
                "editor" : {
                    "feature" : ['publish','draft','pending'],
                    "blog" : ['publish','draft','pending'],
                    "event" : ['publish','draft','pending'],
                    "announcement" : [],
                    "link" : ['publish'],
                },
                "author" : {
                    "feature" : ['draft','pending'],
                    "blog" : ['publish','draft'],
                    "event" : ['publish','draft','pending'],
                    "announcement" : [],
                    "link" : ['publish'],
                },
                "contributor" : {
                    "feature" : [],
                    "blog" : ['publish','draft'],
                    "event" : [],
                    "announcement" : [],
                    "link" : ['publish'],
                },
            };

            // BUILD OPTIONS MENU OBJECT
            var post_status_menu = {};
            var user_role_options = post_status_role_options[current_user_role][post_type];
            if( typeof user_role_options !== 'undefined' ){
                angular.forEach(  user_role_options, function( post_status_slug ){
                    post_status_menu[post_status_slug] = post_status_options[post_status_slug];
                });
                return post_status_menu;
            } else{
                // DEFAULT
                return {
                    "publish" : "Published",
                };
            }

        },
        pwGetPostFormatOptions: function(){
            return {
                "standard" : "Standard",
                "link" : "Link",
                "video" : "Video",
                "audio" : "Audio",
            };
        },
        pwGetPostClassOptions: function(){
            return {
                "contributor" : "Contributor",
                "author" : "Author",
                "members" : "Members Only",
            };
        },
        pwGetPostYearOptions: function(){
            return [
                "2007",
                "2008",
                "2009",
                "2010",
                "2011",
                "2012",
                "2013",
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
                    number:"1"
                },
                {
                    name:"February",
                    number:"2"
                },
                {
                    name:"March",
                    number:"3"
                },
                {
                    name:"April",
                    number:"4"
                },
                {
                    name:"May",
                    number:"5"
                },
                {
                    name:"June",
                    number:"6"
                },
                {
                    name:"July",
                    number:"7"
                },
                {
                    name:"August",
                    number:"8"
                },
                {
                    name:"September",
                    number:"9"
                },
                {
                    name:"October",
                    number:"10"
                },
                {
                    name:"November",
                    number:"11"
                },
                {
                    name:"December",
                    number:"12"
                },
            ];
        },        
        pwGetPostFormatMeta: function(){
            return [
                {
                    name:"",
                    slug:"standard",
                    domains:[],
                    icon:"icon-circle-blank"
                },
                {
                    name:"Link",
                    slug:"link",
                    domains:[],
                    icon:"icon-link"
                },
                {
                    name:"Video",
                    slug:"video",
                    domains:["youtube.com/","youtu.be/","vimeo.com/","hulu.com/","ted.com/","sapo.pt/","dailymotion.com","blip.tv/","ustream.tv/",],
                    icon:"icon-youtube-play"
                },
                {
                    name:"Audio",
                    slug:"audio",
                    domains:["soundcloud.com/","mixcloud.com/","official.fm/","shoudio.com/","rdio.com/"],
                    icon:"icon-headphones"
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





/*
  ____           _     _____                   __  __                  
 |  _ \ ___  ___| |_  |_   _|   _ _ __   ___  |  \/  | ___ _ __  _   _ 
 | |_) / _ \/ __| __|   | || | | | '_ \ / _ \ | |\/| |/ _ \ '_ \| | | |
 |  __/ (_) \__ \ |_    | || |_| | |_) |  __/ | |  | |  __/ | | | |_| |
 |_|   \___/|___/\__|   |_| \__, | .__/ \___| |_|  |_|\___|_| |_|\__,_|
                            |___/|_|                                   

////////// ------------ POST TYPE MENU ------------ //////////*/
// This goes on the user dashboard under + quick add
// Giving the user quick access to create a new post of a specified post type
// TODO : Refactor to take live data from Postworld Admin Panel & registered post types

var postTypeMenu = function($scope, pwPostOptions){ 

    var postTypeOptionsMeta = [
        {
            name: "Feature",
            slug: "feature",
            icon: "icon-star",
            url: "/post/#/new/feature/"
        },
        {
            name: "Blog",
            slug: "blog",
            icon: "icon-pencil",
            url: "/post/#/new/blog/"
        },
        {
            name: "Link",
            slug: "link",
            icon: "icon-link",
            url: "/post/#/new/link/"
        },
        {
            name: "Event",
            slug: "event",
            icon: "icon-calendar",
            url: "/post/#/new/event/"
        },
        {
            name: "Announcement",
            slug: "announcement",
            icon: "icon-sun",
            url: "/post/#/new/announcement/"
        },
    ];

    // Get the post type options which the user has access to "create"
    var userPostTypeOptions = pwPostOptions.pwGetPostTypeOptions( 'create' );

    // Define the Menu Object
    $scope.userPostTypeOptionsMenu = [];

    ///// BUILD USER ACCESS POST TYPE MENU /////
    // FOREACH OPTIONS : Cycle through each option
    angular.forEach( userPostTypeOptions, function( name, slug ){
        // FOREACH META : Cycle through each meta
        angular.forEach( postTypeOptionsMeta , function( meta ){
            if( meta.slug === slug ){
                $scope.userPostTypeOptionsMenu.push(meta);
            }
        });
    });

};





/*
  ____                      _       _____ _      _     _     
 / ___|  ___  __ _ _ __ ___| |__   |  ___(_) ___| | __| |___ 
 \___ \ / _ \/ _` | '__/ __| '_ \  | |_  | |/ _ \ |/ _` / __|
  ___) |  __/ (_| | | | (__| | | | |  _| | |  __/ | (_| \__ \
 |____/ \___|\__,_|_|  \___|_| |_| |_|   |_|\___|_|\__,_|___/
                                                             
////////// ------------ SEARCH FIELDS CONTROLLER ------------ //////////*/
postworld.controller('searchFields', ['$scope', 'pwPostOptions', 'pwEditPostFilters', function($scope, $pwPostOptions, $pwEditPostFilters) {

    // POST TYPE OPTIONS
    $scope.post_type_options = $pwPostOptions.pwGetPostTypeOptions('read');
    // POST YEAR OPTIONS
    $scope.post_year_options = $pwPostOptions.pwGetPostYearOptions();
    // POST MONTH OPTIONS
    $scope.post_month_options = $pwPostOptions.pwGetPostMonthOptions();
    // POST STATUS OPTIONS
    $scope.post_status_options = $pwPostOptions.pwGetPostStatusOptions( );
    // POST FORMAT OPTIONS
    $scope.post_format_options = $pwPostOptions.pwGetPostFormatOptions();
    // POST FORMAT META
    $scope.post_format_meta = $pwPostOptions.pwGetPostFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();

    // TAXONOMY TERMS
    // Gets live set of terms from the DB
    // as $scope.tax_terms
    // TODO : VERIFY THIS WORKS
    $pwPostOptions.getTaxTerms($scope, 'tax_terms');

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
    if (($scope.$parent.feedQuery) && ($scope.$parent.feedQuery.author_name)) {
    	$scope.username = $scope.$parent.feedQuery.author_name;
    };    
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
            if ($scope.username) $scope.$emit('updateUsername', $scope.username);
        }, 1 );
    
    // Catch broadcast of username change
    $scope.$on('updateUsername', function(event, data) { 
    	if (data) $scope.username = data; 
    	});

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
   _        ____       _         _                         
  | |   _  |  _ \ ___ | | ___   / \   ___ ___ ___  ___ ___ 
 / __) (_) | |_) / _ \| |/ _ \ / _ \ / __/ __/ _ \/ __/ __|
 \__ \  _  |  _ < (_) | |  __// ___ \ (_| (_|  __/\__ \__ \
 (   / (_) |_| \_\___/|_|\___/_/   \_\___\___\___||___/___/
  |_|                                                      

/*///////// ------- SERVICE : PW USERS ------- /////////*/  
postworld.service('pwRoleAccess', ['$log', '$window', function ($log, $window) {
    return{
        setRoleAccess : function($scope){
            $scope.current_user = $window.pwGlobals.current_user;
            $scope.roles = {};
            // ESTABLISH ROLE ACCESS
            // Is the user an editor?
            ( $scope.current_user.roles[0] == 'administrator' || $scope.current_user.roles[0] == 'editor' ) ?
                $scope.roles.editor = true : $scope.roles.editor = false;

            // Is the user an author?
            ( $scope.current_user.roles[0] == 'author' ) ?
                $scope.roles.author = true : $scope.roles.author = false;

            // Is the user an contributor?
            ( $scope.current_user.roles[0] == 'contributor' ) ?
                $scope.roles.contributor = true : $scope.roles.contributor = false ;
        },
    }
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

    $scope.spinnerClass = "";

    $scope.togglePostRelationship = function( postRelationship ) {

        // Localize the viewer object
        var viewer = $scope.post.viewer;

        $scope.spinnerClass = "icon-spin";

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
                    $scope.spinnerClass = "";
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
                    $scope.spinnerClass = "";
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

var postVote = function ( $window, $rootScope, $scope, $log, pwData ) {

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
    $scope.spinnerClass = "";
    $scope.votePost = function( points ){
        // Get the voting power of the current user
        if( typeof $window.pwGlobals.current_user.postworld !== 'undefined' )
            var vote_power = parseInt($window.pwGlobals.current_user.postworld.vote_power);
        // If they're not logged in, return false
        if( typeof vote_power === 'undefined' ){
            alert("Must be logged in to vote.");
            return false;
        }
        // Define how many points have they already given to this post
        var has_voted = parseInt($scope.post.viewer.has_voted);
        // Define how many points will be set
        var setPoints = ( has_voted + points );
        // If set points exceeds vote power
        if( Math.abs(setPoints) > vote_power ){
            setPoints = (vote_power * points);
            //alert( "Normalizing : " + setPoints );
        }
        // Setup parameters
        var args = {
            post_id: $scope.post.ID,
            points: setPoints,
        };
        // Set Status
        $scope.voteStatus = "busy";
        $scope.spinnerClass = "icon-spin";
        // AJAX Call 
        pwData.set_post_points ( args ).then(
            // ON : SUCCESS
            function(response) {    
                //alert( JSON.stringify(response.data) );
                // RESPONSE.DATA FORMAT : {"point_type":"post","user_id":1,"id":178472,"points_added":6,"points_total":"3"}
                $log.debug('VOTE RETURN : ' + JSON.stringify(response) );
                if ( response.data.id == $scope.post.ID ){
                    // UPDATE POST POINTS
                    $scope.post.post_points = response.data.points_total;
                    // UPDATE VIEWER HAS VOTED
                    $scope.post.viewer.has_voted = ( parseInt($scope.post.viewer.has_voted) + parseInt(response.data.points_added) ) ;
                } //else
                    //alert('Server error voting.');
                $scope.voteStatus = "done";
                $scope.spinnerClass = "";
            },
            // ON : FAILURE
            function(response) {
                $scope.voteStatus = "done";
                $scope.spinnerClass = "";
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

  $scope.launch = function (post) {
    var modalInstance = $modal.open({
      templateUrl: pwData.pw_get_template('panels','','media_modal'),
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
        $log.debug('Modal dismissed at: ' + new Date());
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

///// DIRECTIVE /////


postworld.directive( 'launchMediaModal', ['$sce',function($scope, $sce){
    return { 
        controller: 'mediaModalCtrl'
    };
}]);




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
                $log.debug('embedly.embedly_call', action, url, options);
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
                    thumbnail_url: link_url_set,
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





/*///////// ------- SERVICE : PW QUICK EDIT ------- /////////*/  
postworld.service('pwQuickEdit', ['$log', '$modal', 'pwData', function ( $log, $modal, pwData ) {
    return{
        openQuickEdit : function( post ){
            console.log( "Launch Quick Edit : ", post );  
            var modalInstance = $modal.open({
              templateUrl: pwData.pw_get_template('panels','','quick_edit'),
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
                $log.debug('Modal dismissed at: ' + new Date());

            });
        },

        trashPost : function ( post_id, scope ){
            if ( window.confirm("Are you sure you want to trash : \n" + scope.post.post_title) ) {
                pwData.pw_trash_post( post_id ).then(
                    // Success
                    function(response) {
                        if (response.status==200) {
                            $log.debug('Post Trashed RETURN : ',response.data);                     
                            if ( response.data == true ){
                                
                                if( typeof scope != undefined ){    
                                    var retreive_url = "/wp-admin/edit.php?post_status=trash&post_type="+scope.post.post_type;
                                    scope.post.post_status = 'trash';
                                }
                            
                            }
                            else{
                                alert( "Error trashing post : " + response.data );
                            }
                        } else {
                            // handle error
                        }
                    },
                    // Failure
                    function(response) {
                        // Failed Delete
                    }
                );
            }
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

var quickEdit = function ($scope, $modal, $log, $window) {
    $scope.openQuickEdit = function( post ){
        console.log( "Launch Quick Edit : ", post );  
        var modalInstance = $modal.open({
          templateUrl: $window.pwGlobals.paths.plugin_url+'/postworld/templates/panels/quick_edit.html',
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
            $log.debug('Modal dismissed at: ' + new Date());
        });
    }; 
};


var quickEditInstanceCtrl = function ($scope, $rootScope, $sce, $modalInstance, post, pwData, $timeout, pwQuickEdit) {
    
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

    // TRASH POST
    $scope.trashPost = function(){
        pwQuickEdit.trashPost($scope.post.ID, $scope);
    }; 


    // WATCH FOR TRASHED
    // Close Modal
    // Set Parent post_status = trash

    // Watch on the value of user_id
    $scope.$watch( "post.post_status",
        function (){
            if( $scope.post.post_status == 'trash'  )
                $modalInstance.dismiss('close');
        }); 

};




/*
  ____           _      ____            _             _ _           
 |  _ \ ___  ___| |_   / ___|___  _ __ | |_ _ __ ___ | | | ___ _ __ 
 | |_) / _ \/ __| __| | |   / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
 |  __/ (_) \__ \ |_  | |__| (_) | | | | |_| | | (_) | | |  __/ |   
 |_|   \___/|___/\__|  \____\___/|_| |_|\__|_|  \___/|_|_|\___|_|   
                                                                    
/*////////// ------------ POST CONTROLLER ------------ //////////*/                
var postController = function ( $scope, $rootScope, $window, $sce, pwData ) {

    // Define backup source for 'post' object 
    if( typeof $scope.post === 'undefined' ){
        if( typeof $window.post != 'undefined' ){
            $scope.post = $window.post;
        }
    }

    // Trust the post_content as HTML
    if( typeof $scope.post.post_content !== 'undefined' ){
        $scope.post.post_content = $sce.trustAsHtml($scope.post.post_content);
    }

    // IMPORT LANGUAGE
    if(
        typeof $window.pwGlobals.paths !== 'undefined' &&
        typeof $window.pwGlobals.language !== 'undefined' &&
        typeof $window.pwGlobals.current_user !== 'undefined' &&
        typeof $scope.post !== 'undefined'
        ){
        $scope.language = $window.pwGlobals.language;
        $scope.current_user_id = $window.pwGlobals.current_user.ID;
        // GENERATE  SHARE LINK
        $scope.share_link = $window.pwGlobals.paths.home_url + "/?u=" + $window.pwGlobals.current_user.ID + "&p=" + $scope.post.ID;
    }
    //alert( JSON.stringify( $scope.language ) );
    
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
    var clickTip = "Click to expand";
    $scope.clickTip = clickTip;
    $scope.toggleExpanded = function(){
        ( $scope.expanded == "" ) ? $scope.expanded = "expanded" : $scope.expanded = "" ;
        ( $scope.clickTip != "" ) ? $scope.clickTip = "" : $scope.clickTip = clickTip ;
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
                        //$log.debug('pwPostLoadController.pw_load_post Success',response.data);                     
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
        agreement:""
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
                    $log.debug('QUERY : ' + username , response.data.results);
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
                    $log.debug('QUERY : ' + email , response.data.results);
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
        $log.debug('INSERTING USER : ' , userdata);
        pwData.pw_insert_user( userdata ).then(
            // Success
            function(response) {
                $log.debug('USER INSERT SUCCESSFUL : ' , response.data);
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
                $log.debug('ACTIVATION RETURN : ', response.data);
                
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

        $log.debug('SENDING NEW PASSWORD : ' , userdata);
        pwData.reset_password_submit( userdata ).then(
            // Success
            function(response) {
                $log.debug('NEW PASSWORD RETURN : ' , response.data);
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
            $log.debug('SENDING ACTIVATION LINK : ' , userdata);
            pwData.send_activation_link( userdata ).then(
                // Success
                function(response) {
                    $log.debug('ACTIVATION LINK RETURN : ' , response.data);
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
            $log.debug('SENDING ACTIVATION LINK : ' , userdata);
            pwData.send_reset_password_link( userdata ).then(
                // Success
                function(response) {
                    $log.debug('ACTIVATION LINK RETURN : ' , response.data);
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
                        $log.debug('QUERY : ' + email , response.data.results);

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
  _____ _ _ _                
 |  ___(_) | |_ ___ _ __ ___ 
 | |_  | | | __/ _ \ '__/ __|
 |  _| | | | ||  __/ |  \__ \
 |_|   |_|_|\__\___|_|  |___/

/*////////////// ------------ FILTERS ------------ //////////////*/  

angular.module('pwFilters', []).filter('htmlToPlaintext', function() {
    return function(text) {
        return String(text).replace(/<(?:.|\n)*?>/gm, '');
    };
});





/*
     _                      _              ____  _                   
    / \   _ __   __ _ _   _| | __ _ _ __  / ___|| |_ _ __ __ _ _ __  
   / _ \ | '_ \ / _` | | | | |/ _` | '__| \___ \| __| '__/ _` | '_ \ 
  / ___ \| | | | (_| | |_| | | (_| | |     ___) | |_| | | (_| | |_) |
 /_/   \_\_| |_|\__, |\__,_|_|\__,_|_|    |____/ \__|_|  \__,_| .__/ 
                |___/                                         |_|                                                                      

 * AngularStrap - Twitter Bootstrap directives for AngularJS
 * @version v0.7.5 - 2013-07-21
 * @link http://mgcrea.github.com/angular-strap
 * @author Olivier Louvignes <olivier@mg-crea.com>
 * @license MIT License, http://www.opensource.org/licenses/MIT
 */
angular.module('$strap.config', []).value('$strapConfig', {});
angular.module('$strap.filters', ['$strap.config']);
angular.module('$strap.directives', ['$strap.config']);
angular.module('$strap', [
  '$strap.filters',
  '$strap.directives',
  '$strap.config'
]);
'use strict';
angular.module('$strap.directives').directive('bsButton', [
  '$parse',
  '$timeout',
  function ($parse, $timeout) {
    return {
      restrict: 'A',
      require: '?ngModel',
      link: function postLink(scope, element, attrs, controller) {
        if (controller) {
          if (!element.parent('[data-toggle="buttons-checkbox"], [data-toggle="buttons-radio"]').length) {
            element.attr('data-toggle', 'button');
          }
          var startValue = !!scope.$eval(attrs.ngModel);
          if (startValue) {
            element.addClass('active');
          }
          scope.$watch(attrs.ngModel, function (newValue, oldValue) {
            var bNew = !!newValue, bOld = !!oldValue;
            if (bNew !== bOld) {
              $.fn.button.Constructor.prototype.toggle.call(button);
            } else if (bNew && !startValue) {
              element.addClass('active');
            }
          });
        }
        if (!element.hasClass('btn')) {
          element.on('click.button.data-api', function (ev) {
            element.button('toggle');
          });
        }
        element.button();
        var button = element.data('button');
        button.toggle = function () {
          if (!controller) {
            return $.fn.button.Constructor.prototype.toggle.call(this);
          }
          var $parent = element.parent('[data-toggle="buttons-radio"]');
          if ($parent.length) {
            element.siblings('[ng-model]').each(function (k, v) {
              $parse($(v).attr('ng-model')).assign(scope, false);
            });
            scope.$digest();
            if (!controller.$modelValue) {
              controller.$setViewValue(!controller.$modelValue);
              scope.$digest();
            }
          } else {
            scope.$apply(function () {
              controller.$setViewValue(!controller.$modelValue);
            });
          }
        };
      }
    };
  }
]).directive('bsButtonsCheckbox', [
  '$parse',
  function ($parse) {
    return {
      restrict: 'A',
      require: '?ngModel',
      compile: function compile(tElement, tAttrs, transclude) {
        tElement.attr('data-toggle', 'buttons-checkbox').find('a, button').each(function (k, v) {
          $(v).attr('bs-button', '');
        });
      }
    };
  }
]).directive('bsButtonsRadio', [
  '$timeout',
  function ($timeout) {
    return {
      restrict: 'A',
      require: '?ngModel',
      compile: function compile(tElement, tAttrs, transclude) {
        tElement.attr('data-toggle', 'buttons-radio');
        if (!tAttrs.ngModel) {
          tElement.find('a, button').each(function (k, v) {
            $(v).attr('bs-button', '');
          });
        }
        return function postLink(scope, iElement, iAttrs, controller) {
          if (controller) {
            $timeout(function () {
              iElement.find('[value]').button().filter('[value="' + controller.$viewValue + '"]').addClass('active');
            });
            iElement.on('click.button.data-api', function (ev) {
              scope.$apply(function () {
                controller.$setViewValue($(ev.target).closest('button').attr('value'));
              });
            });
            scope.$watch(iAttrs.ngModel, function (newValue, oldValue) {
              if (newValue !== oldValue) {
                var $btn = iElement.find('[value="' + scope.$eval(iAttrs.ngModel) + '"]');
                if ($btn.length) {
                  $btn.button('toggle');
                }
              }
            });
          }
        };
      }
    };
  }
]);

'use strict';
angular.module('$strap.directives').directive('bsPopover', [
  '$parse',
  '$compile',
  '$http',
  '$timeout',
  '$q',
  '$templateCache',
  function ($parse, $compile, $http, $timeout, $q, $templateCache) {
    $('body').on('keyup', function (ev) {
      if (ev.keyCode === 27) {
        $('.popover.in').each(function () {
          $(this).popover('hide');
        });
      }
    });
    return {
      restrict: 'A',
      scope: true,
      link: function postLink(scope, element, attr, ctrl) {
        var getter = $parse(attr.bsPopover), setter = getter.assign, value = getter(scope), options = {};
        if (angular.isObject(value)) {
          options = value;
        }
        $q.when(options.content || $templateCache.get(value) || $http.get(value, { cache: true })).then(function onSuccess(template) {
          if (angular.isObject(template)) {
            template = template.data;
          }
          if (!!attr.unique) {
            element.on('show', function (ev) {
              $('.popover.in').each(function () {
                var $this = $(this), popover = $this.data('popover');
                if (popover && !popover.$element.is(element)) {
                  $this.popover('hide');
                }
              });
            });
          }
          if (!!attr.hide) {
            scope.$watch(attr.hide, function (newValue, oldValue) {
              if (!!newValue) {
                popover.hide();
              } else if (newValue !== oldValue) {
                popover.show();
              }
            });
          }
          if (!!attr.show) {
            scope.$watch(attr.show, function (newValue, oldValue) {
              if (!!newValue) {
                $timeout(function () {
                  popover.show();
                });
              } else if (newValue !== oldValue) {
                popover.hide();
              }
            });
          }
          element.popover(angular.extend({}, options, {
            content: template,
            html: true
          }));
          var popover = element.data('popover');
          
          if( typeof popover !== 'undefined' ){
              popover.hasContent = function () {
                return this.getTitle() || template;
              };
              popover.getPosition = function () {
                var r = $.fn.popover.Constructor.prototype.getPosition.apply(this, arguments);
                $compile(this.$tip)(scope);
                scope.$digest();
                this.$tip.data('popover', this);
                return r;
              };
          }
          
          scope.$popover = function (name) {
            popover(name);
          };
          angular.forEach([
            'show',
            'hide'
          ], function (name) {
            scope[name] = function () {
              popover[name]();
            };
          });
          scope.dismiss = scope.hide;
          angular.forEach([
            'show',
            'shown',
            'hide',
            'hidden'
          ], function (name) {
            element.on(name, function (ev) {
              scope.$emit('popover-' + name, ev);
            });
          });
        });
      }
    };
  }
]);
'use strict';
angular.module('$strap.directives').directive('bsSelect', [
  '$timeout',
  function ($timeout) {
    var NG_OPTIONS_REGEXP = /^\s*(.*?)(?:\s+as\s+(.*?))?(?:\s+group\s+by\s+(.*))?\s+for\s+(?:([\$\w][\$\w\d]*)|(?:\(\s*([\$\w][\$\w\d]*)\s*,\s*([\$\w][\$\w\d]*)\s*\)))\s+in\s+(.*)$/;
    return {
      restrict: 'A',
      require: '?ngModel',
      link: function postLink(scope, element, attrs, controller) {
        var options = scope.$eval(attrs.bsSelect) || {};
        $timeout(function () {
          element.selectpicker(options);
          element.next().removeClass('ng-scope');
        });
        if (controller) {
          scope.$watch(attrs.ngModel, function (newValue, oldValue) {
            if (!angular.equals(newValue, oldValue)) {
              element.selectpicker('refresh');
            }
          });
        }
      }
    };
  }
]);



/*
 __        ___     _            _       
 \ \      / (_) __| | __ _  ___| |_ ___ 
  \ \ /\ / /| |/ _` |/ _` |/ _ \ __/ __|
   \ V  V / | | (_| | (_| |  __/ |_\__ \
    \_/\_/  |_|\__,_|\__, |\___|\__|___/
                     |___/              
//////////////// WIDGETS ////////////////*/


///// PANEL WIDGET CONTROLLER /////
postworld.controller('panelWidgetController',
    ['$scope','$timeout','pwData', '$compile',
    function($scope, $timeout, $pwData, $compile) {
    
    $scope.status = "loading";

    $scope.panel_id = "";
    $scope.setPanelID = function(panel_id){
        $scope.panel_id = panel_id;
    }
    $scope.$on('pwTemplatesLoaded', function(event, data) {
        $scope.panel_url = $pwData.pw_get_template('panels','',$scope.panel_id);
    });

}]);


///// POST SHARE REPORT /////
postworld.controller('postShareReport',
    ['$scope','$window','$timeout','pwData',
    function($scope, $window, $timeout, $pwData) {

    $scope.postShareReport = {};

    if( typeof $window.pwGlobals.current_view.post != 'undefined' ){
        $scope.post = $window.pwGlobals.current_view.post;
        var args = { "post_id" : $scope.post.post_id };
        $pwData.post_share_report( args ).then(
            // Success
            function(response) {    
                $scope.postShareReport = response.data;
                $scope.status = "done";
            },
            // Failure
            function(response) {
                //alert('Error loading report.');
            }
        );

    }
}]);


///// POST SHARE REPORT /////
postworld.controller('userShareReportOutgoing',
    ['$scope','$window','$timeout','pwData',
    function($scope, $window, $timeout, $pwData) {

    $scope.postShareReport = {};

    if( typeof $window.pwGlobals.displayed_user.user_id != 'undefined' ){

        $scope.displayed_user_id = $window.pwGlobals.displayed_user.user_id;

        var args = { "displayed_user_id" : $scope.displayed_user_id };

        $pwData.user_share_report_outgoing( args ).then(
            // Success
            function(response) {    
                $scope.shareReportMetaOutgoing = response.data;
                $scope.status = "done";
            },
            // Failure
            function(response) {
                //alert('Error loading report.');
            }
        );

    }
}]);




/*
  _                                             
 | |    __ _ _ __   __ _ _   _  __ _  __ _  ___ 
 | |   / _` | '_ \ / _` | | | |/ _` |/ _` |/ _ \
 | |__| (_| | | | | (_| | |_| | (_| | (_| |  __/
 |_____\__,_|_| |_|\__, |\__,_|\__,_|\__, |\___|
                   |___/             |___/      
////////// POSTWORLD LANGUAGE ACCESS //////////*/
postworld.directive( 'pwLanguage', [function(){
    return { 
        controller: 'pwLanguageCtrl'
    };
}]);

postworld.controller('pwLanguageCtrl',
    ['$scope','$window',
    function($scope, $window) {
    $scope.language = $window.pwGlobals.language;
    
    /*
    $scope.parseHTML = function(string){
        return $sce.parseAsHtml(string);
    };
    */

}]);




/*
     __     __  ____    _    _   _ ____  ____   _____  __     __     __
    / /    / / / ___|  / \  | \ | |  _ \| __ ) / _ \ \/ /    / /    / /
   / /    / /  \___ \ / _ \ |  \| | | | |  _ \| | | \  /    / /    / / 
  / /    / /    ___) / ___ \| |\  | |_| | |_) | |_| /  \   / /    / /  
 /_/    /_/    |____/_/   \_\_| \_|____/|____/ \___/_/\_\ /_/    /_/    
/////////////////////////////////////////////////////////////////*/


// Reduces a url string to just the base domain
postworld.filter( 'domain', function () {
  return function ( input ) {
    var matches,
        output = "",
        urls = /\w+:\/\/([\w|\.]+)/;
    matches = urls.exec( input );
    if ( matches !== null ) output = matches[1];
    return output;
  };
});






