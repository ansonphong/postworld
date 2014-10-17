<?php 
	extract( $vars );
?>
<!-- DROPDOWN -->
<div
	class="select-layout dropdown dropdown-layouts pull-left">
	<!-- SELECTED ITEM -->
	<span
		dropdown-toggle
		class="area area-select">
		<img
			ng-src="{{ selectedLayout( <?php echo $ng_model; ?>.layout ).image }}"
			style="width:45px; height: auto;">
			<label>{{ selectedLayout( <?php echo $ng_model; ?>.layout ).label }}</label>
	</span>
	<!-- MENU -->
	<ul class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >
		
		<!-- DEFAULT OPTION -->
		<label ng-repeat="option in iLayoutOptions.formats.default"
			ng-hide="layout.name == 'default'"
			class="radio_image_select">
			<input ng-model="<?php echo $ng_model; ?>.layout"
				name="{{ layout.name }}"
				value="{{ option.slug }}"
				type="radio" />
			<img ng-src="{{ option.image }}" title="Default" tooltip="Default" tooltip-popup-delay="200">
		</label>

		<!-- LAYOUT FORMATING OPTIONS -->
		<label class="radio_image_select"
			ng-repeat="option in iLayoutOptions.formats.options">
			<input ng-model="<?php echo $ng_model; ?>.layout"
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
				ng-model="<?php echo $ng_model; ?>.header.id"
				ng-options="key as key for (key, value) in i_templates.header">
				<option value="">Default</option>
		</select>
	</div>
	<!-- FOOTER -->
	<div>
		<label><b>Footer</b></label>
		<select
				ng-model="<?php echo $ng_model; ?>.footer.id"
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
			ng-model="<?php echo $ng_model; ?>.sidebars[location.slug].id"
			ng-options="sidebar.id as sidebar.name for sidebar in i_sidebars">
			<option value="">--- Select Widget Area ---</option>
		</select>
		
		<div ng-repeat="screen_size in iLayoutOptions.screen_sizes">
			<label style="text-align:right">{{screen_size.name}}</label>
			<select
				ng-model="<?php echo $ng_model; ?>.sidebars[location.slug].width[screen_size.slug]"
				ng-options="sidebar_width.slug as sidebar_width.name for sidebar_width in iLayoutOptions.column_widths | orderBy:'name'">
			</select>
			<i ng-class="screen_size.icon" class="icon-small"></i>
		</div>

	</span>

</div>