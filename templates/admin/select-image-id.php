<button
	class="button button-primary"
	wp-media-library
	media-id="setImage"
	media-type="image"
	media-title="Select <?php echo $vars['label']; ?>"
	media-button="Set <?php echo $vars['label']; ?>"
	media-default-tab="upload"
	media-tabs="upload,library"
	media-multiple="false"
	media-callback="setSelectedMediaId( '<?php echo $vars['option_var']; ?>', '<?php echo $vars['option_key']; ?>' )"
	media-parent-callback="refreshOptions()"
	media-model="images.<?php echo $vars['slug']; ?>"
	media-model-array="false">
	<i class="icon-image"></i>
	Select <?php echo $vars['label']; ?>
</button>

<span
	ng-show="<?php echo json_encode($vars['remove']); ?> && <?php echo $vars['option_var']; ?>.<?php echo $vars['option_key']; ?>">
	<button
		type="button"
		class="button deletion"
		ng-click="<?php echo $vars['option_var']; ?>.<?php echo $vars['option_key']; ?> = null;">
		<i class="icon-close"></i>
		Remove <?php echo $vars['label']; ?>
	</button>
</span>

<div
	style="position:relative;"
	ng-show="<?php echo $vars['option_var']; ?>.<?php echo $vars['option_key']; ?>"
	pw-image
	image-id="{{ <?php echo $vars['option_var']; ?>.<?php echo $vars['option_key']; ?> }}"
	image-model="images.<?php echo $vars['slug']; ?>">
	<div class="space-1"></div>
	<img
		ng-src="{{ images.<?php echo $vars['slug']; ?>.url }}"
		class="select-image"
		style="width:<?php echo $vars['width']; ?>; max-width:100%; height:auto;"
		<?php echo $vars['attributes']; ?>
		>
</div>



<!--
<pre>images: {{ images | json }}</pre>
ng-style="backgroundImage( pw.images.<?php echo $vars['slug']; ?>.sizes.medium.url, { 'background-size':'cover', 'background-position':'center' })"
media-callback="setPostImage"
media-parent-callback="localSetPostImage"
-->