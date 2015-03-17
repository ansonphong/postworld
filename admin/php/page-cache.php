<?php
	// DEV TEST CACHE INPUT
	$data = array(
		'cache_type' => 'test',
		'cache_name' => pw_random_string(8),
		'cache_hash' => pw_random_hash(16),
		'cache_content' => 'JSON'
		);
	//pw_set_cache( $data );
?>

<script type="text/javascript">
	//////////////////// DATA CONTROLLER ////////////////////
	postworldAdmin.controller('pwCacheDataCtrl',
		[ '$scope', '$window', '_', 'pwData',
		function( $scope, $window, $_, $pwData ){
		$scope.cacheTypeReadout = <?php echo json_encode(pw_get_cache_types_readout()); ?>;
	}]);
</script>

<div
	pw-admin
	pw-admin-cache
	ng-controller="pwCacheDataCtrl"
	ng-cloak
	pw-ui
	class="postworld">

	<h1>
		<i class="pwi-database"></i>
		Cache
	</h1>

	<hr class="thick">

	<!-- ADD "UPDATE" CACHE STATUS BUTTON - icon spins while updating -->
	<!-- ADD CLEAR ALL CACHES BUTTON -->

	<div class="row">
		<div class="col-md-6">

		<div class="well">
			
			<button
				type="button"
				class="button button-primary pull-right"
				ng-click="refreshCacheReadout()">
				<i
					class="pwi-refresh"
					ng-class="uiBoolClass(loading.cacheTypeReadout,'icon-spin')">
				</i>
			</button>

			<h3>Cache Status</h3>

			<div ng-show="cacheTypeReadoutIsEmpty()" class="well">
				<b>
					<i class="pwi-notification"></i>
					Cache is empty.
				</b>
			</div>

			<table
				ng-show="!cacheTypeReadoutIsEmpty()"
				class="wp-list-table widefat">
				<thead>
					<tr>
						<th width="33%">Type</th>
						<th width="33%">Count</th>
						<th width="33%">
							<button
								type="button"
								class="button"
								ng-click="truncateCache()"
								ng-disabled="loading.cacheTypeReadout">
								<i class="pwi-trash"></i>
								Delete All
							</button>
							
						</th>
					</tr>
				</thead>
				<tr ng-repeat="readout in cacheTypeReadout">
					<td class="column-title">
						<b>{{ readout.cache_type }}</b>
					</td>
					<td>
						{{ readout.type_count }}
					</td>
					<td>
						<button
							type="button"
							class="button"
							ng-click="deleteCacheType(readout.cache_type)"
							ng-disabled="loading.cacheTypeReadout">
							<i class="pwi-trash"></i>
							Delete Cache
						</button>
					</td>
				</tr>
			</table>

		</div>

		</div>
	</div>

	<hr class="thick">

	<?php if( pw_dev_mode() ): ?>


	<?php endif; ?>

</div>