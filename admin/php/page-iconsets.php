<script type="text/javascript">
	//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
	postworldAdmin.controller('pwIconsetsDataCtrl',
		[ '$scope', '$window', '_',
		function( $scope, $window, $_ ){

		$scope.pwRegisteredIconsets = <?php echo json_encode( pw_get_registered_iconsets() ) ?>;		
		$scope.pwRequiredIconsets = <?php echo json_encode( pw_get_required_iconsets() ) ?>;
		var pwIconsets = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_ICONSETS ) ) ) ?>;
		// If it's empty, make an object (not array)
		if( _.isEmpty( pwIconsets ) )
			pwIconsets = {};
		$scope.pwIconsets = pwIconsets;

		$scope.shortcodeAttrOptions = <?php echo json_encode( apply_filters( 'pw_icon_shortcode_attr_options', array() ) ) ?>;


	}]);
</script>

<div
	pw-admin
	pw-admin-iconsets
	pw-ui
	ng-controller="pwIconsetsDataCtrl"
	ng-cloak
	class="postworld">

	<h1>
		<i class="pwi-circle-medium"></i>
		Iconsets
	</h1>

	<hr class="thick">


	<div class="row">
		<div class="col-md-6">

			<div class="well">
				<!-- SAVE BUTTON -->
				<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_ICONSETS, 'pwIconsets'); ?></div>

				<h3><i class="pwi-check-square"></i> <?php ___('iconsets.enabled_iconsets'); ?></h3>

				<div ng-repeat="(iconKey, iconset) in ::pwRegisteredIconsets">

					<label>
						<input
							type="checkbox"
							checklist-model="pwIconsets.enabled"
							checklist-value="iconKey"
							ng-disabled="::iconsetIsRequired(iconset.slug)">
							{{ iconset.name }}
						<i ng-show="iconsetIsRequired(iconset.slug)">(Required)</i>
					</label>

				</div>

			</div>

			<div class="well">
				<h3><i class="pwi-code"></i> <?php ___('iconsets.icon_shortcode'); ?></h3>

				<div class="row">
					<div class="col-md-6 col-lg-4">
						<small><?php ___('iconsets.icon_shortcode_description'); ?></small>
						<?php
							echo pw_select_icon_options( array(
									'ng_model' => 'select.shortcodeIcon',
								)); ?>

					</div>
					<div class="col-md-6 col-lg-8">
						<div ng-show="uiBool(select.shortcodeIcon)">
							<small><?php ___('iconsets.shortcode_how_to'); ?></small>
							<input
								type="text"
								class="un-disabled"
								select-on-click
								ng-value="getIconShortcode(select.shortcodeIcon)"
								style="width:100%">

							<!-- ADDITIONAL ATTRIBUTES -->
							<div ng-show="uiBool( shortcodeAttrOptions )">
								<hr>
								<h4>Options:</h4>
								<table>
									<tr ng-repeat="(key, value) in shortcodeAttrOptions">
										<td>
											{{ value.name }}
										</td>
										<td>
											<select
												ng-options="class for class in value.classes"
												ng-model="shortcodeAtts[key]">
											</select>
										</td>
									</tr>
								</table>
							</div>

						</div>
					</div>
				</div>

			</div>

		</div>
	</div>

	<hr class="thick">

	<?php if( pw_dev_mode() ): ?>
		<div class="pw-dev well">
			<h3><i class="pwi-merkaba"></i> Dev Mode</h3>

			<div class="well">
				<h3>$scope.pwIconsets</h3>
				<pre><code>{{ pwIconsets | json }}</code></pre>
			</div>

			<div class="well">
				<h3>Registered Iconsets</h3>
				<pre><code><?php echo json_encode( pw_get_registered_iconsets(), JSON_PRETTY_PRINT ) ?></code></pre>
			</div>

			<div class="well">
				<h3>Loaded Iconsets</h3>
				<pre><code><?php echo json_encode( pw_get_iconsets(), JSON_PRETTY_PRINT ) ?></code></pre>
			</div>
			<hr>
			<div class="well">
				<li>ADD OPTION : Enable Iconset Shortcodes (select from iconsets)</li>
				<li>ADD OPTION : Clear iconset cache (delete all options)</li>
			</div>
		</div>
	<?php endif; ?>

</div>