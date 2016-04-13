<?php
/**
 * SET UNIQUE INSTANCE
 * This is used to identify the slider's MVC 
 */
$instance = 'sliderSettings_'.pw_random_string();

if( !isset( $options ) )
	$options = array();
$options = pw_admin_options( 'slider', $options );

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
		//'parallax_depth',
		//'proportion'
		);

pw_print_ng_controller(array(
	'app' => 'postworld',
	'controller' => $instance,
	'vars' => array(
		'options' => $options,
		),
	));

?>

<div class="postworld" ng-controller="<?php echo $instance ?>">

<?php
	///// PROPORTION /////
	if( in_array( 'proportion', $show ) ){
		echo pw_select_setting( array(
			'setting' => 'proportion', 
			'ng_model' => $ng_model.'.proportion',
			));
		?><hr class="thin"><?php
	}
	?>

<?php
	///// HEIGHT /////
	if( in_array( 'height', $show ) ){
		echo pw_select_setting(array(
			'setting' => 'height',
			'ng_model' => $ng_model.'.height',
			//'methods' => array('window-base','window-percent','pixels'),
			));
		?><hr class="thin"><?php
	}
	?>

<?php if( in_array( 'interval', $show ) ): ?>
	<!-- INTERVAL -->
	<span class="icon-md"><i class="pwi-clock"></i></span>
	<input
		id="input-interval"
		class="short"
		size="3"
		type="number"
		ng-model="<?php echo $ng_model; ?>.interval">
		<label for="input-interval">
			<b><?php _e('milliseconds','postworld') ?></b> <?php _e('interval','postworld') ?>
		</label>
	<hr class="thin">
<?php endif ?>

<?php if( in_array( 'parallax_depth', $show ) ) : ?>
	<!-- PARALLAX DEPTH -->
	<span class="icon-md"><i class="pwi-images"></i></span>
	<input
		id="parallax-depth"
		class="short"
		size="3"
		type="number"
		ng-model="<?php echo $ng_model; ?>.parallax_depth">
	<label for="parallax-depth">
		<?php _e('parallax depth','postworld') ?>
	</label>
	<hr class="thin">
<?php endif ?>

<?php if( in_array( 'max_slides', $show ) ) : ?>
	<!-- MAXIMUM SLIDES -->
	<span class="icon-md"><i class="pwi-plus"></i></span>
	<input type="text"
		id="input-maxposts"
		size="3"
		ng-model="<?php echo $ng_model; ?>.query_vars.max_posts">
	<label for="input-maxposts">
		<?php _e('maximum slides','postworld') ?>
	</label>
	<hr class="thin">
<?php endif ?>

<?php if( in_array( 'hyperlink', $show ) ) : ?>
	<!-- HYPERLINK -->
	<span class="icon-md"><i class="pwi-link"></i></span>
	<input type="checkbox"
		id="input-hyperlink"
		ng-model="<?php echo $ng_model; ?>.hyperlink">
		<label for="input-hyperlink">
			<?php _e('Link slides to their respective posts/pages','postworld') ?>
		</label>
	<hr class="thin">
<?php endif ?>

<?php if( in_array( 'no_pause', $show ) ) : ?>
	<!-- NO PAUSE -->
	<span class="icon-md"><i class="pwi-pause"></i></span>
	<input type="checkbox"
		id="input-no_pause"
		ng-model="<?php echo $ng_model; ?>.no_pause">
		<label for="input-no_pause">
			<?php _e('Do not pause slider on mouse over','postworld') ?>
		</label>
	<hr class="thin">
<?php endif ?>

<?php if( in_array( 'show_title', $show ) ) : ?>
	<!-- SHOW TITLE -->
	<span class="icon-md"><i class="pwi-eye"></i></span>
	<input type="checkbox"
		id="input-show_title"
		ng-model="<?php echo $ng_model; ?>.show_title">
		<label for="input-show_title">
			<?php _e('Show Title','postworld') ?>
		</label>
	<hr class="thin">
<?php endif ?>

<?php if( in_array( 'show_excerpt', $show ) ) : ?>
	<!-- SHOW EXCERPT -->
	<span class="icon-md"><i class="pwi-eye"></i></span>
	<input type="checkbox"
		id="input-show_excerpt"
		ng-model="<?php echo $ng_model; ?>.show_excerpt">
		<label for="input-show_excerpt">
			<?php _e('Show Excerpt','postworld') ?>
		</label>
	<hr class="thin">
<?php endif ?>

<?php if( in_array( 'transition', $show ) ) : ?>
	<!-- TRANSITION -->
	<label class="inner" for="select-transition">
		<i class="pwi-wand"></i>
		<?php _e('Transition','postworld') ?>
	</label>
	<select
		class="labeled"
		id="select-transition"
		ng-model="<?php echo $ng_model; ?>.transition"
		ng-options="option.value as option.name for option in options.transition">
	</select>
	<hr class="thin">
<?php endif ?>

</div>