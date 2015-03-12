<?php
	global $pw;
	$pwInject = $pw['inject'];
	// Get icons if they're defined
	$icons = _get( $vars, 'icons' );
	$controller_id = 'iconDataCtrl_'.pw_random_string();
?>

<script>
	// TODO : MAKE MODAL WINDOW FOR SELECTING ICONS, PASS IN DATA
	postworldAdmin.controller( '<?php echo $controller_id ?>', [
		'$scope', 'pwIconsets', '$log', '_',
		function( $scope, $pwIconsets, $log, $_ ){
		$scope.customIconOptions = <?php echo json_encode( $icons ) ?>;
		$scope.iconsets = $pwIconsets.array();
		//$log.debug( 'iconsets', $scope.iconsets );

		$scope.filterIconset = function( iconsetClasses ){
			if( _.isEmpty($scope.filterString) )
				return true;
			return $_.stringInArray( $scope.filterString, iconsetClasses );
		}

		$scope.filterIcons = function( className ){
			if( _.isEmpty($scope.filterString) )
				return true;
			return $_.inString( $scope.filterString, className );
		}

		$scope.iconSelectedClass = function( iconClass, selectedIcon ){
			return ( iconClass === selectedIcon ) ? 'selected' : '';
		}

	}]);
</script>

<div class="postworld">

	<!-- DROPDOWN -->
	<div
		dropdown
		class="dropdown select-icon"
		pw-ui
		ng-controller="<?php echo $controller_id ?>">

		<!-- SELECTED ITEM -->
		<span dropdown-toggle>
			<button
				type="button"
				class="area-select area-select-icon"
				ng-click="uiFocusElement('#filterString')"
				ng-show="uiBool(<?php echo $vars['ng_model']; ?>)">
				<i
					ng-show="uiBool(<?php echo $vars['ng_model']; ?>)"
					ng-class="<?php echo $vars['ng_model']; ?>"
					class="<?php if( $vars['icon_spin'] == true ) echo 'icon-spin' ?>">
				</i>
			</button>

			<button
				type="button"
				class="button"
				ng-click="uiFocusElement('#filterString')"
				ng-hide="uiBool(<?php echo $vars['ng_model']; ?>)">
				<i class="pwi-target"></i>
				Select an Icon
			</button>
		</span>

		<!-- MENU -->
		<div class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >

			<div class="search-input-wrapper">
				<i class="input-icon pwi-search"></i>
				<input
					class="input-icon-left"
					id="filterString"
					type="text"
					ng-model="filterString"
					placeholder="Search Icons..."
					prevent-default-click
					stop-propagation-click>
			</div>

			<?php
			///// CUSTOM ICONS ARRAY /////
			if( is_array( $icons ) ) : ?>
				<ul class="iconset">
					<li
						class="select-icon"
						ng-repeat="icon in customIconOptions"
						ng-click="<?php echo $vars['ng_model']; ?> = icon">
						<i
							class="{{ icon }}"></i>
					</li>
				</ul>
			<?php endif; ?>

			<?php
			///// REGISTERED ICONSETS /////
			if( !is_array( $icons ) ) : ?>

				<div ng-repeat="iconset in iconsets">
					<div
						class="iconset-wrapper"
						ng-show="filterIconset(iconset.classes)">
						<h4>
							{{ iconset.name }}
						</h4>
						<ul class="iconset">
							<li
								class="select-icon"
								ng-repeat="icon in iconset.classes"
								ng-show="filterIcons(icon)"
								ng-click="<?php echo $vars['ng_model']; ?> = icon"
								ng-class="iconSelectedClass(icon,<?php echo $vars['ng_model']; ?>)">
								<i class="{{ icon }}"></i>
							</li>
						</ul>
					</div>
				</div>

			<?php endif; ?>

		</div>

		<button
			class="button select-icon-none"
			style="vertical-align:top;"
			ng-show="uiBool(<?php echo $vars['ng_model']; ?>)"
			ng-click="<?php echo $vars['ng_model']; ?> = ''"
			prevent-default-click>
			<span><i class="pwi-close"></i></span>
		</button>
	</div>
</div>