<?php
	// Load Globals
	global $iAdmin;
	// Define Variables
	$post_types = get_post_types( array( "public" => true ), 'names' );
	//echo json_encode($post_types);
	// Get Sidebars
	$I_Sidebars = new I_Sidebars();
	$i_sidebars = (array) $I_Sidebars->get_sidebars();

	if( $i_sidebars[0] == false )
		$i_sidebars = array();


	$vars = array(
		'ng_model' => "iLayouts[layout.name]",
		);
	


?>

<div id="infinite_admin" ng-app="infinite" class="layout postworld">
	<h1>
		<i class="icon-th-large"></i>
		Layouts
	</h1>
	<script type="text/javascript">
		//////////////////// LAYOUT VIEW CONTROLLER ////////////////////

		infinite.controller( 'layoutDataCtrl',
			[ '$scope', '$window', '$parse', 'iData',
			function($scope, $window, $parse, iData){

				//$scope.iAdmin = <?php echo json_encode($iAdmin); ?>;
				$scope.iLayoutOptions = <?php echo json_encode( i_layout_options() ); ?>;
				$scope.i_sidebars = <?php echo json_encode($i_sidebars); ?>;
				$scope.i_templates = <?php echo json_encode( i_get_templates() ); ?>;
				// Load Previously Saved Settings Object
				$scope.iLayouts = <?php
					$layout_options = get_option("i-layouts");
					if( $layout_options == false )
						$layout_options = "{}";
					echo $layout_options;
					?>;

		}]);

	</script>

	<div
		i-admin-layout
		ng-controller="layoutDataCtrl"
		ng-cloak>

		<table class="form-table">
			<tr ng-repeat="layout in iLayoutOptions.contexts"
				ng-class="layout.name"
				valign="top" class="module layout">
				<th scope="row"><i class="{{layout.icon}}"></i> {{layout.label}}</th>
				<td>
					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button('i-layouts','iLayouts'); ?></div>

					<?php
						echo i_ob_include_template( 'admin/modules/layout-single.php', $vars );
					?>

				</td>
			</tr>
		</table>

		<hr class="thick">

		<!--
		{{dataModel}} // 
		-->
		<!--
		<pre>{{ i_templates | json }}</pre>
		<hr>
		-->

		<!--
		iLayoutOptions : <pre>{{ iLayoutOptions | json }}</pre>
		iLayouts : <pre>{{ iLayouts | json }}</pre>
		iGlobals : <pre><?php echo htmlentities( json_encode( iGlobals(), JSON_PRETTY_PRINT ) ); ?></pre>
		-->

	</div>
</div>