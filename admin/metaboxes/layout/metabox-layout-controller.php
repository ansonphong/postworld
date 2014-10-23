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
		i-admin-layout
		ng-controller="pwLayoutMetaboxCtrl"
		id="infinite_admin"
		class="pw-metabox metabox-side metabox-layout">
		<?php
			echo i_layout_single_options( array( 'context'	=>	'postAdmin' ) );
			// Action Hook
			do_action('pw_layout_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_layout_post" ng-value="post | json" style="width:100%;">
		
		<!-- DEV : Test Output -->
		<hr><pre>POST : {{ post | json }}</pre>
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// APP /////
	var pwLayoutMetabox = angular.module( 'pwLayoutMetabox', ['infinite'] );
	
	///// CONTROLLER /////
	pwLayoutMetabox.controller('pwLayoutMetaboxCtrl',
		['$scope', 'pwData', '_', '$log',
			function( $scope, $pwData, $_, $log ) {

			/// LOAD IN DATA SOURCES ///
			$scope.iLayoutOptions = <?php echo json_encode( i_layout_options() ); ?>;
			$scope.iSidebars = <?php echo json_encode( i_get_option( array( 'option_name' => 'i-sidebars' ) ) ); ?>;
			$scope.iTemplates = <?php echo json_encode( pw_get_templates( array( 'ext' => 'php', 'type' => 'dir' ) ) ); ?>;
			$scope.iLayouts = <?php echo json_encode( i_get_option( array( 'option_name' => 'i-layouts' ) ) ); ?>;
			$scope.post = <?php echo json_encode( pw_get_post( $post->ID, array('ID','post_meta('.pw_postmeta_key.')') ) ); ?>;

			// Create layout object
			if( !$_.objExists( $scope.post, 'post_meta.layout' ) )
				$scope.post = $_.setObj( $scope.post, 'post_meta.pw_meta.layout', {} );

			// TODO : Add a PW global for pw_postmeta_key and pw_usermeta_key, use that global here

			// ADD : If 'default' template selected, delete the layout object
			// If no template selected, display as 'default'

			// Provide context for the layouts controller
			$scope.context = {
				name: 'single',
				label: 'Single',
				icon: 'icon-circle-medium',
			};

	}]);
	
</script>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_layout_metabox_scripts');
?>

<script>
	///// BOOTSTRAP APP /////
	angular.bootstrap(document.getElementById("pwLayoutMetabox"),['pwLayoutMetabox']);
</script>
