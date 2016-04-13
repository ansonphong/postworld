<?php
	wp_enqueue_media();
?>
<button
	type="button"
	class="button button-primary"
	wp-media-library
	media-id="setImage"
	media-type="image"
	media-title="Select <?php echo $vars['label']; ?>"
	media-button="Set <?php echo $vars['label']; ?>"
	media-default-tab="upload"
	media-tabs="upload,library"
	media-multiple="false"
	media-callback="setSelectedMediaId()"
	media-set-id="<?php echo $vars['ng_model']; ?>"
	media-parent-callback="refreshOptions()"
	media-model="images.<?php echo $vars['slug']; ?>"
	media-model-array="false"
	media-loading="mediaLoading">
	<i class="pwi-image"></i>
	Select <?php echo $vars['label']; ?>
</button>

<span
	ng-show="<?php echo json_encode($vars['remove']); ?> && <?php echo $vars['ng_model']; ?>">
	<button
		type="button"
		class="button deletion"
		ng-click="<?php echo $vars['ng_model']; ?> = null;">
		<i class="pwi-close"></i>
		Remove <?php echo $vars['label']; ?>
	</button>
</span>

<div
	style="position:relative;"
	ng-show="<?php echo $vars['ng_model']; ?> && <?php echo json_encode($vars['display']); ?> == true"
	pw-image
	image-id="{{ <?php echo $vars['ng_model']; ?> }}"
	image-model="images.<?php echo $vars['slug']; ?>">
	<div class="space-1"></div>
	<hr class="thin">
	<img
		ng-src="{{ images.<?php echo $vars['slug']; ?>.url }}"
		class="select-image"
		style="width:<?php echo $vars['width']; ?>; max-width:100%; height:auto; background:#ccc;"
		<?php echo $vars['attributes']; ?>
		>
</div>

<?php /*
<pre>images: {{ images | json }}</pre>
*/ ?>