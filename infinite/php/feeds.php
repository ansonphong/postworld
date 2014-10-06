<?php
	// Include a Postworld Feed Template from templates/feeds
	function i_pw_feed_template( $feed_template, $query = array() ){
		global $i_paths;
		global $iGlobals;

		ob_start();
		//include $i_paths['infinite']['dir'].'/php/setup-archive.php';
		include $i_paths['templates']['dir']['override'] . '/feeds/' . $feed_template . '.php';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
?>