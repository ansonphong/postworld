<?
/*
  ____                  _  __        ___     _            _   
 |  _ \ __ _ _ __   ___| | \ \      / (_) __| | __ _  ___| |_ 
 | |_) / _` | '_ \ / _ \ |  \ \ /\ / /| |/ _` |/ _` |/ _ \ __|
 |  __/ (_| | | | |  __/ |   \ V  V / | | (_| | (_| |  __/ |_ 
 |_|   \__,_|_| |_|\___|_|    \_/\_/  |_|\__,_|\__, |\___|\__|
                                               |___/          
//////////////////// PANEL WIDGET - ADMIN ///////////////////*/
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

<div id="widget-options">

    <!-- TITLE -->
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        
        <input type="checkbox" value="1" title="Show Title" name="<?php echo $this->get_field_name('show_title'); ?>" id="<?php echo $this->get_field_id('show_title'); ?>" <?php if($show_title == '1'){ echo 'checked="checked"'; } ?> >
        <?php _e( 'Title:' ); ?>
    </label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

   
   <!-- TAXONOMIES -->
   <div class="type-wrapper">

    <div class="type-title">
        <label><b>Panel</b></label>
    </div>
    
    <div class="type-options">
        
			<select name="<?php echo $this->get_field_name('panel_id'); ?>"  id="<?php echo $this->get_field_id('panel_id'); ?>">
				<?php

        $pw_templates = pw_get_templates();
				$pw_templates_panels = $pw_templates['panels'];

        foreach($pw_templates_panels as $pw_panel_id => $pw_panel_url ) {
					$selected = '';
					if ( $panel_id == $pw_panel_id ){ $selected = 'selected="selected"'; }
              echo '<option value="'.$pw_panel_id.'" '.$selected.' >'.$pw_panel_id.'</option>';
        }
        ?>

      </select>
        
    </div>
        
   </div>
</div>

