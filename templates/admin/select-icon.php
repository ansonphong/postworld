<?php
	global $pw;
	$pwInject = $pw['inject'];
?>
<!-- DROPDOWN -->
<span
	dropdown
	class="dropdown"
	pw-ui>
	<!-- SELECTED ITEM -->
	<span
		dropdown-toggle
		class="area-select area-select-icon">
		<i ng-show="uiBool(<?php echo $vars['ng_model']; ?>)" class="{{ <?php echo $vars['ng_model']; ?> }}"></i>
		<span class="select-icon-none" ng-hide="uiBool(<?php echo $vars['ng_model']; ?>)">None</span>
	</span>
	<!-- MENU -->
	<ul class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >
		
		<li
			class="select-icon-none"
			ng-show="uiBool(<?php echo $vars['ng_model']; ?>)"
			ng-click="<?php echo $vars['ng_model']; ?> = false">
			<span>None</span>
		</li>

		<?php ///// ICOMOON /////
			if( in_array( 'icomoon', $pwInject ) ){ ?>
				<li
					class="select-icon"
					ng-repeat="icon in ::options.icon.icomoon"
					ng-click="<?php echo $vars['ng_model']; ?> = icon.class">
					<i
						class="{{ icon.class }}"></i>
				</li>
		<?php } ?>

		<?php ///// GLYPHICONS /////
			if( in_array( 'glyphicons-halflings', $pwInject ) ){ ?>
				<li
					class="select-icon"
					ng-repeat="icon in ::options.icon.glyphicons"
					ng-click="<?php echo $vars['ng_model']; ?> = icon.class">
					<i
						class="{{ icon.class }}"></i>
				</li>
		<?php } ?>

	</ul>
</span>