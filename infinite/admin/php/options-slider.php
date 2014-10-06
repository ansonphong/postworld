<?php
function i_metabox_slider_scripts(){
	$ng_model = "iMeta.header";
	?>
	<script>
		///// CONTROLLER /////
		infiniteMetabox.controller('iMetaboxSliderOptionsCtrl',
			['$scope', '$log', 'pwPostOptions', '_',
				function( $scope, $log, $pwPostOptions, $_ ) {
				//$scope.iMeta = <?php echo json_encode($iMeta); ?>;
				
				// Get tax outline by AJAX
				//$pwPostOptions.getTaxTerms( $scope, 'tax_terms' );

				// Define Options
				$scope.sliderOptions = {
					'slider':{
						'mode':[
							{
								slug: 'this_post',
								name: 'This Post',
							},
							{
								slug: 'query',
								name: 'Query',
							},
							{
								slug: 'menu',
								name: 'Menu',
							},
						],
						'transition':[
							{
								slug: false,
								name: 'No',
							},
							{
								slug: 'fade',
								name: 'Fade',
							},
							{
								slug: 'slide',
								name: 'Slide',
							},
						]
					},
				};

				// Watch value : iMeta.header.slider.mode
				$scope.$watch('<?php echo $ng_model; ?>.slider.mode', function(value){
					//$log.debug( value );
					// Switch Query Vars

					switch( value ){
						case 'this_post':
							$scope.<?php echo $ng_model; ?>.slider.query_vars.this_post = true;
							$scope.<?php echo $ng_model; ?>.slider.query_vars.this_post_only = true;
							break;

						case 'query':
							$scope.<?php echo $ng_model; ?>.slider.query_vars.this_post_only = false;
							break;

					}


				});

		}]);
	</script>
	<?php
}
// Add the scripts at the right time
add_action( 'i_admin_options_metabox_scripts', 'i_metabox_slider_scripts' );

?>

<div ng-controller="iMetaboxSliderOptionsCtrl">
	<div class="well">
		<h3><span class="icon-md"><i class="icon-cog"></i></span> Get Slides From</h3>
		<div class="btn-group">
			<label
				ng-repeat="type in sliderOptions.slider.mode"
				class="btn" ng-model="iMeta.header.slider.mode" btn-radio="type.slug">
				{{ type.name }}
			</label>
		</div>

		<!--///// QUERY /////-->
		<div ng-show="iMeta.header.slider.mode == 'query'">
			<h4>Query</h4>
			<hr>
			<div>
				<div class="icon-md"><i class="icon-pushpin"></i></div>
				<input type="checkbox"
					id="input-this_post"
					ng-model="iMeta.header.slider.query_vars.this_post">
					<label for="input-this_post">Include This Post</label>
			</div>
			<hr>
			<div class="icon-md"><i class="icon-image"></i></div>
			<input type="checkbox"
				id="input-hasimage"
				ng-model="iMeta.header.slider.query_vars.has_image">
				<label for="input-hasimage">Only include posts with an image</label>
			<hr>
			<div class="icon-md"><i class="icon-tree"></i></div>
			<input type="checkbox"
				id="input-children"
				ng-model="iMeta.header.slider.query_vars.show_children">
				<label for="input-children">Show Child Posts</label>
			<hr>
			<div class="icon-md"><i class="icon-list-alt"></i></div>
			<select
				class="labeled"
				id="select-feature_term"
				ng-model="iMeta.header.slider.query_vars.tax_query_taxonomy"
				ng-options="key as tax.labels.name for (key,tax) in tax_terms">
				<option value="">Select Taxonomy</option>
			</select>
			<label for="select-feature_term"> taxonomy</label>
			<br>
			<div class="icon-md"><i class="icon-search"></i></div>
			<select
				class="labeled"
				id="select-feature_term"
				ng-model="iMeta.header.slider.query_vars.tax_query_term_id"
				ng-options="term.term_id as term.name group by term.parent_name for term in tax_terms[ iMeta.header.slider.query_vars.tax_query_taxonomy ].terms">
				<option value="">Select Term</option>
			</select>
			<label for="select-feature_term"> term</label>
			<hr>
		</div>

		<!--///// QUERY / THIS POST /////-->
		<div  ng-show="iMeta.header.slider.mode == 'query' || iMeta.header.slider.mode == 'this_post'">
			<h4>Galleries</h4>
			<hr>
			<div class="icon-md"><i class="icon-th"></i></div>
			<input type="checkbox"
				id="input-galleries"
				ng-model="iMeta.header.slider.query_vars.include_galleries">
				<label for="input-galleries">Include images found in galleries</label>
			<div class="indent" ng-show="iMeta.header.slider.query_vars.include_galleries">
				<hr>
				<div class="icon-md"><i class="icon-eye"></i></div>
				<input type="checkbox"
					id="input-only_galleries"
					ng-model="iMeta.header.slider.query_vars.only_galleries">
					<label for="input-only_galleries">Only show images from galleries</label>
				<hr>
				<div class="icon-md"><i class="icon-eye-closed"></i></div>
				<input type="checkbox"
					id="input-hide_galleries"
					ng-model="iMeta.header.slider.query_vars.hide_galleries">
					<label for="input-hide_galleries">Hide galleries in the post content</label>
			</div>
			<hr>
		</div>

		<!--///// MENU /////-->
		<div  ng-show="iMeta.header.slider.mode == 'menu'">
			<hr>
			<div class="icon-md"><i class="icon-th-list"></i></div>
			<?php
				echo i_select_menus( array(
					'options_model'	=>	'options.menus',
					'ng_model'	=>	'iMeta.header.slider.menu_vars.menu_id',
					));
			?>
		</div>

	</div>
	
	<div class="well">
		<h3><span class="icon-md"><i class="icon-cog"></i></span> Settings</h3>
		<?php
			echo i_select_slider_settings( array(
				'ng_model' 	=> 'iMeta.header.slider',
				'show'		=>	'all',
				'defaults'	=>	array(
					'mode'	=>	'menu',
					),
				));
		?>
	</div>
	<!--
	<code><pre>{{ iMeta.header.slider | json }}</pre></code>
	-->
</div>
