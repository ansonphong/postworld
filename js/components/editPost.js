
function tinyMCE_init_custom(){
    //alert( "TINYMCE INITIALIZED" );
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
    'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', 'siteOptions', 'ext', '$window', 'pwRoleAccess',
    function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
        $pwData, $log, $route, $routeParams, $location, $http, $siteOptions, $ext, $window, $pwRoleAccess ) {

    $scope.status = "loading";
    $scope.post_data = {};

    // SET : DEFAULT POST DATA
    $scope.default_post_data = {
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
            hilight : [],
            post_tag : [],
        },
        tags_input : "",
        post_meta:{},
    };

    //alert( JSON.stringify( $route.current.action ) );
    //$scope.mode = "edit";

    // ROLE ACCESS
    // Sets booleans for role access variables : "editor", "author"
    $pwRoleAccess.setRoleAccess($scope);


    ///// WATCH : ROUTE /////
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
                $scope.clear_post_data();
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


    ///// LOAD POST DATA /////
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
                $log.debug('pwData.pw_get_post_edit : RESPONSE : ', response.data);

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
                $scope.set_post_content( get_post_data.post_content );

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

    // SET POST CONTENT
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
    

    /////----- SAVE POST FUNCTION -----//////
    $scope.savePost = function(pwData){
        //alert( tinyMCE.get('post_content').getContent() );//tinyMCE.editors.content.getContent() );
        //alert( JSON.stringify($scope.post_data) );
        // VALIDATE THE FORM
        if ($scope.post_data.post_title != '' || typeof $scope.post_data.post_title !== 'undefined'){
            //alert(JSON.stringify($scope.post_data));

            ///// GET POST_DATA FROM TINYMCE /////
            if ( typeof tinyMCE !== 'undefined' )
                if ( typeof tinyMCE.get('post_content') !== 'undefined'  )
                    $scope.post_data.post_content = tinyMCE.get('post_content').getContent();
            

            ///// SANITIZE FIELDS /////
            if ( typeof $scope.post_data.link_url === 'undefined' )
                $scope.post_data.link_url = '';

            ///// DEFINE POST DATA /////
            var post_data = $scope.post_data;

            //alert( JSON.stringify( post_data ) );
            $log.debug('pwData.pw_save_post : SUBMITTING : ', post_data);

            ///// SAVE VIA AJAX /////
            $scope.status = "saving";
            $pwData.pw_save_post( post_data ).then(
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
                        if( typeof response.data == 'object' )
                            alert("Error : " + JSON.stringify(response.data) );
                        else
                            alert("Error : " + response.data );
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

    ///// CLEAR POST DATA /////
    $scope.clear_post_data = function(){
        //$scope.post_data = {};
        $scope.post_data = $scope.default_post_data;

        $timeout(function() {
            if( typeof tinyMCE !== 'undefined' ){
                if( typeof tinyMCE.get('post_content') !== 'undefined' ){
                    //$log.debug('RESET tinyMCE : ', tinyMCE);
                    tinyMCE.get('post_content').setContent( "" );
                }
            }
        }, 1);

    }

    ///// GET POST OBJECT /////
    $scope.pw_get_post_object = function(){
        var post_data = $scope.default_post_data;

        // SET THE POST CLASS
        if ( $scope.roles.author == true || $scope.roles.editor == true ){
            post_data.post_class = "author";
        }
        else{
            post_data.post_class = "contributor";
        }

        // CHECK TERMS CATEGORY / SUBCATEGORY ORDER
        post_data = $pwEditPostFilters.sortTaxTermsInput( post_data, $scope.tax_terms, 'tax_input' );
        return post_data;   
    }


    ///// LOAD IN DATA /////
    // POST TYPE OPTIONS
    $scope.post_type_options = $pwPostOptions.pwGetPostTypeOptions( 'edit' );
    // POST FORMAT OPTIONS
    $scope.post_format_options = $pwPostOptions.pwGetPostFormatOptions();
    // POST FORMAT META
    $scope.post_format_meta = $pwPostOptions.pwGetPostFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();

    // ACTION : AUTHOR NAME FROM AUTOCOMPLETE MODULE
    // • Interacts with userAutocomplete() controller
    // • Catches the recent value of the auto-complete
    $scope.$on('updateUsername', function( event, data ) { 
        $scope.post_data.post_author_name = data;
    });

    // ACTION : POST TAGS FROM AUTOCOMPLETE MODULE
    // • Interacts with tagsAutocomplete() controller
    // • Catches the recent value of the tags_input and inject into tax_input
    $scope.$on('updateTagsInput', function( event, data ) { 
        $scope.post_data.tax_input.post_tag = data;
    });

    // GET : TAXONOMY TERMS
    // • Gets live set of terms from the DB as $scope.tax_terms
    $pwPostOptions.getTaxTerms( $scope, 'tax_terms' );

    // WATCH : TAXONOMY TERMS
    // • Watch for any changes to the post_data.tax_input
    // • Make a new object which contains only the selected sub-objects
    $scope.selected_tax_terms = {};
    $scope.$watch('[ post_data.tax_input, tax_terms ]',
        function ( newValue, oldValue ){
            if ( typeof $scope.tax_terms !== 'undefined' ){
                // Create selected terms object
                $scope.selected_tax_terms = $pwEditPostFilters.selected_tax_terms($scope.tax_terms, $scope.post_data.tax_input);
                // Clear irrelivent sub-terms
                $scope.post_data.tax_input = $pwEditPostFilters.clear_sub_terms( $scope.tax_terms, $scope.post_data.tax_input, $scope.selected_tax_terms );
            }
        }, 1);

    // WATCH : LINK_URL
    // • Watch for changes in Link URL field
    // • Evaluate the Post Format
    $scope.$watchCollection('[ post_data.link_url, post_data.post_format ]',
        function ( newValue, oldValue ){
            $scope.post_data.post_format = $pwEditPostFilters.evalPostFormat( $scope.post_data.link_url, $scope.post_format_meta );
        });


    // WATCH : POST TYPE
    $scope.$watch( "post_data.post_type",
        function (){
            // ROUTE CHANGE
            if( $scope.mode == "new" )
                $location.path('/new/' + $scope.post_data.post_type);

            // BROADCAST CHANGE TO CHILD CONTROLLERS NODES
            $rootScope.$broadcast('changePostType', $scope.post_data.post_type );
 
            // POST STATUS OPTIONS
            // Re-evaluate available post_status options on post_type switch
            $scope.post_status_options = $pwPostOptions.pwGetPostStatusOptions( $scope.post_data.post_type );
            
            // SET DEFAULT POST STATUS
            if ( $scope.post_data.post_status == null || $scope.post_data.post_status == '' )
                angular.forEach( $scope.post_status_options, function(value, key){
                    //return key;
                    $scope.post_data.post_status = key;
                });


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

    // FORM VALIDATION WATCH
    $scope.$watch( "editPost.$valid",
        function (){
        
        }, 1 );

    // POST DATA OBJECT
    $scope.post_data = $scope.pw_get_post_object();
    //alert(JSON.stringify($scope.post_data));

    $scope.showEditorSource = function(){
        var source = $('#post_content').val();
        source = tinyMCE.get('post_content').getContent({format : 'raw'});
        alert(source);

    };
    
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

            // If start time is set after the end time - make them equal
            if( $scope.post_data.post_meta.event_end_date_obj < $scope.post_data.post_meta.event_start_date_obj )
                $scope.post_data.post_meta.event_end_date_obj = $scope.post_data.post_meta.event_start_date_obj;

        }, 1 );

    // WATCH : EVENT END TIME
    $scope.$watch( "post_data.post_meta.event_end_date_obj",
        function (){
            $scope.post_data.post_meta.event_end_date = $filter('date')(
                $scope.post_data.post_meta.event_end_date_obj, 'yyyy-MM-dd HH:mm' );

            // If end time is set before the start time - make them equal
            if( $scope.post_data.post_meta.event_start_date_obj > $scope.post_data.post_meta.event_end_date_obj  )
                $scope.post_data.post_meta.event_start_date_obj = $scope.post_data.post_meta.event_end_date_obj ;

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
postworld.controller('postLink', ['$scope', '$log', '$timeout','pwPostOptions','pwEditPostFilters','embedly','ext', 'pwData', '$window', 'pwRoleAccess',function($scope, $log, $timeout, $pwPostOptions, $pwEditPostFilters, $embedly, $ext, $pwData, $window, $pwRoleAccess) {

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
    $scope.post_format_options = $pwPostOptions.pwGetPostFormatOptions();
    // POST FORMAT META
    $scope.post_format_meta = $pwPostOptions.pwGetPostFormatMeta();
    // POST CLASS OPTIONS
    $scope.post_class_options = $pwPostOptions.pwGetPostClassOptions();
    
    // GET : TAXONOMY TERMS
    // Gets live set of terms from the DB
    // as $scope.tax_terms
    $pwPostOptions.getTaxTerms($scope, 'tax_terms');

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

    // DEFAULT POST DATA
    $scope.post_data = {
        post_title:"Link Title",
        post_type:"link",
        link_url:"",
        post_format:"standard",
        post_class:"contributor",
        tags_input:"",
        post_status:"publish",
        tax_input : {
            topic : [],
            section : [],
            hilight : [],
            post_tag:[]
        }
    };


    // Set Post Class
    if( $scope.roles.editor == true || $scope.roles.author == true )
        $scope.post_data.post_class = "author";
    
    // GET URL EXTRACT
    // 1. On detect paste
    // 2. On click

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
                $scope.post_data.thumbnail_url = $scope.embedly_extract_image_meta.images[newValue].url;
            else
                $scope.post_data.thumbnail_url = "";
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


    /////----- SAVE POST FUNCTION -----//////
    $scope.savePost = function(pwData){
        $scope.status = "busy";

        ///// SANITIZE FIELDS /////
        if ( typeof $scope.post_data.link_url === 'undefined' )
            $scope.post_data.link_url = '';

        ///// DEFINE POST DATA /////
        var post_data = $scope.post_data;

        //alert( JSON.stringify( post_data ) );
        $log.debug('pwData.pw_save_post : POSTING LINK : ', post_data);

        ///// SAVE VIA AJAX /////
        $pwData.pw_save_post( post_data ).then(
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
