<?php // Template Name: Sandbox [Animation] ?>

<!-- INFINITE HEADER -->
<?php
	i_header();
	global $post;
?>

<?php
////////// PAGE ////////// ?>
<div id="page" class="page-home">
	
	<?php
	////////// CONTENT ////////// ?>
	<div id="content" class="layout full page-bounds" style="margin-top:200px;">


	B:
		<div
			flash-canvas="loops.loading-A"
			canvas-id="loading-ABCD"
			canvas-width="160"
			canvas-height="160"
			canvas-class="animation-loading">
		</div>

		<!--<pre><code><?php //echo json_encode($iGlobals, JSON_PRETTY_PRINT); ?></code></pre>-->

	</div>

</div>


<!-- INFINITE FOOTER -->
<?php i_footer(); ?>

