<div class="grid feed view">
	<?php
	$fields = "preview";
	$feed = pw_print_menu_feed(
		array(
			"menu" => $name,
			"fields" => $fields,
			"view" => "grid-h2o"
			)
		);
	echo $feed;
	?>
</div>