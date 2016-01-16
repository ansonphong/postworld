<?php
/**
 * SOCIAL MODEL
 * Create the default social model.
 */
add_filter( PW_OPTIONS_SOCIAL, 'pw_social_model' );
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
			"country"		=>	"",
			"note"			=>	""
			),
		"networks"	=>	array(
			"facebook"		=>	"",
			"twitter"		=>	"",
			"tripadvisor"	=>	"",
			),
		"share" => array(
			"networks" => array(), //'facebook','twitter','email'
			),
		"widgets" => array(
			"facebook" => array(
				"enable" => true,
				"settings" => array(
					"share" =>  true,
					),
				),
			"twitter" => array(
				"enable" => true,
				),
			),
		);
	$model = array_replace_recursive( $default_model, $model );
	return $model;
}


/**
 * SOCIAL META
 */
function pw_social_meta( $social_meta = array() ){
	$social_meta	=	array(
		array(
			"id"	=>	"networks",
			"name"	=>	"Social Networks",
			"icon"	=>	"pwi-globe-o",
			"fields"	=>	array(
				array(
					"id"			=>	"facebook",
					"name"			=>	"Facebook",
					"icon"			=>	"pwi-facebook-square",
					"description"	=>	"URL of your Facebook Page",
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"facebook_app_id",
					"name"			=>	"Facebook App ID",
					"icon"			=>	"pwi-facebook",
					"description"	=>	"The ID of your Facebook App",
					"prepend_url"	=>	"",
					"_public"		=>	false,
					),
				array(
					"id"			=>	"twitter",
					"name"			=>	"Twitter",
					"icon"			=>	"pwi-twitter-square",
					"description"	=>	"Twitter Username, without the '@'",
					"prepend_url"	=>	"http://twitter.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"twitter_hashtags",
					"name"			=>	"Twitter Hashtags",
					"icon"			=>	"pwi-twitter",
					"description"	=>	"Optional hashtag(s) to include in tweets, without the '#'",
					"prepend_url"	=>	"",
					"_public"		=>	false,
					),
				array(
					"id"			=>	"instagram",
					"name"			=>	"Instagram",
					"icon"			=>	"pwi-instagram-square",
					"description"	=>	"Your instagram username, not URL",
					"prepend_url"	=>	"http://instagram.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"youtube",
					"name"			=>	"YouTube",
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	"Your YouTube username, not URL",
					"prepend_url"	=>	"https://www.youtube.com/user/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"vimeo",
					"name"			=>	"Vimeo",
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	"Your Vimeo username, not URL",
					"prepend_url"	=>	"https://vimeo.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"soundcloud",
					"name"			=>	"SoundCloud",
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	"Your SoundCloud username, not URL",
					"prepend_url"	=>	"https://soundcloud.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"linkedin",
					"name"			=>	"LinkedIn",
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	"URL of your LinkedIn Profile",
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"tripadvisor",
					"name"			=>	"Trip Advisor",
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	"URL of your Tripadvisor Page",
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				),
			),
		array(
			"id"	=>	"contact",
			"name"	=>	"Contact Info",
			"icon"	=>	"pwi-book",
			"fields"	=>	array(
				array(
					"id"	=>	"email",
					"name"	=>	"Email",
					"icon"	=>	"pwi-mail",
					),
				array(
					"id"	=>	"phone",
					"name"	=>	"Phone Number",
					"icon"	=>	"pwi-phone",
					),
				array(
					"id"	=>	"phone_int",
					"name"	=>	"International Phone",
					"icon"	=>	"pwi-phone",
					),
				array(
					"id"	=>	"fax",
					"name"	=>	"Fax Number",
					"icon"	=>	"pwi-file-2",
					),
				array(
					"id"	=>	"address_name",
					"name"	=>	"Address Name",
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"address1",
					"name"	=>	"Address",
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"address2",
					"name"	=>	"Address Details",
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"postal_code",
					"name"	=>	"Postal Code",
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"city",
					"name"	=>	"City",
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"region",
					"name"	=>	"Province/State",
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"country",
					"name"	=>	"Country",
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"note",
					"name"	=>	"Note",
					"icon"	=>	"pwi-pencil",
					),
				),
			),
		
		);

	return apply_filters( 'pw_social_meta', $social_meta );
}

