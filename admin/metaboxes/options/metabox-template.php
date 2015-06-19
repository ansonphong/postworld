<?php
/*_ __  __      _        _               
 (_)  \/  | ___| |_ __ _| |__   _____  __
 | | |\/| |/ _ \ __/ _` | '_ \ / _ \ \/ /
 | | |  | |  __/ || (_| | |_) | (_) >  < 
 |_|_|  |_|\___|\__\__,_|_.__/ \___/_/\_\
/////////////////////////////////////////*/
global $post;
$pwMeta = pw_get_postmeta( array( 'post_id' => $post->ID, 'meta_key' => PW_POSTMETA_KEY ) );
?>

<!--///// METABOX TEMPLATES /////-->
<div ng-cloak id="postworldMetabox" class="infinite postworld">
	<div ng-controller="pwMetaboxCtrl">
		<?php
			// Print the Templates
			do_action('pw_admin_options_metabox_templates');
		?>
		<!-- HIDDEN FIELD -->
		<input type="hidden" name="<?php echo PW_POSTMETA_KEY; ?>" ng-value="pwMeta | json" style="width:100%;">
		
		<?php if( pw_dev_mode() ): ?>
			<div class="well">
				<h3>$scope.pwMeta</h3>
				<!-- DEV -->
				<div class="well">
					<pre><code>{{ pwMeta | json }}</code></pre>
				</div>
			</div>
		<?php endif; ?>
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// CONTROLLER /////
	postworldAdmin.controller('pwMetaboxCtrl',
		['$scope',
			function( $scope ) {
			$scope.pwMeta = <?php echo json_encode($pwMeta); ?>;
	}]);
</script>
<?php
	// Print the Javascript(s)
	do_action('pw_admin_options_metabox_scripts');
?>