<button
	class="button button-primary"
	wp-media-library
	media-id="setImage"
	media-type="image"
	media-title="Select Favicon"
	media-button="Set Favicon"
	media-default-tab="upload"
	media-tabs="upload,library"
	media-multiple="false"
	media-callback="setSelectedMediaId( '<?php echo $vars['option_var']; ?>', '<?php echo $vars['option_subkey']; ?>' )"
	media-parent-callback="refreshOptions()"
	media-model="images.favicon"
	media-model-array="false">
	Select Image
</button>

<hr class="thin">

<div>
	<img
		pw-image
		image-id="{{ <?php echo $vars['option_var']; ?>.<?php echo $vars['option_subkey']; ?> }}"
		image-model="images['favicon']"
		ng-src="{{images.favicon.url}}"
		class="select-image"
		style="width:32px; max-width:100%; height:auto;">
</div>