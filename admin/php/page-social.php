<?php
pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'pwSocialDataCtrl',
	'vars' => array(
		'pwSocial' => pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL ) ),
		'socialMeta' => pw_social_meta(),
		),
	));
?>

<?php do_action( 'postworld_admin_header' ) ?>

<div class="postworld postworld-social wrap social">

	<h1 class="primary">
		<i class="icon pwi-profile"></i>
		<?php _ex('Social','module','postworld' )?>
	</h1>
	
	<hr class="thick">

	<div
		pw-admin-options
		pw-admin-social
		pw-ui
		ng-controller="pwSocialDataCtrl"
		class="pw-cloak">

		<!-- SHARE SOCIAL -->
		<div class="well">
			<div class="save-right">
				<?php pw_save_option_button( PW_OPTIONS_SOCIAL, 'pwSocial'); ?>
			</div>
			<h2>
				<i class="pwi-share"></i>
				<?php _ex('Sharing','social sharing','postworld' )?>
			</h2>
			<small><?php _e('Include share links on each post for the following networks:','postworld' )?></small>
			<hr class="thin">

			<div class="share-networks">
				<label ng-repeat="network in options.share.meta" ng-class="{'active':uiInArray(network.id,pwSocial.share.networks)}">
					<input type="checkbox" checklist-model="pwSocial.share.networks"  checklist-value="network.id">
					<i class="icon" ng-class="network.icon"></i>
					<span>{{network.name}}</span>
				</label>
			</div>

			<div style="clear:both"></div>
		</div>


		<!-- SOCIAL WIDGETS -->
		<div class="well">
			<div class="save-right">
				<?php pw_save_option_button( PW_OPTIONS_SOCIAL, 'pwSocial'); ?>
			</div>
			<h2>
				<i class="pwi-cube"></i>
				<?php _ex('Social Widgets','like button, tweet button, etc','postworld' )?>
			</h2>
			<small><?php _ex('Customize which sharing widgets appear on each post.','like button, tweet button, etc','postworld' )?></small>
			<hr class="thin">
			
			<div class="well">
				<label>
					<input type="checkbox" ng-model="pwSocial.widgets.facebook.enable">
					<i class="icon pwi-facebook"></i>
					<b><?php _e('Facebook Like Button','postworld' )?></b>
				</label>
				<div class="indent" ng-show="pwSocial.widgets.facebook.enable">
					<hr class="thin">
					<label>
						<input type="checkbox" ng-model="pwSocial.widgets.facebook.settings.share">
						<?php _ex('Include share button','next to facebook like button','postworld' )?>
					</label>
				</div>
			</div>

			<div class="well">
				<label>
					<input type="checkbox" ng-model="pwSocial.widgets.twitter.enable">
					<i class="icon pwi-twitter"></i>
					<b><?php _e('Twitter Tweet Button','postworld' )?></b>
				</label>
			</div>

		</div>

		<!-- FIELDS -->
		<div class="well">
			<!-- NG REPEAT : SECTIONS -->
			<div 
				ng-repeat="sectionMeta in socialMeta">
				<!-- SAVE BUTTON -->
				<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_SOCIAL,'pwSocial'); ?></div>
				<h2><i class="{{ sectionMeta.icon }}"></i> {{ sectionMeta.name }}</h2>
				<table class="form-table pad">
					<tr ng-repeat="inputMeta in sectionMeta.fields"
						valign="top"
						class="module layout">
						<th scope="row">
							<b>
								<span class="icon-md"><i class="{{inputMeta.icon}}"></i></span>
								{{inputMeta.name}}
							</b>
						</th>
						<td>
							
							<!-- PROPERTIES -->
							<div>
								<input
									type="text"
									ng-model="pwSocial[sectionMeta.id][inputMeta.id]">
									<small>{{ inputMeta.description }}</small>
							</div>
						</td>
					</tr>
				</table>
				<hr>
			</div>
		</div>

		<?php if( pw_dev_mode() ): ?>
			<hr class="thick">
			<div class="well">
				<h3><i class="pwi-merkaba"></i> <?php _e('Development Mode','postworld') ?></h3>
				<pre><code>PW_OPTIONS_SOCIAL : wp_options.<?php echo PW_OPTIONS_SOCIAL ?> : $scope.pwSocial : {{ pwSocial | json }}</code></pre>
				<pre><code>socialMeta : {{ socialMeta | json }}</code></pre>
			</div>
		<?php endif; ?>

	</div>

</div>