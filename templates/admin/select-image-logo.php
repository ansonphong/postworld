

<button
	class="button button-primary"
	wp-media-library
	media-id="setImage"
	media-type="image"
	media-title="Select Logo"
	media-button="Set Logo"
	media-default-tab="upload"
	media-tabs="upload,library"
	media-multiple="false"
	media-callback="emitSelectedMediaId( 'images.logo' )"
	media-parent-callback="refreshOptions()"
	media-model="images.logo"
	media-model-array="false">
	Select Image
</button>
<hr class="thin">
<div>
	<img
		pw-image
		image-id="{{ iOptions.images.logo }}"
		image-model="images['logo']"
		ng-src="{{images.logo.url}}"
		class="select-image"
		style="width:400px; max-width:100%; height:auto;">
</div>


<!--
<pre>images: {{ images | json }}</pre>

ng-style="backgroundImage( images.link_thumbnail.sizes.medium.url, { 'background-size':'cover', 'background-position':'center' })"
media-callback="setPostImage"
media-parent-callback="localSetPostImage"
-->