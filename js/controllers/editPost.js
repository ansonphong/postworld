'use strict';

/*_____    _ _ _     ____           _   
 | ____|__| (_) |_  |  _ \ ___  ___| |_ 
 |  _| / _` | | __| | |_) / _ \/ __| __|
 | |__| (_| | | |_  |  __/ (_) \__ \ |_ 
 |_____\__,_|_|\__| |_|   \___/|___/\__|

////////// ------------ EDIT POST CONTROLLER ------------ //////////*/

postworld.directive( 'pwEditPost', [ function($scope){
    return {
        restrict: 'AE',
        controller: 'editPost',
        link: function( $scope, element, attrs ){
            $scope.initEditPost = {};
            // OBSERVE Attribute
            attrs.$observe('postType', function(value) {
                if( !_.isUndefined( value ) )
                    $scope.initEditPost['post_type'] = value;
            });
        }
    };
}]);

postworld.controller('editPost',
    ['$scope', '$rootScope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
    'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', 'ext', '$window', 'pwRoleAccess', 'pwQuickEdit',
    function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
        $pwData, $log, $route, $routeParams, $location, $http, $ext, $window, $pwRoleAccess, $pwQuickEdit ) {

    // Define Default Post
    //alert( JSON.stringify($scope.post) );
    //$scope.default_post = $scope.post;

    $scope.setPostObject = function( post ){
        // Given any even incomplete post object
        // This function will build it into a safe post object
        // And set it in the Scope

        //post_type, post_format
        $scope.status = "loading";

        var post_defaults = $window.pwSiteGlobals.post_options.defaults;
        var editPostGlobals = $window.pwSiteGlobals.edit_post;

        ///// DETECT POST TYPE /////
        // Check for Post Type Defined by the 'input' Post Object
        if( !_.isUndefined( post.post_type ) )
            var post_type = post.post_type;

        // Check for Post Type Defined by the 'scope' Post Object
        else if( $ext.objExists( $scope, 'post.post_type' ) )
            var post_type = $scope.post.post_type;

        else
            var post_type = "post";

        ///// DETECT POST FORMAT /////
        // Check for Post Format Defined by the 'input' Post Object 
        if( !_.isUndefined( post.post_format ) )
            var post_format = post.post_format;
        else
            var post_format = 'default';



        ///// GET DEFAULT POST OBJECT /////
        // This function is safe to pass an 'undefined' post type / format
        var post = $scope.getDefaultPostConfig( post_type, post_format );
        

        ///// OVERRIDE WITH SCOPE POST /////
        // If the Post Object is defined the current scope
        // Override the defaults with those values
        if( !_.isUndefined( $scope.post ) ){
            // Over-write inputs over default post
            angular.forEach( $scope.post, function(value, key){
                post[key] = value;
            });
        }

        ///// FILL IN REQUIRED DEFAULTS /////
        // Check it over to make sure it has all the neccessary fields to init
        // SET : DEFAULT POST DATA MODEL
        var default_post = {
            post_title : "",
            post_name : "",
            //post_type : $scope.initEditPost.post_type,
            post_status : post_defaults.edit_post.post_status,
            post_class : post_defaults.edit_post.post_class,
            link_url : "",
            link_format : post_defaults.edit_post.link_format,
            post_date_gmt:"",
            post_permalink : "",
            tax_input : $pwPostOptions.pwGetTaxInputModel(),
            tags_input : "",
            post_meta:{},
        };

        // If post object doesn't contain init defaults, write them in
        angular.forEach( default_post, function(value, key){
            if( _.isUndefined( post[key] ) )
                post[key] = value;
        });

        // CHECK TERMS CATEGORY / SUBCATEGORY ORDER
        post = $pwEditPostFilters.sortTaxTermsInput( post, $scope.tax_terms, 'tax_input' );

        $scope.default_post = post;
        $scope.post = post;

    };

    $scope.getDefaultPostConfig = function( post_type, post_format ){

        ///// DETECT POST TYPE /////
        // If post type is empty or not defined
        // Define Default Post Type
        if( _.isUndefined(post_type) ||
            _.isEmpty( post_type ) ||
            post_type == '' ){
            // Check for Post Type defined by directive
            if (!_.isUndefined( $scope.initEditPost.post_type ) )
                var post_type = $scope.initEditPost.post_type;
            // Check for Post Type defined by scope post object
            else if( $ext.objExists( $scope, 'post.post_type' ) )
                var post_type = $scope.post.post_type;
            else
                var post_type = 'post';
        }

        // Define default Post Format
        if( _.isUndefined(post_format) ||
            _.isEmpty(post_format) )
            var post_format = 'default';

        // Localize Edit Post Config
        var edit_post = $window.pwSiteGlobals.edit_post;

        // Check if the requested post type is defined
        if( _.isUndefined( edit_post[post_type] ) )
            // Fallback on post_type
            post_type = 'post';

        // Check if the requested post format is defined
        if( !$ext.objExists( edit_post, post_type + ".new." + post_format ) )
            // Fallback on post_format
            post_format = 'default';

        // Define New Post from Default Config
        if( !_.isUndefined( edit_post[post_type]['new'] ) &&
            !_.isUndefined( edit_post[post_type]['new'][post_format] ) )
            var post = edit_post[post_type]['new'][post_format];
        else{
            var post = edit_post['post']['new']['default'];
        
            // If post type was defined, but not in config, re-insert here
            if( !_.isUndefined( post_type ) )
                post.post_type = post_type;
        }

        // Return the selected post defaults
        return post;

    };

    // ROLE ACCESS
    // Sets booleans for role access variables : "editor", "author"
    $pwRoleAccess.setRoleAccess($scope);

    ///// CLEAR POST DATA /////
    $scope.newPost = function( post ){
        
        // Set the new mode
        $scope.mode = "new";

        $scope.post = {};
        // Set the new post object in scope
        $scope.setPostObject( post );

        // Clear TinyMCE
        $timeout(function() {
            if( typeof tinyMCE !== 'undefined' ){
                if( typeof tinyMCE.get('post_content') !== 'undefined' ){
                    //$log.debug('RESET tinyMCE : ', tinyMCE);
                    tinyMCE.get('post_content').setContent( "" );
                }
            }
        }, 2);

        // Set the Route
        if( $ext.objExists( $scope, 'post.post_type' ) )
            $location.path('/new/' + $scope.post.post_type);

    }

    ///// NEW QUICK EDIT POST /////
    if( $scope.mode == 'quick-edit-new' ){
        //alert( $scope.post.post_type ); 
        $scope.setPostObject( { 'post_type':$scope.post.post_type } );
        $scope.status = "done";
    }
    
    ///// WATCH : ROUTE /////
    //$timeout( function(){
        $scope.$on(
            "$routeChangeSuccess",
            function( $currentRoute, $previousRoute ){
                //alert( JSON.stringify( $currentRoute ) );
                ///// ROUTE : NEW POST /////
                if ( $route.current.action == "new_post"  ){ // && typeof $scope.post.post_id !== 'undefined'
                    // SWITCH FROM MODE : EDIT > NEW
                    // If we're coming to 'new' mode from 'edit' mode
                    if($scope.mode == "edit"){
                        // Clear post Data
                        $scope.newPost();
                    }

                    // Get the post type from route
                    var post_type = ($routeParams.post_type || "");

                    // Get the post type from the directive attributes
                    if( post_type == "" )
                        if( !_.isUndefined( $scope.initEditPost.post_type ) )
                            post_type = $scope.initEditPost.post_type;

                    /* OBSOLETE
                    // If Post Type has been defined
                    if ( post_type != "" ){
                        // If the post object is not defined, set it
                        if( _.isUndefined($scope.post) )
                            $scope.post = {};
                        // Set the post type
                        $scope.post.post_type = post_type;
                    }
                    */

                    $scope.setPostObject( { 'post_type':post_type } );

                    // Set the status
                    $scope.status = "done";
                }
                ///// ROUTE : EDIT POST /////
                else if ( $route.current.action == "edit_post"  ){ // && typeof $scope.post.post_id !== 'undefined'
                    // Load the specified post data
                    $scope.loadPost();
                }
                ///// ROUTE : SET DEFAULT /////
                else if ( $route.current.action == "default"  ){
                    $location.path('/new/' + post_defaults.edit_post.post_type );
                }
            }
        );
    //}, 1 );

    ///// QUICK EDIT : LOAD POST DATA /////
    $scope.$on('loadPostData', function(event, post_id) {
        $scope.loadPost( post_id );
    });

    ///// LOAD POST DATA /////
    $scope.loadPost = function( post_id ){

        // Post ID passed directly
        if( !_.isUndefined(post_id) ){
            $log.debug('editPost Controller : loadPost( *post_id* ) // Post ID passed directly : ', post_id);

        // Post ID passed by Route
        } else if ( typeof $routeParams.post_id !== 'undefined' &&
            $routeParams.post_id > 0 ){
            var post_id = $routeParams.post_id;
            $log.debug('editPost Controller : loadPost() // Post ID from Route : ', post_id);
        }

        // Post ID passed by Post Object
        else if( !_.isUndefined($scope.post.ID) && $scope.post.ID > 0 ){
            var post_id = $scope.post.ID;
            $log.debug('editPost Controller : loadPost() // Post ID from Post Object : ', post_id);
        }
        
        // GET THE POST DATA
        $pwData.pw_get_post_edit( post_id ).then(
            // Success
            function(response) {
                $log.debug('pwData.pw_get_post_edit : RESPONSE : ', response.data);
                $scope.mode = "edit";

                // FILTER FOR INPUT
                var get_post = response.data;

                ///// LOAD TAXONOMIES /////
                // RENAME THE KEY : TAXONOMY > TAX_INPUT
                var tax_input = {};
                if( !_.isUndefined( get_post['taxonomy'] ) ){
                    var tax_obj = get_post['taxonomy'];
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
                    delete get_post['taxonomy'];

                }
                get_post['tax_input'] = tax_input; 
                

                ///// LOAD POST CONTENT /////
                // SET THE POST CONTENT
                $scope.set_post_content( get_post.post_content );

                ///// LOAD AUTHOR /////
                // EXTRACT AUTHOR NAME
                if ( $ext.objExists( get_post, 'author.user_nicename' ) ){
                    get_post['post_author_name'] = get_post['author']['user_nicename'];
                    delete get_post['author'];
                }
                // BROADCAST TO USERNAME AUTOCOMPLETE FIELD
                $scope.$broadcast('updateUsername', get_post['post_author_name']);


                ///// POST META /////
                if ( !_.isUndefined( get_post['post_meta'] ) ){
                    
                     // Emit Geocode
                     // If geocode data exists, emit it's value
                    if( !_.isUndefined( get_post.post_meta['geocode'] ) )
                        $scope.$emit('pwAddGeocode', get_post.post_meta['geocode']);

                }

                // Parse known JSON Fields from strings into JSON
                // UPDATE : This is now being on in pw_get_post() PHP Method
                //get_post = $pwEditPostFilters.parseKnownJsonFields( get_post );

                // LOCAL CALLBACK ACTION EMIT
                // Any sibling or parent scope can listen on this action
                $scope.$emit('postLoaded', get_post);

                // Set the Route
                $location.path('/edit/' + get_post.ID);

                // SET DATA INTO THE SCOPE
                $scope.post = get_post;
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

    /////----- SAVE POST FUNCTION -----//////
    $scope.savePost = function(pwData){
        //alert( tinyMCE.get('post_content').getContent() );//tinyMCE.editors.content.getContent() );
        //alert( JSON.stringify($scope.post) );
        // VALIDATE THE FORM
        if ($scope.post.post_title != '' || typeof $scope.post.post_title !== 'undefined'){
            //alert(JSON.stringify($scope.post));

            ///// GET post FROM TINYMCE /////
            if ( typeof tinyMCE !== 'undefined' )
                if ( typeof tinyMCE.get('post_content') !== 'undefined'  )
                    $scope.post.post_content = tinyMCE.get('post_content').getContent();
            

            ///// SANITIZE FIELDS /////
            if ( typeof $scope.post.link_url === 'undefined' )
                $scope.post.link_url = '';

            ///// DEFINE POST DATA /////
            var post = $scope.post;

            //alert( JSON.stringify( post ) );
            //$log.debug('pwData.pw_save_post : SUBMITTING : ', post);

            ///// SAVE VIA AJAX /////
            $scope.status = "saving";
            $pwData.pw_save_post( post ).then(
                // Success
                function(response) {    
                    //alert( "RESPONSE : " + response.data );
                    $log.debug('pwData.pw_save_post : RESPONSE : ', response.data);
                    // VERIFY POST CREATION
                    // If it was created, it's an integer
                    if( response.data === parseInt(response.data) ){
                        // SAVE SUCCESSFUL
                        var post_id = response.data;
                        $scope.status = "success";
                        $timeout(function() {
                          $scope.status = "done";
                        }, 4000);

                        // LOAD POST
                        $scope.loadPost( post_id );

                        // ACTION BROADCAST
                        // For Quick Edit Mode - broadcast to children successful update
                        $rootScope.$broadcast('postUpdated', post_id);

                        // ACTION EMIT
                        // Any sibling or parent scope can listen on this action
                        $scope.$emit('postUpdated', post_id);
                    }
                    else{
                        // ERROR
                        if( typeof response.data == 'object' )
                            alert("Error : " + JSON.stringify(response.data) );
                        else
                            alert("ERROR : RESPONSE : (LOGGED IN?) : " + JSON.stringify( response ) );
                        $scope.status = "done";
                    }
                },
                // Failure
                function(response) {
                    //alert('error');
                    $scope.status = "error";
                    $timeout(function() {
                      $scope.status = "done";
                    }, 4000);
                }
            );

        } else {
            alert("Post not saved : missing fields.");
        }
    }
    /////----- END SAVE POST FUNCTION -----//////


    // TRASH POST
    $scope.trashPost = function(){
        $pwQuickEdit.trashPost( $scope.post.ID, $scope );
    }; 

    ///// SET POST CONTENT /////
    // Function checks to see if tinyMCE has initialized yet
    // If not, it sets a timeout and runs the function again
    $scope.set_post_content = function( post_content ){
        $timeout(function() {
            if( typeof tinyMCE !== 'undefined' ){
                if( typeof tinyMCE.get('post_content') !== 'undefined' ){
                    tinyMCE.get('post_content').setContent( post_content );
                }
                else
                    $scope.set_post_content( post_content );
            }
            else
                    $scope.set_post_content( post_content );
        }, 250 );
    };

    ///// LOAD IN DATA /////
    // POST TYPE OPTIONS
    $scope.post_type_options = $pwPostOptions.pwGetPostTypeOptions( 'edit' );
    // POST FORMAT OPTIONS
    $scope.link_format_options = $pwPostOptions.pwGetLinkFormatOptions();
    // POST FORMAT META
    $scope.link_format_meta = $pwPostOptions.pwGetLinkFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();

    // ACTION : AUTHOR NAME FROM AUTOCOMPLETE MODULE
    // • Interacts with userAutocomplete() controller
    // • Catches the recent value of the auto-complete
    $scope.$on('updateUsername', function( event, data ) { 
        if( !_.isUndefined( $scope.post ) )
            $scope.post.post_author_name = data;
    });

    // ACTION : POST TAGS FROM AUTOCOMPLETE MODULE
    // • Interacts with tagsAutocomplete() controller
    // • Catches the recent value of the tags_input and inject into tax_input
    $scope.$on('updateTagsInput', function( event, data ) { 
        $scope.post.tax_input.post_tag = data;
    });

    // GET : TAXONOMY TERMS
    // • Gets live set of terms from the DB as $scope.tax_terms
    $pwPostOptions.getTaxTerms( $scope, 'tax_terms' );

    // WATCH : TAXONOMY TERMS
    // • Watch for any changes to the post.tax_input
    // • Make a new object which contains only the selected sub-objects
    $scope.selected_tax_terms = {};
    $scope.$watch('[ post.tax_input, tax_terms ]',
        function ( newValue, oldValue ){
            if ( !_.isUndefined($scope.tax_terms) &&
                $ext.objExists( $scope, 'post.tax_input' ) ){
                // Create selected terms object
                $scope.selected_tax_terms = $pwEditPostFilters.selected_tax_terms($scope.tax_terms, $scope.post.tax_input);
                // Clear irrelivent sub-terms
                $scope.post.tax_input = $pwEditPostFilters.clear_sub_terms( $scope.tax_terms, $scope.post.tax_input, $scope.selected_tax_terms );
            }
        }, 1);

    // WATCH : LINK_URL
    // • Watch for changes in Link URL field
    // • Evaluate the Post Format
    $scope.$watchCollection('[ post.link_url, post.link_format ]',
        function ( newValue, oldValue ){

            // Check if Object Exists
            if( $ext.objExists( $scope, 'post.link_url' ) )
                $scope.post.link_format = $pwEditPostFilters.evalPostFormat( $scope.post.link_url, $scope.link_format_meta );
        
        });

    // WATCH : POST TYPE
    $scope.$watch( "post.post_type",
        function (){

            // Check if Object Exists
            if( !$ext.objExists( $scope, 'post.post_type' ) )
                return false;

            // ROUTE CHANGE
            if( $scope.mode == "new" )
                $location.path('/new/' + $scope.post.post_type);

            // BROADCAST CHANGE TO CHILD CONTROLLERS NODES
            $rootScope.$broadcast('changePostType', $scope.post.post_type );
 
            // POST STATUS OPTIONS
            // Re-evaluate available post_status options on post_type switch
            $scope.post_status_options = $pwPostOptions.pwGetPostStatusOptions( $scope.post.post_type );
            
            // SET DEFAULT POST STATUS
            if ( $scope.post.post_status == null || $scope.post.post_status == '' )
                angular.forEach( $scope.post_status_options, function(value, key){
                    //return key;
                    $scope.post.post_status = key;
                });

        }, 1 );

    ////////// FEATURED IMAGE //////////
    // Media Upload Window
    $scope.updateFeaturedImage = function(image_object){
        //alert( JSON.stringify(image_object) );
        $scope.post.image = {};
        $scope.post.image.meta = image_object;
        $scope.post.thumbnail_id = image_object.id;
        if( typeof image_object !== 'undefined' ){
            $scope.hasFeaturedImage = 'true';
        }
    }

    // FEATURE IMAGE WATCH : Watch the Featured Image
    ////// DEPRECIATED /////
    /*
    $scope.$watch( "post.image",
        function (){

        // Check if Object Exists
        if( !$ext.objExists( $scope, 'post.image' ) )
            $scope.hasFeaturedImage = false;

        if( !_.isUndefined($scope.post.thumbnail_id) &&
            $scope.post.thumbnail_id !== "" &&
            $scope.post.thumbnail_id !== "delete" )
            $scope.hasFeaturedImage = true;
        else
            $scope.hasFeaturedImage = false;
        }, 1 );
    */

    // Check if Object Exists
    $scope.hasFeaturedImage = function(){
        if( !$ext.objExists( $scope, 'post.image' ) ||
            _.isEmpty($scope.post.image) )
            return false;

        if( !_.isUndefined($scope.post.thumbnail_id) &&
            $scope.post.thumbnail_id !== "" &&
            $scope.post.thumbnail_id !== "delete" )
            return true;
        else
            return false;
    }

    $scope.removeFeaturedImage = function(){
        $scope.post.image = {};
        $scope.post.thumbnail_id = "delete";
    }

    ///// GET POST_CONTENT FROM TINY MCE /////
    $scope.getTinyMCEContent = function(){        
    }

    // FORM VALIDATION WATCH
    $scope.$watch( "editPost.$valid",
        function (){
        }, 1 );

    // LANGUAGE CODE WATCH
    // If 'lang' is defined (by pw-language) then add it to the post object
    $scope.$watch( "lang", function (){

        // Check if Post Exists
        if( !$ext.objExists( $scope, 'post' ) )
            return false;

        if( !_.isUndefined($scope.lang) )
            $scope.post.language_code = $scope.lang;
    } );

    $scope.showEditorSource = function(){
        var source = $('#post_content').val();
        source = tinyMCE.get('post_content').getContent({format : 'raw'});
        alert(source);
    };

}]);

////////// ------------ EVENT DATA/TIME CONTROLLER ------------ //////////*/
postworld.controller('eventInput',
    ['$scope', '$rootScope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
        'pwData', '$log', 'ext', 'pwDate',
    function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, 
        $pwData, $log, $ext, $pwDate ) {

    $timeout(function() {
        // SETUP DATE OBJECTS
        if( _.isUndefined( $scope.post ) )
            $scope.post = {};
        if( _.isUndefined( $scope.post.post_meta ) )
            $scope.post.post_meta = {};
        if( !$ext.objExists( $scope, 'post.post_meta.date_obj' ) ) // _.isUndefined( $scope.post.post_meta.date_obj )
            $scope.post.post_meta.date_obj = {};

        if( typeof $scope.post.post_meta.date_obj.event_start_date_obj === 'undefined' )
            $scope.post.post_meta.date_obj.event_start_date_obj = new Date( );
        if( typeof $scope.post.post_meta.date_obj.event_end_date_obj === 'undefined' )
            $scope.post.post_meta.date_obj.event_end_date_obj = new Date( );
    }, 4 );

    $scope.getUnixTimestamp = function( dateObject ){
        if( !_.isUndefined( dateObject ) ){
            var localDateObj = new Date(dateObject);
            return Math.round( localDateObj.getTime() / 1000);
        }
    };

    $scope.setUnixTimestamps = function(){
        // Add the UNIX Timestamp : event_start
        $scope.post.event_start = $scope.getUnixTimestamp( $scope.post.post_meta.date_obj.event_start_date_obj );
        // Add the UNIX Timestamp : event_end
        $scope.post.event_end = $scope.getUnixTimestamp( $scope.post.post_meta.date_obj.event_end_date_obj );
    }

    // WATCH : EVENT START TIME
    $scope.$watch( "post.post_meta.date_obj.event_start_date_obj",
        function (){
            // End function if variable doesn't exist
            if( !$ext.objExists( $scope, 'post.post_meta.date_obj' ) )
                return false;

            $scope.post.post_meta.date_obj.event_start_date = $filter('date')(
                $scope.post.post_meta.date_obj.event_start_date_obj, 'yyyy-MM-dd HH:mm' );

            // If start time is set after the end time - make them equal
            if( $scope.post.post_meta.date_obj.event_end_date_obj < $scope.post.post_meta.date_obj.event_start_date_obj )
                $scope.post.post_meta.date_obj.event_end_date_obj = $scope.post.post_meta.date_obj.event_start_date_obj;

            // Set UNIX Timestamps
            $scope.setUnixTimestamps();

        }, 1 );

    // WATCH : EVENT END TIME
    $scope.$watch( "post.post_meta.date_obj.event_end_date_obj",
        function (){
            // End function if variable doesn't exist
            if( !$ext.objExists( $scope, 'post.post_meta.date_obj' ) )
                return false;

            $scope.post.post_meta.date_obj.event_end_date = $filter('date')(
                $scope.post.post_meta.date_obj.event_end_date_obj, 'yyyy-MM-dd HH:mm' );

            // If end time is set before the start time - make them equal
            if( $scope.post.post_meta.date_obj.event_start_date_obj > $scope.post.post_meta.date_obj.event_end_date_obj  )
                $scope.post.post_meta.date_obj.event_start_date_obj = $scope.post.post_meta.date_obj.event_end_date_obj ;

            // Set UNIX Timestamps
            $scope.setUnixTimestamps();

        }, 1 );


    // POST TYPE WATCH : Watch the Post Type
    // Cleanup post_meta
    // Hidden - was causing issues with empty post_meta on load
    // $scope.$on('changePostType', function(event, data) { $scope.post.post_meta = {}; });


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

    //$scope.minDate = new Date();
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
        //$scope.post.EventStartHour = $scope.EventStartDateObject.getUTCHours();
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




/*   _         _   _                     _         _                                  _      _       
    / \  _   _| |_| |__   ___  _ __     / \  _   _| |_ ___   ___ ___  _ __ ___  _ __ | | ___| |_ ___ 
   / _ \| | | | __| '_ \ / _ \| '__|   / _ \| | | | __/ _ \ / __/ _ \| '_ ` _ \| '_ \| |/ _ \ __/ _ \
  / ___ \ |_| | |_| | | | (_) | |     / ___ \ |_| | || (_) | (_| (_) | | | | | | |_) | |  __/ ||  __/
 /_/   \_\__,_|\__|_| |_|\___/|_|    /_/   \_\__,_|\__\___/ \___\___/|_| |_| |_| .__/|_|\___|\__\___|
                                                                               |_|                   
////////// ------------ AUTHOR AUTOCOMPLETE CONTROLLER ------------ //////////*/


/*____           _     _     _       _    
 |  _ \ ___  ___| |_  | |   (_)_ __ | | __
 | |_) / _ \/ __| __| | |   | | '_ \| |/ /
 |  __/ (_) \__ \ |_  | |___| | | | |   < 
 |_|   \___/|___/\__| |_____|_|_| |_|_|\_\

////////// ------------ POST LINK CONTROLLER ------------ //////////*/
postworld.controller('postLink', ['$scope', '$log', '$timeout','pwPostOptions','pwEditPostFilters','embedly','ext', 'pwData', '$window', 'pwRoleAccess',
    function($scope, $log, $timeout, $pwPostOptions, $pwEditPostFilters, $embedly, $ext, $pwData, $window, $pwRoleAccess) {

    // Setup the intermediary Link URL
    $scope.link_url = '';

    // Set the default statuss
    $scope.loaded = 'false';

    // Set the default mode
    $scope.mode = "url_input";

    // Set the status
    $scope.status = "done";

    // ROLE ACCESS
    // Sets booleans for role access variables : "editor", "author"
    $pwRoleAccess.setRoleAccess($scope);

    // POST TYPE OPTIONS
    $scope.post_type_options = $pwPostOptions.pwGetPostTypeOptions( 'edit' );
    // POST STATUS OPTIONS
    $scope.post_status_options = $pwPostOptions.pwGetPostStatusOptions( 'link' );
    // POST FORMAT OPTIONS
    $scope.link_format_options = $pwPostOptions.pwGetLinkFormatOptions();
    // POST FORMAT META
    $scope.link_format_meta = $pwPostOptions.pwGetLinkFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();
    
    // GET : TAXONOMY TERMS
    // Gets live set of terms from the DB
    // as $scope.tax_terms
    $pwPostOptions.getTaxTerms($scope, 'tax_terms');

    // TAXONOMY TERM WATCH : Watch for any changes to the post.tax_input
    // Make a new object which contains only the selected sub-objects
    $scope.selected_tax_terms = {};
    $scope.$watch( "post.tax_input",
        function (){
            // Create selected terms object
            $scope.selected_tax_terms = $pwEditPostFilters.selected_tax_terms($scope.tax_terms, $scope.post.tax_input);
            
            // Clear irrelivent sub-terms
            $scope.post.tax_input = $pwEditPostFilters.clear_sub_terms( $scope.tax_terms, $scope.post.tax_input, $scope.selected_tax_terms );
        
        }, 1 );

    // UPDATE AUTHOR NAME FROM AUTOCOMPLETE
    // Interacts with userAutocomplete() controller
    // Catches the recent value of the auto-complete
    $scope.$on('updateUsername', function( event, data ) { 
        $scope.post.post_author_name = data;
    });

    // UPDATE POST TAGS FROM AUTOCOMPLETE MODULE
    // Interacts with tagsAutocomplete() controller
    // Catches the recent value of the tags_input and inject into tax_input
    $scope.$on('updateTagsInput', function( event, data ) { 
        $scope.post.tax_input.post_tag = data;
    });

    // DEFAULT POST DATA
    var post_defaults = $window.pwSiteGlobals.post_options.defaults;
    $scope.post = {
        post_title:"",
        post_type: post_defaults.post_link.post_type,
        link_url:"",
        link_format: post_defaults.post_link.link_format,
        post_class: post_defaults.post_link.post_class,
        tags_input:"",
        post_status: post_defaults.post_link.post_status,
        tax_input : $pwPostOptions.pwGetTaxInputModel(),
    };

    // Set Post Class
    var current_user_role = $window.pwGlobals.current_user.roles[0];
    $scope.post.post_class = $window.pwSiteGlobals.roles[current_user_role].post_class;




    //////////////////// EMBEDLY CORE ////////////////////


    // GET URL EXTRACT
    // 1. On detect paste
    $scope.extract_url = function() {

        $scope.status = "busy";
        $embedly.liveEmbedlyExtract( $scope.link_url ).then( // 
                // Success
                function(response) {
                    console.log(response);    
                    $scope.embedly_extract = response;
                    $scope.status = "done";
                },
                // Failure
                function(response) {
                    //alert('Could not find URL.');
                    throw {message:'Embedly Error'+response};
                    $scope.status = "done";
                }
            );
    }

    $scope.reset_extract = function() {
        $scope.embedly_extract = {};
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

            // Here Process the data from embedly.extract into the post format
            if( $scope.mode == "post_input" ){
                // Translate Embedly Object into WP Object
                $scope.embedly_extract_translated = $embedly.translateToPostData( $scope.embedly_extract );
                // Merge it with the current post
                $scope.post = $ext.mergeRecursiveObj( $scope.post, $scope.embedly_extract_translated ) ;
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
                $scope.post.thumbnail_url = $scope.embedly_extract_image_meta.images[newValue].url;
            else
                $scope.post.thumbnail_url = "";
        }, 1 );

    // TAXONOMY TERM WATCH : Watch for any changes to the post.tax_input
    // Make a new object which contains only the selected sub-objects
    $scope.selected_tax_terms = {};
    $scope.$watch( "post.tax_input",
        function (){
            // Create selected terms object
            $scope.selected_tax_terms = $pwEditPostFilters.selected_tax_terms($scope.tax_terms, $scope.post.tax_input);
            // Clear irrelivent sub-terms
            $scope.post.tax_input = $pwEditPostFilters.clear_sub_terms( $scope.tax_terms, $scope.post.tax_input, $scope.selected_tax_terms );
        }, 1 );

    // LINK_URL WATCH : Watch for changes in link_url
    // Evaluate the link_format
    $scope.$watchCollection('[post.link_url, post.link_format]',
        function ( newValue, oldValue ){
            $scope.post.link_format = $pwEditPostFilters.evalPostFormat( $scope.post.link_url, $scope.link_format_meta );
        });



    //////////////////// END EMBEDLY CORE ////////////////////











    /////----- SAVE POST FUNCTION -----//////
    $scope.savePost = function(pwData){
        $scope.status = "busy";

        ///// SANITIZE FIELDS /////
        if ( typeof $scope.post.link_url === 'undefined' )
            $scope.post.link_url = '';

        ///// DEFINE POST DATA /////
        var post = $scope.post;

        //alert( JSON.stringify( post ) );
        $log.debug('pwData.pw_save_post : POSTING LINK : ', post);

        ///// SAVE VIA AJAX /////
        $pwData.pw_save_post( post ).then(
            // Success
            function(response) {    
                //alert( "RESPONSE : " + response.data );
                $log.debug('pwData.pw_save_post : RESPONSE : ', response.data);
                // VERIFY POST CREATION
                // If it was created, it's an integer
                if( response.data === parseInt(response.data) ){
                    // SAVE SUCCESSFUL
                    var post_id = response.data;
                    $scope.status = "success";
                    $scope.mode = "success";
                    $timeout(function() {
                      $scope.status = "done";
                    }, 2000);
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
                  $scope.postLinkForm.$setValidity('busy',true);
                }, 2000);

            }
        );

    
    }
    /////----- END SAVE POST FUNCTION -----//////

    // ADD ERROR SUPPORT


}]);



/*____        _         ____  _      _             
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



/* ___        _      _      _____    _ _ _   
  / _ \ _   _(_) ___| | __ | ____|__| (_) |_ 
 | | | | | | | |/ __| |/ / |  _| / _` | | __|
 | |_| | |_| | | (__|   <  | |__| (_| | | |_ 
  \__\_\\__,_|_|\___|_|\_\ |_____\__,_|_|\__|
                                             
////////// ------------ QUICK EDIT ------------ //////////*/  

/*///////// ------- SERVICE : PW QUICK EDIT ------- /////////*/  
postworld.service('pwQuickEdit', [ '$rootScope', '$log', '$location', '$modal', 'pwData',
    function ( $rootScope, $log, $location, $modal, pwData ) {
    return{
        openQuickEdit : function( post, mode ){
            
            // Default Defaults
            if( _.isUndefined( mode ) )
                var mode = 'quick-edit';

            $log.debug( "Launch Quick Edit : MODE : " + mode, post );

            var modalInstance = $modal.open({
              templateUrl: pwData.pw_get_template('panels','','quick_edit'),
              controller: 'quickEditInstanceCtrl',
              windowClass: 'quick_edit',
              resolve: {
                post: function(){
                    return post;
                },
                mode: function(){
                    return mode;
                }
              }
            });
            modalInstance.result.then(function (selectedItem) {
                //$scope.post_title = post_title;
            }, function () {
                // WHEN CLOSE MODAL
                $log.debug('Modal dismissed at: ' + new Date());
                
                // Clear the URL params
                //$location.url('/');
                $location.path('/');
                //$rootScope.$apply();

            });
        },

        trashPost : function ( post_id, scope ){
            if ( window.confirm("Are you sure you want to trash : \n" + scope.post.post_title) ) {
                pwData.pw_trash_post( post_id ).then(
                    // Success
                    function(response) {
                        if (response.status==200) {
                            $log.debug('Post Trashed RETURN : ',response.data);                  
                            if ( _.isNumber(response.data) ){
                                var trashed_post_id = response.data;
                                if( typeof scope != undefined ){
                                    // SUCESSFULLY TRASHED
                                    //var retreive_url = "/wp-admin/edit.php?post_status=trash&post_type="+scope.post.post_type;
                                    scope.post.post_status = 'trash';
                                    // Emit Trash Event : post_id
                                    scope.$emit('trashPost', trashed_post_id );
                                    // Broadcast Trash Event : post_id
                                    scope.$broadcast('trashPost', trashed_post_id );
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


postworld.controller('quickEditInstanceCtrl',
    [ '$scope', '$rootScope', '$sce', '$modalInstance', 'post', 'mode', 'pwData', '$timeout', 'pwQuickEdit',
    function($scope, $rootScope, $sce, $modalInstance, post, mode, $pwData, $timeout, $pwQuickEdit) {

    // Set Default Mode
    $scope.mode = ( !_.isUndefined( mode ) ) ?
        mode : "quick-edit";

    // Import the passed post object into the Modal Scope
    $scope.post = post;
    //alert( JSON.stringify( $scope.post ) );

    // TIMEOUT
    // Allow editPost Controller to Initialize
    if( !_.isUndefined( post.ID ) &&
        mode != 'new' ){
        // Broadcast to Load in the Post Data
        $timeout(function() {
          $scope.$broadcast('loadPostData', post.ID );
        }, 3);
    }
    
    // MODAL CLOSE
    $scope.close = function () {
        $modalInstance.dismiss('close');
    };

    // TRASH POST
    $scope.trashPost = function(){
        $pwQuickEdit.trashPost($scope.post.ID, $scope);
    }; 

    // WATCH FOR TRASHED
    // Close Modal
    // Set Parent post_status = trash
    // Watch on the value of post_status
    $scope.$watch( "post.post_status",
        function (){
            if( $scope.post.post_status == 'trash'  )
                $modalInstance.dismiss('close');
        }); 

}]);


postworld.directive( 'pwModalAccess', [ function($scope){
    return {
        restrict: 'AE',
        controller: 'pwModalAccessCtrl',
        link: function( $scope, element, attrs ){
            // OBSERVE Attribute
            //attrs.$observe('postsModel', function(value) {
            //  alert(value);
            //});
        }
    };
}]);

postworld.controller('pwModalAccessCtrl',
    ['$scope', '$rootScope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
    'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', 'ext', '$window', 'pwRoleAccess', 'pwQuickEdit',
    function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
        $pwData, $log, $route, $routeParams, $location, $http, $ext, $window, $pwRoleAccess, $pwQuickEdit ) {

    $scope.newPostModal = function( post ){
        // Open Modal
        $pwQuickEdit.openQuickEdit( post, 'quick-edit-new' );

    }

}]);
