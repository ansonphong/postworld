<?php

	$iFeeds = i_get_option( array( 'option_name' => 'i-feeds' ) );

?>
<script>
	infinite.controller( 'pwFeedsDataCtrl', [ '$scope', function( $scope ){

		$scope.iFeeds = <?php echo json_encode( $iFeeds ); ?>;

	}]);
</script>

<div id="infinite_admin" ng-app="infinite" class="postworld feeds wrap">
	<div
		i-admin-feeds
		ng-controller="pwFeedsDataCtrl"
		ng-cloak>

		<!-- SAVE BUTTON -->
		<div class="save-right"><?php i_save_option_button('i-feeds','iFeeds'); ?></div>

		
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
						ng-repeat="feed in iFeeds"
						ng-click="editFeed(feed)">
						{{ feed.name }}
					</li>
				</ul>
			</div>

			<!-- ///// EDIT FEED ///// -->
			<div class="pw-col-9">
				<div ng-show="editingFeed.id">

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

					<hr class="thin">

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
						<div class="pw-col-3">
							<label
								class="inner"
								tooltip="Maximum number of posts"
								tooltip-popup-delay="333">
								Maxiumum Posts <i class="icon-info-circle"></i>
							</label>
							<input
								class="labeled"
								type="number"
								ng-model="editingFeed.query.posts_per_page">
						</div>
					</div>

					<hr class="thin">
					
					<h3><i class="icon-cube"></i> Template</h3>

					<label>View :</label>
					<select
						ng-model="editingFeed.view.current"
						ng-options="value for value in feedOptions.view">
						
					</select>
					<hr class="thin">

					<h3><i class="icon-search"></i> Query</h3>

					<div class="pw-row">
						<div class="pw-col-3">
							<label
								class="inner"
								tooltip="How many posts to skip at the Query level"
								tooltip-popup-delay="333">
								Offset <i class="icon-info-circle"></i>
							</label>
							<input
								class="labeled"
								type="number"
								ng-model="editingFeed.query.offset">
						</div>
					</div>


					<hr class="thin">

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
		<pre>iFeeds : {{ iFeeds | json }}</pre>
		<pre>feedOptions : {{ feedOptions | json }}</pre>

	</div>

</div>