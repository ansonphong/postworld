<?php
	$iSidebars = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );
?>
<script>
	infinite.controller( 'pwSidebarsDataCtrl', [ '$scope', function( $scope ){
		$scope.iSidebars = <?php echo json_encode( $iSidebars ); ?>;
	}]);
</script>

<div id="infinite_admin" ng-app="infinite" class="postworld sidebars wrap">
	<div
		i-admin
		i-admin-sidebars
		ng-controller="pwSidebarsDataCtrl"
		ng-cloak>

		<h2>
			<i class="icon-map"></i>
			Sidebars
			<button class="add-new-h2" ng-click="newSidebar()"><?php ___('sidebars.add_new'); ?></button>
		</h2>
		
		<hr class="thick">

		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-3">
				<ul class="list-menu">
					<li
						ng-repeat="item in iSidebars"
						ng-click="selectItem(item)"
						ng-class="menuClass(item)">
						{{ item.name }}
					</li>
				</ul>
				<div class="space-6"></div>
			</div>


			<!-- ///// EDIT SETTINGS ///// -->
			<div class="pw-col-9">
				<div ng-show="showView('editItem')">

					<h3><i class="icon-gear"></i> <?php ___('sidebars.item_title'); ?></h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label
								for="item-name"
								class="inner"
								tooltip="<?php ___('sidebars.name_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('sidebars.name'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="item-name"
								class="labeled"
								type="text"
								ng-model="selectedItem.name">
						</div>
						<div class="pw-col-6">
							<label
								for="item-id"
								class="inner"
								tooltip="<?php ___('sidebars.id_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('sidebars.id'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<button
								class="inner inner-bottom-right inner-controls"
								ng-click="enableInput('#item-id');focusInput('#item-id')"
								tooltip="<?php ___('sidebars.id_edit_info'); ?>"
								tooltip-placement="left"
								tooltip-popup-delay="333">
								<i class="icon-edit"></i>
							</button>
							<input
								id="item-id"
								class="labeled"
								type="text"
								ng-model="selectedItem.id"
								pw-sanitize="id"
								disabled
								ng-blur="disableInput('#item-id')">
						</div>
					</div>

					<hr class="thin">

					<div class="pw-row">
						<div class="pw-col-9">
							<label
								for="item-description"
								class="inner"
								tooltip="<?php ___('sidebars.description_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('sidebars.description'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="item-description"
								class="labeled"
								type="text"
								ng-model="selectedItem.description">
						</div>
						<div class="pw-col-3">
							<label
								for="item-class"
								class="inner"
								tooltip="<?php ___('sidebars.class_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('sidebars.class'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								class="labeled"
								id="item-class"
								type="text"
								ng-model="selectedItem.class">
						</div>
					</div>

					<hr class="thin">

					<label
						for="item-before_widget"
						class="inner"
						tooltip="<?php ___('sidebars.before_widget_info'); ?>"
						tooltip-popup-delay="333">
						<?php ___('sidebars.before_widget'); ?>
					</label>
					<textarea
						id="item-before_widget"
						msd-elastic
						class="labeled elastic"
						ng-model="selectedItem.before_widget">
					</textarea>

					<hr class="thin">

					<label
						for="item-after_widget"
						class="inner"
						tooltip="<?php ___('sidebars.after_widget_info'); ?>"
						tooltip-popup-delay="333">
						<?php ___('sidebars.after_widget'); ?>
					</label>
					<textarea
						id="item-after_widget"
						msd-elastic
						class="labeled elastic"
						ng-model="selectedItem.after_widget">
					</textarea>
				
					<hr class="thin">

					<label
						for="item-before_title"
						class="inner"
						tooltip="<?php ___('sidebars.before_title_info'); ?>"
						tooltip-popup-delay="333">
						<?php ___('sidebars.before_title'); ?>
					</label>
					<textarea
						id="item-before_title"
						msd-elastic
						class="labeled elastic"
						ng-model="selectedItem.before_title">
					</textarea>

					<hr class="thin">

					<label
						for="item-after_title"
						class="inner"
						tooltip="<?php ___('sidebars.after_title_info'); ?>"
						tooltip-popup-delay="333">
						<?php ___('sidebars.after_title'); ?>
					</label>
					<textarea
						id="item-after_title"
						msd-elastic
						class="labeled elastic"
						ng-model="selectedItem.after_title">
					</textarea>

					<hr class="thick">

	
					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button( PW_OPTIONS_SIDEBARS,'iSidebars'); ?></div>
		
					<!-- DELETE BUTTON -->
					<button
						class="button deletion"
						ng-click="deleteItem(selectedItem,'iSidebars')">
						<i class="icon-close"></i>
						<?php ___('sidebars.delete'); ?>
					</button>

					<!-- DUPLICATE BUTTON -->
					<button
						class="button"
						ng-click="duplicateItem(selectedItem,'iSidebars')">
						<i class="icon-copy-2"></i>
						<?php ___('sidebars.duplicate'); ?>
					</button>

				</div>

			</div>


		</div>

		<hr class="thick">

		<pre>{{ iSidebars | json }}</pre>

	</div>

</div>