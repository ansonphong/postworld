'use strict';

/*_   _       _                 _      _             _             
 | | | |_ __ | | ___   __ _  __| |    / \__   ____ _| |_ __ _ _ __ 
 | | | | '_ \| |/ _ \ / _` |/ _` |   / _ \ \ / / _` | __/ _` | '__|
 | |_| | |_) | | (_) | (_| | (_| |  / ___ \ V / (_| | || (_| | |   
  \___/| .__/|_|\___/ \__,_|\__,_| /_/   \_\_/ \__,_|\__\__,_|_|   
	   |_|                                                         
/*/////////// ------------ UPLOAD AVATAR ------------ ///////////*/  

postworld.directive('pwEditAvatar', function() {
	return {
		restrict: 'A',
		controller: 'editAvatarCtrl',
		scope:{
			'avatarModel':'=',
			//'avatarStatus':'=',
		},
	};
});

postworld.controller( 'editAvatarCtrl',
	[ '$scope', '$rootScope', 'pwData', '$timeout', '$log', 'pwUsers', '_',
	function( $scope, $rootScope, $pwData, $timeout, $log, $pwUsers, $_ ) {

	$scope.status = "empty";

	$scope.setAvatarStatus = function( status ){
		//if( $scope.avatarStatus )
			//$scope.avatarStatus = status;
	}

	///// NEW MODEL /////

	$scope.updateAvatar = function( vars ){
		
		$log.debug( 'EDIT AVATAR', vars );
		return false;

		$scope.setAvatarStatus('busy');

		var defaultVars = {
			user_id: $_.get( $scope, 'user.ID' ),
			attachment_id: null,
			image_url: null,
		};
	
		vars = array_replace_recursive( defaultVars, vars );

		$pwData.setAvatar( vars ).then(
				function(response) {    
					$scope.setAvatarStatus('done');

					///// BROADCAST : UPDATE AVATAR /////
					$rootScope.$broadcast( 'updatedAvatar', {
						userId: vars.user_id,
					});

				},
				function(response){}
			);
		$scope.status = "setting";
	};


	$scope.$on( 'updateAvatar', function( event, args ){
		$log.debug( 'editAvatarCtrl.updateAvatar : RECEIVED : ', args );

		// If the updated avatar user ID is the same as the current parent scope user ID
		if( $_.get( args, 'userId' ) == $_.get( $scope.$parent, 'user.ID' ) )
			// Update the avatar
			$scope.getAvatars();

	});


	$scope.getAvatars = function( vars ){
		$scope.setAvatarStatus('busy');

		if( _.isEmpty(vars) )
			vars = {};

		var defaultVars = {
			model:'user.avatar',
			user_id: $_.get( $scope.$parent, 'user.ID' ),
			fields:[
				'avatar(small,64)',
				'avatar(medium,256)'
			],
		};

		$log.debug( 'editAvatarCtrl.getAvatars : REQUEST : ', vars );

		vars = array_replace_recursive( defaultVars, vars );

		$pwData.getAvatars( vars ).then(
			function( response ){
				$log.debug( 'editAvatarCtrl.getAvatars : RESPONSE : ', response );
				if( response.status == 200 && $scope.avatarModel )
					$scope.avatarModel = response.data;

				$scope.setAvatarStatus('done');
			},
			function( response ){}
		);

	};





	///// OLD MODEL /////

	$scope.loadAvatarObj = function( user_id ){
		$scope.status = "loading";
		
		// Hit pwData.setAvatar with args
		var args = {
			user_id: user_id
		};
		$pwData.pw_get_avatar( args ).then(
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

		$pwData.setAvatar( args ).then(
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

	

}]);



/*_   _                     _                _       
 | | | |___  ___ _ __   _  | |    ___   __ _(_)_ __  
 | | | / __|/ _ \ '__| (_) | |   / _ \ / _` | | '_ \ 
 | |_| \__ \  __/ |     _  | |__| (_) | (_| | | | | |
  \___/|___/\___|_|    (_) |_____\___/ \__, |_|_| |_|
                                       |___/         
/*/////////// --------- LOGIN --------- ///////////*/  

postworld.directive('pwUserLogin', function() {
	return {
		restrict: 'A',
		controller: 'pwUserLoginCtrl',
	};
});

postworld.controller('pwUserLoginCtrl',
	[ '$scope', '$pw', '$rootScope', 'pwData', '$timeout', '$log', 'pwUsers', '_',
	function( $scope, $pw, $rootScope, $pwData, $timeout, $log, pwUsers, $_ ) {
	
	$scope.view = $pw.view;


}]);


/*
  _   _                     ____  _                         
 | | | |___  ___ _ __   _  / ___|(_) __ _ _ __  _   _ _ __  
 | | | / __|/ _ \ '__| (_) \___ \| |/ _` | '_ \| | | | '_ \ 
 | |_| \__ \  __/ |     _   ___) | | (_| | | | | |_| | |_) |
  \___/|___/\___|_|    (_) |____/|_|\__, |_| |_|\__,_| .__/ 
									|___/            |_|    
/*/////////// ------------ SIGNUP ------------ ///////////*/  

postworld.directive('pwUserSignup', function() {
	return {
		restrict: 'A',
		controller: 'pwUserSignupCtrl',
	};
});

postworld.controller('pwUserSignupCtrl',
	[ '$scope', '$pw', '$rootScope', 'pwData', '$timeout', '$log', 'pwUsers', '_',
	function( $scope, $pw, $rootScope, $pwData, $timeout, $log, pwUsers, $_ ) {
	
	// Localize Site data
	$scope.site = $pw.site;

	///// VIEWS : Outline /////
	$scope.views = [ 'signup', 'activate', ];

	// SETUP
	$scope.formData = {
		name:"",
		username:"",
		password:"",
		password_c:"",
		email:"",
		agreement:""
	};

	$scope.fieldStatus = {
		username:'empty',
		password:'empty',
		email:'empty',
	};

	$scope.mode = "signup";
	$scope.formName = "signupForm";
	$scope.status = "done";

	// Set the Context
	if( _.isUndefined( $scope.meta ) )
		$scope.meta = {};
	if( !$_.objExists( $scope, 'meta.context' ) )
		$scope.meta.context = ( !_.isEmpty( $_.urlParam( 'context' ) ) ) ?
			$_.urlParam( 'context' ) : '';

	// SHOW VIEW : Switch the view based on $scope.mode
	$scope.showView = function( view ){
		switch( view ){
			case 'signup':
				if(
					$scope.mode == 'signup' || 
					$scope.mode == 'signupCustom' )
					return true;
				break;
			case 'activate':
				if( $scope.mode == 'activate' )
					return true;
				break;
		}
		return false;
	}

	// VALIDATE : Username
	$scope.validateUsername = function( username ){

		if(
			$scope[ $scope.formName ].username.$error.minLength ||
			$scope[ $scope.formName ].username.$error.maxLength ||
			$scope[ $scope.formName ].username.$error.pattern ||
			!$scope[ $scope.formName ].username.$dirty
			){
			$scope.fieldStatus.username = "done";
			return false;
		}

		// Handle Empty Username
		if( username == '' || _.isUndefined( username ) )
			username = '0';

		// Setup User Query
		var query_args = {
			number:1,
			search_columns:['user_login', 'user_nicename'],
			fields:['user_login', 'user_nicename'],
			search: username,
		};

		// Set Status
		$scope.fieldStatus.username = "busy";
		
		// Query the DB
		$pwData.wp_user_query( query_args ).then(
			function(response) {
				$log.debug( 'WP USER QUERY RESULTS : ' + username , response.data );

				// NOT AVAILABLE
				// If the value is already taken
				if ( response.data.length > 0 ){
					if( response.data[0].user_nicename === username ){
						// Set Field Status
						$scope.fieldStatus.username = "taken";
						// Set Validity to FALSE
						$scope[ $scope.formName ].username.$setValidity('available',false);
						return false;
					}
				}

				// AVAILABLE
				// If it's not taken
				$scope.fieldStatus.username = "done";
				$scope[ $scope.formName ].username.$setValidity('available',true);
				$scope.formData.username = $scope[ $scope.formName ].username.$viewValue;

			},
			function(response) {
				throw { message:'Error: ' + JSON.stringify( response )};
			}
		);
	};

	// WATCH : value of username
	$scope.$watch( $scope.formName + ".username.$viewValue",
		function (){

			if ( $_.objExists( $scope, $scope.formName + '.username.$viewValue' ) ){

				// Set Field Status
				$scope.fieldStatus.username = "busy";

				// Unvalidate until hearing back from the query
				$scope[ $scope.formName ].username.$setValidity( 'available', false );

				// Clobber the Validation Function
				$_.clobber( 'validateUsername', 1000, function(){
					$scope.validateUsername( $scope[ $scope.formName ].username.$viewValue );
				} );
			}
		}, 1
	);


	// VALIDATE : Email Doesn't Exist
	$scope.validateEmail = function( email ){
		if(
			!($scope[ $scope.formName ].email.$error.required) &&
			!($scope[ $scope.formName ].email.$error.email) &&
			$scope[ $scope.formName ].email.$dirty
			){

			if( email == '' )
				email = '0';

			var query_args = {
				number:1,
				search_columns:['user_email'],
				fields:['user_email'],
				search: email,
			};

			$scope.fieldStatus.email = "busy";

			$pwData.wp_user_query( query_args ).then(
				// Success
				function(response) {
					$log.debug('QUERY : ' + email , response.data);

					// NOT AVAILABLE
					// If the value is already taken
					if ( response.data.length > 0 ){
						if( response.data[0].user_email === email ){
							// Set Field Status
							$scope.fieldStatus.email = "taken";
							// Set Validity to FALSE
							$scope[ $scope.formName ].email.$setValidity('available',false);
							return false;
						}
					}

					// AVAILABLE
					// If the value is not taken
					$scope.fieldStatus.email = "done";
					$scope[ $scope.formName ].email.$setValidity('available',true);
					$scope.formData.email = $scope[ $scope.formName ].email.$viewValue;

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
	$scope.$watch( $scope.formName + ".email.$viewValue",
		function (){

			if ( $_.objExists( $scope, $scope.formName + '.email.$viewValue' ) ){

				// Set Field Status
				$scope.fieldStatus.email = "busy";
				
				// Unvalidate until hearing back from the query
				$scope[ $scope.formName ].email.$setValidity( 'available', false );

				// Clobber the Validation Function
				$_.clobber( 'validateEmail', 1000, function(){
					$scope.validateEmail( $scope[ $scope.formName ].email.$viewValue );
				} );

			}
		}, 1
	);

	// INSERT USER
	$scope.insertUser = function(){        
		$scope.status = "inserting";
		var signupForm = $scope[ $scope.formName ];
		var userdata = {
			user_login: $scope.formData.username,
			user_pass: $scope.formData.password,
			user_email: $scope.formData.email,
			display_name: $scope.formData['name'],
		};

		// Add Context, from $scope.meta.context
		if( $_.objExists( $scope, 'meta.context' ) )
			userdata.context = $scope.meta.context;

		$log.debug('INSERTING USER : ' , userdata);
		$pwData.pw_insert_user( userdata ).then(
			// Success
			function(response) {
				$log.debug('USER INSERT SUCCESSFUL : ' , response);
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


	// WATCH : value of passwords - TODO: Refactor into modular service function
	$scope.$watch( "[ " +[ $scope.formName ]+ ".password.$viewValue, " +[ $scope.formName ]+ ".password_c.$viewValue ]", function (){
		// When it changes, check that confirmation password is the same
		if( $scope[ $scope.formName ].password.$viewValue == $scope[ $scope.formName ].password_c.$viewValue )
			$scope[ $scope.formName ].password_c.$setValidity( 'passwordMatch', true );
		else
			$scope[ $scope.formName ].password_c.$setValidity( 'passwordMatch', false );

	}, 1 );


}]);


/*///////// ------- SIGNUP FORM : RE-ENTER PASSWORD VALIDATION ------- /////////*/  
postworld.directive('validPasswordC', function () {
	return {
		require: 'ngModel',
		link: function (scope, elm, attrs, ctrl) {
			ctrl.$parsers.unshift(function (viewValue, $scope) {
				var noMatch = viewValue != scope.signupForm.password.$viewValue;
				ctrl.$setValidity('noMatch', !noMatch)
			})
		}
	}
});



/*
  _   _                        _        _   _            _       
 | | | |___  ___ _ __   _     / \   ___| |_(_)_   ____ _| |_ ___ 
 | | | / __|/ _ \ '__| (_)   / _ \ / __| __| \ \ / / _` | __/ _ \
 | |_| \__ \  __/ |     _   / ___ \ (__| |_| |\ V / (_| | ||  __/
  \___/|___/\___|_|    (_) /_/   \_\___|\__|_| \_/ \__,_|\__\___|

/*////////////// ------------ ACTIVATE ------------ //////////////*/  

postworld.directive('pwUserActivate', function() {
	return {
		restrict: 'A',
		controller: 'pwUserActivateCtrl',
	};
});

postworld.controller('pwUserActivateCtrl',
	[ '$scope', '$pw', '$rootScope', '$location', 'pwData', '$timeout', '$log', 'pwUsers', '_', '$window', 
	function( $scope, $pw, $rootScope, $location, $pwData, $timeout, $log, $pwUsers, $_, $window ) {

	///// INIT /////
	$scope.status = "done";
	$scope.formName = "resendKey";
	$scope.site = $pw.site;

	$scope.formData = {
		email:"",
	};

	$scope.fieldStatus = {
		email:'empty',
	};

	///// SET MODE /////
	$timeout( function(){
		$scope.authKey = $_.urlParam( 'activation_key' );

		// If auth_key is provided
		if( !$_.isEmpty( $scope.authKey )  ){
			$scope.activateUserKey( $scope.authKey );
		}
		// If user is logged in
		else if( $pw.user ){
			$scope.mode = 'loggedIn';		
		}
		else{
			$scope.mode = "resend";
		}

	}, 1 );


	///// VIEWS : Outline /////
	$scope.views = [ 'activate', 'welcome', 'error', 'resend', 'loggedIn' ];

	// SHOW VIEW : Switch the view based on $scope.mode
	$scope.showView = function( view ){
		return ( $scope.mode == view ) ? 
			true : false;
	}

	///// FUNCTIONS /////
	$scope.sendActivationLink = function( user_email ){
		$pwUsers.sendActivationLink($scope, user_email);
	};

	$scope.activateUserKey = function( auth_key ){        
		$scope.mode = "activate";
		$scope.status = "activating";
		//alert(auth_key);
		$scope.auth_key = auth_key;
		if( typeof $scope.auth_key_animate === 'undefined' )
			$scope.auth_key_animate = " ";
		$pwData.pw_activate_user( auth_key ).then(
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

	// VALIDATE : Email Exists
	$scope.validateEmailExists = function( email ){

		$scope.fieldStatus.email = "busy";

		// Clobber the Validation Function
		$_.clobber( 'validateEmail', 1000, function(){
			// Validate until hearing back from the DB
			$scope[ $scope.formName ].email.$setValidity('exists',true);

			$pwUsers.validateEmailExists( email,
				function( response, email ){

					// If the email is already taken
					if ( response.data.length > 0 ){
						// If they are not a subscriber (they are already activated)
						if( response.data[0].roles[0] != 'subscriber' ){
							// Set Field Status
							$scope.fieldStatus.email = "activated";
							// Set Validity to FALSE
							$scope[ $scope.formName ].email.$setValidity('exists',false);
						}
						else{
							$scope.fieldStatus.email = "done";
							$scope[ $scope.formName ].email.$setValidity('exists',true);
						}
					}
					else {
						$scope.fieldStatus.email = "unregistered";
						$scope[ $scope.formName ].email.$setValidity('exists',false);
					}

				}
			);
		});

	};


	// WATCH : value of email
	$scope.$watch( "resendKey.email.$viewValue", function (){
		// If it exists
		if ( $_.objExists( $scope, 'resendKey.email.$viewValue' ) )
			// Lift Validation Error
			$scope.resendKey.email.$setValidity('exists',true);
			// When it changes, emit it's value to the parent controller
			$scope.validateEmailExists( $scope.resendKey.email.$viewValue );
		}, 1
	);


}]);





/*
  ____                _     ____                                     _ 
 |  _ \ ___  ___  ___| |_  |  _ \ __ _ ___ _____      _____  _ __ __| |
 | |_) / _ \/ __|/ _ \ __| | |_) / _` / __/ __\ \ /\ / / _ \| '__/ _` |
 |  _ <  __/\__ \  __/ |_  |  __/ (_| \__ \__ \\ V  V / (_) | | | (_| |
 |_| \_\___||___/\___|\__| |_|   \__,_|___/___/ \_/\_/ \___/|_|  \__,_|
																	   
/*////////////// ------------ RESET PASSWORD ------------ //////////////*/  

postworld.directive('pwUserPasswordReset', function() {
	return {
		restrict: 'A',
		controller: 'pwUserPasswordResetCtrl',
	};
});


postworld.controller( 'pwUserPasswordResetCtrl',
	[ '$scope', '$rootScope', '$window', 'pwData', '$timeout', '$log', 'pwUsers', '_',
	function( $scope, $rootScope, $window, $pwData, $timeout, $log, $pwUsers, $_ ){

	///// INIT /////
	$scope.status = "done";
	$scope.formData = {
		email:"",
		password:"",
	};

	$scope.fieldStatus = {
		email:'empty',
	};


	///// SET MODE /////
	$timeout( function(){

		// If user is logged in
		if( $_.objExists( $window, 'pw.user.data.ID' ) ){
			$scope.mode = 'loggedIn';			
			return false;
		}

		// If auth_key is provided
		$scope.authKey = $_.urlParam( 'auth_key' );
		if( !$_.isEmpty( $scope.authKey )  ){
			$scope.mode = "resetPassword";
		}
		else{
			// Default Mode
			$scope.mode = "emailInput";
		}

	}, 1 );


	///// VIEWS : Outline /////
	$scope.views = [ 'emailInput', 'resetPassword', 'login', 'loggedIn' ];

	// SHOW VIEW : Switch the view based on $scope.mode
	$scope.showView = function( view ){
		return ( $scope.mode == view ) ? 
			true : false;
	}

	///// EMAIL : SEND LINK /////
	$scope.emailFormName = "emailInput";
	
	$scope.sendResetPasswordLink = function( email ){
		$pwUsers.sendResetPasswordLink( $scope, email );
	};

	// VALIDATE : Email Exists
	$scope.validateEmailExists = function( email ){
		$log.debug( "VALIDATE-EMAIL-EXISTS" );
		//$scope[ $scope.emailFormName ].email.$setValidity( 'exists', false );

		if( _.isEmpty( email ) )
			return false;

		// Set Field Status
		$scope.fieldStatus.email = "busy";

		// Clobber the Validation Function
		$_.clobber( 'validateEmail', 1000, function(){

			// Validate if the email exists
			$pwUsers.validateEmailExists( email,
				function( response, email, formName ){
					// Callback function
					$log.debug( "VALIDATE-EMAIL-EXISTS-CALLBACK : ", response.data );

					// If the email is already taken
					if ( response.data.length > 0 ){
						// If they are not a subscriber (they are already activated)
						$scope[ $scope.emailFormName ].email.$setValidity( 'exists', true );
						$scope.fieldStatus.email = "done";
					}
					else {
						$scope.fieldStatus.email = "unregistered";
						$scope[ $scope.emailFormName ].email.$setValidity( 'exists', false );
					}

				}
			);

		});

	};

	// WATCH : value of email
	$scope.$watch( $scope.emailFormName + ".email.$viewValue",
		function (){
			if( $_.objExists( $scope, $scope.emailFormName + ".email.$viewValue" ) ){
				// Validate the email exists
				$scope.validateEmailExists( $scope[ $scope.emailFormName ].email.$viewValue );
			}
		}
	);

	///// PASSWORD RESET SUBMIT /////
	$scope.passwordFormName = "resetPassword";
	
	$scope.submitNewPassword = function( password ){
		//alert($scope.authKey);
		$scope.status = "busy";
		var userdata = {
			user_pass: password,
			auth_key: $scope.authKey
		};

		//alert(JSON.stringify(userdata));
		$scope[ $scope.passwordFormName ].$setValidity( 'busy', false );

		$log.debug('SENDING NEW PASSWORD : ' , userdata);
		$pwData.reset_password_submit( userdata ).then(
			// Success
			function(response) {
				$log.debug('NEW PASSWORD RETURN : ' , response.data);
				if ( !isNaN( response.data.ID ) ){
					$scope.status = "done";
					$timeout(function() {
					  $scope.mode = "login";
					}, 1000);
					$scope[ $scope.passwordFormName ].$setValidity('success',true);
				} else {
					$scope.status = "error";
					$timeout(function() {
						$scope.status = "done";
						$scope[ $scope.passwordFormName ].$setValidity('busy',true);
						}, 5000);
				}
			},
			// Failure
			function(response) {
				throw { message:'Error: ' + JSON.stringify(response)};
			}
		);

	};

	// WATCH : value of passwords - TODO: Refactor into modular service function
	$scope.$watch( "[ " + $scope.passwordFormName + ".password.$viewValue, " + $scope.passwordFormName + ".password_c.$viewValue ]",
		function (){
			if( $_.objExists( $scope, $scope.passwordFormName + ".password.$viewValue" ) ){
				// When it changes, check that confirmation password is the same
				if( $scope[ $scope.passwordFormName ].password.$viewValue == $scope[ $scope.passwordFormName ].password_c.$viewValue )
					$scope[ $scope.passwordFormName ].password_c.$setValidity( 'passwordMatch', true );
				else
					$scope[ $scope.passwordFormName ].password_c.$setValidity( 'passwordMatch', false );
			}
		}, 1
	);

}]);




/*
   _                      _   _                   
  | |   _   _ ____      _| | | |___  ___ _ __ ___ 
 / __) (_) | '_ \ \ /\ / / | | / __|/ _ \ '__/ __|
 \__ \  _  | |_) \ V  V /| |_| \__ \  __/ |  \__ \
 (   / (_) | .__/ \_/\_/  \___/|___/\___|_|  |___/
  |_|      |_|                                    

/*///////// ------- SERVICE : PW USERS ------- /////////*/  
postworld.service('pwUsers', ['$log', '$timeout', 'pwData', function ($log, $timeout, $pwData) {
	return{
		sendActivationLink : function($scope, user_email){
			$scope.status = "busy";
			var userdata = {
				email: user_email,
			};
			$log.debug('SENDING ACTIVATION LINK : ' , userdata);
			$pwData.activationEmail( userdata ).then(
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
			$pwData.reset_password_email( userdata ).then(
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

		validateEmailExists : function ( email, callback ){
			if( _.isEmpty( email ) )
				return false;

			// Setup Query
			var query_args = {
				number:1,
				search_columns:['user_email'],
				fields:'all',
				search: email,
			};

			// Run Query
			$pwData.wp_user_query( query_args ).then(
				function(response) {
					// Return reponse data to the specified callback function
					if( !_.isUndefined( callback ) ){
						callback( response, email );
					}
				},
				function(response) {
					throw { message:'Error: ' + JSON.stringify(response)};
				}
			);
		},

	}
}]);
