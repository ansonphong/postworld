<div id="infinite_admin" ng-app="infinite" class="social">

	<h1>
		<i class="icon-profile"></i>
		Social
	</h1>

	<script>
		infinite.controller( 'iSocialDataCtrl', [ '$scope', function( $scope ){
			// Social Option Values
			$scope.iSocial = <?php
				global $i_social_model;

				$i_social = get_option('i-social', array());
				if( empty($i_social) )
					$i_social = $i_social_model;
				else
					$i_social = json_decode( $i_social, true );

				$i_social = array_replace_recursive( $i_social_model, $i_social );
				$i_social = json_encode($i_social);

				echo $i_social;
				?>;

			// Merge Default Model with Saved Values
			//var mergedModel = angular.fromJson( angular.toJson( deepmerge( $scope.socialModel, $scope.iSocial ) ) );
			//$scope.iSocial = $scope.socialModel; //deepmerge( $scope.socialModel, $scope.iSocial );

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

		<!-- NG REPEAT : SECTIONS -->
		<div 
			ng-repeat="sectionMeta in socialMeta">
			<hr class="thick">
			<!-- SAVE BUTTON -->
			<div class="save-right"><?php i_save_option_button('i-social','iSocial'); ?></div>
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
		<pre>socialMeta : {{ socialMeta | json }}</pre>

	</div>

</div>