
<?php wp_enqueue_style( 'admin_css' ); ?>
<div id="bgkit_admin">

<h1 class="icon logo">Background Kit</h1>

<!-- GENERAL -->
<h2 class="icon gear">General</h2>

<div class="content">

	<div class="setting">
    	Default Background Style: None, Random, List <br>
        Enable for : Post Types List, Pages, Posts, etc...<br>
        Set default for each post type / top use master default.
    </div>

</div>
<!-- END GENERAL -->

<!-- STYLES -->
<h2 class="icon styles"><a href="/wp-admin/edit.php?post_type=background-kit-style">Styles</a> <a href="/wp-admin/post-new.php?post_type=background-kit-style">+</a></h2>
<div class="content">

	<?php $my_query = new WP_Query('post_type=background-kit-style&posts_per_page=100'); ?>
    
    <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
        
        <a href="<?php echo get_edit_post_link( ); ?>">
            <div class="border-thin round-4 float-left padding-10" style="margin:0 10px 10px 0; width:200px; height:230px;">
				      
                <div class="style_list_preview image"
                style="
                	background-color:#<?php echo get_post_meta( $post->ID, 'bg_color_hex', true); ?>;
                    background-image:url(<?php echo get_post_meta( $post->ID, 'bg_image_URL', true); ?>);
                    background-size:<?php echo get_post_meta( $post->ID, 'bg_image_size', true); ?>;
                    background-position:<?php echo get_post_meta( $post->ID, 'bg_image_position', true); ?>;
                    background-repeat:<?php echo get_post_meta( $post->ID, 'bg_image_repeat', true); ?>;
                "></div>
                
                <div class="style_list_preview pattern"
                style="
                    background-image:url(<?php echo get_post_meta( $post->ID, 'bg_pattern_URL', true); ?>);
                    background-size:<?php echo get_post_meta( $post->ID, 'bg_pattern_size', true); ?>%;
                    background-position:<?php echo get_post_meta( $post->ID, 'bg_pattern_position', true); ?>;
                    background-repeat:<?php echo get_post_meta( $post->ID, 'bg_pattern_repeat', true); ?>;
                    opacity:<?php echo get_post_meta( $post->ID, 'bg_pattern_opacity', true)/100; ?>;
                "></div>
                
                <div class="style_list_preview placeholder"></div> 
                <div class="style_description">
                    <h4><?php the_title(); ?></h4> 
                </div>

            </div>
        </a>
        
    <?php endwhile; ?>

</div>
<!-- END STYLES -->

</div><!-- END #bgkit_admin -->




