<?php
	$uploads_dir = wp_upload_dir();
?>
<button
	type="button"
	class="button button-primary"
	wp-media-library
	media-id="setFile"
	media-type=""
	media-title="Select <?php echo $vars['label']; ?>"
	media-button="Set <?php echo $vars['label']; ?>"
	media-default-tab="upload"
	media-tabs="upload,library"
	media-multiple="false"
	media-callback="setSelectedMediaId()"
	media-set-id="<?php echo $vars['ng_model']; ?>"
	media-parent-callback="refreshOptions()"
	media-model="files.<?php echo $vars['slug']; ?>"
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
	pw-get-post="<?php echo $vars['ng_model']; ?>"
	post-model="attachments.<?php echo $vars['slug']; ?>"
	post-fields="['ID','post_title','post_type','post_meta(_all)']"
	post-loading="postLoading"
	ng-show="<?php echo $vars['ng_model']; ?>">
	<div ng-show="postLoading"><h4>Loading...</h4></div>
	<div ng-show="!postLoading">
		<h4>
			<b>{{ attachments.<?php echo $vars['slug']; ?>.post_title }}</b>
			// ID : {{ attachments.<?php echo $vars['slug']; ?>.ID }}
		</h4>
		<small>
			<a target="_blank" ng-href="<?php echo $uploads_dir['baseurl'] ?>/{{ attachments.<?php echo $vars['slug']; ?>.post_meta._wp_attached_file }}">
				<?php echo $uploads_dir['baseurl'] ?>/{{ attachments.<?php echo $vars['slug']; ?>.post_meta._wp_attached_file }}
			</a>
		</small>
		<hr class="thin">

		<?php /*
		<hr class="thin">
		<?php if( pw_dev_mode() ): ?>
			<div class="well">
				<h3>Dev Mode : $scope.attachments.<?php echo $vars['slug']; ?></h3>
				<pre><code>{{ attachments.<?php echo $vars['slug']; ?> | json }}</code></pre>
			</div>
		<?php endif ?>
		*/?>
	</div>
</div>
