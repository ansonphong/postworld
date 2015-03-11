<?php
	global $pw;
	$ng_model = $vars['option_var'].'.'.$vars['option_key'];
	
	// Get show from vars
	$show = _get( $vars, 'show' );
	// Set defaults to show
	if( !is_array( $show ) )
		$show = array(
			'sidebar',
			'offset',
			'increment',
			'max',
			'classes',
			'background-image',
			'parallax',
			);

?>

<?php if( in_array( 'sidebar', $show ) ): ?>
	<!-- SIDEBAR -->
	<span class="icon-md"><i class="pwi-circle-thick"></i></span>
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
	<hr class="thin">
<?php endif; ?>

<div
	<?php if( in_array( 'sidebar', $show ) ): ?>
		ng-hide="<?php echo $ng_model; ?>.widgets.sidebar == null"
	<?php endif; ?>
	>

	<?php if( in_array( 'offset', $show ) ): ?>
		<!-- OFFSET -->
		<span class="icon-md"><i class="pwi-arrow-right-thin"></i></span>
		<input
			id="feed-blocks-offset"
			type="number"
			class="short"
			ng-model="<?php echo $ng_model; ?>.offset">
		<label for="feed-blocks-offset">
			offset
			<small>: how many posts before the first block</small>
		</label>
		<hr class="thin">
	<?php endif; ?>

	<?php if( in_array( 'increment', $show ) ): ?>
		<!-- INCREMENT -->
		<span class="icon-md"><i class="pwi-arrow-right-thin"></i></span>
		<input
			id="feed-blocks-increment"
			type="number"
			class="short"
			ng-model="<?php echo $ng_model; ?>.increment">
		<label for="feed-blocks-increment">
			increment
			<small>: how many posts in between each block</small>
		</label>
		<hr class="thin">
	<?php endif; ?>

	<?php if( in_array( 'max', $show ) ): ?>
		<!-- MAX -->
		<span class="icon-md"><i class="pwi-plus"></i></span>
		<input
			id="feed-blocks-max"
			type="number"
			class="short"
			ng-model="<?php echo $ng_model; ?>.max">
		<label for="feed-blocks-max">
			max
			<small>: maximum number of blocks to display</small>
		</label>
		<hr class="thin">
	<?php endif; ?>

	<?php if( in_array( 'classes', $show ) ): ?>
		<!-- CLASSES -->
		<span class="icon-md"><i class="pwi-code"></i></span>
		<input
			id="feed-blocks-classes"
			type="text"
			ng-model="<?php echo $ng_model; ?>.classes">
		<label for="feed-blocks-classes">
			classes
			<small>: CSS classes to apply to each block</small>
		</label>
		<hr class="thin">
	<?php endif; ?>

	<?php if( in_array( 'background-image', $show ) ): ?>
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
	<?php endif; ?>

	<?php if( in_array( 'parallax', $show ) ): ?>
		<!-- PARALLAX RATIO -->
		<div
			ng-show="<?php echo $ng_model; ?>.widgets.background_image.id">
			<hr class="thin">
			<span class="icon-md"><i class="pwi-arrows-v"></i></span>
			<input
				id="feed-blocks-max"
				type="number"
				class="short"
				ng-model="<?php echo $ng_model; ?>.widgets.background_image.parallax_ratio"
				placeholder="-0.5">
			<label for="feed-blocks-max">
				parallax ratio
				<small>: a decimal number typically between 0 and -1 which specifies how the background image moves when scrolling down the page</small>
			</label>
		</div>
	<?php endif; ?>


	<!-- TEMPLATE -->
	<!--
	<span class="icon-md"><i class="pwi-circle-thick"></i></span>
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