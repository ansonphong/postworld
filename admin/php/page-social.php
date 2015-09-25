<script>
	postworldAdmin.controller( 'pwSocialDataCtrl',
		[ '$scope', 'iOptionsData', function( $scope, $iOptionsData ){
		// Social Option Values
		$scope.pwSocial = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL ) ) ); ?>;
		$scope['options'] = $iOptionsData['options'];

		// Social Meta Data
		$scope.socialMeta = <?php echo json_encode( pw_social_meta() ); ?>;
	}]);
</script>

<div class="postworld wrap social" ng-cloak>

	<h1>
		<i class="pwi-profile"></i>
		Social
	</h1>
	
	<hr class="thick">

	<div
		pw-admin-social
		ng-controller="pwSocialDataCtrl"
		ng-cloak>

		<!-- SHARE SOCIAL -->
		<div class="well">
			<div class="save-right">
				<?php pw_save_option_button( PW_OPTIONS_SOCIAL, 'pwSocial'); ?>
			</div>
			<h2>
				<i class="pwi-share"></i>
				Sharing
			</h2>
			<small>Include share links on each post for the following networks:</small>
			<hr class="thin">
			<?php echo pw_share_social_options(); ?>
			<div style="clear:both"></div>
		</div>


		<!-- NG REPEAT : SECTIONS -->
		<div 
			ng-repeat="sectionMeta in socialMeta">
			<hr class="thick">
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

		</div>

		<?php if( pw_dev_mode() ): ?>
			<hr class="thick">
			<div class="well">
				<h3><i class="pwi-merkaba"></i> Dev Mode</h3>
				<pre><code>pwSocial : {{ pwSocial | json }}</code></pre>
				<pre><code>socialMeta : {{ socialMeta | json }}</code></pre>
			</div>
		<?php endif; ?>

	</div>

</div>