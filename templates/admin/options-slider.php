<?php
	if( !isset( $vars['ng_model'] ) )
		$vars['ng_model'] = "pwMeta.header.slider";
	$vars['instance'] = 'sliderOptions_' . pw_random_string();
?>

<?php

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => $vars["instance"],
	'vars' => array(
		'sliderOptions' => array(
			'slider' => array(
				'mode' => array(
					array(
						'slug' => 'this_post',
						'name' => 'This Post',
						),
					array(
						'slug' => 'query',
						'name' => 'Query',
						),
					array(
						'slug' => 'menu',
						'name' => 'Menu',
						),
					),
				'transition' => array(
					array(
						'slug' => false,
						'name' => 'No',
						),
					array(
						'slug' => 'fade',
						'name' => 'Fade',
						),
					array(
						'slug' => 'slide',
						'name' => 'Slide',
						),
					),

				),
			),
		),
	));

?>

<script>
	///// CONTROLLER /////
	jQuery( document ).ready(function() {

		postworldAdmin.directive('pwAdminSliderOptions', function( $log, $pwPostOptions, $_ ){
			return{
				link: function( $scope, element, attrs ){
					// Get tax outline by AJAX
					$pwPostOptions.taxTerms( $scope, 'tax_terms' );

					// Watch value : pwMeta.header.slider.mode
					$scope.$watch('<?php echo $vars["ng_model"] ?>.mode', function(value){
						// Switch Query Vars
						switch( value ){
							case 'this_post':
								$scope.<?php echo $vars["ng_model"] ?>.query_vars.this_post = true;
								$scope.<?php echo $vars["ng_model"] ?>.query_vars.this_post_only = true;
								break;

							case 'query':
								$scope.<?php echo $vars["ng_model"] ?>.query_vars.this_post_only = false;
								break;

						}


					});
				}
			}

		});

	});
</script>

<div pw-admin-slider-options ng-controller="<?php echo $vars["instance"] ?>">
	<div class="well">
		<h3>
			<i class="pwi-gear"></i>
			<?php _e( 'Get Slides from', 'postworld' ) ?>
		</h3>
		<div class="btn-group">
			<label
				ng-repeat="type in sliderOptions.slider.mode"
				class="btn"
				ng-model="<?php echo $vars["ng_model"] ?>.mode"
				uib-btn-radio="type.slug">
				{{ type.name }}
			</label>
		</div>

		<hr class="thin">

		<!--///// QUERY /////-->
		<div ng-show="<?php echo $vars["ng_model"] ?>.mode == 'query'">
			<h3>
				<i class="pwi-search"></i>
				<?php _e( 'Query', 'postworld' ) ?>
			</h3>
			<hr class="thin">
			<div>
				<div class="icon-md"><i class="pwi-pushpin"></i></div>
				<input type="checkbox"
					id="input-this_post"
					ng-model="<?php echo $vars["ng_model"] ?>.query_vars.this_post">
					<label for="input-this_post">
						<?php _ex( 'Include This Post', 'in query', 'postworld' ) ?>
					</label>
			</div>
			<hr class="thin">
			<div class="icon-md"><i class="pwi-image"></i></div>
			<input type="checkbox"
				id="input-hasimage"
				ng-model="<?php echo $vars["ng_model"] ?>.query_vars.has_image">
				<label for="input-hasimage">
					<?php _ex( 'Only include posts with an image', 'in query', 'postworld' ) ?>
				</label>
			<hr class="thin">
			<div class="icon-md"><i class="pwi-tree"></i></div>
			<input type="checkbox"
				id="input-children"
				ng-model="<?php echo $vars["ng_model"] ?>.query_vars.show_children">
				<label for="input-children">
					<?php _ex( 'Show Child Posts', 'in query', 'postworld' ) ?>
				</label>
			<hr class="thin">

			<label for="select-feature_tax" class="inner">
				<i class="pwi-th-list"></i>
				<?php _e( 'Taxonomy', 'postworld' ) ?>
			</label>
			<select
				class="labeled"
				id="select-feature_tax"
				ng-model="<?php echo $vars["ng_model"] ?>.query_vars.tax_query_taxonomy"
				ng-options="key as tax.labels.name for (key,tax) in tax_terms">
				<option value="">
					<?php _e( 'Select Taxonomy', 'postworld' ) ?>
				</option>
			</select>

			<label for="select-feature_term" class="inner">
				<i class="pwi-search"></i>
				<?php _ex( 'Term', 'taxonomy term', 'postworld' ) ?>
			</label>
			<select
				class="labeled"
				id="select-feature_term"
				ng-model="<?php echo $vars["ng_model"] ?>.query_vars.tax_query_term_id"
				ng-options="term.term_id as term.name group by term.parent_name for term in tax_terms[ pwMeta.header.slider.query_vars.tax_query_taxonomy ].terms">
				<option value="">
					<?php _ex( 'Select Term', 'taxonomy term', 'postworld' ) ?>
				</option>
			</select>
			
		</div>

		<!--///// QUERY / THIS POST /////-->
		<div ng-show="<?php echo $vars["ng_model"] ?>.mode == 'query' || <?php echo $vars["ng_model"] ?>.mode == 'this_post'">
			<h4>
				<i class="pwi-images"></i>
				<?php _e( 'Galleries', 'postworld' ) ?>
			</h4>
			<hr class="thin">
			<div class="icon-md"><i class="pwi-th"></i></div>
			<input type="checkbox"
				id="input-galleries"
				ng-model="<?php echo $vars["ng_model"] ?>.query_vars.include_galleries">
				<label for="input-galleries">
					<?php _e( 'Include images found in galleries', 'postworld' ) ?>
				</label>
			<div class="indent" ng-show="<?php echo $vars["ng_model"] ?>.query_vars.include_galleries">
				<hr class="thin">
				<div class="icon-md"><i class="pwi-eye"></i></div>
				<input type="checkbox"
					id="input-only_galleries"
					ng-model="<?php echo $vars["ng_model"] ?>.query_vars.only_galleries">
					<label for="input-only_galleries">
						<?php _e( 'Only show images from galleries', 'postworld' ) ?>
					</label>
				<hr class="thin">
				<div class="icon-md"><i class="pwi-eye-closed"></i></div>
				<input type="checkbox"
					id="input-hide_galleries"
					ng-model="<?php echo $vars["ng_model"] ?>.query_vars.hide_galleries">
					<label for="input-hide_galleries">
						<?php _e( 'Hide galleries in the post content', 'postworld' ) ?>
					</label>
			</div>
		</div>

		<!--///// MENU /////-->
		<div  ng-show="<?php echo $vars["ng_model"] ?>.mode == 'menu'">
			<label class="inner">
				<i class="pwi-nav"></i>
				<?php _e( 'Select Menu', 'postworld' ) ?>
			</label>
			<?php
				echo pw_select_menus( array(
					'options_model'	=>	'options.menus',
					'ng_model'	=>	$vars["ng_model"].'.menu_vars.menu_id',
					'class' => 'labeled'
					));
			?>
		</div>

	</div>
	
	<div class="well">
		<h3>
			<span class="icon-md"><i class="pwi-gear"></i></span>
			<?php _e( 'Settings', 'postworld' ) ?>
		</h3>
		<?php
			echo pw_select_slider_settings( array(
				'ng_model' 	=> 	$vars["ng_model"],
				'show'		=>	'all',
				'defaults'	=>	array(
					'mode'	=>	'menu',
					),
				));
		?>
	</div>
	<!--
	<code><pre>{{ pwMeta.header.slider | json }}</pre></code>
	-->
</div>
