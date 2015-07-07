<?php
/*____             _                                   _ 
 | __ )  __ _  ___| | ____ _ _ __ ___  _   _ _ __   __| |
 |  _ \ / _` |/ __| |/ / _` | '__/ _ \| | | | '_ \ / _` |
 | |_) | (_| | (__|   < (_| | | | (_) | |_| | | | | (_| |
 |____/ \__,_|\___|_|\_\__, |_|  \___/ \__,_|_| |_|\__,_|
                       |___/                             
////////////////////////////////////////////////////////*/
global $post;
?>

<!--///// METABOX WRAPPER /////-->
<div
	id="pwBackgroundMetabox"
	class="postworld"
	ng-cloak>
	<div
		pw-admin-background
		ng-controller="pwBackgroundMetaboxCtrl"
		id="poststuff"
		class="pw-metabox metabox-side metabox-background">
		<?php
			echo pw_ob_include( pw_get_admin_template( 'metabox-background' ) );
			//echo i_background_single_options( array( 'context'	=>	'postAdmin' ) );
			// Action Hook
			do_action('pw_background_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="pw_background_post" ng-value="pw_background_post | json" style="width:100%;">
		
		<!-- DEV : Test Output 
		<hr><pre>POST : {{ pw_background_post | json }}</pre>
		-->
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>

	///// CONTROLLER /////
	postworldAdmin.controller('pwBackgroundMetaboxCtrl',
		['$scope', 'pwData', '_', '$log',
			function( $scope, $pwData, $_, $log ) {

			/// LOAD IN DATA SOURCES ///
			$scope.pwBackgrounds = <?php echo json_encode( i_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUNDS ) ) ); ?>;
			$scope.pw_background_post = <?php echo json_encode( pw_get_post( $post->ID, array('ID','post_meta('.pw_postmeta_key.')') ) ); ?>;

			// Create background object
			if( !$_.objExists( $scope.pw_background_post, 'post_meta.<?php echo pw_postmeta_key; ?>.background' ) )
				$scope.pw_background_post = $_.setObj( $scope.pw_background_post, 'post_meta.<?php echo pw_postmeta_key; ?>.background', {} );

			// TODO : Add a PW global for pw_postmeta_key and pw_usermeta_key, use that global here

			// ADD : If 'default' template selected, delete the background object
			// If no template selected, display as 'default'

			// Provide context for the backgrounds controller
			$scope.context = {
				name: 'single',
				label: 'Single',
				icon: 'pwi-circle-medium',
			};

	}]);
	
</script>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_background_metabox_scripts');
?>
