<div id="poststuff" ng-app="infinite" class="postworld social">

	<h1>
		<i class="icon-profile"></i>
		Social
	</h1>

	<script>
		infinite.controller( 'iSocialDataCtrl',
			[ '$scope', 'iOptionsData', function( $scope, $iOptionsData ){
			// Social Option Values
			$scope.iSocial = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_SOCIAL ) ) ); ?>;

			// Merge Default Model with Saved Values
			//var mergedModel = angular.fromJson( angular.toJson( deepmerge( $scope.socialModel, $scope.iSocial ) ) );
			//$scope.iSocial = $scope.socialModel; //deepmerge( $scope.socialModel, $scope.iSocial );

			$scope['options'] = $iOptionsData['options'];

			// Social Meta Data
			$scope.socialMeta = <?php
				global $i_social_meta;
				$i_social_meta = json_encode($i_social_meta);
				echo $i_social_meta;
				?>;
		}]);
	</script>

	<div
		i-admin-social
		ng-controller="iSocialDataCtrl"
		ng-cloak>


		<!-- SHARE SOCIAL -->
		<div class="well">
			<div class="save-right">
				<?php i_save_option_button( PW_OPTIONS_SOCIAL, 'iSocial'); ?>
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
			<div class="save-right"><?php i_save_option_button( PW_OPTIONS_SOCIAL,'iSocial'); ?></div>
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
		<pre>iSocial : {{ iSocial | json }}</pre>
		<!--<pre>socialMeta : {{ socialMeta | json }}</pre>-->

	</div>

</div>