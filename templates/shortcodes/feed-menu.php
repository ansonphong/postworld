<div class="grid feed view feed">
	<?php
	$fields = "all";
	$feed = pw_print_menu_feed( array( "menu" => $name, "fields" => $fields, "view" => "grid-h2o" ) );
	echo $feed;
	?>
</div>