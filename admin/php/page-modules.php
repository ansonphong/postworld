
<div class="postworld social" ng-cloak>

	<h1>
		<i class="pwi-postworld"></i>
		Postworld
	</h1>
	
	<hr class="thick">

	<div>
		<!-- MODULES -->
		<?php echo pw_select_modules(); ?>
	</div>

	<?php if( pw_dev_mode() ): ?>
		<hr class="thick">
		<div class="well">
			<h3><i class="pwi-merkaba"></i> Dev Mode</h3>

			<!-- META BOXES -->
			<h4>Metaboxes</h4>
			<ul>
				<li>Post Parent</li>
				<li>Link URL</li>
			</ul>
		</div>
	<?php endif; ?>

	

</div>

