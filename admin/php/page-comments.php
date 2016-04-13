<?php
$supported_comments = pw_config('comments.supported');
if( !$supported_comments )
	$supported_comments = array('facebook','disqus','wordpress');

pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwCommentsDataCtrl',
	'vars' => array(
		'pwComments' => pw_get_option( array( 'option_name' => PW_OPTIONS_COMMENTS ) ),
		),
	));

?>
<div class="postworld wrap social" ng-cloak>
	<h1>
		<i class="pwi-bubbles"></i>
		<?php _e('Comments', 'postworld') ?>
	</h1>
	<hr class="thick">

	<div
		pw-admin-comments
		ng-controller="pwCommentsDataCtrl"
		ng-cloak>

		<div class="row">

			<?php if( in_array( 'facebook', $supported_comments ) ): ?>

				<div class="col-lg-6 pad-col-lg">

					<!-- WORDPRESS COMMENTS -->
					<div class="well">
						<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_COMMENTS, 'pwComments'); ?></div>
						<h2>
							<i class="icon pwi-wordpress"></i>
							<?php _e('WordPress Comments', 'postworld') ?>
						</h2>
						
						<div class="well">

							<label>
								<input
									type="checkbox"
									ng-model="pwComments.wordpress.enable">
									<?php _e('Enable WordPress Comments', 'postworld') ?>
							</label>
						</div>
					</div>


					<!-- FACEBOOK COMMENTS -->
					<div class="well">
						<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_COMMENTS, 'pwComments'); ?></div>
						<h2>
							<i class="icon pwi-facebook"></i>
							<?php _e('Facebook Comments', 'postworld') ?>
						</h2>
						
						<div class="well">

							<label>
								<input
									type="checkbox"
									ng-model="pwComments.facebook.enable">
									<?php _e('Enable Facebook Comments', 'postworld') ?>
							</label>

							<div
								ng-show="pwComments.facebook.enable">
								<hr class="thin">
								<input
									type="number"
									class="short"
									ng-model="pwComments.facebook.numposts">
								<?php _e('number of posts', 'postworld') ?>
								<small>: <?php _e('the number of most recent comments to show', 'postworld') ?></small>

								<hr class="thin">
								
								<select
									ng-model="pwComments.facebook.colorscheme">
									<option value="dark"><?php _e('Dark', 'postworld') ?></option>
									<option value="light"><?php _e('Light', 'postworld') ?></option>
								</select>
								<?php _e('color scheme', 'postworld') ?>
								<small> : <?php _e('the background color of the comment module', 'postworld') ?></small>

								<hr class="thin">
								
								<select
									ng-model="pwComments.facebook.order_by">
									<option value="social"><?php _e('Social Circle', 'postworld') ?></option>
									<option value="time"><?php _e('Chronological', 'postworld') ?></option>
									<option value="reverse_time"><?php _e('Reverse Chronological', 'postworld') ?></option>
								</select>
								<?php _e('order', 'postworld') ?>
								<small> : <?php _e('the order in which comments appear', 'postworld') ?></small>

								<hr class="thin">
								<select ng-model="pwComments.facebook.href_from">
									<option value="id"><?php _e('Post ID', 'postworld') ?> (<?php _e('default', 'postworld') ?>)</option>
									<option value="url"><?php _e('URL', 'postworld') ?></option>
								</select>
								<?php _e('href', 'postworld') ?>
								<small> : <?php _e('where to derive unique HREF identifier from', 'postworld') ?></small>

								<hr class="thin">
								<select
									ng-model="pwComments.facebook.protocol">
									<option value=""><?php _e('Off', 'postworld') ?></option>
									<option value="http"><?php _e('HTTP', 'postworld') ?></option>
									<option value="https"><?php _e('HTTPS', 'postworld') ?></option>
								</select>
								<?php _e('normalize protocol', 'postworld') ?>
								<small> : <?php _e('force href from a specific protocol', 'postworld') ?></small>

								<div class="well">
									<b><?php _e('Note', 'postworld') ?> :</b> 
									<?php _e('A Facebook App ID is required for comments to work properly.', 'postworld') ?>
									<ol>
										<li>
											<a href="https://developers.facebook.com/apps/" target="_blank">
												<?php _e("Create a Facebook App", 'postworld') ?>
											</a>
										</li>
										<li>
											<a href="<?php get_site_url(); ?>/wp-admin/admin.php?page=<?php echo pw_admin_submenu_slug() ?>-social">
												<?php _e("Enter your app's ID into the Facebook App ID field in Postworld Social", 'postworld') ?>
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
							<?php _e('Disqus Comments', 'postworld') ?>
						</h2>
						
						<div class="well">

							<label>
								<input
									type="checkbox"
									ng-model="pwComments.disqus.enable">
									<?php _e('Enable Disqus Comments', 'postworld') ?>
							</label>

							<div
								ng-show="pwComments.disqus.enable">
								<hr class="thin">
								<input
									type="text"
									ng-model="pwComments.disqus.shortname">
									<?php _e('shortname', 'postworld') ?>
								<small>: <?php _e('this is the unique identifier for your site on Disqus', 'postworld') ?></small>
								<div class="well">
									<b><?php _e('Note', 'general', 'postworld') ?> :</b>
									<?php _e('A Disqus shortname is required for comments to work properly.', 'postworld') ?>
									<ol>
										<li>
											<a href="https://disqus.com/admin/create/">
												<?php _e('Click here to create a Disqus Shortname', 'postworld') ?>
											</a>
										</li>
										<li>
											<?php _e('Enter the Disqus shortname into the shortname field above', 'postworld') ?>
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