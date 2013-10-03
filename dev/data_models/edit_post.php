
directive : 

create edit page

edit_post_settings = {
	'post_id' : 'new',
};

///// edit-field : /////
- becomes the id and name of the inner input

///// data-value : /////
- becomes the default value which is pre-populated


///// data-input : /////

// TEXT INPUTS :
input-text
input-password
input-url
input-hidden

// OTHER INPUTS
input-checkbox
input-radio

// OTHER
dropdown
wysiwyg
textarea

// BUTTON




edit fields :

HEADER
<div edit-field="post_type" data-input="dropdown" data-value="blog"></div>
<div edit-field="post_date_gmt" data-input="date-picker" data-value="2012-10-29 14:00:35"></div>

<div edit-button="preview" data-value="/&n324;"></div>
<div edit-button="permalink" data-value="/&n324;"></div>
<div edit-button="save" data-value="publish/save"></div>

MAIN
<div edit-field="post_title" data-input="input"></div>
<div edit-field="post_content" data-input="wysiwyg"></div>
<div edit-field="post_excerpt" data-input="textarea"></div>
<div edit-field="_yoast_wpseo_title" data-input="input-text"></div>
<div edit-field="_yoast_wpseo_metadesc" data-input="input-text"></div>

ASIDE
<div edit-field="featured_image" data-input="button"></div> // do special action here
<div edit-field="post_format" data-input="button"></div> // do special action here
<div edit-field="taxonomy(topic)" data-input="dropdown"></div>
<div edit-field="taxonomy(tags)" data-input="textarea"></div>
<div edit-field="post_name" data-input="input-text"></div>

EDITORS ONLY (if capabilities (edit_others_posts) )
<div edit-field="taxonomy(section)" data-input="dropdown"></div>
<div edit-field="taxonomy(type)" data-input="dropdown"></div>
<div edit-field="post_class" data-input="dropdown"></div>

<div edit-field="disable_comments" data-input="input-checkbox"></div>

<div edit-field="author_name" data-input="input-text"></div>













