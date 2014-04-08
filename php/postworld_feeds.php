<?php

function pw_live_feed ( $args ){
	/*
	
	Description:

	Used for custom search querying, etc.
	Does not access wp_postworld_feeds caches at all
	Helper function for the pw_live_feed() JS method
	Parameters: $args
	
	feed_id : string
	
	preload : integer => Number of posts to fetch data and return as post_data
	feed_query : Array
	pw_query() Query Variables
	
	 
	Process:
	
	Generate return feed_outline , with pw_feed_outline( $args[feed_query] ) method
	Generate return post data by running the defined preload number of the first posts through pw_get_posts( feed_outline, $args['feed_query']['fields'] )
	Usage:
	
	$args = array (
	     'feed_id' => {{string}},
	     'preload'  => {{integer}}
	     'feed_query' => array(
	          // pw_query args    
	     )
	)
	$live_feed = pw_live_feed ( *$args* );
	return : Object
	
	array(
	    'feed_id' => {{string}},
	    'feed_outline' => '12,356,3564,2362,236',
	    'loaded' => '12,356,3564',
	    'preload' => {{integer}},
	    'post_data' => array(), // Output from pw_get_posts() based on feed_query
	)
	 *  
	 
	• Helper function for the pw_live_feed() JS method
	• Used for custom search querying, etc.
	• Does not access wp_postworld_feeds caches at all

	INPUT :
	$args = array (
		'feed_id'		=> string,
		'preload'		=> integer,
		'feed_query'	=> array( pw_query )
	)
	*/

	extract($args);

	// Get the Feed Outline
	$feed_query = $args["feed_query"];
	$feed_outline = pw_feed_outline( $feed_query );
	
	// Select which posts to preload
	$preload_posts = array_slice( $feed_outline, 0, $preload ); // to get top post ids
	
	// Preload selected posts
	$post_data = pw_get_posts($preload_posts, $feed_query["fields"] );
	
	return (array("feed_id"=>$args["feed_id"], "feed_outline"=>$feed_outline, "loaded"=>$preload_posts,"preload"=>count($post_data),"post_data"=>$post_data ));
	
}



function pw_feed_outline ( $pw_query_args ){
	// • Uses pw_query() method to generate an array of post_ids based on the $pw_query_args
	
	$pw_query_args["fields"] = "ids";
	//echo "<br><br>";
	//print_r($pw_query_args);
	//echo json_encode($pw_query_args);
	//echo "<br><br>";
	$post_array = pw_query($pw_query_args); // <<< TODO : Flatten from returned Object to Array of IDs
	//echo "<br><br>";
	//print_r($post_array);
	$post_ids  = $post_array->posts;
	
	//print_r($post_ids);
	return $post_ids; // Array of post IDs
}


/*
1-pw_get_templates
2-load_feed
3-pw_cache_feed
4-pw_register_feed
5-pw_get_feed
*/

function add_new_feed($feed_id,$feed_query){
	global $wpdb;
	$wpdb->show_errors(); 
	$query = "insert into $wpdb->pw_prefix"."feeds values('$feed_id','".json_encode($feed_query)."',null,null,null,null)";
	//echo $query;
	$wpdb->query($query);
}
function pw_register_feed ( $args ){
	/*
		Description:
		
		Registers the feed in feeds table
		Process:
		
		If the feed_id doesn't appear in the wp_postworld_feeds table :
		
		Create a new row
		Enable write_cache
		Store $args['feed_query'] in the feed_query column in Postworld feeds table as a JSON Object
		
		If write_cache is true, run pw_cache_feed(feed_id)
		
		return : $args Array
		
		Parameters : $args
		
		feed_id : string
		
		feed_query : array
		
		default : none
		The query object which is stored in feed_query in feeds table, which is input directly into pw_query
		write_cache : boolean
		
		If the feed_id is new to the feeds table, set write_cache = true
		false (default) - Wait for cron job to update feed outline later, just update feed_query
		true - Cache the feed with method : run pw_cache_feed( $feed_id )
		Usage :
		
		$args = array (
		    'feed_id' => 'front_page_feed',
		    'write_cache'  => true,
		    'feed_query' => array(
		        // pw_query() $args    
		    )
		);
		pw_register_feed ($args);
	 
	 */
	global $wpdb;
	$wpdb->show_errors(); 
	
	
	 if($args['feed_id']){
	 	$feed_row = pw_get_feed($args['feed_id']);
		// echo json_encode($feed_row);
		if(!$feed_row){
			add_new_feed($args['feed_id'],$args['feed_query']);
				$args['write_cache'] =  TRUE;
				pw_cache_feed($args['feed_id']);
			
		}else{
			// echo ($args['write_cache']);
			//update feed query
			update_feed_query($args['feed_id'], $args['feed_query']);
			if (array_key_exists('write_cache', $args)){
				if( $args['write_cache'] ===  TRUE){
					pw_cache_feed($args['feed_id']);
				}
			}
		}
	 }
	return $args;
}

function update_feed_query($feed_id, $feed_query){
		
	global $wpdb;	
	$wpdb->show_errors(); 
	$query = "update $wpdb->pw_prefix"."feeds set feed_query='".json_encode($feed_query)."' where feed_id='".$feed_id."'";
	//echo $query;
	$wpdb->query($query);
}

function pw_cache_feed ( $feed_id ){
	
	$feed_row = pw_get_feed($feed_id);
	if(!is_null($feed_row)){
			
		//echo ($feed_row->feed_query);
		$time_start = date("Y-m-d H:i:s");
		
		$feed_query_finalized  = finalize_feed_query($feed_row->feed_query);
		
		$feed_outline = pw_feed_outline($feed_query_finalized);
		$time_end = date("Y-m-d H:i:s");
		$timer = (strtotime( $time_end )-strtotime( $time_start))*1000;
		//echo json_encode($feed_outline);
		global $wpdb;
		$wpdb->show_errors(); 
		$query = "update $wpdb->pw_prefix"."feeds set feed_outline='".implode(",", $feed_outline)."',time_start='$time_start',time_end='$time_end',timer='$timer' where feed_id='".$feed_id."'";
		//echo $query;
		$wpdb->query($query);
		return array('number_of_posts'=>count($feed_outline), 'feed_query'=> $feed_row->feed_query);
	} 
}

function finalize_feed_query($feed_query_stringified){
	$pw_query_args = (array)json_decode($feed_query_stringified);
	if(isset($pw_query_args["tax_query"]))
		$pw_query_args["tax_query"][0]= get_object_vars(($pw_query_args["tax_query"][0])) ;
	
	return $pw_query_args;
}

function pw_get_feed ( $feed_id ){
	global $wpdb;
	$wpdb->show_errors(); 
	
	$query = "select * from $wpdb->pw_prefix"."feeds where feed_id='".$feed_id."'";
	$feed_row = $wpdb->get_row($query);
	
	return $feed_row;
	
}
  
function pw_load_feed ( $feed_id, $preload=0, $fields=null ){
	/*
	 Parameters:

		$feed_id : string
		
		$preload : integer (optional)('0' default)
		
		The number of posts to pre-load with post_data
		Process:
		
		Return an object containing all the columns from the Feeds table
		If $preload (integer) is provided, then use pw_get_posts() on that number of the first posts in the feed_outline , return in post_data Object
		Use fields value from feed_query column under key fields
		return : Array
		
		array(
		    'feed_id' => {{string}},
		    'feed_query' => {{array}},
		    'time_start' => {{integer/timestamp}},
		    'time_end' => {{integer/timestamp}},
		    'timer' => {{milliseconds}},
		    'feed_outline' => {{array (of post IDs)}},
		    'post_data' => {{array (of post data)}}
		)
	 */
	
	$feed_row = (array) pw_get_feed($feed_id);
	if($feed_row){
		$feed_row['feed_outline'] = array_map("intval", explode(",", $feed_row['feed_outline']));
		
		if($preload > 0){
			// Get the top preload post IDs
			$preload_posts = array_slice( $feed_row['feed_outline'], 0, $preload ); 
			
			if( $fields == null ){
				// Get the default fields
				$feed_query = (array)json_decode($feed_row['feed_query']);
				$fields = $feed_query['fields'];
			}

			$feed_row['post_data'] = pw_get_posts($preload_posts,$fields);
		}

	}

	return (array)$feed_row;
	
}



function pw_print_feed( $args ){

	// Load a cached feed
	if( isset($args['feed_id']) ){
		// LOAD A CACHED FEED
		// Run Postworld Load Feed
		$load_feed = pw_load_feed( $args['feed_id'], $args['posts'], $args['fields'] );
		$posts = $load_feed['post_data'];

	} else if( isset($args['feed_query']) ) {
		
		// LOAD A FRESH QUERY
		$feed_query = $args['feed_query'];
		
		if( isset($args['fields']) )
			// Override fields
			$feed_query['fields'] = $args['fields'];

		$pw_query = pw_query( $feed_query );
		$posts = $pw_query->posts;

	} else {
		// RETURN ERROR
		return array('error' => 'No feed_id or feed_query defined.');
	}

	
	// Include h2o template engine
	global $pw_globals;
	require_once $pw_globals['paths']['postworld_dir'].'/lib/h2o/h2o.php';

	$pw_post = array();
	$post_html = "";

	// Iterate through each provited post
	foreach( $posts as $post_data ){

		// ID is a required field, to determine the post template
		$post_id = $post_data['ID'];

		// Get the template for this post
		$template_path = pw_get_post_template( $post_id, $args['view'], 'dir', true );
		
		// Initialize h2o template engine
		$h2o = new h2o($template_path);

		// Seed the post data with 'post' for use in template, ie. {{post.post_title}}
		$pw_post['post'] = $post_data;

		// Add rendered HTML to the return data
		$post_html .= $h2o->render($pw_post);
	}

	return $post_html;
}


function get_panel_ids(){
	global $pwSiteGlobals;
	$override_file_names =	list_dir_file_names( $pwSiteGlobals['template_paths']['panels']['dir']['override'] ); //['override_panel_template_abs_path']);
	$default_file_names = 	list_dir_file_names( $pwSiteGlobals['template_paths']['panels']['dir']['default'] ); //['default_panel_template_abs_path'] );

	$final_panel_names = array();
	for ($i=0; $i <count($default_file_names) ; $i++) { 
		$final_panel_names[] = str_replace(".html", "", $default_file_names[$i]);
	}
	
	for ($i=0; $i < count($override_file_names); $i++) {
		$name = str_replace(".html", "", $override_file_names[$i] );
		if(!in_array($name,$final_panel_names)){
			$final_panel_names[] = $name;
		}
	}
	
	return $final_panel_names;
}



function get_comment_ids(){
	global $pwSiteGlobals;
	$override_file_names =	list_dir_file_names( $pwSiteGlobals['template_paths']['comments']['dir']['override'] ); //['override_comment_template_abs_path']);
	$default_file_names =	list_dir_file_names( $pwSiteGlobals['template_paths']['comments']['dir']['default'] );//['default_comment_template_abs_path']);
	
	
	$final_comment_names = array();
	for ($i=0; $i <count($default_file_names) ; $i++) { 
		$final_comment_names[] = str_replace(".html", "", $default_file_names[$i]);
	}
	
	for ($i=0; $i < count($override_file_names); $i++) {
		$name = str_replace(".html", "", $override_file_names[$i] );
		if(!in_array($name,$final_comment_names)){
			$final_comment_names[] = $name;
		}
	}
	
	return $final_comment_names;
}


function list_dir_file_names($directory){
		
	$names_array=array();
	//echo("<br>".$directory."<br>");
	if (is_dir($directory)){
		//echo 'is directoruuu';
	
	$dir = new RecursiveDirectoryIterator($directory,
			    FilesystemIterator::SKIP_DOTS);
			
			// Flatten the recursive iterator, folders come before their files
			$it  = new RecursiveIteratorIterator($dir,
			    RecursiveIteratorIterator::SELF_FIRST);
			
			// Maximum depth is 1 level deeper than the base folder
			$it->setMaxDepth(1);
			
			
			// Basic loop displaying different messages based on file or folder
			foreach ($it as $fileinfo) {
			    if ($fileinfo->isFile()) {
			    	//echo $fileinfo->getFilename();
			        //$names_array[]= $url_path.($fileinfo->getFilename());
					$names_array[]= $fileinfo->getFilename();
			    }
			}
	}
			
	return $names_array;
}


////////// DISCONTINUED //////////
function pw_get_templates_old_and_obsolete_function_delete_me ( $templates_object = null, $path_type='url' ){
	/*
	$templates_object : Array (optional)

		Options:
		Array containing ['posts'] : indicates to return a Post Templates Object
		
		post_types : Array (optional) - Array of post_types which to return template paths for
		default : Get all registered post types with get_post_types() WP Method :
		get_post_types( array( array( 'public' => true, '_builtin' => false ) ), 'names' )
		post_views : Array (optional) - Array of 'feed views' which to retrieve templates for
		default : array( 'list', 'detail', 'grid', 'full' )
		Array containing ['panels'] : indicates to return a Panel Templates Object
		
		panel_id : Return the url for the given panel_id
		null : default
		Returns object with all panels and templates in the default and over-ride folders

	$path : string (optional)
		Options:
		  'url' (default): Returns absolute URL string of template file
		  'dir' (default): Returns absolute directory path of template file

			 * */

	global $pwSiteGlobals;
	$template_paths = $pwSiteGlobals['template_paths'];
	
	$output = array();

	if(!$templates_object){
		/*null : default
			Returns object with all panels and templates in the default and over-ride folders.
		 */
		$pt_args = array(
			 'public'   => true,
			// '_builtin' => false
		);
		$pt_output = 'names'; // names or objects, note names is the default
		$pt_operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types( $pt_args, $pt_output, $pt_operator );

		$post_types_final = array();

		foreach ( $post_types as $post_type ) {
				$post_types_final[] = $post_type ;
		}

		$post_views = $pwSiteGlobals['post_views'];

		$templates_object['posts']=array();
		$templates_object['posts']['post_types']=$post_types_final;
		$templates_object['posts']['post_views']=$post_views; 
		
		//$panel_ids = $pwSiteGlobals['panel_ids']; //array('feed_top','feed_search');
		
		$templates_object['panels']= get_panel_ids();
		$templates_object['comments']= get_comment_ids();
		
		//print_r($templates_object);
	}
	
	///// HANDLE POSTS /////
	// File naming convention : "post_type-post_view.html"

	if(array_key_exists('posts', $templates_object)){
	//if($templates_object['posts']){
		
		 $output['posts'] = array(); 
		 for ($i=0; $i < count($templates_object['posts']['post_types']) ; $i++) {
		 	 $output['posts'][$templates_object['posts']['post_types'][$i] ]=array();
			
			 for ($j=0; $j < count($templates_object['posts']['post_views']) ; $j++) {
				 $template_name = $templates_object['posts']['post_types'][$i] ."-". $templates_object['posts']['post_views'][$j].".html";
			 	 
			 	 if(file_exists($template_paths['posts']['dir']['override'].$template_name)){
			 	 	$output['posts'][$templates_object['posts']['post_types'][$i] ][$templates_object['posts']['post_views'][$j]] = $template_paths['posts'][$path_type]['override'].$template_name;
			 	 }
			 	 
			 	 else{
			 	 	$fall_back_template_name ="post-".$templates_object['posts']['post_views'][$j].".html";
			 	 	
			 	 	if(file_exists($template_paths['posts']['dir']['override'].$fall_back_template_name))
			 	 		$output['posts'][ $templates_object['posts']['post_types'][$i] ][$templates_object['posts']['post_views'][$j]] = $template_paths['posts'][$path_type]['override'].$fall_back_template_name;
					
					else{
						$fall_back_template_default_path = $template_paths['posts'][$path_type]['default']."post-".$templates_object['posts']['post_views'][$j].".html";
						$output['posts'][$templates_object['posts']['post_types'][$i] ][$templates_object['posts']['post_views'][$j]] = $fall_back_template_default_path;
					}

			 	 }
			 }//for
		 } //for

		 
	}

	///// HANDLE ALL OTHER FORMATS /////

	/* array( 'panels'=>'panel_id' )*/
	//if($templates_object['panels' ]) {
	if(array_key_exists('panels', $templates_object)){
		$output['panels']=array();

		for ($i=0; $i < count($templates_object['panels']) ; $i++) {
			if(file_exists( $template_paths['panels']['dir']['override'].$templates_object['panels'][$i].".html")){
				$output['panels'][$templates_object['panels'][$i]] = $template_paths['panels'][$path_type]['override'].$templates_object['panels'][$i].".html";
			}
			else {
				$output['panels'][$templates_object['panels'][$i]] = $template_paths['panels'][$path_type]['default'].$templates_object['panels'][$i].".html";
			}
		}
	}
	
	//echo 'gfde';
	if(array_key_exists('comments', $templates_object)){
		$output['comments']=array();

		for ($i=0; $i < count($templates_object['comments']) ; $i++) {
			if(file_exists( $template_paths['comments']['dir']['override'].$templates_object['comments'][$i].".html")){
				$output['comments'][$templates_object['comments'][$i]] =  $template_paths['comments'][$path_type]['override'].$templates_object['comments'][$i].".html";
			}
			else {
				$output['comments'][$templates_object['comments'][$i]] = $template_paths['comments'][$path_type]['default'].$templates_object['comments'][$i].".html";
			}
		}
		
	}
	
	return $output;
}







//convert object to array $array =  (array) $yourObject;
class pw_query_args{
	public $post_type;
	public $link_format;//pw
	public $post_class;//pw
	public $author;
	public $author_name;
	public $year;
	public $month;
	public $tax_query;
	public $s;
	public $orderby='date';
	public $order='DESC';
	public $posts_per_page="-1";
	public $fields;
}


?>