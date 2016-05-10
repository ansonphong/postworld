<?php 
/**
 * Runs a WordPress standard query on the input query
 * Then returns the requested Postworld fields.
 */
function pw_wp_query( $query, $fields ){
	$query['fields'] = 'ids';
	$results = new WP_Query( $query );
	return pw_get_posts( $results->posts, $fields );
}

/**
 * Wrapper function for PW_Query class.
 */
function pw_query($args,$return_type = 'PW_QUERY') {     
	$the_query = new PW_Query($args);
	switch( $return_type ){
		case 'ARRAY_A':
			return (array) $the_query;
		case 'JSON':
			return json_encode($the_query);
		default:
			return $the_query;
	}
}

/**
 * This extends WP_Query class to use Postworld fields:
 * • Query for link_format & post_class fields from postworld_post_meta table
 * • Sort posts by points & rank_score fields in the postworld_post_meta table
 * • Defines which fields are returned using pw_get_posts() method
 *
 * @todo Destroy PW_Query class altogether, replace with WP_Query filters
 */
class PW_Query extends WP_Query {
	 
	/*
	function __construct( $args = array() ) {
		
		$args = wp_parse_args( $args, array(
		    'post_type' => 'book',
		    'orderby' => 'title',
		    'order' => 'ASC',
		    // Turn off paging
		    'posts_per_page' => -1,            
		    // Since, we won't be paging,
		    // no need to count rows
		'no_found_rows' => true
		) );
		parent::__construct( $args );
		
		//pw_log( 'args', $args );
	}
	*/

	function prepare_fields(){
		$fields = $this->query_vars['fields'];
		if($fields == null || $fields=='' ) 
			$fields='preview';
		return $fields;
	}
	
	function prepare_order_by(){
		global $wpdb;
		
		if( array_key_exists( 'orderby',  $this->query_vars) )
			$orderby = $this->query_vars['orderby'];
		else
			$orderby=null;

		if($orderby!=null && $orderby!=''){
			$orderby = str_replace("date", 			$wpdb->prefix."posts.post_date", $orderby);	
			$orderby = str_replace("rank_score", 	$wpdb->postworld_prefix."post_meta.rank_score", $orderby);	//**
			$orderby = str_replace("post_points", 	$wpdb->postworld_prefix."post_meta.post_points", $orderby); //**
			$orderby = str_replace("modified", 		$wpdb->prefix."posts.post_modified", $orderby);	
			$orderby = str_replace("rand", 			"RAND()", $orderby);	
			$orderby = str_replace("comment_count", $wpdb->prefix."posts.comment_count", $orderby);	
			$orderby = str_replace("event_start", 	$wpdb->postworld_prefix."post_meta.event_start", $orderby); //**
			$orderby = str_replace("event_end", 	$wpdb->postworld_prefix."post_meta.event_end", $orderby); //**
			$orderby = "order by ".str_replace(' ', ',', $orderby);//." ".$args->order;
			$orderby.=" ".$this->query_vars['order'];
		}
		else
			$orderby = 'order by '.$wpdb->prefix.'posts.post_date '.$this->query_vars['order'];
		
		if($this->query_vars['posts_per_page']!=null && $this->query_vars['posts_per_page']!='' && $this->query_vars['posts_per_page']>-1 ){
			if(array_key_exists('offset',  $this->query_vars))
				$orderby.=" Limit ".$this->query_vars["offset"].", ".$this->query_vars['posts_per_page'];	
			else $orderby.=" LIMIT 0,".$this->query_vars['posts_per_page'];
		}
		return $orderby;
				
	}

	function prepare_related_query(){
		///// related_post  /////
		$related_post = $this->query_vars['related_post'];
		$related_query = "related_post = ".$related_post;
		return $related_query." AND ";
	}





	function prepare_time_query(){
		///// event_start,  /////

		$time_query = '';
		$add_and = false;


		if(array_key_exists('event_start',  $this->query_vars)){
			// all events with event_end after this
			$event_start = $this->query_vars['event_start'];
			
			if($add_and == false){
				$time_query = "event_end > ".$event_start;
				$add_and = true;	
			} else {
				$time_query = " AND event_end > ".$event_start;
			}
		}

		///// event_end /////
		if(array_key_exists('event_end',  $this->query_vars)){
			// all events with event_start before this
			$event_end = $this->query_vars['event_end'];

			if($add_and == false){
				$time_query .= "event_start < ".$event_end;
				$add_and = true;
			} else {
				$time_query .= " AND event_start < ".$event_end;
			}
		}

		///// event_before /////
		if(array_key_exists('event_before',  $this->query_vars)){
			// all events with event_end before this
			$event_before = $this->query_vars['event_before'];

			if($add_and == false){
				$time_query .= "event_end < ".$event_before;
				$add_and = true;
			} else {
				$time_query .= " AND event_end < ".$event_before;
			}
		}

		///// event_after /////
		if(array_key_exists('event_after',  $this->query_vars)){
			//pw_log( 'PW_Query : event_after', $this->query_vars['event_after'] );
			// all events with event_start after this
			$event_after = $this->query_vars['event_after'];

			if($add_and == false){
				$time_query .= "event_start > ".$event_after;
				$add_and = true;
			} else {
				$time_query .= " AND event_start > ".$event_after;
			}
		}

		/**
		 * FILTER EVENTS
		 * Adds a complex of clauses based on predefined
		 * filter options. Clauses are grouped with OR operator,
		 * and so are internally additive.
		 */
		if( array_key_exists('event_filter',  $this->query_vars) ){
			$current_timestamp = time();
			$event_filters = $this->query_vars['event_filter'];

			// Force string value as an array
			if( is_string( $event_filters ) )
				$event_filters = array( $event_filters );	

			// If 'event_filter' is not the first clause, add 'AND'
			if( $add_and )
				$time_query .= " AND ";
			$add_and = true;

			/**
			 * @todo :  Here make sure all the event filters
			 *			are from a list of pre-registered options
			 *			Otherwise it could mess up the rest of the query. //**
			*/

			$filter_count = count( $event_filters );

			// Wrap in brackets if multiple filters
			if( $filter_count > 1 )
				$time_query .= " ( ";

			// Iterate through each of the event filters
			$i = 0;
			foreach( $event_filters as $filter ){
				$i++;
				$last_iteration = ( $i === $filter_count ) ? true : false;

				// Switch queries added based on filter
				switch( $filter ){
					case 'past':
						$time_query .= "event_end < ".$current_timestamp;
						break;

					case 'now':
					case 'current':
						$time_query .= "( event_end > ".$current_timestamp." AND event_start < ".$current_timestamp ." ) ";
						break;

					case 'future':
					case 'upcoming':
						$time_query .= "event_start > ".$current_timestamp;
						break;
				}

				// If there is a following clause, add 'OR'
				if( !$last_iteration )
					$time_query .= " OR ";

			}

			// Wrap in brackets if multiple filters
			if( $filter_count > 1 )
				$time_query .= " ) ";

		}

		// Return Query
		if($time_query == "") return "";
		return $time_query." AND ";
	}





	function prepare_geo_query(){
		///// GEO LATITUDE /////

		$geo_query = '';

		$latitude = $this->query_vars['geo_latitude'];
		$longitude = $this->query_vars['geo_longitude'];

		if(array_key_exists('geo_range',  $this->query_vars)){
			// Apply range
			$range = $this->query_vars['geo_range'];

			$lat_low = $latitude - $range;
			$lat_high = $latitude + $range;
			$geo_query = "geo_latitude BETWEEN ".$lat_low." AND ".$lat_high;

			$lng_low = $longitude - $range;
			$lng_high = $longitude + $range;
			$geo_query.=" AND geo_longitude BETWEEN ".$lng_low." AND ".$lng_high;
		} else {
			// Default geo query
			$geo_query = "geo_latitude = ".$latitude;
			$geo_query.=" AND geo_longitude = ".$longitude;
		}

		if($geo_query == "") return " AND ";
		return $geo_query." AND ";
	}





	

	/**
	 * @todo Look at the posts_where filter in WP_Query //**
	 */
	function prepare_where_query(){
		
		$where ="WHERE ";	
		$insertAnd= '0';

		///// LINK FORMAT /////
		// Must be first (or config $where.= to work and update the first one to work like this one currently does)
		if(array_key_exists('link_format',  $this->query_vars)){
			if(gettype($this->query_vars['link_format']) == "array") {
					if($insertAnd=='0'){
						 //$where.=" and ";
						 $insertAnd = '1';
						
					}	
					$where.=" link_format in ('".implode("','", $this->query_vars['link_format'])."') ";
					
				}
				else if(gettype($this->query_vars['link_format']) == "string"){
					if($insertAnd=='0'){
						// $where.=" and ";
						 $insertAnd = '1';
					}	
					$where.=" link_format = '".$this->query_vars['link_format']."' ";
				}
		}
	
		///// POST CLASS /////
		if(array_key_exists('post_class',  $this->query_vars)){
				if(gettype($this->query_vars['post_class']) == "array") {
					if($insertAnd=='1'){
						 $where.=" and ";
						 $insertAnd = '0';
						
					}	
					$where.=" post_class in ('".implode("','", $this->query_vars['post_class'])."') ";
				}
				else if(gettype($this->query_vars['post_class']) == "string"){
					if($insertAnd=='1'){
						 $where.=" and ";
						 $insertAnd = '0'; 
					}	
					$where.=" post_class = '".$this->query_vars['post_class']."' ";
				}
		}

		if($where =="WHERE ") return $where;	
		return $where." AND ";
	}
	
	/**
	 * @todo Look at posts_join filter in WP_Query //**
	 */
	function prepare_new_request($remove_tbl=false){
		global $wpdb;
		$orderBy = $this->prepare_order_by();
		$where = $this->prepare_where_query();

		// Check for geo_latitude
		if(array_key_exists('geo_latitude',  $this->query_vars) && array_key_exists('geo_longitude',  $this->query_vars)){
			$where = $where.$this->prepare_geo_query();
		}

		// Check for event_start
		if($this->has_time_attributes()){
			$where = $where.$this->prepare_time_query();
		}

		// Check for related_post
		if(array_key_exists('related_post', $this->query_vars)){
			$where = $where.$this->prepare_related_query();
		}

		//$where.=" AND ";
		//echo($this->query_vars['fields']);
		if($remove_tbl==false )
		$this->request = str_replace('SELECT', 'SELECT '.$wpdb->prefix.'postworld_post_meta.* , ', $this->request);
			$this->request = str_replace('FROM '.$wpdb->prefix.'posts','FROM '.$wpdb->prefix.'posts left join  '.$wpdb->prefix.'postworld_post_meta on '.$wpdb->prefix.'posts.ID = '.$wpdb->prefix.'postworld_post_meta.post_id ', $this->request);
			$this->request = str_replace('WHERE', $where, $this->request);
			$strposOfOrderBy = strpos($this->request, "ORDER BY");
			$this->request =  substr($this->request ,0,$strposOfOrderBy);
			//$this->request = str_replace("AND 0 = 1", " ", $this->request);
			$this->request.=$orderBy;
		
	}

	function has_time_attributes(){
		if(
			array_key_exists('event_filter',  $this->query_vars) ||
			array_key_exists('event_start',  $this->query_vars) ||
			array_key_exists('event_end',  $this->query_vars) ||
			array_key_exists('event_before',  $this->query_vars) ||
			array_key_exists('event_after',  $this->query_vars)){
			return true;
		} else {
			return false;
		}
	}
	

	/**
	 * REMOVE THIS, AND TWEAK HIGHER LEVEL FUNCTION
	 * WHICH JUST GETS IDS 
	 */
	function get_posts() {
		
		global $wpdb, $user_ID, $_wp_using_ext_object_cache;
	
		if( pw_dev_mode() )
			$wpdb -> show_errors();
	
		$this->parse_query();

		do_action_ref_array('pre_get_posts', array(&$this));

		// Shorthand.
		$q = &$this->query_vars;

		// Fill again in case pre_get_posts unset some vars.
		$q = $this->fill_query_vars($q);

		// Parse meta query
		$this->meta_query = new WP_Meta_Query();
		$this->meta_query->parse_query_vars( $q );

		// Set a flag if a pre_get_posts hook changed the query vars.
		$hash = md5( serialize( $this->query_vars ) );
		if ( $hash != $this->query_vars_hash ) {
			$this->query_vars_changed = true;
			$this->query_vars_hash = $hash;
		}
		unset($hash);

		// First let's clear some variables
		$distinct = '';
		$whichauthor = '';
		$whichmimetype = '';
		$where = '';
		$limits = '';
		$join = '';
		$search = '';
		$groupby = '';
		$fields = '';
		$post_status_join = false;
		$page = 1;

		if ( isset( $q['caller_get_posts'] ) ) {
			_deprecated_argument( 'WP_Query', '3.1', __( '"caller_get_posts" is deprecated. Use "ignore_sticky_posts" instead.' ) );
			if ( !isset( $q['ignore_sticky_posts'] ) )
				$q['ignore_sticky_posts'] = $q['caller_get_posts'];
		}

		if ( !isset( $q['ignore_sticky_posts'] ) )
			$q['ignore_sticky_posts'] = false;

		if ( !isset($q['suppress_filters']) )
			$q['suppress_filters'] = false;

		if ( !isset($q['cache_results']) ) {
			if ( $_wp_using_ext_object_cache )
				$q['cache_results'] = false;
			else
				$q['cache_results'] = true;
		}

		if ( !isset($q['update_post_term_cache']) )
			$q['update_post_term_cache'] = true;

		if ( !isset($q['update_post_meta_cache']) )
			$q['update_post_meta_cache'] = true;

		if ( !isset($q['post_type']) ) {
			if ( $this->is_search )
				$q['post_type'] = 'any';
			else
				$q['post_type'] = '';
		}
		$post_type = $q['post_type'];
		if ( !isset($q['posts_per_page']) || $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = get_option('posts_per_page');
		if ( isset($q['showposts']) && $q['showposts'] ) {
			$q['showposts'] = (int) $q['showposts'];
			$q['posts_per_page'] = $q['showposts'];
		}
		if ( (isset($q['posts_per_archive_page']) && $q['posts_per_archive_page'] != 0) && ($this->is_archive || $this->is_search) )
			$q['posts_per_page'] = $q['posts_per_archive_page'];
		if ( !isset($q['nopaging']) ) {
			if ( $q['posts_per_page'] == -1 ) {
				$q['nopaging'] = true;
			} else {
				$q['nopaging'] = false;
			}
		}
		if ( $this->is_feed ) {
			$q['posts_per_page'] = get_option('posts_per_rss');
			$q['nopaging'] = false;
		}
		$q['posts_per_page'] = (int) $q['posts_per_page'];
		if ( $q['posts_per_page'] < -1 )
			$q['posts_per_page'] = abs($q['posts_per_page']);
		else if ( $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = 1;

		if ( !isset($q['comments_per_page']) || $q['comments_per_page'] == 0 )
			$q['comments_per_page'] = get_option('comments_per_page');

		if ( $this->is_home && (empty($this->query) || $q['preview'] == 'true') && ( 'page' == get_option('show_on_front') ) && get_option('page_on_front') ) {
			$this->is_page = true;
			$this->is_home = false;
			$q['page_id'] = get_option('page_on_front');
		}

		if ( isset($q['page']) ) {
			$q['page'] = trim($q['page'], '/');
			$q['page'] = absint($q['page']);
		}

		// If true, forcibly turns off SQL_CALC_FOUND_ROWS even when limits are present.
		if ( isset($q['no_found_rows']) )
			$q['no_found_rows'] = (bool) $q['no_found_rows'];
		else
			$q['no_found_rows'] = false;

		switch ( $q['fields'] ) {
			case 'ids':
				$fields = "$wpdb->posts.ID";
				break;
			case 'id=>parent':
				$fields = "$wpdb->posts.ID, $wpdb->posts.post_parent";
				break;
			default:
				$fields = "$wpdb->posts.*";
		}

		if ( '' !== $q['menu_order'] )
			$where .= " AND $wpdb->posts.menu_order = " . $q['menu_order'];

		// If a month is specified in the querystring, load that month
		if ( $q['m'] ) {
			$q['m'] = '' . preg_replace('|[^0-9]|', '', $q['m']);
			$where .= " AND YEAR($wpdb->posts.post_date)=" . substr($q['m'], 0, 4);
			if ( strlen($q['m']) > 5 )
				$where .= " AND MONTH($wpdb->posts.post_date)=" . substr($q['m'], 4, 2);
			if ( strlen($q['m']) > 7 )
				$where .= " AND DAYOFMONTH($wpdb->posts.post_date)=" . substr($q['m'], 6, 2);
			if ( strlen($q['m']) > 9 )
				$where .= " AND HOUR($wpdb->posts.post_date)=" . substr($q['m'], 8, 2);
			if ( strlen($q['m']) > 11 )
				$where .= " AND MINUTE($wpdb->posts.post_date)=" . substr($q['m'], 10, 2);
			if ( strlen($q['m']) > 13 )
				$where .= " AND SECOND($wpdb->posts.post_date)=" . substr($q['m'], 12, 2);
		}

		if ( '' !== $q['hour'] )
			$where .= " AND HOUR($wpdb->posts.post_date)='" . $q['hour'] . "'";

		if ( '' !== $q['minute'] )
			$where .= " AND MINUTE($wpdb->posts.post_date)='" . $q['minute'] . "'";

		if ( '' !== $q['second'] )
			$where .= " AND SECOND($wpdb->posts.post_date)='" . $q['second'] . "'";

		if ( $q['year'] )
			$where .= " AND YEAR($wpdb->posts.post_date)='" . $q['year'] . "'";

		if ( $q['monthnum'] )
			$where .= " AND MONTH($wpdb->posts.post_date)='" . $q['monthnum'] . "'";

		if ( $q['day'] )
			$where .= " AND DAYOFMONTH($wpdb->posts.post_date)='" . $q['day'] . "'";

		// If we've got a post_type AND it's not "any" post_type.
		if ( !empty($q['post_type']) && 'any' != $q['post_type'] ) {
			foreach ( (array)$q['post_type'] as $_post_type ) {
				$ptype_obj = get_post_type_object($_post_type);
				if ( !$ptype_obj || !$ptype_obj->query_var || empty($q[ $ptype_obj->query_var ]) )
					continue;

				if ( ! $ptype_obj->hierarchical || strpos($q[ $ptype_obj->query_var ], '/') === false ) {
					// Non-hierarchical post_types & parent-level-hierarchical post_types can directly use 'name'
					$q['name'] = $q[ $ptype_obj->query_var ];
				} else {
					// Hierarchical post_types will operate through the
					$q['pagename'] = $q[ $ptype_obj->query_var ];
					$q['name'] = '';
				}

				// Only one request for a slug is possible, this is why name & pagename are overwritten above.
				break;
			} //end foreach
			unset($ptype_obj);
		}

		if ( '' != $q['name'] ) {
			$q['name'] = sanitize_title_for_query( $q['name'] );
			$where .= " AND $wpdb->posts.post_name = '" . $q['name'] . "'";
		} elseif ( '' != $q['pagename'] ) {
			if ( isset($this->queried_object_id) ) {
				$reqpage = $this->queried_object_id;
			} else {
				if ( 'page' != $q['post_type'] ) {
					foreach ( (array)$q['post_type'] as $_post_type ) {
						$ptype_obj = get_post_type_object($_post_type);
						if ( !$ptype_obj || !$ptype_obj->hierarchical )
							continue;

						$reqpage = get_page_by_path($q['pagename'], OBJECT, $_post_type);
						if ( $reqpage )
							break;
					}
					unset($ptype_obj);
				} else {
					$reqpage = get_page_by_path($q['pagename']);
				}
				if ( !empty($reqpage) )
					$reqpage = $reqpage->ID;
				else
					$reqpage = 0;
			}

			$page_for_posts = get_option('page_for_posts');
			if  ( ('page' != get_option('show_on_front') ) || empty($page_for_posts) || ( $reqpage != $page_for_posts ) ) {
				$q['pagename'] = sanitize_title_for_query( wp_basename( $q['pagename'] ) );
				$q['name'] = $q['pagename'];
				$where .= " AND ($wpdb->posts.ID = '$reqpage')";
				$reqpage_obj = get_post( $reqpage );
				if ( is_object($reqpage_obj) && 'attachment' == $reqpage_obj->post_type ) {
					$this->is_attachment = true;
					$post_type = $q['post_type'] = 'attachment';
					$this->is_page = true;
					$q['attachment_id'] = $reqpage;
				}
			}
		} elseif ( '' != $q['attachment'] ) {
			$q['attachment'] = sanitize_title_for_query( wp_basename( $q['attachment'] ) );
			$q['name'] = $q['attachment'];
			$where .= " AND $wpdb->posts.post_name = '" . $q['attachment'] . "'";
		}

		if ( $q['w'] )
			$where .= ' AND ' . _wp_mysql_week( "`$wpdb->posts`.`post_date`" ) . " = '" . $q['w'] . "'";

		if ( intval($q['comments_popup']) )
			$q['p'] = absint($q['comments_popup']);

		// If an attachment is requested by number, let it supersede any post number.
		if ( $q['attachment_id'] )
			$q['p'] = absint($q['attachment_id']);

		// If a post number is specified, load that post
		if ( $q['p'] ) {
			$where .= " AND {$wpdb->posts}.ID = " . $q['p'];
		} elseif ( $q['post__in'] ) {
			$post__in = implode(',', array_map( 'absint', $q['post__in'] ));
			$where .= " AND {$wpdb->posts}.ID IN ($post__in)";
		} elseif ( $q['post__not_in'] ) {
			$post__not_in = implode(',',  array_map( 'absint', $q['post__not_in'] ));
			$where .= " AND {$wpdb->posts}.ID NOT IN ($post__not_in)";
		}

		if ( is_numeric( $q['post_parent'] ) ) {
			$where .= $wpdb->prepare( " AND $wpdb->posts.post_parent = %d ", $q['post_parent'] );
		} elseif ( $q['post_parent__in'] ) {
			$post_parent__in = implode( ',', array_map( 'absint', $q['post_parent__in'] ) );
			$where .= " AND {$wpdb->posts}.post_parent IN ($post_parent__in)";
		} elseif ( $q['post_parent__not_in'] ) {
			$post_parent__not_in = implode( ',',  array_map( 'absint', $q['post_parent__not_in'] ) );
			$where .= " AND {$wpdb->posts}.post_parent NOT IN ($post_parent__not_in)";
		}

		if ( $q['page_id'] ) {
			if  ( ('page' != get_option('show_on_front') ) || ( $q['page_id'] != get_option('page_for_posts') ) ) {
				$q['p'] = $q['page_id'];
				$where = " AND {$wpdb->posts}.ID = " . $q['page_id'];
			}
		}

		// If a search pattern is specified, load the posts that match
		if ( !empty($q['s']) ) {
			// added slashes screw with quote grouping when done early, so done later
			$q['s'] = stripslashes($q['s']);
			if ( empty( $_GET['s'] ) && $this->is_main_query() )
				$q['s'] = urldecode($q['s']);
			if ( !empty($q['sentence']) ) {
				$q['search_terms'] = array($q['s']);
			} else {
				preg_match_all('/".*?("|$)|((?<=[\r\n\t ",+])|^)[^\r\n\t ",+]+/', $q['s'], $matches);
				$q['search_terms'] = array_map('_search_terms_tidy', $matches[0]);
			}
			$n = !empty($q['exact']) ? '' : '%';
			$searchand = '';
			foreach( (array) $q['search_terms'] as $term ) {
				$term = esc_sql( like_escape( $term ) );
				$search .= "{$searchand}(($wpdb->posts.post_title LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}'))";
				$searchand = ' AND ';
			}

			if ( !empty($search) ) {
				$search = " AND ({$search}) ";
				if ( !is_user_logged_in() )
					$search .= " AND ($wpdb->posts.post_password = '') ";
			}
		}

		// Allow plugins to contextually add/remove/modify the search section of the database query
		$search = apply_filters_ref_array('posts_search', array( $search, &$this ) );

		// Taxonomies
		if ( !$this->is_singular ) {
			$this->parse_tax_query( $q );

			$clauses = $this->tax_query->get_sql( $wpdb->posts, 'ID' );

			$join .= $clauses['join'];
			$where .= $clauses['where'];
		}

		if ( $this->is_tax ) {
			if ( empty($post_type) ) {
				// Do a fully inclusive search for currently registered post types of queried taxonomies
				$post_type = array();
				$taxonomies = wp_list_pluck( $this->tax_query->queries, 'taxonomy' );
				foreach ( get_post_types( array( 'exclude_from_search' => false ) ) as $pt ) {
					$object_taxonomies = $pt === 'attachment' ? get_taxonomies_for_attachments() : get_object_taxonomies( $pt );
					if ( array_intersect( $taxonomies, $object_taxonomies ) )
						$post_type[] = $pt;
				}
				if ( ! $post_type )
					$post_type = 'any';
				elseif ( count( $post_type ) == 1 )
					$post_type = $post_type[0];

				$post_status_join = true;
			} elseif ( in_array('attachment', (array) $post_type) ) {
				$post_status_join = true;
			}
		}

		// Back-compat
		if ( !empty($this->tax_query->queries) ) {
			$tax_query_in_and = wp_list_filter( $this->tax_query->queries, array( 'operator' => 'NOT IN' ), 'NOT' );
			if ( !empty( $tax_query_in_and ) ) {
				if ( !isset( $q['taxonomy'] ) ) {
					foreach ( $tax_query_in_and as $a_tax_query ) {
						if ( !in_array( $a_tax_query['taxonomy'], array( 'category', 'post_tag' ) ) ) {
							$q['taxonomy'] = $a_tax_query['taxonomy'];
							if ( 'slug' == $a_tax_query['field'] )
								$q['term'] = $a_tax_query['terms'][0];
							else
								$q['term_id'] = $a_tax_query['terms'][0];

							break;
						}
					}
				}

				$cat_query = wp_list_filter( $tax_query_in_and, array( 'taxonomy' => 'category' ) );
				if ( ! empty( $cat_query ) ) {
					$cat_query = reset( $cat_query );

					if ( ! empty( $cat_query['terms'][0] ) ) {
						$the_cat = get_term_by( $cat_query['field'], $cat_query['terms'][0], 'category' );
						if ( $the_cat ) {
							$this->set( 'cat', $the_cat->term_id );
							$this->set( 'category_name', $the_cat->slug );
						}
						unset( $the_cat );
					}
				}
				unset( $cat_query );

				$tag_query = wp_list_filter( $tax_query_in_and, array( 'taxonomy' => 'post_tag' ) );
				if ( ! empty( $tag_query ) ) {
					$tag_query = reset( $tag_query );

					if ( ! empty( $tag_query['terms'][0] ) ) {
						$the_tag = get_term_by( $tag_query['field'], $tag_query['terms'][0], 'post_tag' );
						if ( $the_tag )
							$this->set( 'tag_id', $the_tag->term_id );
						unset( $the_tag );
					}
				}
				unset( $tag_query );
			}
		}

		if ( !empty( $this->tax_query->queries ) || !empty( $this->meta_query->queries ) ) {
			$groupby = "{$wpdb->posts}.ID";
		}

		// Author/user stuff

		if ( empty($q['author']) || ($q['author'] == '0') ) {
			$whichauthor = '';
		} else {
			$q['author'] = (string)urldecode($q['author']);
			$q['author'] = addslashes_gpc($q['author']);
			if ( strpos($q['author'], '-') !== false ) {
				$eq = '!=';
				$andor = 'AND';
				$q['author'] = explode('-', $q['author']);
				$q['author'] = (string)absint($q['author'][1]);
			} else {
				$eq = '=';
				$andor = 'OR';
			}
			$author_array = preg_split('/[,\s]+/', $q['author']);
			$_author_array = array();
			foreach ( $author_array as $key => $_author )
				$_author_array[] = "$wpdb->posts.post_author " . $eq . ' ' . absint($_author);
			$whichauthor .= ' AND (' . implode(" $andor ", $_author_array) . ')';
			unset($author_array, $_author_array);
		}

		// Author stuff for nice URLs

		if ( '' != $q['author_name'] ) {
			if ( strpos($q['author_name'], '/') !== false ) {
				$q['author_name'] = explode('/', $q['author_name']);
				if ( $q['author_name'][ count($q['author_name'])-1 ] ) {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-1]; // no trailing slash
				} else {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-2]; // there was a trailing slash
				}
			}
			$q['author_name'] = sanitize_title_for_query( $q['author_name'] );
			$q['author'] = get_user_by('slug', $q['author_name']);
			if ( $q['author'] )
				$q['author'] = $q['author']->ID;
			$whichauthor .= " AND ($wpdb->posts.post_author = " . absint($q['author']) . ')';
		}

		// MIME-Type stuff for attachment browsing

		if ( isset( $q['post_mime_type'] ) && '' != $q['post_mime_type'] )
			$whichmimetype = wp_post_mime_type_where( $q['post_mime_type'], $wpdb->posts );

		$where .= $search . $whichauthor . $whichmimetype;

		if ( empty($q['order']) || ((strtoupper($q['order']) != 'ASC') && (strtoupper($q['order']) != 'DESC')) )
			$q['order'] = 'DESC';


		// Order by
		if ( empty($q['orderby']) ) {
			$orderby = "$wpdb->posts.post_date " . $q['order'];
		} elseif ( 'none' == $q['orderby'] ) {
			$orderby = '';
		} elseif ( $q['orderby'] == 'post__in' && ! empty( $post__in ) ) {
			$orderby = "FIELD( {$wpdb->posts}.ID, $post__in )";
		} elseif ( $q['orderby'] == 'post_parent__in' && ! empty( $post_parent__in ) ) {
			$orderby = "FIELD( {$wpdb->posts}.post_parent, $post_parent__in )";
		} else {
			// Used to filter values
			$allowed_keys = array('name', 'author', 'date', 'title', 'modified', 'menu_order', 'parent', 'ID', 'rand', 'comment_count');
			if ( !empty($q['meta_key']) ) {
				$allowed_keys[] = $q['meta_key'];
				$allowed_keys[] = 'meta_value';
				$allowed_keys[] = 'meta_value_num';
			}
			$q['orderby'] = urldecode($q['orderby']);
			$q['orderby'] = addslashes_gpc($q['orderby']);

			$orderby_array = array();
			foreach ( explode( ' ', $q['orderby'] ) as $i => $orderby ) {
				// Only allow certain values for safety
				if ( ! in_array($orderby, $allowed_keys) )
					continue;

				switch ( $orderby ) {
					case 'menu_order':
						$orderby = "$wpdb->posts.menu_order";
						break;
					case 'ID':
						$orderby = "$wpdb->posts.ID";
						break;
					case 'rand':
						$orderby = 'RAND()';
						break;
					case $q['meta_key']:
					case 'meta_value':
						$orderby = "$wpdb->postmeta.meta_value";
						break;
					case 'meta_value_num':
						$orderby = "$wpdb->postmeta.meta_value+0";
						break;
					case 'comment_count':
						$orderby = "$wpdb->posts.comment_count";
						break;
					default:
						$orderby = "$wpdb->posts.post_" . $orderby;
				}

				$orderby_array[] = $orderby;
			}
			$orderby = implode( ',', $orderby_array );

			if ( empty( $orderby ) )
				$orderby = "$wpdb->posts.post_date ".$q['order'];
			else
				$orderby .= " {$q['order']}";
		}



		if ( is_array( $post_type ) && count( $post_type ) > 1 ) {
			$post_type_cap = 'multiple_post_type';
		} else {
			if ( is_array( $post_type ) )
				$post_type = reset( $post_type );
			$post_type_object = get_post_type_object( $post_type );
			if ( empty( $post_type_object ) )
				$post_type_cap = $post_type;
		}

		if ( 'any' == $post_type ) {
			$in_search_post_types = get_post_types( array('exclude_from_search' => false) );
			if ( ! empty( $in_search_post_types ) )
				$where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $in_search_post_types ) . "')";
		} elseif ( !empty( $post_type ) && is_array( $post_type ) ) {
			$where .= " AND $wpdb->posts.post_type IN ('" . join("', '", $post_type) . "')";
		} elseif ( ! empty( $post_type ) ) {
			$where .= " AND $wpdb->posts.post_type = '$post_type'";
			$post_type_object = get_post_type_object ( $post_type );
		} elseif ( $this->is_attachment ) {
			$where .= " AND $wpdb->posts.post_type = 'attachment'";
			$post_type_object = get_post_type_object ( 'attachment' );
		} elseif ( $this->is_page ) {
			$where .= " AND $wpdb->posts.post_type = 'page'";
			$post_type_object = get_post_type_object ( 'page' );
		} else {
			$where .= " AND $wpdb->posts.post_type = 'post'";
			$post_type_object = get_post_type_object ( 'post' );
		}

		$edit_cap = 'edit_post';
		$read_cap = 'read_post';

		if ( ! empty( $post_type_object ) ) {
			$edit_others_cap = $post_type_object->cap->edit_others_posts;
			$read_private_cap = $post_type_object->cap->read_private_posts;
		} else {
			$edit_others_cap = 'edit_others_' . $post_type_cap . 's';
			$read_private_cap = 'read_private_' . $post_type_cap . 's';
		}

		if ( ! empty( $q['post_status'] ) ) {
			$statuswheres = array();
			$q_status = $q['post_status'];
			if ( ! is_array( $q_status ) )
				$q_status = explode(',', $q_status);
			$r_status = array();
			$p_status = array();
			$e_status = array();
			if ( in_array('any', $q_status) ) {
				foreach ( get_post_stati( array('exclude_from_search' => true) ) as $status )
					$e_status[] = "$wpdb->posts.post_status <> '$status'";
			} else {
				foreach ( get_post_stati() as $status ) {
					if ( in_array( $status, $q_status ) ) {
						if ( 'private' == $status )
							$p_status[] = "$wpdb->posts.post_status = '$status'";
						else
							$r_status[] = "$wpdb->posts.post_status = '$status'";
					}
				}
			}

			if ( empty($q['perm'] ) || 'readable' != $q['perm'] ) {
				$r_status = array_merge($r_status, $p_status);
				unset($p_status);
			}

			if ( !empty($e_status) ) {
				$statuswheres[] = "(" . join( ' AND ', $e_status ) . ")";
			}
			if ( !empty($r_status) ) {
				if ( !empty($q['perm'] ) && 'editable' == $q['perm'] && !current_user_can($edit_others_cap) )
					$statuswheres[] = "($wpdb->posts.post_author = $user_ID " . "AND (" . join( ' OR ', $r_status ) . "))";
				else
					$statuswheres[] = "(" . join( ' OR ', $r_status ) . ")";
			}
			if ( !empty($p_status) ) {
				if ( !empty($q['perm'] ) && 'readable' == $q['perm'] && !current_user_can($read_private_cap) )
					$statuswheres[] = "($wpdb->posts.post_author = $user_ID " . "AND (" . join( ' OR ', $p_status ) . "))";
				else
					$statuswheres[] = "(" . join( ' OR ', $p_status ) . ")";
			}
			if ( $post_status_join ) {
				$join .= " LEFT JOIN $wpdb->posts AS p2 ON ($wpdb->posts.post_parent = p2.ID) ";
				foreach ( $statuswheres as $index => $statuswhere )
					$statuswheres[$index] = "($statuswhere OR ($wpdb->posts.post_status = 'inherit' AND " . str_replace($wpdb->posts, 'p2', $statuswhere) . "))";
			}
			foreach ( $statuswheres as $statuswhere )
				$where .= " AND $statuswhere";
		} elseif ( !$this->is_singular ) {
			$where .= " AND ($wpdb->posts.post_status = 'publish'";

			// Add public states.
			$public_states = get_post_stati( array('public' => true) );
			foreach ( (array) $public_states as $state ) {
				if ( 'publish' == $state ) // Publish is hard-coded above.
					continue;
				$where .= " OR $wpdb->posts.post_status = '$state'";
			}

			if ( $this->is_admin ) {
				// Add protected states that should show in the admin all list.
				$admin_all_states = get_post_stati( array('protected' => true, 'show_in_admin_all_list' => true) );
				foreach ( (array) $admin_all_states as $state )
					$where .= " OR $wpdb->posts.post_status = '$state'";
			}

			if ( is_user_logged_in() ) {
				// Add private states that are limited to viewing by the author of a post or someone who has caps to read private states.
				$private_states = get_post_stati( array('private' => true) );
				foreach ( (array) $private_states as $state )
					$where .= current_user_can( $read_private_cap ) ? " OR $wpdb->posts.post_status = '$state'" : " OR $wpdb->posts.post_author = $user_ID AND $wpdb->posts.post_status = '$state'";
			}

			$where .= ')';
		}

		if ( !empty( $this->meta_query->queries ) ) {
			$clauses = $this->meta_query->get_sql( 'post', $wpdb->posts, 'ID', $this );
			$join .= $clauses['join'];
			$where .= $clauses['where'];
		}

		// Apply filters on where and join prior to paging so that any
		// manipulations to them are reflected in the paging by day queries.
		if ( !$q['suppress_filters'] ) {
			$where = apply_filters_ref_array('posts_where', array( $where, &$this ) );
			$join = apply_filters_ref_array('posts_join', array( $join, &$this ) );
		}

		// Paging
		if ( empty($q['nopaging']) && !$this->is_singular ) {
			$page = absint($q['paged']);
			if ( !$page )
				$page = 1;

			if ( empty($q['offset']) ) {
				$pgstrt = ($page - 1) * $q['posts_per_page'] . ', ';
			} else { // we're ignoring $page and using 'offset'
				$q['offset'] = absint($q['offset']);
				$pgstrt = $q['offset'] . ', ';
			}
			$limits = 'LIMIT ' . $pgstrt . $q['posts_per_page'];
		}

		// Comments feeds
		if ( $this->is_comment_feed && ( $this->is_archive || $this->is_search || !$this->is_singular ) ) {
			if ( $this->is_archive || $this->is_search ) {
				$cjoin = "JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) $join ";
				$cwhere = "WHERE comment_approved = '1' $where";
				$cgroupby = "$wpdb->comments.comment_id";
			} else { // Other non singular e.g. front
				$cjoin = "JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID )";
				$cwhere = "WHERE post_status = 'publish' AND comment_approved = '1'";
				$cgroupby = '';
			}

			if ( !$q['suppress_filters'] ) {
				$cjoin = apply_filters_ref_array('comment_feed_join', array( $cjoin, &$this ) );
				$cwhere = apply_filters_ref_array('comment_feed_where', array( $cwhere, &$this ) );
				$cgroupby = apply_filters_ref_array('comment_feed_groupby', array( $cgroupby, &$this ) );
				$corderby = apply_filters_ref_array('comment_feed_orderby', array( 'comment_date_gmt DESC', &$this ) );
				$climits = apply_filters_ref_array('comment_feed_limits', array( 'LIMIT ' . get_option('posts_per_rss'), &$this ) );
			}
			$cgroupby = ( ! empty( $cgroupby ) ) ? 'GROUP BY ' . $cgroupby : '';
			$corderby = ( ! empty( $corderby ) ) ? 'ORDER BY ' . $corderby : '';

			$this->comments = (array) $wpdb->get_results("SELECT $distinct $wpdb->comments.* FROM $wpdb->comments $cjoin $cwhere $cgroupby $corderby $climits");
			$this->comment_count = count($this->comments);

			$post_ids = array();

			foreach ( $this->comments as $comment )
				$post_ids[] = (int) $comment->comment_post_ID;

			$post_ids = join(',', $post_ids);
			$join = '';
			if ( $post_ids )
				$where = "AND $wpdb->posts.ID IN ($post_ids) ";
			else
				$where = "AND 0";
		}

		$pieces = array( 'where', 'groupby', 'join', 'orderby', 'distinct', 'fields', 'limits' );

		// Apply post-paging filters on where and join. Only plugins that
		// manipulate paging queries should use these hooks.
		if ( !$q['suppress_filters'] ) {
			$where		= apply_filters_ref_array( 'posts_where_paged',	array( $where, &$this ) );
			$groupby	= apply_filters_ref_array( 'posts_groupby',		array( $groupby, &$this ) );
			$join		= apply_filters_ref_array( 'posts_join_paged',	array( $join, &$this ) );
			$orderby	= apply_filters_ref_array( 'posts_orderby',		array( $orderby, &$this ) );
			$distinct	= apply_filters_ref_array( 'posts_distinct',	array( $distinct, &$this ) );
			$limits		= apply_filters_ref_array( 'post_limits',		array( $limits, &$this ) );
			$fields		= apply_filters_ref_array( 'posts_fields',		array( $fields, &$this ) );

			// Filter all clauses at once, for convenience
			$clauses = (array) apply_filters_ref_array( 'posts_clauses', array( compact( $pieces ), &$this ) );
			foreach ( $pieces as $piece )
				$$piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';
		}

		// Announce current selection parameters. For use by caching plugins.
		do_action( 'posts_selection', $where . $groupby . $orderby . $limits . $join );

		// Filter again for the benefit of caching plugins. Regular plugins should use the hooks above.
		if ( !$q['suppress_filters'] ) {
			$where		= apply_filters_ref_array( 'posts_where_request',		array( $where, &$this ) );
			$groupby	= apply_filters_ref_array( 'posts_groupby_request',		array( $groupby, &$this ) );
			$join		= apply_filters_ref_array( 'posts_join_request',		array( $join, &$this ) );
			$orderby	= apply_filters_ref_array( 'posts_orderby_request',		array( $orderby, &$this ) );
			$distinct	= apply_filters_ref_array( 'posts_distinct_request',	array( $distinct, &$this ) );
			$fields		= apply_filters_ref_array( 'posts_fields_request',		array( $fields, &$this ) );
			$limits		= apply_filters_ref_array( 'post_limits_request',		array( $limits, &$this ) );

			// Filter all clauses at once, for convenience
			$clauses = (array) apply_filters_ref_array( 'posts_clauses_request', array( compact( $pieces ), &$this ) );
			foreach ( $pieces as $piece )
				$$piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';
		}

		if ( ! empty($groupby) )
			$groupby = 'GROUP BY ' . $groupby;
		if ( !empty( $orderby ) )
			$orderby = 'ORDER BY ' . $orderby;

		$found_rows = '';
		if ( !$q['no_found_rows'] && !empty($limits) )
			$found_rows = 'SQL_CALC_FOUND_ROWS';

		$this->request = $old_request = "SELECT $found_rows $distinct $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits";
		
		if ( !$q['suppress_filters'] ) {
			$this->request = apply_filters_ref_array( 'posts_request', array( $this->request, &$this ) );
		}

		if ( 'ids' == $q['fields'] ) {
			$this->prepare_new_request(true);
			$this->posts = $wpdb->get_col( $this->request );
			$this->post_count = count( $this->posts );
			$this->set_found_posts( $q, $limits );

			return $this->posts;
		}

		if ( 'id=>parent' == $q['fields'] ) {
				
			$this->prepare_new_request(true);	
				
			/* //TESTING
			$this->request = str_replace('SELECT', 'SELECT wp_postworld_post_meta.* , ', $this->request);
			$this->request = str_replace('FROM wp_posts','FROM wp_posts left join  wp_postworld_post_meta on wp_posts.ID = wp_postworld_post_meta.post_id ', $this->request);
			$this->request = str_replace('WHERE', "WHERE post_class = 'blog' AND link_format='audio' and  ", $this->request);
			$strposOfOrderBy = strpos($this->request, "ORDER BY");
			$this->request =  substr($this->request ,0,$strposOfOrderBy);
			$this->request.="ORDER BY wp_posts.post_date DESC LIMIT 0,10";
			*/
			
			$this->posts = $wpdb->get_results( $this->request );
			$this->post_count = count( $this->posts );
			$this->set_found_posts( $q, $limits );

			$r = array();
			foreach ( $this->posts as $post )
				$r[ $post->ID ] = $post->post_parent;

			return $r;
		}

		$split_the_query = ( $old_request == $this->request && "$wpdb->posts.*" == $fields && !empty( $limits ) && $q['posts_per_page'] < 500 );
		$split_the_query = apply_filters( 'split_the_query', $split_the_query, $this );

		if ( $split_the_query ) {
			// First get the IDs and then fill in the objects

			$this->request = "SELECT $found_rows $distinct $wpdb->posts.ID FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits";

			$this->request = apply_filters( 'posts_request_ids', $this->request, $this );

			
			$this->prepare_new_request(true);
			
			$ids = $wpdb->get_col( $this->request );

			if ( $ids ) {
				$this->posts = $ids;
				$this->set_found_posts( $q, $limits );
				_prime_post_caches( $ids, $q['update_post_term_cache'], $q['update_post_meta_cache'] );
			} else {
				$this->posts = array();
			}
		} else {
			$this->prepare_new_request();
			$this->posts = $wpdb->get_results( $this->request );
			$this->set_found_posts( $q, $limits );
		}
		
		$fields = $this->prepare_fields();
		//log_me($this->request);
		// Convert to WP_Post objects
		if ( $this->posts )
			$this->posts = pw_get_posts($this->posts,$fields);
			//$this->posts = array_map( 'pw_get_post', $this->posts );

		// Raw results filter. Prior to status checks.
		if ( !$q['suppress_filters'] )
			$this->posts = apply_filters_ref_array('posts_results', array( $this->posts, &$this ) );

		if ( !empty($this->posts) && $this->is_comment_feed && $this->is_singular ) {
			$cjoin = apply_filters_ref_array('comment_feed_join', array( '', &$this ) );
			$cwhere = apply_filters_ref_array('comment_feed_where', array( "WHERE comment_post_ID = '{$this->posts[0]->ID}' AND comment_approved = '1'", &$this ) );
			$cgroupby = apply_filters_ref_array('comment_feed_groupby', array( '', &$this ) );
			$cgroupby = ( ! empty( $cgroupby ) ) ? 'GROUP BY ' . $cgroupby : '';
			$corderby = apply_filters_ref_array('comment_feed_orderby', array( 'comment_date_gmt DESC', &$this ) );
			$corderby = ( ! empty( $corderby ) ) ? 'ORDER BY ' . $corderby : '';
			$climits = apply_filters_ref_array('comment_feed_limits', array( 'LIMIT ' . get_option('posts_per_rss'), &$this ) );
			$comments_request = "SELECT $wpdb->comments.* FROM $wpdb->comments $cjoin $cwhere $cgroupby $corderby $climits";
			$this->comments = $wpdb->get_results($comments_request);
			$this->comment_count = count($this->comments);
		}


		// Check post status to determine if post should be displayed.
		if ( !empty($this->posts) && ($this->is_single || $this->is_page) ) {
			$status = get_post_status($this->posts[0]);
			$post_status_obj = get_post_status_object($status);
			//$type = get_post_type($this->posts[0]);
			if ( !$post_status_obj->public ) {
				if ( ! is_user_logged_in() ) {
					// User must be logged in to view unpublished posts.
					$this->posts = array();
				} else {
					if  ( $post_status_obj->protected ) {
						// User must have edit permissions on the draft to preview.
						if ( ! current_user_can($edit_cap, $this->posts[0]->ID) ) {
							$this->posts = array();
						} else {
							$this->is_preview = true;
							if ( 'future' != $status )
								$this->posts[0]->post_date = current_time('mysql');
						}
					} elseif ( $post_status_obj->private ) {
						if ( ! current_user_can($read_cap, $this->posts[0]->ID) )
							$this->posts = array();
					} else {
						$this->posts = array();
					}
				}
			}

			if ( $this->is_preview && $this->posts && current_user_can( $edit_cap, $this->posts[0]->ID ) )
				$this->posts[0] = get_post( apply_filters_ref_array( 'the_preview', array( $this->posts[0], &$this ) ) );
		}

		// Put sticky posts at the top of the posts array
		$sticky_posts = get_option('sticky_posts');
		if ( $this->is_home && $page <= 1 && is_array($sticky_posts) && !empty($sticky_posts) && !$q['ignore_sticky_posts'] ) {
			$num_posts = count($this->posts);
			$sticky_offset = 0;
			// Loop over posts and relocate stickies to the front.
			for ( $i = 0; $i < $num_posts; $i++ ) {
				if ( in_array($this->posts[$i]->ID, $sticky_posts) ) {
					$sticky_post = $this->posts[$i];
					// Remove sticky from current position
					array_splice($this->posts, $i, 1);
					// Move to front, after other stickies
					array_splice($this->posts, $sticky_offset, 0, array($sticky_post));
					// Increment the sticky offset. The next sticky will be placed at this offset.
					$sticky_offset++;
					// Remove post from sticky posts array
					$offset = array_search($sticky_post->ID, $sticky_posts);
					unset( $sticky_posts[$offset] );
				}
			}

			// If any posts have been excluded specifically, Ignore those that are sticky.
			if ( !empty($sticky_posts) && !empty($q['post__not_in']) )
				$sticky_posts = array_diff($sticky_posts, $q['post__not_in']);

			// Fetch sticky posts that weren't in the query results
			if ( !empty($sticky_posts) ) {
				$stickies = get_posts( array(
					'post__in' => $sticky_posts,
					'post_type' => $post_type,
					'post_status' => 'publish',
					'nopaging' => true
				) );

				foreach ( $stickies as $sticky_post ) {
					array_splice( $this->posts, $sticky_offset, 0, array( $sticky_post ) );
					$sticky_offset++;
				}
			}
		}

		if ( !$q['suppress_filters'] )
			$this->posts = apply_filters_ref_array('the_posts', array( $this->posts, &$this ) );

		// Ensure that any posts added/modified via one of the filters above are
		// of the type WP_Post and are filtered.
		if ( $this->posts ) {
			$this->post_count = count( $this->posts );
			$fields = $this->prepare_fields();
			$this->posts = pw_get_posts($this->posts,$fields);
			//$this->posts = array_map( 'pw_get_post', $this->posts );
			
			if ( $q['cache_results'] )
				update_post_caches($this->posts, $post_type, $q['update_post_term_cache'], $q['update_post_meta_cache']);

			$this->post = reset( $this->posts );
		} else {
			$this->post_count = 0;
			$this->posts = array();
		}

		return $this->posts;
	}
}



/***********************************************************************************/


class PW_User_Query extends WP_User_Query {
	function prepare_fields(){
		
		$fields = $this->query_vars['fields'];
		if($fields == null || $fields=='' ) 
			$fields='all';
		//else if($fields!='preview' && $fields!='ids' && gettype($fields)!='array')
		//	$fields='all';
		
		return $fields;
	}
	
	/**
	 * @todo Look at posts_search_orderby filter in WP_Query //**
	 */
	function prepare_order_by($orderBy_Query){
		
		$orderby = $this->query_vars['orderby'];
		 
		global $wpdb;
		if($orderby!=null && $orderby!=''){
			
			$order_by_string = '';
	
			if( $orderby=='comment_points' &&
				pw_config_in_db_tables('user_meta') ){
				$order_by_string.= "$wpdb->postworld_prefix"."user_meta.comment_points"; //**
			}
			
			if ($orderby=='post_points' &&
				pw_config_in_db_tables('user_meta') ){	
				$order_by_string.= "$wpdb->postworld_prefix"."user_meta.post_points"; //**
			}
			
			if ($orderby=='display_name'){
				$order_by_string.= "wp_users.display_name";
			}
			
			if($order_by_string===''){ return $orderBy_Query;}
			else return "order by $order_by_string ".$this->query_vars['order'];
			
				
	}
}
	
	function prepare_where_query($where_query){
		
		$where =" WHERE ";	
		$insertAnd= FALSE;
		//echo($insertAnd);
		
		global $wpdb;
		
		
		if($this->query_vars['location']){
				$location = explode(",", $this->query_vars['location']);
				$where .=" $wpdb->postworld_prefix"."user_meta.location_city='".$location[0]."' and "; //**
				
				$where .=" $wpdb->postworld_prefix"."user_meta.location_city='".$location[1]."' and "; //**
				
				$where .=" $wpdb->postworld_prefix"."user_meta.location_city='".$location[2]."' "; //**
		}else{
	
		
			if($this->query_vars['location_country']){
				$insertAnd =TRUE;
				$where .=" $wpdb->postworld_prefix"."user_meta.location_country='".$this->query_vars["location_country"]."' "; //**
			}
			
			if($this->query_vars['location_region']){
					if($insertAnd ===TRUE) $where.=" and ";
					$where .=" $wpdb->postworld_prefix"."user_meta.location_region='".$this->query_vars["location_region"]."' "; //**
			}
			
			if($this->query_vars['location_city']){
					if($insertAnd ===TRUE) $where.=" and ";
					$where .=" $wpdb->postworld_prefix"."user_meta.location_city='".$this->query_vars["location_city"]."' "; //**
			}
		
		}
		if($where ==" WHERE ") return $where;	
		return $where."  and ";
	}
	
	
	function prepare_new_request($remove_tbl=false){
		global $wpdb;
		$this->query_orderby = $this->prepare_order_by($this->query_orderby);
		$this->query_where = str_replace('WHERE', $this->prepare_where_query(), $this->query_where);
		$this->query_from = str_replace('FROM '.$wpdb->prefix.'users','FROM '.$wpdb->prefix.'users left join  '.$wpdb->postworld_prefix.'user_meta on '.$wpdb->prefix.'users.ID = '.$wpdb->postworld_prefix.'user_meta.user_id ', $this->query_from);
		
		if($remove_tbl===false )
		$this->query_fields = str_replace('SELECT', 'SELECT '.$wpdb->postworld_prefix.'user_meta.* , ', $this->query_fields);
			
		//$this->request = str_replace('FROM wp_posts','FROM wp_posts left join  wp_postworld_post_meta on wp_posts.ID = wp_postworld_post_meta.post_id ', $this->request);
		//$this->request = str_replace('WHERE', $where, $this->request);
		//$strposOfOrderBy = strpos($this->request, "ORDER BY");
		//$this->request =  substr($this->request ,0,$strposOfOrderBy);
		//$this->request.=$orderBy;
		
	}
	/*
	function prepare_query() {
		global $wpdb;

		$qv =& $this->query_vars;

		if ( is_array( $qv['fields'] ) ) {
			$qv['fields'] = array_unique( $qv['fields'] );

			$this->query_fields = array();
			foreach ( $qv['fields'] as $field ) {
				$field = 'ID' === $field ? 'ID' : sanitize_key( $field );
				$this->query_fields[] = "$wpdb->users.$field";
			}
			$this->query_fields = implode( ',', $this->query_fields );
		} elseif ( 'all' == $qv['fields'] ) {
			$this->query_fields = "$wpdb->users.*";
		} else {
			$this->query_fields = "$wpdb->users.ID";
		}

		if ( $qv['count_total'] )
			$this->query_fields = 'SQL_CALC_FOUND_ROWS ' . $this->query_fields;

		$this->query_from = "FROM $wpdb->users";
		$this->query_where = "WHERE 1=1";

		// sorting
		if ( in_array( $qv['orderby'], array('nicename', 'email', 'url', 'registered') ) ) {
			$orderby = 'user_' . $qv['orderby'];
		} elseif ( in_array( $qv['orderby'], array('user_nicename', 'user_email', 'user_url', 'user_registered') ) ) {
			$orderby = $qv['orderby'];
		} elseif ( 'name' == $qv['orderby'] || 'display_name' == $qv['orderby'] ) {
			$orderby = 'display_name';
		} elseif ( 'post_count' == $qv['orderby'] ) {
			// todo: avoid the JOIN
			$where = get_posts_by_author_sql('post');
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT post_author, COUNT(*) as post_count
				FROM $wpdb->posts
				$where
				GROUP BY post_author
			) p ON ({$wpdb->users}.ID = p.post_author)
			";
			$orderby = 'post_count';
		} elseif ( 'ID' == $qv['orderby'] || 'id' == $qv['orderby'] ) {
			$orderby = 'ID';
		} else {
			$orderby = 'user_login';
		}

		$qv['order'] = strtoupper( $qv['order'] );
		if ( 'ASC' == $qv['order'] )
			$order = 'ASC';
		else
			$order = 'DESC';
		$this->query_orderby = "ORDER BY $orderby $order";

		// limit
		if ( $qv['number'] ) {
			if ( $qv['offset'] )
				$this->query_limit = $wpdb->prepare("LIMIT %d, %d", $qv['offset'], $qv['number']);
			else
				$this->query_limit = $wpdb->prepare("LIMIT %d", $qv['number']);
		}

		$search = trim( $qv['search'] );
		if ( $search ) {
			$leading_wild = ( ltrim($search, '*') != $search );
			$trailing_wild = ( rtrim($search, '*') != $search );
			if ( $leading_wild && $trailing_wild )
				$wild = 'both';
			elseif ( $leading_wild )
				$wild = 'leading';
			elseif ( $trailing_wild )
				$wild = 'trailing';
			else
				$wild = false;
			if ( $wild )
				$search = trim($search, '*');

			$search_columns = array();
			if ( $qv['search_columns'] )
				$search_columns = array_intersect( $qv['search_columns'], array( 'ID', 'user_login', 'user_email', 'user_url', 'user_nicename' ) );
			if ( ! $search_columns ) {
				if ( false !== strpos( $search, '@') )
					$search_columns = array('user_email');
				elseif ( is_numeric($search) )
					$search_columns = array('user_login', 'ID');
				elseif ( preg_match('|^https?://|', $search) && ! ( is_multisite() && wp_is_large_network( 'users' ) ) )
					$search_columns = array('user_url');
				else
					$search_columns = array('user_login', 'user_nicename');
			}

			$search_columns = apply_filters( 'user_search_columns', $search_columns, $search, $this );

			$this->query_where .= $this->get_search_sql( $search, $search_columns, $wild );
		}

		$blog_id = absint( $qv['blog_id'] );

		if ( 'authors' == $qv['who'] && $blog_id ) {
			$qv['meta_key'] = $wpdb->get_blog_prefix( $blog_id ) . 'user_level';
			$qv['meta_value'] = 0;
			$qv['meta_compare'] = '!=';
			$qv['blog_id'] = $blog_id = 0; // Prevent extra meta query
		}

		$role = trim( $qv['role'] );

		if ( $blog_id && ( $role || is_multisite() ) ) {
			$cap_meta_query = array();
			$cap_meta_query['key'] = $wpdb->get_blog_prefix( $blog_id ) . 'capabilities';

			if ( $role ) {
				$cap_meta_query['value'] = '"' . $role . '"';
				$cap_meta_query['compare'] = 'like';
			}

			$qv['meta_query'][] = $cap_meta_query;
		}

		$meta_query = new WP_Meta_Query();
		$meta_query->parse_query_vars( $qv );

		if ( !empty( $meta_query->queries ) ) {
			$clauses = $meta_query->get_sql( 'user', $wpdb->users, 'ID', $this );
			$this->query_from .= $clauses['join'];
			$this->query_where .= $clauses['where'];

			if ( 'OR' == $meta_query->relation )
				$this->query_fields = 'DISTINCT ' . $this->query_fields;
		}

		if ( !empty( $qv['include'] ) ) {
			$ids = implode( ',', wp_parse_id_list( $qv['include'] ) );
			$this->query_where .= " AND $wpdb->users.ID IN ($ids)";
		} elseif ( !empty($qv['exclude']) ) {
			$ids = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
			$this->query_where .= " AND $wpdb->users.ID NOT IN ($ids)";
		}

		do_action_ref_array( 'pre_user_query', array( &$this ) );
	}
	*/
	

	function query() {
		global $wpdb;

		$qv =& $this->query_vars;
		
		
		if ( is_array( $qv['fields'] ) || 'all' == $qv['fields'] ) {
			$this->prepare_new_request(false);	
			$query  = "SELECT $this->query_fields $this->query_from $this->query_where $this->query_orderby $this->query_limit";
			//echo ("<br>".$query."<br><br>");
			$this->results = $wpdb->get_results($query);
		} else {
			$this->prepare_new_request(true);	
			$query ="SELECT $this->query_fields $this->query_from $this->query_where $this->query_orderby $this->query_limit"; 
			//echo ("<br>".$query."<br>");
			$this->results = $wpdb->get_col($query);
		}

		if ( $qv['count_total'] )
			$this->total_users = $wpdb->get_var( apply_filters( 'found_users_query', 'SELECT FOUND_ROWS()' ) );

		if ( !$this->results )
			return;

		if ( 'all_with_meta' == $qv['fields'] ) {
			cache_users( $this->results );

			$r = array();
			foreach ( $this->results as $userid )
				$r[ $userid ] = new WP_User( $userid, '', $qv['blog_id'] );

			$this->results = $r;
		} elseif ( 'all' == $qv['fields'] ) {
			foreach ( $this->results as $key => $user ) {
				$this->results[ $key ] = pw_get_userdata($user->ID,'all');//new WP_User( $user );
			}
		}
	}
}


function pw_user_query( $args, $args,$return_Type = 'PW_User_Query' ){
		/*
		 
		  Description:
	
			Similar to WP_User_Query, queries users in wp_users table
			Extends Query fields to Postworld user_meta fields
			Parameters:
			
			$args : Array
			
			role : string
			
			Use 'User Role'
			s : string
			
			Query : table : wp_users, columns: user_login, user_nicename, user_email, user_url, display_name
			location_country : string
			
			Query wp_postworld_user_meta table column location_country
			location_region : string
			
			Query wp_postworld_user_meta table column location_region
			location_city : string
			
			Query wp_postworld_user_meta table column location_city
			location : string
			
			Query location_country , location_city , and location_region
			orderby : string
			
			Options:
			post_points - Points to the user's posts
			comment_points - Points to user's comments
			display_name - Use Display Name, alphabetical
			username - Use Nice Name, alphabetical
			date - Date joined
			order : string
			
			Options :
			ASC (default)
			DESC
			fields : Array
			
			Options :
			All (default)
			Any fields from get_userdata() Method : http://codex.wordpress.org/Function_Reference/get_userdata
			Any fields from pw_get_userdata() Method
			$return_format : string
			
			Options:
			ARRAY_A (default)
			JSON
			Usage:
			
			$args = array(
			     'location_country' => {{search_terms}}
			     'location_region' => {{search_terms}}
			     'location_city' => {{search_terms}}
			     'location' => {{search_terms}}
			     'role' => {{string}}
			     's' => {{search_terms}}
			     'orderby' => {{string}}
			     'order' => {{string}}
			     'fields' => array(ids) // default ids only // use pw_get_userdata() method
			);
			$users = pw_user_query( $args, 'JSON' );
			return : ARRAY_A / JSON (Requested Fields)
	
			 
		 */
		 
		$the_query = new PW_User_Query($args);
		if($return_Type == 'ARRAY_A'){
			return (array) $the_query;
		}
		else if($return_Type == 'JSON'){
			return json_encode($the_query);
		}
		else
		return $the_query;
	
}

class PW_COMMENTS extends WP_Comment_Query {
	

	function query( $query_vars ) {
		global $wpdb;

		$defaults = array(
			'author_email' => '',
			'ID' => '',
			'karma' => '',
			'number' => '',
			'offset' => '',
			'orderby' => '',
			'order' => 'DESC',
			'parent' => '',
			'post_ID' => '',
			'post_id' => 0,
			'post_author' => '',
			'post_name' => '',
			'post_parent' => '',
			'post_status' => '',
			'post_type' => '',
			'status' => '',
			'type' => '',
			'user_id' => '',
			'search' => '',
			'count' => false,
			'meta_key' => '',
			'meta_value' => '',
			'meta_query' => '',
		);

		$groupby = '';

		$this->query_vars = wp_parse_args( $query_vars, $defaults );

		// Parse meta query
		$this->meta_query = new WP_Meta_Query();
		$this->meta_query->parse_query_vars( $this->query_vars );

		do_action_ref_array( 'pre_get_comments', array( &$this ) );
		extract( $this->query_vars, EXTR_SKIP );

		// $args can be whatever, only use the args defined in defaults to compute the key
		$key = md5( serialize( compact(array_keys($defaults)) )  );
		$last_changed = wp_cache_get( 'last_changed', 'comment' );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, 'comment' );
		}
		$cache_key = "get_comments:$key:$last_changed";

		if ( $cache = wp_cache_get( $cache_key, 'comment' ) )
			return $cache;

		$post_id = absint($post_id);

		if ( 'hold' == $status )
			$approved = "comment_approved = '0'";
		elseif ( 'approve' == $status )
			$approved = "comment_approved = '1'";
		elseif ( ! empty( $status ) && 'all' != $status )
			$approved = $wpdb->prepare( "comment_approved = %s", $status );
		else
			$approved = "( comment_approved = '0' OR comment_approved = '1' )";

		$order = ( 'ASC' == strtoupper($order) ) ? 'ASC' : 'DESC';

		if ( ! empty( $orderby ) ) {
			$ordersby = is_array($orderby) ? $orderby : preg_split('/[,\s]/', $orderby);
			$allowed_keys = array(
				'comment_agent',
				'comment_approved',
				'comment_author',
				'comment_author_email',
				'comment_author_IP',
				'comment_author_url',
				'comment_content',
				'comment_date',
				'comment_date_gmt',
				'comment_ID',
				'comment_karma',
				'comment_parent',
				'comment_post_ID',
				'comment_type',
				'user_id',
				'comment_points'
			);
			if ( ! empty( $this->query_vars['meta_key'] ) ) {
				$allowed_keys[] = $this->query_vars['meta_key'];
				$allowed_keys[] = 'meta_value';
				$allowed_keys[] = 'meta_value_num';
			}
			$ordersby = array_intersect( $ordersby, $allowed_keys );
			foreach ( $ordersby as $key => $value ) {
				if ( $value == $this->query_vars['meta_key'] || $value == 'meta_value' ) {
					$ordersby[ $key ] = "$wpdb->commentmeta.meta_value";
				} elseif ( $value == 'meta_value_num' ) {
					$ordersby[ $key ] = "$wpdb->commentmeta.meta_value+0";
				}
			}
			$orderby = empty( $ordersby ) ? 'comment_date_gmt' : implode(', ', $ordersby);
		} else {
			$orderby = 'comment_date_gmt';
		}

		$number = absint($number);
		$offset = absint($offset);

		if ( !empty($number) ) {
			if ( $offset )
				$limits = 'LIMIT ' . $offset . ',' . $number;
			else
				$limits = 'LIMIT ' . $number;
		} else {
			$limits = '';
		}

		if ( $count )
			$fields = 'COUNT(*)';
		else
			$fields = '*';

		$join = '';
		$where = $approved;

		if ( ! empty($post_id) )
			$where .= $wpdb->prepare( ' AND comment_post_ID = %d', $post_id );
		if ( '' !== $author_email )
			$where .= $wpdb->prepare( ' AND comment_author_email = %s', $author_email );
		if ( '' !== $karma )
			$where .= $wpdb->prepare( ' AND comment_karma = %d', $karma );
		if ( 'comment' == $type ) {
			$where .= " AND comment_type = ''";
		} elseif( 'pings' == $type ) {
			$where .= ' AND comment_type IN ("pingback", "trackback")';
		} elseif ( ! empty( $type ) ) {
			$where .= $wpdb->prepare( ' AND comment_type = %s', $type );
		}
		if ( '' !== $parent )
			$where .= $wpdb->prepare( ' AND comment_parent = %d', $parent );
		if ( '' !== $user_id )
			$where .= $wpdb->prepare( ' AND user_id = %d', $user_id );
		if ( '' !== $search )
			$where .= $this->get_search_sql( $search, array( 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_author_IP', 'comment_content' ) );

		$post_fields = array_filter( compact( array( 'post_author', 'post_name', 'post_parent', 'post_status', 'post_type', ) ) );
		if ( ! empty( $post_fields ) ) {
			$join = "JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID";
			foreach( $post_fields as $field_name => $field_value )
				$where .= $wpdb->prepare( " AND {$wpdb->posts}.{$field_name} = %s", $field_value );
		}

		if ( ! empty( $this->meta_query->queries ) ) {
			$clauses = $this->meta_query->get_sql( 'comment', $wpdb->comments, 'comment_ID', $this );
			$join .= $clauses['join'];
			$where .= $clauses['where'];
			$groupby = "{$wpdb->comments}.comment_ID";
		}

		$pieces = array( 'fields', 'join', 'where', 'orderby', 'order', 'limits', 'groupby' );
		$clauses = apply_filters_ref_array( 'comments_clauses', array( compact( $pieces ), &$this ) );
		foreach ( $pieces as $piece )
			$$piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';

		if ( $groupby )
			$groupby = 'GROUP BY ' . $groupby;

		
		if( pw_config_in_db_tables('comment_meta') ){
			$join .= " left join  $wpdb->postworld_prefix"."comment_meta on ".$wpdb->prefix."comments.comment_ID = $wpdb->postworld_prefix"."comment_meta.comment_id "; //**
		}

		$query = "SELECT $fields FROM $wpdb->comments $join WHERE $where $groupby ORDER BY $orderby $order $limits";
		//print_r($query);
		if ( $count )
			return $wpdb->get_var( $query );

		$comments = $wpdb->get_results( $query );
		//echo json_encode($comments);
		$comments = apply_filters_ref_array( 'the_comments', array( $comments, &$this ) );

		wp_cache_add( $cache_key, $comments, 'comment' );

		return $comments;
	}	
	
}

function pw_new_get_comments($args=''){
	$query = new PW_COMMENTS;
	return $query->query( $args );
}




