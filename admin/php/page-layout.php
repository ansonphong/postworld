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

<div class="layout wrap postworld" ng-cloak>
	<h1>
		<i class="pwi-th-large"></i>
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
						echo pw_layout_single_options( array( 'context'	=>	'siteAdmin' ) );
						//echo i_ob_include_template( 'admin/modules/layout-single.php', $vars );
					?>

				</td>
			</tr>
		</table>

		<?php if( pw_dev_mode() ) : ?>
			<hr class="thick">
			<div class="pw-dev well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<div class="well">
					<h3>$scope.iLayouts</h3>
					<pre><code>{{ iLayouts | json }}</code></pre>
				</div>

				<div class="well">
					<h3>$scope.iLayoutOptions</h3>
					<pre><code>{{ iLayoutOptions | json }}</code></pre>
				</div>
			</div>
		<?php endif; ?>

	</div>
</div>