<?php
	global $pw;
?>
<div class="postworld modules" ng-cloak>

	<h1>
		<i class="pwi-postworld"></i>
		Postworld
		<span class="pw-version" style="font-size:.66em; font-weight: lighter;">
			v<?php echo $pw['info']['version'] ?>
		</span>
	</h1>
	
	<hr class="thick">
	
	<div>
		<!-- MODULES -->
		<?php echo pw_select_modules(); ?>
	</div>

	<div class="well">
		<h2>
			<i class="icon pwi-postworld"></i>
			About
		</h2>
		<div class="well">
			Postworld is an open source WordPress theme building framework created by <a href="https://phong.com" target="_blank">Phong Media Design.</a>
			Visit <a href="https://github.com/phongmedia/postworld" target="_blank"> Postworld on GitHub.</a>
		</div>
	</div>

	<?php if( pw_dev_mode() ): ?>
		<hr class="thick">
		<div class="well">
			<h3><i class="icon pwi-merkaba"></i> Dev Mode</h3>

			<!-- META BOXES -->
			<h4>Metaboxes</h4>
			<ul>
				<li>Post Parent</li>
				<li>Link URL</li>
			</ul>
		</div>
	<?php endif; ?>

	

</div>

