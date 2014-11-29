<?
/*_____             _  __        ___     _            _
 |  ___|__  ___  __| | \ \      / (_) __| | __ _  ___| |_
 | |_ / _ \/ _ \/ _` |  \ \ /\ / /| |/ _` |/ _` |/ _ \ __|
 |  _|  __/  __/ (_| |   \ V  V / | | (_| | (_| |  __/ |_
 |_|  \___|\___|\__,_|    \_/\_/  |_|\__,_|\__, |\___|\__|
										   |___/
////////////////// FEED WIDGET - ADMIN //////////////////*/
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
		border: 1px solid #ccc;
		border-radius: 4px;
		margin-top: 5px;
		padding: 10px;
	}
	#widget-options .radio-type-select { margin-right: 5px; }
	#widget-options .type-title { font-size: 14px; letter-spacing: 1px; color: #666; }
	#widget-options .type-options { margin: 10px 0 0 10px; }
</style>

<div id="widget-options">

	<!-- TITLE -->
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">
		<input type="checkbox" value="1" title="Show Title" name="<?php echo $this->get_field_name('show_title'); ?>" id="<?php echo $this->get_field_id('show_title'); ?>" <?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
		<?php _e( 'Title:' ); ?>
	</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />


	<!-- SELECT TAXONOMY -->
	<div class="type-wrapper">
		<div class="type-title">
			<label><b>Taxonomy</b></label>
		</div>
		<div class="type-options">
			<select name="<?php echo $this->get_field_name('taxonomy'); ?>"  id="<?php echo $this->get_field_id('taxonomy'); ?>">
			<?php
				// Term Feed Templates
				$taxonomy_args = array(
					'public' => true,
					);
				$taxonomies = get_taxonomies( $taxonomy_args, 'objects' ); 
				if( !empty( $taxonomies ) ){
					foreach( $taxonomies as $tax ) {
						$selected = '';
						$selected = ( $tax->name == $taxonomy ) ? 'selected="selected"' : '';
						echo '<option value="'.$tax->name.'" '.$selected.' >'.$tax->labels->name.'</option>';
					}
				}
			?>
			</select>
		</div>
   </div>

	<!-- SELECT TEMPLATE -->
	<div class="type-wrapper">
		<div class="type-title">
			<label><b>Template</b></label>
		</div>
		<div class="type-options">
			<select name="<?php echo $this->get_field_name('template_id'); ?>"  id="<?php echo $this->get_field_id('template_id'); ?>">
			<?php
				// Term Feed Templates
				$templates = pw_get_templates(
					array(
						'subdirs' => array('term-feeds'),
						'path_type' => 'url',
						'ext'=>'php',
						)
					)['term-feeds'];

				if( !empty( $templates ) ){
					foreach( $templates as $template_key => $template_value ) {
						$selected = '';
						$selected = ( $template_id == $template_key ) ? 'selected="selected"' : '';
						echo '<option value="'.$template_key.'" '.$selected.' >'.$template_key.'</option>';
					}
				}
			?>
			</select>
		</div>
   </div>
</div>
