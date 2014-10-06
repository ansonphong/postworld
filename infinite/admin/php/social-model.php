<?php

////////// STRUCTURE STYLES MODEL //////////
// This is to access a single style set

function i_social_model(){
	global $i_social_model;
	return $i_social_model;
}

global $i_social_model;
$i_social_model = array(
	"contact"	=>	array(
		"email"			=>	"",
		"phone"			=>	"",
		"phone_int"		=>	"",
		"fax"			=>	"",
		"name"			=>	"",
		"address1"		=>	"",
		"address2"		=>	"",
		"postal_code"	=>	"",
		"city"			=>	"",
		"region"		=>	"",
		"country"		=>	""
		),
	"networks"	=>	array(
		"facebook"		=>	"",
		"twitter"		=>	"",
		"tripadvisor"	=>	"",
		),

	);

////////// MERGE CHILD SOCIAL MODEL //////////
if( isset( $i_child_social_model ) ){
	$i_social_model = array_merge_recursive( $i_social_model, $i_child_social_model );
}

////////// SOCIAL ATTRIBUTES //////////
// - Define how to handle the settings of each type
global $i_social_meta;
$i_social_meta	=	array(
	array(
		"id"	=>	"contact",
		"name"	=>	"Contact Info",
		"icon"	=>	"icon-book",
		"fields"	=>	array(
			array(
				"id"	=>	"email",
				"name"	=>	"Email",
				"icon"	=>	"icon-mail",
				),
			array(
				"id"	=>	"phone",
				"name"	=>	"Phone Number",
				"icon"	=>	"icon-phone",
				),
			array(
				"id"	=>	"phone_int",
				"name"	=>	"International Phone",
				"icon"	=>	"icon-phone",
				),
			array(
				"id"	=>	"fax",
				"name"	=>	"Fax Number",
				"icon"	=>	"icon-file-2",
				),
			array(
				"id"	=>	"address_name",
				"name"	=>	"Address Name",
				"icon"	=>	"icon-globe",
				),
			array(
				"id"	=>	"address1",
				"name"	=>	"Address",
				"icon"	=>	"icon-globe",
				),
			array(
				"id"	=>	"address2",
				"name"	=>	"Address Details",
				"icon"	=>	"icon-globe",
				),
			array(
				"id"	=>	"postal_code",
				"name"	=>	"Postal Code",
				"icon"	=>	"icon-globe",
				),
			array(
				"id"	=>	"city",
				"name"	=>	"City",
				"icon"	=>	"icon-globe",
				),
			array(
				"id"	=>	"region",
				"name"	=>	"Province/State",
				"icon"	=>	"icon-globe",
				),
			array(
				"id"	=>	"country",
				"name"	=>	"Country",
				"icon"	=>	"icon-globe",
				),
			),
		),
	array(
		"id"	=>	"networks",
		"name"	=>	"Social Networks",
		"icon"	=>	"icon-heart",
		"fields"	=>	array(
			array(
				"id"			=>	"facebook",
				"name"			=>	"Facebook",
				"icon"			=>	"icon-facebook",
				"description"	=>	"URL of your Facebook Page",
				"prepend_url"	=>	"",
				),
			array(
				"id"			=>	"facebook_app_id",
				"name"			=>	"Facebook App ID",
				"icon"			=>	"icon-facebook-square",
				"description"	=>	"The ID of your Facebook App",
				"prepend_url"	=>	"",
				),
			array(
				"id"			=>	"twitter",
				"name"			=>	"Twitter",
				"icon"			=>	"icon-twitter",
				"description"	=>	"Twitter Username, without the '@'",
				"prepend_url"	=>	"http://twitter.com/",
				),
			array(
				"id"			=>	"twitter_hashtags",
				"name"			=>	"Twitter Hashtags",
				"icon"			=>	"icon-twitter-square",
				"description"	=>	"Optional hashtag(s) to include in tweets, without the '#'",
				"prepend_url"	=>	"",
				),
			array(
				"id"			=>	"tripadvisor",
				"name"			=>	"Trip Advisor",
				"icon"			=>	"icon-circle-medium",
				"description"	=>	"URL of your Tripadvisor Page",
				"prepend_url"	=>	"",
				),
			),
		),
	);


?>