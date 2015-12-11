<?php extract( $vars ); ?>

<div
	pw-ui
	ui-views="{}">

	<!-- DROPDOWN -->
	<div
		dropdown
		class="select-layout dropdown dropdown-layouts pull-left">
		<!-- SELECTED ITEM -->
		<span
			dropdown-toggle
			class="area area-select">
			<img
				ng-src="{{ selectedLayout( <?php echo $ng_model; ?>.template ).image }}"
				style="width:45px; height: auto;">
				<label>{{ selectedLayout( <?php echo $ng_model; ?>.template ).label }}</label>
		</span>
		<!-- MENU -->
		<ul class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >
			
			<!-- DEFAULT TEMPLATE OPTION -->
			<label ng-repeat="option in iLayoutOptions.templates.default"
				ng-hide="context.name == 'default'"
				class="radio_image_select">
				<input ng-model="<?php echo $ng_model; ?>.template"
					name="{{ context.name }}"
					value="{{ option.slug }}"
					type="radio" />
				<img ng-src="{{ option.image }}" title="Default" tooltip="Default" tooltip-popup-delay="200">
			</label>

			<!-- TEMPLATE OPTIONS -->
			<label class="radio_image_select"
				ng-repeat="option in iLayoutOptions.templates.options">
				<input ng-model="<?php echo $ng_model; ?>.template"
					name="{{ context.name }}"
					value="{{ option.slug }}"
					type="radio" />
				<img ng-src="{{ option.image }}" title="{{ option.label }}" width="90" height="60" tooltip="{{ option.label }}" tooltip-popup-delay="200">
			</label>

		</ul>
	</div>
	
	<button
		class="button"
		ng-show="<?php echo $ng_model; ?>.template != 'default'"
		type="button"
		ng-class="uiSetClass('headerFooter')"
		ng-click="uiToggleView('headerFooter')">
		<i class="pwi-layers"></i>
		Header & Footer
	</button>
	<div class="clearfix"></div>
	<div
		class="area header-footer pull-left"
		ng-show="showModule('headerFooter', <?php echo $ng_model; ?> ) && uiShowView('headerFooter')">

		<!-- HEADER -->
		<div>
			<label><b>Header</b></label>
			<select
					ng-model="<?php echo $ng_model; ?>.header.id"
					ng-options="key as key for (key, value) in iTemplates.header">
					<option value="">Default</option>
			</select>
		</div>
		<!-- FOOTER -->
		<div>
			<label><b>Footer</b></label>
			<select
					ng-model="<?php echo $ng_model; ?>.footer.id"
					ng-options="key as key for (key, value) in iTemplates.footer">
					<option value="">Default</option>
			</select>
		</div>
	</div>

	<div class="clearfix"></div>

	<!-- SIDEBARS -->
	<div class=" sidebars" ng-show="showModule('sidebars', <?php echo $ng_model; ?>)">
		<hr class="thin">
		<div class="select-module"
			ng-repeat="location in iLayoutOptions.widget_areas"
			ng-show="showModule('sidebar-location', <?php echo $ng_model; ?>, location.slug)">
			
			<label><b>{{ location.name }}</b></label>
			<select
				ng-model="<?php echo $ng_model; ?>.sidebars[location.slug].id"
				ng-options="sidebar.id as sidebar.name for sidebar in iSidebars">
				<option value="">--- Select Sidebar ---</option>
			</select>

			<button
				type="button"
				class="button"
				ng-class="uiSetClass('customResponsive_'+location.slug)"
				ng-click="uiToggleView('customResponsive_'+location.slug)">
				<i class="pwi-th-large"></i>
				Resposive
			</button>

			<div
				ng-show="uiShowView('customResponsive_'+location.slug)"
				ng-repeat="screen_size in iLayoutOptions.screen_sizes">
				<label style="text-align:right">{{screen_size.name}}</label>
				<select
					ng-model="<?php echo $ng_model; ?>.sidebars[location.slug].width[screen_size.slug]"
					ng-options="sidebar_width.slug as sidebar_width.name for sidebar_width in iLayoutOptions.column_widths | orderBy:'name'">
				</select>
				<i ng-class="screen_size.icon" class="pwi-small"></i>
			</div>

		</div>

		<hr class="thin">

		


	</div>

</div>