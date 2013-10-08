<?php
/**
 * Template Name: Edit Post
 *
 * Description: Twenty Twelve loves the no-sidebar look as much as
 * you do. Use this page template to remove the sidebar from any page.
 *
 * Tip: to remove the sidebar from all posts and pages simply remove
 * any active widgets from the Main Sidebar area, and the sidebar will
 * disappear everywhere.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
wp_enqueue_script( 'angularJS', plugins_url().'/postworld/js/angular.min.js' ); 
wp_enqueue_script( 'postworld-JS', plugins_url().'/postworld/js/postworld.js' ); 
wp_enqueue_style( 'Bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css' );
get_header(); ?>

<script>
	var post_types = <?php echo json_encode( get_user_post_types() ); ?>; // {"post":"Post","attachment":"Media"} ;
	var taxonomy = <?php echo json_encode( taxonomies_outline() ); ?>;
	<?php
		$post_id = '178424';
		$post_fields = array('ID','post_title', 'post_name','post_type', 'post_excerpt', 'taxonomy(category)[slug]'); //taxonomy(category)[slug]
		$post_data = json_encode( pw_get_post( $post_id, $post_fields ));
	?>
	var edit_fields = <?php echo $post_data; ?>;
</script>

<div id="primary" class="site-content" style="width:100%">
	<div id="content" role="main" ng-app="postworld">

		Post Title : (input-text) <br>
		<span edit-field="post_title" data-input="input-text" data-bind="edit_fields.post_title"></span> <!-- data-value="Default Title" -->
		<hr>
		Post Name : (input-text) <br>
		<span edit-field="post_name" data-input="input-text"></span>
		<hr>
		Post Types : (select) <br>
		<span edit-field="post_type" data-input="select" data-value="post" data-size="1" data-object="post_types"></span>
		<hr>
		Taxonomy Category : (select-multiple / taxonomy) <br>
		<span edit-field="taxonomy(category)" data-input="select-multiple" data-size="4"></span>
		<hr>
		Post Excerpt : (textarea) <br>
		<span edit-field="post_excerpt" data-input="textarea"></span>
		<hr>

		Two Way Binding Test : <br>
		<input ng-model="model_test" type="text">

		<select ng-model="model_test">
			<option value="red">Red</option>
			<option value="blue">Blue</option>
		</select>

		<hr>

		</div>
	</div><!-- #content -->
</div><!-- #primary -->
<?php get_footer(); ?>




