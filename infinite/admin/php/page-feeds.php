<?php
	$iFeeds = i_get_option( array( 'option_name' => 'i-feeds' ) );
	$iFeedSettings = i_get_option( array( 'option_name' => 'i-feed-settings' ) );
?>
<script>
	infinite.controller( 'pwFeedsDataCtrl', [ '$scope', function( $scope ){
		$scope.iFeeds = <?php echo json_encode( $iFeeds ); ?>;
		$scope.iFeedSettings = <?php echo json_encode( $iFeedSettings ); ?>;
	}]);
</script>

<div id="infinite_admin" ng-app="infinite" class="postworld feeds wrap">
	<div
		i-admin
		i-admin-feeds
		ng-controller="pwFeedsDataCtrl"
		ng-cloak>
		
		<h2>
			<i class="icon-th-small"></i>
			Feeds
			<button class="add-new-h2" ng-click="newFeed()">Add New Feed</button>
		</h2>

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
						ng-repeat="item in iFeeds"
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
					
					<h3>Loading Icon</h3>

					<!-- DROPDOWN -->
					<span
						class="dropdown">
						<span
							dropdown-toggle
							class="area-select-icon">
							<i class="{{ iFeedSettings.loading_icon }} icon-spin"></i>
						</span>
						<ul class="dropdown-menu grid" role="menu" aria-labelledby="dLabel" >
							<li
								class="select-icon"
								ng-repeat="icon in feedSettingsOptions.loadingIcon"
								ng-click="iFeedSettings.loading_icon = icon">
								<i
									class="{{ icon }}"></i>
							</li>

						</ul>
					</span>

					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button('i-feed-settings','iFeedSettings'); ?></div>
		
				</div>


				<!-- ///// EDIT SETTINGS ///// -->
				<div ng-show="showView('editItem')">

					<h3><i class="icon-gear"></i> Feed Settings</h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label
								for="item-name"
								class="inner">
								Feed Name
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
								tooltip="Must be unique"
								tooltip-popup-delay="333">
								Feed ID <i class="icon-info-circle"></i>
							</label>
							<button
								class="inner inner-bottom-right inner-controls"
								ng-click="enableInput('#item-id');focusInput('#item-id')"
								tooltip="Editing the ID may cause instances of the feed to disappear"
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
								ng-blur="disableInput('#item-id')">
						</div>
					</div>

					<div class="pw-row">
						<div class="pw-col-3">
							<label
								for="item-preload"
								class="inner"
								tooltip="How many posts to preload"
								tooltip-popup-delay="333">
								Preload <i class="icon-info-circle"></i>
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
								tooltip="How many posts to load each infinite scroll"
								tooltip-popup-delay="333">
								Load Increment <i class="icon-info-circle"></i>
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
								tooltip="How many posts to skip at the UI level"
								tooltip-popup-delay="333">
								Offset <i class="icon-info-circle"></i>
							</label>
							<input
								id="item-offset"
								class="labeled"
								type="number"
								ng-model="selectedItem.offset">
						</div>

					</div>

					<hr class="thin">

					<h3><i class="icon-search"></i> Query</h3>

					<div class="pw-row">
						<div class="pw-col-3">
							<label
								for="query-post_type"
								class="inner">
								Post Type
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
								Post Status
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
								Post Class
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
								tooltip="How many posts to skip at the Query level"
								tooltip-popup-delay="333">
								Offset <i class="icon-info-circle"></i>
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
								Order By
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
								Order
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
								tooltip="Maximum number of posts"
								tooltip-popup-delay="333">
								Maxiumum Posts <i class="icon-info-circle"></i>
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
								<i class="icon-calendar"></i>  Event Filter
							</label>
							<select
								id="query-event_filter"
								class="labeled"
								ng-options="item.value as item.name for item in feedOptions.query.event_filter"
								ng-model="selectedItem.query.event_filter">
								<option value="">None</option>
							</select>
						</div>

					</div>

					<div class="space-2"></div>
					
					<hr class="thin">
					
					<h3><i class="icon-cube"></i> Template</h3>
					<div class="pw-row">
						<div class="pw-col-3">
							<label
								for="feed_view"
								class="inner">
								View
							</label>
							<select
								id="feed_view"
								class="labeled"
								ng-model="selectedItem.view.current"
								ng-options="value for value in feedOptions.view">
								
							</select>
						</div>
						<div class="pw-col-3">
							<label
								for="feed_view_options"
								class="inner">
								View Options
							</label>
							<select
								id="feed_view_options"
								class="labeled"
								ng-model="selectedItem.view.options"
								ng-options="value for value in feedOptions.view"
								multiple>
								<option value="">None</option>
							</select>
						</div>
					</div>
					
					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button('i-feeds','iFeeds'); ?></div>
		
					<!-- DELETE BUTTON -->
					<button
						class="button deletion"
						ng-click="deleteItem(selectedItem,'iFeeds')">
						<i class="icon-close"></i>
						Delete Feed
					</button>

					<!-- DELETE BUTTON -->
					<button
						class="button deletion"
						ng-click="duplicateItem(selectedItem,'iFeeds')">
						<i class="icon-copy-2"></i>
						Duplicate Feed
					</button>

				</div>
			</div>
		</div>

		<hr>

		<hr class="thick">
		<pre>iFeedSettings : {{ iFeedSettings | json }}</pre>
		<pre>iFeeds : {{ iFeeds | json }}</pre>
		

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