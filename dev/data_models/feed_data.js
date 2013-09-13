// FEED OBJECT
var feed_data = {
		front_page_blog : {
			view : 'detail',
			orderBy: 'post_title',
			outline : '1,3,5,15,52,64',
			loaded : '1,3,5',
			feed_increment : 10,
			status : 'loaded',
			
			posts : <?php echo $front_page_blog_posts; ?>,

			},
		front_page_feature : {...},
	};