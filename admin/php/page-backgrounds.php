<?php
	// Enable Media Library
	wp_enqueue_media();

	$pwBackgrounds = pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUNDS ) );
	$pw_backgrounds_structure = apply_filters( PW_MODEL_BACKGROUNDS, array() );
?>
<div class="postworld styles wrap">
	<script type="text/javascript">
		//////////////////// LAYOUT VIEW CONTROLLER ////////////////////
		postworldAdmin.controller('pwBackgroundsDataCtrl',
			[ '$scope', '$window', '$_',
			function( $scope, $window, $_ ){
			// Print Data
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
			<i class="pwi-paint-format"></i>
			<?php _e( 'Backgrounds', 'postworld' ) ?>
			<button class="add-new-h2" ng-click="newBackground()">
				<?php _e( 'Add New Background', 'postworld' ) ?>
			</button>
		</h1>

		<hr class="thick">

		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-2">
				<ul class="list-menu">
					<li
						ng-click="selectItem('settings');"
						ng-class="menuClass('settings')">
						<i class="pwi-gear"></i> Settings
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
				<div ng-show="showView('settings')" class="well flush-top">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_BACKGROUND_CONTEXTS, 'pwBackgroundContexts'); ?></div>

					<h3>
						<i class="icon pwi-target"></i>
						Contexts
					</h3>

					<div
						ng-repeat="context in contexts"
						class="pw-row well">
						<div class="pw-col-2">
							<span
								uib-tooltip="{{context.name}}"
								tooltip-popup-delay="333">
								<i class="{{context.icon}}"></i>
								{{context.label}}
							</span>
						</div>
						<div class="pw-col-10">
							<?php echo pw_background_select( array( 'context' => 'siteAdmin' ) ); ?>
						</div>
					</div>

					<?php if( pw_dev_mode() ) : ?>
						<hr class="thick">
						<div class="pw-dev well">
							<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
							<pre><code>{{ pwBackgroundContexts | json }}</code></pre>
						</div>
					<?php endif; ?>

				</div>

				<!-- ///// EDIT BACKGROUND ///// -->
				<div ng-show="showView('editItem')" class="well">

					<?php
						echo pw_background_single_options( array( 'context'	=>	'siteAdmin' ) );
					?>

					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right">
						<?php pw_save_option_button( PW_OPTIONS_BACKGROUNDS,'pwBackgrounds'); ?>
					</div>
		

					<h3>
						<i class="pwi-gear"></i>
						<?php _e( 'Background Settings', 'postworld' ) ?>
					</h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label
								for="item-name"
								class="inner"
								uib-tooltip="<?php _e( 'The name makes it easy to find', 'postworld' ) ?>"
								tooltip-popup-delay="333">
								<?php _e( 'Background Name', 'postworld' ) ?>
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
								uib-tooltip="<?php _e( 'The ID is the unique name for the vackground. It contains only letters, numbers, and hyphens.', 'postworld' ) ?>"
								tooltip-popup-delay="333">
								<?php _e( 'Background ID', 'postworld' ) ?>
								<i class="pwi-info-circle"></i>
							</label>
							<button
								class="inner inner-bottom-right inner-controls"
								ng-click="enableInput('#item-id');focusInput('#item-id')"
								uib-tooltip="<?php _e( 'Editing the ID may cause instances of the background to disappear.', 'postworld' ) ?>"
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
						uib-tooltip="<?php _e( 'The description describes the intended use of the Background.', 'postworld' ) ?>"
						tooltip-popup-delay="333">
						<?php _e( 'Description', 'postworld' ) ?>
						<i class="pwi-info-circle"></i>
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
						<i class="pwi-close"></i>
						<?php _e( 'Delete Background', 'postworld' ) ?>
					</button>

					<!-- DUPLICATE BUTTON -->
					<button
						class="button"
						ng-click="duplicateItem(selectedItem,'pwBackgrounds')">
						<i class="pwi-copy-2"></i>
						<?php _e( 'Duplicate Background', 'postworld' ) ?>
					</button>


				</div>

			</div>

		</div>

		<?php if( pw_dev_mode() ) : ?>
			<hr class="thick">
			<div class="pw-dev well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<div class="well">
					<h3>$scope.pwBackgrounds</h3>
					<pre><code>{{ pwBackgrounds | json }}</code></pre>
				</div>

				<div class="well">
					<h3>$scope.images</h3>
					<pre><code>{{ images | json }}</code></pre>
				</div>
			</div>
		<?php endif; ?>


	</div>

</div>