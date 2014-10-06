

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
	media-callback="emitSelectedMediaId( 'images.logo_overlay' )"
	media-parent-callback="refreshOptions()"
	media-model="images['logo_overlay']"
	media-model-array="false">
	Select Image
</button>

<div>
	<img
		pw-image
		image-id="{{ iOptions.images.logo_overlay }}"
		image-model="images['logo_overlay']"
		ng-src="{{images.logo_overlay.url}}"
		style="max-width:400px; height:auto; background:rgba(128,128,128,0.8)">
</div>


<!--
<pre>images: {{ images | json }}</pre>

ng-style="backgroundImage( images.link_thumbnail.sizes.medium.url, { 'background-size':'cover', 'background-position':'center' })"
media-callback="setPostImage"
media-parent-callback="localSetPostImage"
-->