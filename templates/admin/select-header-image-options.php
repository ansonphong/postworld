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
		<label class="inner" for="select-proportion">
			<i class="pwi-square-thin"></i>
			Proportion
		</label>
		<select
			class="labeled"
			id="select-proportion"
			ng-model="<?php echo $ng_model; ?>.proportion"
			ng-options="option.value as option.name for option in options.proportion">
		</select>
		<hr class="thin">
	<?php endif; ?>
	<?php if( in_array('height', $show ) ): ?>
		<!-- Show if there is no set proportion or if it's set to false/flex -->
		<div ng-show="!uiBool( <?php echo $ng_model; ?>.proportion )">
			<div class="icon-md"><i class="pwi-arrows-v"></i></div>
			<input
				id="input-height"
				size="3"
				ng-model="<?php echo $ng_model; ?>.height"> 
			<label for="input-height">% height</label>
		</div>
	<?php endif; ?>
</div>