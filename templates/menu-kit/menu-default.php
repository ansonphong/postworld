<a <?php
		echo $link_attributes;
		// Add Tooltip
		if( $description ){
		?>
			tooltip="<?php echo $description; ?>"
			tooltip-placement="left"
		<?php
		}
	?>
	>
	<?php echo $link_label ?>
</a>