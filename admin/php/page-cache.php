<script type="text/javascript">
	//////////////////// DATA CONTROLLER ////////////////////
	postworldAdmin.controller('pwCacheDataCtrl',
		[ '$scope', '$window', '_',
		function( $scope, $window, $_ ){

	}]);
</script>

<div
	pw-admin
	pw-admin-iconsets
	ng-controller="pwCacheDataCtrl"
	ng-cloak
	class="postworld">

	<h1>
		<i class="pwi-circle-medium"></i>
		Cache
	</h1>

	<hr class="thick">

	<?php

	$data = array(
		'cache_type' => 'test',
		'cache_name' => 'phong',
		'cache_hash' => pw_random_hash(),
		'cache_content' => '{"dove_name":"vishnu"}'
		);
	//echo json_encode( pw_insert_cache( $data ) );

	$delete = array(
		'cache_type' => 'test'
		);
	//echo json_encode( pw_delete_cache( $delete ) );

	
	echo json_encode( pw_get_cache( array(
		'cache_name' => '',
		'cache_hash' => '',
		)));


	?>




	<div class="row">
		<div class="col-md-6">

			

		</div>
	</div>

	<hr class="thick">

	<?php if( pw_dev_mode() ): ?>


	<?php endif; ?>

</div>