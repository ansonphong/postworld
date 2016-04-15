<?php
/*
  ____      _       _           _   ____           _       
 |  _ \ ___| | __ _| |_ ___  __| | |  _ \ ___  ___| |_ ___ 
 | |_) / _ \ |/ _` | __/ _ \/ _` | | |_) / _ \/ __| __/ __|
 |  _ <  __/ | (_| | ||  __/ (_| | |  __/ (_) \__ \ |_\__ \
 |_| \_\___|_|\__,_|\__\___|\__,_| |_|   \___/|___/\__|___/
                                                           
////////////////////// RELATED POSTS //////////////////////*/?>

<?php
	$instance = 'pwRelatedPosts_'.pw_random_string(8);
	if ( isset( $options[ 'title' ] ) ) {
		  $title = $options[ 'title' ];
		}
		else {
		  $title = __( 'Widget', 'text_domain' );
		}
	extract($options);

	$template_options = pw_template_options( 'related_posts' );
?>

<!--///// METABOX SCRIPTS /////-->
<script>

	console.log( 'Init Related Posts Widget Script : ' + '<?php echo $instance ?>' );

	///// CONTROLLER /////
	postworldAdmin.controller('<?php echo $instance ?>',
		function( $scope, $pwData, $_, $log ) {
			$log.debug( 'Init Related Posts Widget Controller : ' + '<?php echo $instance ?>' );
			$scope.settings = <?php echo json_encode( $options['settings'] ); ?>;
			$scope.templateOptions = <?php echo json_encode( $template_options ); ?>;

			$scope.addRelatedByClause = function( type ){
				switch( type ){
					case 'taxonomy':
						var newClause = {
							//id:$_.randomString(8,['numbers','lowercase']),
							type:'taxonomy',
							weight:1,
							taxonomies:[
								{
									taxonomy: 'post_tag',
									weight:1
								}
							]
						};
						break;
				}
				$scope.settings.related_by.push(newClause);
			}

			$scope.removeRelatedByClause = function( clause ){
				if( $_.get( $scope, 'settings.related_by' ) === false )
					return false;
				$scope.settings.related_by = _.reject(
					$scope.settings.related_by,
					function( thisClause ){
						return thisClause == clause
					}
				);
			}

			$scope.addSubClause = function( clause ){
				var subClauseKey, defaultSubClause;
				switch( clause.type ){
					case 'taxonomy':
						subClauseKey = 'taxonomies';
						defaultSubClause = {
							taxonomy: 'post_tag',
							weight:1
						};
						break;
					case 'field':
						subClauseKey = 'fields';
						defaultSubClause = {
							field: 'post_author',
							weight:1
						};
						break;
				}
				clause[subClauseKey].push( defaultSubClause );
			}

			$scope.removeSubClause = function( clause, subClause ){
				var subClauseKey = $scope.getSubClauseKey(clause);

				clause[subClauseKey] = _.reject(
					clause[subClauseKey],
					function( thisSubclause ){
						return thisSubclause == subClause
					}
				);

			}

			$scope.getSubClauseKey = function( clause ){
				switch( clause.type ){
					case 'taxonomy':
						return 'taxonomies';
						break;
					case 'field':
						return 'fields';
						break;
				}
			}
			
	});

	pwRegisterController( '<?php echo $instance ?>', 'postworldAdmin' );
	pwCompileElement( 'body', '<?php echo $instance ?>' );

</script>

<div
	id="<?php echo $instance ?>"
	class="postworld admin-widget"
	pw-globals="pw">
	<div
		class="postworld-widget postworld-widget-related-posts"
		pw-feed-options
		ng-controller="<?php echo $instance ?>">

		<!-- TITLE -->
		<label class="inner" for="<?php echo $this->get_field_id( 'show_title' ); ?>">
			<input
				type="checkbox"
				value="1"
				title="Show Title"
				name="<?php echo $this->get_field_name('show_title'); ?>"
				id="<?php echo $this->get_field_id('show_title'); ?>"
				<?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
			<?php _e( 'title', 'title of widget', 'postworld' ); ?>
		</label>
		<input
			class="labeled"
			id="<?php echo $this->get_field_id( 'title' ); ?>"
			name="<?php echo $this->get_field_name( 'title' ); ?>"
			type="text" value="<?php echo esc_attr( $title ); ?>" />
		<hr class="thin">


		<div class="row gutter-sm">
			<div class="col-sm-6">
				<label class="inner">
					<?php _e( 'Number', 'postworld' ) ?>
				</label>
				<input
					type="number"
					class="labeled"
					ng-model="settings.number">
				<small><?php _e( 'The number of related posts to show', 'postworld' ) ?></small>
			</div>
			<div class="col-sm-6">
				<label class="inner">
					<?php _e( 'Depth', 'postworld' ) ?>
				</label>
				<input
					type="number"
					class="labeled"
					ng-model="settings.depth">
				<small><?php _e( 'Higher number for slower, more accurate results, 0 searches all posts', 'postworld' ) ?></small>
			</div>
		</div>

		<hr class="thin">

		<label
			for="query-post_type"
			class="inner">
			<?php _e( 'Post Type', 'postworld' ) ?>
		</label>
		<select
			id="query-post_type"
			class="labeled"
			ng-options="key as value for (key, value) in feedOptions.query.post_type"
			ng-model="settings.query.post_type"
			multiple>
		</select>

		<hr class="thin">

		<div class="row gutter-sm">
			<div class="col-sm-3">
				<label class="inner">
					<?php _ex( 'Within', 'within X period of time', 'postworld' ) ?>
				</label>
				<input
					type="number"
					class="labeled"
					ng-model="settings.query.date_from.after_ago.multiplier">
			</div>
			<div class="col-sm-9">
				<label class="inner">
				<?php _ex( 'Period', 'time period', 'postworld' ) ?>
				</label>
				<select
					class="labeled"
					ng-model="settings.query.date_from.after_ago.period">
					<option value="year"><?php _e( 'Year(s)', 'postworld' ) ?></option>
					<option value="month"><?php _e( 'Month(s)', 'postworld' ) ?></option>
					<option value="day"><?php _e( 'Day(s)', 'postworld' ) ?></option>
				</select>
			</div>
		</div>

		<hr class="thin">

		<label
			for="select-view"
			class="inner">
			<?php _e( 'View', 'postworld' ) ?>
		</label>
		<select
			id="select-view"
			class="labeled"
			ng-options="value.name as value.label for value in templateOptions"
			ng-model="settings.view">
		</select>

		<hr class="thin">

		<button
			type="button"
			class="button button-primary"
			ng-click="addRelatedByClause('taxonomy')">
			<i class="pwi-plus-thin"></i>
			<?php _e( 'Taxonomies', 'postworld' ) ?>
		</button>

		<!--///// RELATED BY CLAUSES /////-->
		<div ng-repeat="clause in settings.related_by" class="well">

			<div class="float-right relative" style="top:-5px; left:5px;">
				<button
					type="button"
					class="button"
					ng-click="addSubClause(clause)">
					<i class="pwi-plus-thin"></i>
					<?php _ex( 'Sub Clause', 'search clause', 'postworld' ) ?>
				</button>
				<button
					type="button"
					class="button"
					ng-click="removeRelatedByClause(clause)">
					<i class="pwi-close-thin"></i>
				</button>
			</div>

			<h3>
				<i class="pwi-circle-thin"></i> <b>{{ clause.type }}</b>
			</h3>

			<div class="clearfix"></div>

			<!--/// SUB CLAUSES ///-->
			<div
				ng-repeat="subClause in clause[getSubClauseKey(clause)]"
				class="well relative">

				<div class="row gutter-sm">
					<div class="col-sm-6">
						<label class="inner"><?php _e( 'Taxonomy', 'postworld' ) ?></label>
						<select
							id="query-post_type"
							class="labeled"
							ng-options="value.name as value.labels.singular_name for (key, value) in pw.taxonomies"
							ng-model="subClause.taxonomy">
						</select>

					</div>
					<div class="col-sm-6 relative">
						<button
							type="button"
							class="button inner inner-right"
							style="z-index:2;right:4px;"
							ng-click="removeSubClause(clause,subClause)">
							<i class="pwi-close-thin"></i>
						</button>
						<label class="inner"><?php _ex( 'Weight', 'weight as in search relavance', 'postworld' ) ?></label>
						<input
							type="number"
							class="labeled"
							ng-model="subClause.weight">
					</div>
				</div>

			</div>

		</div>

		<?php if( pw_dev_mode() ): ?>
			<hr>
			<pre><code>{{ settings | json }}</code></pre>
		<?php endif; ?>
		
		<!-- HIDDEN INPUTS -->
		<input
			type="hidden"
			name="<?php echo $this->get_field_name( 'settings' ); ?>"
			value="{{ settings | json }}">

	</div>
</div>


