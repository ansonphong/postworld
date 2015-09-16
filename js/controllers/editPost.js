'use strict';

/*_____    _ _ _     ____           _   
 | ____|__| (_) |_  |  _ \ ___  ___| |_ 
 |  _| / _` | | __| | |_) / _ \/ __| __|
 | |__| (_| | | |_  |  __/ (_) \__ \ |_ 
 |_____\__,_|_|\__| |_|   \___/|___/\__|

////////// ------------ EDIT POST CONTROLLER ------------ //////////*/

/**
 * @ngdoc directive
 * @name postworld.directive: pwEditPost
 * @description
 * Provides local scope with functionality for easily
 * loading, editing and saving posts.
 */
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
			// Edit Mode
			attrs.$observe('editMode', function(value) {
				if( value == 'new' || value == 'edit' )
					$scope.mode = value;
			});

			// OBSERVE Attribute
			// Enable routing on the edit post - boolean
			attrs.$observe('editRouting', function(value) {
				if( !_.isUndefined( value ) )
					// Calculate string into a boolean
					$scope.editPostConfig['routing'] = ( value === 'true' );
			});

			// OBSERVE Attribute
			// Enable routing on the edit post - boolean
			attrs.$observe('editAutoReload', function(value) {
				if( !_.isUndefined( value ) )
					// Calculate string into a boolean
					$scope.editPostConfig['autoReload'] = ( value === 'true' );
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
			// Save Success Callback evaluates on success of saving a post
			attrs.$observe('loadSuccessCallback', function(value) {
				if( !_.isUndefined( value ) )
					$scope.initEditPost['loadSuccessCallback'] = value;
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
	'embedly', 'pwData', '$log', '$route', '$routeParams', '$location', '$http', '$window',
	'pwRoleAccess', 'pwQuickEdit', '_', '$sce', 'pwTemplatePartials', 'iOptionsData', '$pw',
	function($scope, $rootScope, $pwPostOptions, $pwEditPostFilters, $timeout, $filter, $embedly,
		$pwData, $log, $route, $routeParams, $location, $http, $window,
		$pwRoleAccess, $pwQuickEdit, $_, $sce, $pwTemplatePartials, $iOptionsData, $pw ) {

	$scope['options'] = $iOptionsData['options'];

	$log.debug( "$scope.mode @ init", $scope.mode );

	$log.debug( "$scope.meta @ init", $scope.meta );

	$timeout( function(){
		$log.debug( 'editPost Controller', $scope.initEditPost );
		$log.debug( "$scope.post @ 2ms", $scope.post );
	}, 2 );
	
	//////////////////// INITIALIZE ////////////////////
	$scope.status = 'done';

	// Define Global Edit Post Defaults
	var postDefaults = $pw.config.edit_post.post['new']['default'];

	// Localize Edit Post Object
	if( $_.objExists( $pw, 'config.edit_post' ) )
		$scope.editPostGlobals = $pw.config.edit_post;
	
	// Localize Post Options Object
	if( $_.objExists( $pw, 'config.post_options' ) )
		$scope.postOptions = $pw.config.post_options;


	///// INITIALIZE /////
	$timeout( function(){

		$log.debug( "EDIT MODE : ", $scope.mode );

		///// MODES /////
		// Set the default mode
		if( _.isUndefined( $scope.mode ) )
			$scope.mode = 'new';

		///// SWITCH MODE /////
		switch( $scope.mode ){

			///// MODE : NEW /////
			case 'new':
				// Make a new post in the scope with the universally defined post type
				$scope.newPost( $scope.getPost( { 'post_type':$scope.getPostType() } ) );
				$scope.status = "done";
				break;

			///// MODE : EDIT /////
			case 'edit':
				// If a post ID is specified
				if( $_.objExists( $scope, 'post.ID' ) ){
					// Load the post freshly in edit post mode
					$scope.loadEditPost( $scope.post.ID );
					$scope.status = 'loading';
				}
				break;

		}

	}, 0 );

	// ROLE ACCESS
	// Sets booleans for role access variables : "editor", "author"
	$pwRoleAccess.setRoleAccess($scope);

	//////////////////// FUNCTIONS ////////////////////

	$scope.getPost = function( post ){
		// Checks to see if a post object already exists in the scope
		// And merges the provided post object then returns that

		// Set default provided post value
		if( _.isUndefined( post ) )
			post = {};
		
		// Get the post from the scope and merge it if defined
		if( !_.isUndefined( $scope.post ) )
			post = deepmerge( $scope.post, post );

		return post;
	}

	$scope.getPostOptions = function( option, subkey ){
		// Define which post type to return within a set of options
		// Allows to have a 'default' post type object
		// With the option to over-ride with the current scope post type
		// option ~= "post_excerpt" / "post_title", etc

		if( $_.objExists( $scope, 'postOptions.' + option + '.' + subkey ) )
			return $scope.postOptions[ option ][ subkey ];

		else if( $_.objExists( $scope, 'postOptions.' + option + '.default' ) )
			return $scope.postOptions[ option ][ 'default' ];
		
		else
			return false;
	};

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
		
		if(!$_.objExists( $scope, 'post.link_format' ))
			return false;
		
		var link_format = $scope.post.link_format;
		return _.where( $scope.link_format_meta, { "slug" : link_format } );
	}

	///// SET POST OBJECT /////
	$scope.setPostObject = function( post ){
		// Given any even incomplete post object
		// This function will build it into a safe post object
		// And set it in the Scope

		//post_type, post_format
		//$scope.status = "loading";

		///// DETECT POST TYPE /////
		// Check for Post Type Defined by the 'input' Post Object
		if( !_.isUndefined( post.post_type ) ){
			var post_type = post.post_type;
		}
		// Check for Post Type Defined by the 'scope' Post Object
		else if( $_.objExists( $scope, 'post.post_type' ) ){
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
			post_type : $scope.getPostType(),
			post_status : postDefaults.post_status,
			post_class : postDefaults.post_class,
			link_url : "",
			link_format : postDefaults.link_format,
			post_date_gmt:"",
			post_permalink : "",
			tax_input : $pwPostOptions.taxInputModel(),
			tags_input : "",
			post_meta:{},
		};

		// If post object doesn't contain init defaults, write them in
		angular.forEach( default_post, function(value, key){
			if( _.isUndefined( post[key] ) )
				post[key] = value;
		});

		// CHECK TERMS CATEGORY / SUBCATEGORY ORDER
		// RSV2 SPECIFIC
		post = $pwEditPostFilters.sortTaxTermsInput( post, $scope.tax_terms, 'tax_input' );


		$scope.default_post = post;
		$scope.post = post;

		// SET THE POST CLASS


		// Post Initilize Callback
		if( !_.isUndefined( $scope.initEditPost['initCallback'] ) )
			$scope.$eval( $scope.initEditPost['initCallback'] );

	};

	$scope.getPostType = function( post_type ){
		///// DETECT POST TYPE /////
		// If post type is empty or not defined
		// Define Default Post Type
		if( _.isUndefined( post_type ) ||
			_.isEmpty( post_type ) ||
			post_type == '' ){
			// Check for Post Type defined by directive
			if (!_.isUndefined( $scope.initEditPost.post_type ) )
				var post_type = $scope.initEditPost.post_type;
			// Check for Post Type defined by scope post object
			else if( $_.objExists( $scope, 'post.post_type' ) )
				post_type = $scope.post.post_type;
			else if( $_.objExists( postDefaults, 'post_type' ) )
				post_type = postDefaults.post_type;
			else
				post_type = 'post';
			return post_type;	
		}
		// TODO : Here validate the post_type
		else
			return post_type;

	}

	///// GET DEFAULT POST CONFIG /////
	$scope.getDefaultPostConfig = function( post_type, post_format ){

		post_type = $scope.getPostType( post_type );

		// Define default Post Format
		if( _.isUndefined(post_format) ||
			_.isEmpty(post_format) )
			var post_format = 'default';

		// Localize Edit Post Config
		var edit_post = $pw.config.edit_post;

		// Check if the requested post type is defined

		if( !$_.objExists( edit_post, post_type ) )
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

	///// CLEAR POST DATA /////
	$scope.newPost = function( post ){
		// Set the new mode
		$scope.mode = "new";
		// Set the default empty post if not provided
		if( _.isUndefined( post ) )
			post = {};

		// Set the new post object in scope
		$scope.setPostObject( post );

		// Clear TinyMCE
		$timeout(function() {

			if( typeof tinyMCE !== 'undefined' ){
				if( !_.isUndefined( tinyMCE.get('post_content') ) &&
					!_.isNull( tinyMCE.get('post_content') ) ){
					$log.debug('Clear tinyMCE : ', tinyMCE);
					tinyMCE.get('post_content').setContent( "" );
				}
			}


		}, 2);

		// Set the Route
		if( $_.objExists( $scope, 'post.post_type' ) &&
			$_.get( $scope.editPostConfig, 'routing' ) == true )
			$location.path('/new/' + $scope.post.post_type);

	}

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
		$pwData.getPostEdit( post_id ).then(
			// Success
			function(response) {
				$log.debug('pwData.getPostEdit : RESPONSE : ', response.data);
			
				// FILTER FOR INPUT
				var get_post = response.data;

				///// LOAD TAXONOMIES /////
				// This takes the post.taxonomy key, which contains all the tag meta-data
				// And re-configures it into a tax_input model
				// Then deletes the original taxonomy object
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
						if( taxonomy == "post_tag"){

							$log.debug( 'postTagsObject : $broadcast : Post ID : ' + get_post.ID, terms );
							$rootScope.$broadcast('postTagsObject', {
								postId: get_post.ID,
								taxonomy:'post_tag',
								terms: terms,
							});

						}
					});
					delete get_post['taxonomy'];

				}
				get_post['tax_input'] = tax_input; 
				
				///// LOAD POST CONTENT /////
				// SET THE POST CONTENT
				$scope.set_post_content( get_post.post_content );

				///// LOAD AUTHOR /////
				// EXTRACT AUTHOR NAME
				if ( $_.objExists( get_post, 'author.user_nicename' ) ){
					get_post['post_author_name'] = get_post['author']['user_nicename'];
					delete get_post['author'];
				}
				// BROADCAST TO USERNAME AUTOCOMPLETE FIELD
				$scope.$broadcast('updateUsername', get_post['post_author_name']);

				///// POST META /////
				if ( !_.isUndefined( get_post['post_meta'] ) ){
					// If post_meta is empty, ensure it is an object, not array
					if( _.isEmpty( get_post.post_meta ) )
						get_post.post_meta = {};

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
				if( $_.get( $scope.editPostConfig, 'routing' ) == true )
					$location.path('/edit/' + get_post.ID);

				// SET DATA INTO THE SCOPE
				$scope.post = get_post;

				// EVALUATE CALLBACK
				if( $_.get( $scope.initEditPost, 'loadSuccessCallback' ) )
					$scope.$eval( $scope.initEditPost['loadSuccessCallback'] );

				// UPDATE MODE
				$scope.mode = "edit";
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
					$log.debug( "pw-edit-post: $scope.savePost › $pwData.pw_save_post › SUCCESS : ACTION : BROADCAST & EMIT : 'postUpdated' ", post_id );

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

	///// ON : ROUTE CHANGE /////
	$scope.$on(
		"$routeChangeSuccess",
		function( $currentRoute, $previousRoute ){

			// TEMP : For DEV
			$scope.routeAction = $route.current.action;

			// Stop here if routing is disabled
			if( $_.get( $scope.editPostConfig, 'routing' ) == false ){
				return false;
			}

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
						//alert( post_type );
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
				$scope.status = 'loading';
				$scope.loadEditPost();
				$scope.mode = "edit";
			}
			///// ROUTE : SET DEFAULT /////
			else if ( $route.current.action == "default"  ){
				$location.path('/new/' + postDefaults.post_type );
			}
		}
	);

	////////// FEATURED IMAGE //////////
	// Media Upload Window
	$scope.updateFeaturedImage = function(image_object){
		//alert( JSON.stringify(image_object) );
		$scope.post.image = {};
		$scope.post.image.meta = image_object;
		$scope.post.thumbnail_id = image_object.id;
		if( typeof image_object !== 'undefined' ){
			$scope.hasFeaturedImage = true;
		}
	}

	// Check if Object Exists
	$scope.hasFeaturedImage = function(){
		var fullImgUrl = $_.getObj( $scope, 'post.image.sizes.full.url' );
		if( fullImgUrl && fullImgUrl != null )
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

	$scope.showEditorSource = function(){
		var source = $('#post_content').val();
		source = tinyMCE.get('post_content').getContent({format : 'raw'});
		alert(source);
	};


	//////////////////// WATCHES ////////////////////

	///// ON : LOAD POST DATA /////
	// For Quick Edit
	$scope.$on('loadPostData', function(event, post_id) {
		$scope.loadEditPost( post_id );
	});

	///// WATCH : LINK FORMAT /////
	$scope.$watch("post.link_format", function (){
		var selectedLinkFormatMeta = $scope.getSelectedLinkFormatMeta();
		$scope.selectedLinkFormatMeta = selectedLinkFormatMeta[0];
	}, 1);
	
	///// LOAD IN DATA /////
	// POST TYPE OPTIONS
	$scope.post_type_options = $pwPostOptions.postType( 'edit' );
	// POST FORMAT OPTIONS
	$scope.link_format_options = $pwPostOptions.linkFormat();
	// POST FORMAT META
	$scope.link_format_meta = $pwPostOptions.linkFormatMeta();
	// POST CLASS OPTIONS
	$scope.post_class_options = $pwPostOptions.postClass();

	// ACTION : AUTHOR NAME FROM AUTOCOMPLETE MODULE
	// • Interacts with userAutocomplete() controller
	// • Catches the recent value of the auto-complete
	$scope.$on('updateUsername', function( event, data ) { 
		if( !_.isUndefined( $scope.post ) )
			$scope.post.post_author_name = data;
	});
 
	// ACTION : POST TAGS FROM AUTOCOMPLETE MODULE
	// • Interacts with tagsAutocomplete() controller / pw-autocomplete-tags directive
	// • Catches the recent value of the tags_input and inject into tax_input
	$scope.$on('updateTagsInput', function( event, data ) { 
		// TODO : Support all non-heirarchical taxonomies

		// TAGS PASS-BACK AND FORTH MECHANISM IS BROKEN - DO TESTING PASSING BOTH WAYS

		$log.debug( 'pwEditPost : $on.updateTagsInput : ', data );

		if( data.taxonomy == 'post_tag' && data.postId == $_.get( $scope, 'post.ID' ) ){
			if( _.isUndefined( $scope.post.tax_input ) )
				$scope.post.tax_input = {};
			$scope.post.tax_input.post_tag = data.terms;
		}

		//$scope.post = $_.set( $scope.post, 'tax_input.post_tag', tagSlugs );

	});

	/*
	// ACTION : CREATE NEW POST OBJECT
	// • Creates a new post object in the scope
	$scope.$on( 'newPostObject', function( event, data ){
		//var post_type = $scope.getPostType( data.post_type );
		//$scope.newPost({ 'post_type': post_type });
	});
	*/

	// GET : TAXONOMY TERMS
	// • Gets live set of terms from the DB as $scope.tax_terms
	$pwPostOptions.taxTerms( $scope, 'tax_terms' );



	///// WATCH : LINK URL /////
	// • Watch for changes in Link URL field
	// • Evaluate the Post Format
	$scope.$watchCollection('[ post.link_url, post.link_format ]',
		function ( newValue, oldValue ){
			// Check if Object Exists
			if( $_.objExists( $scope, 'post.link_url' ) )
				$scope.post.link_format = $pwEditPostFilters.evalPostFormat( $scope.post.link_url, $scope.link_format_meta );
		});

	// Wait for the controller to initialize
	///// WATCH : POST TYPE /////
	$scope.$watch( "post.post_type", function(){
			// Check if Post Type Exists, if not stop here
			if( !$_.objExists( $scope, 'post.post_type' ) )
				return false;

			// ROUTE CHANGE
			if( $scope.mode == "new" && $_.get( $scope.editPostConfig, 'routing' ) == true ){
				//alert( "MODE:" + $scope.mode );
				$location.path('/new/' + $scope.post.post_type);
			}

			// BROADCAST CHANGE TO CHILD CONTROLLERS NODES
			$rootScope.$broadcast('changePostType', $scope.post.post_type );
 
			// POST STATUS OPTIONS
			// Re-evaluate available post_status options on post_type switch
			$scope.post_status_options = $pwPostOptions.postStatus( $scope.post.post_type );
			
			// SET DEFAULT POST STATUS
			if ( $scope.post.post_status == null || $scope.post.post_status == '' )
				angular.forEach( $scope.post_status_options, function(value, key){
					//return key;
					$scope.post.post_status = key;
				});

		}, 1 );

	// FORM VALIDATION WATCH
	//$scope.$watch( "editPost.$valid", function (){}, 1 );

	// LANGUAGE CODE WATCH
	// If 'lang' is defined (by pw-language) then add it to the post object
	$scope.$watch( "lang", function (){

		// Check if Post Exists
		if( !$_.objExists( $scope, 'post' ) )
			return false;

		if( !_.isUndefined($scope.lang) )
			$scope.post.language_code = $scope.lang;
	} );


	// Alias of the template partials
	$scope.getTemplatePartial = function( vars ){
		return $pwTemplatePartials.get( vars );
	}


}]);


/*
  _____                 _     ___                   _   
 | ____|_   _____ _ __ | |_  |_ _|_ __  _ __  _   _| |_ 
 |  _| \ \ / / _ \ '_ \| __|  | || '_ \| '_ \| | | | __|
 | |___ \ V /  __/ | | | |_   | || | | | |_) | |_| | |_ 
 |_____| \_/ \___|_| |_|\__| |___|_| |_| .__/ \__,_|\__|
                                       |_|              
//////// ----- EVENT DATA/TIME CONTROLLER ----- ////////*/

postworld.directive( 'pwEventInput',
	[
	'$rootScope',
	'pwPostOptions',
	'pwEditPostFilters',
	'$timeout',
	'$filter',
	'pwData',
	'$log',
	'_',
	'pwDate',
	function(
		$rootScope,
		$pwPostOptions,
		$pwEditPostFilters,
		$timeout,
		$filter, 
		$pwData,
		$log,
		$_,
		$pwDate
		){
	return {
		restrict: 'AE',
		scope:{
			'e':"=pwEventInput",
			/*
			'startDateObj':"=eventObj",
			'endDateObj':"=",
			'startDate':"=",
			'endDate':"=",
			'timezone':"="
			*/
		},
		link: function( $scope, element, attrs ){

			/**
			 * Watch the timezone object for changes
			 * Set the boolean $scope.hasTimezone
			 *
			 * If it has timezone, offset the date/timepicker
			 * To the event time on initialization.
			 */
			/*
			$scope.$watch( function(){
					return $_.get( $scope.e, 'timezone' );
				},
				function( timezone, oldTimezone ){
					if( !_.isEmpty( timezone ) &&
						timezone !== false )
						$scope.hasTimezone = true;
					else
						$scope.hasTimezone = false;

					if( $scope.hasTimezone ){
						var clientTimezone = $pwDate.getTimezone();
						var eventTimezone = $pwDate.getTimezone( timezone.time_zone_id );
					
						var eventStart = moment.tz($scope.e.date.start_date, timezone.time_zone_id );

						$log.debug( 'eventStart : AT EVENT LOCATION', eventStart.format() );

						var clientTimezone = jstz.determine().name();
						var eventStartClient = eventStart.clone().tz( clientTimezone );

						$log.debug( 'eventStart : AT client LOCATION', eventStartClient.format() );


						//$scope.e.date.local_start_date_obj = 'test';

					}

				}, 1 );

			*/
			/*
				@todo - Refactor how the timezones work

				X *start_date* - store literal time at location
				X *end_date* - store literal time at location

				*start_date_obj*
					- this is potentially temp data
					- re-evaluate it on inititialization IF there is a timezone
					- if there is a timezone
						- 	calculate the difference between
							the current client's timezone,
							and the timezone it's in and make an
							offset to trick the UTC time to being
							synonymous with the timezone of the location

				*timezone*
					- store the timezone object

				*start_date* (unix)
					- process based on timezone
					- if no timezone, use literal

				*end_date* (unix)
					- process based on timezone
					- if no timezone, use literal

			*/

			$scope.getUnixTimestamp = function( dateObject ){
				if( !_.isUndefined( dateObject ) ){
					var localDateObj = new Date(dateObject);
					return Math.round( localDateObj.getTime() / 1000);
				}
			};

			$scope.setUnixTimestamps = function(){
			
				// Add the UNIX Timestamp : event_start
				if( !_.isUndefined( $scope.e.date.start_date_obj ) && !_.isUndefined( $scope.$parent.post ) )
					$scope.$parent.post.event_start = $scope.getUnixTimestamp( $scope.e.date.start_date_obj );
				
				// Add the UNIX Timestamp : event_end
				if( !_.isUndefined( $scope.e.date.end_date_obj ) && !_.isUndefined( $scope.$parent.post )  )
					$scope.$parent.post.event_end = $scope.getUnixTimestamp( $scope.e.date.end_date_obj );
			
			};

			// WATCH : EVENT START TIME
			$scope.$watch( 'e.date.start_date_obj',
				function (){
					$log.debug( 'CHANGED : e.date.start_date_obj' );

					// End function if variable doesn't exist
					//if( _.isUndefined( $scope.e.date.start_date_obj ) )
					//	return false;

					// Set the alternate date format
					$scope.e.date.start_date = $filter('date')(
						$scope.e.date.start_date_obj, 'yyyy-MM-dd HH:mm' );

					// If start time is set after the end time - make them equal
					if( $scope.e.date.end_date_obj < $scope.e.date.start_date_obj )
						$scope.e.date.end_date_obj = $scope.e.date.start_date_obj;

					// Set UNIX Timestamps
					$scope.setUnixTimestamps();

				}, 1 );

			// WATCH : EVENT END TIME
			$scope.$watch( 'e.date.end_date_obj',
				function (){
					$log.debug( 'CHANGED : e.date.end_date_obj' );

					// End function if variable doesn't exist
					//if( _.isUndefined( $scope.e.date.end_date_obj ) )
					//	return false;

					// Set the alternate date format
					$scope.e.date.end_date = $filter('date')(
						$scope.e.date.end_date_obj, 'yyyy-MM-dd HH:mm' );

					// If end time is set before the start time - make them equal
					if( $scope.e.date.start_date_obj > $scope.e.date.end_date_obj )
						$scope.e.date.start_date_obj = $scope.e.date.end_date_obj;

					// Set UNIX Timestamps
					$scope.setUnixTimestamps();

				}, 1 );

			////////// TIME PICKER : CONFIG //////////
			$scope.$parent.eventOptions = {
				// Time Picker
				hstep: 1,
				mstep: 1,
				meridian: true,
				
				// Date Picker
				//'year-format': "'yy'",
				//'starting-day': 1
			};

			// Toggle AM/PM // 24H
			$scope.$parent.toggleMeridian = function() {
				$scope.$parent.eventOptions.meridian = ! $scope.$parent.eventOptions.meridian;
			};

			// Example (bind to ng-change)
			$scope.$parent.dateChanged = function () {

			};

		}

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



