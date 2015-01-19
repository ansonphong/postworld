<script type="text/javascript">
	//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
	postworldAdmin.controller( 'layoutDataCtrl',
		[ '$scope', '$window', '$parse', 'iData',
		function($scope, $window, $parse, iData){
			$scope.iLayoutOptions = <?php echo json_encode( i_layout_options() ); ?>;
			$scope.iSidebars = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) ) ); ?>;
			$scope.iTemplates = <?php echo json_encode( pw_get_templates( array( 'ext' => 'php', 'type' => 'dir' ) ) ); ?>;
			$scope.iLayouts = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) ) ); ?>;
	}]);
</script>

<div ng-app="postworldAdmin" class="layout wrap postworld" ng-cloak>
	<h1>
		<i class="icon-th-large"></i>
		Layouts
	</h1>
	<hr class="thick">
	<div
		pw-admin-layout
		ng-controller="layoutDataCtrl"
		ng-cloak>

		<table class="form-table">
			<tr ng-repeat="context in iLayoutOptions.contexts"
				ng-class="context.name"
				valign="top" class="module layout context">
				<th scope="row">
					<span
						tooltip="{{context.name}}"
						tooltip-popup-delay="333">
						<i class="{{context.icon}}"></i>
						{{context.label}}
						</th>
					</span>
				<td>
					<!-- SAVE BUTTON -->
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_LAYOUTS, 'iLayouts'); ?></div>

					<?php
						echo i_layout_single_options( array( 'context'	=>	'siteAdmin' ) );
						//echo i_ob_include_template( 'admin/modules/layout-single.php', $vars );
					?>

				</td>
			</tr>
		</table>

		<hr class="thick">

		<!--
		iLayouts : <pre>{{ iLayouts | json }}</pre>
		iLayoutOptions : <pre>{{ iLayoutOptions | json }}</pre>
		iGlobals : <pre><?php echo htmlentities( json_encode( iGlobals(), JSON_PRETTY_PRINT ) ); ?></pre>
		-->

	</div>
</div>