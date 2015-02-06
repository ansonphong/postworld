<script>
	postworldAdmin.controller( 'pwSocialDataCtrl',
		[ '$scope', 'iOptionsData', function( $scope, $iOptionsData ){
		// Social Option Values
		$scope.iSocial = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL ) ) ); ?>;
		$scope['options'] = $iOptionsData['options'];

		// Social Meta Data
		$scope.socialMeta = <?php echo json_encode( pw_social_meta() ); ?>;
	}]);
</script>

<div class="postworld wrap social" ng-cloak>

	<h1>
		<i class="icon-profile"></i>
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
				<?php pw_save_option_button( PW_OPTIONS_SOCIAL, 'iSocial'); ?>
			</div>
			<h2>
				<i class="icon-share"></i>
				Sharing
			</h2>
			<small>Include share links on each post for the following networks:</small>
			<hr class="thin">
			<?php echo i_share_social_options(); ?>
			<div style="clear:both"></div>
		</div>


		<!-- NG REPEAT : SECTIONS -->
		<div 
			ng-repeat="sectionMeta in socialMeta">
			<hr class="thick">
			<!-- SAVE BUTTON -->
			<div class="save-right"><?php pw_save_option_button( PW_OPTIONS_SOCIAL,'iSocial'); ?></div>
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
								ng-model="iSocial[sectionMeta.id][inputMeta.id]">
								<small>{{ inputMeta.description }}</small>
						</div>
					</td>

				</tr>
			</table>

		</div>

		<hr class="thick">
		<!--<pre>iSocial : {{ iSocial | json }}</pre>-->
		<!--<pre>socialMeta : {{ socialMeta | json }}</pre>-->

	</div>

</div>