<script>
	postworldAdmin.directive('pwAdminSliderOptions', function( $log, $pwPostOptions, $_ ){
		return{
			link: function( $scope, element, attrs ){
				// Get tax outline by AJAX
				$pwPostOptions.taxTerms( $scope, 'tax_terms' );
				// Watch value : pwMeta.header.slider.mode
				$scope.$watch('<?php echo $vars["ng_model"] ?>.mode', function(value){
					// Switch Query Vars
					switch( value ){
						case 'this_post':
							$scope.<?php echo $vars["ng_model"] ?>.query_vars.this_post = true;
							$scope.<?php echo $vars["ng_model"] ?>.query_vars.this_post_only = true;
							break;
						case 'query':
							$scope.<?php echo $vars["ng_model"] ?>.query_vars.this_post_only = false;
							break;
					}
				});
			}
		}
	});
</script>