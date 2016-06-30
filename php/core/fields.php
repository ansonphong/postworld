<?php
/*_____ _      _     _     
 |  ___(_) ___| | __| |___ 
 | |_  | |/ _ \ |/ _` / __|
 |  _| | |  __/ | (_| \__ \
 |_|   |_|\___|_|\__,_|___/*/

/**
 * Register a field model preset
 *
 * @param string $type The type of field model [post/user]
 * @param string $name The name of the field model
 * @param array $fields An array of fields
 */
function pw_register_field_model( $type, $name, $fields ){
	global $pw;

	//pw_log( 'pw_register_field_model : ' . $type . ' : ' . $name );

	if( !is_string($name) || !is_array($fields) )
		return false;

	$pw['fields'][$type][$name] = $fields;

	return true;
}

/**
 * Register a post field model preset
 *
 * @see pw_register_field_model()
 * @param string $name The name of the field model
 * @param array $fields An array of fields
 */
function pw_register_post_field_model( $name, $fields ){
	return pw_register_field_model( 'post', $name, $fields );
}

/**
 * Register a user field model preset
 *
 * @see pw_register_field_model()
 * @param string $name The name of the field model
 * @param array $fields An array of fields
 */
function pw_register_user_field_model( $name, $fields ){
	return pw_register_field_model( 'user', $name, $fields );
}

/**
 * Returns both the post and user field models
 */
function pw_field_models(){
	global $pw;
	return apply_filters( PW_FIELD_MODELS, $pw['fields'] );
}

/**
 * Returns array of all post field models
 */
function pw_post_field_models(){
	global $pw;
	return apply_filters( PW_POST_FIELD_MODELS, $pw['fields']['post'] );
}

/**
 * Returns a specific field model and filters it
 * @see pw_register_field_model()
 * @param string $type The type of field model [post/user]
 * @param string $name The name of the specific field model
 */
function pw_get_field_model( $type, $name ){
	global $pw;

	$field_model = _get( $pw['fields'], $type.'.'.$name );

	// If the field model doesn't exist
	if( $field_model === false )
		return false;

	// Setup a caching key
	$cache_key = 'field_model_'.$type.'_'.$name;

	// If it's cached a value already
	if( pw_has_runtime_cache( $cache_key ) )
		return pw_get_runtime_cache( $cache_key );

	/**
	 * Filters the field model according to type and name
	 * Example of filter name: 'pw_post_field_model_preview'
	 */
	$field_model = apply_filters( 'pw_'.$type.'_field_model_'.$name, $field_model );

	// Set a runtime cache
	pw_set_runtime_cache( $cache_key, $field_model );

	return $field_model;
}

/**
 * Get a field model for type 'post'.
 */
function pw_get_post_field_model( $name ){
	return pw_get_field_model( 'post', $name );
}

/**
 * Registers core Postworld field models
 */
add_action( 'init', 'pw_register_core_post_field_models' );
function pw_register_core_post_field_models(){

	///// POSTWORLD POST META /////
	$fields_pw_post_meta = array(
		'post_class',
		'post_author',
		'event_start',
		'event_end',
		'geo_longitude',
		'geo_latitude',
		'related_post'
		);
	pw_register_post_field_model( 'pw_post_meta', $fields_pw_post_meta );

	///// EDIT /////
	$fields_edit = array(
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
	pw_register_post_field_model( 'edit', $fields_edit );

	///// MICRO /////
	$fields_micro = array(
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
	pw_register_post_field_model( 'micro', $fields_micro );

	///// IMAGE /////
	$fields_image = array(
		'ID',
		'post_type',
		'image(stats)',
		'image(tags)',
		'image(all)'
		);
	pw_register_post_field_model( 'image', $fields_image );

	///// PREVIEW /////
	$fields_preview = array_merge(
		$fields_micro,
		$fields_pw_post_meta,
		array(
			'post_timestamp',
			'comment_count',
			'image(all)',
			'image(stats)',
			'image(tags)',
			//'post_points',
			//'rank_score',
			'edit_post_link',
			'taxonomy(all)',
			'author(ID,display_name,user_nicename,posts_url,user_profile_url)',
			'avatar(small,96)',
			'post_format',
			'post_meta(all)',
			'feed_order',
			//'viewer(has_voted,is_favorite,is_view_later)',
			)
		);
	pw_register_post_field_model( 'preview', $fields_preview );

	///// DETAIL /////
	$fields_detail = array_merge(
		$fields_preview,
		array(
			'post_path',
			'image(full)',
			'image(colors)',
			'post_content',
			'post_type_labels',
			'gallery(ids,posts)',
			'post_categories_list',
			'post_tags_list',
			)
		);
	pw_register_post_field_model( 'detail', $fields_detail );

	///// ALL /////
	$fields_all = array_merge(
		$fields_detail,
		array(
			'image(post,micro)',	// (post, [ preset field handle ]) Gets the image post data as post.image.post
			'parent_post(micro)',				// Gets the parent post as post_parent : parent_post( [field model] )
			'child_post_count',					// Gets the number of posts which have this post as a parent
			'child_posts_comment_count',		// Gets the sum of all comment counts on all child posts
			'child_posts_karma_count',			// Gets a sum of all the karma on all child posts
			'comments(3,all,comment_date_gmt)',	// Gets comments associated with the post : comments( [number of comments], [field model], [orderby] )
			//'post_excerpt(256,post_content)',
			)
		);
	pw_register_post_field_model( 'all', $fields_all );

	///// GALLERY /////
	$fields_gallery = array(
		'ID',
		'post_title',
		'post_excerpt',
		'post_content',
		'post_type',
		'post_status',
		'post_parent',
		'post_permalink',
		'post_excerpt',
		'post_date',
		'post_date_gmt',
		'time_ago',
		'post_meta(all)',
		'image(stats)',
		'image(tags)',
		'image(all)',
		'post_author',
		'fields',
		);
	pw_register_post_field_model( 'gallery', $fields_gallery );

	return;

}


/**
 * In Development
 */
add_action( 'init', 'pw_register_core_user_field_models' );
function pw_register_core_user_field_models(){

	///// MICRO /////
	$micro_fields = array(
		'ID',
		'user_nicename',
		'display_name',
		'posts_url',
		'user_profile_url',
		);
	pw_register_user_field_model( 'micro', $micro_fields );

	///// PREVIEW /////
	$preview_fields = array_merge(
		$micro_fields,
		array(
			'avatar(medium,128)',
			)
		);
	pw_register_user_field_model( 'preview', $preview_fields );

	///// WORDPRESS /////
	$wordpress_fields = array(
		'ID',
		'user_login',
		'user_nicename',
		'user_email',
		'user_url',
		'user_registered',
		'display_name',
		'user_firstname',
		'user_lastname',
		'nickname',
		'user_description',
		'wp_capabilities',
		'admin_color',
		'closedpostboxes_page',
		'primary_blog',
		'rich_editing',
		'source_domain',
		'roles',
		'capabilities',
		'posts_url',
		);
	pw_register_user_field_model( 'wordpress', $wordpress_fields );

	///// POSTWORLD /////
	$postworld_fields = array(
		'viewed',
		'favorites',
		'location_city',
		'location_region',
		'location_country',
		'post_points',
		'comment_points',
		'post_points_meta',
		);
	pw_register_user_field_model( 'postworld', $postworld_fields );

	///// BUDDYPRESS /////
	$buddypress_fields = array(
		'user_profile_url',
		'xprofile(all)',
		);
	pw_register_user_field_model( 'buddypress', $buddypress_fields );

	///// USERMETA /////
	$usermeta_fields = array(
		'usermeta(all)',
		);
	pw_register_user_field_model( 'usermeta', $usermeta_fields );

	///// POSTWORLD AVATAR /////
	$postworld_avatar_fields = array(
		'avatar(small,64)',
		'avatar(medium,256)',
		'avatar(large,1024)',
		);
	pw_register_user_field_model( 'postworld_avatar', $postworld_avatar_fields );
	
	///// ALL /////
	$all_fields = array_merge(
		$preview_fields,
		$wordpress_fields,
		$postworld_fields,
		$buddypress_fields,
		$usermeta_fields,
		$postworld_avatar_fields
		);
	$all_fields = array_unique($all_fields);
	pw_register_user_field_model( 'all', $all_fields );

	return;

}

