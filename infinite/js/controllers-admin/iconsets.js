/*___                         _       
 |_ _|___ ___  _ __  ___  ___| |_ ___ 
  | |/ __/ _ \| '_ \/ __|/ _ \ __/ __|
  | | (_| (_) | | | \__ \  __/ |_\__ \
 |___\___\___/|_| |_|___/\___|\__|___/
                                      
//////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminIconsets', [ function(){
    return { 
        controller: 'pwAdminIconsetsCtrl',
        link:function( $scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-iconsets');
        }
    };
}]);

postworldAdmin.controller( 'pwAdminIconsetsCtrl',
	[ '$scope', '$log', '$window', '$parse', 'iData', 'pwData', '_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $iData, $pwData, $_, $pwPostOptions ) {
	
	
	

	
}]);
