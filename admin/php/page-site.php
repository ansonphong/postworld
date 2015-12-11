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

		$scope.memoryOptions = <?php echo json_encode( apply_filters('pw_options_site_memory',array()) ) ?>;
		$scope.postworldModeOptions = <?php echo json_encode( apply_filters('pw_options_postworld_mode',array()) ) ?>;

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
						<?php echo __('Favicon','postworld') ?>
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
						<?php echo __('Default Avatar','postworld') ?>
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
						<?php echo __('Security','postworld') ?>
					</h2>
					<div class="well">
						<label>
							<input type="checkbox" ng-model="pwSiteOptions.security.disable_xmlrpc">
							<b><?php echo __('Disable XMLRPC API','postworld') ?></b>
							<small>Commonly an access point for DDoS attacks.</small>
						</label>
						<hr class="thin">
						<label>
							<input type="checkbox" ng-model="pwSiteOptions.security.require_login">
							<b><?php echo __('Require Login','postworld') ?></b>
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
						<?php echo __('Performance','postworld') ?>
					</h2>
					<div class="well">
						<label>
							<select
								ng-model="pwSiteOptions.memory.image_memory_limit"
								ng-options="option.value as option.label for option in memoryOptions">
							</select>
							<b><?php echo __('Image Memory Limit','postworld') ?></b>
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
						<?php echo __('WordPress Core','postworld') ?>
					</h2>
					<div class="well">
						<label>
							<input type="checkbox" ng-model="pwSiteOptions.wp_core.disable_wp_emojicons">
							<b><?php echo __('Disable WP Emojicons','postworld') ?></b>
							<small>Increases load speed.</small>
						</label>
					</div>
				</div>

				<!-- POSTWORLD MODE -->
				<div class="well">
					<div class="save-right">
						<?php pw_save_option_button( PW_OPTIONS_SITE, 'pwSiteOptions'); ?>
					</div>
					<h2>
						<span class="icon-md"><i class="pwi-postworld"></i></span>
						<?php echo __('Postworld','postworld') ?>
					</h2>
					<div class="well">
						<label>
							<select
								ng-model="pwSiteOptions.postworld.mode"
								ng-options="option.value as option.label for option in postworldModeOptions">
							</select>
							<b><?php echo __('Mode','postworld') ?></b>
							<small>Keep it in production mode unless you know what you're doing.</small>
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
				<?php echo __('Header Code','postworld') ?>
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