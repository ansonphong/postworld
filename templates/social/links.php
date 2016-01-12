<?php foreach( $vars as $network ): ?>
	<a
		href="<?php echo $network['link']; ?>"
		target="<?php echo $network['target']; ?>"
		class="<?php echo $network['id'];?> <?php echo $network['classes'];?>"
		uib-tooltip="<?php echo $network['name']; ?>"
		tooltip-placement="<?php echo $network['tooltip_placement']; ?>">
		<i
			class="<?php echo $network['classes'];?> <?php echo $network['icon'];?>">
		</i>
	</a>
<?php endforeach; ?>