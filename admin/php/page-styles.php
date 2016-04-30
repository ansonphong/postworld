<?php
pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwStylesDataCtrl',
	'vars' => array(
		'pwStyles' => pw_get_option( array( 'option_name' => PW_OPTIONS_STYLES ) ),
		'pwStyleStructure' => apply_filters( PW_MODEL_STYLES, array() ),
		'pwStyleDefaults' => apply_filters( PW_STYLES_DEFAULT, array() ),
		),
	));
?>

<?php do_action( 'postworld_admin_header' ) ?>

<div class="postworld styles wrap">
	<div
		pw-admin-style
		ng-controller="pwStylesDataCtrl">
		<h1 class="primary">
			<i class="icon pwi-brush"></i>
			<?php _ex('Styles','module','postworld') ?>
		</h1>
		<hr class="thick">

		<div class="pw-cloak">

			<!-- ////////// VARIABLES ////////// -->
			<div class="well" ng-repeat="type in pwStyleStructure">

				<!-- SAVE BUTTON -->
				<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_STYLES,'pwStyles'); ?></div>

				<h4>
					<i class="icon {{ type.icon }}"></i>
					{{ type.name }}
				</h4>

				<table class="form-table pad" style="margin-top:15px;">
					<tr ng-repeat="section in type.values"
						valign="top"
						class="module layout">
						<th scope="row">
							<h5>
								<i class="icon {{ section.icon }}"></i>
								{{ section.name }}
							</h5>
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
				
			</div>

			<!-- ////////// END VARIABLES ////////// -->

			<!-- SAVE BUTTON -->
			<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_STYLES, 'pwStyles'); ?></div>

			<!--<button ng-click="resetStyleDefaults()" class="button">Reset to Defaults</button>-->
			
			<?php if( pw_dev_mode() ): ?>
				<hr class="thick">
				<div class="well">
					<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
					<pre><code>pwStyles : {{ pwStyles | json }}</code></pre>
				</div>
			<?php endif; ?>

		</div>

	</div>

</div>