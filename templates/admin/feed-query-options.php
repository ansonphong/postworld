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
			<option value=""><?php _ex( 'Any', 'option', 'postworld' ) ?></option>
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
			<?php _e( 'Order By', 'postworld' ) ?>
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
			<?php _e( 'Order', 'postworld' ) ?>
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
			uib-tooltip="<?php _e( 'Maximum number of posts', 'postworld' ) ?>"
			tooltip-popup-delay="333">
			<?php _e( 'Maximum Posts', 'postworld' ) ?>
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
			<?php _e( 'Event Filter', 'postworld' ) ?>
		</label>
		<select
			id="query-event_filter"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.event_filter"
			ng-model="<?php echo $vars['ng_model'] ?>.query.event_filter">
			<option value=""><?php _ex( 'None', 'option', 'postworld' ) ?></option>

		</select>
	</div>

</div>

<div class="pw-row">
	<div class="pw-col-3">
		<label
			for="query-post_parent_from"
			class="inner">
			<i class="pwi-flow-children"></i>
			<?php _e( 'Post Parent', 'postworld' ) ?>
		</label>
		<select
			id="query-post_parent_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.post_parent_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.post_parent_from"
			uib-tooltip="{{ selectOptionObj( 'query.post_parent_from' ).description }}"
			tooltip-placement="bottom">
			<option value="">
				<?php _ex( 'None', 'option', 'postworld' ) ?>
			</option>
		</select>
	</div>

	<div class="pw-col-3" ng-show="<?php echo $vars['ng_model'] ?>.query.post_parent_from == 'post_id'">
		<label
			for="query-post_parent_id"
			class="inner"
			uib-tooltip="<?php _e( 'Enter the ID of the parent post', 'postworld' ) ?>"
			tooltip-popup-delay="333">
			<?php _e( 'Post Parent ID', 'postworld' ) ?>
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
			<?php _e( 'Exlude Posts', 'postworld' ) ?>
		</label>
		<select
			id="query-exclude_posts_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.exclude_posts_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.exclude_posts_from"
			uib-tooltip="{{ selectOptionObj( 'query.exclude_posts_from' ).description }}"
			tooltip-placement="bottom">
			<option value=""><?php _ex( 'None', 'option', 'postworld' ) ?></option>
		</select>
	</div>

	<div class="pw-col-3">
		<label
			for="query-include_posts_from"
			class="inner">
			<?php _e( 'Include Posts', 'postworld' ) ?>
		</label>
		<select
			id="query-include_posts_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.include_posts_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.include_posts_from"
			uib-tooltip="{{ selectOptionObj( 'query.include_posts_from' ).description }}"
			tooltip-placement="bottom">
			<option value=""><?php _ex( 'None', 'option', 'postworld' ) ?></option>
		</select>
	</div>

</div>
<div class="pw-row">

	<div class="pw-col-3">
		<label
			for="query-author_from"
			class="inner">
			<i class="pwi-user"></i>
			<?php _e( 'Author', 'postworld' ) ?>
		</label>
		<select
			id="query-author_from"
			class="labeled"
			ng-options="item.value as item.name for item in feedOptions.query.author_from"
			ng-model="<?php echo $vars['ng_model'] ?>.query.author_from"
			uib-tooltip="{{ selectOptionObj( 'query.author_from' ).description }}"
			tooltip-placement="bottom">
			<option value=""><?php _ex( 'None', 'option', 'postworld' ) ?></option>
		</select>
	</div>

	<div class="pw-col-3" ng-show="<?php echo $vars['ng_model'] ?>.query.author_from == 'author_id'">
		<label
			for="query-author"
			class="inner"
			uib-tooltip="<?php _e( 'The user ID of the author', 'postworld' ) ?>"
			tooltip-popup-delay="333">
			<?php _e( 'Author ID', 'postworld' ) ?>
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
	<i class="pwi-plus"></i>
	<?php _e( 'Taxonomy Query', 'postworld' ) ?>
</button>

<hr class="thin">

<div
	ng-repeat="taxQuery in selectedItem.query.tax_query">

	<div class="pw-row">
		<div class="pw-col-3">

			<label
				for="query-taxonomy"
				class="inner">
				<?php _e( 'Taxonomy', 'postworld' ) ?>
			</label>

			<select
				class="labeled"
				id="select-feature_tax"
				ng-model="taxQuery.taxonomy"
				ng-options="key as tax.labels.name for (key,tax) in taxTerms">
				<option value=""><?php _e( 'Select Taxonomy', 'postworld' ) ?></option>
			</select>

		</div>

		<div class="pw-col-3">

			<label for="select-feature_term" class="inner">
				<?php _ex( 'Term(s)', 'as in taxonomy term', 'postworld' ) ?>
			</label>
			<select
				class="labeled"
				id="select-feature_term"
				ng-model="taxQuery.terms"
				ng-options="term.term_id as term.name group by term.parent_name for term in taxTerms[ taxQuery.taxonomy ].terms"
				multiple>
			</select>

		</div>

		<div class="pw-col-3">
			<label>
				<input
					type="checkbox"
					ng-model="taxQuery.include_children">
				<?php _ex( 'Include Children', 'as in child posts', 'postworld' ) ?>
			</label>

			<hr class="thin">
			<?php _ex( 'Operator', 'logical operator', 'postworld' ) ?> :
			<select
				ng-model="taxQuery.operator">
				<option value="IN"><?php _ex( 'IN', 'logical operator', 'postworld' ) ?></option>
				<option value="NOT IN"><?php _ex( 'NOT IN', 'logical operator', 'postworld' ) ?></option>
				<option value="AND"><?php _ex( 'AND', 'logical operator', 'postworld' ) ?></option>
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
