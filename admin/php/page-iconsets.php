<?php

$pwIconsets = pw_get_option( array( 'option_name' => PW_OPTIONS_ICONSETS ) );
if( empty($pwIconsets) )
	$pwIconsets = array('_' => 0);

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwIconsetsDataCtrl',
	'vars' => array(
		'pwRegisteredIconsets' => pw_get_registered_iconsets(),
		'pwRequiredIconsets' => pw_get_required_iconsets(),
		'pwIconsets' => $pwIconsets,
		'shortcodeAttrOptions' => apply_filters( 'pw_icon_shortcode_attr_options', array() ),
		),
	));
?>

<?php do_action( 'postworld_admin_header' ) ?>

<div
	pw-admin
	pw-admin-iconsets
	pw-ui
	ng-controller="pwIconsetsDataCtrl"
	class="postworld wrap">

	<h1 class="primary">
		<i class="icon pwi-circle-medium"></i>
		<?php _e( 'Iconsets', 'postworld' ) ?>
	</h1>

	<hr class="thick">

	<div class="pw-cloak">

		<div class="well">
			<h3>
				<i class="pwi-code"></i>
				<?php _e( 'Icon Shortcode', 'postworld' ) ?>
			</h3>

			<div class="row">
				<div class="col-md-6 col-lg-4">
					<small>
						<?php _e( "Select an icon to get it's shortcode", 'postworld' ) ?>
					</small>
					<?php
						echo pw_select_icon_options( array(
							'ng_model' => 'select.shortcodeIcon',
							)); ?>
				</div>
				<div class="col-md-6 col-lg-8">
					<div ng-show="uiBool(select.shortcodeIcon)">
						<small>
							<?php _e( 'To use the shortcode, paste the following text into a post', 'postworld' ) ?>
						</small>
						<input
							type="text"
							class="un-disabled"
							select-on-click
							ng-value="getIconShortcode(select.shortcodeIcon)"
							style="width:100%">

						<!-- ADDITIONAL ATTRIBUTES -->
						<div ng-show="uiBool( shortcodeAttrOptions )">
							<hr>
							<h4><?php _e( 'Options', 'postworld' ) ?>:</h4>
							<table>
								<tr ng-repeat="(key, value) in shortcodeAttrOptions">
									<td>
										{{ value.name }}
									</td>
									<td>
										<select
											ng-options="class for class in value.classes"
											ng-model="shortcodeAtts[key]">
											<option value=""><?php _ex( 'None', 'option', 'postworld' ) ?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>

					</div>
				</div>
			</div>

		</div>


		<div class="well">
			<!-- SAVE BUTTON -->
			<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_ICONSETS, 'pwIconsets'); ?></div>

			<h3>
				<i class="pwi-check-square"></i>
				<?php _e( 'Enabled Iconsets', 'postworld' ) ?>
			</h3>

			<div ng-repeat="(iconKey, iconset) in ::pwRegisteredIconsets">

				<label>
					<input
						type="checkbox"
						checklist-model="pwIconsets.enabled"
						checklist-value="iconKey"
						ng-disabled="::iconsetIsRequired(iconset.slug)">
						{{ iconset.name }}
					<i ng-show="iconsetIsRequired(iconset.slug)">(<?php _e( 'Required', 'postworld' ) ?>)</i>
				</label>

			</div>

		</div>


	</div>

	<?php if( pw_dev_mode() ): ?>
		<div class="pw-dev well">
			<h3>
				<i class="pwi-merkaba"></i>
				<?php _e( 'Development Mode', 'postworld' ) ?>
			</h3>

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