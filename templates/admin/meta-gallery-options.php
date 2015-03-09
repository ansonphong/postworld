<div class="btn-group">
	<label
		ng-repeat="template in options.gallery.template"
		class="btn" ng-model="<?php echo $vars['ng_model']; ?>.template" btn-radio="template.slug">
		{{ template.name }}
	</label>
</div>
<div class="well" ng-show="<?php echo $vars['ng_model']; ?>.template != 'inline'">
	<table>
		<tr>
			<td valign="top">
				<img
					style="float:left; margin-right:15px;"
					ng-src="<?php echo get_infinite_directory_uri(); ?>/images/layouts/galleries/i-gallery-layout-{{ <?php echo $vars['ng_model']; ?>.template }}.png">
			</td>
			<td>
				{{ getSelectedOption('gallery.template').description }}
				
				<!-- X SCROLL OPTIONS -->
				<div ng-show="<?php echo $vars['ng_model']; ?>.template == 'horizontal'">
					<span class="pwi-md"><i class="pwi-arrows-h"></i></span>
					<input type="text" size="4" ng-model="<?php echo $vars['ng_model']; ?>.x_scroll_distance" id="horizontal-scroll-distance">
					<label for="horizontal-scroll-distance"><b>horizontal scroll distance</b></label>
					<small> - Number of pixels on the right before load more images <i>(default: 1500)</i></small>
					<hr class="thin">
					<span class="pwi-md"><i class="pwi-arrows-v"></i></span>
					<input type="text" size="3" ng-model="<?php echo $vars['ng_model']; ?>.height" id="gallery-height">
					<label for="gallery-height"><b>% height</b></label>
					<small> - Percentage height of the window to size the horizontal scroll gallery</small>
					<!--
					- Include the Featured Image as the first image in the gallery (default : false)
					-->
				</div>

				<!-- Y SCROLL OPTIONS -->
				<div ng-show="<?php echo $vars['ng_model']; ?>.template == 'vertical'">
					<span class="pwi-md"><i class="pwi-arrows-v"></i></span>
					<input type="text" size="4" ng-model="<?php echo $vars['ng_model']; ?>.y_scroll_distance" id="vertical-scroll-distance">
					<label for="vertical-scroll-distance"><b>vertical scroll distance</b></label>
					<small> - Number of pixels on the bottom before load more images <i>(default: 1000)</i></small>
					<hr class="thin">
					<span class="pwi-md"><i class="pwi-arrows-h"></i></span>
					<input type="text" size="3" ng-model="<?php echo $vars['ng_model']; ?>.width" id="gallery-width">
					<label for="gallery-width"><b>% width</b></label>
					<small> - Percentage width of the window to size the vertical scroll gallery</small>
					
				</div>

			</td>
		</tr>	
	</table>




	<div style="clear:both;"></div>
</div>