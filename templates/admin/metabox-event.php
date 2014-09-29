<div
	pw-event-input
	start-date-obj="post.post_meta[ eventKey ].date.start_date_obj"
	end-date-obj="post.post_meta[ eventKey ].date.end_date_obj"
	start-date="post.post_meta[ eventKey ].date.start_date"
	end-date="post.post_meta[ eventKey ].date.end_date">

	<div class="pw-row">
		<div class="pw-col-6">

			<h3>Event Start</h3>
			<!-- DATE DROPDOWN -->
			<span class="time dropdown">
				<button dropdown-toggle class="button">
					<i class="icon-calendar"></i>
					{{ post.post_meta[ eventKey ].date.start_date_obj | date:'MMMM dd, yyyy' }}
				</button>
				<!--<input type="hidden" ng-model="post.post_meta.date_obj.event_start_date_obj" dropdown-toggle>-->
				<ul class="dropdown-menu pull-left"  prevent-default-click>
					<div class="well well-small pull-left stay-open">
						<datepicker
							ng-model="post.post_meta[ eventKey ].date.start_date_obj"
							show-weeks="false">
						</datepicker>
					</div>
				</ul>
			</span>

		</div>
		<div class="pw-col-6">
			
			<h3>Event End</h3>
			<!-- DATE DROPDOWN -->
			<span class="time dropdown">
				<button dropdown-toggle class="button">
					<i class="icon-calendar"></i>
					{{ post.post_meta[ eventKey ].date.end_date_obj | date:'MMMM dd, yyyy' }}
				</button>
				<!--<input type="hidden" ng-model="post.post_meta.date_obj.event_start_date_obj" dropdown-toggle>-->
				<ul class="dropdown-menu pull-left"  prevent-default-click>
					<div class="well well-small pull-left stay-open">
						<datepicker
							ng-model="post.post_meta[ eventKey ].date.end_date_obj"
							show-weeks="false">
						</datepicker>
					</div>
				</ul>
			</span>

		</div>
	</div>

</div>

<hr>


<!-- LOCATION -->
<div
	pw-geo-autocomplete
	
	pw-geo-input
	geo-post="post"
	geo-location-obj="post.post_meta[ eventKey ].location"
	geo-return-obj=""

	class="input_module labeled">

	<div
		pw-ui
		ui-views="{ searchInput: false }"
		class="well">
		
		<!-- UI VIEWS : <pre>{{ uiViews | json }}</pre><hr> -->
		
		<!-- SEARCH BUTTON -->
		<div
			ng-show="!showView('searchInput')">
			<button
				id="searchLocations"
				class="button float-left"
				name="search"
				type="button"
				ng-click="toggleView('searchInput'); focusElement('#searchBarInput')">
				<i class="icon-search"></i> Search Locations
			</button>
			<h4 class="float-left unit">
				<i>Autocomplete Geocode</i>
			</h4>
			<div class="clearfix"></div>
		</div>
		
		<!-- SEARCH INPUT -->
		<div
			ng-show="showView('searchInput')"
			style="position:relative;">
			<label for="location" class="inner inner-right unit">
					<i ng-show="loadingLocations" class="icon-spinner-2 icon-spin"></i>
			</label>
			<label for="location" class="input-icon">
				<i class="icon-search"></i>
			</label>
			<input
				id="searchBarInput"
				type="text"
				ng-model="post.post_meta[ eventKey ].location.geocode"
				placeholder="Type Location..."
				typeahead="address.formatted_address as address.formatted_address for address in getLocation($viewValue) | filter:$viewValue"
				typeahead-loading="loadingLocations"
				typeahead-on-select="addGeocode($item);"
				placeholder=""
				ng-blur="toggleView('searchInput');"
				class="input-icon-left">
			<!--<button
				class="button"
				type="button"
				ng-click="toggleView('searchInput');">
				<i class="icon-close"></i>
			</button>-->
		</div>
	
	</div>

		<!-- TODO :
		<div ng-show="showView('mapUnit')">
			<google-map
				id="googleMap"
				center='map.center'
				zoom='map.zoom'
				draggable='map.draggable'
				options='map.options'
				events="map.events">
				<marker
					coords='marker'
					options='marker.options'
					click="clickMarker()">
				</marker>
			</google-map>
		</div>
	-->

</div>

<div class="pw-row">
	<div class="pw-col-6">
		<h3>Location</h3>
		<div class="pw-row">
			<div class="pw-col-12">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].location.name"
					placeholder="Name">
			</div>
		</div>
		<div class="pw-row">
			<div class="pw-col-12">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].location.address"
					placeholder="Address">
			</div>
		</div>
		<div class="pw-row">
			<div class="pw-col-6">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].location.city"
					placeholder="City">
			</div>
			<div class="pw-col-6">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].location.region"
					placeholder="State/Province">
			</div>
		</div>
		<div class="pw-row">
			<div class="pw-col-6">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].location.country"
					placeholder="Country">
			</div>
			<div class="pw-col-6">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].location.postal_code"
					placeholder="Postal Code">
			</div>
		</div>
		<div class="pw-row">
			<div class="pw-col-6">
				<h4>Latitude</h4>
				<input
					type="text"
					ng-model="post.geo_latitude"
					placeholder="0.000">
			</div>
			<div class="pw-col-6">
				<h4>Longitude</h4>
				<input
					type="text"
					ng-model="post.geo_longitude"
					placeholder="0.000">
			</div>
		</div>
	</div>
	<div class="pw-col-6">
		<h3>Organizer</h3>
		<div class="pw-row">
			<div class="pw-col-12">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].organizer.name"
					placeholder="Name">
			</div>
		</div>

		<div class="pw-row">
			<div class="pw-col-6">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].organizer.phone"
					placeholder="Phone">
			</div>
			<div class="pw-col-6">
				<input
					type="text"
					ng-model="post.post_meta[ eventKey ].organizer.email"
					placeholder="Email">
			</div>
		</div>
		<div class="pw-row">
			<div class="pw-col-12">
				<input
					type="url"
					ng-model="post.post_meta[ eventKey ].organizer.link_url"
					placeholder="Social Profile Link">
			</div>
		</div>
	</div>
</div>


<hr class="thin">

<div class="pw-row">
	<h3>Details</h3>
	<div class="pw-col-10">
		<input
			type="url"
			ng-model="post.post_meta[ eventKey ].details.link_url"
			placeholder="http://">
	</div>
	<div class="pw-col-2">	
		<input
			type="text"
			ng-model="post.post_meta[ eventKey ].details.cost"
			placeholder="Cost">
	</div>
</div>



<hr>
