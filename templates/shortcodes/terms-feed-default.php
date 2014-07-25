<script>
	postworld.controller( '<?php echo $instance; ?>', [ '$scope', function( $scope ){
		$scope.vars = <?php echo json_encode($vars); ?>;
   		$scope.termsFeed = <?php echo json_encode($terms_feed); ?>;
	}]);
</script>
<div ng-controller="<?php echo $instance; ?>">
	<div
		style="width:50%; float:left; border:1px solid #fff;padding:20px; postition:relative;"
		ng-repeat="term in termsFeed">
		{{ term.term | json }}
		<div
			ng-repeat="post in term.posts">
				<hr>

				<img pw-src="post.image.sizes.thumbnail.url"><br>
				{{ post | json }}
		</div>
	</div>
</div>
<div class="clearfix"></div>
<hr>