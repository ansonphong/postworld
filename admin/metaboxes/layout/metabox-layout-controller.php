<?php
/*_                            _   
 | |    __ _ _   _  ___  _   _| |_ 
 | |   / _` | | | |/ _ \| | | | __|
 | |__| (_| | |_| | (_) | |_| | |_ 
 |_____\__,_|\__, |\___/ \__,_|\__|
             |___/                 
///////////////////////////////////*/
global $post;
?>

<!--///// METABOX WRAPPER /////-->
<div id="pwLayoutMetabox" class="postworld">
	<div
		pw-admin-layout
		ng-controller="pwLayoutMetaboxCtrl"
		id="poststuff"
		class="pw-metabox metabox-side metabox-layout"
		ng-cloak>
		<?php
			echo pw_layout_single_options( array( 'context'	=>	'postAdmin' ) );
			// Action Hook
			do_action('pw_layout_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_layout_post" ng-value="pw_layout_post | json" style="width:100%;">
		
		<?php if( pw_dev_mode() ): ?>
			<hr><pre>pw_layout_post.post_meta.pw_meta.layout : {{ pw_layout_post.post_meta.pw_meta.layout | json }}</pre>
		<?php endif; ?>
		
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// CONTROLLER /////
	postworldAdmin.controller('pwLayoutMetaboxCtrl',
		['$scope', 'pwData', '$_', '$log',
			function( $scope, $pwData, $_, $log ) {

			/// LOAD IN DATA SOURCES ///
			$scope.pwLayoutOptions = <?php echo json_encode( pw_layout_options() ); ?>;
			$scope.pwSidebars = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) ) ); ?>;
			$scope.pwTemplates = <?php echo json_encode( pw_get_templates( array( 'ext' => 'php', 'type' => 'dir' ) ) ); ?>;
			$scope.pwLayouts = <?php echo json_encode( pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) ) ); ?>;
			$scope.pw_layout_post = <?php echo json_encode( pw_get_post( $post->ID, array('ID','post_meta('.pw_postmeta_key.')') ) ); ?>;

			// Create layout object
			if( !$_.objExists( $scope.pw_layout_post, 'post_meta.<?php echo pw_postmeta_key; ?>.layout' ) )
				$scope.pw_layout_post = $_.setObj( $scope.pw_layout_post, 'post_meta.<?php echo pw_postmeta_key; ?>.layout', {} );

			// If the Layout object is empty
			if( _.isEmpty( $_.get( $scope.pw_layout_post, 'post_meta.pw_meta.layout' ) ) )
				// Make it an object
				$scope.pw_layout_post = $_.set( $scope.pw_layout_post, 'post_meta.pw_meta.layout', { template : 'default' } );

			// TODO : Add a PW global for pw_postmeta_key and pw_usermeta_key, use that global here

			// ADD : If 'default' template selected, delete the layout object
			// If no template selected, display as 'default'

			// Provide context for the layouts controller
			$scope.context = {
				name: 'single',
				label: 'Single',
				icon: 'pwi-circle-medium',
			};

	}]);
	
</script>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_layout_metabox_scripts');
?>