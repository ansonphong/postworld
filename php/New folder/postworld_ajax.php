<?php

/* 	WP Ajax Tips
 *	http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
  
*/


//---------- oEMBED GET ----------//
function ajax_oembed_get(){
	list($response, $args, $nonce) = initAjaxResponse();
	$pw_args = $args;
	$oEmbed = wp_oembed_get( $pw_args['link_url'] );
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $oEmbed;
	echo json_encode($response);
	// die ( $oEmbed );
	die;
}
add_action("wp_ajax_nopriv_ajax_oembed_get", "ajax_oembed_get");
add_action("wp_ajax_ajax_oembed_get", "ajax_oembed_get");



/* *************************
 *	General Ajax Functions 
 * 
 ************************** */

global $postworld_api_version;
$postworld_api_version = "0.1";

function ErrorReturn($response, $status, $message) {
	$response['status'] = $status;
	$response['message'] = $message;
	echo json_encode($response);
	die;
}

function initAjaxResponse() {
	global $postworld_api_version;
	// Create Response JSON Object, to include api version, status, error code if any, data results
	$response = array();
	$response['version'] = $postworld_api_version;
	// data is received in the raw data, not in the post data http://stackoverflow.com/questions/10494574/what-is-the-difference-between-form-data-and-request-payload
	// $args_text = $_POST['args']; // This will not work unless the Ajax Call is modified to post as a form with url encoded parameters
	$params_text = file_get_contents("php://input");
	// if no parameters then return error
	if (!$params_text) {
		ErrorReturn($response, 400, 'Error in parameters');	
	}
	$params = json_decode($params_text,true);
	if (!$params) {
		ErrorReturn($response, 400, 'Error in parameters');	
	}
	if (!isset($params['nonce'])) ErrorReturn($response, 400, 'Error in parameters');
	$nonce = $params['nonce'];
	// TODO check Nonce value
	if (!isset($params['args'])) ErrorReturn($response, 400, 'Error in parameters');
	$args = $params['args'];	
	return array($response, $args, $nonce);
}


/* *************************
 *	Feed Functions 
 * 
 ************************** */

/* Actions for pw_live_feed() */

function pw_live_feed_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args']['feed_query'];
	$pw_args = $args['args'];
	// Get the results in array format, so that it is converted once to json along with the rest of the response
	$results = pw_live_feed ( $pw_args );
	// TODO check results are ok
	// TODO return success code or failure code , as well as version number with the results.
	/* set the response type as JSON */
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}



/* Actions for pw_register_feed () */

function pw_register_feed_admin() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	$func_args = $args['args'];
	// TODO check results are ok
	/* set the response type as JSON */
	$results = pw_register_feed($args['args']);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Actions for pw_load_feed () */

function pw_load_feed_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	$func_args = $args['args'];
	// TODO check results are ok
	/* set the response type as JSON */
	// TODO check values are correct
	if ($func_args['feed_id']) $feed_id = $func_args['feed_id']; else $feed_id = ''; 
	if ($func_args['preload']) $preload = $func_args['preload']; else $preload = 0;
	$results = pw_load_feed($feed_id,$preload);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Action Hook for pw_live_feed() - Logged in users */
add_action("wp_ajax_pw_live_feed", "pw_live_feed_anon");

/* Action Hook for pw_live_feed() - Anonymous users */
add_action("wp_ajax_nopriv_pw_live_feed", "pw_live_feed_anon");

/* Action Hook for pw_load_feed() - Logged in users */
add_action("wp_ajax_pw_load_feed", "pw_load_feed_anon");

/* Action Hook for pw_load_feed() - Anonymous users */
add_action("wp_ajax_nopriv_pw_load_feed", "pw_load_feed_anon");

/* Action Hook for pw_register_feed() - Logged in users */
add_action("wp_ajax_pw_register_feed", "pw_register_feed_admin");

/* Action Hook for pw_register_feed() - Anonymous users */
// add_action("wp_ajax_nopriv_pw_register_feed", "pw_register_feed_anon");


/* *************************
 *	Posts Functions 
 * 
 ************************** */


/* Actions for pw_get_posts () */

function pw_get_posts_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args']['feed_query'];
	$pw_args = $args['args'];
	// Get the results in array format, so that it is converted once to json along with the rest of the response
	$results = pw_get_posts ( $args['post_ids'],$args['fields'] );
	
	// TODO check results are ok
	/* set the response type as JSON */
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}


/* Actions for pw_get_post_types () */

function pw_get_post_types_admin() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args']['feed_query'];
	
	$pw_args = $args['args'];
	
	// Get the results in array format, so that it is converted once to json along with the rest of the response
	
	//$results = pw_get_posts ( $args['post_ids'],$args['fields'] );
	
	// TODO check results are ok
	/* set the response type as JSON */
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = 'hello universe!'; //$results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Action Hook for pw_get_posts() - Logged in users */
add_action("wp_ajax_pw_get_posts", "pw_get_posts_anon");

/* Action Hook for pw_get_posts() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_posts", "pw_get_posts_anon");

/* Action Hook for pw_get_post_types() - Logged in users */
add_action("wp_ajax_pw_get_post_types", "pw_get_post_types_admin");


/* *************************
 *	Tempalte Functions 
 * 
 ************************** */

 /* Actions for pw_get_templates () */

function pw_get_templates_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args'];
	// TODO check results are ok
	// TODO return success code or failure code , as well as version number with the results.
	/* set the response type as JSON */
	$results = pw_get_templates($args['templates_object']);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}
 
/* Action Hook for pw_get_templates() - Logged in users */
add_action("wp_ajax_pw_get_templates", "pw_get_templates_anon");

/* Action Hook for pw_get_templates() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_templates", "pw_get_templates_anon");


/* *************************
 *	Comments Functions 
 * 
 ************************** */

 /* Actions for pw_get_comments () */

function pw_get_comments_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	if($args['query']) $query = $args['query'];
	else ErrorReturn($response, 400, 'missing argument query'); 
	if ($args['fields']) $fields = $args['fields'];
	else $fields = null;
	if ($args['tree'])	$tree = $args['tree'];
	else $tree = null;
	/* set the response type as JSON */
	$results = pw_get_comments($query,$fields,$tree);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

 /* Actions for pw_save_comment () */

function pw_save_comment_loggedIn() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	if($args['comment_data']) $commentdata = $args['comment_data'];
	else ErrorReturn($response, 400, 'missing argument comment_data');
	// had to rename it to return_value, since return in ajax javascript is a reserved word 
	if ($args['return_value']) $return = $args['return_value'];
	else $return = null;
	
	// TODO should we use wp_new_comment instead of wp_insert_comment http://codex.wordpress.org/Function_Reference/wp_new_comment?
	// Sanitize
	$commentdata = apply_filters('preprocess_comment', $commentdata);
	
	
	// Get User ID, it must be real, since this function is called for logged in users only
	$user_ID = get_current_user_id();
	if (!$user_ID) ErrorReturn($response, 400, 'User must be authenticated to perform this action');
	$commentdata['user_id'] = $user_ID;
	
	// Get Author Info
	 $user_data = get_userdata( $user_ID );
	if ($user_data->display_name) {
		$commentdata['comment_author'] = $user_data->display_name; 
	} else if ($user_data->user_nicename) {
		$commentdata['comment_author'] = $user_data->user_nicename; 
	} else 
		$commentdata['comment_author'] = $user_data->user_login; 
	
	if ($user_data->user_email) {
		$commentdata['comment_author_email'] = $user_data->user_email; 
	}
	  
	if ($user_data->user_url) {
		$commentdata['comment_author_url'] = $user_data->user_url; 
	}  	
	
	// Get IP, Agent
	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
	$commentdata['comment_agent']     = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ) : '';
	// Get Date
	// $commentdata['comment_date']     = current_time('mysql');
	$commentdata['comment_date_gmt'] = current_time('mysql', 1);

	// Sanitize
	$commentdata = wp_filter_comment($commentdata);
	$commentdata['comment_approved'] = wp_allow_comment($commentdata);	
			
	
	$results = pw_save_comment($commentdata,$return);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

 /* Actions for pw_delete_comment () */

function pw_delete_comment_loggedIn() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	if($args['comment_id']) $comment_id = $args['comment_id'];
	else ErrorReturn($response, 400, 'missing argument comment_id');
		
	// Get User ID, it must be real, since this function is called for logged in users only
//	$user_ID = get_current_user_id();
//	if (!$user_ID) ErrorReturn($response, 400, 'User must be authenticated to perform this action');
//	$commentdata['user_id'] = $user_ID;					
	
	// $results = pw_save_comment($commentdata,$return);
	$results = wp_delete_comment( $comment_id);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}



/* Action Hook for pw_delete_comment() - Logged in users */
add_action("wp_ajax_pw_delete_comment", "pw_delete_comment_loggedIn");

 
/* Action Hook for pw_save_comment() - Logged in users */
add_action("wp_ajax_pw_save_comment", "pw_save_comment_loggedIn");


/* Action Hook for pw_get_comments() - Logged In users */
add_action("wp_ajax_pw_get_comments", "pw_get_comments_anon");

/* Action Hook for pw_get_comments() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_comments", "pw_get_comments_anon");



/* *************************
 *	Test Functions - used for testing and can be removed 
 * 
 ************************** */
 
 function pw_test_code_admin() {
	// list($response, $args, $nonce) = initAjaxResponse();
	global $postworld_api_version;
	// Create Response JSON Object, to include api version, status, error code if any, data results
	$response = array();
	$response['version'] = $postworld_api_version;
	
	$query = array(
            'post_id' => 166220,            
    	);
		
	// TODO check results are ok
	/* set the response type as JSON */
	// TODO check values are correct
	$results = pw_get_comments ($query,'all',true);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}
 

/* Action Hook for pw_test_code() - Logged in users */
add_action("wp_ajax_pw_test_code", "pw_test_code_admin" );

	function sssssssssss() {



//print_r(set_favorite(TRUE,14,3));
//print_r(set_view_later(TRUE,3363,4));

//set_favorite(FALSE,66866);
//print_r(set_view_later(TRUE));


	/*$json_terms= array(
			"category"=>array(
					array( 'slug'=> 'haidy',
							'name'=> 'changed name',
							'children'=>array(
								'ebn_haidy'=>'Ancient ebn_haidy',
								'ebn_haidy2'=> 'Astrology ebn_haidy',
								'ebn_haidy3'=> 'Consciousness ebn_haidy',
								'ebn_haidy4'=> 'Dreams ebn_haidy'),
					)  ));*/
					/*,
					array(
					'slug'=> 'arts',
							'name'=> 'arts','children'=>array(
							'conferences'=> 'Conferences',
	        				'digital_art'=> 'Digital Art',
	        				'world_art'=> 'World Art')
					
					),
					
					array(
					'slug'=> 'body',
							'name'=> 'body',
							'children'=>array(
							 'energy_medicine'=> 'Energy Medicine',
	       					 'food_nutrition'=> 'Food & Nutrition',
	      					 'healing'=> 'Healing',
	       					 'herbalism'=> 'Herbalism')
					
					),

	)
	);*/

//$json_terms = json_encode($json_terms);

//pw_insert_terms($json_terms,ARRAY_A, true);
	
	
/*$args = array (
            'feed_id' => 'feed_1',
            'write_cache'  => true,
            'feed_query' => array(
                'post_count' => 200,
                'fields' => 'all',
                'post_type' => 'post',
                'orderby' => 'date',
                'offset' => 15,
                'post_format' => null,
                'post_class' => null,
                'posts_per_page' => 200,
                'tax_query' => array( // <<<< This isn't working
                    array(
                      'taxonomy' => 'category',
                      'field' => 'slug',
                      'terms' => 'life'
                    ),
                  ),
            )
        );
       ( pw_register_feed ($args));				
		
(pw_feed_outline($args['feed_query']));
echo "<br><br>";
print_r(new WP_Query($args['feed_query']));	*/
		
/*$args =array('orderby' => 'date');
		
print_r(new WP_Query( 'author=1,post_type=any' ));*/
		
	
	
	$my_post = array(
	'ID' =>13,
  	'post_title'    => 'helggggggggggggggglo',
  	'post_content'  =>null,
  	'post_status'   => 'publish',
  	'post_author'   => 1,
  	'post_category' => array( 8,39 ),
  	'post_class'=>'test',
	'post_format'=>'ggggggg',
	//'link_url'=>'sssssss',
	'external_image'=>'fgdfgdfgdf',
	);
	print_r(pw_update_post($my_post));
	
	
	
	//echo "<br><br>".json_encode( new PW_User_Query( array( 'orderby'=>null ,'email'=>null,'fields'=>'all') ));
	
	
	
	//list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	//$pw_args = $args->args;
	// Get the results in array format, so that it is converted once to json along with the rest of the response
	//echo json_encode(new WP_User_Query(null));
	//echo 'ssssssssssssssssssss';
	//echo json_encode(new PW_User_Query( array( 'orderby'=>'ID,display_name,name,login,nicename,email,url,registered,post_count') ));
	//echo json_encode(new PW_Query(array( "offset"=>2,"fields"=>"ids")));

	 /*$args = array(
  			 'comments' => array(
        	 'post_types' => array( 'post', 'link' ),
        	 'post_views' => array( 'grid', 'list', 'detail', 'full' )
    ),
);*/

	//$args =array( 'panels'=>'panel_id' );
	//print_r(pw_get_templates ( null ));
	//print_r(pw_get_templates ( $args ));
	//$args =array( 'comments'=>array('comment-single') );
	//print_r(pw_get_templates ( $args ));
	
	//print_r(get_comment_ids());
	
	
	//add_new_feed("feed_1",json_encode(array("posts_per_page"=>5, "offset"=>2,"fields"=>"ids")));
	//pw_register_feed(array("feed_id"=>"feed_2","write_cache"=>TRUE,"feed_query"=>array("posts_per_page"=>5, "offset"=>2,"fields"=>"ids")));
	
	//print_r(get_panel_ids());
	//print_r(add_recored_to_post_meta(44,0,0,0,0));
	//add_share(1, 2, 1,null, date("Y-m-d H:i:s"));
	//print_r(calculate_user_shares(1,'outgoing'));
	
	//print_r(add_record_to_user_meta(2));
	
	
	//print_r(set_post_relationship('viewed', 13, 1, TRUE	));
	//print_r(get_post_relationship('viewed',13,66));
	//print_r(get_post_relationships(1,'viewed'));
	/*$my_post = array(
	'ID' =>14444444444444443,
  	'post_title'    => 'helggggggggggggggglo',
  	'post_content'  =>'sdsfdsfds',
  	'post_status'   => 'publish',
  	'post_author'   => 1,
  	'post_category' => array( 8,39 ),
  	'post_class'=>'test',
	'post_format'=>'ggggggg',
	'link_url'=>'sssssss',
	'external_image'=>'fgdfgdfgdf',
	);
	print_r(pw_update_post($my_post));*/
	
	/*$website = 'http://wordpress.org';
    $userdata= array ( 'ID' => 1, 'user_url' => $website,user_fields_names::$LOCATION_CITY=>'city yasssssssss city',user_fields_names::$COMMENT_POINTS=>30 ,user_fields_names::$SHARE_POINTS_META=>'3,4,5,6') ;
	print_r(pw_update_user($userdata));*/
	
	/*'ID' - Order by user id.
		'display_name' - Order by user display name.
		'name' / 'user_name' - Order by user name.
		'login' / 'user_login' - Order by user login.
		'nicename' / 'user_nicename' - Order by user nicename.
		'email' / 'user_email' - Order by user email.
		'url' / 'user_url' - Order by user url.
		'registered' / 'user_registered' - Order by user registered date.
		'post_count' - Order*/
		
	//print_r(cache_user_shares(1,'both'));
	//print_r(cache_post_shares(1));
	//echo "<br><br>".json_encode( new PW_User_Query( array( 'orderby'=>'post_points' ,'email'=>'asun@phong.com','fields'=>'all') ));
	
	//print_r(add_record_to_comment_points(13, 1, 5));
	//print_r(add_record_to_post_points(14, 1, 5));
	
	//$args =  array('ID'=>13,'post_points'=>20);
	//print_r(pw_update_post($args));
	
	//print_r(pw_get_post_template(13,'detail'));
	//print_r(pw_get_templates());
	
	//print_r(pw_load_feed('feed_1',1));
	/*print_r(pw_live_feed(array('feed_id'=>'feed_1','preload'=>4,'feed_query' => array(
            'post_count' => 200,
            'fields' => 'all',
            'post_type' => 'post',
            'orderby' => 'date',
            'offset' => 3,
            'post_format' => null,
            'post_class' => null,
            'posts_per_page' => 200
        ))));
	*/
	
	
//	print_r(pw_get_templates());
	//echo json_encode(pw_get_posts( array(34,854),'all'));
	// TODO check results are ok
	// TODO return success code or failure code , as well as version number with the results.
	/* set the response type as JSON */
	//header('Content-Type: application/json');
	
	// documentation says that die() should be the end...
	die();
	
}
/* Action Hook for pw_live_feed() - Logged in users */
add_action("wp_ajax_sssssssssss", "sssssssssss");



?>