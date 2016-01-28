<?php
	global $pw;
?>
<div class="postworld modules" ng-cloak>

	<h1>
		<i class="pwi-postworld"></i>
		<?php _e( 'Postworld', 'postworld' ) ?>
		<span class="pw-version" style="font-size:.66em; font-weight: lighter;">
			v<?php echo (string) $pw['info']['version'] ?>
		</span>
	</h1>
	
	<hr class="thick">
	
	<div class="well">
		<h2>
			<i class="icon pwi-postworld"></i>
			<?php _e( 'About', 'postworld' ) ?>
		</h2>
		<div class="well">
			<?php _e( 'Postworld is an open source WordPress Theme building framework.', 'postworld' ) ?>
			<a href="https://github.com/phongmedia/postworld" target="_blank">
				<?php _e( 'Visit Postworld on GitHub.', 'postworld' ) ?>
				<i class="icon pwi-external-link"></i>
			</a>
		</div>
	</div>

	<div>
		<!-- MODULES -->
		<?php echo pw_select_modules(); ?>
	</div>

</div>

