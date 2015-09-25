<!-- POSTWORLD GALLERY SHORTCODE : <?php echo $vars['instance']; ?> -->
<script>
	postworld.controller( '<?php echo $vars['instance']; ?>',
		[ '$scope', '_', '$pw', 'pwImages', 'pwPosts', 'pwModal','pwData',
		function( $scope, $_, $pw, $pwImages, $pwPosts, $pwModal, $pwData ){
		var instance = "<?php echo $vars['instance']; ?>";
		var galleryInstance = "gallery-" + instance;
		var galleryPosts = <?php echo json_encode( $vars['posts'] ); ?>;

		$pwData.insertFeed( galleryInstance, { posts: galleryPosts } );
		$scope.feed = $pwPosts.getFeed( galleryInstance );

	}]);
	pwRegisterController( "<?php echo $vars['instance']; ?>" );
</script>

<div
	class="pw-gallery-shortcode"
	ng-cloak
	ng-controller="<?php echo $vars['instance']; ?>">
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
				ng-click="openModal({ mode:'feed', post:galleryPost })">

				<div class="overlay">
					<h2>{{ galleryPost.post_title }}</h2>
				</div>

				<div class="gallery-image"
					style="background-image:url({{ galleryPost.image.sizes[ getImageSize('thumb-', galleryPost.image.tags ) ].url }})">
				</div>
			</div>
		</div>
	</masonry>
</div>