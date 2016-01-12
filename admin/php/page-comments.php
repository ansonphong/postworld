<script>
	postworldAdmin.controller( 'pwCommentsDataCtrl',function( $scope ){
		$scope.pwComments = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_COMMENTS ) ) ); ?>;
	});
</script>
<div class="postworld wrap social" ng-cloak>
	<h1>
		<i class="pwi-bubbles"></i>
		Comments
	</h1>
	<hr class="thick">

	<div
		pw-admin-comments
		ng-controller="pwCommentsDataCtrl"
		ng-cloak>

		<div class="row">
			<div class="col-lg-6 pad-col-lg">
				
				<!-- FACEBOOK COMMENTS -->
				<div class="well">
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_COMMENTS, 'pwComments'); ?></div>
					<h2>
						<i class="icon pwi-facebook"></i>
						<?php _ex('Facebook Comments','postworld') ?>
					</h2>
					
					<div class="well">

						<label>
							<input
								type="checkbox"
								ng-model="pwComments.facebook.enable">
								<?php _ex('Enable Facebook Comments','postworld') ?>
						</label>

						<div
							ng-show="pwComments.facebook.enable">
							<hr class="thin">
							<input
								type="number"
								class="short"
								ng-model="pwComments.facebook.numposts">
							<?php _ex('number of posts','postworld') ?>
							<small>: <?php _ex('The number of most recent comments to show','postworld') ?></small>

							<hr class="thin">
							
							<select
								ng-model="pwComments.facebook.colorscheme">
								<option value="dark"><?php _ex('Dark','postworld') ?></option>
								<option value="light"><?php _ex('Light','postworld') ?></option>
							</select>
							<?php _ex('color scheme','postworld') ?>
							<small> : <?php _ex('the background color of the comment module','postworld') ?></small>

							<hr class="thin">
							
							<select
								ng-model="pwComments.facebook.order_by">
								<option value="social"><?php _ex('Social Circle','postworld') ?></option>
								<option value="time"><?php _ex('Chronological','postworld') ?></option>
								<option value="reverse_time"><?php _ex('Reverse Chronological','postworld') ?></option>
							</select>
							<?php _ex('order','postworld') ?>
							<small> : <?php _ex('the order in which comments appear','postworld') ?></small>

							<div class="well">
								<b><?php _ex('Note','postworld') ?> :</b> 
								<?php _ex('A Facebook App ID is required for comments to work properly.','postworld') ?>

								First <a href="https://developers.facebook.com/apps/">create a Facebook App</a>,
								then enter your app's ID into the Facebook App ID field in
								<a href="<?php get_site_url(); ?>/wp-admin/admin.php?page=postworld-social">Postworld Social</a>. 
							
							</div>
						</div>


					</div>

				</div>

			</div>

			<div class="col-lg-6 pad-col-lg">
			

				<!-- DISQUS COMMENTS -->
				<div class="well">
					<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_COMMENTS, 'pwComments'); ?></div>
					<h2>
						<i class="icon pwi-bubbles-o"></i>
						Disqus Comments
					</h2>
					
					<div class="well">

						<label>
							<input
								type="checkbox"
								ng-model="pwComments.disqus.enable">
							Enable Disqus Comments
						</label>

						<div
							ng-show="pwComments.disqus.enable">
							<hr class="thin">
							<input
								type="text"
								ng-model="pwComments.disqus.shortname">
							shortname
							<small>: This is the unique identifier for your site on Disqus</small>

							<div class="well">
								<b>Note:</b> A Disqus shortname is required for comments to work properly.
								Once you have <a href="https://disqus.com/admin/create/">created a Disqus shortname</a>,
								enter your shortname into the above field.
							</div>

						</div>


					</div>

				</div>

			</div>

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