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
			// Init Edit Post Object
			$scope.initEditPost = {};

			// Edit Post Config Setup
			$scope.editPostConfig = {
				routing: true,		// Enables Routing on the URL
				autoReload: true,	// Auto reloads the post data from the DB after saving
			};

			// OBSERVE Attribute
			attrs.$observe('postType', function(value) {
				if( !_.isUndefined( value ) )
					$scope.initEditPost['post_type'] = value;
			});

			// OBSERVE Attribute
			// Edit Mode is for changing the behavior of the routing while editing a post
			// if edit-mode='inline', the routing functionality will be bypassed
			attrs.$observe('editMode', function(value) {
				if( !_.isUndefined( value ) )
					$scope.initEditPost['editMode'] = value;
					if( value == 'inline-submit' ){
						$scope.editPostConfig.routing = false;
						$scope.editPostConfig.autoReload = false;
					}
			});

			// OBSERVE Attribute
			// Save Callback evaluates on submitting a post to be saved
			attrs.$observe('saveSubmitCallback', function(value) {
				if( !_.isUndefined( value ) )
					$scope.initEditPost['saveSubmitCallback'] = value;
			});

			// OBSERVE Attribute
			// Save Success Callback evaluates on success of saving a post
			attrs.$observe('saveSuccessCallback', function(value) {
				if( !_.isUndefined( value ) )
					$scope.initEditPost['saveSuccessCallback'] = value;
			});

			// OBSERVE Attribute
			// Save Callback Error evaluates on error saving a post
			attrs.$observe('saveErrorCallback', function(value) {
				if( !_.isUndefined( value ) )
					$scope.initEditPost['saveErrorCallback'] = value;
			});

			// OBSERVE Attribute
			// Init Callback on initiating a new post
			attrs.$observe('initCallback', function(value) {
				if( !_.isUndefined( value ) )
					$scope.initEditPost['initCallback'] = value;
			});


		}
	};
}]);

postworld.controller('editPost',
	['$scope', '$rootScope', 'pwPostOptions', 'pwEditPostFilters', '$timeout', '$filter',
	'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', 'ext', '$window', 'pwRoleAccess', 'pwQuickEdit', '_',
	function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
		$pwData, $log, $route, $routeParams, $location, $http, $ext, $window, $pwRoleAccess, $pwQuickEdit, $_ ) {

	// Set the default mode
	if( _.isUndefined( $scope.mode ) )
		$scope.mode = 'default';

	// Define Global Edit Post Defaults
	var postDefaults = $window.pwSiteGlobals.edit_post.post['new']['default'];
	var editPostGlobals = $window.pwSiteGlobals.edit_post;
	
	// If No Post Object exists, create one
	$timeout( function(){
		if( _.isUndefined( $scope.post ) ){
			$scope.setPostObject({});
		}
	}, 1 );
	
	$scope.clearAndClose = function(){
		// Clear Embedly Extract
		$scope.clearExtract();

		// Clear Post (not working in modal context properly)
		//var post_type = $scope.post.post_type;
		//$scope.post = {};
		//$scope.setPostObject( { 'post_type' : post_type } );

		// Close Modal
		$scope.close();

	};

	// Define Default Post
	$scope.getSelectedLinkFormatMeta = function(){
		
		if(!$ext.objExists( $scope, 'post.link_format' ))
			return false;
		
		var link_format = $scope.post.link_format;
		return _.where( $scope.link_format_meta, { "slug" : link_format } );
	}

	$scope.$watch("post.link_format", function (){
		var selectedLinkFormatMeta = $scope.getSelectedLinkFormatMeta();
		$scope.selectedLinkFormatMeta = selectedLinkFormatMeta[0];
	}, 1);


	$scope.setPostObject = function( post ){
		// Given any even incomplete post object
		// This function will build it into a safe post object
		// And set it in the Scope

		//post_type, post_format
		$scope.status = "loading";

		///// DETECT POST TYPE /////
		// Check for Post Type Defined by the 'input' Post Object
		if( !_.isUndefined( post.post_type ) ){
			var post_type = post.post_type;
		}
		// Check for Post Type Defined by the 'scope' Post Object
		else if( $ext.objExists( $scope, 'post.post_type' ) ){
			var post_type = $scope.post.post_type;
		}
		// Check for Post Type Defined by the attribute 'post-type'
		else if( $_.objExists( $scope, 'initEditPost.post_type' ) ){
			var post_type = $scope.initEditPost.post_type;
		}

		else{
			var post_type = "post";
		}

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
			post_status : postDefaults.post_status,
			post_class : postDefaults.post_class,
			link_url : "",
			link_format : postDefaults.link_format,
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

		// Post Initilize Callback
		if( !_.isUndefined( $scope.initEditPost['initCallback'] ) )
			$scope.$eval( $scope.initEditPost['initCallback'] );

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

		//if( !$_.objExists( edit_post, post_type ) )
		if( _.isUndefined( edit_post[post_type] ) )
			// Fallback on post_type
			post_type = 'post';

		// Check if the requested post format is defined
		if( !$_.objExists( edit_post, post_type + ".new." + post_format ) )
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
		if( $ext.objExists( $scope, 'post.post_type' ) &&
			$scope.editPostConfig.routing == true )
			$location.path('/new/' + $scope.post.post_type);

	}

	///// NEW QUICK EDIT POST /////
	if( $scope.mode == 'quick-edit-new' ){
		//alert( $scope.post.post_type ); 
		$scope.setPostObject( { 'post_type':$scope.post.post_type } );
		$scope.status = "done";
	}
	
	///// WATCH : ROUTE /////

	$scope.$on(
		"$routeChangeSuccess",
		function( $currentRoute, $previousRoute ){

			// TEMP : For DEV
			$scope.routeAction = $route.current.action;

			// Stop here if routing is disabled
			if( $scope.editPostConfig.routing == false )
				return false;

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

				// Set Default Post Type
				if( post_type == "" ){
					
					// Get the post type from the directive attributes
					if( !_.isUndefined( $scope.initEditPost.post_type ) )
						post_type = $scope.initEditPost.post_type;

					// Get the post type from the default post definition
					if( !_.isUndefined( postDefaults.post_type ) ){
						post_type = postDefaults.post_type;
						alert( post_type );
					}

				}

				// If Post Type has been defined in the route, update the post model
				if ( post_type != "" ){
					// If the post object is not defined, set it
					if( _.isUndefined($scope.post) )
						$scope.post = {};
					// Set the post type
					$scope.post.post_type = post_type;
				}

				// Set a new post object
				$scope.setPostObject( { 'post_type':post_type } );

				// Set the status
				$scope.status = "done";
				// Set the mode
				$scope.mode = "new";
			}
			///// ROUTE : EDIT POST /////
			else if ( $route.current.action == "edit_post"  ){ // && typeof $scope.post.post_id !== 'undefined'
				// Load the specified post data
				$scope.loadEditPost();
				$mode = "edit";
			}
			///// ROUTE : SET DEFAULT /////
			else if ( $route.current.action == "default"  ){
				$location.path('/new/' + postDefaults.post_type );
			}
		}
	);


	///// QUICK EDIT : LOAD POST DATA /////
	$scope.$on('loadPostData', function(event, post_id) {
		$scope.loadEditPost( post_id );
	});

	///// LOAD POST DATA /////
	$scope.loadEditPost = function( post_id ){

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
				//$timeout( function(){
					if( $scope.editPostConfig.routing == true )
						$location.path('/edit/' + get_post.ID);
				//}, 10 );
				

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
	$scope.savePost = function(){

		// EVALUATE CALLBACK
		if( !_.isUndefined( $scope.initEditPost['saveSubmitCallback'] ) )
			$scope.$eval( $scope.initEditPost['saveSubmitCallback'] );

		// VALIDATE THE POST
		if ($scope.post.post_title == '' || _.isUndefined( $scope.post.post_title ) ){
			alert("Post not saved : missing Title field.");
			return false;
		}

		///// GET post FROM TINYMCE /////
		if ( typeof tinyMCE != 'undefined' )
			if ( !_.isUndefined( tinyMCE.get('post_content') )  )
				$scope.post.post_content = tinyMCE.get('post_content').getContent();

		///// SANITIZE FIELDS /////
		if ( _.isUndefined( $scope.post.link_url ) )
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

					// RELOAD POST
					if( $scope.editPostConfig.autoReload == true )
						$scope.loadEditPost( post_id );
					
					// ACTION BROADCAST
					// For Quick Edit Mode - broadcast to children successful update
					$rootScope.$broadcast('postUpdated', post_id);

					// ACTION EMIT
					// Any sibling or parent scope can listen on this action
					$scope.$emit('postUpdated', post_id);

					// EVALUATE CALLBACK
					if( !_.isUndefined( $scope.initEditPost['saveSuccessCallback'] ) )
						$scope.$eval( $scope.initEditPost['saveSuccessCallback'] );
					
				}
				else{
					// ERROR
					if( typeof response.data == 'object' )
						alert("Error : " + JSON.stringify(response.data) );
					else
						alert("ERROR : RESPONSE : (LOGGED IN?) : " + JSON.stringify( response ) );
					$scope.status = "done";

					// EVALUATE CALLBACK
					if( !_.isUndefined( $scope.initEditPost['saveErrorCallback'] ) )
						$scope.$eval( $scope.initEditPost['saveErrorCallback'] );

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
		if( $_.objExists( $scope, 'post.tax_input' ) )
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

	

	// Wait for the controller to initialize
	//$timeout( function(){
		// WATCH : POST TYPE
		$scope.$watch( "post.post_type",
			function (){

				// Check if Post Type Exists, if not stop here
				if( !$_.objExists( $scope, 'post.post_type' ) )
					return false;

				// ROUTE CHANGE
				if( $scope.mode == "new" && $scope.editPostConfig.routing == true ){
					//alert( "MODE:" + $scope.mode );
					$location.path('/new/' + $scope.post.post_type);
				}

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

	//}, 1 );


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


		if( $ext.objExists( $scope, 'post.image' ) &&
			!_.isEmpty($scope.post.image) )
			return true;
		else
			return false;


		//------------------------------------\\
		/*
		if( !$ext.objExists( $scope, 'post.image' ) ||
			_.isEmpty($scope.post.image) )
			return false;

		if( !_.isUndefined($scope.post.thumbnail_id) &&
			$scope.post.thumbnail_id !== "" &&
			$scope.post.thumbnail_id !== "delete" )
			return true;
		else
			return false;
		*/
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
		// SETUP // INITIALIZE DATE OBJECTS
		// If this is enabled, it causes post.post_meta.date_obj functionality to break
		// TODO : Refactor eventInput as an isolated scope directive

		//alert( JSON.stringify( $scope.post ) );

		/*
		if( !$ext.objExists( $scope, 'post' ) )
			$scope.post = {};
		*/
		/*
		if( _.isUndefined( $scope.post.post_meta ) )
			$scope.post.post_meta = {};
		*/
		/*	
		if( !$ext.objExists( $scope, 'post.post_meta.date_obj' ) ) // _.isUndefined( $scope.post.post_meta.date_obj )
			$scope.post.post_meta.date_obj = {};
		*/
		/*
		if( typeof $scope.post.post_meta.date_obj.event_start_date_obj === 'undefined' )
			$scope.post.post_meta.date_obj.event_start_date_obj = new Date( );
		if( typeof $scope.post.post_meta.date_obj.event_end_date_obj === 'undefined' )
			$scope.post.post_meta.date_obj.event_end_date_obj = new Date( );
		*/

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



