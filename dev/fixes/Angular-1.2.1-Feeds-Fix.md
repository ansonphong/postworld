Phong, here are the changes I made:-
 
For the postworld-dev repo,
1-      in the theme, I changed the call to postworld_includes to use 1.2.1 instead of 1.2-rc2. I believe it should also work with later versions, but I didn’t try it yet.

2-      I updated the theme index.php file directed, instead of the sandbox file. The initial template looks like this:-

<div live-feed='feed1' ></div>

<div load-feed="front_page_features" ></div>
                The ng-include is taken away from the livefeed/loadfeed directive and used in the template definition of each directive.
You may need to create a database entry for community-blog on wp_postworld_feeds to see some output on this.
For the postworld Unify-B branch,
1-      Added 2 templates for the infinite scrolling [live_feed_4 and load_feed_4], one for livefeed and one for loadfeed, the basic idea is to remove ng-include from  the feed-item directive, and put it inside the directive template definition.

<div feed-item post="post" feed-id='args.feed_id'></div>

2-  For compatibility, I modified postworld_includes.php to use <?php at the beginning. This is not related to the issue.

3-  feedItem.js directive is updated to use a template inside the directive the definition, and to use a variable named differently from templateUrl, to avoid conflict with templateUrl from livefeed/loadfeed since it is contained inside it.

postworld.directive('feedItem', function() {

    return {

        restrict: 'A',

        replace: true,

        controller: 'pwFeedItemController',

        template: '<div ng-include src="templateUrlf"></div>',       

        scope: {

              // this identifies the panel id, hence the panel template

              feedItem      : '=',

              post : "=",   // Get from ng-repeat

              feedId : '=', // Get from Parent Scope of Live Feed

              }

    };

});

4- liveFeed and loadFeed directives are updated to include template like this:-
postworld.directive('liveFeed', function() {
       return {
              restrict: 'A',
              replace: true,
              controller: 'pwFeedController',
              template: '<div ng-include="templateUrl"></div>',
              scope : {
              },
       };
});
 
 
While the changes seem trivial, they were not suggested anywhere online, they do the trick because:-
1-  ng-include is separated from the controller and the directive, so there is no conflict in priority anymore between ng-include and controller, neither between ng-include and directive.

2-  templateUrl is defined inside the directive, not on the directive, so it doesn’t have to be defined in the isolated scope definition, and can be easily used inside the scope as it is used previously.

Any other solution I tried made lots of conflicts. This is probably the only solution that keeps using ng-include and templateUrl to provide dynamic templating of a directive in 1.2.1 going forward.
 
Keeping my fingers crossed to see this working when you port it to your environment. I could test it on RSV2 when I have the time.
 
Waiting for feedback, but I might not be available in the coming hours as I will be mostly away until late afternoon.
Michel.