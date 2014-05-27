<?php
/*_     _       _      _   _ ____  _     
 | |   (_)_ __ | | __ | | | |  _ \| |    
 | |   | | '_ \| |/ / | | | | |_) | |    
 | |___| | | | |   <  | |_| |  _ <| |___ 
 |_____|_|_| |_|_|\_\  \___/|_| \_\_____|
/////////////////////////////////////////*/

//global $post;
//echo $post->ID;

?>
<style>
	.link_url_field{
		box-sizing:border-box;
		width:100%;	
	}

	.link_format{
		margin-top:5px;
	}
	
	.link_format label{
			padding:5px 10px;
			display: inline-block;
		}
	.link_format input[type="radio"]{
			display:none;
		}
	.link_format input[type="radio"]:checked+label{
			background:#444;
			color:#fff;
			border-radius:3px;
		}


</style>

<script>
	///// LINK URL CONTROLLER /////
	postworld.controller('linkUrlCtrl',
		['$scope', 'pwEditPostFilters', 'pwPostOptions',
			function($scope, $pwEditPostFilters, $pwPostOptions ) {

		$scope.post = {
			link_url: '<?php echo ( $link_url != '') ? $link_url : ''; ?>',
			link_format: '<?php echo ( $link_format != '') ? $link_format : ''; ?>',
		};

		// Get Link Format Meta
		$scope.link_format_meta = $pwPostOptions.pwGetLinkFormatMeta();

		// LINK_URL WATCH : Watch for changes in link_url
		// Evaluate the link_format
		$scope.$watchCollection('[post.link_url, post.link_format]',
			function (){
				$scope.post.link_format = $pwEditPostFilters.evalPostFormat( $scope.post.link_url );
			});

	}]);
</script>

<div ng-app="postworld">
	<div ng-controller="linkUrlCtrl">

		<!-- LINK URL -->
		<input
			type="text"
			name="link_url"
			autocomplete="off"
			class="link_url link_url_field half_length"
			value="<?php echo ( $link_url != '') ? $link_url : ''; ?>"
			ng-model="post.link_url"
			placeholder="http://">
	

		<!-- LINK FORMAT -->
		<div class="link_format">

			<span ng-repeat="format in link_format_meta" class="row-fluid">
					<input
						type="radio"
						id="link_format"
						name="link_format"
						value="{{format.slug}}"
						ng-model="post.link_format"
						/> <!-- disabled  -->
					<label>
						<i class="{{format.icon}}"></i> <span ng-bind="format.name"></span>
					</label>
			</span>

			<!--
			<span ng-show="post.link_url.length">
				<span  style="float:right; padding:5px 10px;"><a href="{{post.link_url}}" target="_blank">View <i class="icon-external-link"></i></a></span>
			</span>
			-->
		</div>

	<!--{{ post | json }}-->

	</div>
	
</div>





