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


var postworld = angular.module('postworld', ['ngResource','ngRoute', 'ngSanitize', 'infinite-scroll', 'ui.bootstrap', 'monospaced.elastic','TimeAgoFilter','TruncateFilter' ])
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



    $routeProvider.when('/new/:post_type',
        {
            action: "new_post",
        });


    $routeProvider.when('/edit/:post_id',
        {
            //templateUrl: jsVars.pluginurl+'/postworld/templates/samples/postLink.html',             
            //mode:'new';

            action: "edit_post",

            //controller: 'editPost',
            //resolve: { post_type:"link" }

            /*
            resolve: { post_type: function($route){
                return $route.current.params.post_type;
            }}
            */
            /*
            resolve: {
                resolvedprop: [function () {
                    var apiObject = {url: 'phong.com' };                      
                    return apiObject;
                }],
            }
            */
            /*

            resolve: {
                resolvedprop: ['$route', '$q', function ($route, $q) {
                   var apiObject = {url: 'abc.com' };                      
                   return apiObject     
                         }],
            }

            */


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
postworld.service('pwPostOptions', ['$log', function ($log) {
    // Do one AJAX call here which returns all the options
    return{
        pwGetPostTypeOptions: function(){
        return {
            feature : "Features",
            blog : "Blog",
            // link : "Link",
            announcement : "Announcement",
            tribe_events : "Event"
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
        pwGetTaxTerms_old: function(){
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
        pwGetTaxTerms: function(){
            return {
                "topic" : [
                    {
                        slug:"psyche",
                        name:"/psyche",
                        children:[
                            {
                                name:"Ancient Mysteries",                     
                                slug: "ancient",
                            },
                            {
                                name:"Astrology",                     
                                slug: "astrology",
                            },
                            {
                                name:"Consciousness",                     
                                slug: "consciousness",
                            },
                        ],
                    },
                    {
                        slug:"arts",
                        name:"/arts",
                        children:[
                            {
                                name:"Conferences",                     
                                slug: "conferences",
                            },
                            {
                                name:"Digital Art",                     
                                slug: "digital_art",
                            },
                            {
                                name:"World Art",                     
                                slug: "world_art",
                            },
                        ],
                    },
                    {
                        slug:"body",
                        name:"/body",
                        children:[
                            {
                                name:"Energy Medicine",                     
                                slug: "energy_medicine",
                            },
                            {
                                name:"Food & Nutrition",                     
                                slug: "food_nutrition",
                            },
                            {
                                name:"Healing",                     
                                slug: "healing",
                            },
                        ],
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
    $scope.tax_terms = $pwPostOptions.pwGetTaxTerms();

}]);


/*
  _____    _ _ _     ____           _   
 | ____|__| (_) |_  |  _ \ ___  ___| |_ 
 |  _| / _` | | __| | |_) / _ \/ __| __|
 | |__| (_| | | |_  |  __/ (_) \__ \ |_ 
 |_____\__,_|_|\__| |_|   \___/|___/\__|

////////// ------------ EDIT POST CONTROLLER ------------ //////////*/
postworld.controller('editPost',
    ['$scope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
    'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', 'siteOptions', 
    function($scope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
        $pwData, $log, $route, $routeParams, $location, $http, $siteOptions ) {

    //alert( JSON.stringify( $route.current.action ) );

    $scope.default_post_data = {
        //post_id : 24,
        post_author: 1,
        post_title : "",
        post_name : "",
        post_type : "blog",
        post_status : "publish",
        post_format : "standard",
        post_class : "contributor",
        link_url : "",
        post_permalink : "",
        tax_input : {
            topic : [],
            section : [],
            type : []
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
                // Set the new mode
                $scope.mode = "new";

                // Get the post type
                var post_type = ($routeParams.post_type || "");
                // If post type is supplied
                if ( post_type != "" )
                    // Set the post type
                    $scope.post_data.post_type = post_type;
            }

            ///// ROUTE : EDIT POST /////
            if ( $route.current.action == "edit_post"  ){ // && typeof $scope.post_data.post_id !== 'undefined'
                // Load the specified post data
                $scope.load_post_data();
            }
           
        }
    );


    $scope.load_post_data = function(){
        $scope.mode = "edit";

        // GET THE POST DATA
        $pwData.pw_get_post_edit( $routeParams.post_id ).then(
            // Success
            function(response) {    
                $log.info('pwData.pw_get_post : RESPONSE : ', response.data);

                // FILTER FOR INPUT
                var get_post_data = response.data;

                // BREAK OUT THE TAGS INTO TAGS_INPUT
                if ( typeof get_post_data.taxonomy.post_tag !== 'undefined'  ){
                    get_post_data['tags_input'] = "";
                    angular.forEach( get_post_data.taxonomy.post_tag, function( tag ){
                        get_post_data['tags_input'] += tag.slug + ", ";
                    });
                    delete get_post_data.taxonomy.post_tag;
                }
                
                // RENAME THE KEY : TAXONOMY > TAX_INPUT
                var tax_input = {};
                var tax_obj = get_post_data['taxonomy'];
                angular.forEach( tax_obj, function( terms, taxonomy ){
                    tax_input[taxonomy] = [];
                    angular.forEach( terms, function( term ){
                        tax_input[taxonomy].push(term.slug);
                    });
                });
                delete get_post_data['taxonomy'];
                get_post_data['tax_input'] = tax_input; 

                
                // SET THE POST CONTENT
                tinyMCE.get('post_content').setContent( get_post_data.post_content );

                $scope.post_data = get_post_data;
            },
            // Failure
            function(response) {
                alert('error');
                $scope.status = "error";
            }
        );  
    }



    $scope.status = "done";

    // SAVE POST FUNCTION
    $scope.savePost = function(pwData){

        // VALIDATE THE FORM
        if ($scope.post_data.post_title != '' || typeof $scope.post_data.post_title !== 'undefined'){
            //alert(JSON.stringify($scope.post_data));

            ///// GET POST_DATA FROM TINYMCE /////
            if ( typeof tinyMCE.get('post_content').getContent() !== 'undefined' )
                $scope.post_data.post_content = tinyMCE.get('post_content').getContent();

            ///// SANITIZE FIELDS /////
            if ( typeof $scope.post_data.link_url === 'undefined' )
                $scope.post_data.link_url = '';

            ///// SAVE POST VIA AJAX /////
            var post_data = $scope.post_data;
            //alert(JSON.stringify(post_data));

            $scope.status = "saving";
            $pwData.pw_save_post( post_data ).then(
            //pwData.pw_save_post( post_data ).then(
                // Success
                function(response) {    
                    //alert( "RESPONSE : " + response.data );
                    $log.info('pwData.pw_save_post : RESPONSE : ', response.data);

                    // VERIFY POST CREATION
                    // If it was created, it's an integer
                    if( response.data === parseInt(response.data) ){
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
                    }
                    else{
                        alert("Post not saved : " + JSON.stringify(response.data) );
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
        

        /*
        ///// EVENTS DATE & TIME /////
        ///// EXTRACT THE DATE FOR TRIBE EVENTS ///// 
        var EventStartHour = $filter('date')( $scope.EventStartDateObject, 'HH'); 
        var EventStartMinute = $filter('date')( $scope.EventStartDateObject, 'mm'); 
        //EventStartMaridian
        var EventEndHour = $filter('date')( $scope.EventEndDateObject, 'HH');
        var EventEndMinute = $filter('date')( $scope.EventEndDateObject, 'mm'); 
        //EventEndMaridian
        var EventStartDate = $filter('date')( $scope.EventStartDateObject, 'yyyy-MM-dd HH:mm Z');
        var EventEndDate = $filter('date')( $scope.EventEndDateObject, 'yyyy-MM-dd HH:mm Z');

        alert(
            "Start Date : " + EventStartDate  + "\n" + 
            " End Date :  " + EventEndDate + "\n" 
            );
        */
        //alert( JSON.stringify( $scope.post_data ) );
    }


    $scope.clear_post_data = function(){
        $scope.post_data = {};
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

    // TAXONOMY TERMS
    // Gets dummy terms form testing models : $scope.tax_terms = $pwPostOptions.pwGetTaxTerms();
    // Gets live set of terms from the DB :
    $scope.getTaxTerms = function(){
        var args = $siteOptions.taxOutlineMixed();
        $pwData.taxonomies_outline_mixed( args ).then(
            // Success
            function(response) {    
                //alert(JSON.stringify(response.data));
                $scope.tax_terms = response.data;
                //return response.data;
            },
            // Failure
            function(response) {
                alert('Error loading terms.');
            }
        );
    }
    $scope.getTaxTerms();



    // POST DATA OBJECT
    $scope.post_data = $scope.pw_get_post_object();


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
            // TRIBE EVENTS
            if( $scope.post_data.post_type == 'tribe_events' && typeof $scope.EventStartDateObject === 'undefined' ){
               
                // DATE : Initialize Objects
                $scope.EventStartDateObject = new Date();
                $scope.EventEndDateObject = new Date();

            }
        }, 1 );

    ////////// EVENT DATE PICKER //////////
    

    // DATE CHANGE : Watch the date objects for a change
    $scope.updateDate = function(){

    }

    //$scope.post_data.WPDate = $filter('date')(new Date(), 'yyyy-MM-dd');

    $scope.showWeeks = false;

    $scope.clear = function () {
        $scope.dt = null;
    };

    $scope.dateOptions = {
        'year-format': "'yy'",
        'starting-day': 1
    };

    ////////// TIME PICKER //////////

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
        console.log('Time changed to: ' + $scope.EventStartTimeObject );
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


    ///// FEATURED IMAGE /////
    // Media Upload Window
    $scope.updateFeaturedImage = function(image_object){
        //alert( JSON.stringify(image_object) );
        $scope.featured_image = image_object;
        $scope.post_data.thumbnail_id = image_object.id;
        if( typeof image_object !== 'undefined' ){
            $scope.has_featured_image = 'true';
        }
    }
    $scope.removeFeaturedImage = function(){
        $scope.featured_image = {};
        $scope.has_featured_image = 'false';
        $scope.post_data.thumbnail_id = '';
    }

    ///// GET POST_CONTENT FROM TINY MCE /////
    $scope.getTinyMCEContent = function(){        
        
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
                    alert('Could not find URL.');
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


var mediaModalCtrl = function ($scope, $modal, $log) {

  $scope.openMediaModal = function (post) {
    var modalInstance = $modal.open({
      templateUrl: jsVars.pluginurl+'/postworld/templates/panels/media_modal.html',
      controller: MediaModalInstanceCtrl,
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

    //$scope.oEmbedDecode = '<iframe width="500" height="281" src="http://www.youtube.com/embed/38peWm76l-U?feature=oembed" frameborder="0" allowfullscreen></iframe> ';
    $scope.oEmbedDecode = $sce.trustAsHtml( $scope.oEmbedDecode );
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
                alert("error");
            }
        );
        
    };
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
        	// Do stuff related to the rendering of the element here - only if needed
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
                        $scope.oEmbed = $sce.trustAsHtml( response.data );
                        
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



postworld.service('pwPostOptions2', ['$log', '$q', 'pwData', 'siteOptions', function ($log, $q, pwData, siteOptions, $scope) {
    // Do one AJAX call here which returns all the options
    return{
        taxOutlineMixed : function(){
            
            function get_tax_outline_mixed(){
                var deferred = $q.defer();


                var args = siteOptions.taxOutlineMixed();
                pwData.taxonomies_outline_mixed( args ).then(
                    // Success
                    function(response) {    
                        //alert(JSON.stringify(response.data));
                        deferred.resolve( response.data );
                    },
                    // Failure
                    function(response) {
                        deferred.reject( "error" ); 
                    }
                );


                return deferred.promise;
            }

            var promise = get_tax_outline_mixed();

            promise.then(function(result) {
              alert('Success: ' + JSON.stringify(result));
              //return result;
            }, function(reason) {
              alert('Failed: ' + reason);
            }, function(update) {
              alert('Got notification: ' + update);
            });


        },
    }

}]);











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
     __     __  ____    _    _   _ ____  ____   _____  __     __     __
    / /    / / / ___|  / \  | \ | |  _ \| __ ) / _ \ \/ /    / /    / /
   / /    / /  \___ \ / _ \ |  \| | | | |  _ \| | | \  /    / /    / / 
  / /    / /    ___) / ___ \| |\  | |_| | |_) | |_| /  \   / /    / /  
 /_/    /_/    |____/_/   \_\_| \_|____/|____/ \___/_/\_\ /_/    /_/   
                                                                       
*/
