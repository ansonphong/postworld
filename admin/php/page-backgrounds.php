<?php
	// Enable Media Library
	wp_enqueue_media();

	$pwBackgrounds = pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUNDS ) );
	$pw_backgrounds_structure = apply_filters( PW_MODEL_BACKGROUNDS, array() );
?>
<div ng-app="postworldAdmin" class="postworld styles wrap">
	<script type="text/javascript">
		//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
		postworldAdmin.controller('pwBackgroundsDataCtrl',
			[ '$scope', '$window', '_',
			function( $scope, $window, $_ ){
			$scope.lang = "en";
			// Print Data
			$scope.language = <?php global $i_style_language; echo json_encode( $i_style_language ); ?>;
			$scope.pwBackgrounds = <?php echo json_encode( $pwBackgrounds ); ?>;
			$scope.pwBackgroundsStructure = <?php echo json_encode( $pw_backgrounds_structure ); ?>;
			$scope.contexts = <?php echo json_encode( pw_get_contexts() ); ?>;
		
			$scope.pwBackgroundContexts = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUND_CONTEXTS ) ) ); ?>;
			if( _.isEmpty( $scope.pwBackgroundContexts ) )
				$scope.pwBackgroundContexts = {};
			
			// Watch Background Contexts
			$scope.$watch( 'pwBackgroundContexts', function(val){
				// Delete empty values
				$_.removeEmpty( $scope.pwBackgroundContexts );
			}, 1);

		}]);
	</script>
	<div
		pw-admin
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
			<div class="pw-col-2">
				<ul class="list-menu">
					<li
						ng-click="selectItem('contexts');"
						ng-class="menuClass('contexts')">
						<i class="icon-target"></i> Contexts
					</li>
				</ul>
				
				<hr class="thin">

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

			<div class="pw-col-10">

				<!-- ///// EDIT SETTINGS ///// -->
				<div ng-show="showView('contexts')" class="well flush-top">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_BACKGROUND_CONTEXTS, 'pwBackgroundContexts'); ?></div>

					<h3>Contexts</h3>

					<table>
						<tr ng-repeat="context in contexts"
							valign="top">
							<th scope="row" align="left">
								<span
									tooltip="{{context.name}}"
									tooltip-popup-delay="333">
									<i class="{{context.icon}}"></i>
									{{context.label}}
									</th>
								</span>
							<td>
								<?php echo pw_background_select( array( 'context' => 'siteAdmin' ) ); ?>
							</td>
						</tr>
					</table>

					<pre>{{ pwBackgroundContexts | json }}</pre>

				</div>

				<!-- ///// EDIT BACKGROUND ///// -->
				<div ng-show="showView('editItem')" class="well">

					<?php
						echo pw_background_single_options( array( 'context'	=>	'siteAdmin' ) );
						//echo i_ob_include_template( 'admin/modules/layout-single.php', $vars );
					?>

					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_BACKGROUNDS,'pwBackgrounds'); ?></div>
		

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

		<!--
		<hr class="thick">
		<button ng-click="resetDefaults()" class="button">Reset to Defaults</button>

		<hr class="thick">
		<pre>images : {{ images | json }}</pre>

		<hr class="thick">

		<pre>pwBackgrounds : {{ pwBackgrounds | json }}</pre>
		-->

		

	</div>

</div>