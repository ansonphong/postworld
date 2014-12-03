<?php
	///// GET DATA /////
	// Feeds
	$pwFeeds = pw_get_option( array( 'option_name' => PW_OPTIONS_FEEDS ) );
	// Feed Settings
	$pwFeedSettings = i_get_option( array( 'option_name' => PW_OPTIONS_FEED_SETTINGS ) );
	// Feed Templates
	$htmlFeedTemplates = pw_get_templates(
		array(
			'subdirs' => array('feeds'),
			'path_type' => 'url',
			'ext'=>'html',
			)
		)['feeds'];	
	// Aux Feed Templates
	$phpFeedTemplates = pw_get_templates(
		array(
			'subdirs' => array('feeds'),
			'path_type' => 'url',
			'ext'=>'php',
			)
		)['feeds'];
?>
<script>
	postworldAdmin.controller( 'pwFeedsDataCtrl',
		[ '$scope', '_',
		function( $scope, $_ ){
		$scope.pwFeeds = <?php echo json_encode( $pwFeeds ); ?>;
		$scope.pwFeedSettings = <?php echo json_encode( $pwFeedSettings ); ?>;
		$scope.htmlFeedTemplates = <?php echo json_encode( $htmlFeedTemplates ); ?>;
		$scope.phpFeedTemplates = <?php echo json_encode( $phpFeedTemplates ); ?>;
		$scope.contexts = <?php echo json_encode( pw_get_contexts( array( 'default', 'standard', 'archive', 'search', 'taxonomy', 'post-type' ) ) ); ?>;
	
		// Watch Feed Settings
		$scope.$watch( 'pwFeedSettings', function(val){
			// Delete empty values
			$_.removeEmpty( $scope.pwFeedSettings );
		}, 1);

	}]);
</script>

<div ng-app="postworldAdmin" class="postworld feeds wrap" ng-cloak>
	<div
		pw-admin
		pw-admin-feeds
		ng-controller="pwFeedsDataCtrl"
		ng-cloak
		>
		
		<h1>
			<i class="icon-th-small"></i>
			Feeds
			<button class="add-new-h2" ng-click="newFeed()">Add New Feed</button>
		</h1>

		<hr class="thick">

		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-3">
				<ul class="list-menu">
					<li
						ng-click="selectItem('settings');"
						ng-class="menuClass('settings')">
						<i class="icon-gear"></i> Settings
					</li>
				</ul>
					<hr class="thin">
				<ul class="list-menu">
					<li
						ng-repeat="item in pwFeeds"
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

						<!-- SAVE BUTTON -->
						<div class="save-right"><?php i_save_option_button( PW_OPTIONS_FEED_SETTINGS,'pwFeedSettings'); ?></div>
		
						<h3><?php ___('feeds.settings.loading_icon') ?></h3>

						<!-- DROPDOWN -->
						<span
							class="dropdown">
							<!-- SELECTED ITEM -->
							<span
								dropdown-toggle
								class="area-select area-select-icon">
								<i class="{{ pwFeedSettings.loading_icon }} icon-spin"></i>
							</span>
							<!-- MENU -->
							<ul class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >
								<li
									class="select-icon"
									ng-repeat="icon in feedSettingsOptions.loadingIcon"
									ng-click="pwFeedSettings.loading_icon = icon">
									<i
										class="{{ icon }}"></i>
								</li>
							</ul>
						</span>
					</div>

					<div class="well">
						<!-- SAVE BUTTON -->
						<div class="save-right"><?php i_save_option_button( PW_OPTIONS_FEED_SETTINGS,'pwFeedSettings'); ?></div>
		
						<h3>Contexts</h3>

						<table
							width="100%"
							pw-ui
							ui-views="{}">
							<tr ng-repeat="context in contexts"
								valign="top">
								<th scope="row" align="left" width="25%">
									<span
										tooltip="{{context.name}}"
										tooltip-popup-delay="333">
										<i class="{{context.icon}}"></i>
										{{context.label}}
										</th>
									</span>
								<td>

									<button
										type="button"
										class="button"
										ng-class="uiSetClass('template_'+context.name)"
										ng-click="uiToggleView('template_'+context.name)">
										<i class="icon-th-large"></i>
										Template
									</button>

									<div
										ng-show="uiShowView('template_'+context.name)">
										<?php echo pw_feed_template_options( array( 'ng_model' => 'pwFeedSettings.context[context.name]' ) ); ?>
									</div>

								</td>
							</tr>
						</table>

					</div>

					
					{{ pwFeedSettings }}

				</div>


				<!-- ///// EDIT SETTINGS ///// -->
				<div ng-show="showView('editItem')">

					<h3><i class="icon-gear"></i> <?php ___('feeds.item_title'); ?></h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label
								for="item-name"
								class="inner"
								tooltip="<?php ___('feeds.name_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('feeds.name') ?>
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
								tooltip="<?php ___('feeds.id_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('feeds.id') ?>
								<i class="icon-info-circle"></i>
							</label>
							<button
								class="inner inner-bottom-right inner-controls"
								ng-click="enableInput('#item-id');focusInput('#item-id')"
								tooltip="<?php ___('feeds.id_edit_info'); ?>"
								tooltip-placement="left"
								tooltip-popup-delay="333">
								<i class="icon-edit"></i>
							</button>
							<input
								id="item-id"
								class="labeled"
								type="text"
								ng-model="selectedItem.id"
								disabled
								pw-sanitize="id"
								ng-blur="disableInput('#item-id');">
						</div>
					</div>

					<div class="pw-row">
						<div class="pw-col-3">
							<label
								for="item-preload"
								class="inner"
								tooltip="<?php ___('feeds.preload_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('feeds.preload'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="item-preload"
								class="labeled"
								type="number"
								ng-model="selectedItem.preload">
						</div>
						<div class="pw-col-3">
							<label
								for="item-load_increment"
								class="inner"
								tooltip="<?php ___('feeds.increment_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('feeds.increment'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="item-load_increment"
								class="labeled"
								type="number"
								ng-model="selectedItem.load_increment">
						</div>
						<div class="pw-col-3">
							<label
								for="item-offset"
								class="inner"
								tooltip="<?php ___('feeds.offset_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('feeds.offset'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="item-offset"
								class="labeled"
								type="number"
								ng-model="selectedItem.offset">
						</div>

					</div>

					<hr class="thin">

					<h3
						tooltip="{{ selectedItem.query | json }}"
						tooltip-popup-delay="333">
						<i class="icon-search"></i> Query
					</h3>

					<div class="pw-row">
						<div class="pw-col-3">
							<label
								for="query-post_type"
								class="inner">
								<?php ___('query.post_type'); ?>
							</label>
							<select
								id="query-post_type"
								class="labeled"
								ng-options="key as value for (key, value) in feedOptions.query.post_type"
								ng-model="selectedItem.query.post_type"
								multiple>
							</select>
						</div>
						<div class="pw-col-3">
							<label
								for="query-post_status"
								class="inner">
								<?php ___('query.post_status'); ?>
							</label>
							<select
								id="query-post_status"
								class="labeled"
								ng-options="item.slug as item.name for item in feedOptions.query.post_status"
								ng-model="selectedItem.query.post_status">
							</select>
						</div>
						<div class="pw-col-3">
							<label
								for="query-post_class"
								class="inner">
								<?php ___('query.post_class'); ?>
							</label>
							<select
								id="query-post_class"
								class="labeled"
								ng-options="key as value for (key, value) in postClassOptions()"
								ng-model="selectedItem.query.post_class">
								<option value="">Any</option>
							</select>
						</div>
						<div class="pw-col-3">
							<label
								for="query-offset"
								class="inner"
								tooltip="<?php ___('query.offset_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('query.offset'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="query-offset"
								class="labeled"
								type="number"
								ng-model="selectedItem.query.offset">
						</div>
						<div class="pw-col-3">
							<label
								for="query-orderby"
								class="inner">
								<?php ___('query.orderby'); ?>
							</label>
							<select
								id="query-orderby"
								class="labeled"
								ng-options="item.slug as item.name for item in feedOptions.query.orderby"
								ng-model="selectedItem.query.orderby">
								
							</select>
						</div>
						<div class="pw-col-3">
							<label
								for="query-order"
								class="inner">
								<?php ___('query.order'); ?>
							</label>
							<select
								id="query-order"
								class="labeled"
								ng-options="item.slug as item.name for item in feedOptions.query.order"
								ng-model="selectedItem.query.order">
							</select>
						</div>
						<div class="pw-col-3">
							<label
								for="query-posts_per_page"
								class="inner"
								tooltip="<?php ___('query.posts_per_page_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('query.posts_per_page'); ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="query-posts_per_page"
								class="labeled"
								type="number"
								ng-model="selectedItem.query.posts_per_page">
						</div>

						<div class="pw-col-3">
							<label
								for="query-event_filter"
								class="inner">
								<i class="icon-calendar"></i>
								<?php ___('query.event_filter'); ?>
							</label>
							<select
								id="query-event_filter"
								class="labeled"
								ng-options="item.value as item.name for item in feedOptions.query.event_filter"
								ng-model="selectedItem.query.event_filter">
								<option value=""><?php ___('general.none'); ?></option>
							</select>
						</div>

					</div>

					<div class="pw-row">
						<div class="pw-col-3">
							<label
								for="query-post_parent_from"
								class="inner">
								<i class="icon-flow-children"></i>
								<?php ___('query.post_parent'); ?>
							</label>
							<select
								id="query-post_parent_from"
								class="labeled"
								ng-options="item.value as item.name for item in feedOptions.query.post_parent_from"
								ng-model="selectedItem.query.post_parent_from"
								tooltip="{{ selectOptionObj( 'query.post_parent_from' ).description }}"
								tooltip-placement="bottom">
								<option value=""><?php ___('general.none'); ?></option>
							</select>
						</div>

						<div class="pw-col-3" ng-show="selectedItem.query.post_parent_from == 'post_id'">
							<label
								for="query-post_parent_id"
								class="inner"
								tooltip="<?php ___('query.post_parent_id_info'); ?>"
								tooltip-popup-delay="333">
								<?php ___('query.post_parent_id'); ?>
							</label>
							<input
								id="query-post_parent_id"
								class="labeled"
								type="number"
								ng-model="selectedItem.query.post_parent">

						</div>

						<div class="pw-col-3">
							<label
								for="query-exclude_posts_from"
								class="inner">
								<?php ___('query.exclude_posts'); ?>
							</label>
							<select
								id="query-exclude_posts_from"
								class="labeled"
								ng-options="item.value as item.name for item in feedOptions.query.exclude_posts_from"
								ng-model="selectedItem.query.exclude_posts_from"
								tooltip="{{ selectOptionObj( 'query.exclude_posts_from' ).description }}"
								tooltip-placement="bottom">
								<option value=""><?php ___('general.none'); ?></option>
							</select>
						</div>

						<div class="pw-col-3">
							<label
								for="query-include_posts_from"
								class="inner">
								<?php ___('query.include_posts'); ?>
							</label>
							<select
								id="query-include_posts_from"
								class="labeled"
								ng-options="item.value as item.name for item in feedOptions.query.include_posts_from"
								ng-model="selectedItem.query.include_posts_from"
								tooltip="{{ selectOptionObj( 'query.include_posts_from' ).description }}"
								tooltip-placement="bottom">
								<option value=""><?php ___('general.none'); ?></option>
							</select>
						</div>

					</div>

					<div class="space-2"></div>
					
					<hr class="thin">
					
					<h3><i class="icon-cube"></i> <?php ___('feeds.view.title'); ?></h3>
					<?php echo pw_feed_template_options( array( 'ng_model' => 'selectedItem' ) ); ?>
					
					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button( PW_OPTIONS_FEEDS,'pwFeeds'); ?></div>
		
					<!-- DELETE BUTTON -->
					<button
						class="button deletion"
						ng-click="deleteItem(selectedItem,'pwFeeds')">
						<i class="icon-close"></i>
						<?php ___('feeds.delete'); ?>
					</button>

					<!-- DUPLICATE BUTTON -->
					<button
						class="button deletion"
						ng-click="duplicateItem(selectedItem,'pwFeeds')">
						<i class="icon-copy-2"></i>
						<?php ___('feeds.duplicate'); ?>
					</button>

				</div>
			</div>
		</div>

		<hr>

		<hr class="thick">

		<!--
		<pre>pwFeedSettings : {{ pwFeedSettings | json }}</pre>
		<pre>pwFeeds : {{ pwFeeds | json }}</pre>
		-->

		<!--
		RADIO BUTTONS
		<b><i class="icon-calendar"></i> Events Filter</b>
		<br>
		<div class="btn-group">
			<label
				ng-repeat="option in eventOptions.timeFilter"
				class="btn"
				ng-model="eventInput.timeFilter"
				btn-radio="option.value">
				{{ option.name }}
			</label>
		</div>
		-->



	</div>

</div>