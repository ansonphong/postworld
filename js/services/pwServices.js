'use strict';

/* _                
  | | _ ____      __
 / __) '_ \ \ /\ / /
 \__ \ |_) \ V  V / 
 (   / .__/ \_/\_/  
  |_||_|            
//////////////////*/

postworld.factory( '$pw',
	function ($resource, $q, $log, $window, $_, $location ) {   

	// TEMPLATES
	var pwTemplates = ( $_.objExists( $window, 'pw.templates' ) ) ?
		$window.pw.templates : {};

	var pwUser = function(){
		if( !$_.objExists( $window, "pw.user" ) )
			return false;
		return $window.pw.user;
	}

	// DECLARATIONS
	return {
		//version: $window.pw.info.version,		// Todo, front load from PHP var
		info: $window.pw.info,
		templates: pwTemplates,

		state:{
			modals:{
				open:0,
			},
			keybindings:{},
		},

		user: pwUser(), //$window.pw.user, // (or something) - refactor to go directly to pwUser
		// view: $window.pw.view
		// language: $window.pwSiteLanguage,

		config: $window.pw.config,
		view: $window.pw.view,
		query: $window.pw.query,
		paths: $window.pw.config.paths,
		site: $window.pw.config.site,
		controls: $window.pw.config.controls,
		fields: $window.pw.config.fields,
		modules: $window.pw.modules,
		iconsets: $window.pw.iconsets,
		postTypes: $window.pw.config.post_types,
		postViews: $window.pw.config.post_views,
		taxonomies: $window.pw.config.taxonomies,
		options: $window.pw.options,
		optionsMeta: $window.pw.optionsMeta,
		device: $window.pw.device,
		posts: $window.pw.posts,

		// Get the admin data, will only be present if is_admin()
		admin: $_.get( $window, 'pw.admin' ),

		// Return string, the current device type: mobile | tablet | desktop
		getDeviceType: function(){
			if( !_.contains( $window.pw.info.modules, 'devices') )
				return false;
			var device = $window.pw.device;
			if( device.is_mobile )
				return 'mobile';
			else if( device.is_tablet )
				return 'tablet';
			else
				return 'desktop';
		},

		moduleEnabled: function( module ){
			$log.debug('$window.pw.info.modules', $window.pw.info.modules);
			return _.contains( $window.pw.info.modules, module );
		},

		pluginUrl: function(value){
			if( !_.isUndefined(value) )
				value = "/postworld/" + value;
			else
				value = "/postworld/";

			return $window.pw.config.wordpress.plugins_url + value;
		},
		loadScript: function( url, callback ){
			// Adding the script tag to the head as suggested before
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = url;

			// Then bind the event to the callback function.
			// There are several events for cross browser compatibility.
			script.onreadystatechange = callback;
			script.onload = callback;

			// Fire the loading
			head.appendChild(script);
		},

		setKeybindings: function( contextObj ){
			// Sets a context to enable keybindings for
			/*
				contextObj = {
					feedId: 	// optional
					postId: 	// optional
					context: 	// optional, could be 'gallery' etc
				}
			*/
			this.state.keybindings = contextObj;
			$log.debug( '$pw.setkeybindings() : ', this.state.keybindings );
		},

		hasKeybindings: function( contextObj ){
			// Returns true/false if the key/value matches the keybindings state
			if( _.isEmpty(contextObj) )
				return false;
			if( _.size(contextObj) != _.size(this.state.keybindings) )
				return false;

			var has = true;
			// Localize this state
			var thisState = this.state;
			// Iterate through provided context checks
			angular.forEach( contextObj, function( value, key ){
				// If any checks fail, return false
				if( $_.get( thisState, 'keybindings.' + key ) != value )
					has = false; 
			});
			$log.debug( '$pw.hasKeybindings() : ' + JSON.stringify(has), contextObj );
			return has;
		},

		revertKeybindings: function(){
			// Reverses keybindings to their previous state
			// TODO : retain history of keybindgs, when unset keybinding, reverse to previous
			
		},

		setQuery: function(query){
			this.query = query;
		},

		locationToQuery: function(){
			// Get Query String Parameters
			var query = $location.search();
			if ( !_.isEmpty( $_.get( query, 'tax_query' ) ) ) {    			
				query.tax_query = JSON.parse(query.tax_query); 
			}
			return query;
		},

		queryToLocation: function(query){
			// Change the location to reflect input query
			$log.debug('pw.queryToLocation',query);	

			// Loop on all query variables
			var queryString = "";
			for(var key in query){
				// Remove Null Values
				if ( query[key] === null ){  					
					continue;
				}
				if( key=="tax_query" && !_.isEmpty( query[key] ) ) {
					var taxInput = escape(angular.toJson(query[key]));
					queryString += key + "=" + taxInput + "&";
					continue;
				};

				// Do not allow 's' to be empty, breaks WordPress routing
				if( key === 's' && query[key] === '' )
					query[key] = ' ';

				// Remove empty values, except 0 and false
				if ( (query[key]!==0) && (query[key]!==false) ) {
					if( query[key] == "" || _.isUndefined(query[key]) ) {
						continue;
					}
				}  
				// Add to query string
				queryString += key + "=" + escape(query[key]) + "&"; 
			}

			// Clip the last character off the query string
			queryString = queryString.substring(0, queryString.length - 1);

			// Set the location
			$log.debug('pw.queryToLocation : path is ',$location.path());
			var path = $location.path();
			$location.path(path).search(queryString);

			//$log.debug('pw.queryToLocation : absolute path ',$location.absUrl(),queryString);	
		
		},

		/**
		* @ngdoc method
		* @name postworld.service#injectVariables
		* @methodOf postworld.$pw
		* @description Injects known variables into strings based on their identifiers.
		* @param {string} string The string to inject.
		* @returns {boolean} The resulting string after variable injections.
		*/
		injectVariables: function( string ){
			/**
			 * Known system variables
			 * with the variable as the key and
			 * the replacement string as the value.
			 *
			 * @todo Customize via filters / config
			 *
			 */
			var knownVars = {
				'themeUrl': this.paths.template_url
			};

			/*
			 * Iterate through each known variables
			 * and replace it's instance in the value.
			 */
			$log.debug( 'injectVariables : STRING', string );
			angular.forEach( knownVars, function( value, key ){
				var searchString = "@{"+ key + "}";
				string = string.replace( searchString, value );
			});
			return string;

		},
		
	};

});


/*                      _                                  
		_   _ _ __   __| | ___ _ __ ___  ___ ___  _ __ ___ 
	   | | | | '_ \ / _` |/ _ \ '__/ __|/ __/ _ \| '__/ _ \
	   | |_| | | | | (_| |  __/ |  \__ \ (_| (_) | | |  __/
  ____(_)__,_|_| |_|\__,_|\___|_|  |___/\___\___/|_|  \___|
 |_____|                                                   
 
 //////////////////////////////////////////////////////////*/

/**
 * @ngdoc service
 * @name postworld.$_
 * @todo Rename to $_
 */
postworld.factory('$_',
	function ( $rootScope, $log, $window, $timeout ) {   
	// DECLARATIONS

	function get( obj, key ){
		// Returns a sub-object
		// SYNTAX : key = 'object.subkey.subsubkey'

		if( _.isUndefined( obj ) )
			return false;
		
		///// MINE OBJECT /////
		var parts = key.split('.');
		for(var i = 0, l = parts.length; i < l; i++) {
			var part = parts[i];
			if(obj !== null && typeof obj === "object" && part in obj) {
				obj = obj[part];
			}
			else {
				return false;
			}
		}

		// Return findWhere
		return obj;
	}

	///// SET OBJECT VALUES /////
	function set( obj, key, value  ){
			/* 	Sets the value of an object,
			 * 	even if it or it's parent(s) doesn't exist.
			 *
			 *  PARAMETERS:
			 *	obj     =   [object]
			 *	key     =   [string] ie. ( "key.subkey.subsubkey" )
			 *	value   =   [string/array/object]
			*/

			///// KEY PARTS /////
			// FROM : "key.subkey.sub.subkey"
			// TO   : [ "key", "subkey", "subkey" ]
			var key_parts = key.split('.');
			key_parts = key_parts.reverse();
			// Count how many parts
			var key_parts_count = key_parts.length;
			// Prepare to catch finished key parts
			var key_parts_done = [];
			// Iterate through each key part
			var seed = [];
			var i = 0;
			angular.forEach( key_parts, function( key_part ){
				i++;
				// First Key
				if( i == 1 ){
					// Create seed with first key
					seed[i] = {};
					seed[i][key_part] = value;
				// Other Keys
				} else{
					// Nest previous seed in current key
					seed[i] = {};
					seed[i][key_part] = seed[(i-1)];
				}
				// Last Key
				if( i == key_parts_count ){
					// Return final seed result
					seed = seed[i];
				}
			});
			//$log.debug( "SEED : ", seed);
			//$log.debug( "OBJ : ", obj);
			// Merge $seed array with input $array
			obj = deepmerge( obj, seed );
			//$log.debug( "RESULT : ", obj);
			return obj;
		};

	return {
		exists: function(value){
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
		isNumeric: function(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		},
		isInArray: function(value, array) {
			// DEPRECIATED
			if (array)
				return array.indexOf(value) > -1 ? true : false;
			else
				return false;
		},
		inArray: function(value, array) {
			if( _.isArray( array ) )
				return array.indexOf(value) > -1 ? true : false;
			else
				return false;
		},
		isArray: function( data ){
			return (Object.prototype.toString.call(data) == '[object Array]');
		},
		inString: function(value, string) {
			if( _.isString( string ) )
				return string.indexOf(value) > -1 ? true : false;
			else
				return false;
		},
		stringInArray: function( value, array ){
			if( !_.isArray( array ) )
				return false;
			for( var i = 0; i < array.length; i++ ){
				if( this.inString( value, array[i] ) )
					return true;
			}
			return false;
		},
		isEmpty: function(value){
			if ( typeof value === 'undefined' ||
				value == '' ||
				value == null ||
				value == false ||
				value == [] ||
				value == {} )
				return true;
			else
				return false; 
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
		stringToBool : function(string){
			return this.stringToBoolean(string);
		},
		
		/**
		* @ngdoc method
		* @name postworld.service#stringToBoolean
		* @methodOf postworld.$_
		* @description Converts strings, such as `'true'` or `'false'`, to a boolean.
		* @param {string} string The string.
		* @returns {boolean} The boolean representation of the string.
		*/
		stringToBoolean : function(string){
			if( _.isBoolean( string ) )
				return string;
			if( !_.isString(string) )
				return false;
			switch(string.toLowerCase()){
				case "true": case "yes": case "1": return true;
				case "false": case "no": case "0": case null: return false;
				default: return Boolean(string);
			}
		},
		objExists : function(obj, prop){
			var parts = prop.split('.');
			for(var i = 0, l = parts.length; i < l; i++) {
				var part = parts[i];
				if(obj !== null && typeof obj === "object" && part in obj) {
					obj = obj[part];
				}
				else {
					return false;
				}
			}
			return true;
			
		},
		get : function( obj, key ){
			return get( obj, key  );
		},
		///// DEPRECIATED /////
		getObj: function( obj, key ){
			return get( obj, key );
		},
		set : function( obj, key, value  ){
			return set( obj, key, value  );
		},
		///// DEPRECIATED /////
		setObj : function( obj, key, value  ){
			return set( obj, key, value  );
		},
		objEquality : function( obj1, obj2 ){
			return ( JSON.stringify(obj1) === JSON.stringify(obj2) );
		},

		clobber: function( id, t, f, w ){
			// id = unique string
			// t = timeout in ms
			// f = function to run
			// w = boolean, whether to wait until it stops firing before clobbering
			//		- If true, the first action will fire instantly, and further requests will be delayed

			/*	Times out for the given time before running a function.
			 *	Any sequential functions that are clobbered with the same ID before the function runs
			 *	Will over-write the previous action and again timeout.
			 */

			// Set defaults
			// TODO : Impliment 'w' variable
			if( _.isUndefined( w ) )
				w = true;

			// Establish the Clobber Object
			if( _.isUndefined( $rootScope.clobber ) )
				$rootScope.clobber = {};
			if( _.isUndefined( $rootScope.clobber[ id ] ) )
				$rootScope.clobber[ id ] = 1;
			else
				// Increase Clobber Value
				$rootScope.clobber[ id ] ++;

			// Timeout
			$timeout( function(){
				// Decrease Clobber Value
				$rootScope.clobber[ id ] --;
				$log.debug( "clobber // ID: " + id + " // T: " + t + " // clobber : " + $rootScope.clobber[ id ] );

				// If no clobbering left, run the function
				if( $rootScope.clobber[ id ] == 0 )
					f();

			}, t );

		},
		urlParam: function( name ) {
			name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
			return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		},
		setScopeValues: function( $scope, values ){
			// values is an associative array, where the key is the expression and the value is the value
			var parts, firstKey, subKeys;
			angular.forEach( values, function( value, key ){
				parts = key.split('.');
				firstKey = parts[0];
				subKeys = key.slice( firstKey.length + 1, value.length );
				//$log.debug( "FIRST KEY :", firstKey );
				$scope[firstKey] = setObj( $scope, subKeys, value );
			});

		},
		sanitizeKey: function( input ){
			if( !_.isString(input) )
				return false;
			
			// Pass this function anything such as URL
			// And it will sanitize it for use as a key
			input = input.replaceAll( 'http://', '' );
			input = input.replaceAll( 'https://', '' );
			input = input.replaceAll( ':', '-' );
			input = input.replaceAll( '.', '-' );
			input = input.replaceAll( '/', '-' );
			input = input.replaceAll( '#', '-' );
			input = input.replaceAll( '?', '-' );
			input = input.replaceAll( '&', '-' );
			input = input.replaceAll( '=', '-' );
			return input;
		},
		makeHash: function( hashLength ){
			if( _.isEmpty(hashLength) )
				hashLength = 8;

			var hash = "";
			var alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

			for( var i=0; i < hashLength; i++ )
				hash += alpha.charAt(Math.floor(Math.random() * alpha.length));

			return hash;
		},
		randomString: function( randomLength, charTypes ){
			// Generates a random string based on length
			// and specified character types

			//if( _.isEmpty(randomLength) )
			//	randomLength = 8;

			if( _.isEmpty( charTypes ) )
				charTypes = ['numbers','uppercase','lowercase','special'];

			var randomString = "";

			var alpha = "";

			if( this.inArray( 'uppercase', charTypes ) )
				alpha += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

			if( this.inArray( 'lowercase', charTypes ) )
				alpha += "abcdefghijklmnopqrstuvwxyz";

			if( this.inArray( 'numbers', charTypes ) )
				alpha += "0123456789";

			if( this.inArray( 'special', charTypes ) )
				alpha += "!@#$%^&*()_-+";

			for( var i=0; i < randomLength; i++ )
				randomString += alpha.charAt(Math.floor(Math.random() * alpha.length));

			return randomString;
		},

		arrayFromObjectWatch: function( $scope, $array, $object ){
			/*
				•	This function is made to watch a specified $scope[ $object ]
					And where the key values are true, a string with that key
					Is added to $scope[ $array ]
				•	Made for use in translating an object of boolean values
					Into a flat array
				•	Useful when using a series of checkboxes with ng-model
					And turning the selected values into a flat array
			*/

			// Iterate through the flat array 
			// And generate an object
			$scope[ $object ] = {};
			if( !_.isEmpty( $scope[ $array ] ) ){
				angular.forEach( $scope[ $array ], function( value ){
					$scope[ $object ][ value ] = true;
				});
			}
			// Setup a watch on the object
			$scope.$watch( $object, function( val ){
				// Iterate through the object
				// And generate a flat array
				var flatArray = [];
				angular.forEach( $scope[ $object ], function( value, key ){	
					if( value == true )
						flatArray.push( key );
				});
				$scope[ $array ] = flatArray;
			}, 1 );
		},

		deepWhere: function( list, key, val ){
			// Deeply looks through each value in the list
			// Returning an array of all the values where the key equals the value
			var newList = [];
			angular.forEach( list, function( item ){
				var mineVal = get( item, key );
				//$log.debug( "item : ", item );
				if( mineVal == val )
					newList.push( item );
			});
			return newList;
		},

		// Compact arrays with empty entries; delete keys from objects with empty value
		removeEmpty: function( obj ){
			for ( var k in obj ){
				if ( _.isEmpty( obj[k] ) && !_.isNumber( obj[k] ) && !_.isBoolean( obj[k] ) )
					_.isArray( obj ) ?
						obj.splice(k,1) :
						delete obj[k];
				else if ( _.isObject( obj[k] ) )
					this.removeEmpty( obj[k] );
			}
		},

		xScrollable: function( element ){
			// Returns true if the contents of the container
			// is wider than the viewable area
			return ( element[0].scrollWidth > element.innerWidth() );
		},

		xScrolled: function( element ){
			// If the container has been scrolled
			return ( element.scrollLeft() > 0 );
		},

		yScrollable: function( element ){
			/** @todo : Impliment */
		},

		yScrolled: function( element ){
			/** @todo : Impliment */
		},

		windowScrollY: function(){
			return angular.element($window)[0].scrollY;
			//yScroll = window.scrollY || ((window.pageYOffset || document.body.scrollTop) - (document.body.clientTop || 0));
		},

		addXScrollClasses: function( element, vars ){
			// Adds classes to the element based on
			// If it's horizontally scrollable or scrolled

			var scrollableClass = vars.scrollable;
			var scrollable = this.xScrollable( element );
			if( scrollable )
				element.addClass(scrollableClass);
			else if( element.hasClass(scrollableClass) )
				element.removeClass(scrollableClass);

			var scrolledClass = vars.scrolled;
			var scrolled = this.xScrolled( element );
			if( scrolled )
				element.addClass(scrolledClass);
			else if( element.hasClass(scrolledClass) )
				element.removeClass(scrolledClass);

		},

		getSupportedProp: function(proparray) {
			var root = document.documentElement;
			for (var i=0; i<proparray.length; i++) { 
				if (proparray[i] in root.style) { return proparray[i]; }
			}
		},

		// Returns true if element is vertically within the viewport, even partially
		isInView: function(el) {
			/**
			 * Make more efficient using cached values 
			 */
			var rect = el.getBoundingClientRect();
			return (
				rect.bottom >= 0 &&
				rect.top <= (window.innerHeight || document.documentElement.clientHeight) 
			);
		},

		ancestorHasStyle: function(element, property, value){
			var node = element;
			while( node.length > 0) {
				// Reached the top level
				if( node[0].tagName === 'HTML' )
					return false;
				// Check for position fixed
				if( $window.getComputedStyle( node[0] )[property] == value ) {
					return true;
				}
				node = node.parent();
			}
			return false;
		},

	};

});


/* _                      ____           _       
  | |   _   _ ____      _|  _ \ ___  ___| |_ ___ 
 / __) (_) | '_ \ \ /\ / / |_) / _ \/ __| __/ __|
 \__ \  _  | |_) \ V  V /|  __/ (_) \__ \ |_\__ \
 (   / (_) | .__/ \_/\_/ |_|   \___/|___/\__|___/
  |_|      |_|                                   */

postworld.factory('$pwPosts',
	function ( $rootScope, $log, $window, $pwData, $_, $pw ) {  

	///// FACTORY DECLARATIONS /////
	

	var getFeedPost = function( feedId, postId ){
		///// Gets a post from a Feed by ID /////

		// Check if feed exists
		if( !$_.objExists( $pwData, 'feeds.'+feedId+'.posts' ) )
			return false;
		// Get the Post
		var post = _.findWhere( $pwData.feeds[feedId].posts, { ID: postId } );
		// Check if it's defined
		if( _.isUndefined( post ) )
			return false;
		return post;
	}

	var updateFeedPost = function( feedId, newPost ){
		///// Updates a post in a Feed by ID /////

		// Check if feed exists
		if( !$_.objExists( $pwData, 'feeds.'+feedId+'.posts' ) )
			return false;

		
		// Get the posts array
		var posts = $pwData.feeds[feedId].posts;

		// Rebuild Feed with the new Post
		var newPosts = [];
		angular.forEach( posts, function( post ){
			// If we're on the new post, update it
			if( post.ID == newPost.ID )
				// Deep merge the old post with the new one
				post = newPost;
			// Push posts to array
			newPosts.push( post );
		});

		// Set the new posts into the feed
		$pwData.feeds[feedId].posts = newPosts;
		
		return true;
		
	};

	var setFeedPostKeyValue = function( feedId, postId, key, value ){
		if( $_.get( $pwData, 'feeds.' + feedId ) == false )
			return false;
		// Iterate through feed posts
		for( var i; i < $pwData.feeds[feedId].posts.length; i++ ){
			// Check for post ID
			if( $pwData.feeds[feedId].posts[i].ID == postId ){
				// Get the post
				var post = $pwData.feeds[feedId].posts[i];
				// Set the value into the post
				post = $_.set( post, key, value );
				// Replace the post in the feed
				$pwData.feeds[feedId].posts[i] = post;
			}
		}
		return true;
	}

	var mergeFeedPost = function( feedId, mergePost ){
			// Get the original Post
			var post = getFeedPost( feedId, mergePost.ID );
			
			$log.debug( "mergeFeedPost : mergePost : ", mergePost );

			if( post == false )
				return false;

			// Combine Fields Value
			var oldFields = $_.get( post, 'fields' );
			if( oldFields !== false ){
				mergePost.fields = mergePost.fields.concat( oldFields );
			}

			// Deep merge the new data with the post
			post = pw_array_replace_recursive( post, mergePost );

			// Update the post
			return updateFeedPost( feedId, post );
	};

	var getMissingFields = function( post, requiredFields ){
		// Detect if the post has the required fields
		var missingFields = [];

		// If no fields field, return empty handed
		if( _.isUndefined( post.fields ) ){
			$log.debug( "pwPosts.missingFields() ›› post.fields not defined." );
			return false;
		}

		// Iterate through each required field
		angular.forEach( requiredFields, function( requiredField ){
			// If it's not in the fields
			if( !$_.inArray( requiredField, post.fields ) )
				// Add it to the missing fields array
				missingFields.push( requiredField );
		});

		$log.debug( "pwPosts.getMissingFields", missingFields );
		
		return missingFields;

	}

	///// FACTORY FUNCTIONS /////
	return {

		get: function( vars ){
			/* UNDER DEVELOPMENT */
			// Gets a post by first checking the post cache
			// If the post and the neccessary fields aren't contained in the post cache
			// Then the post and/or fields are aquired from the server
			/*
			 *	vars = {
			 *		post_id : [ number ],
			 *		fields : [ string / array ]
			 *	}
			 */

			 /*
			 // Default Vars
			 if( !_.isObject( vars ) )
				vars = {};

			 // If no post ID
			 if( _.isUndefined( vars.post_id ) )
				return false;

			 // If no fields
			 if( _.isUndefined( vars.fields ) )
				vars.fields = 'preview';

			 // If fields is a string
			 if( _.isString( vars.fields ) )
				vars.fields = $_.get( $pw.fields.post, vars.fields );

			 // If the requested field string doesn't exist
			 if( vars.fields == false )
				return false;

			 // Get the post
			 var post = $_.get( $pwData, 'posts.' + vars.post_id );

			 // If the post exists
			 if( post ){
				// Check if it has any missing fields
				var missingFields = getMissingFields( post, vars.fields );
				if( missingFields.length > 0 ){
				}
			 }

			 // If the post doesn't exist, return the promise of getting it $pwData.get_post
			*/
			 
		},
		requiredFields: function( vars ){
			// Checks a post to see if the specified required fields are present
			// And if not, it gets from the server automatically and plants them in the feed
			/*
			 *	vars = {
			 *		feedId: [string]	// Required
			 *		postId: [integer]	// Required
			 *		fields: [array]		// Required
			 *	}
			 */

			// Detect mode, if we're in a $pwData.feeds or $pwData.posts

			$log.debug( "REQUIRED FIELDS : ", vars );

			// Get the original Post
			var post = getFeedPost( vars.feedId, vars.postId );
			if( post == false )
				return false;

			// Detect if the post has a 'fields' field, which tells which fields are loaded
			if( _.isUndefined( post.fields ) )
				return false;

			var missingFields = getMissingFields( post, vars.fields );

			// If there are no missing fields
			if( missingFields.length == 0 )
				return true;

			// If there are missing fields, get them from the server
			var args = {
				post_id: vars.postId,
				fields: missingFields,
			};
			$pwData.getPost(args).then(
				// Success
				function(response) {
					// Catch the new post data
					var newPostData = response.data;
					// Add the previously missing fields to the 'fields' field
					newPostData.fields = missingFields;
					// Merge it into the feed post
					var merged = mergeFeedPost( vars.feedId, newPostData );
					$log.debug( "REQUIRED FIELDS : MERGE WITH FEED/POST : " + vars.feedId + " / " + vars.postId, newPostData );
					// Broadcast event for child listeners to pick up the new data
					$rootScope.$broadcast( 'feedPostUpdated', {
							feedId: vars.feedId,
							postId: vars.postId
						});
					
				},
				// Failure
				function(response) {}
			);
		},
		reloadFeedPost: function( feedId, postId ){
			/* Reloads the post data from the server and plants it in the feed
			 */

			// Get the specified post from the feed
			var post = getFeedPost( feedId, postId );
			if( post == false )
				return false;

			// Get the 'fields' value of the post
			var fields = $_.getObj( post, 'fields' );
			if( fields == false )
				fields = 'all';

			// Get the post from the server
			var args = {
				post_id: postId,
				fields: fields,
			};
			$pwData.get_post(args).then(
				// Success
				function(response) {
					// Catch the new post data
					var postData = response.data;
					// Merge it into the feed post
					var merged = mergeFeedPost( feedId, postData );
					$log.debug( "$pwPosts.reloadFeedPost( "+feedId+", "+postId+" ).$pwData.get_post() : MERGE WITH FEED/POST : ", postData );
					// Broadcast event for child listeners to pick up the new data
					$rootScope.$broadcast( 'feedPostUpdated', {
							feedId: feedId,
							postId: postId
						});
				},
				// Failure
				function(response) {
					$log.error( "$pwPosts.reloadFeedPost( "+feedId+", "+postId+" ).$pwData.get_post() : UNKOWN ERROR" );
				}
			);

		},
		getFeedPost: function( feedId, postId ){
			return getFeedPost( feedId, postId );
		},
		updateFeedPost: function( feedId, post ){
			return updateFeedPost( feedId, post );
		},
		setFeedPostKeyValue: function( feedId, postId, key, value ){
			return setFeedPostKeyValue( feedId, postId, key, value );
		},
		mergeFeedPost: function( feedId, mergePost ){
			return mergeFeedPost( feedId, mergePost );
		},
		// DEPRECIATED
		getFeed: function( feedId ){
			return $pwData.getFeed( feedId );
		},
		setFeedView: function( feedId, view ){
			$log.debug( 'pwPosts : setFeedView : ' + feedId + ' ', view );
			var vars = {
				'feedId' 	: feedId,
				'view'		: view,
			};
			$rootScope.$broadcast( "feed.changeTemplate", vars );
		},
		
	};

});


/*_____                    _       _         ____            _   _       _     
 |_   _|__ _ __ ___  _ __ | | __ _| |_ ___  |  _ \ __ _ _ __| |_(_) __ _| |___ 
   | |/ _ \ '_ ` _ \| '_ \| |/ _` | __/ _ \ | |_) / _` | '__| __| |/ _` | / __|
   | |  __/ | | | | | |_) | | (_| | ||  __/ |  __/ (_| | |  | |_| | (_| | \__ \
   |_|\___|_| |_| |_| .__/|_|\__,_|\__\___| |_|   \__,_|_|   \__|_|\__,_|_|___/ */

postworld.factory( '$pwTemplatePartials',
	function( $pw, $pwData, $log, $_, $timeout, $rootScope ){

	var evalCallbacks = function( vars ){

		///// CALLBACK /////
		// Evaluate given callback
		if( _.isString( vars.callback ) ){

			if( _.isUndefined( vars.callbackTimeout ) )
				vars.callbackTimeout = 0;
			else
				vars.callbackTimeout = parseInt( vars.callbackTimeout );

			$timeout( function(){
				$log.debug( 'pwTemplatePartials : callback : ' + vars.callback );
				
				try{
					eval( vars.callback );
				}
				catch(error){
					$log.debug(error);
				}

			}, vars.callbackTimeout );
		
		}

		///// CALLBACK EVENT /////
		// Broadcast event callback event from the rootScope
		if( _.isString( vars.callbackEvent ) ){
			$log.debug( 'pwTemplatePartials : callbackEvent : ' + vars.callbackEvent );
			$rootScope.$broadcast( vars.callbackEvent, vars );
		}

	}


	return{
		data : function(){
			return $pwData.partials;
		},
		getId : function( id ){
			return $_.get( $pwData.partials, id );
		},
		get : function( vars ){
			/*
				vars = {
					partial: [string] 	// required, the object path to the registered partial function
					vars: [mixed] 		// optional, variables to pass to the partial function
					id: [string] 		// optional, additional identifier
				}
			*/
			var cachePath = vars.partial;

			// If an ID is defined
			if( !_.isUndefined( vars['id'] ) )
				// append the ID to the cache path
				cachePath = cachePath + ".id" + vars['id'];
			
			//$log.debug( "GET TEMPLATE PARTIAL : " + cachePath, vars );

			if( !$_.objExists( $pwData.partials, cachePath ) ){
				$pwData.partials = $_.setObj( $pwData.partials, cachePath, '' ); // TODO : Add loading partial option

				$pwData.getTemplatePartial( vars ).then(
					function( response ){

						// Debug Log
						$log.debug( "PW TEMPLATE PARTIAL : RESPONSE :", response );

						// Get the response data
						var partialHtml = response.data;

						// Cache the data in the $pwData partials cache path
						$pwData.partials = $_.setObj( $pwData.partials, cachePath, partialHtml );

						// Evaluate Callbacks
						vars.firstRun = true;
						evalCallbacks( vars );

					},
					function( response ){
					}
				);

			}

			var partialData = $_.getObj( $pwData.partials, cachePath );

			// Evaluate Callbacks
			//vars.firstRun = false;
			//evalCallbacks( vars );

			return partialData;

		},
	}
});


/* _                      ___                                 
  | |   _   _ ____      _|_ _|_ __ ___   __ _  __ _  ___  ___ 
 / __) (_) | '_ \ \ /\ / /| || '_ ` _ \ / _` |/ _` |/ _ \/ __|
 \__ \  _  | |_) \ V  V / | || | | | | | (_| | (_| |  __/\__ \
 (   / (_) | .__/ \_/\_/ |___|_| |_| |_|\__,_|\__, |\___||___/
  |_|      |_|                                |___/           */

postworld.factory('$pwImages',
	function ( $log, $window, $_, $pw ) {  

	///// UNIVERSALS /////
	// TODO : Get Tag mapping from pwConfig
	var tagMappings = [
		{
			name: 'square',
			width: 1,
			height: 1,
		},
		{
			name: 'wide',
			width: 1,
			height: 1,
		},
		{
			name: 'x-wide',
			width: 2,
			height: 1,
		},
		{
			name: 'tall',
			width: 1,
			height: 1.5,
		},
		{
			name: 'x-tall',
			width: 1,
			height: 2,
		},
	];

	/**
	 * Override the default tag mappings
	 * With those provided in Postworld Config
	 */
	var overrideTagMappings = $_.get( $pw, 'config.images.tag_mapping' );
	if( !_.isEmpty( overrideTagMappings ) )
		tagMappings = overrideTagMappings;

	///// FACTORY VALUES /////
	return {
		selectImageTag: function( tags, mappings ){
			// Set Default Mapping
			if( _.isUndefined( mappings ) )
				mappings = tagMappings;

			var selectedTag = {};
			// Iterate through each image tag in the selected image
			angular.forEach( tags, function( imageTag ){
				// Iterate through each mapping option
				angular.forEach( mappings, function( tagMapping ){
					// Select the last match
					if( tagMapping['name'] == imageTag )
						selectedTag = tagMapping;
				});
			});
			
			//$log.debug( "selectImageTag : " + tags + " // selectedTag : " + JSON.stringify(selectedTag) + " // mappings : ", mappings );

			// If none selected
			if( selectedTag == {} )
				return false;
			// Return the selected tag
			return selectedTag;
		},

		/**
		 * Selects the correctly sized image from a series of variables.
		 * In general, the smallest image which equals or exceeds
		 * The input variables will be selected.
		 *
		 * @param {object} image A Postworld image object
		 */
		selectImageSize: function( imageSizes, vars ){

			if( _.isEmpty( imageSizes ) || imageSizes === null )
				return false;

			if( _.isEmpty( vars ) || vars === null )
				return false;

			/**
			 * Set the default variables
			 */
			var defaultVars = {
				width:0,
				height:0,
				priority:false,
			};
			vars = array_replace_recursive( defaultVars, vars );

			/**
			 * Make a new array, without the keys
			 * So that it can be properly sorted as an Array
			 */
			var imageArray = [];
			angular.forEach( imageSizes, function( img, key ){
				// Calculate the image area
				img['area'] = parseInt( img['width'] ) * parseInt( img['height'] );
				// Add the object key
				img['key'] = key;
				imageArray.push( img );
			});

			// Sort the image objects by area
			imageArray = _.sortBy( imageArray, 'area' ).reverse();

			// Precalculate if width and height are present
			var hasWidth = ( vars['width'] !== 0 );
			var hasHeight = ( vars['height'] !== 0 );

			/** 
			 * Get the image with the smallest area
			 * that is equal to or above the
			 * Specified width and height.
			 *
			 * @note This is where the magic happens.
			 */
			var selectImg = {};
			angular.forEach( imageArray, function( img ){

				// Check Width
				var passWidth = false;
				if( vars.priority == 'height' )
					passWidth = true;
				else if( hasWidth ){
					if( img['width'] >= vars['width'] ){
						passWidth = true;
					}
				} else
					passWidth = true;
				
				// Check Height
				var passHeight = false;
				if( vars.priority == 'width' )
					passHeight = true;
				else if( hasHeight ){
					if( img['height'] >= vars['height'] ){
						passHeight = true;
					}
				} else
					passHeight = true;
				
				// If it's a valid width and height, select it
				if( passWidth && passHeight )
					selectImg = img;
				
			});

			/**
			 * If no image was selected it is
			 * Because none of the images were high res enough,
			 * So select the image with the largest area.
			 */
			if( _.isEmpty( selectImg ) )
				selectImg = imageArray[0];

			return selectImg;

		},

	};

});


/*
   _        ____           _      ___        _   _                 
  | |   _  |  _ \ ___  ___| |_   / _ \ _ __ | |_(_) ___  _ __  ___ 
 / __) (_) | |_) / _ \/ __| __| | | | | '_ \| __| |/ _ \| '_ \/ __|
 \__ \  _  |  __/ (_) \__ \ |_  | |_| | |_) | |_| | (_) | | | \__ \
 (   / (_) |_|   \___/|___/\__|  \___/| .__/ \__|_|\___/|_| |_|___/
  |_|                                 |_|                          

////////// ------------ EDIT POST OPTIONS SERVICE ------------ //////////*/  
postworld.service('$pwPostOptions',
	function( $window, $log, $pwData, $_, $pw ) {

	return{
		taxTerms: function( $scope, taxObj ){
			if( typeof taxObj === 'undefined' )
				taxObj = "tax_terms";

			var args = $pw.config.post_options.taxonomy_outline;
			$pwData.taxOutlineMixed( args ).then(
				// Success
				function(response) {
					$log.debug('pwPostOptions.taxTerms : RESPONSE : ', response); 
					$scope[taxObj] = response.data;
				},
				// Failure
				function(response) {
					//alert('Error loading terms.');
				}
			);
		},

		postType: function( mode ){
			// MODE OPTIONS
			// read / edit / edit_others / publish / create / edit_published / edit_private
			// IF READ MODE : Return all public post types
			
			if( _.isUndefined(mode) )
				mode = 'read';

			var postTypes = $pw.config.post_types;

			if( mode == 'read' ){
				return postTypes;
			}

			// IF EDIT/OTHER MODE : Compare post types against their capabilities
			// Cycle through provided postTypes
			// Which postTypes does the user have access to 'mode' operation?
			var userPostTypeOptions = {};
			if( $window.pw.user != 0 ){
				angular.forEach( postTypes , function( name, slug ){
					var cap_type = mode + "_"+ slug + "s";
					if( $_.get( $window, 'pw.user.allcaps.'+cap_type ) !== false ){
						userPostTypeOptions[slug] = name;
					}
				});
			}
			
			return userPostTypeOptions;
		},

		postStatus: function( postType ){

			if( _.isUndefined( postType ) )
				return [
					{
						slug: 'any',
						name: 'Any',
					},
					{
						slug: 'publish',
						name: 'Published',
					},
					{
						slug: 'draft',
						name: 'Draft',
					},
					{
						slug: 'pending',
						name: 'Pending',
					},
					{
						slug: 'future',
						name: 'Future',
					},
					{
						slug: 'private',
						name: 'Private',
					},
					{
						slug: 'trash',
						name: 'Trash',
					}
				];

			if ((!$window.pw.user) || (!$window.pw.user))
				return;

			// GET ROLE
			var currentUserRole = $window.pw.user.roles[0];
			// DEFINE : POST STATUS OPTIONS
			var postStatusOptions = $pw.config.post_options.post_status;
			// DEFINE : POST STATUS OPTIONS PER ROLE BY POST TYPE
			var rolePostTypeStatusAccess = $pw.config.post_options.role_post_type_status_access;
			// BUILD OPTIONS MENU OBJECT
			var postStatusSelect = {};
			var userRoleOptions = rolePostTypeStatusAccess[currentUserRole][postType];
			if( typeof userRoleOptions !== 'undefined' ){
				angular.forEach(  userRoleOptions, function( postStatusSlug ){
					postStatusSelect[postStatusSlug] = postStatusOptions[postStatusSlug];
				});
				return postStatusSelect;
			} else{
				// DEFAULT
				return postStatusOptions;
			}
		},
		
		postClass: function( postType ){
			// Get the first item from an array
			if( _.isArray( postType ) )
				postType = postType[0];
			// Get the options from site globals
			var postClassOptions = $_.getObj( $pw.config, 'post_options.post_class' );
			// If none found, return false
			if( !postClassOptions )
				return {
					"members": 	"Members Only",
					"public": 	"Public",
				};
			// If no post type, return all
			if( _.isEmpty( postType ) )
				return postClassOptions;
			// Get the post type subobject
			var postClassSet = $_.getObj( postClassOptions, postType );
			// If no subobject of specified post type
			if( !postClassSet )
				// Try default 'post' settings
				postClassSet = $_.getObj( postClassOptions, 'post' );
			return postClassSet;
		},

		postView: function(){
			return $_.getObj( $pw.config, 'post_views' );
		},

		linkFormat: function(){
			return $_.getObj( $pw.config, 'post_options.link_format' );
		},

		linkFormatMeta: function(){
			return $_.getObj( $pw.config, 'post_options.link_format_meta' );
		},

		postYear: function(){
			return $_.getObj( $pw.config, 'post_options.year' );
		},

		postMonth: function(){
			/*
			var monthsObj = $pw.config.post_options.month;
			var monthsArray = [];
			// Convert from "1:January" Associative Object format to { number:1, name:"January" } 
			angular.forEach( monthsObj, function( value, key ){
				var month = {
					'number' : parseInt(key),
					'name' : value
				};
				monthsArray.push( month );
			});
			*/
			var monthsArray = [
				{
					number:1,
					name:'January'
				},
				{
					number:2,
					name:'February'
				},
				{
					number:3,
					name:'March'
				},
				{
					number:4,
					name:'April'
				},
				{
					number:5,
					name:'May'
				},
				{
					number:6,
					name:'June'
				},
				{
					number:7,
					name:'July'
				},
				{
					number:8,
					name:'August'
				},
				{
					number:9,
					name:'September'
				},
				{
					number:10,
					name:'October'
				},
				{
					number:11,
					name:'November'
				},
				{
					number:12,
					name:'December'
				},
			];
			return monthsArray;
		},

		order: function(){
			return [
				{
					slug: 'DESC',
					name: 'Descending',
				},
				{
					slug: 'ASC',
					name: 'Ascending',
				},
			];
		},

		orderBy: function(){
			return [
				{
					slug: 'date',
					name: 'Date',
				},
				{
					slug: 'none',
					name: 'None',
				},
				{
					slug: 'menu_order',
					name: 'Menu Order',
				},
				{
					slug: 'ID',
					name: 'Post ID',
				},
				{
					slug: 'type',
					name: 'Post Type',
				},
				{
					slug: 'rank_score',
					name: 'Rank Score',
				},
				{
					slug: 'post_points',
					name: 'Post Points',
				},
				{
					slug: 'modified',
					name: 'Date Modified',
				},
				{
					slug: 'rand',
					name: 'Random',
				},
				{
					slug: 'comment_count',
					name: 'Comment Count',
				},
				{
					slug: 'parent',
					name: 'Post Parent ID',
				},
				{
					slug: 'author',
					name: 'Author',
				},
				{
					slug: 'event_start',
					name: 'Event Start',
				},
				{
					slug: 'event_end',
					name: 'Event End',
				}
			];
		},

		eventFilter: function(){
			return [
				{
					value: 'future',
					name: 'Upcoming Events',
				},
				{
					value: 'now',
					name: 'Current Events',
				},
				{
					value: 'past',
					name: 'Past Events',
				},
			];
		},

		taxInputModel: function(){
			// TAXONOMY OBJECT MODEL
			// Makes empty array in the taxInput object for each taxonomy inputs
			var taxonomies = $_.getObj( $pw.config, 'post_options.taxonomies' );
			if( !taxonomies )
				return false;

			var taxInput = {};
			angular.forEach( taxonomies, function( value ){
				taxInput[value] = [];
			});
			return taxInput;
		},

	}
});


/* _        ____       _         _                         
  | |   _  |  _ \ ___ | | ___   / \   ___ ___ ___  ___ ___ 
 / __) (_) | |_) / _ \| |/ _ \ / _ \ / __/ __/ _ \/ __/ __|
 \__ \  _  |  _ < (_) | |  __// ___ \ (_| (_|  __/\__ \__ \
 (   / (_) |_| \_\___/|_|\___/_/   \_\___\___\___||___/___/
  |_|                                                      

/*///////// ------- SERVICE : PW USERS ------- /////////*/  
postworld.service('$pwRoleAccess',
	function ($log, $window, $_, $pw) {
	return{
		setRoleAccess : function($scope){
			$scope.current_user = $window.pw.user;

			( $scope.current_user != 0 ) ?
				$scope.current_user_role = $window.pw.user.roles[0] :
				$scope.current_user_role = 'guest' ;

			$scope.roles = {};
			$scope.role_map = $pw.config.role.map;

			// ESTABLISH ROLE ACCESS
			// Is the user an editor?
			( $_.isInArray( $scope.current_user_role, $scope.role_map.editor ) ) ?
				$scope.roles.editor = true : $scope.roles.editor = false;

			// Is the user an author?
			( $_.isInArray( $scope.current_user_role, $scope.role_map.author ) ) ?
				$scope.roles.author = true : $scope.roles.author = false;

			// Is the user an contributor?
			( $_.isInArray( $scope.current_user_role, $scope.role_map.contributor ) ) ?
				$scope.roles.contributor = true : $scope.roles.contributor = false ;

		},
	}
});



/* ___        _      _      _____    _ _ _   
  / _ \ _   _(_) ___| | __ | ____|__| (_) |_ 
 | | | | | | | |/ __| |/ / |  _| / _` | | __|
 | |_| | |_| | | (__|   <  | |__| (_| | | |_ 
  \__\_\\__,_|_|\___|_|\_\ |_____\__,_|_|\__|
											 
////////// ------------ QUICK EDIT ------------ //////////*/  

/*///////// ------- SERVICE : PW QUICK EDIT ------- /////////*/  
postworld.service('$pwQuickEdit',
	function ( $rootScope, $log, $location, $uibModal, $pwData, $pw, $_ ) {
	return{
		openQuickEdit : function( meta ){
			
			// Default Defaults
			if( _.isUndefined( meta.mode ) )
				meta.mode = 'quick-edit';

			$log.debug( "Launch Quick Edit : META : " + meta, meta.post );

			var modalInstance = $uibModal.open({
			  templateUrl: $pwData.pw_get_template( { subdir: 'modals', view: 'modal-edit-post' } ),
			  controller: 'quickEditInstanceCtrl',
			  windowClass: 'quick_edit',
			  resolve: {
				meta: function(){
					return meta;
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

		trashPost : function ( post_id, $scope ){
			if ( window.confirm("Are you sure you want to trash : \n" + $scope.post.post_title) ) {
				$pwData.pw_trash_post( post_id ).then(
					// Success
					function(response) {
						if (response.status==200) {
							$log.debug('Post Trashed RETURN : ',response.data);                  
							if ( _.isNumber(response.data) ){
								var trashedPostId = response.data;
								if( typeof $scope != undefined ){
									// SUCESSFULLY TRASHED
									//var retreive_url = "/wp-admin/edit.php?post_status=trash&post_type="+scope.post.post_type;
									$scope.post.post_status = 'trash';
									// Emit Trash Event : post_id
									$scope.$emit('trashPost', trashedPostId );
									// Broadcast Trash Event : post_id
									$scope.$broadcast('trashPost', trashedPostId );
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
});





/*
   _        _____    _ _ _     ____           _     _____ _ _ _                
  | |   _  | ____|__| (_) |_  |  _ \ ___  ___| |_  |  ___(_) | |_ ___ _ __ ___ 
 / __) (_) |  _| / _` | | __| | |_) / _ \/ __| __| | |_  | | | __/ _ \ '__/ __|
 \__ \  _  | |__| (_| | | |_  |  __/ (_) \__ \ |_  |  _| | | | ||  __/ |  \__ \
 (   / (_) |_____\__,_|_|\__| |_|   \___/|___/\__| |_|   |_|_|\__\___|_|  |___/
  |_|                                                                          
////////// ------------ EDIT POST FILTERS SERVICE ------------ //////////*/  
postworld.service('$pwEditPostFilters',
	function ($log, $_, $window, $pwPostOptions, $pw ) {

	return {
		parseKnownJsonFields: function( post ){

			if ( !_.isUndefined( post['post_meta'] ) ){

				// Deserialize Known JSON Fields
				var knownJsonFields = [
					'geocode',
					'location_obj',
					'related_post'
					];
				angular.forEach( post.post_meta , function(value, key){
					if( $_.isInArray( key, knownJsonFields ) )
						post.post_meta[key] = angular.fromJson(value);
				});

			}
			return post;

		},
		sortTaxTermsInput: function(post, tax_terms, sub_object){
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
			
			// Return the post if missing input
			if( _.isUndefined(post) ||
				_.isUndefined(tax_terms) )
				return post;

			angular.forEach( post[sub_object], function( selected_terms, taxonomy ){
				if ( selected_terms.length > 1 ){
					// FOR EACH TAXONOMY TERM OPTION
					// Go through each top level term for taxonomy in tax_terms
					// If it equals the first value of terms, leave it as is
					// If it isn't found, then swap order
					var reorder = true;
					angular.forEach( tax_terms[taxonomy].terms, function( term_option ){
						// Compare each term option to the selected terms
						// If they're the same, do not reorder
						if ( term_option.slug == selected_terms[0] ){
							// If the term is the first term
							reorder = false;
						}
					});

					if ( reorder == true ){
						post[sub_object][taxonomy].reverse();
					}
					
				}
			});
			return post;
		},

		///// EVALUATE AND SET link_format DEPENDING ON LINK_URL /////
		evalPostFormat: function( link_url, link_format_meta ){

			// If Custom Link Format Meta isn't supplied, get the default
			if ( _.isUndefined( link_format_meta ) )
				link_format_meta = $pwPostOptions.linkFormatMeta();

			// Set the default format
			var default_format = $pw.config.post_options.link_format_defaults.none;
			var set = "";

			// If link_url has a value
			if ( !$_.isEmpty( link_url ) && !$_.isEmpty( link_format_meta ) ){
				///// FOR EACH POST FORMAT : Go through each post format
				angular.forEach( link_format_meta, function( link_format ){
					///// FOR EACH DOMAIN : Go through each domain
					angular.forEach( link_format.domains, function( domain ){
					// If domain exists in the link_url, set that format
						if ( $_.isInArray( domain, link_url ) ){
							set = link_format.slug;
						}
					});
				});
				// If no matches, set default
				if ( set == "" )
					return $pw.config.post_options.link_format_defaults.link;
				else
					return set;
			}
			// Otherwise, set default
			else {
				return default_format;
			}
		},
		selected_tax_terms: function( tax_terms , tax_input){
			///// SELECTED TAXONOMY TERMS /////
			// • Extracts an object with singular sub-term data
			//   so that they can be referred to to define subtopics

			// Return false if missing input
			if( _.isUndefined(tax_terms) ||
				_.isUndefined(tax_input) )
				return false;

			// EACH TAXONOMY : Cycle through each taxonomy
			var selected_tax_terms = {};
			angular.forEach( tax_terms, function( tax_obj, taxonomy ){
				// Setup Object
				if ( $_.isEmpty( tax_input[taxonomy] ) )
					tax_input[taxonomy] = [];
				// SET TERM : Cycle through each term
				// Set the selected taxonomy terms object
				angular.forEach( tax_obj.terms, function( term ){
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
				var child_term_is_set = !$_.isEmpty( tax_input[taxonomy][1] );
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
});




/*              ____        _         ____                  _               
  _ ____      _|  _ \  __ _| |_ ___  / ___|  ___ _ ____   _(_) ___ ___  ___ 
 | '_ \ \ /\ / / | | |/ _` | __/ _ \ \___ \ / _ \ '__\ \ / / |/ __/ _ \/ __|
 | |_) \ V  V /| |_| | (_| | ||  __/  ___) |  __/ |   \ V /| | (_|  __/\__ \
 | .__/ \_/\_/ |____/ \__,_|\__\___| |____/ \___|_|    \_/ |_|\___\___||___/
 |_|                                                                        
//////////// ------------ POSTWORLD DATE SERVICES ------------ ////////////*/  


postworld.service('$pwDate', function ($log, $_, $window) {
	return {

		setDateRange: function( set, dateObj, offset ){
			// set = 'min' / 'max'
			// dateObj = a JS Date Object
			// offset = how much to offset in milliseconds from given time

			// USAGE
			// setDateRange();                              // Resets both to null
			// setDateRange( 'min' );                       // Sets min to current time
			// setDateRange( 'max' );                       // Sets max to null
			// setDateRange( 'min', dateObj );              // Offsets the dateObj to the minDate
			// setDateRange( 'max', dateObj, '100000' );    // Offsets the dateObj by 100000 milliseconds and sets the maxDate
			// setDateRange( 'max', 'now', 'oneDay' );    // Offsets the dateObj by 100000 milliseconds and sets the maxDate

			///// DEFAULT SETTINGS ////
			if( _.isUndefined(set) ){
				// Defaults
				//$scope.minDate = null;
				//$scope.maxDate = null;
				return false;
			}

			///// DATE OBJ OPTIONS /////
			if( dateObj == 'now' )
				dateObj = new Date();

			///// OFFSET OPTIONS /////
			if( offset == 'oneDay' )
				offset = parseInt(86400000);
			
			///// MINIMUM SETTINGS /////
			if( set == 'min'  ){
				// If dateObj is undefined, set as current time
				if( _.isUndefined( dateObj ) ){
					//$scope.minDate = new Date();
					var minDate = new Date();
					return minDate;
				}

				// If Date Object is defined but not offset
				// Set the setting to the given time
				if( _.isUndefined( offset ) ){
					//$scope.minDate = new Date(dateObj);
					var minDate = new Date(dateObj);
					return minDate;
				}

				// If a offset number is given
				// Subtract that many milliseconds
				if( _.isNumber(offset) ){
					var localDateObj = new Date(dateObj);
					var parsedDateObj = Date.parse(localDateObj);
					var newTime = parsedDateObj - offset;
					var setTime = new Date(newTime);
					//$scope.minDate = setTime;
					return setTime;
				}
				return false;
				
			}


			///// MAXIMUM SETTINGS /////
			if( set == 'max'  ){
				// If dateObj is undefined, set as null
				if( _.isUndefined( dateObj ) ){
					//$scope.maxDate = null;
					return false;
				}

				// If Date Object is defined but not offset
				// Set the setting to the given time
				if( _.isUndefined( offset ) ){
					//$scope.maxDate = new Date(dateObj);
					var maxDate = new Date(dateObj);
					return maxDate;
				}

				// If a offset number is given
				// Add that many milliseconds
				offset = parseInt(offset);
				if( _.isNumber(offset) ){
					var localDateObj = new Date(dateObj);
					var parsedDateObj = Date.parse(localDateObj);
					var newTime = parsedDateObj + offset;
					var setTime = new Date(newTime);
					//$scope.maxDate = setTime;
					return setTime;
				}
				return false;
				
			}

		},


		getTimezone: function( timezoneId ){
			/**
			 * If there's no timezoneId defined, use client time
			 */
			if( timezoneId === null ||
				_.isEmpty( timezoneId ) ||
				_.isUndefined( timezoneId ) ){
				/**
				 * Use jstz to get the name of the current timezone
				 * In the future this will soon be supported by Moment.js
				 */
				timezoneId = jstz.determine().name();
			}

			/**
			 * Use Moment.js to get the timezone object
			 */
			var momentTimezone = ( typeof(moment) == "function" ) ?
				moment().tz( timezoneId ) : false;

			// Compile some values for a simple object
			var timezone = {
				offset: momentTimezone._offset,
				name: timezoneId,
				code: momentTimezone.format('z'),
			};

			//$log.debug( 'pwDate.getTimezone : momentTimezone : ' + timezoneId, momentTimezone );
			$log.debug( 'pwDate.getTimezone : ' + timezoneId, timezone );

			return timezone;

		},



	};
});



function pw_is_array( data ){
	return (Object.prototype.toString.call(data) == '[object Array]');
}

function pw_array_replace_recursive(arr) {
	var retObj = {},
		i = 0,
		p = '',
		argl = arguments.length;

	if (argl < 2) {
		throw new Error('There should be at least 2 arguments passed to pw_array_replace_recursive()');
	}

	// Although docs state that the arguments are passed in by reference, it seems they are not altered, but rather the copy that is returned (just guessing), so we make a copy here, instead of acting on arr itself
	for (p in arr) {
		retObj[p] = arr[p];
	}

	for (i = 1; i < argl; i++) {
		for (p in arguments[i]) {
			if ( retObj[p] && typeof retObj[p] === 'object' && !pw_is_array( retObj[p] ) ) {
				retObj[p] = pw_array_replace_recursive(retObj[p], arguments[i][p]);
			} else {
				retObj[p] = arguments[i][p];
			}
		}
	}
	return retObj;
}

