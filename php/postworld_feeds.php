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
	$post_array = pw_query($pw_query_args); // <<< TODO : Flatten from returned Object to Array of IDs
	$post_ids  = $post_array->posts;
	

	return $post_ids; // Array of post IDs
}


/*
1-pw_get_templates
2-load_feed
3-pw_cache_feed
4-pw_register_feed
5-pw_get_feed
*/

function pw_load_feed ( $feed_id, $preload ){
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
}



function list_dir_file_names($directory, $url_path){
		
	$names_array=array();
	if (is_dir($directory)){
		
	
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
			        $names_array[]= $url_path.($fileinfo->getFilename());
			    }
			}
	}
			
	return $names_array;
}
function pw_get_templates ( $templates_object ){
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
			 * */
		
		//abs paths
		$default_posts_template_abs_path = ABSPATH . "wp-content/plugins/postworld/templates/posts/" ;
		$override_posts_template_abs_path = get_template_directory()."\\postworld\\templates\\posts\\";
		$default_panel_template_abs_path = ABSPATH . "wp-content/plugins/postworld/templates/panels/" ;
		$override_panel_template_abs_path = get_template_directory()."\\postworld\\templates\\panels\\";
		
		//urls
		$default_posts_template_url = plugins_url()."/postworld/templates/posts/";
		$override_posts_template_url = get_template_directory_uri()."/postworld/templates/posts/";			 
		$default_panel_template_url = plugins_url()."/postworld/templates/panels/";
		$override_panel_template_url = get_template_directory_uri()."/postworld/templates/panels/";
		
		
		$output = array();	 
		if(!$templates_object){
			/*null : default
				Returns object with all panels and templates in the default and over-ride folders.
			 */
			
			
			
			
			$output['templates']= array();
			$output['templates']['default']= list_dir_file_names($default_posts_template_abs_path,$default_posts_template_url);
			$output['templates']['override']= list_dir_file_names($override_posts_template_abs_path,$override_posts_template_url);
			
			
			
			$output['panels'] = array();
			$output['panels']['default']= list_dir_file_names($default_panel_template_abs_path,$default_panel_template_url);
			$output['panels']['override']= list_dir_file_names($override_panel_template_abs_path,$override_panel_template_url);
		}
		
		
		else if($templates_object['posts']){
			
			 $output['posts'] = array(); 
			 for ($i=0; $i < count($templates_object['posts']['post_types']) ; $i++) {
			 	 $output['posts'][$templates_object['posts']['post_types'][$i] ]=array();
				
				 for ($j=0; $j < count($templates_object['posts']['post_views']) ; $j++) {
					 	 
					 $template_name = $templates_object['posts']['post_types'][$i] ."-". $templates_object['posts']['post_views'][$j].".html";
					// echo "<br>".$template_name;
					// echo("post_over+name :" . $override_posts_template_abs_path.$template_name);
				 	 if(file_exists($override_posts_template_abs_path.$template_name)){
				 	// 	echo 'file exists';	
				 	 	$output['posts'][$templates_object['posts']['post_types'][$i] ][$templates_object['posts']['post_views'][$j]]= $override_posts_template_url.$template_name;
				 	 }
				 	 
				 	 else{
				 	 //	echo ('file doesnt exist');
				 	 	$fall_back_template_name ="post-".$templates_object['posts']['post_views'][$j].".html";
				 	 //	echo("fallbackname:".$fall_back_template_name."<br>");
				 	 	if(file_exists($override_posts_template_abs_path.$fall_back_template_name))
				 	 		$output['posts'][$templates_object['posts']['post_types'][$i] ][$templates_object['posts']['post_views'][$j]]=$override_posts_template_url.$fall_back_template_name;
						
						else{
							$fall_back_template_default_path = $default_posts_template_url."post-".$templates_object['posts']['post_views'][$j].".html";
							$output['posts'][$templates_object['posts']['post_types'][$i] ][$templates_object['posts']['post_views'][$j]]=$fall_back_template_default_path;
						}

				 	 }
				 }//for
			 } //for

			 
		}

		/* array( 'panels'=>'panel_id' )*/
		else if($templates_object['panels' ]) {
			$output=array();
			$output['panels']=array();

			
			if(file_exists($override_panel_template_abs_path.$templates_object['panels']).".html"){
				$output['panels'][$templates_object['panels']] =  $override_panel_template_url.$templates_object['panels'].".html";
			}
			else {
				$output['panels'][$templates_object['panels']] =  $default_panel_template_url.$templates_object['panels'].".html";
			}
		}
		return $output;
	}
		//convert object to array $array =  (array) $yourObject;
	class pw_query_args{
		public $post_type;
		public $post_format;//pw
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