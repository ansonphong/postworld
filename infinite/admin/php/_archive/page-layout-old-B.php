<?php
	// Load Globals
	global $i_options;
	global $i_style_language;

?>

<script type="text/javascript">
	//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
	var styleData = function( $scope, $window ){ // $parse, iData
		
		$scope.lang = "en";

		// Import Data
		$scope.language = <?php echo json_encode( $i_style_language ); ?>;

		//$scope.settings = <?php echo json_encode( i_style_model() ); ?>;
		// Load Previously Saved Settings Object
		$scope.style_model_default = <?php echo json_encode( i_style_model() ); ?>;
		$scope.style_model_saved = <?php echo i_get_styles(); ?>;

	};
</script>
<div
	i-admin-style
	ng-controller="styleData"
	ng-cloak>

	<!--Sub-Themes (Dropdown) | Save as Theme-->
	<hr>

	<!--<div ng-include src="'/wp-content/themes/infinite/admin/templates/style-element.html'"></div>-->

	<h2> <i class="{{ language.meta.var.icon }}"></i> {{ language.meta.var.label[lang] }}</h2>

	<!-- ////////// VARIABLES ////////// -->
	<table class="form-table pad">
		<tr ng-repeat="(key, properties) in settings.var"
			valign="top"
			class="module layout">
			<th scope="row">
				<h3>
					{{key}}
				</h3>
			</th>
			<td class="align-top">
				
				<!-- PROPERTIES -->
				<div style="margin-top:10px;">
					<table>
						<tr ng-repeat="(var, value) in properties">
							<td>
								{{ language.var[var].label[lang] }}
								<div class="font-nano">{{var}}</div>
							</td>
							<td>
								<input
									type="text"
									
									ng-model="settings.var[key][var]">
							</td>
							<td>
								<div class="font-micro">{{ language.var[var].info[lang] }}</div>
							</td>
						</tr>
					</table>
				</div>
				
			</td>
		</tr>
	</table>

	<!-- ////////// END VARIABLES ////////// -->

	<hr>

	<!-- ////////// ELEMENTS ////////// -->

	<div class="button-panel-right">
		<!-- SAVE BUTTON -->
		<button i-save-option ng-click="saveOption('i-styles', 'settings' )" class="button button-primary button-large">
			<span ng-show="status != 'saving'"><i class="icon-save"></i> Save</span>
			<span ng-show="status == 'saving'"><i class="icon-spinner icon-spin"></i> Save</span>
		</button>
	</div>

	<h2> <i class="{{ language.meta.element.icon }}"></i> {{ language.meta.element.label[lang] }}</h2>
	
	<hr>

	<table class="form-table pad">
		<tr ng-repeat="(key, properties) in settings.element"
			valign="top"
			class="module layout">
			<th scope="row">
				<h3>
					{{ language.element[key].label[lang] }}
				</h3>
				<div class="font-micro">
					( <b>{{key}}</b> ) -
					{{ language.element[key].info[lang] }}
				</div>
			</th>
			<td class="align-top">
				<div>
					<!-- STYLE SAMPLE -->
					<div class="sample">
						{{ elementStart(key) }} SAMPLE {{ elementEnd(key) }}
					</div>
					<hr>
					<!-- EDIT BUTTON -->
					<button class="button"
						ng-click="toggleShow(key, 'properties')">
						<span ng-hide="showing[key].properties">
							{{ language.general.edit[lang] }}
						</span>
						<span ng-show="showing[key].properties">
							{{ language.general.done[lang] }}
						</span>
					</button>
					<!-- PROPERTIES -->
					<div ng-show="showing[key].properties">
						<hr>
						<table>
							<tr ng-repeat="(property, value) in properties">
								<td>
									{{ language.property[property].label[lang] }}
									<div class="font-nano">{{property}}</div>
								</td>
								<td>
									<input
										type="text"
										
										ng-model="settings.element[key][property]">
								</td>
								<td>
									<div class="font-micro">{{ language.property[property].info[lang] }}</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<!-- ////////// END ELEMENTS ////////// -->

	<hr>

	<!-- ////////// CLASSES ////////// -->
	<div class="button-panel-right">
		<!-- SAVE BUTTON -->
		<button i-save-option ng-click="saveOption('i-styles', 'settings' )" class="button button-primary button-large">
			<span ng-show="status != 'saving'"><i class="icon-save"></i> Save</span>
			<span ng-show="status == 'saving'"><i class="icon-spinner icon-spin"></i> Save</span>
		</button>
	</div>

	<h2> <i class="{{ language.meta.class.icon }}"></i> {{ language.meta.class.label[lang] }}</h2>

	<hr>

	<table class="form-table pad">
		<tr ng-repeat="(key, properties) in settings.class"
			valign="top"
			class="module layout">
			<th scope="row">
				<h3>
					.{{key}}
				</h3>
			</th>
			<td class="align-top">
				<div>
					<!-- EDIT BUTTON -->
					<button class="button"
						ng-click="toggleShow(key, 'properties')">
						<span ng-hide="showing[key].properties">
							{{ language.general.edit[lang] }}
						</span>
						<span ng-show="showing[key].properties">
							{{ language.general.done[lang] }}
						</span>
					</button>
					<!-- PROPERTIES -->
					<div ng-show="showing[key].properties">
						<hr>
						<table>
							<tr ng-repeat="(property, value) in properties">
								<td>
									
									<div class="font-nano">{{property}}</div>
								</td>
								<td>
									<input
										type="text"
										value="value"
										ng-model="settings.class[key][property]">
								</td>
								<td>
									<div class="font-micro">{{ language.property[property].info[lang] }}</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<!-- ////////// END CLASSES ////////// -->

	<hr>

	<button ng-click="resetDefaults()" class="button">Reset to Defaults</button>
	<hr>
	<pre>SETTINGS : {{ settings | json }}</pre>

	<!--
	<hr>
	<pre>STYLE MODEL SAVED : {{ style_model_saved | json }}</pre>
	<hr>
	<pre>STYLE MODEL DEFAULT : {{ style_model_default | json }}</pre>
	<hr>
	-->

</div>