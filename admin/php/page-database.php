<?php
	global $wpdb;
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
	postworldAdmin.controller('pwDatabaseDataCtrl',
		[ '$scope', '$window', '_', 'pwData',
		function( $scope, $window, $_, $pwData ){
		$scope.cacheTypeReadout = <?php echo json_encode(pw_get_cache_types_readout()); ?>;
		$scope.progress = <?php echo json_encode( pw_get_progress() ) ?>;
		if( _.isEmpty( $scope.progress ) )
			$scope.progress = {};

	}]);
</script>

<div
	pw-admin
	pw-admin-database
	ng-controller="pwDatabaseDataCtrl"
	ng-cloak
	pw-ui
	class="postworld">

	<h1>
		<i class="pwi-database"></i>
		Database
	</h1>

	<hr class="thick">

	<!-- ADD "UPDATE" CACHE STATUS BUTTON - icon spins while updating -->
	<!-- ADD CLEAR ALL CACHES BUTTON -->

	<div class="row">
		<div class="col-md-6 pad-col-md">

			<div class="well">
				
				<button
					type="button"
					class="button button-primary pull-right"
					ng-click="refreshCacheReadout()">
					<i
						class="pwi-refresh"
						ng-class="uiBoolClass(busy.cacheTypeReadout,'icon-spin')">
					</i>
				</button>

				<h3>
					<i class="pwi-cube"></i>
					Cache : Status
				</h3>

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
									ng-disabled="busy.cacheTypeReadout">
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
								ng-disabled="busy.cacheTypeReadout">
								<i class="pwi-trash"></i>
								Delete Cache
							</button>
						</td>
					</tr>
				</table>

			</div>

			<?php if( pw_module_enabled( 'rank_score' ) ):?>
				<!-- POST RANK -->
				<div class="well">
					<h3>
						<i class="pwi-bars"></i>
						Rank Score
					</h3>

					If it keeps spinning even after the maximum PHP process time,
					you may need to increase the maximum allowed process time in your PHP configuration.

					<!-- POSTMETA TABLE -->

					What's happening now is that if the process gets cut-off mid-stream, the pgress reamins stuck

					<div
						class="well"
						ng-repeat="xCacheType in xCacheTypes">
						<button
							type="button"
							class="button"
							ng-click="initProgressFunction( xCacheType.functionName )"
							ng-disabled="uiBool(busy[ xCacheType.functionName])">
							<span class="icon-sm">
								<i
									class="icon pwi-refresh"
									ng-class="uiBoolClass(busy[xCacheType.functionName],'icon-spin')">
								</i>
							</span>
							{{xCacheType.label}}
						</button>

						<button
							class="button"
							type="button"
							ng-show="isBusy(xCacheType.functionName)"
							ng-click="endProgress( xCacheType.functionName )">
							<i class="icon pwi-close"></i> Stop
						</button>

						<div ng-show="isBusy(xCacheType.functionName)">
							<div class="progress-bar-container">
								<div
									class="progress-bar"
									ng-style="getPercentWidth( progress[xCacheType.functionName].items.current, progress[xCacheType.functionName].items.total  )">
								</div>
							</div>
							<div ng-show="uiBool(progress[xCacheType.functionName].items.current)">
								<b>{{ progress[xCacheType.functionName].items.current }}</b> / {{progress[xCacheType.functionName].items.total}}
								<span ng-show="uiBool(progress[xCacheType.functionName].meta.current_label)">
									( <b>{{ progress[xCacheType.functionName].meta.current_label }}</b> )
								</span>
							</div>
							<div ng-show="uiBool( progress[xCacheType.functionName].meta.current )">
								<div class="progress-bar-container">
									<div
										class="progress-bar"
										ng-style="getPercentWidth( progress[xCacheType.functionName].meta.current, progress[xCacheType.functionName].meta.total  )">
									</div>
								</div>
								<div ng-show="uiBool(progress[xCacheType.functionName].meta.current)">
									<b>{{ progress[xCacheType.functionName].meta.current }}</b> / {{progress[xCacheType.functionName].meta.total}}
								</div>
							</div>

						</div>

						<div ng-show="isBusy(xCacheType.functionName)">
							<hr class="thin">
							<b>{{ progress[xCacheType.functionName] | json }}</b>
						</div>

					</div>

					<!--Clear Cron Logs (Show Row Count) (pw_clear_cron_logs)-->

					PROGRESS : <pre><code>{{ progress | json }}</code></pre>

				</div>
			<?php endif; ?>

		</div>

		<div class="col-md-6 pad-col-md">

			<div class="well">

				<h3>
					<i class="pwi-lightning"></i>
					Cleanup Metadata
				</h3>

				<!-- POSTMETA TABLE -->
				<div
					class="well"
					ng-repeat="cleanupTable in cleanupTables">
					<button
						type="button"
						class="button"
						ng-click="cleanupMeta( cleanupTable.type )"
						ng-disabled="uiBool(busy['cleanup_'+cleanupTable.type])">
						<span class="icon-sm">
							<i
								class="icon pwi-refresh"
								ng-class="uiBoolClass(busy['cleanup_'+cleanupTable.type],'icon-spin')">
							</i>
						</span>
						Cleanup {{cleanupTable.name}}
					</button>
					<hr class="thin">
					<small>Delete orphaned rows in the <b>{{ cleanupTable.name }}</b> database table</small>
					<div ng-show="uiBool( cleanupMetaReadout[cleanupTable.type] )">
						<hr class="thin">
						Time : <b>{{ cleanupMetaReadout[cleanupTable.type].timer }} seconds</b> //
						Cleaned items: <b>{{ cleanupMetaReadout[cleanupTable.type].cleaned_items_count }}</b> // 
						{{ cleanupMetaReadout[cleanupTable.type].cleaned_items | json }}
					</div>
				</div>

			</div>

		</div>

	</div>


	<?php if( pw_dev_mode() ) : ?>
		<hr class="thick">
		<div class="pw-dev well">
			<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
			<div class="well">
				<h3>$scope.busy</h3>
				<pre><code>{{ busy | json }}</code></pre>
			</div>
			<div class="well">
				<h3>$scope.cacheTypeReadout</h3>
				<pre><code>{{ cacheTypeReadout | json }}</code></pre>
			</div>
			<div class="well">
				<h3>$scope.cleanupMetaReadout</h3>
				<pre><code>{{ cleanupMetaReadout | json }}</code></pre>
			</div>
	<?php endif; ?>

</div>