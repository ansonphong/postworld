<!-- ////////// FEED ////////// -->
<script>
	///// LIVE FEED /////
	feed_settings['featured-posts'] = {
		preload : 9,
		load_increment : 3,
		offset: 0,
		max_posts:200,
		order_by : '-post_date',
		view : {
			current : 'grid',
			options : ['detail', 'grid','grid-horizontal']
		},
		query_args : {
			post_type:['post','page'],
			post_status:'publish',
			tax_query:[
				{
					taxonomy: 'i-feature',
					field: 'slug',
					terms: '<?php echo $term; ?>',
				}
				
			],
			//category_name: 'home-page-feature',
			//post_parent: <?php echo $post_id; ?>,
		},
		feed_template: 'feed-live-feed',	// Optional, needed in case of different widgets [having different panels for example] 
	};	
</script>

<div live-feed='featured-posts' class="grid feed view"></div>

<!-- ////////// END FEED ////////// -->