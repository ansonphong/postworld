<div
	pw-event-input
	start-date-obj="post.post_meta[ eventKey ].date.start_date_obj"
	end-date-obj="post.post_meta[ eventKey ].date.end_date_obj"
	start-date="post.post_meta[ eventKey ].date.start_date"
	end-date="post.post_meta[ eventKey ].date.end_date">

	Event Start :
	<!-- DATE DROPDOWN -->
	<span class="time dropdown">
		<button dropdown-toggle class="btn btn-blue">
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


	Event End :
	<!-- DATE DROPDOWN -->
	<span class="time dropdown">
		<button dropdown-toggle class="btn btn-blue">
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

<hr>

<input
	ng-model="post.post_meta[ eventKey ].location.name"
	placeholder="Venue Name">

<input
	ng-model="post.post_meta[ eventKey ].location.address"
	placeholder="Address">

<input
	ng-model="post.post_meta[ eventKey ].location.city"
	placeholder="City">

<input
	ng-model="post.post_meta[ eventKey ].location.region"
	placeholder="State/Province">

<input
	ng-model="post.post_meta[ eventKey ].location.country"
	placeholder="Country">

<input
	ng-model="post.post_meta[ eventKey ].location.postal_code"
	placeholder="Postal Code">
<input
	ng-model="post.post_meta[ eventKey ].details.cost"
	placeholder="Cost">

<hr>


<!-- LOCATION -->
<div
	class="input_module labeled"
	pw-geo-autocomplete
	unify-geocode-input>

	<div ng-show="!showGeoModule()">
		<h3>{{ language.edit.event.location_select[lang] }}</h3>
		<label for="location" class="inner"><div>{{ language.edit.event.location[lang] }} <i ng-show="loadingLocations" class="icon-spinner icon-spin"></i></div></label>
		<input
			id="location"
			type="text"
			ng-model="post.post_meta[ eventKey ].location.geocode"
			placeholder="Type Location..."
			typeahead="address.formatted_address as address.formatted_address for address in getLocation($viewValue) | filter:$viewValue"
			typeahead-loading="loadingLocations"
			typeahead-on-select="addGeocode($item)"
			placeholder=""
			class="labeled post_title gray bold">
	</div>

	<!-- 
	<div ng-show="showGeoModule()">
		
		<h3>
			{{ post.post_meta.location_obj.location_name }}
			<button ng-click="clearGeocode()" class="btn pull-right"><i class="icon-remove"></i></button>
		</h3>

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
