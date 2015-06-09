/*____           _        _        _   _                 
 |  _ \ ___  ___| |_     / \   ___| |_(_) ___  _ __  ___ 
 | |_) / _ \/ __| __|   / _ \ / __| __| |/ _ \| '_ \/ __|
 |  __/ (_) \__ \ |_   / ___ \ (__| |_| | (_) | | | \__ \
 |_|   \___/|___/\__| /_/   \_\___|\__|_|\___/|_| |_|___/
                                                         
////////// ------------ POST ACTIONS CONTROLLER ------------ //////////*/

postworld.directive( 'pwPostActions',
    [ '$pw', '$log', '_',
    function( $pw, $log, $_ ){
    return {
        restrict: 'AE',
        controller: 'postActions',
        link: function( $scope, element, attrs ){

            var userId = $_.get( $pw, 'user.ID' );
            $log.debug( 'pwPostActions : userId', userId );

            if( !$_.isNumeric( userId ) )
                element.addClass('ng-hide');

        },
    };
}]);

postworld.controller('postActions',
    [ "$scope", "pwData", "_",
    function($scope, pwData, $_ ) {

    $scope.$watch( "post.viewer",
        function (){
            if( $_.objExists( $scope, "post.viewer" ) ){
                ( $scope.post.viewer.is_favorite == true ) ? $scope.isFavorite="selected" : $scope.isFavorite="" ;
                ( $scope.post.viewer.is_view_later == true ) ? $scope.isViewLater="selected" : $scope.isViewLater="" ;
            }
        }, 1 );

    $scope.setFavorite = function($event){
        $scope.togglePostRelationship('favorites');
        //if ($event.stopPropagation) $event.stopPropagation();
        //if ($event.preventDefault) $event.preventDefault();
        
    }

    $scope.setViewLater = function($event){
        $scope.togglePostRelationship('view_later');
        //if ($event.stopPropagation) $event.stopPropagation();
        //if ($event.preventDefault) $event.preventDefault();
        
    }

    $scope.spinnerClass = "";

    $scope.togglePostRelationship = function( postRelationship ) {

        // Localize the viewer object
        var viewer = $scope.post.viewer;

        $scope.spinnerClass = "icon-spin";

        // Check toggle switch
        var setTo;
        if ( postRelationship == "favorites" ){
            ( viewer.is_favorite == true ) ? setTo = false : setTo = true;
            $scope.favoriteStatus = "busy";
        }
        if ( postRelationship == "view_later" ){
            ( viewer.is_view_later == true ) ? setTo = false : setTo = true ;
            $scope.viewLaterStatus = "busy";
        }
        // Setup parmeters
        var args = {
            "relationship" : postRelationship,
            "switch" : setTo,
            "post_id" : $scope.post.ID,
        };
        // AJAX Call 
        pwData.setPostRelationship( args ).then(
            // ON : SUCCESS
            function(response) {    
                //SET FAVORITE
                if ( postRelationship == "favorites"){
                    if ( response.data === false )
                        $scope.post.viewer.is_favorite = false;
                    else if ( response.data === true )
                        $scope.post.viewer.is_favorite = true;
                    //else
                        //alert( "Server error setting favorite." )
                    $scope.favoriteStatus = "done";
                    $scope.spinnerClass = "";
                }
                //SET VIEW LATER
                if ( postRelationship == "view_later"){
                    if ( response.data === false )
                        $scope.post.viewer.is_view_later = false;
                    else if ( response.data === true )
                        $scope.post.viewer.is_view_later = true;
                    //else
                        //alert( "Server error setting view later." )

                    $scope.viewLaterStatus = "done";
                    $scope.spinnerClass = "";
                }
            },
            // ON : FAILURE
            function(response) {
                //alert('Client error.');
            }
        );

    };

}]);




/*____           _    __     __    _       
 |  _ \ ___  ___| |_  \ \   / /__ | |_ ___ 
 | |_) / _ \/ __| __|  \ \ / / _ \| __/ _ \
 |  __/ (_) \__ \ |_    \ V / (_) | ||  __/
 |_|   \___/|___/\__|    \_/ \___/ \__\___|
                                                                                          
////////// ------------ POST ACTIONS CONTROLLER ------------ //////////*/
postworld.directive( 'pwPostVote', [ function($scope){
    return {
        restrict: 'AE',
        controller: 'postVote',
    };
}]);

postworld.controller('postVote',
    [ '$window', '$rootScope', '$scope', '$log', 'pwData',
    function( $window, $rootScope, $scope, $log, pwData ) {


    // SWITCH CSS CLASSES BASED ON VOTE
    // TODO : REFACTOR THIS WITHOUT WATCH - ??
    
    $scope.$watch( "post.viewer.has_voted",
        function (){
            ( $scope.post.viewer.has_voted > 0 ) ? $scope.hasVotedUp = "selected" : $scope.hasVotedUp = "" ;
            ( $scope.post.viewer.has_voted < 0 ) ? $scope.hasVotedDown = "selected" : $scope.hasVotedDown = "" ;
            if ( $scope.post.viewer.has_voted == 0 ){
                $scope.hasVotedUp = "";
                $scope.hasVotedDown = "";
            }
        }, 1 );
    

    // CAST VOTE ON THE POST
    $scope.spinnerClass = "";
    $scope.votePost = function( points ){
        // Get the voting power of the current user
        if( typeof $window.pw.user.postworld !== 'undefined' )
            var vote_power = parseInt($window.pw.user.postworld.vote_power);
        // If they're not logged in, return false
        if( typeof vote_power === 'undefined' ){
            alert( "Must be logged in to vote." );
            return false;
        }
        // Define how many points have they already given to this post
        var has_voted = parseInt($scope.post.viewer.has_voted);
        // Define how many points will be set
        var setPoints = ( has_voted + points );
        // If set points exceeds vote power
        if( Math.abs(setPoints) > vote_power ){
            setPoints = (vote_power * points);
            //alert( "Normalizing : " + setPoints );
        }
        // Setup parameters
        var vars = {
            post_id: $scope.post.ID,
            points: setPoints,
        };
        // Set Status
        $scope.voteStatus = "busy";
        $scope.spinnerClass = "icon-spin";

        // AJAX Call 
        $log.debug('pwPostVote : VOTE SUBMIT : ', vars );
        pwData.setPostPoints ( vars ).then(
            // ON : SUCCESS
            function(response) {
                // RESPONSE.DATA FORMAT : {"point_type":"post","user_id":1,"id":178472,"points_added":6,"points_total":"3"}
                $log.debug('pwPostVote : VOTE RETURN : ' + JSON.stringify(response) );
                if ( response.data.id == $scope.post.ID ){
                    // UPDATE POST POINTS
                    $scope.post.post_points = response.data.points_total;
                    // UPDATE VIEWER HAS VOTED
                    $scope.post.viewer.has_voted = ( parseInt($scope.post.viewer.has_voted) + parseInt(response.data.points_added) ) ;
                } //else
                    //alert('Server error voting.');
                $scope.voteStatus = "done";
                $scope.spinnerClass = "";
            },
            // ON : FAILURE
            function(response) {
                $scope.voteStatus = "done";
                $scope.spinnerClass = "";
                //alert('Client error voting.');
            }
        );

    }

}]);


/*   _       _           _         ____                      _                     
    / \   __| |_ __ ___ (_)_ __   |  _ \ _ __ ___  _ __   __| | _____      ___ __  
   / _ \ / _` | '_ ` _ \| | '_ \  | | | | '__/ _ \| '_ \ / _` |/ _ \ \ /\ / / '_ \ 
  / ___ \ (_| | | | | | | | | | | | |_| | | | (_) | |_) | (_| | (_) \ V  V /| | | |
 /_/   \_\__,_|_| |_| |_|_|_| |_| |____/|_|  \___/| .__/ \__,_|\___/ \_/\_/ |_| |_|
                                                  |_|                              
////////// ------------ ADMIN POSTS DROPDOWN ------------ //////////*/   

postworld.directive( 'pwAdminPostMenu', [ function($scope){
    return {
        restrict: 'AE',
        controller: 'adminPostDropdown',
    };
}]);

postworld.controller('adminPostDropdown',
    [ '$scope', '$rootScope', '$location', '$window', '$log', 'pwModal', 'pwQuickEdit', '_', '$timeout', '$pw',
    function( $scope, $rootScope, $location, $window, $log, $pwModal, $pwQuickEdit, $_, $timeout, $pw ) {

    ///// MENU OPTIONS /////
    // Default Menu Options
    $scope.menuOptions = [
        {
            name: "Quick Edit",
            icon:"pwi-pencil",
            action:"quick-edit"
        },
        {
            name: "Edit",
            icon:"pwi-edit",
            action:"pw-edit",
        },
        {
            name: "WP Edit",
            icon:"pwi-edit-square",
            action:"wp-edit",
        },
        /*
        {
            name: "Flag",
            icon:"icon-flag",
            action:"flag",
        },
        */
        {
            name: "Trash",
            icon:"pwi-trash",
            action:"trash",
        }
    ];

    // Merge with over-rides
    // Iterate through each one, based on 'action'
    // Over-write Names / Icons if specified

    // Menu Options

    $scope.getControlsObject = function( post ){
        // Gets the controls object for the post's post type
        // Using the 'post' object as default fallback

        // Get the post type from the post
        var getPostType = $_.getObj( post, 'post_type' );
        var postType = ( getPostType ) ?
            getPostType : 'post';

        // Get the object for this post type
        var postTypeObject = $_.getObj( $pw, 'controls.' + postType );
        // Get the default object
        var defaultObject =  $_.getObj( $pw, 'controls.post' );

        // Use post type object if available, otherwise use default object
        var output = ( postTypeObject ) ?
            postTypeObject : defaultObject;

        //$log.debug('GET CONTROLS OBJECT');

        return output ;

    }

    var controlsObject = $scope.getControlsObject( $scope.post );

    $scope.getOverrideMenuOptions = function( post ){
        var getPostType = $_.getObj( post, 'post_type' );
        var postType = ( getPostType ) ?
            getPostType : 'post';

        // Check if there's over ride menu options for this post type
        var postTypeOverrideMenuOptions = $_.getObj( $window, 'pwSiteGlobals.controls.' + postType + '.menu_options' );
        var defaultOverrideMenuOptions =  $_.getObj( $window, 'pwSiteGlobals.controls.post.menu_options' );

        var output = ( postTypeOverrideMenuOptions ) ?
            postTypeOverrideMenuOptions : defaultOverrideMenuOptions;

        return output ;
    
    }

    var overrideMenuOptions = $_.get( controlsObject, 'menu_options' );
    // If custom menu options are provided
    if( overrideMenuOptions != false ){
        // Custom menu items to over-ride defaults
        var newMenuOptions = [];
        var overrideMenuOption, newOption;
        // Iterate through each default menu option
        angular.forEach( $scope.menuOptions, function( menuOption ){
            newOption = menuOption;
            // Get the related override by action
            overrideMenuOption = _.findWhere( overrideMenuOptions, { action: menuOption.action } );
            // Override : NAME
            if( $_.getObj( overrideMenuOption, 'name' ) )
                newOption.name = overrideMenuOption.name;
            // Override : ICON
            if( $_.getObj( overrideMenuOption, 'icon' ) )
                newOption.icon = overrideMenuOption.icon;

            newMenuOptions.push(newOption);
        });
        $scope.menuOptions = newMenuOptions;
    }


    ///// ROLES /////
    // Define actions which each role has access to
    var actionsByRole = controlsObject.role_access;

    var initAttempts = 0;
    $scope.initMenu = function(){
        //$log.debug( "$scope.post.author.ID", $_.getObj( $scope, 'post.author.ID' ) );
        //$log.debug( "$pw.view.post.post_author", $_.getObj( $pw, 'view.post.post_author' ) );

        // Detect the Author ID
        if( $_.objExists( $scope, 'post.author.ID' ) )
            var author_id = $scope.post.author.ID;
        else if( $_.objExists( $scope, 'post.post_author' ) )
             var author_id = $scope.post.post_author;
        else if( $_.objExists( $pw, 'view.post.post_author' ) )
             var author_id = $pw.view.post.post_author;
        
        // Try Initializing the menu until author ID is defined
        if( _.isUndefined( author_id ) ){
            initAttempts ++;
            // Stop trying after 100 tries
            if( initAttempts <= 10 ){
                $timeout(function() {
                    $scope.initMenu();
                }, 500);  
            }
            return false;
        }

        // Detect the user's possession in relation to the post
        // If the user's ID is same as the post author's ID
        if ( typeof $pw.user.data !== 'undefined' ){
            if( $pw.user.data.ID == author_id )
                $scope.postPossession = "own";
            else
                $scope.postPossession = "other";
        } else {
            $scope.postPossession = "other";
        }

        // Detect current user's role
        if ( $pw.user == 0 )
            $scope.currentRole = "guest";
        else if ( typeof $pw.user.roles != undefined ){
            $scope.currentRole = $pw.user.roles[0];
        }

        // Setup empty menu options array
        $scope.userOptions = [];

        // @todo : CHECK POST OBJECT, IF USER ID = SAME AS POST AUTHOR
        // Build menu for user based on role
        angular.forEach( $scope.menuOptions, function( option ){
            // Get the allowed actions
            var allowedActions = $_.get( actionsByRole, $scope.currentRole + '.' + $scope.postPossession );
            // Add only the allowed actions
            if( $_.inArray( option.action, allowedActions ) )
                $scope.userOptions.push( option );
        });

        // If no options added, set empty
        if ( $scope.userOptions == [] )
            $scope.userOptions = "0";        

    }
    // Run the function
    $scope.initMenu();
    
    $scope.getEditPostPageUrl = function(){

        // Define Post Type
        var post_type = ( $_.objExists( $scope, 'post.post_type' ) ) ?
            $scope.post.post_type : 'post';

        // Localize Options
        var edit_post = $window.pwSiteGlobals.edit_post;

        // Check if that post type page name is defined
        var url = ( $_.objExists( edit_post, post_type + '.url' ) ) ?
            edit_post[post_type].url :
            // If not, use the default
            edit_post['post'].url;

        return url;

    }

    $scope.menuAction = function(action){

        switch( action ){

            case 'wp-edit':
                $window.location.href = $scope.post.edit_post_link.replace("&amp;","&");
                break;

            case 'pw-edit':
                var editPostPageUrl = $scope.getEditPostPageUrl();
                $window.location.href = editPostPageUrl + "#/edit/"+$scope.post.ID;
                break;

            case 'quick-edit':
                $pwModal.openModal( { mode: 'edit', post: $scope.post } );
                break;

            case 'trash':
                $pwQuickEdit.trashPost($scope.post.ID, $scope);
                break;
        }


    };

}]);


/*   _       _           _         ____                      _                     
    / \   __| |_ __ ___ (_)_ __   |  _ \ _ __ ___  _ __   __| | _____      ___ __  
   / _ \ / _` | '_ ` _ \| | '_ \  | | | | '__/ _ \| '_ \ / _` |/ _ \ \ /\ / / '_ \ 
  / ___ \ (_| | | | | | | | | | | | |_| | | | (_) | |_) | (_| | (_) \ V  V /| | | |
 /_/   \_\__,_|_| |_| |_|_|_| |_| |____/|_|  \___/| .__/ \__,_|\___/ \_/\_/ |_| |_|
                                                  |_|                              
////////// ------------ ADMIN COMMENTS DROPDOWN ------------ //////////*/   

postworld.controller( 'adminCommentDropdown',
    [ '$scope', '$rootScope', '$location', '$window', '$log', 'pwCommentsService', '$pw',
    function($scope, $rootScope, $location, $window, $log, pwCommentsService, $pw){

    var comment = $scope.child;

    $scope.menuOptions = [
        {
            name: "Edit",
            icon:"pwi-edit",
            action:"edit",
        },
        {
            name: "Flag",
            icon:"pwi-flag",
            action:"flag",
        },
        {
            name: "Trash",
            icon:"pwi-trash",
            action:"trash",
        }
    ];

    // Actions which each role has access to
    var actionsByRole = $window.pwSiteGlobals.controls.comment.role_access;

    
    // Detect if the user owns the comment
    // If the user's ID is same as the post author's ID
    if ( typeof $pw.user.data !== 'undefined' && typeof comment !== 'undefined' ){
        if( $pw.user.data.ID == comment.user_id )
            $scope.postPossession = "own";
        else
            $scope.postPossession = "other";
    } else {
        $scope.postPossession = "other";
    }

    // Detect current user's role
    if ( $pw.user == 0 || $pw.user == false )
        $scope.currentRole = "guest";
    else if ( typeof $pw.user.roles != undefined ){
        $scope.currentRole = $pw.user.roles[0];
    }

    // Setup empty menu options array
    $scope.userOptions = [];

    // Build menu for user based on role
    angular.forEach( $scope.menuOptions, function( option ){
        if( actionsByRole[ $scope.currentRole ][ $scope.postPossession ].indexOf( option.action ) != "-1" )
            $scope.userOptions.push( option );
    });

    // If no options added, set empty
    if ( $scope.userOptions == [] )
        $scope.userOptions = "0";
    
    // Menu Actions
    $scope.menuAction = function(action, child){
        if( action == "edit" )
            $scope.toggleEditBox(child);
        if( action == "flag" ){
            $scope.flagComment(child);
            // Remove the flag option after flagging
            var updatedUserOptions = [];
            angular.forEach( $scope.userOptions, function( option ){
                if( option.action != 'flag' )
                    updatedUserOptions.push( option );
            });
            $scope.userOptions = updatedUserOptions;
        }
        if( action == "trash" ){
            if ( window.confirm("Are you sure you want to delete this comment?") ) {
                $scope.deleteComment(child);
            }
        }
    };



}]);
