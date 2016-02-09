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
	[ '$scope', '$log', '$window', '$parse', '$pwData', '$_', 'pwPostOptions',
	function ( $scope, $log, $window, $parse, $pwData, $_, $pwPostOptions ) {
	
	$scope.view = 'settings';

	$scope.newShortcode = function(){
		var newShortcode = {
			'name': 			'New Shortcode',
			'id': 				'shortcode-' + $_.randomString( 4, ['numbers','lowercase'] ),
			'type': 			'self-enclosing', // enclosing / self-enclosing
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
			if( sidebar != value )
				updatedShortcodes.push(value);
		});
		$scope.pwShortcodeSnippets = updatedShortcodes;
	}

	$scope.generateShortcode = function( item ){
		if( item.type == 'self-enclosing' )
			return "[" + item.id + "]";
		if( item.type == 'enclosing' )
			return "[" + item.id + "]" + "Content here." + "[/" + item.id + "]";
	}

}]);
