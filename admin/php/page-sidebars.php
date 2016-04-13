<?php
	$pwSidebars = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );
?>
<script>
	postworldAdmin.controller( 'pwSidebarsDataCtrl', [ '$scope', function( $scope ){
		$scope.pwSidebars = <?php echo json_encode( $pwSidebars ); ?>;
	}]);
</script>

<div
	class="postworld sidebars wrap"
	pw-ui
	ng-cloak>
	<div
		pw-admin
		pw-admin-sidebars
		ng-controller="pwSidebarsDataCtrl">
		<h1>
			<i class="pwi-map"></i>
			<?php _e( 'Sidebars', 'postworld' ) ?>
			<button class="add-new-h2" ng-click="newSidebar()">
				<?php _e( 'Add New Sidebar', 'postworld' ) ?>
			</button>
		</h1>
		
		<hr class="thick">

		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-3">
				<ul class="list-menu">
					<li
						ng-repeat="item in pwSidebars"
						ng-click="selectItem(item)"
						ng-class="menuClass(item)">
						{{ item.name }}
					</li>
				</ul>
				<div class="space-6"></div>
			</div>

			<!-- ///// EDIT SETTINGS ///// -->
			<div class="pw-col-9">
				<div class="well" ng-show="showView('editItem')">

					<h3>
						<i class="pwi-gear"></i>
						<?php _e( 'Sidebar Settings', 'postworld' ) ?>
					</h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label
								for="item-name"
								class="inner"
								uib-tooltip="<?php _e( 'The name is how it appears on the widgets options page', 'postworld' ) ?>"
								tooltip-popup-delay="333">
								<?php _e( 'Sidebar Name', 'postworld' ) ?>
								<i class="pwi-info-circle"></i>
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
								uib-tooltip="<?php _e( 'The ID is the unique name for the sidebar. It contains only letters, numbers, and hyphens.', 'postworld' ) ?>"
								tooltip-popup-delay="333">
								<?php _e( 'Sidebar ID', 'postworld' ) ?>
								<i class="pwi-info-circle"></i>
							</label>
							<button
								class="inner inner-bottom-right inner-controls"
								ng-click="enableInput('#item-id');focusInput('#item-id')"
								uib-tooltip="<?php _e( 'Editing the ID may cause instances of the sidebar to disappear', 'postworld' ) ?>"
								tooltip-placement="left"
								tooltip-popup-delay="333">
								<i class="pwi-edit"></i>
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

					<label
						for="item-description"
						class="inner"
						uib-tooltip="<?php _e( 'Describes the intended use of the sidebar', 'postworld' ) ?>"
						tooltip-popup-delay="333">
						<?php _e( 'Description', 'postworld' ) ?>
						<i class="pwi-info-circle"></i>
					</label>
					<input
						id="item-description"
						class="labeled"
						type="text"
						ng-model="selectedItem.description">
						
					<!-- ADVANCED -->
					<div
						ng-show="uiShowView('sidebar_advanced')">
						<hr class="thin">

						<label
							for="item-class"
							class="inner"
							uib-tooltip="<?php _e( 'The CSS class which is applied to each widget', 'postworld' ) ?>"
							tooltip-popup-delay="333">
							<?php _e( 'Class', 'postworld' ) ?>
							<i class="pwi-info-circle"></i>
						</label>
						<input
							class="labeled"
							id="item-class"
							type="text"
							ng-model="selectedItem.class">

						<hr class="thin">

						<label
							for="item-before_widget"
							class="inner"
							uib-tooltip="<?php _e( 'HTML that goes before the widget', 'postworld' ) ?>"
							tooltip-popup-delay="333">
							<?php _e( 'Before Widget', 'postworld' ) ?>
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
							uib-tooltip="<?php _e( 'HTML that goes after the widget', 'postworld' ) ?>"
							tooltip-popup-delay="333">
							<?php _e( 'After Widget', 'postworld' ) ?>
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
							uib-tooltip="<?php _e( 'HTML that goes before the title of each widget', 'postworld' ) ?>"
							tooltip-popup-delay="333">
							<?php _e( 'Before Title', 'postworld' ) ?>
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
							uib-tooltip="<?php _e( 'HTML that goes after the title of each widget', 'postworld' ) ?>"
							tooltip-popup-delay="333">
							<?php _e( 'After Title', 'postworld' ) ?>
						</label>
						<textarea
							id="item-after_title"
							msd-elastic
							class="labeled elastic"
							ng-model="selectedItem.after_title">
						</textarea>

					</div>

					<div class="well">

						<!-- SAVE BUTTON -->
						<div class="save-right">
							<?php pw_save_option_button( PW_OPTIONS_SIDEBARS,'pwSidebars'); ?>
						</div>
			
						<!-- DELETE BUTTON -->
						<button
							class="button deletion"
							ng-click="deleteItem(selectedItem,'pwSidebars')">
							<i class="pwi-close"></i>
							<?php _e( 'Delete Sidebar', 'postworld' ) ?>
						</button>

						<!-- DUPLICATE BUTTON -->
						<button
							class="button"
							ng-click="duplicateItem(selectedItem,'pwSidebars')">
							<i class="pwi-copy-2"></i>
							<?php _e( 'Duplicate Sidebar', 'postworld' ) ?>
						</button>

						<!-- ADVANCED BUTTON -->
						<button
							type="button"
							class="button"
							ng-click="uiToggleView('sidebar_advanced')"
							ng-class="uiSetClass('sidebar_advanced')">
							<i class="icon pwi-gear"></i>
							<?php _e( 'Advanced Options', 'postworld' ) ?>
						</button>

					</div>

				</div>

			</div>

		</div>

		<?php if( pw_dev_mode() ): ?>
			<hr class="thick">
			<div class="well">
				<h3>
					<i class="pwi-merkaba"></i>
					<?php _e( 'Development Mode', 'postworld' ) ?>
				</h3>
				<pre><code>pwSidebars : {{ pwSidebars | json }}</code></pre>
			</div>
		<?php endif; ?>
	</div>

</div>