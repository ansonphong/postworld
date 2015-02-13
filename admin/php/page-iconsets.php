<div
	pw-admin
	pw-admin-iconsets
	ng-cloak
	class="postworld">
	<h1>
		<i class="icon-circle-medium"></i>
		Iconsets
	</h1>

	<li>ADD OPTION : Enable Iconset Shortcodes (select from iconsets)</li>
	<li>ADD OPTION : Clear iconset cache (delete all options)</li>

	<?php if( pw_dev_mode() ): ?>
		<hr>
		<div class="well">
			<h3>Loaded Iconsets</h3>
			<pre><code><?php echo json_encode( pw_get_iconsets(), JSON_PRETTY_PRINT ) ?></code></pre>
		</div>
	<?php endif; ?>

</div>