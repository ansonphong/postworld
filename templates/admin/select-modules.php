<?php
pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwModulesCtrl',
	'vars' => array(
		'pwModules' => pw_enabled_modules(),
		'availableModules' => pw_available_modules(),
		'supportedModules' => pw_supported_modules(),
		'requiredModules' => pw_required_modules(),
		),
	));
?>

<div
	pw-admin-options
	pw-admin-modules
	ng-controller="pwModulesCtrl"
	class="well">

	<div class="save-right">
		<?php pw_save_option_button( PW_OPTIONS_MODULES, 'pwModules'); ?>
	</div>
	<h2>
		<i class="pwi-th-large"></i>
		Modules
	</h2>
	<small>Here you can enable the modules you would like to use, and disable the ones you do not need.</small>
	<hr class="thin">

	<div class="well" ng-repeat="module in availableModules">
		<label for="select-{{module.slug}}">
			<div>
				<input
					id="select-{{module.slug}}"
					type="checkbox"
					ng-model="selectedModules[ module.slug ]"
					ng-disabled="isRequired( module.slug )">
				<i class="{{ module.icon }}"></i>
				{{ module.name }}
				<small
					ng-show="isRequired( module.slug )">
					(required)
				</small>
			</div>
		</label>
	</div>

	<!--
	<pre>selectedModules : {{ selectedModules | json }}</pre>
	<pre>pwModules : {{ pwModules | json }}</pre>
	-->

</div>