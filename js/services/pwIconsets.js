///// SERVICE /////
postworld.factory( 'pwIconsets', [ '$pw', '_', function( $pw, $_ ){
	return {
		get: function( iconset ){
			// Returns the iconsets object, and if a key is specified as 'iconset', just that key
			if( _.isUndefined( iconset ) )
				return $pw.iconsets;
			else
				return $_.get( $pw.iconsets, iconset );
		},
		array: function(){
			// Returns the iconsets as a flat array, not associative array
			var array = [];
			angular.forEach( $pw.iconsets, function( value, key ){
				array.push( value );
			});
			return array;
		},
	};
}]);
