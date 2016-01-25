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
	<?php
	/**
	 *	@todo INCLUDE A GUIDE OF EXISTING SHORTCODES
	 *	@todo Do maintainance on the existing Postworld Shortcodes system
	 *	@todo List all the options for each shortcode,
	 *				and auto-generate the shortcode optionally
	*/
	?>
</script>

<div class="postworld shortcodes wrap" ng-cloak>
	<div
		pw-admin
		pw-admin-shortcodes
		pw-ui
		ng-controller="pwShortcodesDataCtrl">

		<h1>
			<i class="pwi-code"></i>
			<?php _e( 'Shortcodes', 'postworld' ) ?>
			<button class="add-new-h2" ng-click="newShortcode()">
				<?php _e( 'Add New Shortcode', 'postworld' ) ?>
			</button>
		</h1>
		
		<hr class="thick">

		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-3">
				<ul class="list-menu">
					<li
						ng-click="selectItem('settings');"
						ng-class="menuClass('settings')">
						<i class="pwi-gear"></i>
						<?php _e( 'Settings', 'postworld' ) ?>
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
				<div class="well" ng-show="showView('editItem')">

					<label
						for="item-name"
						class="inner transparent">
						<i class="pwi-code"></i>
						<?php _e( 'Shortcode', 'postworld' ) ?>
					</label>
					<input
						id="item-name"
						class="labeled"
						type="text"
						disabled
						ng-value="generateShortcode(selectedItem)">

					<h3>
						<i class="pwi-gear"></i>
						<?php _e( 'Shortcode Settings', 'postworld' ) ?>
					</h3>

					<div class="pw-row">
						<div class="pw-col-4">
							<label
								for="item-id"
								class="inner"
								uib-tooltip="<?php _e( 'The ID is the unique name for the shortcode. This is used to invoke the shortcode.', 'postworld' ) ?>"
								tooltip-popup-delay="333">
								<?php _e( 'Shortcode ID', 'postworld' ) ?>
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
								uib-tooltip="<?php _e( 'Whatever you want to call it', 'postworld' ) ?>"
								tooltip-popup-delay="333">
								<?php _e( 'Shortcode Snippet Name', 'postworld' ) ?>
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
								<label class="inner"><?php _e( 'Shortcode Type', 'postworld' ) ?></label>
								<div class="btn-group">
									<label
										class="btn"
										ng-model="selectedItem.type"
										uib-btn-radio="'self-enclosing'">
										<?php _ex( 'Self-enclosing', 'shortcode', 'postworld' ) ?>
									</label>
									<label
										class="btn"
										ng-model="selectedItem.type"
										uib-btn-radio="'enclosing'">
										<?php _ex( 'Enclosing', 'shortcode', 'postworld' ) ?>
									</label>
								</div>

								<!-- DESCRIPTION -->
								&nbsp;
								<span ng-show="selectedItem.type == 'enclosing'">
									<?php _e( 'Contains two parts, a beginning and end, which enclose content', 'postworld' ) ?>
								</span>
								<span ng-show="selectedItem.type == 'self-enclosing'">
									<?php _e( 'Contains one part, which is self-contained', 'postworld' ) ?>
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
							<?php _e( 'Content', 'postworld' ) ?>
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
							<?php _e( 'Before Content', 'postworld' ) ?>
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
							<?php _e( 'After Content', 'postworld' ) ?>
						</label>
						<textarea
							id="shortcode-after_content"
							msd-elastic
							class="labeled elastic"
							ng-model="selectedItem.after_content">
						</textarea>

					</div>

					<div class="well">

						<!-- SAVE BUTTON -->
						<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_SHORTCODE_SNIPPETS,'pwShortcodeSnippets'); ?></div>
			
						<!-- DELETE BUTTON -->
						<button
							class="button deletion"
							ng-click="deleteItem(selectedItem,'pwShortcodeSnippets')">
							<i class="pwi-close"></i>
							<?php _e( 'Delete Shortcode', 'postworld' ) ?>
						</button>

						<!-- DUPLICATE BUTTON -->
						<button
							class="button"
							ng-click="duplicateItem(selectedItem,'pwShortcodeSnippets')">
							<i class="pwi-copy-2"></i>
							<?php _e( 'Duplicate Shortcode', 'postworld' ) ?>
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
				<div class="well">
					<h3>$scope.pwShortcodeSnippets</h3>
					<pre><code>{{ pwShortcodeSnippets | json }}</code></pre>
				</div>
			</div>
		<?php endif; ?>

	</div>

</div>