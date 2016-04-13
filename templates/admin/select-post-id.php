<?php
	$instance = pw_random_string();
	$vars['query']['fields'] = 'ids';
	$query = new WP_Query($vars['query']);
	$fields = array(
		'ID',
		'post_title',
		);
	$posts = (!empty($query->posts)) ? pw_get_posts($query->posts,$fields) : array();
?>
<script>
	postworldAdmin.controller('<?php echo $instance ?>', function($scope){
		$scope.posts_<?php echo $instance ?> = <?php echo json_encode($posts) ?>;
	});
</script>
<span ng-controller="<?php echo $instance ?>">
	<select ng-options="post.ID as post.post_title for post in posts_<?php echo $instance ?>" ng-model="<?php echo $vars['ng_model'] ?>">
	</select>
</span>