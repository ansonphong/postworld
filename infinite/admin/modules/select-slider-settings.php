<?php
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
			);
?>

<?php
	///// HEIGHT /////
	if( in_array( 'height', $show ) ){
	?>
	<span class="icon-md"><i class="icon-arrows-v"></i></span>
	<input
		
		id="input-height"
		size="3"
		ng-model="<?php echo $ng_model; ?>.height">
		<label for="input-height">% height</label>
	<hr>
	<?php
	}
?>
<?php
	///// INTERVAL /////
	if( in_array( 'interval', $show ) ){
	?>
	<span class="icon-md"><i class="icon-clock"></i></span>
	<input
		id="input-interval"
		size="3"
		ng-model="<?php echo $ng_model; ?>.interval">
		<label for="input-interval">milliseconds interval</label>
	<hr>
	<?php
	}
?>
<?php
	///// MAXIMUM SLIDES /////
	if( in_array( 'max_slides', $show ) ){
	?>
	<span class="icon-md"><i class="icon-plus"></i></span>
	<input type="text"
		id="input-maxposts"
		size="3"
		ng-model="<?php echo $ng_model; ?>.query_vars.max_posts">
	<label for="input-maxposts">maximum slides</label>
	<hr>
	<?php
	}
?>
<?php
	///// TRANSITION /////
	if( in_array( 'transition', $show ) ){
	?>
	<span class="icon-md"><i class="icon-magic"></i></span>
	<select
		class="labeled"
		id="select-transition"
		ng-model="<?php echo $ng_model; ?>.transition"
		ng-options="option.slug as option.name for option in sliderOptions.slider.transition">
	</select>
	<label for="select-transition">transition</label>
	<hr>
	<?php
	}
?>
<?php
	///// HYPERLINK /////
	if( in_array( 'hyperlink', $show ) ){
	?>
	<span class="icon-md"><i class="icon-link"></i></span>
	<input type="checkbox"
		id="input-hyperlink"
		ng-model="<?php echo $ng_model; ?>.hyperlink">
		<label for="input-hyperlink">Link slides to their respective posts/pages</label>
	<hr>
	<?php
	}
?>
<?php
	///// NO PAUSE /////
	if( in_array( 'no_pause', $show ) ){
	?>
	<span class="icon-md"><i class="icon-pause"></i></span>
	<input type="checkbox"
		id="input-no_pause"
		ng-model="<?php echo $ng_model; ?>.no_pause">
		<label for="input-no_pause">Do not pause slider on mouse over</label>
	<hr>
	<?php
	}
?>
<?php
	///// SHOW TITLE /////
	if( in_array( 'show_title', $show ) ){
	?>
	<span class="icon-md"><i class="icon-eye"></i></span>
	<input type="checkbox"
		id="input-show_title"
		ng-model="<?php echo $ng_model; ?>.show_title">
		<label for="input-show_title">Show Title</label>
	<hr>
	<?php
	}
?>
<?php
	///// SHOW EXCERPT /////
	if( in_array( 'show_excerpt', $show ) ){
	?>
	<span class="icon-md"><i class="icon-eye"></i></span>
	<input type="checkbox"
		id="input-show_excerpt"
		ng-model="<?php echo $ng_model; ?>.show_excerpt">
		<label for="input-show_excerpt">Show Excerpt</label>
	<hr>
	<?php
	}
?>