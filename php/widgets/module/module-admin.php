<?
/*__  __           _       _       __        ___     _            _   
 |  \/  | ___   __| |_   _| | ___  \ \      / (_) __| | __ _  ___| |_ 
 | |\/| |/ _ \ / _` | | | | |/ _ \  \ \ /\ / /| |/ _` |/ _` |/ _ \ __|
 | |  | | (_) | (_| | |_| | |  __/   \ V  V / | | (_| | (_| |  __/ |_ 
 |_|  |_|\___/ \__,_|\__,_|_|\___|    \_/\_/  |_|\__,_|\__, |\___|\__|
                                                       |___/                    
/////////////////////// MODULE WIDGET - ADMIN ///////////////////////*/
?>

<?php

if ( isset( $OPTIONS[ 'title' ] ) ) {
			$title = $OPTIONS[ 'title' ];
		}
		else {
			$title = __( 'Widget', 'text_domain' );
		}

extract($OPTIONS);
?>

<style>
	#widget-options .type-wrapper {
		border:1px solid #ccc; border-radius: 4px;
		margin-top:5px;
		padding: 10px;
	}
	
	#widget-options .radio-type-select {
	 margin-right:5px;
	}
		
	#widget-options .type-title { font-size:14px; letter-spacing:1px; color:#666; }
	#widget-options .type-options { margin:10px 0 0 10px; }
		
</style>

<?php
	//echo json_encode( $pw_templates["panels"] );
?>

<div class="postworld admin-widget" id="widget-options">

	<!-- TITLE -->
	<label class="inner" for="<?php echo $this->get_field_id( 'show_title' ); ?>">
		<input
			type="checkbox"
			value="1"
			title="Show Title"
			name="<?php echo $this->get_field_name('show_title'); ?>"
			id="<?php echo $this->get_field_id('show_title'); ?>"
			<?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
		<?php _e( 'Title' ); ?>
	</label>
	<input
		class="labeled"
		id="<?php echo $this->get_field_id( 'title' ); ?>"
		name="<?php echo $this->get_field_name( 'title' ); ?>"
		type="text" value="<?php echo esc_attr( $title ); ?>" />
	<hr class="thin">
	 

	<label class="inner"><b>Module</b></label>
	<select class="labeled" name="<?php echo $this->get_field_name('module_id'); ?>"  id="<?php echo $this->get_field_id('module_id'); ?>">
		<?php
		$pw_templates = pw_get_templates(array(
			'subdirs'	=>	array('modules'),
			'path_type'	=>	'dir',
			'ext'		=>	'php',
			));
		$pw_templates_modules = $pw_templates['modules'];

		foreach($pw_templates_modules as $pw_module_id => $pw_module_path ) {
			$selected = '';
			if ( $module_id == $pw_module_id ){ $selected = 'selected="selected"'; }
					echo '<option value="'.$pw_module_id.'" '.$selected.' >'.$pw_module_id.'</option>';
		}
		?>
	</select>
	

</div>
