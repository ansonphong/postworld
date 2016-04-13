<!--///// METABOX WRAPPER /////-->
<div id="pwColorsMetabox" class="postworld">
	<div
		pw-admin-colors
		class="pw-metabox metabox-side metabox-colors"
		style="position:relative;">

		<table style="width:100%;">

			<?php if( empty( $pw_post['image']['colors'] ) && is_array( $colors ) ): ?>
				<tr>
					<?php foreach( $colors as $color ) : ?>
						<td style="height:32px; background:<?php echo $color ?>"></td>
					<?php endforeach ?>
				</tr>
			<?php elseif( is_array( $pw_post['image']['colors'] ) ): ?>
				<?php foreach( $pw_post['image']['colors'] as $profile ) : ?>
				<tr>
					<?php foreach( $profile['colors'] as $color ) : ?>
						<td style="height:32px; background:<?php echo $color['hex'] ?>"></td>
					<?php endforeach ?>
				</tr>
				<?php endforeach ?>
			<?php endif ?>

		</table>

		<?php do_action('pw_colors_metabox_templates') ?>
		
	</div>	
</div>

<?php
	// Action hook to print the Javascript(s)
	do_action('pw_colors_metabox_scripts');
?>