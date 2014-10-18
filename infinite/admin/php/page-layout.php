<script type="text/javascript">
	//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
	infinite.controller( 'layoutDataCtrl',
		[ '$scope', '$window', '$parse', 'iData',
		function($scope, $window, $parse, iData){
			$scope.iLayoutOptions = <?php echo json_encode( i_layout_options() ); ?>;
			$scope.iSidebars = <?php echo json_encode( i_get_option( array( 'option_name' => 'i-sidebars' ) ) ); ?>;
			$scope.iTemplates = <?php echo json_encode( pw_get_templates( array( 'ext' => 'php', 'type' => 'dir' ) ) ); ?>;
			$scope.iLayouts = <?php echo json_encode( i_get_option( array( 'option_name' => 'i-layouts' ) ) ); ?>;
	}]);
</script>

<div id="infinite_admin" ng-app="infinite" class="layout postworld">
	<h1>
		<i class="icon-th-large"></i>
		Layouts
	</h1>
	<div
		i-admin-layout
		ng-controller="layoutDataCtrl"
		ng-cloak>

		<table class="form-table">
			<tr ng-repeat="context in iLayoutOptions.contexts"
				ng-class="context.name"
				valign="top" class="module layout context">
				<th scope="row"><i class="{{context.icon}}"></i> {{context.label}}</th>
				<td>
					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button('i-layouts','iLayouts'); ?></div>

					<?php
						echo i_layout_single_options( array( 'context'	=>	'siteAdmin' ) );
						//echo i_ob_include_template( 'admin/modules/layout-single.php', $vars );
					?>

				</td>
			</tr>
		</table>

		<hr class="thick">

		<!--
		iLayoutOptions : <pre>{{ iLayoutOptions | json }}</pre>
		iLayouts : <pre>{{ iLayouts | json }}</pre>
		iGlobals : <pre><?php echo htmlentities( json_encode( iGlobals(), JSON_PRETTY_PRINT ) ); ?></pre>
		-->

	</div>
</div>