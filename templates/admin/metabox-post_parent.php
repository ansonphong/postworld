<div
	pw-ui
	ui-views="{ searchInput: false }">
	<!-- DISPLAY PARENT POST -->
	<div ng-show="parent_post">
		<div class="align-center">
			<img
				ng-show="parent_post.image.sizes.thumbnail.url"
				ng-src="{{ parent_post.image.sizes.thumbnail.url }}"
				style="width:150px; height:150px;">
		</div>
		<h3>
			{{ parent_post.post_title }}
			<br>
			<small>
				TYPE : {{ parent_post.post_type }} /
				ID : {{ parent_post.ID }}
				<br>
				{{ parent_post.post_timestamp * 1000 | date:"longDate" }}
			</small> 
		</h3>
		<hr class="thin">
		<button
			type="button"
			class="button button-small float-right"
			ng-click="removePostParent();">
			<i class="{{ labels.remove_icon }}"></i>
			{{ labels.remove }}
		</button>
		<a ng-href="{{ parent_post.edit_post_link }}" target="_blank">
			<i class="{{ labels.edit_icon }}"></i>
			{{ labels.edit }}
		</a>
		&nbsp;
		<a ng-href="{{ parent_post.post_permalink }}" target="_blank">
			<i class="{{ labels.view_icon }}"></i>
			{{ labels.view }}
		</a>
	</div>

	<!-- SELECT PARENT POST -->
	<div ng-hide="parent_post">
		<div ng-show="!uiShowView('searchInput')">
			<button
				type="button"
				class="button"
				ng-click="uiToggleView('searchInput',200); uiFocusElement('#pwSearchPostParent', 200)">
				<i class="{{ labels.search_icon }}"></i>
				{{ labels.search }}
			</button>
		</div>
		<div ng-show="uiShowView('searchInput')" style="position:relative;">
			<div ng-show="loadingQuery" class="inner inner-right unit">
				<i class="{{labels.loading_icon}}"></i>
			</div>
			<label for="location" class="input-icon">
				<i class="{{ labels.search_icon }}"></i>
			</label>
			<input
				id="pwSearchPostParent"
				type="text"
				ng-model="searchQuery"
				placeholder="{{ labels.search }}"
				uib-typeahead="qPost.post_title as qPost.post_title for qPost in getPosts($viewValue)"
				typeahead-loading="loadingQuery"
				typeahead-on-select="addPostParent($item);"
				typeahead-focus-first="false"
				ng-blur="uiToggleView('searchInput',200)"
				class="input-icon-left">
		</div>
	</div>
</div>