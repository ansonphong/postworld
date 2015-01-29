<?php

////////// STRUCTURE STYLES MODEL //////////
// This is to access a single style set

function pw_social_model( $model ){

	$default_model = array(
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

	$model = array_replace_recursive( $default_model, $model );

	return $model;

}


add_filter( PW_OPTIONS_SOCIAL, 'pw_social_model' );


////////// SOCIAL ATTRIBUTES //////////
// - Define how to handle the settings of each type

function pw_social_meta( $social_meta = array() ){

	$social_meta	=	array(
		array(
			"id"	=>	"networks",
			"name"	=>	"Social Networks",
			"icon"	=>	"icon-globe-o",
			"fields"	=>	array(
				array(
					"id"			=>	"facebook",
					"name"			=>	"Facebook",
					"icon"			=>	"icon-facebook-square",
					"description"	=>	"URL of your Facebook Page",
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"facebook_app_id",
					"name"			=>	"Facebook App ID",
					"icon"			=>	"icon-facebook",
					"description"	=>	"The ID of your Facebook App",
					"prepend_url"	=>	"",
					"_public"		=>	false,
					),
				array(
					"id"			=>	"twitter",
					"name"			=>	"Twitter",
					"icon"			=>	"icon-twitter-square",
					"description"	=>	"Twitter Username, without the '@'",
					"prepend_url"	=>	"http://twitter.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"twitter_hashtags",
					"name"			=>	"Twitter Hashtags",
					"icon"			=>	"icon-twitter",
					"description"	=>	"Optional hashtag(s) to include in tweets, without the '#'",
					"prepend_url"	=>	"",
					"_public"		=>	false,
					),
				array(
					"id"			=>	"instagram",
					"name"			=>	"Instagram",
					"icon"			=>	"icon-instagram-square",
					"description"	=>	"Your instagram username, not URL",
					"prepend_url"	=>	"http://instagram.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"youtube",
					"name"			=>	"YouTube",
					"icon"			=>	"icon-circle-medium",
					"description"	=>	"Your YouTube username, not URL",
					"prepend_url"	=>	"https://www.youtube.com/user/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"linkedin",
					"name"			=>	"LinkedIn",
					"icon"			=>	"icon-circle-medium",
					"description"	=>	"URL of your LinkedIn Profile",
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"tripadvisor",
					"name"			=>	"Trip Advisor",
					"icon"			=>	"icon-circle-medium",
					"description"	=>	"URL of your Tripadvisor Page",
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				),
			),
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
		
		);

	$social_meta = apply_filters( 'pw_social_meta', $social_meta );

	return $social_meta;

}






?>