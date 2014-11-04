<div id="poststuff" ng-app="infinite" class="main postworld">
	<h1>
		<i class="icon-gears"></i>
		Site Options
	</h1>
	<?php
		// Enable Media Library
		wp_enqueue_media();
		///// GET OPTIONS /////
		$iOptions = pw_get_option( array( 'option_name' => PW_OPTIONS_SITE ) );
		$i_header_code = json_encode( get_option( 'postworld-header-code', '' ) );
	?>
	<script>
		infinite.controller( 'optionsDataCtrl',
			[ '$scope', 'iOptionsData',
			function( $scope, $iOptionsData ){
			$scope.iOptions = <?php echo json_encode($iOptions); ?>;
			$scope.iHeaderCode = <?php echo $i_header_code; ?>;
			$scope['images'] = {};
			$scope['options'] = $iOptionsData['options'];
		}]);
	</script>
	<div
		i-admin-options
		ng-cloak
		ng-controller="optionsDataCtrl">

		<!--///// THEME OPTIONS /////-->
		<hr class="thick">

		<div class="row">
			<div class="col-sm-6 pad-col-md">

				<!-- FAVICON -->
				<div class="well">
					<div class="save-right">
						<?php i_save_option_button( PW_OPTIONS_SITE, 'iOptions'); ?>
					</div>
					<h2>
						<span class="icon-md"><i class="icon-image"></i></span>
						Favicon
					</h2>
					<?php echo i_select_image_favicon(); ?>
				</div>

			</div>
			<div class="col-sm-6 pad-col-md">
				
				

			</div>
		</div>



		<div class="well">
			<div class="save-right">
				<?php i_save_option_button('postworld-header-code','iHeaderCode'); ?>
			</div>
			<h2>
				<i class="icon-code"></i>
				Header Code
			</h2>
			<small>
				This code will be inserted into the page header.
				Here is a good place to post tracking codes such as Google Analytics, or third-party additions.
			</small>
			<hr class="thin">
			<div>
				<textarea
					msd-elastic
					class="form-control"
					ng-model="iHeaderCode"></textarea>
			</div>
		</div>


		<hr class="thick">
		<h3>Google Fonts</h3>
		
		<hr class="thick">

		<pre>iOptions: {{ iOptions | json }}</pre>

	</div>
</div>