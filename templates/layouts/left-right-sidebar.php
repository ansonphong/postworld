<div class="row page">
	<div class="<?php i_insert_column_classes('left'); ?> col-left sidebar">
		<?php i_insert_sidebar('left'); ?>
	</div>
	<div class="<?php i_insert_column_classes('content'); ?> col-content">
		<?php i_insert_content($vars); ?>
	</div>
	<?php i_insert_clearfix('right'); ?>
	<div class="<?php i_insert_column_classes('right'); ?> col-right sidebar">
		<?php i_insert_sidebar('right'); ?>
	</div>
</div>