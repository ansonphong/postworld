<div
	pw-ui
	ui-views="{ searchInput: false }">

	<div ng-show="!showView('searchInput')">
		<button
			type="button"
			class="button"
			ng-click="toggleView('searchInput'); focusElement('#pwSearchPostParent')">
			<i class="{{ labels.search_icon }}"></i>
			{{ labels.search }}
		</button>
	</div>

	<div ng-show="showView('searchInput')" style="position:relative;">
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
			typeahead="qPost.post_title as qPost.post_title for qPost in getPosts($viewValue) | filter:$viewValue"
			typeahead-loading="loadingQuery"
			typeahead-on-select="addPostParent($item);"
			ng-blur="toggleView('searchInput');"
			class="input-icon-left">
	</div>

</div>