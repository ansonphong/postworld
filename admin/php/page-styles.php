<script type="text/javascript">
	postworldAdmin.controller('pwStylesDataCtrl', [ '$scope', '$window', function( $scope, $window ){
		$scope.lang = "en";
		$scope.language = <?php global $i_style_language; echo json_encode( $i_style_language ); ?>;
		$scope.pwStyles = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_STYLES ) ) ); ?>;
		$scope.pwStyleStructure = <?php echo json_encode( apply_filters( PW_MODEL_STYLES, array() ) ); ?>;
		$scope.pwStyleDefaults = <?php echo json_encode( apply_filters( PW_OPTIONS_STYLES, array() ) ); ?>;
	}]);
</script>

<div class="postworld styles wrap" ng-cloak>
	<div
		pw-admin-style
		ng-controller="pwStylesDataCtrl">
		<h1>
			<i class="pwi-brush"></i>
			Styles
		</h1>
		<hr class="thick">

		<!-- ////////// VARIABLES ////////// -->
		<div ng-repeat="type in pwStyleStructure">

			<!-- SAVE BUTTON -->
			<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_STYLES,'pwStyles'); ?></div>

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

									<!-- EDITOR -->
									<td ng-show="showProperty( property, 'edit' )">
										{{ property.name }}
										<div class="font-nano">@{{ property.key }}</div>
									</td>
									<td
										ng-show="showProperty( property, 'edit' )"
										style="position:relative;">
										<!-- INCLUDE INCLUDE TEMPLATE : FROM /templates/admin/style-input-{{property.input}} -->
										<div
											pw-admin-style-input="property"
											input-options="options"
											input-model="pwStyles[ type.key ][ section.key ]">
											
										</div>
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
		<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_STYLES, 'pwStyles'); ?></div>


		<button ng-click="resetStyleDefaults()" class="button">Reset to Defaults</button>
		
		<?php if( pw_dev_mode() ): ?>
			<hr class="thick">
			<div class="well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<pre><code>pwStyles : {{ pwStyles | json }}</code></pre>
			</div>
		<?php endif; ?>

	</div>

</div>