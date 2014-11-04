<?
	$iStyles = pw_get_option( array( 'option_name' => PW_OPTIONS_STYLES ) );
	$i_styles_structure = apply_filters( PW_MODEL_STYLES, array() );
?>
<div ng-app="infinite" class="postworld styles wrap">
	<script type="text/javascript">
		//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
		infinite.controller('iStylesDataCtrl', [ '$scope', '$window', function( $scope, $window ){
			$scope.lang = "en";
			// Print Data
			$scope.language = <?php global $i_style_language; echo json_encode( $i_style_language ); ?>;
			$scope.iStyles = <?php echo json_encode( $iStyles ); ?>;
			$scope.iStyleStructure = <?php echo json_encode( $i_styles_structure ); ?>;
		}]);
	</script>
	<div
		i-admin-style
		ng-controller="iStylesDataCtrl"
		ng-cloak>

		<h1>
			<i class="icon-brush"></i>
			Styles
		</h1>

		<hr class="thick">


		<!--<pre>{{ iStyleStructure | json }}</pre>-->

		<!-- ////////// VARIABLES ////////// -->

		<div ng-repeat="type in iStyleStructure">

			<!-- SAVE BUTTON -->
			<div class="save-right"><?php i_save_option_button( PW_OPTIONS_STYLES,'iStyles'); ?></div>

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
										
										<!-- COLOR -->
										<div
											class="inner-right color-box"
											ng-show="showProperty( property, 'edit-color' )"
											ng-style="backgroundColor( iStyles[ type.key ][ section.key ][ property.key ] )">
											
										</div>

										<!-- ICON -->
										<div
											class="inner-right inner-icon"
											ng-show="property.icon">
											<i ng-class="property.icon"></i>
										</div>

										<!-- TEXT -->
										<input
											type="text"
											ng-model="iStyles[ type.key ][ section.key ][ property.key ]">

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

		</div>

		
		<!-- ////////// END VARIABLES ////////// -->

		<hr class="thick">
		
		<!-- SAVE BUTTON -->
		<div class="save-right"><?php i_save_option_button( PW_OPTIONS_STYLES, 'iStyles'); ?></div>


		<button ng-click="resetDefaults()" class="button">Reset to Defaults</button>
		
		<hr class="thick">
		<pre>iStyles : {{ iStyles | json }}</pre>

	</div>

</div>