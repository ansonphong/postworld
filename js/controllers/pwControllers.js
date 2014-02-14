'use strict';

/*
  ____           _      ____            _             _ _           
 |  _ \ ___  ___| |_   / ___|___  _ __ | |_ _ __ ___ | | | ___ _ __ 
 | |_) / _ \/ __| __| | |   / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
 |  __/ (_) \__ \ |_  | |__| (_) | | | | |_| | | (_) | | |  __/ |   
 |_|   \___/|___/\__|  \____\___/|_| |_|\__|_|  \___/|_|_|\___|_|   
                                                                    
/*////////// ------------ POST CONTROLLER ------------ //////////*/                
var postController = function ( $scope, $rootScope, $window, $sce, pwData ) {

    // Define backup source for 'post' object 
    if( typeof $scope.post === 'undefined' ){
        if( typeof $window.post != 'undefined' ){
            $scope.post = $window.post;
        }
    }

    // RUN CUSTOM POST FUNCTIONS
    // This function can be added to the $window object
    // For performing theme-specific per-post operations
    if( typeof $window.pw_post_functions === "function" )
        $window.pw_post_functions($scope);

    // Trust the post_content as HTML
    if( typeof $scope.post.post_content !== 'undefined' ){
        $scope.post.post_content = $sce.trustAsHtml($scope.post.post_content);
    }

    // IMPORT LANGUAGE
    if(
        typeof $window.pwGlobals.paths !== 'undefined' &&
        typeof $window.pwSiteLanguage !== 'undefined' &&
        typeof $window.pwGlobals.current_user !== 'undefined' &&
        typeof $scope.post !== 'undefined'
        ){
        $scope.language = $window.pwSiteLanguage;
        $scope.current_user_id = $window.pwGlobals.current_user.ID;
        // GENERATE  SHARE LINK
        $scope.share_link = $window.pwGlobals.paths.home_url + "/?u=" + $window.pwGlobals.current_user.ID + "&p=" + $scope.post.ID;
    }

    // Toggles class="expaned", used with ng-class="expanded" 
    $scope.expanded = "";
    var clickTip = "Click to expand";
    $scope.clickTip = clickTip;
    $scope.toggleExpanded = function(){
        ( $scope.expanded == "" ) ? $scope.expanded = "expanded" : $scope.expanded = "" ;
        ( $scope.clickTip != "" ) ? $scope.clickTip = "" : $scope.clickTip = clickTip ;
    };

    // Update the contents of post after Quick Edit
    $rootScope.$on('postUpdated', function(event, post_id) {
        if ( $scope.post.ID == post_id ){
            var args = {
                post_id: post_id,
                fields: 'all'
            };
            pwData.pw_get_post(args).then(
                // Success
                function(response) {
                    if (response.status==200) {
                        //$log.debug('pwPostLoadController.pw_load_post Success',response.data);                     
                        $scope.post = response.data;

                        // Update Classes
                        $scope.setClass();
                    
                    } else {
                        // handle error
                    }
                },
                // Failure
                function(response) {
                    // $log.error('pwFeedController.pw_live_feed Failure',response);
                    // TODO Show User Friendly Message
                }
            );
        }
    });

    ///// TIME FUNCTIONS /////
    $scope.jsDateToTimestamp = function(jsDate){
        var dateObject = new Date(jsDate);

        return Date.parse(dateObject);

    }

    

    ///// IMAGE FUNCTIONS /////
    $scope.backgroundImage = function( imageUrl, properties ){

        // Set the Image URL
        //var imageUrl = $scope.post.image[imageHandle].url;

        var style = { 'background-image': "url(" + imageUrl + ")" };

        // Add additional properties
        if( !_.isUndefined( properties ) ){
            angular.forEach( properties, function(value, key){
                style[key] = value;
            });
        }
        return style;
    }


    ///// SET ACTIVE CLASS /////
    $scope.setActiveClass = function( boolean ){
        //alert('test');
        return ( boolean ) ? "active" : "";
    }

    $scope.gotoUrl = function( url ){
        window.location = url;
    };


};
