<?php
//////////////////// FIELD MODEL ////////////////////

function pw_field_model(){
	// Returns both the post and user field models

	$field_models = array(
		'post'	=>	pw_post_field_model(),
		'user'	=>	pw_user_field_model(),
		);

	return apply_filters( PW_MODEL_FIELDS, $field_models );
}


function pw_post_field_model(){
	// Returns the post field model

	///// CACHING LAYER /////
	global $pw_field_model_cache;
	if( !empty( $pw_field_model_cache ) && $pw_field_model_cache != null )
		return $pw_field_model_cache;

	////////// SETS //////////

	///// POST : POST META /////
	$field_model['pw_post_meta'] = array(
		'post_class',
		'link_format',
		'link_url',
		'post_author',
		'event_start',
		'event_end',
		'geo_longitude',
		'geo_latitude',
		'related_post'
		);

	////////// POSTS //////////

	///// POST : EDIT //////
	$field_model['edit'] = array(
		'ID',
		'post_id',
		'post_type',
		'post_status',
		'post_title',
		'post_content',
		'post_format',
		'post_excerpt',
		'post_name',
		'post_permalink',
		'post_date',
		'post_date_gmt',
		'post_timestamp',
		'post_class',
		'link_format',
		'link_url',
		'image(id)',
		'image(all)',
		'image(meta)',
		'taxonomy(all)',
		'taxonomy_obj(post_tag)',
		'comment_status',
		'author(ID,display_name,user_nicename,posts_url,user_profile_url)',
		'post_meta(all)',
		'post_parent',
		'event_start',
		'event_end',
		'geo_latitude',
		'geo_longitude',
		'related_post',
		'fields',
		);

	///// POST : MICRO /////
	$field_model['micro'] =	array(
		'ID',
		'post_title',
		'post_excerpt',
		'post_permalink',
		'time_ago',
		'post_date',
		'post_date_gmt',
		'post_type',
		'post_status',
		'fields',
		);

	// TODO : Refactor instances of this, then remove
	$field_model['micro'] = apply_filters( 'pw_get_post_micro_fields', $field_model['micro'] );

	///// POST : PREVIEW /////
	$field_model['preview'] = array_merge(
		$field_model['micro'],
		$field_model['pw_post_meta'],
		array(
			'post_timestamp',
			'comment_count',
			'image(all)',
			'image(stats)',
			'image(tags)',
			'post_points',
			'rank_score',
			'edit_post_link',
			'taxonomy(all)',
			'author(ID,display_name,user_nicename,posts_url,user_profile_url)',
			'avatar(small,96)',
			'post_format',
			'post_meta(all)',
			'feed_order',
			'viewer(has_voted,is_favorite,is_view_later)',
			)
		);


	// TODO : Refactor instances of this, then remove
	$field_model['preview'] = apply_filters( 'pw_get_post_preview_fields', $field_model['preview'] );

	///// POST : DETAIL /////
	$field_model['detail'] = array_merge(
		$field_model['preview'],
		array(
			'post_path',
			'image(full)',
			'post_content',
			'post_type_labels',
			'gallery(ids,posts)',
			'post_categories_list',
			'post_tags_list',
			)
		);

	///// POST : ALL /////
	$field_model['all'] = array_merge(
		$field_model['detail'],
		array(
			'parent_post(micro)',				// Gets the parent post as post_parent : parent_post( [field model] )
			'child_post_count',					// Gets the number of posts which have this post as a parent
			'child_posts_comment_count',		// Gets the sum of all comment counts on all child posts
			'child_posts_karma_count',			// Gets a sum of all the karma on all child posts
			'comments(3,all,comment_date_gmt)',	// Gets comments associated with the post : comments( [number of comments], [field model], [orderby] )
			//'post_excerpt(256,post_content)',
			)
		);
	

	///// POST : GALLERY /////
	$field_model['gallery'] =	array(
		'ID',
		'post_title',
		'post_excerpt',
		'post_content',
		'post_type',
		'post_parent',
		'post_permalink',
		'post_excerpt',
		'link_url',
		'link_format',
		'post_date',
		'post_date_gmt',
		'time_ago',
		'image(all)',
		'image(stats)',
		'image(tags)',
		'post_author',
		'fields',
		);

	///// FILTER /////
	$field_model = apply_filters( PW_MODEL_POST_FIELDS, $field_model );

	///// CACHING LAYER /////
	$pw_field_model_cache = $field_model;

	return $field_model;

}


function pw_user_field_model(){


}

?>