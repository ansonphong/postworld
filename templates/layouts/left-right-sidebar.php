<div class="row page">
	<div class="<?php pw_insert_column_classes('left'); ?> col-left sidebar">
		<?php pw_insert_sidebar('left'); ?>
	</div>
	<div class="<?php pw_insert_column_classes('content'); ?> col-content">
		<?php pw_insert_content($vars); ?>
	</div>
	<?php pw_insert_clearfix('right'); ?>
	<div class="<?php pw_insert_column_classes('right'); ?> col-right sidebar">
		<?php pw_insert_sidebar('right'); ?>
	</div>
</div>