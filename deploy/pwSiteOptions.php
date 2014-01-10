<?php
header("Content-Type: text/javascript");
?>


//alert("postworld site options");


var pwGlobals = <?php echo json_encode( parse_pw_globals() ); ?>;

