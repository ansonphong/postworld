<?php
	global $pw;
?>

<?php do_action( 'postworld_admin_header' ) ?>

<div class="postworld modules wrap" ng-cloak>

	<h1 class="primary">
		<i class="icon pwi-cubes"></i>
		<?php _e( 'Modules', 'postworld' ) ?>
		<span class="pw-version" style="font-size:.66em; font-weight: lighter;">
			• Postworld
			v<?php echo (string) $pw['info']['version'] ?>
		</span>
	</h1>
	
	<hr class="thick">
	
	<div class="pw-cloak">

		<div>
			<!-- MODULES -->
			<?php echo pw_select_modules(); ?>
		</div>

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

		

	</div>

</div>

