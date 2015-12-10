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

		$scope.memoryOptions = [
			{
				label:'256 MB',
				value:'256M'
			},
			{
				label:'512 MB',
				value:'512M'
			},
			{
				label:'1 GB',
				value:'1G'
			},
			{
				label:'2 GB',
				value:'2G'
			},
			{
				label:'3 GB',
				value:'3G'
			},

		];

	}]);
</script>

<div class="main wrap postworld" ng-cloak>
	<h1>
		<i class="pwi-gear"></i>
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
						<span class="icon-md"><i class="pwi-image"></i></span>
						Favicon
					</h2>
					<div class="well">
						Select the favicon, which appears as the tab icon in the browser.
						<hr class="thin">
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

				<!-- AVATAR IMAGE -->
				<div class="well">
					<div class="save-right">
						<?php pw_save_option_button( PW_OPTIONS_SITE, 'pwSiteOptions'); ?>
					</div>
					<h2>
						<span class="icon-md"><i class="pwi-image"></i></span>
						Default Avatar
					</h2>
					<div class="well">
						Select the default avatar image.
						<hr class="thin">
						<?php
							echo pw_select_image_id( array(
								'ng_model'		=>	'pwSiteOptions.images.avatar',
								'slug'			=>	'avatar',
								'label'			=>	'Default Avatar',
								'display'		=>	true,
								'width'			=> 	'256px',
							 	));?>
					</div>
				</div>

			</div>
			<div class="col-sm-6 pad-col-md">
				

				<!-- SECURITY -->
				<div class="well">
					<div class="save-right">
						<?php pw_save_option_button( PW_OPTIONS_SITE, 'pwSiteOptions'); ?>
					</div>
					<h2>
						<span class="icon-md"><i class="pwi-lock"></i></span>
						Security
					</h2>
					<div class="well">
						<label>
							<input type="checkbox" ng-model="pwSiteOptions.security.disable_xmlrpc">
							<b>Disable XMLRPC API</b>
							<small>Commonly an access point for DDoS attacks.</small>
						</label>
						<hr class="thin">
						<label>
							<input type="checkbox" ng-model="pwSiteOptions.security.require_login">
							<b>Require Login</b>
							<small>Require login to access site.</small>
						</label>
					</div>
				</div>


				<!-- PERFORMANCE -->
				<div class="well">
					<div class="save-right">
						<?php pw_save_option_button( PW_OPTIONS_SITE, 'pwSiteOptions'); ?>
					</div>
					<h2>
						<span class="icon-md"><i class="pwi-fire"></i></span>
						Performance
					</h2>
					<div class="well">
						<label>
							<select
								ng-model="pwSiteOptions.memory.image_memory_limit"
								ng-options="option.value as option.label for option in memoryOptions">
							</select>
							<b>Image Memory Limit</b>
							<small>The maximum amount of memory used for processing images.</small>
						</label>
					</div>
				</div>

				<!-- WORDPRESS CORE -->
				<div class="well">
					<div class="save-right">
						<?php pw_save_option_button( PW_OPTIONS_SITE, 'pwSiteOptions'); ?>
					</div>
					<h2>
						<span class="icon-md"><i class="pwi-wordpress"></i></span>
						WordPress Core
					</h2>
					<div class="well">
						<label>
							<input type="checkbox" ng-model="pwSiteOptions.wp_core.disable_wp_emojicons">
							<b>Disable WP Emojicons</b>
							<small>Increases load speed.</small>
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="well">
			<div class="save-right">
				<?php pw_save_option_button( PW_OPTIONS_HEADER_CODE, 'pwHeaderCode'); ?>
			</div>
			<h2>
				<i class="pwi-code"></i>
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

		<?php if( pw_dev_mode() ): ?>
			<hr class="thick">
			<div class="well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<pre><code>pwSiteOptions: {{ pwSiteOptions | json }}</code></pre>
			</div>
		<?php endif; ?>

	</div>
</div>