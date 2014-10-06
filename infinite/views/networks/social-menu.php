<a
	href="<?php echo $network_link; ?>"
	target="_blank"
	class="<?php echo $network_id;?>">
	<img
		tooltip="<?php echo $network_name; ?>"
		<?php if ( isset($meta['tooltip-placement']) ){ ?>
			tooltip-placement="<?php echo $meta['tooltip-placement']; ?>"
		<?php } ?>
		src="<?php echo $network_image_url; ?>">
</a>