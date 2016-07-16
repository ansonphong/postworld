<?php
if( empty( $vars['gallery_options'] ) )
	$vars['gallery_options'] = array('inline','frame','horizontal','vertical'); 

if( empty( $vars['show'] ) )
	$vars['show'] = array();	

$vars['show'] = array_merge_recursive( array(
		'immersion' => array(
			'enable' => false,
			'show_for' => array('frame','horizontal','vertical')
			),
		'inline' => array(
			'show_featured_image',
			),
		'vertical' => array(
			'show_title',
			'show_caption',
			'width'
			),
		), $vars['show'] );


$gallery_templates = array(
	'inline' =>	array(
		'name' => _x( 'Inline', 'gallery type', 'postworld' ),
		'description' => __( 'Galleries appear inline with the post content as a grid of images.', 'postworld' ),
		),
	'frame' => array(
		'name' => _x( 'Frame', 'gallery type', 'postworld' ),
		'description' => __( 'All galleries in the post are merged into a single frame gallery.' ),
		),
	'horizontal' => array(
		'name' => _x( 'Horizontal', 'gallery type', 'postworld' ),
		'description' => __( 'All galleries in the post are merged into a single horizontal infinite scrolling gallery.' ),
		),
	'vertical' => array(
		'name' => _x( 'Vertical', 'gallery type', 'postworld' ),
		'description' => __( 'All galleries in the post are merged into a single vertical infinite scrolling gallery.' ),
		),
	);
$gallery_templates = apply_filters( 'pw_gallery_templates', $gallery_templates );


/**
 * Prepare gallery options and export to Angular
 */
$gallery_options = array();
foreach( $gallery_templates as $key => $value ){
	if( in_array( $key, $vars['gallery_options'] ) ){
			$value['key'] = $key;
			$gallery_options[] = $value;
		}
}
pw_print_ng_controller(array(
	'app' => 'postworldAdmin',
	'controller' => 'galleryOptionsData',
	'vars' => array(
		'galleryOptions' => $gallery_options,
		'showOptions' => $vars['show'],
		'galleryModel' => $vars['ng_model'],
		),
	));

?>

<div pw-admin-gallery-options ng-controller="galleryOptionsData">

	<div class="btn-group">
		<label
			ng-repeat="template in galleryOptions"
			class="btn"
			ng-model="<?php echo $vars['ng_model']; ?>.template"
			uib-btn-radio="template.key">
			{{ template.name }}
		</label>
	</div>
	<div class="well">
		<table>
			<tr>
				<td valign="top">
					<img
						style="float:left; margin-right:15px;"
						ng-src="<?php echo postworld_directory_uri(); ?>/images/layouts/galleries/gallery-{{ <?php echo $vars['ng_model']; ?>.template }}.png">
				</td>
				<td>
					{{ getSelectedOption(<?php echo $vars['ng_model']; ?>.template).description }}
					<?php if( _get( $vars, 'gallery_meta' ) !== false ) : ?>
						<!-- X SCROLL OPTIONS -->
						<div ng-show="<?php echo $vars['ng_model']; ?>.template == 'horizontal'">
							<hr class="thin">
							<span class="icon-md"><i class="pwi-arrows-h"></i></span>
							<input type="text" size="4" ng-model="<?php echo $vars['ng_model']; ?>.x_scroll_distance" id="horizontal-scroll-distance">
							<label for="horizontal-scroll-distance">
								<b><?php _e( 'horizontal scroll distance', 'postworld' ) ?></b>
							</label>
							<small> : <?php _e( 'Number of pixels on the right before load more images', 'postworld' ) ?>  <i>(<?php _e( 'default', 'postworld' ) ?>: 1500)</i></small>
							<hr class="thin">
							<span class="icon-md"><i class="pwi-arrows-v"></i></span>
							<input type="text" size="3" ng-model="<?php echo $vars['ng_model']; ?>.height" id="gallery-height">
							<label for="gallery-height"><b>% <?php _e( 'height', 'postworld' ) ?></b></label>
							<small> : <?php _e( 'Percentage height of the window to size the horizontal scroll gallery', 'postworld' ) ?></small>
						</div>
					<?php endif; ?>

					<?php if( in_array( 'inline', $vars['gallery_options'] ) ) : ?>
						<!-- Y SCROLL OPTIONS -->
						<div ng-show="<?php echo $vars['ng_model']; ?>.template == 'inline'">
							<?php if( in_array( 'show_featured_image', $vars['show']['inline'] ) ): ?>
								<hr class="thin">
								<input type="checkbox" ng-model="<?php echo $vars['ng_model']; ?>.inline.show_featured_image" id="inline-show-featured-image">
								<label for="inline-show-featured-image"><b><?php _ex( 'Show Featured Image', 'option', 'postworld' ) ?></b></label>
							<?php endif ?>
						</div>
					<?php endif; ?>


					<?php if( in_array( 'vertical', $vars['gallery_options'] ) ) : ?>
						<!-- Y SCROLL OPTIONS -->
						<div ng-show="<?php echo $vars['ng_model']; ?>.template == 'vertical'">
							<?php if( in_array( 'width', $vars['show']['vertical'] ) ): ?>
								<hr class="thin">
								<span class="icon-md"><i class="pwi-arrows-h"></i></span>
								<input type="text" size="3" ng-model="<?php echo $vars['ng_model']; ?>.width" id="gallery-width">
								<label for="gallery-width"><b>% <?php _e( 'width', 'postworld' ) ?></b></label>
								<small> : <?php _e( 'Percentage width of the window to size the vertical scroll gallery', 'postworld' ) ?></small>
							<?php endif ?>

							<?php if( in_array( 'show_title', $vars['show']['vertical'] ) ): ?>
								<hr class="thin">
								<span class="icon-md"><i class="pwi-eye"></i></span>
								<input type="checkbox" ng-model="<?php echo $vars['ng_model']; ?>.vertical.show_title" id="v-show-title">
								<label for="v-show-title"><b><?php _ex( 'Show Title', 'option', 'postworld' ) ?></b></label>
							<?php endif ?>

							<?php if( in_array( 'show_caption', $vars['show']['vertical'] ) ): ?>
								<hr class="thin">
								<span class="icon-md"><i class="pwi-eye"></i></span>
								<input type="checkbox" ng-model="<?php echo $vars['ng_model']; ?>.vertical.show_caption" id="v-show-caption">
								<label for="v-show-caption"><b><?php _ex( 'Show Caption', 'option', 'postworld' ) ?></b></label>
							<?php endif ?>


						</div>
					<?php endif; ?>

				</td>
			</tr>	
		</table>

		<div style="clear:both;"></div>

		<?php if( _get( $vars, 'show.immersion.enable' ) ) : ?>
			<div ng-if="showGalleryView('showImmersion')">
				<hr class="thin">
				<label>
					<input type="checkbox" ng-model="<?php echo $vars['ng_model']; ?>.immersive">
					<b><?php _e( 'Make gallery immersive', 'postwold' ) ?></b>
					<small>: <?php _e( 'Hides other content to immerse browser in the gallery.', 'postwold' ) ?> </small>
				</label>
			</div>
		<?php endif ?>

	</div>



</div>