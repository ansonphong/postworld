

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
	media-callback="emitSelectedMediaId( 'images.favicon' )"
	media-model="images['favicon']"
	media-model-array="false">
	Select Image
	<!-- { option_name: 'i-options', key:'images.favicon', value:'id' } -->
</button>
<hr class="thin">
<div>
	<img
		pw-image
		image-id="{{ iOptions.images.favicon }}"
		image-model="images['favicon']"
		ng-src="{{images.favicon.url}}"
		class="select-image"
		style="max-width:64px; height:auto;">
</div>


<!--
<pre>images: {{ images | json }}</pre>

ng-style="backgroundImage( images.link_thumbnail.sizes.medium.url, { 'background-size':'cover', 'background-position':'center' })"
media-callback="setPostImage"
media-parent-callback="localSetPostImage"
-->