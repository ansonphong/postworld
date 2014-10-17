<div id="infinite_admin" ng-app="infinite" class="layout postworld">
	<h1>
		<i class="icon-th-large"></i>
		<?php echo $theme_admin['layout']['page_title']; ?>
	</h1>
		
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
	?>

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

					<!-- DROPDOWN -->
					<div
						class="select-layout dropdown dropdown-layouts pull-left">
						<!-- SELECTED ITEM -->
						<span
							dropdown-toggle
							class="area area-select">
							<img
								ng-src="{{ selectedLayout( iLayouts[layout.name].layout ).image }}"
								style="width:45px; height: auto;">
								<label>{{ selectedLayout( iLayouts[layout.name].layout ).label }}</label>
						</span>
						<!-- MENU -->
						<ul class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >
							
							<!-- DEFAULT OPTION -->
							<label ng-repeat="option in iLayoutOptions.formats.default"
								ng-hide="layout.name == 'default'"
								class="radio_image_select">
								<input ng-model="iLayouts[layout.name].layout"
									name="{{ layout.name }}"
									value="{{ option.slug }}"
									type="radio" />
								<img ng-src="{{ option.image }}" title="Default" tooltip="Default" tooltip-popup-delay="200">
							</label>

							<!-- LAYOUT FORMATING OPTIONS -->
							<label class="radio_image_select"
								ng-repeat="option in iLayoutOptions.formats.options">
								<input ng-model="iLayouts[layout.name].layout"
									name="{{ layout.name }}"
									value="{{ option.slug }}"
									type="radio" />
								<img ng-src="{{ option.image }}" title="{{ option.label }}" width="90" height="60" tooltip="{{ option.label }}" tooltip-popup-delay="200">
							</label>
						</ul>
					</div>

					<div class="area header-footer pull-left" ng-show="showModule('headerFooter', layout.name)">
						<!-- HEADER -->
						<div>
							<label><b>Header</b></label>
							<select
									ng-model="iLayouts[layout.name].header.id"
									ng-options="key as key for (key, value) in i_templates.header">
									<option value="">Default</option>
							</select>
						</div>
						<!-- FOOTER -->
						<div>
							<label><b>Footer</b></label>
							<select
									ng-model="iLayouts[layout.name].footer.id"
									ng-options="key as key for (key, value) in i_templates.footer">
									<option value="">Default</option>
							</select>
						</div>
					</div>
					
					<div class="clearfix"></div>

					<!-- SIDEBARS -->
					<div class=" sidebars" ng-show="showModule('sidebars', layout.name)">

						<span class="select-module"
							ng-repeat="location in iLayoutOptions.widget_areas"
							ng-show="showModule('sidebar-location', layout.name, location.slug)">
							<hr class="thin">
							<label><b>{{ location.name }}</b></label>
							<select
								ng-model="iLayouts[layout.name].sidebars[location.slug].id"
								ng-options="sidebar.id as sidebar.name for sidebar in i_sidebars">
								<option value="">--- Select Widget Area ---</option>
							</select>
							
							<div ng-repeat="screen_size in iLayoutOptions.screen_sizes">
								<label style="text-align:right">{{screen_size.name}}</label>
								<select
									ng-model="iLayouts[layout.name].sidebars[location.slug].width[screen_size.slug]"
									ng-options="sidebar_width.slug as sidebar_width.name for sidebar_width in iLayoutOptions.column_widths | orderBy:'name'">
								</select>
								<i ng-class="screen_size.icon" class="icon-small"></i>
							</div>

						</span>

					</div>


				</td>
			</tr>
		</table>
		
		<hr>
		TODO : 
		<ul>
			<li>This Module in Meta Box per post and page and selected post types</li>
			<li>Toggle each post type to control via meta box, including post and page</li>
		</ul>

		<hr>

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