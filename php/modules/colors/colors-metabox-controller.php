<?php
global $post;
?>

<!--///// METABOX WRAPPER /////-->
<div id="pwColorsMetabox" class="postworld">
	<div
		pw-admin-colors
		ng-controller="pwColorsMetaboxCtrl"
		class="pw-metabox metabox-side metabox-colors"
		style="position:relative;">

		<table style="width:100%;">

			<?php if( empty( $pw_post['image']['colors'] ) && is_array( $colors ) ): ?>
				<tr>
					<?php foreach( $colors as $color ) : ?>
						<td style="height:32px; background:<?php echo $color ?>"></td>
					<?php endforeach ?>
				</tr>
			<?php elseif( is_array( $pw_post['image']['colors'] ) ): ?>
				<?php foreach( $pw_post['image']['colors'] as $profile ) : ?>
				<tr>
					<?php foreach( $profile['colors'] as $color ) : ?>
						<td style="height:32px; background:<?php echo $color['hex'] ?>"></td>
					<?php endforeach ?>
				</tr>
				<?php endforeach ?>
			<?php endif ?>

		</table>

		<?php
			//echo pw_colors_single_options( array( 'context'	=>	'postAdmin' ) );
			// Action Hook
			do_action('pw_colors_metabox_templates');
		?>

		<?php /*if( pw_dev_mode() ): ?>
			<hr><pre>DEV MODE</pre>
			<?php echo json_encode( $pw_post, JSON_PRETTY_PRINT ); ?>
		<?php endif */ ?>
		
	</div>	
</div>

<!--///// METABOX SCRIPTS /////-->
<script>
	///// CONTROLLER /////
	postworldAdmin.controller('pwColorsMetaboxCtrl',
		['$scope', 'pwData', '_', '$log',
			function( $scope, $pwData, $_, $log ) {


	}]);
	
</script>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_colors_metabox_scripts');
?>