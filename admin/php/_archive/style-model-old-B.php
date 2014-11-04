<?php

////////// STRUCTURE STYLES MODEL //////////
// This is to access a single style set

function i_style_model(){
	global $i_style_model;
	return $i_style_model;
}

global $i_style_model;
$i_style_model = array(

	///// HTML Elements /////
	"element"	=>	array(
		"body"	=>	array(
			"background-color"		=>	"#fff",
			"color"					=>	"inherit",
			"font-family"			=>	"inherit",
			),
		"a"	=>	array(
			"color"					=>	"inherit",
			"color:hover"			=>	"#000",
			"background-color"		=>	"transparent",
			"background-color:hover"=>	"transparent",
			"text-decoration"		=>	"none",
			"text-decoration:hover"	=>	"underline",
			),
		"h1"	=>	array(
			"color"				=>	"inherit",
			"font-size"			=>	"32px",
			"font-family"		=>	"inherit",
			"letter-spacing"	=>	"0",
			"line-height"		=>	"auto",
			"background-color"	=>	"transparent",
			"margin"			=>	"inherit",
			"padding"			=>	"inherit",
			),
		"h2"	=>	array(
			"color"				=>	"inherit",
			"font-size"			=>	"24px",
			"font-family"		=>	"inherit",
			"letter-spacing"	=>	"0",
			"line-height"		=>	"auto",
			"background-color"	=>	"transparent",
			"margin"			=>	"inherit",
			"padding"			=>	"inherit",
			),
		"h3"	=>	array(
			"color"				=>	"inherit",
			"font-size"			=>	"17px",
			"font-family"		=>	"inherit",
			"letter-spacing"	=>	"0",
			"line-height"		=>	"auto",
			"background-color"	=>	"transparent",
			"margin"			=>	"inherit",
			"padding"			=>	"inherit",
			),
		"p"		=>	array(
			"color"				=>	"inherit",
			"font-size"			=>	"21px",
			"font-family"		=>	"inherit",
			"letter-spacing"	=>	"0",
			"line-height"		=>	"auto",
			"background-color"	=>	"transparent",
			"margin"			=>	"0 0 20px",
			"padding"			=>	"0px",
			),
		),

	///// CLASSES /////
	"class"	=>	array(
		"page"	=>	array(
			"background-color"	=>	"#fff",
			"border-radius"		=>	"0px",
			"box-shadow"		=>	"none",
			"border"			=>	"none",
			"padding"			=>	"0px",
			"margin"			=>	"0px"
			),

		"block"	=>	array(
			"background-color"	=>	"#f2f2f2",
			"border-radius"		=>	"6px",
			"box-shadow"		=>	"none",
			"border"			=>	"1px solid #ccc",
			"padding"			=>	"10px",
			"margin"			=>	"10px"
			),

		"content"	=>	array(
			"background-color"	=>	"#fff",
			"border-radius"		=>	"0px",
			"box-shadow"		=>	"none",
			"border"			=>	"none",
			"padding"			=>	"40px",
			"font-size"			=>	"17px",
			"font-family"		=>	"inherit",
			"color"				=>	"#000",
			),

		"sidebar"	=>	array(
			"background-color"	=>	"#fff",
			"border-radius"		=>	"6px",
			"box-shadow"		=>	"none",
			"border"			=>	"0px solid transparent",
			),

		"sidebar-widget"	=>	array(
			"background-color"	=>	"#fff",
			"border-radius"		=>	"6px",
			"box-shadow"		=>	"none",
			"border"			=>	"1px solid #ccc",
			"padding"			=>	"10px",
			"margin"			=>	"10px"
			),

		"sidebar-title"	=>	array(
			"color"				=>	"#000",
			"font-size"			=>	"18px",
			"font-family"		=>	"inherit",
			"letter-spacing"	=>	"0",
			"line-height"		=>	"auto",
			"background-color"	=>	"transparent",
			"margin"			=>	"inherit",
			"padding"			=>	"inherit",
			),

		"sidebar-list"	=>	array(
			"list-style-type"	=>	"none",
			"margin"			=>	"0px",
			"padding"			=>	"0px",
			),

		"sidebar-list-link"	=>	array(
			"transition"		=>	"0.33s ease all",
			"display"			=>	"block",
			"color"				=>	"#999",
			"color:hover"		=>	"#333",
			"background-color"	=>	"#f2f2f2",
			"background-color:hover" =>	"#ccc",
			"font-size"			=>	"17px",
			"font-weight"		=>	"normal",
			"font-family"		=>	"inherit",
			"border"			=>	"1px solid #ccc",
			"border:hover"		=>	"1px solid #000",
			"border-radius"		=>	"3px",
			"margin"			=>	"2px",
			"padding"			=>	"5px 20px",
			"text-decoration"	=>	"none",
			"text-decoration-hover" => "none",
			"position"			=>	"relative",
			"top"				=>	"0",
			"top:hover"			=>	"0",
			"left"				=>	"0",
			"left:hover"		=>	"0",
			"box-shadow"		=>	"none",
			"text-shadow"		=>	"none",
			"box-shadow:hover"	=>	"none",
			),

		"sidebar-list-link-selected"	=>	array(
			"display"			=>	"block",
			"color"				=>	"#999",
			"color:hover"		=>	"#333",
			"background-color"	=>	"#f2f2f2",
			"background-color:hover" =>	"#ccc",
			"font-size"			=>	"17px",
			"font-weight"		=>	"bold",
			"border"			=>	"1px solid #ccc",
			"border:hover"		=>	"1px solid #000",
			"border-radius"		=>	"3px",
			"margin"			=>	"auto",
			"padding"			=>	"auto",
			"text-decoration"	=>	"none",
			"text-decoration-hover" => "none",
			"position"			=>	"relative",
			"top"				=>	"0",
			"top:hover"			=>	"0",
			"left"				=>	"0",
			"left:hover"		=>	"0",
			"box-shadow"		=>	"none",
			//"box-shadow:hover"	=>	"none",
			"text-shadow"		=>	"none",
			),

		"tag"	=>	array(
			"color"				=>	"#999",
			"color:hover"		=>	"#333",
			"background-color"	=>	"#f2f2f2",
			"background-color:hover" =>	"#ccc",
			"font-size"			=>	"21px",
			"font-family"		=>	"inherit",
			"border"			=>	"1px solid #ccc",
			"border:hover"		=>	"1px solid #000",
			"border-radius"		=>	"3px",
			"box-shadow"		=>	"none",
			"text-decoration"	=>	"none",
			"text-decoration-hover" => "none",
			),
		),

	'var'	=>	array(
		'bootstrap'	=>	array(
			'grid-gutter-width'	=>	'30px',
			)
		),
	);


////////// MERGE CHILD STYLE MODEL //////////
if( isset( $i_child_style_model ) ){
	$i_style_model = array_replace_recursive( $i_style_model, $i_child_style_model );
}

////////// PROFILES MODEL //////////
// This is for saving a series of profiles
$i_style_profiles = array(
	"default"	=>	array(
		// Style Settings Model
		),
	);


////////// STYLE ATTRIBUTES //////////
// - Define how to handle the settings of each type
$i_style_property_meta	=	array(
	"color"	=>	array(
		"input"		=>	"color-picker",
		),
	"font-size"	=>	array(
		"input"		=>	"number",
		),
	"font-family"	=>	array(
		"input"		=>	"google-fonts",
		),
	);


////////// STYLE LANGUAGE //////////
global $i_style_language;
$i_style_language = array(

	///// GENERAL LANGUAGE /////
	"general"	=>	array(
		"edit"	=>	array(
			"en"	=>	"Edit",
			"jp"	=>	"編集",
			"hi"	=>	"संपादित करें",
			),
		"done"	=>	array(
			"en"	=>	"Done",
			"hi"	=>	"पूर्ण",
			"jp"	=>	"完成",
			),
		),

	///// META DATA /////
	"meta"	=>	array(
		"element"	=>	array(
			"icon"	=>	"icon-code",
			"label"	=>	array(
				"en"	=>	"Elements",
				"jp"	=>	"要素",
				"hi"	=>	"तत्व",
				),
			"info"	=>	array(
				"en"	=>	"HTML elements which appear throughout the site",
				"jp"	=>	"要素",
				"hi"	=>	"तत्व",
				),
			),
		"class"	=>	array(
			"icon"	=>	"icon-asterisk",
			"label"	=>	array(
				"en"	=>	"Classes",
				"jp"	=>	"要素",
				"hi"	=>	"तत्व",
				),
			"info"	=>	array(
				"en"	=>	"Style classes which appear throughout the site",
				"jp"	=>	"要素",
				"hi"	=>	"तत्व",
				),
			),
		"var"	=>	array(
			"icon"	=>	"icon-cog",
			"label"	=>	array(
				"en"	=>	"Variables",
				"jp"	=>	"要素",
				"hi"	=>	"तत्व",
				),
			"info"	=>	array(
				"en"	=>	"Variables which effect the styling",
				"jp"	=>	"要素",
				"hi"	=>	"तत्व",
				),
			),
		),

	///// HTML ELEMENTS /////
	"element"	=>	array(
		"body"	=>	array(
			"label"	=>	array(
				"en"	=>	"Body",
				"jp"	=>	"ボディ",
				"hi"	=>	"शरीर",
				),
			"info"	=>	array(
				"en"	=>	"Hyperlinks",
				"jp"	=>	"ページの上部に一度に表示され、メインの見出し",
				"hi"	=>	"एक पृष्ठ के शीर्ष पर एक बार दिखाई देते हैं कि मुख्य शीर्षकोंं",
				),
			),
		"a"	=>	array(
			"label"	=>	array(
				"en"	=>	"Link",
				"jp"	=>	"主な標目",
				"hi"	=>	"प्राथमिक शीर्षक",
				),
			"info"	=>	array(
				"en"	=>	"Hyperlinks",
				"jp"	=>	"ページの上部に一度に表示され、メインの見出し",
				"hi"	=>	"एक पृष्ठ के शीर्ष पर एक बार दिखाई देते हैं कि मुख्य शीर्षकोंं",
				),
			"sample"	=>	array(
				"en"	=>	"Lorem ipsum dolor sit amet, consectetur adipiscing elit",
				"jp"	=>	"Lorem ipsum dolor sit amet, consectetur adipiscing elit",
				"hi"	=>	"Lorem ipsum dolor sit amet, consectetur adipiscing elit",
				),
			),
		"h1"	=>	array(
			"label"	=>	array(
				"en"	=>	"Heading 1",
				"jp"	=>	"主な標目",
				"hi"	=>	"प्राथमिक शीर्षक",
				),
			"info"	=>	array(
				"en"	=>	"The main headings that appear once at the top of a page",
				"jp"	=>	"ページの上部に一度に表示され、メインの見出し",
				"hi"	=>	"एक पृष्ठ के शीर्ष पर एक बार दिखाई देते हैं कि मुख्य शीर्षकोंं",
				),
			"sample"	=>	array(
				"en"	=>	"Lorem ipsum dolor sit amet, consectetur adipiscing elit",
				"jp"	=>	"Lorem ipsum dolor sit amet, consectetur adipiscing elit",
				"hi"	=>	"Lorem ipsum dolor sit amet, consectetur adipiscing elit",
				),
			),
		"h2"	=>	array(
			"label"	=>	array(
				"en"	=>	"Heading 2",
				"jp"	=>	"主な標目",
				"hi"	=>	"प्राथमिक शीर्षक"
				),
			"info"	=>	array(
				"en"	=>	"The secondary headings",
				"jp"	=>	"ページの上部に一度に表示され、メインの見出し",
				"hi"	=>	"एक पृष्ठ के शीर्ष पर एक बार दिखाई देते हैं कि मुख्य शीर्षकोंं",
				),
			),
		"h3"	=>	array(
			"label"	=>	array(
				"en"	=>	"Heading 3",
				"jp"	=>	"主な標目",
				"hi"	=>	"प्राथमिक शीर्षक"
				),
			"info"	=>	array(
				"en"	=>	"The headings that appear in-body",
				"jp"	=>	"ページの上部に一度に表示され、メインの見出し",
				"hi"	=>	"एक पृष्ठ के शीर्ष पर एक बार दिखाई देते हैं कि मुख्य शीर्षकोंं",
				),
			),
		"p"	=>	array(
			"label"	=>	array(
				"en"	=>	"Paragraph",
				"jp"	=>	"段落",
				"hi"	=>	"पैरा"
				),
			"info"	=>	array(
				"en"	=>	"Paragraphs in a body of text",
				"jp"	=>	"テキストの本文の段落",
				"hi"	=>	"पाठ की एक संस्था में अनुच्छेदों",
				),
			),
		),


	///// LESS VARIABLES /////

	"var"	=>	array(
		"grid-gutter-width"	=>	array(
			"label"	=>	array(
				"en"	=>	"Grid Gutter Width",
				),
			"info"	=>	array(
				"en"	=>	"Distance between columns",
				),
			),
		),

	///// CSS PROPERTIES /////
	"property"	=>	array(
		"color"	=>	array(
			"label"	=>	array(
				"en"	=>	"Text Color",
				"jp"	=>	"文字の色",
				"hi"	=>	"पाठ का रंग",
				),
			"info"	=>	array(
				"en"	=>	"CSS / Hexadecimal",
				"jp"	=>	"CSS / 16進数",
				"hi"	=>	"सीएसएस / हेक्साडेसिमल",
				),
			),
		"color:hover"	=>	array(
			"label"	=>	array(
				"en"	=>	"Text Color : Hover",
				"jp"	=>	"文字の色 : にカーソル",
				"hi"	=>	"पाठ का रंग : मंडराना",
				),
			"info"	=>	array(
				"en"	=>	"CSS / Hexadecimal",
				"jp"	=>	"CSS / 16進数",
				"hi"	=>	"सीएसएस / हेक्साडेसिमल",
				),
			),
		"background-color"	=>	array(
			"label"	=>	array(
				"en"	=>	"Background Color",
				"jp"	=>	"背景色",
				"hi"	=>	"पृष्ठभूमि का रंग"
				),
			"info"	=>	array(
				"en"	=>	"CSS / Hexadecimal",
				"jp"	=>	"CSS / 16進数",
				"hi"	=>	"सीएसएस / हेक्साडेसिमल",
				),
			),
		"background-color:hover"	=>	array(
			"label"	=>	array(
				"en"	=>	"Background Color : Hover",
				"jp"	=>	"色 : にカーソル",
				"hi"	=>	"रंग : मंडराना"
				),
			"info"	=>	array(
				"en"	=>	"CSS / Hexadecimal",
				"jp"	=>	"CSS / 16進数",
				"hi"	=>	"सीएसएस / हेक्साडेसिमल",
				),
			),
		"font-family"	=>	array(
			"label"	=>	array(
				"en"	=>	"Font",
				"jp"	=>	"フォント",
				"hi"	=>	"फॉन्ट",
				),
			"info"	=>	array(
				"en"	=>	"Style of the font",
				"jp"	=>	"フォントのスタイル",
				"hi"	=>	"फॉन्ट की स्टाइल",
				),
			),
		"font-size"	=>	array(
			"label"	=>	array(
				"en"	=>	"Font Size",
				"jp"	=>	"フォントサイズ",
				"hi"	=>	"फ़ॉन्ट का आकार",
				),
			"info"	=>	array(
				"en"	=>	"Size of the font in pixels",
				"jp"	=>	"ピクセル単位でのフォントの大きさ",
				"hi"	=>	"पिक्सल में फ़ॉन्ट का आकार",
				),
			),
		"letter-spacing"	=>	array(
			"label"	=>	array(
				"en"	=>	"Letter Spacing",
				"jp"	=>	"文字間隔",
				"hi"	=>	"पत्र रिक्ति",
				),
			"info"	=>	array(
				"en"	=>	"Space between each letter in pixels",
				"jp"	=>	"ピクセル単位で各文字の間にスペース",
				"hi"	=>	"पिक्सल में प्रत्येक अक्षर के बीच अंतरिक्ष",
				),
			),
		"line-height"	=>	array(
			"label"	=>	array(
				"en"	=>	"Line Height",
				"jp"	=>	"行の高さ",
				"hi"	=>	"पंक्ति की ऊंचाई",
				),
			"info"	=>	array(
				"en"	=>	"Space between each line of text in pixels",
				"jp"	=>	"ピクセル単位でテキストの各行の間にスペース",
				"hi"	=>	"पिक्सल में पाठ की प्रत्येक पंक्ति के बीच अंतरिक्ष",
				),
			),
		"text-decoration"	=>	array(
			"label"	=>	array(
				"en"	=>	"Text Decoration",
				"jp"	=>	"テキスト装飾",
				"hi"	=>	"पाठ सजावट",
				),
			"info"	=>	array(
				"en"	=>	"none / underline / overline / line-through / inherit",
				"jp"	=>	"none / underline / overline / line-through / inherit",
				"hi"	=>	"none / underline / overline / line-through / inherit",
				),
			),
		"text-decoration:hover"	=>	array(
			"label"	=>	array(
				"en"	=>	"Text Decoration : Hover",
				"jp"	=>	"テキスト装飾 : にカーソル",
				"hi"	=>	"पाठ सजावट : मंडराना",
				),
			"info"	=>	array(
				"en"	=>	"none / underline / overline / line-through / inherit",
				"jp"	=>	"none / underline / overline / line-through / inherit",
				"hi"	=>	"none / underline / overline / line-through / inherit",
				),
			),
		"margin"	=>	array(
			"label"	=>	array(
				"en"	=>	"Margin",
				"jp"	=>	"余裕",
				"hi"	=>	"हाशिया",
				),
			"info"	=>	array(
				"en"	=>	"Space around the outside",
				"jp"	=>	"外側の周囲の空間",
				"hi"	=>	"बाहर के आसपास की जगह",
				),
			),
		"padding"	=>	array(
			"label"	=>	array(
				"en"	=>	"Padding",
				"jp"	=>	"パディング",
				"hi"	=>	"गद्दी",
				),
			"info"	=>	array(
				"en"	=>	"Space around the inside",
				"jp"	=>	"内側の周囲の空間",
				"hi"	=>	"अंदर चारों ओर अंतरिक्ष",
				),
			),
		)
	);

////////// MERGE CHILD STYLE LANGUAGE //////////
if( isset( $i_child_style_language ) ){
	$i_style_language = array_replace_recursive( $i_style_language, $i_child_style_language );
}

?>