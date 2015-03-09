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
<div class="columns-2">
<?php
	///// HEIGHT /////
	if( in_array( 'height', $show ) ){
	?>
	<span class="pwi-md"><i class="pwi-arrows-v"></i></span>
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
	<span class="pwi-md"><i class="pwi-clock"></i></span>
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
	<span class="pwi-md"><i class="pwi-plus"></i></span>
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
	///// TRANSITION /////
	if( in_array( 'transition', $show ) ){
	?>
	<label class="inner" for="select-transition"><i class="pwi-magic"></i> Transition</label>
	<select
		class="labeled"
		id="select-transition"
		ng-model="<?php echo $ng_model; ?>.transition"
		ng-options="option.slug as option.name for option in sliderOptions.slider.transition">
	</select>
	
	<hr class="thin">
	<?php
	}
?>
<?php
	///// HYPERLINK /////
	if( in_array( 'hyperlink', $show ) ){
	?>
	<span class="pwi-md"><i class="pwi-link"></i></span>
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
	<span class="pwi-md"><i class="pwi-pause"></i></span>
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
	<span class="pwi-md"><i class="pwi-eye"></i></span>
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
	<span class="pwi-md"><i class="pwi-eye"></i></span>
	<input type="checkbox"
		id="input-show_excerpt"
		ng-model="<?php echo $ng_model; ?>.show_excerpt">
		<label for="input-show_excerpt">Show Excerpt</label>
	<hr class="thin">
	<?php
	}
?>
</div>