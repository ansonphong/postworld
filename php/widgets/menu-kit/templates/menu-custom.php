<?php
// Get the menu templates
$menu_templates = pw_get_menu_templates();
// Get the menu template path
$template_path = $menu_templates[ $OPTIONS['menu_template'] ] ;//'templates/custom_menu-walker.php';
// Duplicate Slug as ID
$OPTIONS['menu_id'] = $OPTIONS['menu_slug'];
// Output Buffering to include template
?>
<div class="menu-kit custom-menu">
	<?php echo pw_ob_include( $template_path, $OPTIONS ); ?>
</div>