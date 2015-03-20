<?php
	$pwShortcodes = pw_get_option( array( 'option_name' => PW_OPTIONS_SHORTCODES ) );
	$pwShortcodeSnippets = pw_get_option( array( 'option_name' => PW_OPTIONS_SHORTCODE_SNIPPETS ) );

	$enabled_modules = pw_enabled_modules();

?>
<script>
	postworldAdmin.controller( 'pwShortcodesDataCtrl', [ '$scope', function( $scope ){
		
		$scope.pwShortcodes = <?php echo json_encode( $pwShortcodes ); ?>;
		$scope.pwShortcodeSnippets = <?php echo json_encode( $pwShortcodeSnippets ); ?>;

	}]);

	// INCLUDE OPTION FOR CONTENT SHORTCODE OR WRAPPING SHORTCODE, WITH BEGINNING AND END

	// INCLUDE A GUIDE OF EXISTING SHORTCODES

	// Do maintainance on the existing Postworld Shortcodes system

	// ADD ICON SHORTCODE : [icon class="pwi-merkaba"]
	// - List all the options, and auto-generate the shortcode optionally

</script>

<div class="postworld shortcodes wrap" ng-cloak>
	<div
		pw-admin
		pw-admin-shortcodes
		pw-ui
		ng-controller="pwShortcodesDataCtrl">

		<h1>
			<i class="pwi-code"></i>
			Shortcodes
			<button class="add-new-h2" ng-click="newShortcode()"><?php ___('shortcodes.add_new'); ?></button>
		</h1>
		
		<hr class="thick">

		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-3">
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
						ng-repeat="item in pwShortcodeSnippets"
						ng-click="selectItem(item)"
						ng-class="menuClass(item)">
						{{ item.name }}
					</li>
				</ul>
				<div class="space-6"></div>
			</div>

			<div class="pw-col-9">

				<!-- ///// EDIT SETTINGS ///// -->
				<div ng-show="showView('settings')">

					<div class="well">
						
					</div>

				</div>

				<!-- ///// EDIT ITEMS ///// -->
				<div ng-show="showView('editItem')">

					<label
						for="item-name"
						class="inner transparent">
						<i class="pwi-code"></i>
						<?php ___('shortcodes.shortcode'); ?>
					</label>
					<input
						id="item-name"
						class="labeled"
						type="text"
						disabled
						ng-value="generateShortcode(selectedItem)">

					<h3><i class="pwi-gear"></i> <?php ___('shortcodes.item_title'); ?></h3>

					<div class="pw-row">
						<div class="pw-col-4">
							<label
								for="item-id"
								class="inner"
								tooltip="<?php ___('shortcodes.id_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('shortcodes.id'); ?>
								<i class="pwi-info-circle"></i>
							</label>
							
							<input
								id="item-id"
								class="labeled"
								type="text"
								ng-model="selectedItem.id"
								pw-sanitize="id">
						</div>
						<div class="pw-col-8">
							<label
								for="item-name"
								class="inner"
								tooltip="<?php ___('shortcodes.name_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('shortcodes.name'); ?>
								<i class="pwi-info-circle"></i>
							</label>
							<input
								id="item-name"
								class="labeled"
								type="text"
								ng-model="selectedItem.name">
						</div>
					</div>

					<hr class="thin">

					<div class="pw-row">
						<div class="pw-col-12">
							
							<div class="labeled-area">
								<label class="inner">Type</label>
								<div class="btn-group">
									<label
										class="btn"
										ng-model="selectedItem.type"
										btn-radio="'self-enclosing'">
										<?php ___('shortcodes.self_enclosing'); ?>
									</label>
									<label
										class="btn"
										ng-model="selectedItem.type"
										btn-radio="'enclosing'">
										<?php ___('shortcodes.enclosing'); ?>
									</label>
								</div>

								<!-- DESCRIPTION -->
								&nbsp;
								<span ng-show="selectedItem.type == 'enclosing'">
									<?php ___('shortcodes.enclosing_description'); ?>
								</span>
								<span ng-show="selectedItem.type == 'self-enclosing'">
									<?php ___('shortcodes.self_enclosing_description'); ?>
								</span>

							</div>

						</div>
						<div class="pw-col-0">
							
						</div>
					</div>

					<hr class="thin">

					<!-- SELF-ENCOSING -->
					<div ng-show="selectedItem.type == 'self-enclosing'">
						<label
							for="shortcode-content"
							class="inner">
							<?php ___('shortcodes.content'); ?>
						</label>
						<textarea
							id="shortcode-content"
							msd-elastic
							class="labeled elastic"
							ng-model="selectedItem.content">
						</textarea>
					</div>

					<!-- ENCOSING -->
					<div ng-show="selectedItem.type == 'enclosing'">
						<label
							for="shortcode-before_content"
							class="inner">
							<?php ___('shortcodes.before_content'); ?>
						</label>
						<textarea
							id="shortcode-before_content"
							msd-elastic
							class="labeled elastic"
							ng-model="selectedItem.before_content">
						</textarea>

						<label
							for="shortcode-after_content"
							class="inner">
							<?php ___('shortcodes.after_content'); ?>
						</label>
						<textarea
							id="shortcode-after_content"
							msd-elastic
							class="labeled elastic"
							ng-model="selectedItem.after_content">
						</textarea>

					</div>

					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_SHORTCODE_SNIPPETS,'pwShortcodeSnippets'); ?></div>
		
					<!-- DELETE BUTTON -->
					<button
						class="button deletion"
						ng-click="deleteItem(selectedItem,'pwShortcodeSnippets')">
						<i class="pwi-close"></i>
						<?php ___('shortcodes.delete'); ?>
					</button>

					<!-- DUPLICATE BUTTON -->
					<button
						class="button"
						ng-click="duplicateItem(selectedItem,'pwShortcodeSnippets')">
						<i class="pwi-copy-2"></i>
						<?php ___('shortcodes.duplicate'); ?>
					</button>

				</div>

			</div>


		</div>

		<?php if( pw_dev_mode() ): ?>
			<hr class="thick">
			<div class="well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<div class="well">
					<h3>$scope.pwShortcodeSnippets</h3>
					<pre><code>{{ pwShortcodeSnippets | json }}</code></pre>
				</div>
			</div>
		<?php endif; ?>

	</div>

</div>