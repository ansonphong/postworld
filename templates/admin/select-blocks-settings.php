<?php
	global $pw;
?>


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

<!-- TEMPLATE -->
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

<hr class="thin">



<!-- SIDEBAR -->
<span class="icon-md"><i class="icon-circle-thick"></i></span>
<span
	pw-admin-sidebars="pw.sidebars">
	<select
		id="feed-blocks-template"
		ng-model="<?php echo $ng_model; ?>.widgets.sidebar"
		ng-options="value.id as value.name for value in pw.sidebars">
	</select>
</span>

<label for="feed-blocks-template">
	sidebar
	<small>: which template to insert into the blocks</small>
</label>

<hr class="thin">


<!--
'offset'	=>	2,
'increment'	=>	8,
'max' 		=> 	50,
'classes'	=>	'view-grid block-widget x-wide',
'template' 	=> 	'widget-grid',
'widgets'	=>	array(
	'sidebar'	=>	'home-page-sidebar',
	),

-->

