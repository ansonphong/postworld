// FEED OBJECT
var feed_data = {
		front_page_blog : { // FEED ID
			view : {
				current : 'detail',
				options : ['list','detail','full']
			},
			order_by: 'post_title',
			outline : [1,3,5,15,52,64],
			loaded : [1,3,5],
			load_increment : 3,
			status : 'loaded',
			posts : [Array],
			},
			
		front_page_feature : { // FEED ID
			...
		},
	};