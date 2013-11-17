<?
/*
  ____           _     ____  _                      ____                       _   
 |  _ \ ___  ___| |_  / ___|| |__   __ _ _ __ ___  |  _ \ ___ _ __   ___  _ __| |_ 
 | |_) / _ \/ __| __| \___ \| '_ \ / _` | '__/ _ \ | |_) / _ \ '_ \ / _ \| '__| __|
 |  __/ (_) \__ \ |_   ___) | | | | (_| | | |  __/ |  _ <  __/ |_) | (_) | |  | |_ 
 |_|   \___/|___/\__| |____/|_| |_|\__,_|_|  \___| |_| \_\___| .__/ \___/|_|   \__|
                                                             |_|                   
///////////////////////////// POST SHARE REPORT VIEW /////////////////////////////*/

$post_id = $GLOBALS['post']->ID;

?>

<script>
	// VIEW CONTROLLER
	var postReportMeta = function ( $scope ) {
		$scope.postShareReport = <?php echo json_encode( post_share_report_meta( post_share_report( $post_id ) ) ); ?>;
	};
</script>

<div ng-controller="postReportMeta" ng-cloak>

	<!-- INCOMING SHARES -->
	<ul class="user-list">
		<li ng-repeat="share in postShareReport | orderBy: '-last_time'">
			

			<!-- PER USER -->
			<span class="metadata" tooltip="Total shares by this user" tooltip-placement="bottom">
				<i class="icon-share-alt sm"></i> {{ share.shares }}
			</span>
			<a class="metadata" href="{{ share.user.user_profile_url }}" target="_blank" tooltip="@{{ share.user.user_nicename }}" tooltip-placement="bottom">
				{{ share.user.display_name }}
			</a>
			<span class="metadata" tooltip="Most recent share" tooltip-placement="bottom">
				<i class="icon-time sm"></i>
				{{ share.last_time | timeago }}
			</span>
		


		</li>
	</ul>


</div>

