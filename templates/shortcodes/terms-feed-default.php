<script>
	postworld.controller( '<?php echo $instance; ?>', [ '$scope', function( $scope ){
		 $scope.vars = <?php echo json_encode($vars); ?>;
   		$scope.termsFeed = <?php echo json_encode($terms_feed); ?>;
	}]);
</script>

<div ng-controller="<?php echo $instance; ?>">

	TERMS FEED : 
	{{ termsFeed | json }}

</div>
