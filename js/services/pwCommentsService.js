/**
 *
 * pw.comments['post_single'] = {
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
postworld.factory( '$pwComments', function( $resource, $q, $log, $pwData, $window ){	 
	var commentsSettings = $window.pw.comments;

	$log.debug('$pwComments() Registering commentsSettings', commentsSettings);
		
    return {
    	commentsSettings: commentsSettings,
    	commentsData: {},
    	//commentsOptions: $_.get( $window.pw, 'options.comments' ),

		pw_get_comment: function(args) {
			if (!args.comment_id) throw {message:'pw_get_comment - no id defined'};
			return $pwData.wpAjax('pw_get_comment',args);
		},
		pw_get_comments: function(feed) {
			var settings = this.commentsSettings[feed];
			if (!settings) throw {message:'comments settings not initialized properly'};
			if (!settings.query) throw {message:'query for comments settings is not initialized properly'};
			$log.debug('$pwComments.pw_get_comments',settings);
			// will pass settings as is, which will include few more parameters that will not be used in php
			return $pwData.wpAjax('pw_get_comments',settings);
		},
		pw_save_comment: function(args) {
			if (!args.comment_data) throw {message:'pw_save_comment - argument comment_data not initialized properly'};
			$log.debug('$pwComments.pw_save_comment',args);
			return $pwData.wpAjax('pw_save_comment',args);
		},
		pw_delete_comment: function(args) {
			if (!args.comment_id) throw {message:'pw_delete_comment - argument comment_id not initialized properly'};
			$log.debug('$pwComments.pw_delete_comment',args);
			return $pwData.wpAjax('pw_delete_comment',args);
		},
		flag_comment: function(args) {
			$log.debug('$pwComments.flag_comment',args);
			var params = {args:args};
			return $pwData.wpAjax('flag_comment',params);
		},
   };
});
