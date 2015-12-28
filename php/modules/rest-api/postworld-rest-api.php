<?php

/**
 * Make core endpoints, with configurable defaults settings in postworld config.
 *
 * PREFIX : /postworld/v1/
 *
 * /posts/?ids=151,162,253,465,684,758&fields=preview - DONE
 * /post/?id=555&fields=full - DONE
 *
 * /feed?id=jfk84j2 - DONE
 *
 * /terms/?taxonomy=category - return terms list
 * /term_feed/?taxonomy=category - return terms list with feed objects
 *
 * /feed?type=post,blog&fields=preview&max=25&id=jfk84j2
 *
 * /feed/date/year/2015
 * /feed/
 *
 * /related/[post|term]/[id]/[vars]
 * 
 */

function pw_rest_namespace(){
	return apply_filters( 'pw_rest_namespace', 'postworld' );
}

/**
 * @link http://v2.wp-api.org/extending/adding/
 */
add_action( 'rest_api_init', array('PW_REST_Controller','register_routes') );
class PW_REST_Controller{ //  extends WP_REST_Controller

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$thisClass = 'PW_REST_Controller';
		$version = '1';
		$namespace = pw_rest_namespace().'/v' . $version;

		register_rest_route( $namespace, '/post', array(
			array(
				'methods'	=> WP_REST_Server::READABLE,
				'callback'	=> array( $thisClass, 'get_post' ),
				'args'		=> array(
					'id' => array(
						'type' => 'integer'
					),
					'fields' => array(
						'default'	=>	'preview',
						'type' 		=>	'string',
						'sanitize_callback' => array( $thisClass, 'sanitize_post_fields' )
					),
				),
			),
		));
		
		register_rest_route( $namespace, '/posts', array(
			array(
				'methods'	=> WP_REST_Server::READABLE,
				'callback'	=> array( $thisClass, 'get_posts' ),
				'args'		=> array(
					'ids'          => array(
						'default'  => false,
						'type' => 'string',
						'sanitize_callback' => array( $thisClass, 'sanitize_ids' )
					),
					'fields' => array(
						'default'	=>	'preview',
						'type' 		=>	'string',
						'sanitize_callback' => array( $thisClass, 'sanitize_post_fields' )
					),
				),
			),
			
		));

		register_rest_route( $namespace, '/feed', array(
			array(
				'methods'	=> WP_REST_Server::READABLE,
				'callback'	=> array( $thisClass, 'get_feed' ),
				'args'		=> array(
					'id' => array(
						'type' => 'integer'
					),
					'fields' => array(
						'default'	=>	'preview',
						'type' 		=>	'string',
						'sanitize_callback' => array( $thisClass, 'sanitize_post_fields' )
					),
					'type' => array(
						'default'	=>	'post',
						'type' 		=>	'string',
						'sanitize_callback' => array( $thisClass, 'sanitize_post_types' )
					),
				),
			),
		));

		
		register_rest_route( $namespace, '/terms', array(
			array(
				'methods'	=> WP_REST_Server::READABLE,
				'callback'	=> array( $thisClass, 'get_terms' ),
				'args'		=> array(
					'taxonomy' => array(
						'type' => 'string',
						'default' => 'category'
					),
					

				),
			),
		));
		

	}


	/**
	 * Get a post
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_post( $request ) {
		$post_exists = post_exists_by_id( $request['id'] );
		if( $post_exists ){
			$post = pw_get_post( $request['id'], $request['fields'] );
			return new WP_REST_Response( $post, 200 );
		} else
			return new WP_Error( 'code', __( 'Post ID doesn\'t exist', 'postworld' ) );
	}


	/**
	 * Get a collection of posts
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_posts( $request ) {
		$posts = pw_get_posts( $request['ids'], $request['fields'] );
		if( !$posts )
			return new WP_Error( 'code', __( 'Error getting posts.', 'postworld' ) );
		else
			return new WP_REST_Response( $posts, 200 );
	}


	/**
	 * Get a feed of posts
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_feed( $request ) {

		// By Feed ID
		if( is_string( $request['id'] ) ){		
			$feed = pw_get_feed_by_id($request['id']);
			if( !$feed )
				return new WP_Error( 'code', __( 'Feed ID doesn\'t exist', 'postworld' ) );
			$query = $feed['query'];
			$result = pw_query( $query, $request['fields'] );
			return $result->posts;
		}

	}

	/**
	 * Get terms in a taxonomy.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_terms( $request ) {
		pw_log('get terms', (array) $request);
		return array( 'taxonomy' => $request['taxonomy'] );

		/*
		// By Feed ID
		if( is_string( $request['id'] ) ){		
			$feed = pw_get_feed_by_id($request['id']);
			if( !$feed )
				return new WP_Error( 'code', __( 'Feed ID doesn\'t exist', 'postworld' ) );
			$query = $feed['query'];
			$result = pw_query( $query, $request['fields'] );
			return $result->posts;
		}
		*/

	}
	

	/**
	 * Sanitizes a Postworld 'field' request.
	 */
	public function sanitize_post_fields( $fields ){
		$fields_array = explode( ',', trim($fields) );
		if( count( $fields_array ) === 1 ){
			$field_model = pw_get_field_model( 'post', $fields_array[0] );
			if( !$field_model )
				return 'preview';
			else
				return $fields_array[0];
		}
		else
			return $fields_array;
	}

	/**
	 * Santizes a comma deliniated array of IDs
	 * @param string $ids_string Comma deliniated IDs
	 * @return array Numerical IDs.
	 */
	public function sanitize_ids( $ids_string ){
		$ids = explode( ',', $ids_string );
		$ids = pw_sanitize_numeric_array( $ids );
		return $ids;
	}

	public function sanitize_post_types( $post_type ){
		if( !post_type_exists($post_type) )
			return 'post';
		else
			return $post_type;
	}


}


