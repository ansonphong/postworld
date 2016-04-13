<script type="text/javascript">
	//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
	postworldAdmin.controller( 'layoutDataCtrl',
		[ '$scope', '$window', '$parse',
		function($scope, $window, $parse ){
			$scope.pwLayoutOptions = <?php echo json_encode( pw_layout_options() ); ?>;
			$scope.pwSidebars = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) ) ); ?>;
			$scope.pwTemplates = <?php echo json_encode( pw_get_templates( array( 'ext' => 'php', 'type' => 'dir' ) ) ); ?>;
			$scope.pwLayouts = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) ) ); ?>;
	}]);
</script>

<div class="layout wrap postworld" ng-cloak>
	<h1>
		<i class="pwi-th-large"></i>
		<?php _e( 'Layouts', 'postworld' ) ?>
	</h1>
	<hr class="thick">
	<div
		pw-admin-layout
		ng-controller="layoutDataCtrl"
		ng-cloak>

		<table class="form-table">
			<tr ng-repeat="context in pwLayoutOptions.contexts"
				ng-class="context.name"
				valign="top" class="module layout context">
				<th scope="row">
					<span
						uib-tooltip="{{context.name}}"
						tooltip-popup-delay="333">
						<i class="{{context.icon}}"></i>
						{{context.label}}
						</th>
					</span>
				<td>
					<!-- SAVE BUTTON -->
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_LAYOUTS, 'pwLayouts'); ?></div>

					<?php
						echo pw_layout_single_options( array( 'context'	=>	'siteAdmin' ) );
					?>

				</td>
			</tr>
		</table>

		<?php if( pw_dev_mode() ) : ?>
			<hr class="thick">
			<div class="pw-dev well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<div class="well">
					<h3>$scope.pwLayouts</h3>
					<pre><code>{{ pwLayouts | json }}</code></pre>
				</div>

				<div class="well">
					<h3>$scope.pwLayoutOptions</h3>
					<pre><code>{{ pwLayoutOptions | json }}</code></pre>
				</div>
			</div>
		<?php endif; ?>

	</div>
</div>