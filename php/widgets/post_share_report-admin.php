<?
/*
  ____           _     ____  _                      ____                       _   
 |  _ \ ___  ___| |_  / ___|| |__   __ _ _ __ ___  |  _ \ ___ _ __   ___  _ __| |_ 
 | |_) / _ \/ __| __| \___ \| '_ \ / _` | '__/ _ \ | |_) / _ \ '_ \ / _ \| '__| __|
 |  __/ (_) \__ \ |_   ___) | | | | (_| | | |  __/ |  _ <  __/ |_) | (_) | |  | |_ 
 |_|   \___/|___/\__| |____/|_| |_|\__,_|_|  \___| |_| \_\___| .__/ \___/|_|   \__|
                                                             |_|                   
///////////////////////////// POST SHARE REPORT VIEW /////////////////////////////*/
?>

<?php

if ( isset( $OPTIONS[ 'title' ] ) ) {
      $title = $OPTIONS[ 'title' ];
    }
    else {
      $title = __( 'Widget', 'text_domain' );
    }

/*
// GLOBAL SETTINGS : LOAD
$show_title = $OPTIONS['show_title'];
$menu_type = $OPTIONS['menu_type'];


// PAGES SETTINGS : LOAD
$show_parent_pages = $OPTIONS['show_parent_pages'];
$show_sibling_pages = $OPTIONS['show_sibling_pages'];
$show_child_pages = $OPTIONS['show_child_pages'];

// TAXONOMY SETTINGS : LOAD
$taxonomy = $OPTIONS['taxonomy'];
*/

extract($OPTIONS);

?>


<div id="menu-kit-options">

    <!-- TITLE -->
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        
        <input type="checkbox" value="1" title="Show Title" name="<?php echo $this->get_field_name('show_title'); ?>" id="<?php echo $this->get_field_id('show_title'); ?>" <?php if($show_title == '1'){ echo 'checked="checked"'; } ?> >
        <?php _e( 'Title:' ); ?>
    </label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />



</div>

<script>
jQuery(document).ready(function() {
	
	
	
	});

</script>
       
