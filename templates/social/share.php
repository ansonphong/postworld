<?php
	$s = pw_get_social_share_meta( $vars );
	// TODO : REFACTOR AS FOREACH
	// ACCESS CENTRAL SOCIAL NETWORK INFO DATAS / LANGUAGING - USE LIKE PW DATA FOR PHP
	// IE. "Share on" & "Google Plus" & "icon-google-plus"
?>

<?php if( array_key_exists( 'facebook', $s ) ){ ?> 
	<span class="pull-left" tooltip="Share on Facebook" tooltip-popup-delay="500" tooltip-placement="bottom">
		<a href="<?php echo _get( $s, 'facebook.link' ); ?>" target="_blank">
			<i class="icon icon-facebook-square"></i>
		</a>
	</span>
<?php } ?>

<?php if( array_key_exists( 'twitter', $s ) ){ ?> 
	<span class="pull-left" tooltip="Share on Twitter" tooltip-popup-delay="500" tooltip-placement="bottom">
		<a href="<?php echo _get( $s, 'twitter.link' ); ?>" target="_blank">
			<i class="icon icon-twitter-square"></i>
		</a>
	</span>
<?php } ?>

<?php if( array_key_exists( 'reddit', $s ) ){ ?> 
	<span class="pull-left" tooltip="Share on Reddit" tooltip-popup-delay="500" tooltip-placement="bottom">
		<a href="<?php echo _get( $s, 'reddit.link' ); ?>" target="_blank">
			<i class="icon icon-reddit-square"></i>
		</a>
	</span>
<?php } ?>

<?php if( array_key_exists( 'google_plus', $s ) ){ ?> 
	<span class="pull-left" tooltip="Share on Google Plus" tooltip-popup-delay="500" tooltip-placement="bottom">
		<a href="<?php echo _get( $s, 'google_plus.link' ); ?>" target="_blank">
			<i class="icon icon-google-plus-square"></i>
		</a>
	</span>
<?php } ?>

<?php if( array_key_exists( 'pinterest', $s ) ){ ?> 
	<span class="pull-left" tooltip="Share on Pinterest" tooltip-popup-delay="500" tooltip-placement="bottom">
		<a href="<?php echo _get( $s, 'pinterest.link' ); ?>" target="_blank">
			<i class="icon icon-pinterest-square"></i>
		</a>
	</span>
<?php } ?>
