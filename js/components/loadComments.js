'use strict';
/*
 * Angular Tree Implementations
 * recursive templates https://github.com/eu81273/angular.treeview
 * recursive directive https://gist.github.com/furf/4331090
 * lazy loading http://blog.boxelderweb.com/2013/08/19/angularjs-a-lazily-loaded-recursive-tree-widget/
 * 
 * Tasks
 * Get Comments Service [OK]
 * Create Recursive Comment Structure [OK]
 * 	- with Maximimze/minimize [OK] 
 *  - +/- Karma Points [OK - just add ajax Functions]
 * Create Comment Tempalte [OK]
 * Add Comment/Reply [OK]
 * Edit Comment [OK]
 * Delete Comment [OK]
 * Toggle Reply/Delete/Edit [OK]
 * Flag comment [Doesn't exist]
 * Remove ngAnimate to enhance performance! [OK]
 * Bind HTML/Sanitize Content/Format Content [OK]
 * NOTE: Karma points are always zero, even if updated in the wp_comments table, it seems they need to be updated somewhere else
 *  - Maximize/Minimize based on Points [OK] = Note that we are currently using a random function to generate points - for testing purposes
 * performance Tuning of initial loading time [OK] [using chrome timeline - less than 1 second to render - after loading data from server]
 * Create a Comment on the Top Level [OK]
 * OrderBy is moved to query field and underscore is removed, just to match the arguments format of the get_comments function
 * Sort Options and refresh based on sorting [OK]
 * Show as Tree or linear [OK]
 * Show Loading Icon while loading data [OK]
 * Permissions, you can only edit and delete your own comments [OK]
 * Show More - when exceeding 600 characters show only the first 4 lines of the comment [OK]
 * encapsulate in a simple directive with a template [OK]
 * 
 *  - show control bar on hover only
 *  - show icons for control bar
 *  - highlight selected function of the control bar [reply, edit, delete, etc...]
 *  
 * add Timezone to date [OK]
 * 
 * when adding new comments they should be open by default?
 * Can we use the html returned directly?

 * Cleanup UI
 * Cleanup CSS
 * Animation? Performance Limitations
 * If we can show the comments with tree=false, what happens when users reply? it will become a tree? no?
 * 
 * Future: Lazy Loading/Load More/Load on Scrolling, etc...
 * Performance http://tech.small-improvements.com/2013/09/10/angularjs-performance-with-large-lists/
 */

postworld.directive('ngShowMore', function ($timeout,$animate) {
	//console.log('at least we got in directive');
	function link(scope, element, attrs) {
					scope.child.showMore = false;
					scope.child.tall = false;
					//console.log(scope.child.comment_ID,scope.child.comment_content.length);
			if (scope.child.comment_content.length>600) {
							scope.child.showMore = true;
							scope.child.tall = true;				
			} ;
					//  this needs to perform better, so it is replaced with the above function
					/*
				$timeout(function(){
						scope.child.showMore = false;
						scope.child.tall = false;
						scope.child.height = element.height();
						if (scope.child.height>60) { 
							scope.child.showMore = true;
							scope.child.tall = true;
							} 
				});
				*/
							 
			}	
	return {
		restrict: 'A',
		link: link,
	};
});


postworld.directive('loadComments', function() {
	return {
		restrict: 'A',
		replace: true,
		controller: 'pwTreeController',
		template: '<div ng-include="templateUrl" class="comments"></div>',
		scope: {
			postId : '=',
		}
	};
});

postworld.controller('pwTreeController',
	[ '$scope', '$timeout', 'pwCommentsService', '$rootScope', '$sce', '$attrs', 'pwData', '$log', '$window', '$pw',
	function ($scope, $timeout, pwCommentsService, $rootScope, $sce, $attrs, pwData, $log, $window, $pw) {
		$scope.json = '';

		if ( $pw.user  )
			$scope.user_id = $pw.user['data'].ID;
		else
			$scope.user_id = 0;

		$scope.startTime = 0;
		$scope.endTime = 0;
		$scope.treedata = {children: []};
		$scope.minimized = false;
		$scope.treeUpdated = false;
		$scope.commentsLoaded = false;
		$scope.key = 0;
		$scope.commentsCount = 0;
		$scope.feed = $attrs.loadComments;
		$scope.pluginUrl = jsVars.pluginurl;
		$scope.templateLoaded = false;
		$log.debug('Comments post ID is:', $scope.postId);
		var settings = pwCommentsService.comments_settings[$scope.feed];

		// Set the Post ID
		if ( $scope.postId ) {
			settings.query.post_id = $scope.postId; // setting the post id here    	
		}

		$scope.minPoints = settings.min_points;

		$scope.labels = settings.labels;

		if (settings.query.orderby) $scope.orderBy = settings.query.orderby;
		else $scope.orderBy = 'comment_points'; 

		if (settings.order_options) $scope.orderOptions = settings.order_options;
		 
		// Get Templates
		if (settings.view) {
			var template = 'comments-'+settings.view;
			$scope.templateUrl = pwData.pw_get_template( { subdir: 'comments', view: template } );
			$log.debug('pwLoadCommentsController Set Post Template to ',$scope.templateUrl);
		}
		else {
			$scope.templateUrl = $pw.paths.plugin_url+'/postworld/templates/comments/comments-default.html';
			// this template fires the loadComments function, so there is no possibility that loadComments will run first.
		}
			
		$scope.loadComments = function () {
			$scope.commentsLoaded = false;
			settings.query.orderby = $scope.orderBy;

			pwCommentsService.pw_get_comments($scope.feed).then(function(value) {
				$log.debug('Got Comments: ', value.data );
				$scope.treedata = {children: value.data};
				$scope.commentsLoaded = true;
				$scope.treeUpdated = !$scope.treeUpdated;			      
				$scope.commentsCount = value.data.length;
				/*
				// not used here, but can be used for progressive element loading
					var recursiveTimeout = function() {
						var load = $timeout( function loadComments() {	    		  
								for (var i=0;i<20;i++) {
									if ($scope.key<$scope.commentsCount) {
										// console.log('loading data', $scope.key);
										$scope.treedata.children[$scope.key] = value.data[$scope.key];
										$scope.key++;
									}
								}
								$scope.treeUpdated = !$scope.treeUpdated;			      
								if ($scope.key<$scope.commentsCount) {
									load = $timeout(loadComments, 50);
								}
							}, 50); 
					};
					recursiveTimeout(); 
					*/
			});

		};
	// $scope.loadComments();
		
	$scope.toggleMinimized = function (child) {
		child.minimized = !child.minimized;
	};
	
	$scope.OpenClose = function(child) {
		if (parseInt(child.comment_points)>$scope.minPoints) child.minimized = false;
		else child.minimized = true;
	};
	
	$scope.trustHtml = function(child) {
		child.trustedContent = $sce.trustAsHtml(child.comment_content);
	};

	$scope.voteUpSelected = function(child){
		if( child.viewer_points > 0 ){
			return 'selected';
		}
	}

	$scope.voteDownSelected = function(child){
		if( child.viewer_points < 0 ){
			return 'selected';
		}
	}

	// CAST VOTE ON THE POST
	$scope.voteComment = function( points, child ){

			// Get the voting power of the current user
			if( typeof $window.pw.user.postworld !== 'undefined' )
					var vote_power = parseInt($window.pw.user.postworld.vote_power);
			// If they're not logged in, return false
			if( typeof vote_power === 'undefined' ){
					alert("Must be logged in to vote.");
					return false;
			}
			
			// Define how many points have they already given to this post
			var has_voted = parseInt(child.viewer_points);

			// Define how many points will be set
			var setPoints = ( has_voted + points );

			// If set points exceeds vote power
			if( Math.abs(setPoints) > vote_power ){
					setPoints = (vote_power * points);
					//alert( "Normalizing : " + setPoints );
			}

			// Setup parameters
			var args = {
					comment_id: child.comment_ID,
					points: setPoints,
			};

			// Set Status
			child.voteStatus = "busy";
			// AJAX Call 
			pwData.set_comment_points ( args ).then(
					// ON : SUCCESS
					function(response) {    
							//alert( JSON.stringify(response.data) );
							// RESPONSE.DATA FORMAT : {"point_type":"comment","user_id":1,"id":51407,"points_added":0,"points_total":"5"}
							$log.debug('VOTE RETURN : ' + JSON.stringify(response) );
							if ( response.data.id == child.comment_ID ){
									// UPDATE POST POINTS
									child.comment_points = response.data.points_total;
									// UPDATE VIEWER HAS VOTED
									child.viewer_points = ( parseInt(child.viewer_points) + parseInt(response.data.points_added) ) ;
							} //else
									//alert('Server error voting.');
							child.voteStatus = "done";
					},
					// ON : FAILURE
					function(response) {
							child.voteStatus = "done";
							//alert('Client error voting.');
					}
			);
	}

	$scope.addChild = function (child, data) {
		if (!child.children) child.children = [];
		child.children.push(data);
		$scope.treeUpdated = !$scope.treeUpdated;			      
	};
	
	$scope.updateChild = function (child, data) {
		// child.comment_content = data.comment_content;
		for (var key in data) {
			child[key] = data[key];
		}
		$scope.treeUpdated = !$scope.treeUpdated;			      
	};

	$scope.toggleReplyBox = function(child) {
		if (child.editInProgress || child.deleteInProgress || child.replyInProgress) return;
		// close other boxes
		child.editMode = false;
		child.deleteBox = false;
		// toggle reply box
		child.replyBox = !child.replyBox;

		// TODO add focus here
		//$window.$( "#reply-"+child.comment_ID ).focus();

		if ( child.replyBoxSelected == "" )
			child.replyBoxSelected = "selected";
		else
			child.replyBoxSelected = "";

	};
	
	$scope.toggleEditBox = function(child) {
		if (child.editInProgress || child.deleteInProgress || child.replyInProgress) return;
		// close other boxes
		child.deleteBox = false;
		child.replyBox = false;
		
		// if in Edit Mode, just close it.
		if (child.editMode) {
			child.editMode = false;
			return;
		}
		// if not in Edit Mode, make a call to get comment  	
		if (!child.editMode) {
			var args = {};
			args.comment_id = child.comment_ID;
			args.fields = 'edit';
			// Should we set editInProgress here?
			pwCommentsService.pw_get_comment(args).then(
				// success
				function(response) {
					if ((response.status==200)&&(response.data)) {
						// set raw comment value
						child.comment_content_raw = response.data.comment_content; 
						// child.editText = response.comment_content;  					
						// set editMode
						child.editMode = true;
					} else {
						child.editMode = false;
					}
				},
				// failure
				function(response) {
						child.editMode = false;  				
				}
			);  		
		}
		// child.editMode = !child.editMode;  	
	};
	
	$scope.toggleDeleteBox = function(child) {
		if (child.editInProgress || child.deleteInProgress || child.replyInProgress) return;
		// close other boxes
		child.editMode = false;
		child.replyBox = false;
		// toggle delete box
		child.deleteBox = !child.deleteBox;
		// TODO add focus here
	};
	
	$scope.replyComment = function(child) {

		// Disable reply button, text editing, cancelling until we are back
		child.replyInProgress = true;
		child.replyError = "";
		// trigger call to send reply
		var args = {};
		args.comment_data = {};
		args.comment_data.comment_content = child.replyText;
		// we can get the post id from the child comment post id too, however, when there is no parent, we cannot get it from here. so we can get it always directly from settings.
		args.comment_data.comment_post_ID = settings.query.post_id; 
		args.comment_data.comment_date = new Date(); // should we do it here? security?
		// args.comment_data.comment_date_gmt = ;
		// args.comment_data.comment_type = 'comment';  	// in documentation, this is not added in wordpress insert/add functions	  			
		if (child == $scope.treedata) {
			args.comment_data.comment_parent = 0;  			
		} else {
			args.comment_data.comment_parent = child.comment_ID;  			
		}
		
		args.return_value = 'data';  		
		pwCommentsService.pw_save_comment(args).then(
			function(response) {
				if ((response.status==200)&&(response.data)) {
					// reset form and hide it
					child.replyInProgress = false;
					child.replyText = "";
					child.replyBox = false;
					child.replyBoxSelected = "";
					child.replyError = "";
					console.log('added',response);
					// show the new comment
					$scope.addChild(child, response.data);
				} else {
					// reset the form
					child.replyInProgress = false;
					// TODO add more descriptive error
					child.replyError = "Error adding new comment";
					// show the error
					console.log('error adding new comment',response);  					
				}
			},
			function(response) {
				// reset the form
				child.replyInProgress = false;
				// TODO add more descriptive error
				child.replyError = "Error adding new comment";
				// show the error
				console.log('error adding new comment',response);
			}
		);
	};
	

	$scope.editComment = function(child) {
			//alert( JSON.stringify(child.user_id) );
			// Disable edit button, text editing, cancelling until we are back
			child.editInProgress = true;
			child.editError = "";		
			// trigger call to send reply
			var args = {};
			args.comment_data = {
				"comment_ID": child.comment_ID,
				"comment_content": child.editText,
				"user_id": child.user_id
			};
			
			args.return_value = 'data';

			pwCommentsService.pw_save_comment(args).then(
				function(response) {
					if ((response.status==200)&&(response.data)) {
						
						// reset form and hide it
						child.editMode = false;
						child.editInProgress = false;
						child.editText = "";
						child.editBox = false;
						child.editError = "";
						//console.log('edited',response);
						// show the new comment
						$scope.updateChild(child, response.data);  		
						//alert(JSON.stringify(response.data));			
					} else {
						// reset the form
						child.editMode = false;
						child.editInProgress = false;
						// TODO add more descriptive error
						child.editError = "Error editing comment";
						// show the error
						console.log('error editing comment',response);  					
					}
				},
				function(response) {
					// reset the form
					child.editMode = false;
					child.editInProgress = false;
					// TODO add more descriptive error
					child.editError = "Error editing comment";
					// show the error
					console.log('error editing comment',response);
				}
			);
	};

	$scope.deleteComment = function(child) {
			// Disable edit button, text editing, cancelling until we are back
			child.deleteInProgress = true;
			child.deleteError = "";
			// trigger call to send reply
			var args = {};
			args.comment_id = child.comment_ID;
			
			pwCommentsService.pw_delete_comment(args).then(
				function(response) {
					if ((response.status==200)&&(response.data)) {
						
						// reset form and hide it
						child.deleteInProgress = false;
						child.deleteBox = false;
						child.deleteError = "";
						console.log('deleted',response);
						// show the new comment
						$scope.removeChild(child);  					
					} else {
						// reset the form
						child.deleteInProgress = false;
						// TODO add more descriptive error
						child.deleteError = "Error deleting comment";
						// show the error
						console.log('error deleting comment',response);  					
					}
				},
				function(response) {
					// reset the form
					child.deleteInProgress = false;
					// TODO add more descriptive error
					child.deleteError = "Error deleting comment";
					// show the error
					console.log('error deleting comment',response);
				}
			);
	};


	$scope.flagComment = function(child){

		var args = {
			"comment_ID" : child.comment_ID,
		};

		pwCommentsService.flag_comment(args).then(
				function(response) {
					if ((response.status==200)&&(response.data == true)) {
						alert( "Comment flagged for moderation." );          
					} else {
						alert( "Comment not flagged for moderation." );       
					}
				},
				function(response) {
					alert( "Comment not flagged for moderation." );   
				}
			);
	};


	$scope.removeChild = function (child) {
		function walk(target) {
			var children = target.children,
				i;
			if (children) {
				i = children.length;
				while (i--) {
					if (children[i] === child) {
						return children.splice(i, 1);
					} else {
						walk(children[i]);
					}
				}
			}
		}
		walk($scope.treedata);
		$scope.treeUpdated = !$scope.treeUpdated;			      
	};

	$scope.setRoles = function(child){

		var current_user = $pw.user;

		// Set the roles/relationship of the user to each post
		child.roles = {};

		// Guest
		( current_user == 0 ) ?
			child.roles.isGuest = true : child.roles.isGuest = false;

		// User
		( current_user.user_id != 0 ) ? 
			child.roles.isUser = true : child.roles.isUser = false;
			
		// Owner
		if( $pw.user )
			( current_user.data.ID == child.user_id ) ? 
				child.roles.isOwner = true : child.roles.isOwner = false;
		else
			child.roles.isOwner = false;

		// Admin
		if( $pw.user )
			( current_user.roles[0] == "administrator" || 
				current_user.roles[0] == "editor" ) ? 
				child.roles.isAdmin = true : child.roles.isAdmin = false; 
		else
			child.roles.isAdmin = false;
		
		//child.roles.isGuest

	}


}]);







