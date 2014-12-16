<a
	href="<?php echo $vars['network_link']; ?>"
	target="_blank"
	class="<?php echo $vars['network_id'];?> <?php echo $vars['classes'];?>"
	tooltip="<?php echo $vars['network_name']; ?>"
	tooltip-placement="<?php echo $vars['tooltip_placement']; ?>">
	<i
		class="<?php echo $vars['network_icon'];?>">
	</i>
	<!--
	<img
		tooltip="<?php echo $vars['network_name']; ?>"
		<?php if ( isset($vars['tooltip-placement']) ){ ?>
			tooltip-placement="<?php echo $vars['tooltip-placement']; ?>"
		<?php } ?>
		src="<?php echo $vars['network_image_url']; ?>">
	-->
</a>