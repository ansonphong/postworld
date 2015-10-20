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
		 * Parse a single property value, and replace instances
		 * Of content appearing between {{ }}
		 */
		parsePropertyValue: function( propertyValue, colorProfiles ){
			/**
			 * Get a percentage through an array of colors.
			 * Percentage is parsed as 0%, the first array index,
			 * 100% the last array index and everything in between.
			 *
			 * @param [integer] percentage (0-100) Percent through the array to return. 
			 * @param [array] colors An array of processed color objects.
			 */
			var getColorPercentageFromProfile = function( percentage, colors ){
				var colorCount = colors.length;
				var decimal = percentage / 100;
				var index = Math.floor( decimal * (colorCount-1) );
				return colors[index];
			}
			/**
			 * Gets a color from a profile where the format is provided
			 * Period deliniated, where the first value is the profile key
			 * And the second value is the percentage through that profile.
			 *
			 * @param string profilePercentage Example. "dynamic.100"
			 * @return object Processed color object, with hex, rgb, and hsl keys.
			 */
			var getColorFromProfile = function( profilePercentage ){
				var matches = profilePercentage.split('.');
				var key = matches[0];
				var colorProfile = colorProfiles[key]
				return getColorPercentageFromProfile( parseInt(matches[1]), colorProfile.colors );
			}
			/**
			 * Parse various color functions to return color values.
			 * These child functions are used in the special synatax
			 * used by dynamic style definitions.
			 */
			var parseColorFunctions = function( content ){

				function hex( profilePercentage ){
					var c = getColorFromProfile( profilePercentage );
					return c['hex'];
				}

				function rgba( profilePercentage, alpha ){
					var c = getColorFromProfile( profilePercentage );
					return 'rgba('+c.rgb[0]+','+c.rgb[1]+','+c.rgb[2]+','+alpha+')';
				}

				return eval( content );

			}
			// Process the contents of each double curly braces
			var newPropertyValue = propertyValue.replace( /\{\{(.*?)\}\}/g, function(content){
				// Remove the curly brackets
				content = content.replace( '{{', '' ).replace('}}','');

				// Check if it contains a function
				var checkFunction = /\(([^)]+)\)/;
				var matches = checkFunction.exec(content);
				var hasFunction = ( matches != null );

				// Logging
				console.log( 'matches', matches );
				console.log( 'hasFunction', hasFunction );

				// If it has a function, package and process it with parseColorFunctions()
				if( hasFunction ){
					content = parseColorFunctions( content );
				}
				// Otherwise, examine the contents of the content, and do otherwise (?)
				return content;
			});
			return newPropertyValue;
		},
		/**
		 * Parse a set of style definitions. 
		 */
		/* @example styleObj
			{
				".icon": {
					color: "{{ hex('dynamic.100') }}",
					background: "{{ rgba('dynamic.0') }}",
					test:"{{ dynamic.50 }}"
				},
			}
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
		outputStyles: function( styleObj, colorProfiles, styleTags ){
			if( _.isNull( styleTags ) )
				styleTags = true;
			if( styleTags )
				return "<style>" + this.parseStyles(styleObj,colorProfiles) + "</style>";
			else
				return this.parseStyles(styleObj,colorProfiles);
		}
	}
}]);

postworld.directive('pwColors',
	[ '$pw', '_', '$pwColors', '$log',
	function( $pw, $_, $pwColors, $log ){
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
			
			/**
			 * For use with ng-mouseover, passing $event
			 * @example ng-mouseover="applyColorStyles($event)"
			 */
			/*
			$scope.applyColorStyles = function( $event ){
				$log.debug( 'parseColorStyles.srcElement', $event.srcElement );
				var elem = angular.element($event.srcElement);
				elem.css( 'background', '#fff' );
			}
			*/

			/**
			 * For use on style or div element using pw-compile-code directive.
			 * For development purposes only to observe the output of styles.
			 * For production purses, use pwStyleColors directive. 
			 *
			 * @example <div pw-compile-code="outputStyles(styleObj,colorProfiles)"></div>
			 */
			$scope.outputStyles = function( styleObj, colorProfiles ){
				return $pwColors.outputStyles( styleObj, colorProfiles, false );
			}
		}
	}
}]);


/**
 * Generates and injects dynaimc style definitions
 * For adjusting style colors based on post color profiles.
 *
 * @param object|expression pwStyleColors (Required) A Postworld color styles object
 * 	@example See $pwcolors.parseStyles
 * @param object|expression colorProfiles (Required) Link to color profiles, such as `post.image.colors`
 * @param boolean|expression color-profiles-enable (Optional) Default true. A boolean value, whether or not to enable color output.
 * @param none color-profiles-dynamic If this is present, watch for changes in color profiles.
 */
postworld.directive('pwStyleColors', [ '$pw', '_', '$pwColors', '$log', function( $pw, $_, $pwColors, $log ){
	return{
		scope: {
			'styleObj': '=pwStyleColors',
			'colorProfiles': '='
		},
		link: function( $scope, $element, $attrs ){
	
			/**
			 * Parse styles, with or without enclosing style tags
			 * Depending on if the current element is a style tag.
			 */
			var parseStyles = function(){
				var styleTags = !( $element[0].tagName == 'STYLE' );
				return $pwColors.outputStyles( $scope.styleObj, $scope.colorProfiles, styleTags );
			}

			var updateStyles = function(){
				$element.html( parseStyles() );
			}

			updateStyles();

			/**
			 * If color-profiles-dynamic attribute is found
			 * Then keep a watch on changes in the colorProfiles object for changes
			 */
			if( !_.isUndefined( $attrs.colorProfilesDynamic ) ){
				$scope.$watch( 'colorProfiles', function(newVal, oldVal){
					updateStyles();
				});
			}

		}
	}
}]);
