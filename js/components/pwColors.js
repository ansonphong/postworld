postworld.factory( '$pwColors', [ '$pw', '$_', function( $pw, $_ ){
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
	var getColorFromProfiles = function( colorProfiles, profilePercentage ){
		var matches = profilePercentage.split('.');
		var key = matches[0];
		var colorProfile = colorProfiles[key]
		return getColorPercentageFromProfile( parseInt(matches[1]), colorProfile.colors );
	}


	return{

		/**
		 * Public alias for the internal function.
		 */
		getColorFromProfiles: function(colorProfiles, profilePercentage){
			return getColorFromProfiles(colorProfiles, profilePercentage);
		},

		/**
		 * Parse a single property value, and replace instances
		 * Of content appearing between {{ }}
		 */
		parsePropertyValue: function( propertyValue, colorProfiles ){
			
			/**
			 * Parse various color functions to return color values.
			 * These child functions are used in the special synatax
			 * used by dynamic style definitions.
			 */
			var parseColorFunctions = function( content ){

				function hex( profilePercentage ){
					var c = getColorFromProfiles( colorProfiles, profilePercentage );
					return c['hex'];
				}

				function rgba( profilePercentage, alpha ){
					var c = getColorFromProfiles( colorProfiles, profilePercentage );
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
				//console.log( 'matches', matches );
				//console.log( 'hasFunction', hasFunction );

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

/**
 * @ngdoc directive
 * @name postworld.directive:pwColors
 *
 * @description Makes color profile functions available in local scope.
 * @param colorsEnable [expression] Optional. Default: true. Boolean, whether or not to enable color functions.
 */
postworld.directive('pwColors',
	[ '$pw', '$_', '$pwColors', '$log',
	function( $pw, $_, $pwColors, $log ){
	return{
		link: function( $scope, $element, $attrs ){

			/**
			 * Define if colors are enabled, from within the $pw.options object.
			 */
			$scope.colorsEnable = ( !_.isUndefined( $attrs.colorsEnableOption ) ) ?
				$_.get( $pw.options, $attrs.colorsEnableOption ) : true;
			
			var getColorProfilesFromPost = function( post ){
				return $_.get( post, 'image.colors' );
			} 

			/**
			 * For use with ng-style directive.
			 * @example ng-style="{background:getHex(post,'dynamic.75')}"
			 */
			$scope.getHex = function( post, profilePercentage ){
				if( !$scope.colorsEnable ) return false;
				if( _.isEmpty( post ) || _.isNull( profilePercentage ) )
					return false;
				var colorProfiles = getColorProfilesFromPost(post);
				if( colorProfiles == false ) return false;

				return $pwColors.getColorFromProfiles(colorProfiles,profilePercentage).hex;
			}
			$scope.getRGBA = function( post, profilePercentage, alpha ){
				if( !$scope.colorsEnable ) return false;
				if( _.isEmpty( post ) || _.isNull( profilePercentage ) )
					return false;
				var colorProfiles = getColorProfilesFromPost(post);
				if( colorProfiles == false ) return false;

				var color = $pwColors.getColorFromProfiles(colorProfiles,profilePercentage);
				var rgba = color.rgb;
				rgba[3] = alpha;
				return 'rgba('+rgba[0]+','+rgba[1]+','+rgba[2]+','+rgba[3]+')';
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
 * @ngdoc directive
 * @name postworld.directive:pwStyleColors
 *
 * @description Generates and injects dynamic style definitions
 * 	for adjusting style colors based on post color profiles.
 *
 * @param {object|expression} pwStyleColors (Required) A Postworld color styles object
 * 	@example See $pwcolors.parseStyles
 * @param {object|expression} colorProfiles (Required) Link to color profiles, such as `post.image.colors`
 * @param {boolean|expression} colorProfilesEnable (Optional) Default true. A boolean value, whether or not to enable color output.
 * @param {none} color-profiles-dynamic If this is present, watch for changes in color profiles.
 */
postworld.directive('pwStyleColors', [ '$pw', '$_', '$pwColors', '$log', function( $pw, $_, $pwColors, $log ){
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
				// If not enabled, empty styles, return early.
				if( !_.isUndefined( $attrs.colorProfilesEnable ) ){
					var enable = $scope.$parent.$eval( $attrs.colorProfilesEnable );
					if( !enable ){
						$element.html( '' );
						return false;
					}
				}
				// Otherwise, parse the styles and output them into HTML
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
