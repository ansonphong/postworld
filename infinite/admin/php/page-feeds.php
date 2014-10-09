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
		i-admin-feeds
		ng-controller="pwFeedsDataCtrl"
		ng-cloak>

		
		<h2>
			<i class="icon-star"></i>
			Feeds
			<button class="add-new-h2" ng-click="newFeed()">Add New Feed</button>
		</h2>

		<hr class="thick">

		<div class="pw-row">
			<!-- ///// FEEDS MENU ///// -->
			<div class="pw-col-3">
				<ul class="list-menu">

					<li
						ng-click="editFeed('settings');"
						ng-class="menuClass('settings')">
						<i class="icon-gear"></i> Settings
					</li>

					<li
						ng-repeat="feed in iFeeds"
						ng-click="editFeed(feed)"
						ng-class="menuClass(feed)">
						{{ feed.name }}
					</li>

				</ul>

				<div class="space-6"></div>

			</div>



			<!-- ///// EDIT FEED ///// -->
			<div class="pw-col-9">

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
								ng-click="selectItem( 'iFeedSettings', 'loading_icon', icon )">
								<i
									class="{{ icon }}"></i>
							</li>

						</ul>
					</span>

					<hr class="thick">

					<!-- SAVE BUTTON -->
					<div class="save-right"><?php i_save_option_button('i-feed-settings','iFeedSettings'); ?></div>
		

				</div>

				<div ng-show="showView('editFeed')">

					<h3><i class="icon-gear"></i> Feed Settings</h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label class="inner">
								Feed Name
							</label>
							<input
								class="labeled"
								type="text"
								ng-model="editingFeed.name">
						</div>
						<div class="pw-col-6">
							<label
								class="inner"
								tooltip="Must be unique"
								tooltip-popup-delay="333">
								Feed ID <i class="icon-info-circle"></i>
							</label>
							<input
								class="labeled"
								type="text"
								ng-model="editingFeed.id">
						</div>
					</div>

					<div class="pw-row">
						<div class="pw-col-3">
							<label
								class="inner"
								tooltip="How many posts to preload"
								tooltip-popup-delay="333">
								Preload <i class="icon-info-circle"></i>
							</label>
							<input
								class="labeled"
								type="number"
								ng-model="editingFeed.preload">
						</div>
						<div class="pw-col-3">
							<label
								class="inner"
								tooltip="How many posts to load each infinite scroll"
								tooltip-popup-delay="333">
								Load Increment <i class="icon-info-circle"></i>
							</label>
							<input
								class="labeled"
								type="number"
								ng-model="editingFeed.load_increment">
						</div>
						<div class="pw-col-3">
							<label
								class="inner"
								tooltip="How many posts to skip at the UI level"
								tooltip-popup-delay="333">
								Offset <i class="icon-info-circle"></i>
							</label>
							<input
								class="labeled"
								type="number"
								ng-model="editingFeed.offset">
						</div>

					</div>

					<hr class="thin">

					<h3><i class="icon-search"></i> Query</h3>

					<div class="pw-row">
						<div class="pw-col-3">
							<label class="inner">
								Post Type
							</label>
							<select
								class="labeled"
								ng-options="key as value for (key, value) in feedOptions.query.post_type"
								ng-model="editingFeed.query.post_type"
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
								ng-model="editingFeed.query.post_status">
							</select>
						</div>
						<div class="pw-col-3">
							<label class="inner">
								Post Class
							</label>
							<select
								class="labeled"
								ng-options="key as value for (key, value) in postClassOptions()"
								ng-model="editingFeed.query.post_class">
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
								ng-model="editingFeed.query.offset">
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
								ng-model="editingFeed.query.orderby">
								
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
								ng-model="editingFeed.query.order">
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
								ng-model="editingFeed.query.posts_per_page">
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
								ng-model="editingFeed.query.event_filter">
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
								ng-model="editingFeed.view.current"
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
								ng-model="editingFeed.view.options"
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
						ng-click="deleteFeed(editingFeed)">
						<i class="icon-close"></i>
						Delete Feed
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