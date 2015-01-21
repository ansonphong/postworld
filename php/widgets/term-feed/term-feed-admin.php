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
	.term-feed-widget{
		margin-bottom:10px;
	}
	.term-feed-widget .type-wrapper {
		border: 1px solid #ccc;
		border-radius: 4px;
		margin-top: 5px;
		padding: 10px;
	}
	.term-feed-widget .radio-type-select { margin-right: 5px; }
	.term-feed-widget .type-title { font-size: 14px; letter-spacing: 1px; color: #666; }
	.term-feed-widget .type-options { margin: 10px 0 0 10px; }
</style>

<div class="term-feed-widget postworld">

	<!-- TITLE -->
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">
		<input
			type="checkbox"
			value="1"
			title="Show Title"
			name="<?php echo $this->get_field_name('show_title'); ?>"
			id="<?php echo $this->get_field_id('show_title'); ?>"
			<?php if( !empty($show_title) && $show_title == '1' ){ echo 'checked="checked"'; } ?> >
		<?php _e( 'Title:' ); ?>
	</label>
	<input
		class="widefat"
		id="<?php echo $this->get_field_id( 'title' ); ?>"
		name="<?php echo $this->get_field_name( 'title' ); ?>"
		type="text" value="<?php echo esc_attr( $title ); ?>" />

	<div class="type-wrapper">
	

		<table>
			<!-- SELECT TAXONOMY -->
			<tr>
				<td>
					<b>Taxonomy</b>
				</td>
				<td>
					<select
						name="<?php echo $this->get_field_name('taxonomy'); ?>" 
						id="<?php echo $this->get_field_id('taxonomy'); ?>">
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
				</td>
			</tr>


			<!-- SELECT TERMS NUMBER -->
			<tr>
				<td>
					<b>Number</b>
				</td>
				<td>
					<input
					type="number"
					name="<?php echo $this->get_field_name('terms_number'); ?>"
					class="short"
					id="<?php echo $this->get_field_id('terms_number'); ?>"
					value="<?php echo $OPTIONS['terms_number']; ?>">
					<small>: maximum terms</small>
				</td>
			</tr>

			<!-- SELECT TERMS ORDER -->
			<tr>
				<td>
					<b>Order</b>
				</td>
				<td>
					<select
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
				</td>
			</tr>


			<!-- SELECT TERMS ORDER BY -->
			<tr>
				<td>
					<b>Order By</b>
				</td>
				<td>
					<select
						name="<?php echo $this->get_field_name('terms_orderby'); ?>" 
						id="<?php echo $this->get_field_id('terms_orderby'); ?>">
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
				</td>
			</tr>

			<!-- SELECT TEMPLATE -->
			<tr>
				<td>
					<b>Template</b>
				</td>
				<td>
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
				</td>
			</tr>
			
		</table>

   </div>

</div>
