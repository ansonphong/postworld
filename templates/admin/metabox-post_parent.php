<div
	class=""
	pw-ui
	ui-views="{ searchInput: false }">

	<input
		type="number"
		placeholder="ID"
		ng-model="post.post_parent">

	<div ng-show="!showView('searchInput')">
		<button
			type="button"
			class="button"
			ng-click="toggleView('searchInput'); focusElement('#pwSearchPostParent')">
			<i class="icon-search"></i>
			Search
		</button>
	</div>

	<div ng-show="showView('searchInput')" style="position:relative;">
		<div ng-show="loadingQuery" class="inner inner-right unit">
			<i class="icon-spinner-2 icon-spin"></i>
		</div>
		<input
			id="pwSearchPostParent"
			type="text"
			ng-model="searchQuery"
			placeholder="Search Posts..."
			typeahead="qPost.post_title as qPost.post_title for qPost in getPosts($viewValue) | filter:$viewValue"
			typeahead-loading="loadingQuery"
			typeahead-on-select="addPostParent($item);toggleView('searchInput');"
			ng-blur="toggleView('searchInput');">
	</div>

	

</div>