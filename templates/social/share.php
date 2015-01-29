<?php
	$s = pw_get_social_share_meta( $vars );

	$meta = pw_get_social_media_meta();
?>

<?php foreach( $s as $key => $value ): ?>

	<span
		class="pull-left"
		tooltip="<?php echo _get($meta,$key.'.share_label') ?>"
		tooltip-popup-delay="500"
		tooltip-placement="bottom">
		<a href="<?php echo _get( $value, 'link' ); ?>" target="_blank">
			<i class="icon <?php echo _get($meta,$key.'.icon') ?>"></i>
		</a>
	</span>

<?php endforeach; ?>
