<?php
	wp_enqueue_media();
?>
<script type="text/javascript">
	postworldAdmin.controller( 'pwUsermetaAvatarCtrl',
		[ '$scope', '_', '$pw',
		function( $scope, $_, $pw ){
		$scope['options'] = $pw.optionsMeta;
		$scope.pwAvatar = <?php echo json_encode($vars) ?>;
		// TODO : Fix issue with digest cycle not updating hidden form field

	}]);
</script>

<?php //echo json_encode($pwAvatar) ?>

<h3><i class="pwi-postworld"></i> Avatar</h3>

<div>
	
	<table
		class="form-table"
		ng-controller="pwUsermetaAvatarCtrl">
		<tr>
			<th><label for="twitter">Select an image</label></th>
			<td>
				<?php
					echo pw_select_image_id( array( 
						'ng_model'		=>	'pwAvatar',
						'slug'			=>	'pwAvatar',
						'label'			=>	'Avatar Image',
						'width'			=>	'256px',
					 	));?>

				<input
					type="hidden"
					name="pw_avatar"
					id="pw_avatar"
					value="{{ pwAvatar }}">
			</td>
		</tr>

	</table>

</div>
