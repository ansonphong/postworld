<?php
	global $pw;
	$pwInject = $pw['inject'];
	// Get icons if they're defined
	$icons = _get( $vars, 'icons' );
?>
<script>
	postworldAdmin.controller( 'iconDataCtrl', [ '$scope', 'pwIconsets', '$log',
		function($scope, $pwIconsets, $log){
		$scope.customIconOptions = <?php echo json_encode( $icons ) ?>;
		$scope.iconsets = $pwIconsets.array();
		//$log.debug( 'iconsets', $scope.iconsets );
	}]);
</script>
<!-- DROPDOWN -->
<span
	dropdown
	class="dropdown"
	pw-ui
	ng-controller="iconDataCtrl">
	<!-- SELECTED ITEM -->
	<button
		type="button"
		dropdown-toggle
		class="area-select area-select-icon">
		<i ng-show="uiBool(<?php echo $vars['ng_model']; ?>)" class="{{ <?php echo $vars['ng_model']; ?> }} <?php if( $vars['icon_spin'] == true ) echo 'icon-spin' ?>"></i>
		<span class="select-icon-none" ng-hide="uiBool(<?php echo $vars['ng_model']; ?>)">None</span>
	</button>
	<!-- MENU -->
	<ul class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >
		<?php
		///// CUSTOM ICONS ARRAY /////
		if( is_array( $icons ) ) : ?>
				<li
					class="select-icon"
					ng-repeat="icon in ::customIconOptions"
					ng-click="<?php echo $vars['ng_model']; ?> = icon">
					<i
						class="{{ icon }}"></i>
				</li>
		<?php endif; ?>

		<?php
		///// REGISTERED ICONSETS /////
		if( !is_array( $icons ) ) : ?>

			<div ng-repeat="iconset in ::iconsets">
				<h3>{{ iconset.name }}</h3>
				<li
					class="select-icon"
					ng-repeat="icon in iconset.classes"
					ng-click="<?php echo $vars['ng_model']; ?> = icon">
					<i class="{{ icon }}"></i>
				</li>
			</div>

		<?php endif; ?>

	</ul>

	<button
		class="select-icon-none"
		ng-show="uiBool(<?php echo $vars['ng_model']; ?>)"
		ng-click="<?php echo $vars['ng_model']; ?> = false">
		<span><i class="icon-close"></i></span>
	</button>
</span>