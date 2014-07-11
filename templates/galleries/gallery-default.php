<!-- POSTWORLD GALLERY SHORTCODE : <?php echo $gallery['instance']; ?> -->
<script>
	postworld.controller( 'pwGalleryInstance_<?php echo $gallery['instance']; ?>',
		[ '$scope', '_', '$pw', 'pwImages', 'pwPosts', 'pwModal',
		function( $scope, $_, $pw, $pwImages, $pwPosts, $pwModal ){
		
		var instance = "<?php echo $gallery['instance']; ?>";
		var galleryInstance = "gallery-" + instance;
		var galleryPosts = <?php echo json_encode( $gallery['posts'] ); ?>;

		$pwPosts.insertFeed( galleryInstance, { posts: galleryPosts } );
		$scope.feed = $pwPosts.getFeed( galleryInstance );

	}]);
</script>
<div
	class="pw-gallery-shortcode"
	ng-controller="pwGalleryInstance_<?php echo $gallery['instance']; ?>">
	<hr>
	<masonry
		column-width=".grid-sizer"
		masonry-options='{ "gutter": 0, "transitionDuration":0 }'>
		<div
			pw-grid
			pw-modal-access>
			<div class="grid-sizer"></div>
			<div
				class="gallery-post masonry-brick"
				ng-repeat="galleryPost in feed.posts"
				ng-class="setGridClass( galleryPost.image.tags )"
				ng-style="setGridStyle( galleryPost.image.tags )"
				ng-click="openModal({ mode:'feed', post:galleryPost })">
				<div class="overlay"></div>
				<div class="gallery-image"
					style="background-image:url({{ galleryPost.image.sizes[ getImageSize('thumb-', galleryPost.image.tags ) ].url }})">
				</div>
			</div>
		</div>
	</masonry>
	<hr>
</div>