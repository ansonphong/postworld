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
					"name"			=>	__("Facebook","postworld"),
					"icon"			=>	"pwi-facebook-square",
					"description"	=>	__("URL of your Facebook Page","postworld"),
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"facebook_app_id",
					"name"			=>	__("Facebook App ID","postworld"),
					"icon"			=>	"pwi-facebook",
					"description"	=>	__("The ID of your Facebook App","postworld"),
					"prepend_url"	=>	"",
					"_public"		=>	false,
					),
				array(
					"id"			=>	"twitter",
					"name"			=>	__("Twitter","postworld"),
					"icon"			=>	"pwi-twitter-square",
					"description"	=>	__("Twitter Username, without the '@'","postworld"),
					"prepend_url"	=>	"http://twitter.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"twitter_hashtags",
					"name"			=>	__("Twitter Hashtags","postworld"),
					"icon"			=>	"pwi-twitter",
					"description"	=>	__("Optional hashtag(s) to include in tweets, without the '#'","postworld"),
					"prepend_url"	=>	"",
					"_public"		=>	false,
					),
				array(
					"id"			=>	"instagram",
					"name"			=>	__("Instagram","postworld"),
					"icon"			=>	"pwi-instagram-square",
					"description"	=>	__("Your instagram username, not URL","postworld"),
					"prepend_url"	=>	"http://instagram.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"youtube",
					"name"			=>	__("YouTube","postworld"),
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	__("Your YouTube username, not URL","postworld"),
					"prepend_url"	=>	"https://www.youtube.com/user/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"vimeo",
					"name"			=>	__("Vimeo","postworld"),
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	__("Your Vimeo username, not URL","postworld"),
					"prepend_url"	=>	"https://vimeo.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"soundcloud",
					"name"			=>	__("SoundCloud","postworld"),
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	__("Your SoundCloud username, not URL","postworld"),
					"prepend_url"	=>	"https://soundcloud.com/",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"linkedin",
					"name"			=>	__("LinkedIn","postworld"),
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	__("URL of your LinkedIn Profile","postworld"),
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				array(
					"id"			=>	"tripadvisor",
					"name"			=>	__("Trip Advisor","postworld"),
					"icon"			=>	"pwi-circle-medium",
					"description"	=>	__("URL of your Tripadvisor Page","postworld"),
					"prepend_url"	=>	"",
					"_public"		=>	true,
					),
				),
			),
		array(
			"id"	=>	"contact",
			"name"	=>	__("Contact Info","postworld"),
			"icon"	=>	"pwi-book",
			"fields"	=>	array(
				array(
					"id"	=>	"email",
					"name"	=>	__("Email","postworld"),
					"icon"	=>	"pwi-mail",
					),
				array(
					"id"	=>	"phone",
					"name"	=>	__("Phone Number","postworld"),
					"icon"	=>	"pwi-phone",
					),
				array(
					"id"	=>	"phone_int",
					"name"	=>	__("International Phone","postworld"),
					"icon"	=>	"pwi-phone",
					),
				array(
					"id"	=>	"fax",
					"name"	=>	__("Fax Number","postworld"),
					"icon"	=>	"pwi-file",
					),
				array(
					"id"	=>	"address_name",
					"name"	=>	__("Address Name","postworld"),
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"address1",
					"name"	=>	__("Address","postworld"),
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"address2",
					"name"	=>	__("Address Details","postworld"),
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"postal_code",
					"name"	=>	__("Postal Code","postworld"),
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"city",
					"name"	=>	__("City","postworld"),
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"region",
					"name"	=>	__("Province/State","postworld"),
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"country",
					"name"	=>	__("Country","postworld"),
					"icon"	=>	"pwi-globe",
					),
				array(
					"id"	=>	"note",
					"name"	=>	__("Note","postworld"),
					"icon"	=>	"pwi-pencil",
					),
				),
			),
		
		);

	return apply_filters( 'pw_social_meta', $social_meta );
}

