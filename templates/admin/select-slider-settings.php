<?php
/**
 * SET UNIQUE INSTANCE
 * This is used to identify the slider's MVC 
 */
$instance = 'sliderSettings_'.pw_random_string();

/**
 * SET DEFAULT OPTIONS
 * These options can be overriden
 * by passing in an array of options.
 */
$default_options = array(
	'transition' => array(
		array(
			'value' => false,
			'name' => 'None',
			),
		array(
			'value' => 'slide',
			'name' => 'Slide',
			),
		array(
			'value' => 'fade',
			'name' => 'Fade',
			),
		),
	'proportion' => array(
		array(
			'value' => false,
			'name' => 'Flexible',
			),
		array(
			'value' => 2,
			'name' => '2 : 1',
			),
		array(
			'value' => 2.5,
			'name' => '2.5 : 1',
			),
		array(
			'value' => 3,
			'name' => '3 : 1',
			),
		),
	);

if( !is_array($options) )
	$options = $default_options;
else
	$options = array_replace_recursive( $default_options, $options );

/**
 * SET DEFAULT OPTIONS TO SHOW
 * These options can be overriden
 * by passing in an array of options.
 */
if( !is_array($show) )
	$show = array(
		'height',
		'interval',
		'max_slides',
		'transition',
		'no_pause',
		'hyperlink',
		'show_title',
		'show_excerpt',
		//'proportion'
		);
?>

<script>
	postworld.controller( '<?php echo $instance ?>', function($scope){
		$scope.options = <?php echo json_encode( $options ) ?>;
	});
</script>

<div ng-controller="<?php echo $instance ?>">

<?php
	///// HEIGHT /////
	if( in_array( 'height', $show ) ){
	?>
	<span class="icon-md"><i class="pwi-arrows-v"></i></span>
	<input
		id="input-height"
		class="short"
		size="3"
		type="number"
		ng-model="<?php echo $ng_model; ?>.height">
		<label for="input-height"><b>%</b> height</label>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// INTERVAL /////
	if( in_array( 'interval', $show ) ){
	?>
	<span class="icon-md"><i class="pwi-clock"></i></span>
	<input
		id="input-interval"
		class="short"
		size="3"
		type="number"
		ng-model="<?php echo $ng_model; ?>.interval">
		<label for="input-interval"><b><span tooltip="milliseconds">ms</span></b> interval</label>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// MAXIMUM SLIDES /////
	if( in_array( 'max_slides', $show ) ){
	?>
	<span class="icon-md"><i class="pwi-plus"></i></span>
	<input type="text"
		id="input-maxposts"
		size="3"
		ng-model="<?php echo $ng_model; ?>.query_vars.max_posts">
	<label for="input-maxposts">maximum slides</label>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// HYPERLINK /////
	if( in_array( 'hyperlink', $show ) ){
	?>
	<span class="icon-md"><i class="pwi-link"></i></span>
	<input type="checkbox"
		id="input-hyperlink"
		ng-model="<?php echo $ng_model; ?>.hyperlink">
		<label for="input-hyperlink">Link slides to their respective posts/pages</label>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// NO PAUSE /////
	if( in_array( 'no_pause', $show ) ){
	?>
	<span class="icon-md"><i class="pwi-pause"></i></span>
	<input type="checkbox"
		id="input-no_pause"
		ng-model="<?php echo $ng_model; ?>.no_pause">
		<label for="input-no_pause">Do not pause slider on mouse over</label>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// SHOW TITLE /////
	if( in_array( 'show_title', $show ) ){
	?>
	<span class="icon-md"><i class="pwi-eye"></i></span>
	<input type="checkbox"
		id="input-show_title"
		ng-model="<?php echo $ng_model; ?>.show_title">
		<label for="input-show_title">Show Title</label>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// SHOW EXCERPT /////
	if( in_array( 'show_excerpt', $show ) ){
	?>
	<span class="icon-md"><i class="pwi-eye"></i></span>
	<input type="checkbox"
		id="input-show_excerpt"
		ng-model="<?php echo $ng_model; ?>.show_excerpt">
		<label for="input-show_excerpt">Show Excerpt</label>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// TRANSITION /////
	if( in_array( 'transition', $show ) ){
	?>
	<label class="inner" for="select-transition">
		<i class="pwi-wand"></i>
		Transition
	</label>
	<select
		class="labeled"
		id="select-transition"
		ng-model="<?php echo $ng_model; ?>.transition"
		ng-options="option.value as option.name for option in options.transition">
	</select>
	<hr class="thin">
	<?php
	}
?>
<?php
	///// PROPORTION /////
	if( in_array( 'proportion', $show ) ){
	?>
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
	<?php
	}
?>

</div>