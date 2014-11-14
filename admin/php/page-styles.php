<script type="text/javascript">
	postworldAdmin.controller('pwStylesDataCtrl', [ '$scope', '$window', function( $scope, $window ){
		$scope.lang = "en";
		$scope.language = <?php global $i_style_language; echo json_encode( $i_style_language ); ?>;
		$scope.pwStyles = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_STYLES ) ) ); ?>;
		$scope.pwStyleStructure = <?php echo json_encode( apply_filters( PW_MODEL_STYLES, array() ) ); ?>;
		$scope.pwStyleDefaults = <?php echo json_encode( apply_filters( PW_OPTIONS_STYLES, array() ) ); ?>;
	}]);
</script>

<div ng-app="postworldAdmin" class="postworld styles wrap">
	<div
		pw-admin-style
		ng-controller="pwStylesDataCtrl">

		<h1>
			<i class="icon-brush"></i>
			Styles
		</h1>

		<hr class="thick">


		<!--<pre>{{ pwStyleStructure | json }}</pre>-->

		<!-- ////////// VARIABLES ////////// -->

		<div ng-repeat="type in pwStyleStructure">

			<!-- SAVE BUTTON -->
			<div class="save-right"><?php i_save_option_button( PW_OPTIONS_STYLES,'pwStyles'); ?></div>

			<h2>
				<i class="{{ type.icon }}"></i>
				{{ type.name }}
			</h2>

			<table class="form-table pad">
				<tr ng-repeat="section in type.values"
					valign="top"
					class="module layout">
					<th scope="row">
						<h3>
							<i class="{{ section.icon }}"></i>
							{{ section.name }}
						</h3>
					</th>
					<td class="align-top">
						
						<!-- PROPERTIES -->
						<div style="margin-top:10px;">
							
							<table>
							
								<tr ng-repeat="property in section.values">

									<!-- PROPERTY VALUE ISN'T REGISTERING ON NG-REPEAT??? -->

									<!-- EDITOR -->
									<td ng-show="showProperty( property, 'edit' )">
										{{ property.name }}
										<div class="font-nano">@{{ property.key }}</div>
									</td>
									<td ng-show="showProperty( property, 'edit' )" style="position:relative;">
										
										<!--
											TODO : 
											Here what we want to do is
											use a custom directive to ng-include
											a transcluded scope templates
											for each of the different input types.
										-->

										<!-- COLOR -->
										<div
											class="inner-right color-box"
											ng-show="showProperty( property, 'edit-color' )"
											ng-style="backgroundColor( pwStyles[ type.key ][ section.key ][ property.key ] )">
										</div>

										<!-- ICON -->
										<div
											class="inner-right inner-icon"
											ng-show="property.icon">
											<i ng-class="property.icon"></i>
										</div>

										<!-- TEXT -->
										<input
											ng-show="property.input == 'text' || property.input == 'color'"
											type="text"
											ng-model="pwStyles[ type.key ][ section.key ][ property.key ]">

										<!-- SELECT -->
										<select
											ng-show="property.input == 'select'"
											ng-options="value for value in {{ property.ng_options }}"
											ng-model="pwStyles[ type.key ][ section.key ][ property.key ]">
											
										</select>
										
									</td>
									<td ng-show="showProperty( property, 'edit' )">
										<div class="font-micro">
											{{ property.description }}
										</div>
									</td>
		
									<!-- SPACER -->
									<td
										ng-show="showProperty( property, 'space' )"
										colspan="3">
										<div style="height:20px;"></div>
									</td>

									<!-- LINE -->
									<td
										ng-show="showProperty( property, 'line' )"
										colspan="3">
										<hr>
									</td>

								</tr>

							</table>
						</div>
						
					</td>
				</tr>
			</table>

			<hr class="thick">

		</div>

		<!-- ////////// END VARIABLES ////////// -->

		<!-- SAVE BUTTON -->
		<div class="save-right"><?php i_save_option_button( PW_OPTIONS_STYLES, 'pwStyles'); ?></div>


		<button ng-click="resetStyleDefaults()" class="button">Reset to Defaults</button>
		
		<hr class="thick">
		<pre>pwStyles : {{ pwStyles | json }}</pre>

	</div>

</div>