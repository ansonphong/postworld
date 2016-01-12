<?php

$s = pw_get_social_share_meta( $vars );
$meta = pw_get_social_media_meta();

if( is_array( $s ) )
	foreach( $s as $key => $value ): ?>
		<a
			href="<?php echo _get( $value, 'link' ); ?>"
			target="_blank"
			uib-tooltip="<?php echo _get($meta,$key.'.share_label') ?>"
			tooltip-popup-delay="500"
			tooltip-placement="bottom">
			<i class="icon <?php echo _get($meta,$key.'.icon') ?>"></i>
		</a>
<?php endforeach; ?>