<div class="user-contact-methods">
	<?php
		foreach( $vars as $contact ){
			?>
			<a
				tooltip="<?php echo $contact['label'] ?>"
				tooltip-placement="bottom"
				href="<?php echo $contact['url'] ?>" class="contact-method" target="_blank">
				<i class="icon <?php echo $contact['icon'] ?>"></i>
			</a>
			<?php
		}
	?>
</div>