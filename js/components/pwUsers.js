'use strict';

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


    // WATCH : value of passwords - TODO: Refactor into modular service function
    $scope.$watch( "[formData.password, formData.password_c]", function (){
        // When it changes, check that confirmation password is the same
        if( $scope.formData.password == $scope.formData.password_c )
            $scope.signupForm.password_c.$setValidity('noMatch',true);
        else
            $scope.signupForm.password_c.$setValidity('noMatch',false);
        }, 1 );

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
});




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

    // WATCH : value of passwords - TODO: Refactor into modular service function
    $scope.$watch( "[formData.password, formData.password_c]", function (){
        // When it changes, check that confirmation password is the same
        if( $scope.formData.password == $scope.formData.password_c )
            $scope.signupForm.password_c.$setValidity('noMatch',true);
        else
            $scope.signupForm.password_c.$setValidity('noMatch',false);
        }, 1 );

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
