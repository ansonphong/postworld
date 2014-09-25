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
