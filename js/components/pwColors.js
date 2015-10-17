postworld.factory( '$pwColors', [ '$pw', '_', function( $pw, $_ ){
	return{
		getColorByTag: function( post, profile, tag ){
			var colors = post.image.colors[profile]['colors'];
			var index;
			switch( tag ){
				case 'first':
					index = 0;
					return colors[index]
					break;
				case 'second':
					index = ( colors.length > 2 ) ? 1 : 0;
					return colors[index]
					break;
				case 'second-last':
					index = ( colors.length > 2 ) ? colors.length - 2 : colors.length - 1;
					return colors[index]
					break;
				case 'last':
					index = colors.length - 1;
					return colors[index];
					break;
			}

			return false;

		},
	}
}]);

postworld.directive('pwColors', [ '$pw', '_', '$pwColors', function( $pw, $_, $pwColors ){
	return{
		link: function( $scope, $element, $attrs ){
			$scope.getHex = function( post, profile, tag ){
				if( _.isEmpty( post ) || _.isNull( profile ) )
					return false;
				return $pwColors.getColorByTag( post, profile, tag ).hex;
			}
			$scope.getRGBA = function( post, profile, tag, alpha ){
				if( _.isEmpty( post ) || _.isNull( profile ) )
					return false;
				var color = $pwColors.getColorByTag( post, profile, tag );
				var rgba = color.rgb;
				rgba[3] = alpha;
				return 'rgba('+rgba+')';

			}

		}
	}

}]);