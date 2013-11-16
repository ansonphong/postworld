
## MVC Class Structure

### Overview
Each appearance of a post appears with the `.post` class at the root level.

#### Post Views
Post Views re fundamental to any display of a post in the feed or single view. In addition to the `.post` class, the view class is also appended to the root level of a post element. The view class cooroponds with the view name in the feed and templte structure.

Default post view supported are : 

- __compact__ 
- __list__
- __detail__
- __grid__
- __full__

##### Example
``` html
<div class="post detail">
	{{post}}
</div>
```

#### Post Formats
Post Formats additionally contribute to the styling schema. Post format classes are applied at a child level per post, since the parent view could house one of any post formats, and the post format has no bearing on the template file names. The AngularJS `ng-switch`, `ng-show` and `ng-hide` directives are used to control the displayed contents of a template per post format.

Post formats are automatically determined by the contents of the `link_url` field.

Default post formats supported are :

- __standard__ (default)
- __link__ - Any post with a link URL attached which doesn't fall into another format
- __video__ - Links URLs to video sharing sites
- __audio__ - Link URLs to audio sharing sites

Using `ng-switch` to determine displayed elements by `post_format`, this DOM structure would be used:

```html
<div ng-switch="post.post_format" class="post_format">
	<div ng-switch-default class="standard">{{ default elements }}</div>
	<div ng-switch-when="link" class="link">{{ link elements }}</div>
	<div ng-switch-when="video" class="video">{{ video elements }}</div>
	<div ng-switch-when="audio" class="audio">{{ audio elements }}</div>
</div>
```


#### Post Types
Post Types are optionally used in the template file naming schema, and so including a post type class in the root level of the post is optional. If the choosen template structure includes seperate template files for individual custom post types, it's advisable to include that post type class in the root level of the element, alongside with the post view class.

Otherwise, using `ng-switch` to determine displayed elements by `post_type`, this DOM structure would be used:

```html
<div ng-switch="post.post_type" class="post_type">
	<div ng-switch-default class="post">{{ default elements }}</div>
	<div ng-switch-when="feature" class="feature">{{ feature elements }}</div>
	<div ng-switch-when="blog" class="blog">{{ blog elements }}</div>
	<div ng-switch-when="link" class="link">{{ link elements }}</div>
</div>
```


#### Post Data
Standard post data fields are used throughout the PHP and Javascript framework, which find their way into the CSS structure to make working with the entire MVC pattern as simple as possible.

Here are in-point examples of DOM elements for displaying data fields, and their coorosponding classes and model methods.


##### Post Title
``` html
<h2 class="post_title">{{post.post_title}}</h2>
```

##### Post Excerpt
``` html
<div class="post_excerpt" ng-bind-html="post.post_excerpt"></div>
```

##### Taxonomy Term List

``` html
<a ng-repeat="term in post.taxonomy.category" href="{{term.url}}" ng-class="term.slug" class="category">{{term.name}}</a>
```

This will additonally assign a class to each term element with the value of the term slug, for customizing the elements on a per term basis. The taxonomy used in the example is `category`. This can be supplimented for any other requested taxonomy object.

##### Tags Term List

``` html
	<a ng-repeat="tag in post.taxonomy.post_tag" href="{{tag.url}}" class="post_tag">{{tag.name}}</a>
```

##### Post Content

``` html
<div class="post_content" ng-bind-html="post.post_content"></div>
```


##### Launch Media Modal
This will display a button to launch the media modal for media post formats.

``` html
<button ng-show="post.post_format == 'video' || post.post_format == 'audio'"
	launch-media-modal ng-click="launch(post)">
  <i class="icon-play"> Play</i> 
</button>

```






