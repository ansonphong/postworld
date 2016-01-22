<?php
	$supported_comments = pw_config('comments.supported');
	if( $supported_comments == false ){
		$supported_comments = array('facebook','disqus');
	}
?>
<script>
	postworldAdmin.controller( 'pwCommentsDataCtrl',function( $scope ){
		$scope.pwComments = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_COMMENTS ) ) ); ?>;
	});
</script>
<div class="postworld wrap social" ng-cloak>
	<h1>
		<i class="pwi-bubbles"></i>
		<?php _ex('Comments', 'heading', 'postworld') ?>
	</h1>
	<hr class="thick">

	<div
		pw-admin-comments
		ng-controller="pwCommentsDataCtrl"
		ng-cloak>

		<div class="row">

			<?php if( in_array( 'facebook', $supported_comments ) ): ?>

				<div class="col-lg-6 pad-col-lg">

					<!-- FACEBOOK COMMENTS -->
					<div class="well">
						<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_COMMENTS, 'pwComments'); ?></div>
						<h2>
							<i class="icon pwi-facebook"></i>
							<?php _ex('Facebook Comments', 'heading', 'postworld') ?>
						</h2>
						
						<div class="well">

							<label>
								<input
									type="checkbox"
									ng-model="pwComments.facebook.enable">
									<?php _ex('Enable Facebook Comments', 'setting toggle', 'postworld') ?>
							</label>

							<div
								ng-show="pwComments.facebook.enable">
								<hr class="thin">
								<input
									type="number"
									class="short"
									ng-model="pwComments.facebook.numposts">
								<?php _ex('number of posts','postworld') ?>
								<small>: <?php _ex('the number of most recent comments to show', 'self-explanitory', 'postworld') ?></small>

								<hr class="thin">
								
								<select
									ng-model="pwComments.facebook.colorscheme">
									<option value="dark"><?php _ex('Dark', 'color scheme', 'postworld') ?></option>
									<option value="light"><?php _ex('Light', 'color scheme', 'postworld') ?></option>
								</select>
								<?php _ex('color scheme','postworld') ?>
								<small> : <?php _ex('the background color of the comment module', 'self-explanitory', 'postworld') ?></small>

								<hr class="thin">
								
								<select
									ng-model="pwComments.facebook.order_by">
									<option value="social"><?php _ex('Social Circle', 'facebook comments', 'postworld') ?></option>
									<option value="time"><?php _ex('Chronological', 'facebook comments', 'postworld') ?></option>
									<option value="reverse_time"><?php _ex('Reverse Chronological', 'facebook comments', 'postworld') ?></option>
								</select>
								<?php _ex('order','postworld') ?>
								<small> : <?php _ex('the order in which comments appear', 'setting', 'postworld') ?></small>

								<hr class="thin">
								<select ng-model="pwComments.facebook.href_from">
									<option value="id"><?php _ex('Post ID', 'setting', 'postworld') ?> (<?php _ex('default', 'setting', 'postworld') ?>)</option>
									<option value="url"><?php _ex('URL', 'setting', 'postworld') ?></option>
								</select>
								<?php _ex('href','postworld') ?>
								<small> : <?php _ex('where to derive unique HREF identifier from', 'setting', 'postworld') ?></small>

								<hr class="thin">
								<select
									ng-model="pwComments.facebook.protocol">
									<option value=""><?php _ex('Off', 'setting', 'postworld') ?></option>
									<option value="http"><?php _ex('HTTP', 'setting', 'postworld') ?></option>
									<option value="https"><?php _ex('HTTPS', 'setting', 'postworld') ?></option>
								</select>
								<?php _ex('normalize protocol', 'settings', 'postworld') ?>
								<small> : <?php _ex('force href from a specific protocol', 'setting', 'postworld') ?></small>

								<div class="well">
									<b><?php _ex('Note', 'general', 'postworld') ?> :</b> 
									<?php _ex('A Facebook App ID is required for comments to work properly.', 'Facebook', 'postworld') ?>
									<ol>
										<li>
											<a href="https://developers.facebook.com/apps/" target="_blank">
												<?php _ex("Create a Facebook App", 'list-item', 'postworld') ?>
											</a>
										</li>
										<li>
											<a href="<?php get_site_url(); ?>/wp-admin/admin.php?page=<?php echo pw_admin_submenu_slug() ?>-social">
												<?php _ex("Enter your app's ID into the Facebook App ID field in Postworld Social", 'list-item', 'postworld') ?>
											</a>
										</li>
									</ol>

								</div>
							</div>

						</div>

					</div>

				</div>

			<?php endif ?>

			<?php if( in_array( 'disqus', $supported_comments ) ): ?>

				<div class="col-lg-6 pad-col-lg">
			
					<!-- DISQUS COMMENTS -->
					<div class="well">
						<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_COMMENTS, 'pwComments'); ?></div>
						<h2>
							<i class="icon pwi-bubbles-o"></i>
							<?php _ex('Disqus Comments', 'heading', 'postworld') ?>
						</h2>
						
						<div class="well">

							<label>
								<input
									type="checkbox"
									ng-model="pwComments.disqus.enable">
									<?php _ex('Enable Disqus Comments', 'setting', 'postworld') ?>
							</label>

							<div
								ng-show="pwComments.disqus.enable">
								<hr class="thin">
								<input
									type="text"
									ng-model="pwComments.disqus.shortname">
									<?php _ex('shortname', 'Disqus', 'postworld') ?>
								<small>: <?php _ex('this is the unique identifier for your site on Disqus', 'self-explanitory', 'postworld') ?></small>
								<div class="well">
									<b><?php _ex('Note', 'general', 'postworld') ?> :</b>
									<?php _ex('A Disqus shortname is required for comments to work properly.', 'Disqus', 'postworld') ?>
									<ol>
										<li>
											<a href="https://disqus.com/admin/create/">
												<?php _ex('Click here to create a Disqus Shortname', 'Disqus', 'postworld') ?>
											</a>
										</li>
										<li>
											<?php _ex('Enter the Disqus shortname into the shortname field above', 'Disqus', 'postworld') ?>
										</li>
									</ol>
									
								</div>
							</div>

						</div>

					</div>

				</div>

			<?php endif ?>

		</div>
		
		<?php if( pw_dev_mode() ): ?>
			<hr class="thick">
			<div class="well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<pre><code>pwComments : {{ pwComments | json }}</code></pre>
			</div>
		<?php endif; ?>

	</div>

</div>