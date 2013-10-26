/**
 *
 * load_comments['post_single'] = {
    query : {
        post_id : 24,
        status : 'approve'
        },
    fields : 'all',
    tree : true,
    order_by : 'comment_points',
    order_options : {
        'comment_points' : 'Points',
        'comment_date' : 'Date'
        },
    min_points : 0,
};


Get posts with most comments 

SELECT comment_post_ID, COUNT( * ) 
FROM  `wp_comments` 
GROUP BY comment_post_ID
ORDER BY COUNT( * ) DESC 
LIMIT 0 , 30

 *   
 */

postworld.factory('pwCommentsService', function ($resource, $q, $log,pwData) {	  
	// Check feed_settigns to confirm we have valid settings
	var validSettings = true;
	// Set feed_settings and feed_data in pwData Singleton
	var comments_settings = window['load_comments'];
	// TODO check mandatory fields
	if (comments_settings == null) {
		validSettings = false;
		$log.error('pwCommentsService() no valid comments_settings defined');
	}
	
	var comments_data = {};
	
	$log.info('pwCommentsService() Registering comments_settings', comments_settings);
		
    return {
    	comments_settings: comments_settings,
    	comments_data: comments_data,
		pw_get_comments: function(feed) {
			var settings = this.comments_settings[feed];
			if (!settings) throw {message:'comments settings not initialized properly'};
			if (!settings.query) throw {message:'query for comments settings is not initialized properly'};
			$log.info('pwCommentsService.pw_get_comments',settings);
			// will pass settings as is, which will include few more parameters that will not be used in php
			return pwData.wp_ajax('pw_get_comments',settings);
		},
		pw_save_comment: function(args) {
			if (!args.comment_data) throw {message:'pw_save_comment - argument comment_data not initialized properly'};
			$log.info('pwCommentsService.pw_save_comment',args);
			return pwData.wp_ajax('pw_save_comment',args);
		},
		pw_delete_comment: function(args) {
			if (!args.comment_id) throw {message:'pw_delete_comment - argument comment_id not initialized properly'};
			$log.info('pwCommentsService.pw_delete_comment',args);
			return pwData.wp_ajax('pw_delete_comment',args);
		},
   };
});
