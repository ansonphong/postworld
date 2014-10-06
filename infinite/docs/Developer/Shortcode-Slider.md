Infinite Slider Shortcode
-----


## Attributes

### image-size
Default : __large__
- Deliniates the ID of the registered image size in Wordpress which to use.

### template
Default : __default__
- Get via `locate_template()`


## Usage

``` html

[slider
	image-size="large"
	template=""
	width="100%"
	height="300px"

    title="true"
    description="true"

    menu="Main Menu"
	query="{post_type:'reviews'}"]

```