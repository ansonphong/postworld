<?php
	// Enable Media Library
	wp_enqueue_media();

	$pwBackgrounds = pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUNDS ) );
	$pw_backgrounds_structure = apply_filters( PW_MODEL_BACKGROUNDS, array() );
?>
<div ng-app="infinite" class="postworld styles wrap">
	<script type="text/javascript">
		//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
		infinite.controller('pwBackgroundsDataCtrl', [ '$scope', '$window', function( $scope, $window ){
			$scope.lang = "en";
			// Print Data
			$scope.language = <?php global $i_style_language; echo json_encode( $i_style_language ); ?>;
			$scope.pwBackgrounds = <?php echo json_encode( $pwBackgrounds ); ?>;
			$scope.pwBackgroundsStructure = <?php echo json_encode( $pw_backgrounds_structure ); ?>;
		}]);
	</script>
	<div
		i-admin
		pw-admin-backgrounds
		ng-controller="pwBackgroundsDataCtrl"
		ng-cloak>

		<h1>
			<i class="icon-paint-format"></i>
			Backgrounds
			<button class="add-new-h2" ng-click="newBackground()"><?php ___('backgrounds.add_new'); ?></button>
		</h1>

		<hr class="thick">


		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-3">
				<ul class="list-menu">
					<li
						ng-repeat="item in pwBackgrounds"
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

					<?php
						echo pw_background_single_options( array( 'context'	=>	'siteAdmin' ) );
						//echo i_ob_include_template( 'admin/modules/layout-single.php', $vars );
					?>

					<h3><i class="icon-gear"></i> <?php ___('backgrounds.item_title'); ?></h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label
								for="item-name"
								class="inner"
								tooltip="<?php ___('backgrounds.name_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('backgrounds.name'); ?>
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
								tooltip="<?php ___('backgrounds.id_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('backgrounds.id'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<button
								class="inner inner-bottom-right inner-controls"
								ng-click="enableInput('#item-id');focusInput('#item-id')"
								tooltip="<?php ___('backgrounds.id_edit_info'); ?>"
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

					<label
						for="item-description"
						class="inner"
						tooltip="<?php ___('backgrounds.description_info'); ?>"
						tooltip-popup-delay="333">
						<?php ___('backgrounds.description'); ?>
						<i class="icon-info-circle"></i>
					</label>
					<input
						id="item-description"
						class="labeled"
						type="text"
						ng-model="selectedItem.description">

					<hr class="thin">

	
					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button( PW_OPTIONS_BACKGROUNDS,'pwBackgrounds'); ?></div>
		
					<!-- DELETE BUTTON -->
					<button
						class="button deletion"
						ng-click="deleteItem(selectedItem,'pwBackgrounds')">
						<i class="icon-close"></i>
						<?php ___('backgrounds.delete'); ?>
					</button>

					<!-- DUPLICATE BUTTON -->
					<button
						class="button"
						ng-click="duplicateItem(selectedItem,'pwBackgrounds')">
						<i class="icon-copy-2"></i>
						<?php ___('backgrounds.duplicate'); ?>
					</button>





				</div>

			</div>


		</div>






		<hr class="thick">
		
		<!-- SAVE BUTTON -->
		<div class="save-right"><?php i_save_option_button( PW_OPTIONS_BACKGROUNDS, 'pwBackgrounds'); ?></div>

		<button ng-click="resetDefaults()" class="button">Reset to Defaults</button>
		
		<hr class="thick">

		<pre>pwBackgrounds : {{ pwBackgrounds | json }}</pre>

	</div>

</div>