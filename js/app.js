'use strict';
var feed_settings = [];
var load_comments = [];

var postworld = angular.module('postworld', ['ngResource','ngRoute', 'ngSanitize', 'infinite-scroll', 'ui.bootstrap' ])
    .config(function ($routeProvider, $locationProvider) {              
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
    }
}]);



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


////////// ------------ SEARCH FIELDS CONTROLLER ------------ //////////
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


////////// ------------ EDIT POST CONTROLLER ------------ //////////
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


function AuthorAutocomplete($scope) {
  $scope.selected = undefined;
  $scope.authors = ['Erik Davis', 'Daniel Pinchbeck', 'Ken Jordan', 'Starhawk', 'Faye', 'Alex Grey', 'Nick Meador', 'Maureen Dawn Healy', 'Jonathon Miller Weisberger', 'Adam Elenbaas', 'Dan Phiffer', 'Michael Garfield', 'Jay Michaelson', 'Nathan Walters', 'Carolyn Elliott', 'Gary Lachman', 'Kourosh Ziabari', 'Adam Sommer'];
}


////////// ------------ DIRECTIVES ------------ //////////

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


