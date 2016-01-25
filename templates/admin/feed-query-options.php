<div class="pw-row">
	<div class="pw-col-3">
		<label
			for="query-post_type"
			class="inner">
			<?php _e( 'Post Type', 'postworld' ) ?>
		</label>
		<select
			id="query-post_type"
			class="labeled"
			ng-options="key as value for (key, value) in feedOptions.query.post_type"
			ng-model="<?php echo $vars['ng_model'] ?>.query.post_type"
			multiple>
		</select>
	</div>
	<div class="pw-col-3">
		<label
			for="query-post_status"
			class="inner">
			<?php _e( 'Post Status', 'postworld' ) ?>
		</label>
		<select
			id="query-post_status"
			class="labeled"
			ng-options="item.slug as item.name for item in feedOptions.query.post_status"
			ng-model="<?php echo $vars['ng_model'] ?>.query.post_status">
		</select>
	</div>
	<div class="pw-col-3">
		<label
			for="query-post_class"
			class="inner">
			<?php _e( 'Post Class', 'postworld' ) ?>
		</label>
		<select
			id="query-post_class"
			class="labeled"
			ng-options="key as value for (key, value) in postClassOptions()"
			ng-model="<?php echo $vars['ng_model'] ?>.query.post_class">
			<option value="">Any</option>
		</select>
	</div>
	<div class="pw-col-3">
		<label
			for="query-offset"
			class="inner"
			uib-tooltip="<?php _e( 'How many posts to skip at the MySQL level', 'postworld' ) ?>"
			tooltip-popup-delay="333">
			<?php _e( 'Offset', 'postworld' ) ?>
			<i class="pwi-info-circle"></i>
		</label>
		<input
			id="query-offset"
			class="labeled"
			type="number"
			ng-model="<?php echo $vars['ng_model'] ?>.query.offset">
	</div>
	<div class="pw-col-3">
		<label
			for="query-orderby"
			class="inner">
			<?php ___('query.orderby'); ?>
		</label>
		<select
			id="query-orderby"
			class="labeled"
			ng-options="item.slug as item.name for item in feedOptions.query.orderby"
			ng-model="<?php echo $vars['ng_model'] ?>.query.orderby">
			
		</select>
	</div>
	<div class="pw-col-3">
		<label
			for="query-order"
			class="inner">
			<?php ___('query.order'); ?>
		</label>
		<select
			id="query-order"
			class="labeled"
			ng-options="item.slug as item.name for item in feedOptions.query.order"
			ng-model="<?php echo $vars['ng_model'] ?>.query.order">
		</select>
	</div>
	<div class="pw-col-3">
		<label
			for="query-posts_per_page"
			class="inner"
			uib-tooltip="<?php ___('query.posts_per_page_info'); ?>"
			tooltip-popup-delay="333">
			<?php ___('query.posts_per_page'); ?>
			<i class="pwi-info-circle"></i>
		</label>
		<input
			id="query-posts_per_page"
			class="labeled"
			type="number"
			ng-model="<?php echo $vars['ng_model'] ?>.query.posts_per_page">
	</div>

	<div class="pw-col-3">
		<label
			for="query-event_filter"
			class="inner">
			<i class="pwi-calendar"></i>
			<?php ___('query.event_filter'); ?>
		</label>
		<select
			id="query-event_filter"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.event_filter"
			ng-model="<?php echo $vars['ng_model'] ?>.query.event_filter">
			<option value=""><?php ___('general.none'); ?></option>
		</select>
	</div>

</div>

<div class="pw-row">
	<div class="pw-col-3">
		<label
			for="query-post_parent_from"
			class="inner">
			<i class="pwi-flow-children"></i>
			<?php ___('query.post_parent'); ?>
		</label>
		<select
			id="query-post_parent_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.post_parent_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.post_parent_from"
			uib-tooltip="{{ selectOptionObj( 'query.post_parent_from' ).description }}"
			tooltip-placement="bottom">
			<option value=""><?php ___('general.none'); ?></option>
		</select>
	</div>

	<div class="pw-col-3" ng-show="<?php echo $vars['ng_model'] ?>.query.post_parent_from == 'post_id'">
		<label
			for="query-post_parent_id"
			class="inner"
			uib-tooltip="<?php ___('query.post_parent_id_info'); ?>"
			tooltip-popup-delay="333">
			<?php ___('query.post_parent_id'); ?>
		</label>
		<input
			id="query-post_parent_id"
			class="labeled"
			type="number"
			ng-model="<?php echo $vars['ng_model'] ?>.query.post_parent">
	</div>

	<div class="pw-col-3">
		<label
			for="query-exclude_posts_from"
			class="inner">
			<?php ___('query.exclude_posts'); ?>
		</label>
		<select
			id="query-exclude_posts_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.exclude_posts_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.exclude_posts_from"
			uib-tooltip="{{ selectOptionObj( 'query.exclude_posts_from' ).description }}"
			tooltip-placement="bottom">
			<option value=""><?php ___('general.none'); ?></option>
		</select>
	</div>

	<div class="pw-col-3">
		<label
			for="query-include_posts_from"
			class="inner">
			<?php ___('query.include_posts'); ?>
		</label>
		<select
			id="query-include_posts_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.include_posts_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.include_posts_from"
			uib-tooltip="{{ selectOptionObj( 'query.include_posts_from' ).description }}"
			tooltip-placement="bottom">
			<option value=""><?php ___('general.none'); ?></option>
		</select>
	</div>

</div>
<div class="pw-row">

	<div class="pw-col-3">
		<label
			for="query-author_from"
			class="inner">
			<i class="pwi-user"></i>
			<?php ___('query.author_from'); ?>
		</label>
		<select
			id="query-author_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.author_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.author_from"
			uib-tooltip="{{ selectOptionObj( 'query.author_from' ).description }}"
			tooltip-placement="bottom">
			<option value=""><?php ___('general.none'); ?></option>
		</select>
	</div>

	<div class="pw-col-3" ng-show="<?php echo $vars['ng_model'] ?>.query.author_from == 'author_id'">
		<label
			for="query-author"
			class="inner"
			uib-tooltip="<?php ___('query.author_id_info'); ?>"
			tooltip-popup-delay="333">
			<?php ___('query.author_id'); ?>
		</label>
		<input
			id="query-author"
			class="labeled"
			type="number"
			ng-model="<?php echo $vars['ng_model'] ?>.query.author">
	</div>

</div>

<hr class="thin">

<button
	type="button"
	class="button"
	ng-click="addTaxQuery(<?php echo $vars['ng_model'] ?>.query)">
	<i class="pwi-plus"></i> Taxonomy Query
</button>

<hr class="thin">

<div
	ng-repeat="taxQuery in selectedItem.query.tax_query">

	<div class="pw-row">
		<div class="pw-col-3">

			<label
				for="query-taxonomy"
				class="inner">
				<?php ___('query.taxonomy'); ?>
			</label>

			<select
				class="labeled"
				id="select-feature_tax"
				ng-model="taxQuery.taxonomy"
				ng-options="key as tax.labels.name for (key,tax) in taxTerms">
				<option value="">Select Taxonomy</option>
			</select>

		</div>

		<div class="pw-col-3">

			<label for="select-feature_term" class="inner">
				<i class="pwi-search"></i> term
			</label>
			<select
				class="labeled"
				id="select-feature_term"
				ng-model="taxQuery.terms"
				ng-options="term.term_id as term.name group by term.parent_name for term in taxTerms[ taxQuery.taxonomy ].terms"
				multiple>
				<option value="">Select Term</option>
			</select>

		</div>

		<div class="pw-col-3">
			<label>
				<input
					type="checkbox"
					ng-model="taxQuery.include_children">
				Include Children
			</label>

			<hr class="thin">

			Operator:
			<select
				ng-model="taxQuery.operator">
				<option value="IN">IN</option>
				<option value="NOT IN">NOT IN</option>
				<option value="AND">AND</option>
			</select>

		</div>

		<div class="pw-col-3">
			<button
				type="button"
				class="button"
				ng-click="removeTaxQuery(<?php echo $vars['ng_model'] ?>.query, taxQuery )">
				<i class="pwi-close-thin"></i>
			</button>
		</div>
	</div>

</div>
