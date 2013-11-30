/*
     _       _           _         ____                      _                     
    / \   __| |_ __ ___ (_)_ __   |  _ \ _ __ ___  _ __   __| | _____      ___ __  
   / _ \ / _` | '_ ` _ \| | '_ \  | | | | '__/ _ \| '_ \ / _` |/ _ \ \ /\ / / '_ \ 
  / ___ \ (_| | | | | | | | | | | | |_| | | | (_) | |_) | (_| | (_) \ V  V /| | | |
 /_/   \_\__,_|_| |_| |_|_|_| |_| |____/|_|  \___/| .__/ \__,_|\___/ \_/\_/ |_| |_|
                                                  |_|                              
////////// ------------ ADMIN POSTS DROPDOWN ------------ //////////*/   
var adminPostDropdown = function ($scope, $rootScope, $location, $window, $log, pwQuickEdit) {

    $scope.menuOptions = [
        {
            name: "Quick Edit",
            icon:"icon-pencil",
            action:"quick-edit"
        },
        {
            name: "Edit",
            icon:"icon-edit",
            action:"pw-edit",
        },
        {
            name: "WP Edit",
            icon:"icon-edit-sign",
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
            icon:"icon-trash",
            action:"trash",
        }
    ];

    // Actions which each role has access to
    var actionsByRole = {
        "administrator": {
            own:['quick-edit', 'pw-edit', 'wp-edit','trash'],
            other:['quick-edit', 'pw-edit', 'wp-edit','trash']
        },
        "editor":{
            own: ['quick-edit', 'pw-edit', 'wp-edit','trash'],
            other: ['quick-edit', 'pw-edit', 'wp-edit','trash'],
        },
        "author":{
            own: ['quick-edit', 'pw-edit', 'wp-edit','trash'],
            other: [],
        },
        "contributor":{
            own: ['quick-edit', 'pw-edit', 'wp-edit','trash'],
            other: [],
        },
        "guest":{
            own: [],
            other: [],
        },
    };

    // Localize current user data
    $scope.current_user = $window.pwGlobals.current_user;

    // Detect the user's possession in relation to the post
    // If the user's ID is same as the post author's ID
    if ( typeof $scope.current_user.data !== 'undefined' && typeof $scope.post.author.ID !== 'undefined' ){
        if( $scope.current_user.data.ID == $scope.post.author.ID )
            $scope.postPossession = "own";
        else
            $scope.postPossession = "other";
    } else {
        $scope.postPossession = "other";
    }

    // Detect current user's role
    if ( $scope.current_user == 0 )
        $scope.currentRole = "guest";
    else if ( typeof $scope.current_user.roles != undefined ){
        $scope.currentRole = $scope.current_user.roles[0];
    }

    // Setup empty menu options array
    $scope.userOptions = [];

    // TODO : CHECK POST OBJECT, IF USER ID = SAME AS POST AUTHOR

    // Build menu for user based on role
    angular.forEach( $scope.menuOptions, function( option ){
        if( actionsByRole[ $scope.currentRole ][ $scope.postPossession ].indexOf( option.action ) != "-1" )
            $scope.userOptions.push( option );
    });

    // If no options added, set empty
    if ( $scope.userOptions == [] )
        $scope.userOptions = "0";
    

    $scope.menuAction = function(action){

        if( action == "wp-edit" )
            $window.location.href = $scope.post.edit_post_link.replace("&amp;","&");

        if( action == "pw-edit" )
            $window.location.href = "/post/#/edit/"+$scope.post.ID;

        if( action == "quick-edit" ){
            pwQuickEdit.openQuickEdit($scope.post);
        }
        if( action == "trash" ){
            pwQuickEdit.trashPost($scope.post.ID, $scope);
        }

    };

};





/*
     _       _           _         ____                      _                     
    / \   __| |_ __ ___ (_)_ __   |  _ \ _ __ ___  _ __   __| | _____      ___ __  
   / _ \ / _` | '_ ` _ \| | '_ \  | | | | '__/ _ \| '_ \ / _` |/ _ \ \ /\ / / '_ \ 
  / ___ \ (_| | | | | | | | | | | | |_| | | | (_) | |_) | (_| | (_) \ V  V /| | | |
 /_/   \_\__,_|_| |_| |_|_|_| |_| |____/|_|  \___/| .__/ \__,_|\___/ \_/\_/ |_| |_|
                                                  |_|                              
////////// ------------ ADMIN COMMENTS DROPDOWN ------------ //////////*/   
var adminCommentDropdown = function ($scope, $rootScope, $location, $window, $log, pwCommentsService) {

    var comment = $scope.child;

    $scope.menuOptions = [
        {
            name: "Edit",
            icon:"icon-edit",
            action:"edit",
        },
        {
            name: "Flag",
            icon:"icon-flag",
            action:"flag",
        },
        {
            name: "Trash",
            icon:"icon-trash",
            action:"trash",
        }
    ];

    // Actions which each role has access to
    var actionsByRole = {
        "administrator": {
            own:['edit','flag','trash'],
            other:['edit','flag','trash']
        },
        "editor":{
            own: ['edit','flag','trash'],
            other: ['edit','flag','trash'],
        },
        "author":{
            own: ['edit','trash'],
            other: ['flag'],
        },
        "contributor":{
            own: ['edit','trash'],
            other: ['flag'],
        },
        "guest":{
            own: [],
            other: [],
        },
    };

    // Localize current user data
    $scope.current_user = $window.pwGlobals.current_user;

    // Detect if the user owns the comment
    // If the user's ID is same as the post author's ID
    if ( typeof $scope.current_user.data !== 'undefined' && typeof comment !== 'undefined' ){
        if( $scope.current_user.data.ID == comment.user_id )
            $scope.postPossession = "own";
        else
            $scope.postPossession = "other";
    } else {
        $scope.postPossession = "other";
    }

    // Detect current user's role
    if ( $scope.current_user == 0 )
        $scope.currentRole = "guest";
    else if ( typeof $scope.current_user.roles != undefined ){
        $scope.currentRole = $scope.current_user.roles[0];
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

};
