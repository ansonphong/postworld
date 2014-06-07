<a <?php
		// Print Attributes
		pw_print_html_attr( 'title', $link_attr['title'], "" );
		pw_print_html_attr( 'target', $link_attr['target'], "" );
		pw_print_html_attr( 'rel', $link_attr['rel'], "" );
		pw_print_html_attr( 'href', $link_attr['href'], "" );
	?>
	>
	<?php echo $link_meta['label'] ?>
</a>