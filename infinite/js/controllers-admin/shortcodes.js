/*____  _                _                _           
 / ___|| |__   ___  _ __| |_ ___ ___   __| | ___  ___ 
 \___ \| '_ \ / _ \| '__| __/ __/ _ \ / _` |/ _ \/ __|
  ___) | | | | (_) | |  | || (_| (_) | (_| |  __/\__ \
 |____/|_| |_|\___/|_|   \__\___\___/ \__,_|\___||___/
                                                      
//////////////////////////////////////////////////////*/

postworldAdmin.directive( 'pwAdminShortcodes', [ function(){
    return { 
        controller: 'pwAdminShortcodesCtrl',
        link:function( scope, element, attrs ){
        	// Add Module Class
        	element.addClass('pw-admin-shortcodes');
        }
    };
}]);

postworldAdmin.controller('pwAdminShortcodesCtrl',
	[ '$scope', '$log', '$window', '$parse', 'iData', 'pwData', '_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $iData, $pwData, $_, $pwPostOptions ) {
	
	$scope.newShortcode = function(){
		var newShortcode = {
			'name': 			'New Shortcode',
			'id': 				'shortcode-' + $_.randomString( 8, ['numbers','lowercase'] ),
			'type': 			'self-enclosing', // enclosing / self-inclosing
			'description': 		'',
			'before_content': 	'<div>',
			'after_content': 	'</div>',
			'content': 			'<div></div>',
		};

		$scope.pwShortcodeSnippets.push( newShortcode );
		$scope.selectItem( newShortcode );
	}

	$scope.removeShortcode = function(shortcode){
		var updatedShortcodes = [];
		angular.forEach( $scope.pwShortcodeSnippets, function( value ){
			if( sidebar != value ){
				updatedShortcodes.push(value);
			}
		});
		$scope.pwShortcodeSnippets = updatedShortcodes;
	}

	$scope.generateShortcode = function( item ){
		return "[pw-shortcode id='" + item.id + "']";
	}

	
}]);
