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

		/**
		 * Selects a particular percentage point from a profile of colors.
		 */
		getColorPercentage: function( percentage, colorProfile ){



		},

		/**
		 * Parse color functions embedded in property values.
		 * hex(profile.50), rgb(profile.50), rgba(profile.50,.8) 
		 */
		parseColorFunctions: function( propertyInner ){
			
			console.log( 'parseColorFunctions(propertyInner)', propertyInner );

			function hex(){
				return 777;
			}
			function rgba(){
				return 888;
			}

			return eval(propertyInner);

		},

		/**
		 * Parse a single property value, and replace instances
		 * Of content appearing between {{ }}
		 */
		parsePropertyValue: function( propertyValue, colorProfiles ){

			//var parseColorFunctions = this.parseColorFunctions;

			var newPropertyValue = propertyValue.replace( /\{\{(.*?)\}\}/g, function(x){  // this grabs replacement tags
				// Remove the curly brackets
				x = x.replace( '{{', '' ).replace('}}','');
				console.log( 'REGEX', x );

				function hex( val ){
					return '#fff';
				}

				function rgba(){
					return 'rgba(255,255,255,1)';
				}
				
				var evalOutput = eval( x );

				console.log( 'EVAL', evalOutput );
				
				return evalOutput; // x.replace( /\[(\d+)\]/g,'.$1' );  // this replaces array indexers
			});

			return newPropertyValue;// '#fff';

		},

		/**
		 * Parse a set of style definitions. 
		 */
		parseStyles: function( styleObj, colorProfiles ){
			var styles = "";
			var parsePropertyValue = this.parsePropertyValue;
			angular.forEach( styleObj, function( properties, selector ){
				styles += selector + "{";
				angular.forEach( properties, function( value, property ){
					styles += property + ":" + parsePropertyValue( value, colorProfiles ) + ";";
				});
				styles += "} ";
			});
			return styles;
		},

		/**
		 * Return style definitions prepared to be output to browser.
		 */
		outputStyles: function( styleObj, colorProfiles ){
			return "<style>" + this.parseStyles(styleObj,colorProfiles) + "</style>";
		}

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

			$scope.outputStyles = function( styleObj, colorProfiles ){
				return $pwColors.outputStyles( styleObj, colorProfiles );
			}

		}
	}

}]);