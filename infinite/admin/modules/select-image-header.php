

<button
	class="button button-primary"
	wp-media-library
	media-id="setImage"
	media-type="image"
	media-title="Select Header Image"
	media-button="Set Header Image"
	media-default-tab="upload"
	media-tabs="upload,library"
	media-multiple="false"
	media-callback="emitSelectedMediaId( 'images.header' )"
	media-parent-callback="refreshOptions()"
	media-model="images['header']"
	media-model-array="false">
	Select Image
</button>

<div>
	<img
		pw-image
		image-id="{{ iOptions.images.header }}"
		image-model="images['header']"
		ng-src="{{images.header.url}}"
		style="max-width:400px; height:auto; background:rgba(128,128,128,0.8)">
</div>


<!--
<pre>images: {{ images | json }}</pre>

ng-style="backgroundImage( images.link_thumbnail.sizes.medium.url, { 'background-size':'cover', 'background-position':'center' })"
media-callback="setPostImage"
media-parent-callback="localSetPostImage"
-->