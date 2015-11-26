<?php
/**
 * SET UNIQUE INSTANCE
 * This is used to identify the MVC 
 */
$instance = 'headerImageOptions_'.pw_random_string();

// Set default modules to show
if( !isset($show) )
	$show = array(
		'height',
		'proportion'
		); 

// Get the options
if( !isset( $options ) )
	$options = array();
$options = pw_admin_options( 'header-image', $options );

?>
<script>
	postworld.controller( '<?php echo $instance ?>', function($scope){
		$scope.options = <?php echo json_encode( $options ) ?>;
	});
</script>
<div ng-controller="<?php echo $instance ?>" pw-ui>
	<?php if( in_array( 'proportion', $show ) ): ?>
		<?php
			echo pw_select_setting( array(
				'setting' => 'proportion', 
				'ng_model' => $ng_model.'.proportion',
				));
		?>
		<hr
			class="thin"
			ng-show="!uiBool( <?php echo $ng_model; ?>.proportion )">

	<?php endif; ?>
	<?php if( in_array('height', $show ) ): ?>
		<!-- Show if there is no set proportion or if it's set to false/flex -->
		<div ng-show="!uiBool( <?php echo $ng_model; ?>.proportion )">
			<?php
				echo pw_select_setting( array(
					'setting' => 'height-percent', 
					'ng_model' => $ng_model.'.height',
					));
			?>
		</div>
	<?php endif; ?>
</div>