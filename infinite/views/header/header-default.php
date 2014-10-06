<?php
// Template Name: Default Header
// Template Description: The main header for all pages.
?>


<!DOCTYPE html>
<html  <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1" />
	<title><?php wp_title(' | ', true, 'right'); ?></title>
	
	<link rel="icon" type="image/png" href="/favicon.png">

	<?php wp_head(); ?>

</head>

<body ng-app="postworld">

	<?php
		//echo "context : " . $i_context . "<br>";
		//echo "<pre>layout : " . json_encode($i_layout , JSON_PRETTY_PRINT) . "<br></pre>";
		//echo "sidebars : " . json_encode($i_sidebars) . "<br>";
		//echo "templates : " . json_encode( i_get_templates() , JSON_PRETTY_PRINT);
	?>

<div id="background"></div>


	<!-- HEADER / NAVIGATION -->
	<header id="header">
		<div class="column-left">
			<h2><?php echo get_bloginfo('name'); ?></h2>
		</div>
	</header>

	<div class="clearfix"></div>

	<div id="page" class="layout left full">

		<!-- CONTENT -->
		<div id="content">
