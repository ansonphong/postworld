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
var load_comments = [];

var postworld = angular.module('postworld', ['ngResource','ngRoute', 'ngSanitize', 'infinite-scroll', 'ui.bootstrap', 'monospaced.elastic' ])
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
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLiveFeed2Widget.html',                
        });
    $routeProvider.when('/edit-post/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/editPost.html',                
        });
    $routeProvider.when('/load-comments/',
        {
            templateUrl: jsVars.pluginurl+'/postworld/templates/samples/pwLoadCommentsWidget.html',             
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
postworld.run(function($rootScope, $templateCache, pwData) {    
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
 *  Add Validations
 *  Add Dynamic Sub Forms [ng-switch]
 *  Add Embedding of URLs [embed.ly?]
 *  Will be used in URL like #/post/edit/id, #/post/new, etc...
 *  Will Switch between forms dynamically
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
postworld.service('pwPostOptions', ['$log', function ($log) {
    // Do one AJAX call here which returns all the options
    return{
        pwGetPostTypeOptions: function(){
        return {
            feature : "Features",
            blog : "Blog",
            link : "Links",
            announcement : "Announcements",
            tribe_events : "Events"
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
                    name:"Standard",
                    slug:"standard",
                    domains:[],
                    icon:"<i class='icon-circle-blank'></i>"
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
        pwGetTaxTerms: function(){
            return {
                "topic" : [
                    {
                        slug:"psyche",
                        name:"/psyche",
                        children:{
                            ancient:"Ancient Mysteries",
                            astrology:"Astrology",
                            consciousness:"Consciousness",
                            dreams:"Dreams",
                            ets:"Extraterrestrials",
                            indigenous:"Indigenous Cultures",
                            occult:"Occult",
                            psi:"Psi",
                            psychedelics:"Psychedelics",
                            psychology:"Psychology",
                            shamanism:"Shamanism",
                            spirituality:"Spirituality",
                            transformation:"Transformation",
                            psyche_misc:"Misc"
                            },
                    },
                    {
                        slug:"arts",
                        name:"/arts",
                        children:{
                            conferences:"Conferences",
                            digital_art:"Digital Art",
                            world_art:"World Art",
                            festival_culture:"Festival Culture",
                            games:"Games",
                            music:"Music",
                            performance:"Performance",
                            publications:"Publications",
                            video:"Video",
                            film:"Film",
                            misc:"Misc"
                            },
                    },
                    {
                        slug:"body",
                        name:"/body",
                        children:{
                            energy_medicine:"Energy Medicine",
                            food_nutrition:"Food & Nutrition",
                            healing:"Healing",
                            herbalism:"Herbalism",
                            homeopathy:"Homeopathy",
                            sexuality:"Sexuality",
                            slow_living:"Slow Living",
                            tantra:"Tantra",
                            yoga:"Yoga",
                            body_misc:"Misc",
                            },
                    },
                    {
                        slug:"eco",
                        name:"/eco",
                        children:{
                            animal_rights:"Animal Rights",
                            climate_change:"Climate Change",
                            conservation:"Conservation",
                            energy:"Energy",
                            environment:"Environment",
                            extinction:"Extinction",
                            gardening:"Gardening",
                            permaculture:"Permaculture",
                            sustainability:"Sustainability",
                            water:"Water",
                            eco_misc:"Misc",
                            },
                    },
                    {
                        slug:"tech",
                        name:"/tech",
                        children:{
                            biomimicry:"Biomimicry",
                            cosmology:"Cosmology",
                            design_science:"Design Science",
                            digital:"Digital",
                            diy:"DIY",
                            networks:"Networks",
                            privacy:"Privacy",
                            robotics:"Robotics",
                            singularity:"Singularity",
                            tech_misc:"Misc"
                        }
                    },
                    {
                        slug:"commons",
                        name:"/commons",
                        children:{
                            action_alerts:"Action Alerts",
                            activism:"Activism",
                            alternative_economics:"Alternative Economics",
                            collaboration:"Collaboration",
                            community:"Community",
                            crowdfunding:"Crowdfunding",
                            democracy:"Democracy",
                            drug_laws:"Drug Laws",
                            evolver:"Evolver",
                            evolver_spores:"Evolver Spores",
                            open_source:"Open Source",
                            peer_to_peer:"Peer to Peer",
                            retreats:"Retreats",
                            commons_misc:"Misc"
                        }
                    },
                ],
                'section' : [
                    {
                        slug:"psychedelic",
                        name:"Psychedelic Culture",
                    },
                    {
                        slug:"conscious_convergences",
                        name:"Conscious Convergences",
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
                        slug:"podcast",
                        name:"Podcasts",
                    },
                    {
                        slug:"edm",
                        name:"Evolver EDM",
                    },
                    {
                        slug:"evo_network",
                        name:"Evolver Network",
                    },
                    {
                        slug:"evo_learning_lab",
                        name:"Evolver Learning Lab",
                    },

                ],
                'type' : [
                    {
                        slug:"song_week",
                        name:"Song of the Week",
                        parent_name:"Hilight",
                        parent:"hilight",
                    },
                    {
                        slug:"video_week",
                        name:"Video of the Week",
                        parent_name:"Hilight",
                        parent:"hilight"
                    },
                    {
                        slug:"event_feature",
                        name:"Featured Event",
                        parent_name:"Events",
                        parent:"events"
                    },
                    {
                        slug:"event_evolver",
                        name:"Evolver Event",
                        parent_name:"Events",
                        parent:"events"
                    },
                ],
                
            };
        },

    }
}]);



/*
   _        _____    _ _ _     ____           _   
  | |   _  | ____|__| (_) |_  |  _ \ ___  ___| |_ 
 / __) (_) |  _| / _` | | __| | |_) / _ \/ __| __|
 \__ \  _  | |__| (_| | | |_  |  __/ (_) \__ \ |_ 
 (   / (_) |_____\__,_|_|\__| |_|   \___/|___/\__|
  |_|                                             
////////// ------------ EDIT POST SERVICE ------------ //////////*/  
postworld.service('pwEditPost', ['$log', function ($log) {
        return {
            pwGetPost: function(){
                return {
                    post_id : 24,
                    post_title : "Hello Space",
                    post_name : "hello_space",
                    post_type : "feature",
                    post_status : "publish",
                    post_format : "video",
                    post_class : "contributor",
                    link_url : "http://youtube.com/",
                    post_permalink : "http://realitysandwich.com/",
                    tax_input : {
                        topic : ["healing","body"],
                        section : ["psi"],
                    },
                    tags_input : "tag1, tag2, tag3",
                };
            },
            
        };
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
                        return default_format;
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
                        if ( typeof term_set.children !== 'undefined' )
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
                        angular.forEach( term_set.children, function( child_term_value, child_term_key ){
                            if ( child_term_key == tax_input[taxonomy][1] )
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
postworld.controller('searchFields', ['$scope', 'pwEditPost', 'pwPostOptions', 'pwEditPostFilters', function($scope, $pwEditPost, $pwPostOptions, $pwEditPostFilters) {

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
    $scope.tax_terms = $pwPostOptions.pwGetTaxTerms();

}]);


/*
  _____    _ _ _     ____           _   
 | ____|__| (_) |_  |  _ \ ___  ___| |_ 
 |  _| / _` | | __| | |_) / _ \/ __| __|
 | |__| (_| | | |_  |  __/ (_) \__ \ |_ 
 |_____\__,_|_|\__| |_|   \___/|___/\__|

////////// ------------ EDIT POST CONTROLLER ------------ //////////*/
postworld.controller('editPost', ['$scope', 'pwEditPost', 'pwPostOptions', 'pwEditPostFilters', function($scope, $pwEditPost, $pwPostOptions, $pwEditPostFilters) {

    $scope.pw_get_post_object = function(){
        var post_data = $pwEditPost.pwGetPost();
        // CHECK TERMS ORDER
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
    // TAXONOMY TERMS
    $scope.tax_terms = $pwPostOptions.pwGetTaxTerms();
    // POST DATA OBJECT
    $scope.post_data = $scope.pw_get_post_object();

    // SET MODE : ( new | edit )
    if ( typeof $scope.post_data.post_id !== 'undefined'  )
        $scope.mode = "edit";
    else
        $scope.mode = "new";

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

    // SAVE POST FUNCTION
    $scope.savePost = function(){
        alert( JSON.stringify( $scope.post_data ) );
    }

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
postworld.controller('postLink', ['$scope', '$timeout','pwPostOptions','pwEditPostFilters','embedly','ext',function($scope, $timeout, $pwPostOptions, $pwEditPostFilters, $embedly, $ext) {

    // Setup the intermediary Link URL
    $scope.link_url = '';

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
    $scope.tax_terms = $pwPostOptions.pwGetTaxTerms();

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

    $scope.mode = "url_input";

    // GET URL EXTRACT
    // 1. On detect paste
    // 2. On click

    $scope.extract_url = function() {
        $scope.embedly_extract = $embedly.embedly_extract( $scope.link_url );
        //alert(JSON.stringify($scope.embedly_extract));
        //alert('extract');
    }
    $scope.reset_extract = function() {
        $scope.embedly_extract = {};
        //alert(JSON.stringify($scope.embedly_extract));
        //alert('extract');
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

    // WATCH $scope.selected_image
    // UPDATE : $scope.post_data.image_url 

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


    function timeoutStatus(){
        $scope.mode =  "success";
        $scope.submit_status = "ready";
        };

    $scope.submit_status = "ready";
    $scope.savePost = function(){
        
        //alert(JSON.stringify($scope.post_data));
        $scope.submit_status = "busy";
        //$scope.mode = $timeout( timeoutStatus, 1000);
        $timeout( timeoutStatus, 1000);

    }

    // ADD ERROR SUPPORT


}]);

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
                        if ( textarea_contents.length > maxChars && textarea_contents.length > (maxChars-readMore.length) ){
                            textarea_contents = textarea_contents.slice(0, (maxChars-readMore.length)) + readMore;
                        }
                        // Insert new textarea_contents;
                        $scope[modelObjArray[0]][modelObjArray[1]] = textarea_contents;
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
   _                       _              _   _       
  | |   _    ___ _ __ ___ | |__   ___  __| | | |_   _ 
 / __) (_)  / _ \ '_ ` _ \| '_ \ / _ \/ _` | | | | | |
 \__ \  _  |  __/ | | | | | |_) |  __/ (_| |_| | |_| |
 (   / (_)  \___|_| |_| |_|_.__/ \___|\__,_(_)_|\__, |
  |_|                                           |___/ 
////////// ------------ EDIT POST SERVICE ------------ //////////*/  
postworld.service('embedly', ['$log', function ($log) {
        return {
            liveEmbedlyExtract: function( link_url ){
                // LIVE EMBEDLY EXTRACT
                // API : http://embed.ly/docs/extract/api

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
                }
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
                }
            },
            embedly_extract: function(){
                var embedly_extract_multi_object = [
                    {   // YOU TUBE
                        "provider_url": "http://www.youtube.com/", 
                        "authors": [], 
                        "provider_display": "www.youtube.com", 
                        "related": [], 
                        "favicon_url": "http://s.ytimg.com/yts/img/favicon-vfldLzJxy.ico", 
                        "keywords": [
                            {
                                "score": 32, 
                                "name": "google"
                            }, 
                            {
                                "score": 30, 
                                "name": "picasa"
                            }, 
                            {
                                "score": 30, 
                                "name": "orkut"
                            }, 
                            {
                                "score": 30, 
                                "name": "mavireck"
                            }, 
                            {
                                "score": 26, 
                                "name": "chrome"
                            }, 
                            {
                                "score": 26, 
                                "name": "nova"
                            }, 
                            {
                                "score": 20, 
                                "name": "earth"
                            }, 
                            {
                                "score": 20, 
                                "name": "gmail"
                            }, 
                            {
                                "score": 17, 
                                "name": "video"
                            }, 
                            {
                                "score": 16, 
                                "name": "youtube"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "http://www.youtube.com/watch?v=38peWm76l-U", 
                        "media": {
                            "duration": 6862, 
                            "width": 500, 
                            "html": "<iframe width=\"500\" height=\"281\" src=\"http://www.youtube.com/embed/38peWm76l-U?feature=oembed\" frameborder=\"0\" allowfullscreen></iframe>", 
                            "type": "video", 
                            "height": 281
                        }, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 3, 
                                "name": "Google"
                            }, 
                            {
                                "count": 3, 
                                "name": "Picasa"
                            }, 
                            {
                                "count": 3, 
                                "name": "Google Account"
                            }, 
                            {
                                "count": 1, 
                                "name": "PBS NOVA Finding Life Beyond Earth 2011 Legendado \\*\\* Beatiful Nature Around The World"
                            }, 
                            {
                                "count": 1, 
                                "name": "NASA"
                            }, 
                            {
                                "count": 1, 
                                "name": "Earth"
                            }
                        ], 
                        "provider_name": "YouTube", 
                        "type": "html", 
                        "description": "The groundbreaking two-hour special that reveals a spectacular new space-based vision of our planet. Produced in extensive consultation with NASA scientists, NOVA takes data from earth-observing satellites and transforms it into dazzling visual sequences, each one exposing the intricate and surprising web of forces that sustains life on earth.", 
                        "embeds": [], 
                        "images": [
                            {
                                "width": 480, 
                                "url": "http://i1.ytimg.com/vi/38peWm76l-U/hqdefault.jpg", 
                                "height": 360, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            45, 
                                            70, 
                                            75
                                        ], 
                                        "weight": 0.279296875
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            2, 
                                            4
                                        ], 
                                        "weight": 0.25537109375
                                    }, 
                                    {
                                        "color": [
                                            84, 
                                            104, 
                                            111
                                        ], 
                                        "weight": 0.16455078125
                                    }, 
                                    {
                                        "color": [
                                            111, 
                                            136, 
                                            149
                                        ], 
                                        "weight": 0.1259765625
                                    }, 
                                    {
                                        "color": [
                                            144, 
                                            173, 
                                            190
                                        ], 
                                        "weight": 0.118896484375
                                    }
                                ], 
                                "entropy": 5.90822075866, 
                                "size": 44269
                            }, 
                            {
                                "width": 48, 
                                "url": "https://lh5.googleusercontent.com/-LBcQruSPiVE/AAAAAAAAAAI/AAAAAAAAAAA/ZZy901nR234/s48-c-k/photo.jpg", 
                                "height": 48, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            172, 
                                            188, 
                                            206
                                        ], 
                                        "weight": 0.162353515625
                                    }, 
                                    {
                                        "color": [
                                            113, 
                                            146, 
                                            179
                                        ], 
                                        "weight": 0.141357421875
                                    }, 
                                    {
                                        "color": [
                                            55, 
                                            102, 
                                            146
                                        ], 
                                        "weight": 0.10546875
                                    }, 
                                    {
                                        "color": [
                                            53, 
                                            79, 
                                            82
                                        ], 
                                        "weight": 0.09716796875
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            44, 
                                            114
                                        ], 
                                        "weight": 0.05615234375
                                    }
                                ], 
                                "entropy": 6.59194584276, 
                                "size": 2316
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "English", 
                        "url": "http://www.youtube.com/watch?v=38peWm76l-U", 
                        "title": "Earth From Space HD 1080p / Nova", 
                        "published": null

                    },
                    {   // SOUND CLOUD
                        "provider_url": "http://soundcloud.com", 
                        "authors": [], 
                        "provider_display": "soundcloud.com", 
                        "related": [], 
                        "favicon_url": "http://a1.sndcdn.com/favicon.ico?3eddc42", 
                        "keywords": [
                            {
                                "score": 11, 
                                "name": "log"
                            }, 
                            {
                                "score": 11, 
                                "name": "privacy"
                            }, 
                            {
                                "score": 10, 
                                "name": "2007-2013"
                            }, 
                            {
                                "score": 10, 
                                "name": "dmt"
                            }, 
                            {
                                "score": 10, 
                                "name": "trinkly"
                            }, 
                            {
                                "score": 10, 
                                "name": "soundcloud"
                            }, 
                            {
                                "score": 9, 
                                "name": "ambient"
                            }, 
                            {
                                "score": 8, 
                                "name": "lucid"
                            }, 
                            {
                                "score": 8, 
                                "name": "whirlpool"
                            }, 
                            {
                                "score": 6, 
                                "name": "imprint"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "https://soundcloud.com/bluetech/lost-found", 
                        "media": {
                            "width": 500, 
                            "html": "<iframe width=\"500\" height=\"166\" scrolling=\"no\" frameborder=\"no\" src=\"https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F116037149&show_artwork=true&maxwidth=900\"></iframe>", 
                            "type": "rich", 
                            "height": 166
                        }, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 1, 
                                "name": "SoundCloud Ltd."
                            }
                        ], 
                        "provider_name": "SoundCloud", 
                        "type": "html", 
                        "description": "Track #4 off of my new ambient album created for Lucid Dreaming practice.", 
                        "embeds": [], 
                        "images": [
                            {
                                "width": 500, 
                                "url": "http://i1.sndcdn.com/artworks-000060496011-f1jim7-t500x500.jpg?3eddc42", 
                                "height": 500, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            234, 
                                            240, 
                                            245
                                        ], 
                                        "weight": 0.352783203125
                                    }, 
                                    {
                                        "color": [
                                            198, 
                                            181, 
                                            207
                                        ], 
                                        "weight": 0.18896484375
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            125, 
                                            171
                                        ], 
                                        "weight": 0.168212890625
                                    }, 
                                    {
                                        "color": [
                                            71, 
                                            190, 
                                            229
                                        ], 
                                        "weight": 0.129638671875
                                    }, 
                                    {
                                        "color": [
                                            18, 
                                            12, 
                                            29
                                        ], 
                                        "weight": 0.072509765625
                                    }
                                ], 
                                "entropy": 5.81182154176, 
                                "size": 102652
                            }, 
                            {
                                "width": 400, 
                                "url": "http://i1.sndcdn.com/artworks-000060496011-f1jim7-crop.jpg?3eddc42", 
                                "height": 400, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            234, 
                                            240, 
                                            245
                                        ], 
                                        "weight": 0.35986328125
                                    }, 
                                    {
                                        "color": [
                                            197, 
                                            182, 
                                            208
                                        ], 
                                        "weight": 0.196044921875
                                    }, 
                                    {
                                        "color": [
                                            0, 
                                            126, 
                                            172
                                        ], 
                                        "weight": 0.160888671875
                                    }, 
                                    {
                                        "color": [
                                            74, 
                                            192, 
                                            231
                                        ], 
                                        "weight": 0.120849609375
                                    }, 
                                    {
                                        "color": [
                                            19, 
                                            14, 
                                            31
                                        ], 
                                        "weight": 0.070556640625
                                    }

                                ],
                                "entropy": 5.755225306848601, 
                                "size": 73327
                            }, 
                            {
                                "width": 47, 
                                "url": "http://i1.sndcdn.com/avatars-000039409754-3pj42q-badge.jpg?3eddc42", 
                                "height": 47, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            71, 
                                            68, 
                                            65
                                        ], 
                                        "weight": 0.156982421875
                                    }, 
                                    {
                                        "color": [
                                            26, 
                                            24, 
                                            27
                                        ], 
                                        "weight": 0.138427734375
                                    }, 
                                    {
                                        "color": [
                                            105, 
                                            103, 
                                            98
                                        ], 
                                        "weight": 0.085693359375
                                    }, 
                                    {
                                        "color": [
                                            137, 
                                            132, 
                                            133
                                        ], 
                                        "weight": 0.076904296875
                                    }, 
                                    {
                                        "color": [
                                            178, 
                                            171, 
                                            173
                                        ], 
                                        "weight": 0.049072265625
                                    }
                                ], 
                                "entropy": 6.531873462937484, 
                                "size": 1756
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "English", 
                        "url": "http://soundcloud.com/bluetech/lost-found", 
                        "title": "Lost & Found by Bluetech", 
                        "published": null
                    },
                    {   // WEBSITE
                        "provider_url": "http://partners.nytimes.com", 
                        "authors": [], 
                        "provider_display": "partners.nytimes.com", 
                        "related": [], 
                        "favicon_url": null, 
                        "keywords": [
                            {
                                "score": 130, 
                                "name": "planetariums"
                            }, 
                            {
                                "score": 70, 
                                "name": "museum"
                            }, 
                            {
                                "score": 67, 
                                "name": "2000"
                            }, 
                            {
                                "score": 48, 
                                "name": "meteorite"
                            }, 
                            {
                                "score": 44, 
                                "name": "february"
                            }, 
                            {
                                "score": 37, 
                                "name": "center"
                            }, 
                            {
                                "score": 30, 
                                "name": "rose"
                            }, 
                            {
                                "score": 30, 
                                "name": "universe"
                            }, 
                            {
                                "score": 28, 
                                "name": "willamette"
                            }, 
                            {
                                "score": 27, 
                                "name": "space"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "http://partners.nytimes.com/library/national/science/planetarium-index.html", 
                        "media": {}, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 6, 
                                "name": "Earth"
                            }, 
                            {
                                "count": 5, 
                                "name": "American Museum of Natural History"
                            }, 
                            {
                                "count": 3, 
                                "name": "Willamette Meteorite"
                            }, 
                            {
                                "count": 2, 
                                "name": "Oregon"
                            }, 
                            {
                                "count": 2, 
                                "name": "GLENN COLLINS"
                            }, 
                            {
                                "count": 2, 
                                "name": "Hayden Planetarium"
                            }, 
                            {
                                "count": 1, 
                                "name": "Copernicus"
                            }, 
                            {
                                "count": 1, 
                                "name": "JAMES GLANZ"
                            }, 
                            {
                                "count": 1, 
                                "name": "Dr. Neil de Grasse Tyson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Ralph Appelbaum"
                            }, 
                            {
                                "count": 1, 
                                "name": "JULIE V. IOVINE"
                            }, 
                            {
                                "count": 1, 
                                "name": "NYC"
                            }, 
                            {
                                "count": 1, 
                                "name": "Manhattan"
                            }, 
                            {
                                "count": 1, 
                                "name": "ROBERT D. McFADDEN"
                            }, 
                            {
                                "count": 1, 
                                "name": "DAVID W. DUNLAP"
                            }, 
                            {
                                "count": 1, 
                                "name": "RITA REIF"
                            }, 
                            {
                                "count": 1, 
                                "name": "New York Convention and Visitors Bureau"
                            }, 
                            {
                                "count": 1, 
                                "name": "HERBERT MUSCHAMP"
                            }, 
                            {
                                "count": 1, 
                                "name": "Pluto"
                            }, 
                            {
                                "count": 1, 
                                "name": "Galileo"
                            }, 
                            {
                                "count": 1, 
                                "name": "JOHN NOBLE WILFORD"
                            }, 
                            {
                                "count": 1, 
                                "name": "MALCOLM W. BROWNE"
                            }, 
                            {
                                "count": 1, 
                                "name": "KENNETH CHANG"
                            }, 
                            {
                                "count": 1, 
                                "name": "JOHN SULLIVAN"
                            }, 
                            {
                                "count": 1, 
                                "name": "BENJAMIN WEISER"
                            }, 
                            {
                                "count": 1, 
                                "name": "SARAH BOXER"
                            }, 
                            {
                                "count": 1, 
                                "name": "MATTHEW MIRAPAUL"
                            }, 
                            {
                                "count": 1, 
                                "name": "EDWARD ROTHSTEIN"
                            }, 
                            {
                                "count": 1, 
                                "name": "Planetarium"
                            }, 
                            {
                                "count": 1, 
                                "name": "New York"
                            }, 
                            {
                                "count": 1, 
                                "name": "TINA KELLEY"
                            }, 
                            {
                                "count": 1, 
                                "name": "Cristyne F. Lategano"
                            }
                        ], 
                        "provider_name": "Nytimes", 
                        "type": "html", 
                        "description": "For Some, the Universe Is Over Their Heads By TINA KELLEY (March 7, 2000) Since the Rose Center for Earth and Space opened last month amid praise for its architecture and multidimensional tour of the universe, visitors have lined up with enthusiasm. And, sometimes, they have left in confusion.", 
                        "embeds": [], 
                        "images": [
                            {
                                "url": "http://graphics.nytimes.com/library/national/science/planetarium-index.jpg", 
                                "width": 305, 
                                "height": 256, 
                                "caption": null, 
                                "size": 29893
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.5.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 5606
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.2.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 5359
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.1.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 5191
                            }, 
                            {
                                "url": "http://partners.nytimes.com/library/national/science/021300planetarium-index.3.gif", 
                                "width": 75, 
                                "height": 75, 
                                "caption": null, 
                                "size": 4344
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "English", 
                        "url": "http://partners.nytimes.com/library/national/science/planetarium-index.html", 
                        "title": "Space: Rose Center for Earth and Space", 
                        "published": null
                    },
                    {   // VIMEO
                        "provider_url": "https://vimeo.com/", 
                        "authors": [], 
                        "provider_display": "vimeo.com", 
                        "related": [], 
                        "favicon_url": "http://a.vimeocdn.com/images_v6/favicon_32.ico", 
                        "keywords": [
                            {
                                "score": 39, 
                                "name": "patterns"
                            }, 
                            {
                                "score": 38, 
                                "name": "networks"
                            }, 
                            {
                                "score": 25, 
                                "name": "systems"
                            }, 
                            {
                                "score": 20, 
                                "name": "recurring"
                            }, 
                            {
                                "score": 19, 
                                "name": "ecosystems"
                            }, 
                            {
                                "score": 18, 
                                "name": "vimeo"
                            }, 
                            {
                                "score": 18, 
                                "name": "cells"
                            }, 
                            {
                                "score": 18, 
                                "name": "whitworth"
                            }, 
                            {
                                "score": 17, 
                                "name": "slime"
                            }, 
                            {
                                "score": 16, 
                                "name": "creative"
                            }
                        ], 
                        "lead": null, 
                        "original_url": "http://vimeo.com/34182381", 
                        "media": {
                            "duration": 105, 
                            "width": 500, 
                            "html": "<iframe src=\"http://player.vimeo.com/video/34182381\" width=\"500\" height=\"281\" frameborder=\"0\" title=\"TO UNDERSTAND IS TO PERCEIVE PATTERNS\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>", 
                            "type": "video", 
                            "height": 281
                        }, 
                        "content": null, 
                        "entities": [
                            {
                                "count": 2, 
                                "name": "Rob Whitworth"
                            }, 
                            {
                                "count": 1, 
                                "name": "Cheryl Colan"
                            }, 
                            {
                                "count": 1, 
                                "name": "Pedro Miguel Cruz"
                            }, 
                            {
                                "count": 1, 
                                "name": "Moon Soundtrack"
                            }, 
                            {
                                "count": 1, 
                                "name": "Johnson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Aaron Koblin"
                            }, 
                            {
                                "count": 1, 
                                "name": "Tiffany Shlain"
                            }, 
                            {
                                "count": 1, 
                                "name": "Adrian Bejan"
                            }, 
                            {
                                "count": 1, 
                                "name": "Takuya Hosogane"
                            }, 
                            {
                                "count": 1, 
                                "name": "Stephen Johnson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Katie Armstrong"
                            }, 
                            {
                                "count": 1, 
                                "name": "Manhattan"
                            }, 
                            {
                                "count": 1, 
                                "name": "Jared Raab"
                            }, 
                            {
                                "count": 1, 
                                "name": "Andrea Tseng"
                            }, 
                            {
                                "count": 1, 
                                "name": "Steven Johnson"
                            }, 
                            {
                                "count": 1, 
                                "name": "Angela Palmer"
                            }, 
                            {
                                "count": 1, 
                                "name": "Clint Mansell"
                            }, 
                            {
                                "count": 1, 
                                "name": "Paul Stammetts"
                            }, 
                            {
                                "count": 1, 
                                "name": "Jason Silva"
                            }, 
                            {
                                "count": 1, 
                                "name": "Jesse Kanda"
                            }, 
                            {
                                "count": 1, 
                                "name": "Death & Technology"
                            }
                        ], 
                        "provider_name": "Vimeo", 
                        "type": "html", 
                        "description": "Follow me on Twitter: https://twitter.com/JasonSilva @JasonSilva and @notthisbody Special thanks to filmmaker/photographer Rob Whitworth for allowing a clip from his video (https://vimeo.com/32958521) to be featured. Check out his website: www.robwhitworth.co.uk My videos: Beginning of Infinity - http://vimeo.com/29938326 Imagination - http://vimeo.com/34902950 INSPIRATION: The Imaginary Foundation says \"To Understand Is To Perceive Patterns\"...", 
                        "embeds": [], 
                        "images": [
                            {
                                "width": 1280, 
                                "url": "http://b.vimeocdn.com/ts/232/668/232668361_1280.jpg", 
                                "height": 740, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            2, 
                                            6, 
                                            13
                                        ], 
                                        "weight": 0.478515625
                                    }, 
                                    {
                                        "color": [
                                            39, 
                                            41, 
                                            36
                                        ], 
                                        "weight": 0.2099609375
                                    }, 
                                    {
                                        "color": [
                                            192, 
                                            157, 
                                            115
                                        ], 
                                        "weight": 0.109130859375
                                    }, 
                                    {
                                        "color": [
                                            76, 
                                            26, 
                                            76
                                        ], 
                                        "weight": 0.1064453125
                                    }, 
                                    {
                                        "color": [
                                            151, 
                                            59, 
                                            101
                                        ], 
                                        "weight": 0.03759765625
                                    }
                                ], 
                                "entropy": 5.29376026059, 
                                "size": 234555
                            }, 
                            {
                                "width": 75, 
                                "url": "http://b.vimeocdn.com/ps/323/365/3233659_75.jpg", 
                                "height": 75, 
                                "caption": null, 
                                "colors": [
                                    {
                                        "color": [
                                            19, 
                                            19, 
                                            19
                                        ], 
                                        "weight": 0.596923828125
                                    }, 
                                    {
                                        "color": [
                                            207, 
                                            207, 
                                            207
                                        ], 
                                        "weight": 0.301025390625
                                    }, 
                                    {
                                        "color": [
                                            138, 
                                            138, 
                                            138
                                        ], 
                                        "weight": 0.10205078125
                                    }
                                ], 
                                "entropy": 3.42717878362, 
                                "size": 8744
                            }
                        ], 
                        "safe": true, 
                        "offset": null, 
                        "cache_age": 86400, 
                        "language": "Lithuanian", 
                        "url": "http://vimeo.com/34182381", 
                        "title": "TO UNDERSTAND IS TO PERCEIVE PATTERNS", 
                        "published": null
                    }

                ];

                //return embedly_extract_multi_object[0];
                
                // Return Random
                return embedly_extract_multi_object[Math.floor(Math.random() * embedly_extract_multi_object.length)];

            },
            
        };
    }]);



/*
  ____  _               _   _                
 |  _ \(_)_ __ ___  ___| |_(_)_   _____  ___ 
 | | | | | '__/ _ \/ __| __| \ \ / / _ \/ __|
 | |_| | | | |  __/ (__| |_| |\ V /  __/\__ \
 |____/|_|_|  \___|\___|\__|_| \_/ \___||___/
                                             
////////// ------------ DIRECTIVES ------------ //////////*/

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







/*
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
     __     __  ____    _    _   _ ____  ____   _____  __     __     __
    / /    / / / ___|  / \  | \ | |  _ \| __ ) / _ \ \/ /    / /    / /
   / /    / /  \___ \ / _ \ |  \| | | | |  _ \| | | \  /    / /    / / 
  / /    / /    ___) / ___ \| |\  | |_| | |_) | |_| /  \   / /    / /  
 /_/    /_/    |____/_/   \_\_| \_|____/|____/ \___/_/\_\ /_/    /_/   
                                                                       
*/
