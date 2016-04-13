Postworld
=========

Postworld is an open source WordPress Theme building framework.

Postworld provides a backend framework which builds ontop of the native WordPress functionality coupled with the client-side Javascript to create an exceptional experience for both the developer and the user.


## Features:

### Modular Activation Structure
  - Use all or just part of the featureset with minimal impact on your server performance.

### Infinite Scrolling
  - Easily impliment feeds with with infinite scrolling.

### Smart Image Loading
  - Images can be loaded dynamically based on the user's screen pixel ratio, and the resolution of the images, making every image completely responsive and retina ready without thinking about it.

### Cached Feeds
  - Instantly display feeds from a massive database of posts, Postworld supercharges sites with thousands or hundreds of thousands of posts, and complex recursive queries.

### Dynamic Search
  - Dynamically search and sort posts in real-time according to search criteria.

### REST API Implimentation
  - Since WordPress 4.4, the REST API has been a central core feature. Take advantage of the elegance and high performance of the latest additions to WordPress, and start building your themes like an app, not simply static pages.

### Events
  - Search events by start time, end time,  geo-location, filter by upcoming events, past events, current events.
  - Promote syncronized events which countdown accurate in any timezone.

### Extensive AJAX Functionality
  - Handle everything from new user signup verifications, to post editing with an extensive array of AJAX hooks built in.

### Caching
  - Provides an optional caching mechanism which can be easily implimented by any custom functions, and is used in the core to cache posts, feeds and related post queries, so your site can take advantage of rich recursive querying methods, without making users wait.

### Widgets
  - *Related Posts* widget which lists related posts based on custom criteria
  - *Term list* widget which graphically lists your tags or categories showing their contents and sub-categories.
  - *User* widget easily displays a user and all their data

### Frontend Post Editing
  - Build your own front-end post editing interfaces, so users don't ever need to see the WordPress admin panels

### Easily Add Custom Post Editing Metaboxes
  - By calling a single function, add rich metaboxs to input custom data on any post type, and save to the postmeta table

### Custom Shortcodes
  - Allow users to generate their own custom shortcodes

### Custom Sidebars
  - Allow users to create unlimited sidebars and place them in layouts

### Custom Layouts
  - Allow users to customize page layouts per context or page, and place custom sidebars, as well as adjust responsiveness

### Templating

  #### RESS Technology:
    + RESS stands for Responsive with Server Side component, which allows you to detect which device a request is coming from before the data is served, taking repsonsive design to the next level.

  #### Post Views
    + Custom reactive templating system, built with Angular JS, and H2O, so you can use `{{handlebar}}` templating for displaying lists of posts in multiple views, optionally customized by the type of device
    + Writing templates is this easy:

    ```
    <a href="{{post.post_permalink}}">
      {{post.post_title}}
    </a>
    ```

### Indexed Post Meta
- Adds new meta data fields to posts, including:
  - **Post Class**  
    Used to differentiate between unique classes of content / ie. *Community* / *Editorial*
  - **Post Format**  
  	Used to differentiate inique formats of content, and code templates accordingly / ie. *video* / *audio*
  - **Link URL**    
  	USed to attribute a default link URL for each post, generally used to denote an offsite page, video or audio file for custom embedding or formatting

