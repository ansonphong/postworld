<?php
	global $pw;
	$ng_model = $vars['option_var'].'.'.$vars['option_key'];
?>

<!-- SIDEBAR -->
<span class="icon-md"><i class="icon-circle-thick"></i></span>
<span
	pw-sidebars="pw.sidebars">
	<select
		id="feed-blocks-template"
		ng-model="<?php echo $ng_model; ?>.widgets.sidebar"
		ng-options="value.id as value.name for value in pw.sidebars">
		<option value="">None</option>
	</select>
</span>

<label for="feed-blocks-template">
	sidebar
	<small>: which widget area to inject into the blocks</small>
</label>

<div ng-hide="<?php echo $ng_model; ?>.widgets.sidebar == null">

	<hr class="thin">

	<!-- OFFSET -->
	<span class="icon-md"><i class="icon-arrow-right-thin"></i></span>
	<input
		id="feed-blocks-offset"
		type="number"
		ng-model="<?php echo $ng_model; ?>.offset">
	<label for="feed-blocks-offset">
		offset
		<small>: how many posts before the first block</small>
	</label>


	<hr class="thin">


	<!-- INCREMENT -->
	<span class="icon-md"><i class="icon-arrow-right-thin"></i></span>
	<input
		id="feed-blocks-increment"
		type="number"
		ng-model="<?php echo $ng_model; ?>.increment">
	<label for="feed-blocks-increment">
		increment
		<small>: how many posts in between each block</small>
	</label>


	<hr class="thin">


	<!-- MAX -->
	<span class="icon-md"><i class="icon-plus"></i></span>
	<input
		id="feed-blocks-max"
		type="number"
		ng-model="<?php echo $ng_model; ?>.max">
	<label for="feed-blocks-max">
		max
		<small>: maximum number of blocks to display</small>
	</label>


	<hr class="thin">

	<!-- CLASSES -->
	<span class="icon-md"><i class="icon-code"></i></span>
	<input
		id="feed-blocks-classes"
		type="text"
		ng-model="<?php echo $ng_model; ?>.classes">
	<label for="feed-blocks-classes">
		classes
		<small>: CSS classes to apply to each block</small>
	</label>

	<hr class="thin">

	<!-- BACKGROUND IMAGE -->

	<?php
		echo pw_select_image_id( array( 
			'ng_model'		=>	$option_var . '.' . $option_key . '.widgets.background_image.id',
			'slug'			=>	'blocksBg',
			'label'			=>	'Background Image',
			'width'			=>	'400px',
			'remove'		=>	true,
			'display'		=> 	true,
		 	));?>

	<!-- PARALLAX RATIO -->
	<div
		ng-show="<?php echo $ng_model; ?>.widgets.background_image.id">
		<hr class="thin">
		<span class="icon-md"><i class="icon-arrows-v"></i></span>
		<input
			id="feed-blocks-max"
			type="number"
			ng-model="<?php echo $ng_model; ?>.widgets.background_image.parallax_ratio"
			placeholder="-0.5">
		<label for="feed-blocks-max">
			parallax ratio
			<small>: a decimal number typically between 0 and -1 which specifies how the background image moves when scrolling down the page</small>
		</label>
	</div>


	<!-- TEMPLATE -->
	<!--
	<span class="icon-md"><i class="icon-circle-thick"></i></span>
	<span
		pw-admin-templates="pw.templates">
		<select
			id="feed-blocks-template"
			ng-model="<?php echo $ng_model; ?>.template"
			ng-options="value for value in pw.templates.html.blocks">
		</select>
	</span>

	<label for="feed-blocks-template">
		template
		<small>: which template to insert into the blocks</small>
	</label>
	-->

</div>