<?php
	// Enable Media Library
	wp_enqueue_media();
	///// GET OPTIONS /////
	$pwSiteOptions = pw_get_option( array( 'option_name' => PW_OPTIONS_SITE ) );
	$pw_header_code = json_encode( get_option( PW_OPTIONS_HEADER_CODE, '' ) );
?>
<script>
	postworldAdmin.controller( 'pwOptionsDataCtrl',
		[ '$scope', 'iOptionsData',
		function( $scope, $iOptionsData ){

		// Set default empty value as object, not array
		var siteOptions = <?php echo json_encode( $pwSiteOptions ); ?>;
		if( _.isEmpty( siteOptions ) )
			siteOptions = {};

		$scope.pwSiteOptions = siteOptions;
		$scope.pwHeaderCode = <?php echo $pw_header_code; ?>;
		$scope['images'] = {};
		$scope['options'] = $iOptionsData['options'];
	}]);
</script>

<div class="main wrap postworld" ng-cloak>
	<h1>
		<i class="icon-gears"></i>
		Site Options
	</h1>
	
	
	<div
		pw-admin-options
		ng-cloak
		ng-controller="pwOptionsDataCtrl">

		<!--///// THEME OPTIONS /////-->
		<hr class="thick">

		<div class="row">
			<div class="col-sm-6 pad-col-md">

				<!-- FAVICON -->
				<div class="well">
					<div class="save-right">
						<?php pw_save_option_button( PW_OPTIONS_SITE, 'pwSiteOptions'); ?>
					</div>
					<h2>
						<span class="icon-md"><i class="icon-image"></i></span>
						Favicon
					</h2>
					<?php
						echo pw_select_image_id( array(
							'ng_model'		=>	'pwSiteOptions.images.favicon',
							'slug'			=>	'favicon',
							'label'			=>	'Favicon',
							'display'		=>	true,
							'width'			=> 	'64px',
						 	));?>
				</div>

			</div>
			<div class="col-sm-6 pad-col-md">
				
				

			</div>
		</div>


		<div class="well">
			<div class="save-right">
				<?php pw_save_option_button( PW_OPTIONS_HEADER_CODE, 'pwHeaderCode'); ?>
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
					ng-model="pwHeaderCode"></textarea>
			</div>
		</div>


		<hr class="thick">
		<h3>Google Fonts</h3>
		
		<hr class="thick">

		<pre>pwSiteOptions: {{ pwSiteOptions | json }}</pre>

	</div>
</div>