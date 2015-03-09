/*
   ___        _   _                 
  / _ \ _ __ | |_(_) ___  _ __  ___ 
 | | | | '_ \| __| |/ _ \| '_ \/ __|
 | |_| | |_) | |_| | (_) | | | \__ \
  \___/| .__/ \__|_|\___/|_| |_|___/
       |_|                          
////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminOptions', [ function(){
    return { 
        controller: 'pwAdminOptionsCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-options');
        }
    };
}]);

postworldAdmin.controller('pwAdminOptionsCtrl',
	[ '$scope', '$window', '$parse', '$log', 'iData', 'pwData', '_',
	function ( $scope, $window, $parse, $log, $iData, $pwData, $_ ) {

	$scope.refreshOptions = function(){
		//alert("UPDATE OPTIONS");
	}

	$scope.test = function(message){
		alert(message);
	}

	///// ACTION • UPDATE OPTIONS /////
	$scope.$on('updateOptions', function( scope, vars ) { 
        $log.debug( "UPDATE OPTIONS : ", vars );

        if( typeof vars == 'object' )
	        $scope.iOptions = $_.setObj( $scope.iOptions, vars['key'], vars['value'] );

    });


	///// ACTION • SELECTED MEDIA /////
	$scope.$on('selectedMedia', function( scope, vars ) { 
        $log.debug( "SELECTED MEDIA : ", vars );

        // SET SCOPE MODEL
        if( vars.format == 'media-id' ){
        	$scope.iOptions = $_.setObj( $scope.iOptions, vars['key'], vars['media'] );
        }

        // SAVE TO THE DATABASE
        var vars = {
        	option_name: 'i-options',
        	key: 	vars['key'],
        	value: 	vars['media']
        };
		$pwData.set_option_obj( vars ).then(
			function(response){
				$log.debug( "set_option_obj", response );
			},
			function(response) {}
		);


    });


	//$scope.iOptions = $_.setObj( $scope.iOptions, 'buddha.dharma.sangha', 'love' );


}]);

