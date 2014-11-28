<?php
	///// GET DATA /////
	// Feeds
	$pwTermFeeds = pw_get_option( array( 'option_name' => PW_OPTIONS_TERM_FEEDS ) );
	// Feed Settings
	//$pwFeedSettings = i_get_option( array( 'option_name' => PW_OPTIONS_FEED_SETTINGS ) );

	// Term Feed Templates
	$termFeedTemplates = pw_get_templates(
		array(
			'subdirs' => array('term-feeds'),
			'path_type' => 'url',
			'ext'=>'php',
			)
		)['term-feeds'];
?>
<script>
	postworldAdmin.controller( 'pwTermFeedsDataCtrl', [ '$scope', function( $scope ){
		$scope.pwTermFeeds = <?php echo json_encode( $pwTermFeeds ); ?>;
		//$scope.pwFeedSettings = <?php //echo json_encode( $pwFeedSettings ); ?>;
		$scope.termFeedTemplates = <?php echo json_encode( $termFeedTemplates ); ?>;
	}]);
</script>

<div ng-app="postworldAdmin" class="postworld feeds wrap" ng-cloak>
	<div
		pw-admin
		pw-admin-term-feeds
		ng-controller="pwTermFeedsDataCtrl"
		ng-cloak>
		
		<h1>
			<i class="icon-tag"></i>
			Term Feeds
			<button class="add-new-h2" ng-click="newTermFeed()">Add New Term Feed</button>
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

					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button( PW_OPTIONS_FEED_SETTINGS,'pwFeedSettings'); ?></div>
		
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
					<div class="pw-row">

						<div class="pw-col-3">
							<label
								for="item-template"
								class="inner">
								<?php ___('feeds.template'); ?>
							</label>
							<select
								id="item-template"
								class="labeled"
								ng-model="selectedItem.template"
								ng-options="key as key for (key, value) in termFeedTemplates">
							</select>
						</div>

					</div>
					
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