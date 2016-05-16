<?php
/*_____             _  __        ___     _            _
 |  ___|__  ___  __| | \ \      / (_) __| | __ _  ___| |_
 | |_ / _ \/ _ \/ _` |  \ \ /\ / /| |/ _` |/ _` |/ _ \ __|
 |  _|  __/  __/ (_| |   \ V  V / | | (_| | (_| |  __/ |_
 |_|  \___|\___|\__,_|    \_/\_/  |_|\__,_|\__, |\___|\__|
										   |___/
////////////////// FEED WIDGET - ADMIN //////////////////*/

if ( isset( $OPTIONS[ 'title' ] ) ) {
	  $title = $OPTIONS[ 'title' ];
	}
	else {
	  $title = __( 'Widget', 'text_domain' );
	}
extract($OPTIONS);

// Term Feed Templates
$templates = pw_get_templates(
	array(
		'subdirs' => array('term-feeds'),
		'path_type' => 'url',
		'ext'=>'php',
		)
	);
$templates = $templates['term-feeds'];

// Filter which templates are available for widgets
$show_templates = pw_config( 'widgets.settings.term_feed.views' );

// If none specified, show all
if( empty( $show_templates ) ){
	foreach( $templates as $key => $val ){
		$show_templates[] = $key;
	}
}


?>
<div class="postworld">
	<div class="postworld-widget postworld-widget-term-feed">

		<!-- TITLE -->
		<label class="inner" for="<?php echo $this->get_field_id( 'show_title' ); ?>">
			<input
				type="checkbox"
				value="1"
				title="Show Title"
				name="<?php echo $this->get_field_name('show_title'); ?>"
				id="<?php echo $this->get_field_id('show_title'); ?>"
				<?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
			<?php _e( 'title', 'postworld' ); ?>
		</label>
		<input
			class="labeled"
			id="<?php echo $this->get_field_id( 'title' ); ?>"
			name="<?php echo $this->get_field_name( 'title' ); ?>"
			type="text" value="<?php echo esc_attr( $title ); ?>" />
		
		<hr class="thin">

		<label
			for="<?php echo $this->get_field_id('taxonomy'); ?>"
			class="inner">
			Taxonomy
		</label>
		<select
			name="<?php echo $this->get_field_name('taxonomy'); ?>" 
			id="<?php echo $this->get_field_id('taxonomy'); ?>"
			class="labeled">
			<?php
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


		<label
			for="<?php echo $this->get_field_id('terms_number'); ?>"
			class="inner">
			Maximum Terms
		</label>
		<input
			type="number"
			name="<?php echo $this->get_field_name('terms_number'); ?>"
			class="labeled"
			id="<?php echo $this->get_field_id('terms_number'); ?>"
			value="<?php echo $OPTIONS['terms_number']; ?>">


		<label
			for="<?php echo $this->get_field_id('terms_order'); ?>"
			class="inner">
			Order
		</label>
		<select
			class="labeled"
			name="<?php echo $this->get_field_name('terms_order'); ?>" 
			id="<?php echo $this->get_field_id('terms_order'); ?>">
			<?php
				$terms_order_options = array(
					'ASC'	=>	"Ascending",
					'DESC'	=>	"Descending",
					);
				foreach( $terms_order_options as $order_option => $order_name ) {
					$selected = '';
					$selected = ( $order_option == $OPTIONS['terms_order'] ) ? 'selected="selected"' : '';
					echo '<option value="'.$order_option.'" '.$selected.' >'.$order_name.'</option>';
				}
			?>
		</select>


		<label
			for="<?php echo $this->get_field_id('terms_orderby'); ?>"
			class="inner">
			Order By
		</label>
		<select
			name="<?php echo $this->get_field_name('terms_orderby'); ?>" 
			id="<?php echo $this->get_field_id('terms_orderby'); ?>"
			class="labeled">
			<?php
				$terms_orderby_options = array(
					'count'	=>	"Count (Number of posts)",
					'name'	=>	"Name (Alphabetical)",
					'id'	=>	"ID (Time Created)",
					'none'	=>	"None",
					);
				foreach( $terms_orderby_options as $orderby_option => $orderby_name ) {
					$selected = '';
					$selected = ( $orderby_option == $OPTIONS['terms_orderby'] ) ? 'selected="selected"' : '';
					echo '<option value="'.$orderby_option.'" '.$selected.' >'.$orderby_name.'</option>';
				}
			?>
		</select>


		<label
			for="<?php echo $this->get_field_name('template_id'); ?>"
			class="inner">
			Template
		</label>
		<select
			class="labeled"
			name="<?php echo $this->get_field_name('template_id'); ?>"
			id="<?php echo $this->get_field_id('template_id'); ?>">
			<?php
				// Show available template options
				if( !empty( $templates ) ){
					foreach( $templates as $template_key => $template_value ) {
						if( !in_array( $template_key, $show_templates ) )
							continue;

						$selected = '';
						$selected = ( $template_id == $template_key ) ? 'selected="selected"' : '';
						echo '<option value="'.$template_key.'" '.$selected.' >'.$template_key.'</option>';
					}
				}
			?>
		</select>


	</div>

</div>