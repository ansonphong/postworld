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

$progress = pw_get_progress();
if( empty( $progress ) )
	$progress = array( '_' => 0 );

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwDatabaseDataCtrl',
	'vars' => array(
		'cacheTypeReadout' => pw_get_cache_types_readout(),
		'progress' => $progress,
		),
	));

?>

<div
	pw-admin
	pw-admin-database
	ng-controller="pwDatabaseDataCtrl"
	ng-cloak
	pw-ui
	class="postworld">

	<h1>
		<i class="pwi-database"></i>
		<?php _e('Database', 'postworld') ?>
	</h1>

	<hr class="thick">

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
					<?php _e('Cache Status', 'postworld') ?>
				</h3>

				<div ng-show="cacheTypeReadoutIsEmpty()" class="well">
					<b>
						<i class="pwi-notification"></i>
						<?php _e('Cache is empty.', 'postworld') ?>
					</b>
				</div>

				<table
					ng-show="!cacheTypeReadoutIsEmpty()"
					class="wp-list-table widefat">
					<thead>
						<tr>
							<th width="33%"><?php _e('Type', 'postworld') ?></th>
							<th width="33%"><?php _ex('Count', 'noun', 'postworld') ?></th>
							<th width="33%">
								<button
									type="button"
									class="button"
									ng-click="truncateCache()"
									ng-disabled="busy.cacheTypeReadout">
									<i class="pwi-trash"></i>
									<?php _e('Delete All', 'postworld') ?>
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
								<?php _e('Delete Cache', 'postworld') ?>
							</button>
						</td>
					</tr>
				</table>

			</div>

			<?php if( pw_module_enabled( 'rank_score' ) ):?>
				<?php
				/**
				 * @todo Using rank_score to toggle this is an over-simplification
				 * @todo Activate each section by enabled modules
				 */
				?>
				<!-- POST RANK -->
				<div class="well">
					<h3>
						<i class="icon pwi-circle-medium"></i>
						<?php _e('Update Caches', 'postworld') ?>
					</h3>

					<?php /*
					<!--
					If it keeps spinning even after the maximum PHP process time,
					you may need to increase the maximum allowed process time in your PHP configuration.
					-->
					*/ ?>

					<!-- POSTMETA TABLE -->
					<div
						class="well"
						ng-repeat="xCacheType in xCacheTypes">

						<h3>
							<i class="icon" ng-class="xCacheType.icon"></i>
							{{xCacheType.title}}
						</h3>

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
							<i class="icon pwi-close"></i> <?php _e('Stop', 'postworld') ?>
						</button>

						<div ng-show="isBusy(xCacheType.functionName)">
							<div class="space-2"></div>
							<div class="progress-bar-container">
								<div
									class="progress-bar"
									ng-style="getPercentWidth( progress[xCacheType.functionName].items.current, progress[xCacheType.functionName].items.total  )">
								</div>
							</div>
							<!-- PRIMARY STATUS -->
							<div ng-show="uiBool(progress[xCacheType.functionName].items.current)">
								<b>{{ progress[xCacheType.functionName].items.current }}</b> / {{progress[xCacheType.functionName].items.total}}
								<span ng-show="uiBool(progress[xCacheType.functionName].meta.current_label)">
									( <b>{{ progress[xCacheType.functionName].meta.current_label }}</b> )
								</span>
							</div>
							<!-- META -->
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

						<?php /*
						<!--
						<div ng-show="isBusy(xCacheType.functionName)">
							<hr class="thin">
							<b>{{ progress[xCacheType.functionName] | json }}</b>
						</div>
						-->
						*/ ?>

					</div>

					<?php if( pw_dev_mode() ) : ?>
						<hr class="thick">
						<!--Clear Cron Logs (Show Row Count) (pw_clear_cron_logs)-->
						$scope.progress : <pre><code>{{ progress | json }}</code></pre>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>

		<div class="col-md-6 pad-col-md">

			<div class="well">

				<h3>
					<i class="pwi-lightning"></i>
					<?php _e('Cleanup Metadata', 'postworld') ?>
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
						<?php _e('Cleanup', 'postworld') ?>
						{{cleanupTable.name}}
					</button>
					<hr class="thin">
					<small>
						<?php _e('Delete orphaned rows in database table', 'postworld') ?> :
						<b>{{ cleanupTable.name }}</b> 
					</small>
					<div ng-show="uiBool( cleanupMetaReadout[cleanupTable.type] )">
						<hr class="thin">
						<?php _e('Time', 'postworld') ?> : <b>{{ cleanupMetaReadout[cleanupTable.type].timer }} seconds</b> //
						<?php _e('Cleaned items', 'postworld') ?> : <b>{{ cleanupMetaReadout[cleanupTable.type].cleaned_items_count }}</b> // 
						{{ cleanupMetaReadout[cleanupTable.type].cleaned_items | json }}
					</div>
				</div>

			</div>


			<div class="well">

				<h3>
					<i class="icon pwi-gear"></i>
					<?php _e('Taxonomy Operations', 'postworld') ?>
				</h3>

				<!-- POSTMETA TABLE -->
				<div
					class="well"
					ng-repeat="taxOp in taxOps">
					<button
						type="button"
						class="button"
						ng-click="doTaxOp( taxOp.type )"
						ng-disabled="uiBool(busy['taxOp_'+taxOp.type])">
						<span class="icon-sm">
							<i
								class="icon pwi-refresh"
								ng-class="uiBoolClass(busy['taxOp_'+taxOp.type],'icon-spin')">
							</i>
						</span>
						{{ taxOp.title }}
					</button>
					<hr class="thin">
					<small> {{ taxOp.description }} </small>
					<div ng-show="uiBool( taxOpReadout[taxOp.type] )">
						<hr class="thin">
						<?php _e('Time', 'postworld') ?> : <b>{{ taxOpReadout[taxOp.type].timer }} seconds</b> //
						<?php _ex('Total Rows', 'database rows', 'postworld') ?> : <b>{{ taxOpReadout[taxOp.type].total_terms }}</b> // 
						<?php _ex('Repaired Rows', 'database rows', 'postworld') ?> : <b>{{ taxOpReadout[taxOp.type].count }}</b> // 
						{{ taxOpReadout[taxOp.type].items | json }}
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